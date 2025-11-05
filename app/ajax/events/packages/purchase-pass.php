<?php
/*
    Copyright (c) 2021 FenclWebDesign.com
    @Author: Deryk
*/
file_put_contents('/tmp/which_purchase_pass.log', __FILE__ . "\n", FILE_APPEND);

use Items\Enums\Statuses;
use Items\Enums\Tables;
use Items\Enums\Types;
use PHPMailer\PHPMailer\PHPMailer;

if (defined('DEV_MODE') && DEV_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

try {
    $to_email = SITE_EMAIL;
    $subjects = [
        'success' => sprintf("Reservation Success - %s", SITE_NAME),
        'failure' => sprintf("Reservation Failure - %s", SITE_NAME),
        'receipt' => sprintf("Your Reservation Receipt - %s", SITE_NAME)
    ];

    // --- Validate form inputs
    $required = [
        'first_name' => FILTER_DEFAULT,
        'last_name'  => FILTER_DEFAULT,
        'phone'      => FILTER_DEFAULT,
        'email'      => FILTER_VALIDATE_EMAIL,
        'captcha'    => [FILTER_CALLBACK, ['options' => 'verifyHash']],
    ];

    $errors = [];
    foreach ($required as $field => $validation) {
        if (is_array($validation)) {
            $value = filter_input(INPUT_POST, $field, $validation[0], $validation[1] ?? []);
        } else {
            $value = filter_input(INPUT_POST, $field, $validation);
        }
        if (!$value) {
            $errors[] = sprintf("%s is missing or invalid.", Helpers::PrettyTitle($field));
        }
    }
    if (!empty($errors)) {
        throw new FormException($errors, 'You are missing required fields.');
    }

    // --- Collect inputs
    $event_id   = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
    $first_name = trim(filter_input(INPUT_POST, 'first_name'));
    $last_name  = trim(filter_input(INPUT_POST, 'last_name'));
    $phone      = trim(filter_input(INPUT_POST, 'phone'));
    $email      = trim(filter_input(INPUT_POST, 'email'));
    $comments   = strip_tags(trim(filter_input(INPUT_POST, 'comments', FILTER_UNSAFE_RAW)));
    $packages   = filter_input(INPUT_POST, 'event_packages', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $discount   = filter_input(INPUT_POST, 'discount') ?: 0.00;
    $amount     = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $paypal_id  = trim(filter_input(INPUT_POST, 'paypal_transaction_id'));
    $full_name  = trim("$first_name $last_name");

    if (empty($packages)) throw new Exception('You must select a package to continue.');
    $event = Items\Event::Init($event_id);
    if (!$event?->isPublished()) throw new Exception('Event not found or unpublished.');

    // --- Init member
    $member = Membership::Init();
    $member_id = ($member && $member->getId()) ? $member->getId() : null;

    // --- Auto-create member if missing
    if (!$member_id) {
        $existing = Database::Action("
            SELECT `id` FROM `members` WHERE `email` = :email LIMIT 1
        ", ['email' => $email])->fetchColumn();

        if ($existing) {
            $member_id = $existing;
        } else {
            Database::Action("
                INSERT INTO `members`
                SET 
                    `first_name` = :first_name,
                    `last_name`  = :last_name,
                    `email`      = :email,
                    `phone`      = :phone,
                    `status`     = 'active',
                    `created_at` = NOW()
            ", [
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'email'      => $email,
                'phone'      => $phone
            ]);

            // ✅ Get new ID safely via SELECT LAST_INSERT_ID()
            $member_id = Database::Action("SELECT LAST_INSERT_ID()")->fetchColumn();
        }
    }

    // --- Free or paid transaction
    if ($amount <= 0.00) {
        $transaction_label = sprintf("FREE-%s-%s", $member_id ?? 'GUEST', time());
    } else {
        if (empty($paypal_id)) {
            throw new Exception('Missing PayPal Transaction ID.');
        }
        $transaction_label = $paypal_id;
    }

    // --- Minimal transaction insert (no schema assumptions)
    Database::Action("INSERT INTO `transactions`() VALUES()");
    $transaction_db_id = Database::Action("SELECT LAST_INSERT_ID()")->fetchColumn();

    // --- Insert reservation(s)
    foreach ($packages as $package_id => $quantity) {
        $package = Items\Events\Package::Init($package_id);

        for ($i = 1; $i <= $quantity; $i++) {
            Database::Action("
                INSERT INTO `member_reservations`
                SET 
                    `status` = :status,
                    `member_id` = :member_id,
                    `event_id` = :event_id,
                    `event_package_id` = :event_package_id,
                    `name_on_pass` = :name_on_pass,
                    `phone` = :phone,
                    `package_amount` = :package_amount,
                    `package_name` = :package_name,
                    `total_amount` = :total_amount,
                    `total_discount` = :total_discount,
                    `total_paid` = :total_paid,
                    `comments` = :comments,
                    `transaction_id` = :transaction_id,
                    `user_agent` = :user_agent,
                    `ip_address` = :ip_address
            ", [
                'status'           => Statuses\Reservation::PAID->getValue(),
                'member_id'        => $member_id,
                'event_id'         => $event->getId(),
                'event_package_id' => $package_id,
                'name_on_pass'     => $full_name,
                'phone'            => $phone,
                'package_amount'   => $package?->getPrice() ?: 0.00,
                'package_name'     => $package?->getName() ?: 'General Admission',
                'total_amount'     => $amount ?: 0.00,
                'total_discount'   => $discount ?: 0.00,
                'total_paid'       => ($amount <= 0.00) ? 0.00 : $amount,
                'comments'         => $comments,
                'transaction_id'   => $transaction_db_id,
                'user_agent'       => $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
                'ip_address'       => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'
            ]);
        }
    }

    // --- Emails
    $mailer = new Mailer(TRUE);
    $mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
    $mailer->addAddress($email, $full_name);
    $mailer->setSubject($subjects['receipt']);
    $mailer->setAdmin(FALSE);
    $mailer->setBgColor('#d8bb66');
    $mailer->setBody('events/purchase-pass/notifications/receipt.twig', [
        'sale' => [
            'invoice' => $transaction_label,
            'amount'  => ($amount <= 0.00) ? 'Free' : Helpers::FormatCurrency($amount),
            'tax'     => Helpers::FormatCurrency(0.00),
            'account' => ($amount <= 0.00) ? 'N/A (Free Event)' : 'Paid via PayPal'
        ],
        'billing' => [
            'name'  => sprintf("%s, %s", $last_name, $first_name),
            'phone' => $phone,
            'email' => $email
        ],
        'event' => [
            'heading'      => $event->getHeading(),
            'date'         => $event->getDate(),
            'name_on_pass' => $full_name
        ],
        'comments' => $comments
    ])->send();

    $mailer = new Mailer(TRUE);
    $mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
    $mailer->addAddress($to_email, SITE_COMPANY);
    $mailer->setSubject($subjects['success']);
    $mailer->setAdmin(TRUE);
    $mailer->setBgColor('#198754');
    $mailer->setBody('events/purchase-pass/notifications/success.twig', [
        'sale' => [
            'invoice' => $transaction_label,
            'amount'  => ($amount <= 0.00) ? 'Free' : Helpers::FormatCurrency($amount),
            'tax'     => Helpers::FormatCurrency(0.00),
            'account' => ($amount <= 0.00) ? 'N/A (Free Event)' : 'Paid via PayPal'
        ],
        'billing' => [
            'name'  => sprintf("%s, %s", $last_name, $first_name),
            'phone' => $phone,
            'email' => $email
        ],
        'event' => [
            'heading'      => $event->getHeading(),
            'date'         => $event->getDate(),
            'name_on_pass' => $full_name
        ],
        'comments' => $comments
    ])->send();

    $json_response = [
        'status' => 'success',
        'transaction_id' => $transaction_db_id,
        'html' => Render::GetTemplate('events/purchase-pass/success.twig')
    ];

} catch (FormException $exception) {
    $json_response = ['status' => 'error', 'errors' => $exception->getErrors()];
} catch (Throwable $exception) {
    $json_response = ['status' => 'error', 'message' => $exception->getMessage()];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($json_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT);
exit;
