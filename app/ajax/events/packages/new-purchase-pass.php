<?php
/*
    Copyright (c) 2021 FenclWebDesign.com
    This script may not be copied, reproduced or altered in whole or in part.
    We check the Internet regularly for illegal copies of our scripts.
    Do not edit or copy this script for someone else, because you will be held responsible as well.
    This copyright shall be enforced to the full extent permitted by law.
    Licenses to use this script on a single website may be purchased from FenclWebDesign.com
    @Author: Deryk
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var null|Membership   $member
 */

// Imports
use Items\Enums\Statuses;
use Items\Enums\Tables;
use Items\Enums\Types;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

try {
    // Variable Defaults
    $to_email = SITE_EMAIL;
    $subjects = [
        'success' => sprintf("Reservation Success - %s", SITE_NAME),
        'failure' => sprintf("Reservation Failure - %s", SITE_NAME),
        'receipt' => sprintf("Your Reservation Receipt - %s", SITE_NAME)
    ];

    // Validate Form
    $errors = call_user_func(function() {
        $required = [
            'first_name'   => FILTER_DEFAULT,
            'last_name'    => FILTER_DEFAULT,
            'phone'        => FILTER_DEFAULT,
            'email'        => FILTER_VALIDATE_EMAIL,
            'captcha'      => [FILTER_CALLBACK, ['options' => 'verifyHash']]
        ];
        foreach ($required as $field => $validation) {
            if (!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
                $errors[] = sprintf("%s is missing or invalid.", Helpers::PrettyTitle($field));
            }
        }
        return $errors ?? false;
    });
    if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');

    // Collect Inputs
    $event_id   = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
    $first_name = trim(filter_input(INPUT_POST, 'first_name'));
    $last_name  = trim(filter_input(INPUT_POST, 'last_name'));
    $phone      = trim(filter_input(INPUT_POST, 'phone'));
    $email      = trim(filter_input(INPUT_POST, 'email'));
    $comments   = trim(filter_input(INPUT_POST, 'comments'));
    $packages   = filter_input(INPUT_POST, 'event_packages', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $discount   = filter_input(INPUT_POST, 'discount') ?: 0.00;
    $amount     = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $full_name  = trim($first_name . ' ' . $last_name);

    if (empty($packages)) throw new Exception('You must select a package to continue.');

    $event = Items\Event::Init($event_id);
    if (!$event?->isPublished()) throw new Exception('Event not found or unpublished.');

    // --- Allow guests to reserve (no login required) ---
    $member = Membership::Init();
    $member_id = ($member && $member->getId()) ? $member->getId() : null;

    // --- Create transaction ID ---
    $transaction_id = ($amount > 0)
        ? 'PENDING-' . uniqid()
        : 'FREE-' . ($member_id ?? 'GUEST') . '-' . time();

    // --- Initialize Mobius Pay for paid reservations ---
    if ($amount > 0) {
        // Mobius Pay API request
        $mobius_url = "https://api.mobiuspay.com/v1/payments";
        $mobius_data = [
            "amount" => $amount * 100, // Convert to cents
            "currency" => "USD",
            "description" => "Event Reservation Payment",
            "return_url" => "https://yourwebsite.com/payment-success",
            "cancel_url" => "https://yourwebsite.com/payment-cancel",
            "customer" => [
                "email" => $email,
                "name" => $full_name
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $mobius_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mobius_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . MOBIUS_PAY_SECRET_KEY,
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $mobius_response = json_decode($response, true);

        if (isset($mobius_response['error'])) {
            throw new Exception("Mobius Pay Error: " . $mobius_response['error']);
        }

        $payment_url = $mobius_response['payment_url'];
        $transaction_id = $mobius_response['transaction_id'];
    }

    // --- Add Reservation(s) ---
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
                'status'           => ($amount > 0) ? Statuses\Reservation::PENDING->getValue() : Statuses\Reservation::PAID->getValue(),
                'member_id'        => $member_id,
                'event_id'         => $event->getId(),
                'event_package_id' => $package_id,
                'name_on_pass'     => $full_name,
                'phone'            => $phone,
                'package_amount'   => $package?->getPrice() ?: 0.00,
                'package_name'     => $package?->getName() ?: 'General Admission',
                'total_amount'     => $amount ?: 0.00,
                'total_discount'   => $discount ?: 0.00,
                'total_paid'       => ($amount > 0) ? 0.00 : $amount,
                'transaction_id'   => $transaction_id,
                'comments'         => $comments,
                'user_agent'       => $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
                'ip_address'       => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'
            ]);
        }
    }

    // --- Log Reservation Only If Logged In ---
    if ($member_id) {
        $member->log()->setData(
            type: Types\Log::CREATE,
            table_name: Tables\Members::RESERVATIONS
        )->execute();
    }

    // --- EMAIL RECEIPT (user) ---
    $mailer = new Mailer(TRUE);
    $mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
    $mailer->addAddress($email, $full_name);
    $mailer->setSubject($subjects['receipt']);
    $mailer->setAdmin(FALSE);
    $mailer->setBgColor('#d8bb66');
    $mailer->setBody('events/purchase-pass/notifications/receipt.twig', [
        'sale' => [
            'invoice' => $transaction_id,
            'amount'  => ($amount <= 0.00) ? 'Free' : Helpers::FormatCurrency($amount),
            'tax'     => Helpers::FormatCurrency(0.00),
            'account' => 'N/A (Free Event)'
        ],
        'billing' => [
            'name'           => sprintf("%s, %s", $last_name, $first_name),
            'phone'          => $phone,
            'email'          => $email,
            'address_line_1' => '',
            'address_line_2' => '',
            'city'           => '',
            'state'          => '',
            'country'        => '',
            'zip_code'       => ''
        ],
        'event' => [
            'heading'      => $event->getHeading(),
            'date'         => $event->getDate(),
            'name_on_pass' => $full_name,
            'packages'     => array_map(function(int $quantity, int $package_id) {
                $package = Items\Events\Package::Init($package_id);
                return [
                    'quantity' => $quantity,
                    'text'     => sprintf("[%s] %s", $package?->getPrice(TRUE), $package?->getName())
                ];
            }, $packages, array_keys($packages))
        ],
        'comments' => $comments
    ])->send();

    // --- Admin Notification ---
    $mailer = new Mailer(TRUE);
    $mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
    $mailer->addAddress($to_email, SITE_COMPANY);
    $mailer->setSubject($subjects['success']);
    $mailer->setAdmin(TRUE);
    $mailer->setBgColor('#198754');
    $mailer->setBody('events/purchase-pass/notifications/success.twig', [
        'sale' => [
            'invoice' => $transaction_id,
            'amount'  => ($amount <= 0.00) ? 'Free' : Helpers::FormatCurrency($amount),
            'tax'     => Helpers::FormatCurrency(0.00),
            'account' => 'N/A (Free Event)'
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

    // --- Final JSON Response ---
    $json_response = ($amount > 0)
        ? [
            'status' => 'pending',
            'payment_url' => $payment_url,
            'transaction_id' => $transaction_id,
            'html' => Render::GetTemplate('events/purchase-pass/pending.twig')
        ]
        : [
            'status' => 'success',
            'transaction_id' => $transaction_id,
            'html' => Render::GetTemplate('events/purchase-pass/success.twig')
        ];

} catch (FormException $exception) {
    $json_response = [
        'status' => 'error',
        'errors' => $exception->getErrors()
    ];
} catch (\Exception $exception) {
    $json_response = [
        'status' => 'error',
        'message' => $exception->getMessage()
    ];
}

// --- Output JSON Response ---
echo json_encode($json_response);
