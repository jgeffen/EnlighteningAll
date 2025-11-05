<?php
/*
    Copyright (c) 2021, 2022 FenclWebDesign.com
    This script may not be copied, reproduced or altered in whole or in part.
    We check the Internet regularly for illegal copies of our scripts.
    Do not edit or copy this script for someone else, because you will be held responsible as well.
    This copyright shall be enforced to the full extent permitted by law.
    Licenses to use this script on a single website may be purchased from FenclWebDesign.com
    @Author: Developer
*/

/**
 * @var Router\Dispatcher $dispatcher
 */

// Imports
use Items\Enums\Sizes;
use Items\Enums\Tables;
use Items\Enums\Types;

try {
    // Variable Defaults
    $subject = sprintf("Confirmation Email for %s Membership", SITE_NAME);

    // Set Errors
    $errors = call_user_func(function () {
        $errors = [];

        // Required fields
        $required = [
            'first_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'last_name'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'email'      => FILTER_VALIDATE_EMAIL,
            'captcha'    => [FILTER_CALLBACK, ['options' => 'verifyHash']]
        ];

        foreach ($required as $field => $validation) {
            if (!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
                $errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
            }
        }

        // ✅ Replace missing Membership::EmailExists() with direct DB check
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if ($email) {
            $exists = Database::Action("SELECT COUNT(*) FROM members WHERE email = :email", ['email' => $email])->fetchColumn();
            if ($exists > 0) {
                $errors['email'] = 'An account with this email already exists.';
            }
        }

        // ✅ Replace Membership::UsernameExists() with DB check
        $username = filter_input(INPUT_POST, 'username');
        if ($username) {
            $exists = Database::Action("SELECT COUNT(*) FROM members WHERE username = :username", ['username' => $username])->fetchColumn();
            if ($exists > 0) {
                $errors['username'] = 'Username already exists.';
            } elseif (!preg_match(Types\Regex::USERNAME->getValue(), $username)) {
                $errors['username'] = 'Username is not acceptable.';
            }
        }

        // Validate Password
        if (filter_input(INPUT_POST, 'password')) {
            if (filter_input(INPUT_POST, 'retype_password')) {
                if (!strcmp($_POST['password'], $_POST['retype_password'])) {
                    if (!preg_match(Types\Regex::PASSWORD->getValue(), $_POST['password'])) {
                        $errors['password'] = 'Password is not strong enough.';
                    }
                } else {
                    $errors['password'] = 'Passwords do not match.';
                }
            } else {
                $errors['password'] = 'Please re-type the password.';
            }
        }

        // Validate Avatar Upload
        if (empty($_FILES['avatar']['name'])) {
            $errors['avatar'] = 'You must upload a photo of yourself.';
        } elseif (!is_file($_FILES['avatar']['tmp_name'])) {
            $errors['avatar'] = 'Photo did not upload properly.';
        } elseif (!in_array(strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION)), ['jpeg', 'jpg', 'png'])) {
            $errors['avatar'] = 'This file type is not allowed.';
        } elseif (!empty($_FILES['avatar']['error'])) {
            $errors['avatar'] = sprintf("Upload Error: %s", match ($_FILES['avatar']['error']) {
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
                3 => 'The uploaded file was only partially uploaded.',
                4 => 'No file was uploaded.',
                6 => 'Missing a temporary folder.',
                7 => 'Failed to write file to disk.',
                8 => 'A PHP extension stopped the file upload.',
                default => 'An unknown error has occurred.'
            });
        } else {
            try {
                new Imagick($_FILES['avatar']['tmp_name']);
            } catch (ImagickException $exception) {
                $errors['avatar'] = Debug::Exception($exception);
            }
        }

        return $errors ?: false;
    });

    // Stop if validation failed
    if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');

    // Insert into database
    $member_id = Database::Action(
        "INSERT INTO members SET
            referred_by = :referred_by,
            intake_survey = :intake_survey,
            username = :username,
            email = :email,
            password = :password,
            address_line_1 = :address_line_1,
            address_line_2 = :address_line_2,
            address_city = :address_city,
            address_country = :address_country,
            address_state = :address_state,
            address_zip_code = :address_zip_code,
            bio = :bio,
            first_name = :first_name,
            last_name = :last_name,
            phone = :phone,
            is_staff = :is_staff,
            teacher = :teacher,
            teacher_approved = :teacher_approved,
            user_agent = :user_agent,
            ip_address = :ip_address",
        [
            'referred_by' => filter_input(INPUT_POST, 'referred_by', FILTER_VALIDATE_INT),
            'intake_survey' => filter_input(INPUT_POST, 'teacher', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]),
            'username' => filter_input(INPUT_POST, 'username'),
            'email' => filter_input(INPUT_POST, 'email'),
            'password' => password_hash(filter_input(INPUT_POST, 'password'), PASSWORD_DEFAULT),
            'address_line_1' => filter_input(INPUT_POST, 'address_line_1'),
            'address_line_2' => filter_input(INPUT_POST, 'address_line_2'),
            'address_city' => filter_input(INPUT_POST, 'address_city'),
            'address_country' => filter_input(INPUT_POST, 'address_country'),
            'address_state' => filter_input(INPUT_POST, 'address_state'),
            'address_zip_code' => filter_input(INPUT_POST, 'address_zip_code'),
            'bio' => filter_input(INPUT_POST, 'bio'),
            'first_name' => filter_input(INPUT_POST, 'first_name'),
            'last_name' => filter_input(INPUT_POST, 'last_name'),
            'phone' => filter_input(INPUT_POST, 'phone'),
            'is_staff' => filter_input(INPUT_POST, 'is_staff', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]),
            'teacher' => filter_input(INPUT_POST, 'teacher', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]),
            'teacher_approved' => 0,
            'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
            'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
        ],
        TRUE
    );

    // Initialize new member
    $member = Membership::Init($member_id);
    if (is_null($member)) throw new Exception('Something went wrong with your registration. Please try again.');

    // Log registration
    $member->log()->setType(Types\Log::REGISTER)->execute();

    // ✅ Send confirmation email with simple login link (no verification)
    $mailer = new Mailer(TRUE);
    $mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
    $mailer->addAddress($member->getEmail(), $member->getFullNameLast());
    $mailer->setSubject($subject);
    $mailer->setBody('members/registration/notification.twig', [
        'names'    => $member->getFirstNames(),
        'username' => $member->getUsername(),
        'link'     => sprintf('%s/members/login', SITE_URL)
    ])->send();

    // Success response
    $json_response = [
        'status' => 'success',
        'html'   => Template::Render('members/registration/success.twig', [
            'email' => $member->getEmail()
        ])
    ];

} catch (FormException $exception) {
    $json_response = ['status' => 'error', 'errors' => $exception->getErrors()];
} catch (PDOException $exception) {
    Debug::Exception($exception);
    $json_response = ['status' => 'error', 'message' => 'There was an issue communicating with the database. Please try again later.'];
} catch (Exception $exception) {
    $json_response = ['status' => 'error', 'message' => $exception->getMessage()];
}

