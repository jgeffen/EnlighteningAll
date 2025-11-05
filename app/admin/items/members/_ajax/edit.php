<?php
/*
    Copyright (c) 2021, 2022 FenclWebDesign.com
    This script may not be copied, reproduced or altered in whole or in part.
    We check the Internet regularly for illegal copies of our scripts.
    Do not edit or copy this script for someone else, because you will be held responsible as well.
    This copyright shall be enforced to the full extent permitted by law.
    Licenses to use this script on a single website may be purchased from FenclWebDesign.com
    @Author: Deryk
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Admin\User        $admin
 */

// Imports
use Items\Enums\Tables;
use Items\Enums\Types;

try {
    // Variable Defaults
    $item   = filter_input(INPUT_POST, 'item', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $member = Items\Member::Init($item['id']);
    $errors = call_user_func(function() use ($member) {
        // Variable Defaults
        $required = array(
            'first_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'last_name'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'email'      => FILTER_VALIDATE_EMAIL
        );

        // Check Required Fields
        foreach($required as $field => $validation) {
            switch($validation) {
                case FILTER_VALIDATE_INT:
                    if(is_null(filter_input(INPUT_POST, $field, $validation)) || filter_input(INPUT_POST, $field, $validation) === FALSE) {
                        $errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
                    }
                    break;
                case FILTER_DEFAULT:
                default:
                    if(!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
                        $errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
                    }
            }
        }

        // ✅ Validate Email (replace old Membership::EmailExists)
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if ($email) {
            $exists = Database::Action("
					SELECT COUNT(*) 
					FROM members 
					WHERE email = :email 
					  AND email != :current_email
				", [
                'email' => $email,
                'current_email' => $member->getEmail()
            ])->fetchColumn();

            if ($exists > 0) {
                $errors['email'] = 'Email already exists.';
            }
        }

        // ✅ Validate Username (replace old Membership::UsernameExists)
        $username = filter_input(INPUT_POST, 'username');
        if ($username) {
            $exists = Database::Action("
					SELECT COUNT(*) 
					FROM members 
					WHERE username = :username 
					  AND id != :id
				", [
                'username' => $username,
                'id' => $member->getId()
            ])->fetchColumn();

            if ($exists > 0) {
                $errors['username'] = 'Username already exists.';
            } elseif (!preg_match(Types\Regex::USERNAME->getValue(), $username)) {
                $errors['username'] = 'Username is not acceptable.';
            }
        }

        // Validate Partner
        if($member->isCouple()) {
            if(!filter_input(INPUT_POST, 'partner_first_name')) {
                $errors['partner_first_name'] = 'Please tell us your partner\'s first name.';
            }
        }

        return $errors ?? FALSE;
    });

    // Check Errors
    if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');

    // Check Member
    if(is_null($member)) throw new Exception('Member not found in database.');

    // Update Member Data
    Database::Action("UPDATE `members` SET 
			`username` = :username, 
			`email` = :email, 
			`address_line_1` = :address_line_1, 
			`address_line_2` = :address_line_2, 
			`address_city` = :address_city, 
			`address_country` = :address_country, 
			`address_state` = :address_state, 
			`address_zip_code` = :address_zip_code, 
			`bead_colors` = :bead_colors, 
			`bio` = :bio, 
			`first_name` = :first_name, 
			`last_name` = :last_name, 
			`necklace_color` = :necklace_color, 
			`partner_bead_colors` = :partner_bead_colors, 
			`partner_first_name` = :partner_first_name, 
			`partner_necklace_color` = :partner_necklace_color, 
			`phone` = :phone, 
			`approved` = :approved, 
			`banned` = :banned, 
			`couple` = :couple, 
			`is_staff` = :is_staff, 
			`teacher` = :teacher, 
			`verified` = :verified, 
			`is_id_verified` = :is_id_verified, 
			`id_verified_admin_approval` = :id_verified_admin_approval, 
			`id_verified_timestamp` = :id_verified_timestamp, 
			`id_verified_ip_address` = :id_verified_ip_address, 
			`author` = :author 
			WHERE `id` = :member_id", array(
            'username'                   => filter_input(INPUT_POST, 'username'),
            'email'                      => filter_input(INPUT_POST, 'email'),
            'address_line_1'             => filter_input(INPUT_POST, 'address_line_1') ?: NULL,
            'address_line_2'             => filter_input(INPUT_POST, 'address_line_2') ?: NULL,
            'address_city'               => filter_input(INPUT_POST, 'address_city') ?: NULL,
            'address_country'            => filter_input(INPUT_POST, 'address_country') ?: NULL,
            'address_state'              => filter_input(INPUT_POST, 'address_state') ?: NULL,
            'address_zip_code'           => filter_input(INPUT_POST, 'address_zip_code') ?: NULL,
            'bead_colors'                => json_encode(filter_input(INPUT_POST, 'bead_colors', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? array()),
            'bio'                        => trim(strip_tags(filter_input(INPUT_POST, 'bio'), array('p', 'br', 'strong', 'em', 'u'))),
            'first_name'                 => filter_input(INPUT_POST, 'first_name'),
            'last_name'                  => filter_input(INPUT_POST, 'last_name'),
            'necklace_color'             => filter_input(INPUT_POST, 'necklace_color') ?: NULL,
            'partner_bead_colors'        => json_encode(filter_input(INPUT_POST, 'partner_bead_colors', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? array()),
            'partner_first_name'         => filter_input(INPUT_POST, 'partner_first_name') ?: NULL,
            'partner_necklace_color'     => filter_input(INPUT_POST, 'partner_necklace_color') ?: NULL,
            'phone'                      => filter_input(INPUT_POST, 'phone') ?: NULL,
            'approved'                   => filter_input(INPUT_POST, 'approved', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
            'banned'                     => filter_input(INPUT_POST, 'banned', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
            'couple'                     => filter_input(INPUT_POST, 'couple', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
            'is_staff'                   => filter_input(INPUT_POST, 'is_staff', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
            'teacher'                    => filter_input(INPUT_POST, 'approved', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
            'verified'                   => filter_input(INPUT_POST, 'verified', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
            'is_id_verified'             => filter_input(INPUT_POST, 'is_id_verified', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
            'id_verified_admin_approval' => filter_input(INPUT_POST, 'id_verified_admin_approval') ?: NULL,
            'id_verified_timestamp'      => !empty(filter_input(INPUT_POST, 'id_verified_admin_approval')) ? date('Y-m-d H:i:s') : NULL,
            'id_verified_ip_address'     => !empty(filter_input(INPUT_POST, 'id_verified_admin_approval')) ? filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP) : NULL,
            'author'                     => $admin->getId(),
            'member_id'                  => $member->getId()
        )
    );

    // Log Action
    $admin->log(
        type       : Types\Log::UPDATE,
        table_name : Tables\Secrets::MEMBERS,
        table_id   : $member->getId(),
        payload    : $_POST
    );

    // Set Message
    Admin\SetMessage('Updated database successfully.', 'success');

    // Set Response
    $json_response = array(
        'status'   => 'success',
        'message'  => Admin\GetMessage(),
        'table_id' => $member->getId()
    );
} catch(FormException $exception) {
    $json_response = array(
        'status' => 'error',
        'errors' => $exception->getErrors()
    );
} catch(PDOException $exception) {
    $json_response = array(
        'status'  => 'error',
        'message' => Debug::Exception($exception)
    );
} catch(Exception $exception) {
    $json_response = array(
        'status'  => 'error',
        'message' => $exception->getMessage()
    );
}

// Output Response
echo json_encode($json_response);