// ✅ Handle avatar upload after success
if ($json_response['status'] == 'success' && !empty($member)) {
    try {
        $image_path = Helpers::CreateDirectory(
            sprintf("%s/files/members/%d/avatar", dirname(__DIR__, 4), $member->getId())
        );

        $filename = strtolower(uniqid());
        $pathinfo = pathinfo($_FILES['avatar']['name']);

        while (file_exists(sprintf("%s/%s.%s", $image_path, $filename, $pathinfo['extension']))) {
            $counter = ($counter ?? 0) + 1;
            $filename = sprintf("%s-%d", $filename, $counter);
        }

        $filename = sprintf("%s.%s", $filename, $pathinfo['extension']);

        $imagick = new Imagick($_FILES['avatar']['tmp_name']);
        $imagick->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
        $imagick->resizeImage(min(1200, $imagick->getImageWidth()), 0, Imagick::FILTER_LANCZOS, 1);
        $imagick->setImageCompressionQuality(80);
        $imagick->writeImage(sprintf("%s/%s", $image_path, $filename));

        foreach (Sizes\Avatar::options() as $size) {
            $thumb = clone $imagick;
            $thumb_path = Helpers::CreateDirectory(sprintf("%s/%d", $image_path, $size));
            $thumb->cropThumbnailImage($size, $size);
            $thumb->setImageCompressionQuality(80);
            $thumb->writeImage(sprintf("%s/%s", $thumb_path, $filename));
        }

        $member->log()->setData(
            type: Types\Log::CREATE,
            table_name: Tables\Members::AVATARS,
            table_id: Database::Action(
                "INSERT INTO member_avatars SET
                    member_id = :member_id,
                    filename = :filename,
                    approved = :approved,
                    user_agent = :user_agent,
                    ip_address = :ip_address",
                [
                    'member_id' => $member->getId(),
                    'filename'  => $filename,
                    'approved'  => TRUE,
                    'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
                    'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
                ],
                TRUE
            )
        )->execute();

    } catch (Exception|ImagickException $exception) {
        $member->log()->setData(
            type: Types\Log::ERROR,
            table_name: Tables\Members::AVATARS,
            notes: Debug::Exception($exception)
        )->execute();
    }
}

// Output JSON
echo json_encode($json_response);
