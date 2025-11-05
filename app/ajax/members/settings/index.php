<?php
	/*
	Copyright (c) 2021, 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Set Errors
		$errors = call_user_func(function() use ($member) {
			// Required Fields
			$required = array(
				'first_name' => FILTER_SANITIZE_SPECIAL_CHARS,
				'last_name'  => FILTER_SANITIZE_SPECIAL_CHARS,
			);
			
			// Check Required Fields
			foreach($required as $field => $validation) {
				// Switch Validation
				switch($validation) {
					case FILTER_VALIDATE_INT:
						if(is_null(filter_input(INPUT_POST, $field, $validation)) || filter_input(INPUT_POST, $field, $validation) === FALSE) {
							$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
						}
						break;
					case FILTER_DEFAULT:
					default:
						if(!filter_input(INPUT_POST, $field, $validation)) {
							$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
						}
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
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_settings')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Update Member Data
		Database::Action("UPDATE `members` SET `address_line_1` = :address_line_1, `address_line_2` = :address_line_2, `address_city` = :address_city, `address_country` = :address_country, `address_state` = :address_state, `address_zip_code` = :address_zip_code, `bead_colors` = :bead_colors, `bio` = :bio, `first_name` = :first_name, `last_name` = :last_name, `necklace_color` = :necklace_color, `partner_bead_colors` = :partner_bead_colors, `partner_first_name` = :partner_first_name, `partner_necklace_color` = :partner_necklace_color, `phone` = :phone, `display_rsvps` = :display_rsvps, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `id` = :member_id", array(
			'address_line_1'         => filter_input(INPUT_POST, 'address_line_1'),
			'address_line_2'         => filter_input(INPUT_POST, 'address_line_2'),
			'address_city'           => filter_input(INPUT_POST, 'address_city'),
			'address_country'        => filter_input(INPUT_POST, 'country'),
			'address_state'          => filter_input(INPUT_POST, 'address_state'),
			'address_zip_code'       => filter_input(INPUT_POST, 'postal_code'),
			'bead_colors'            => json_encode(filter_input(INPUT_POST, 'bead_colors', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? array()),
			'bio'                    => trim(strip_tags(filter_input(INPUT_POST, 'bio'), array('p', 'br', 'strong', 'em', 'u'))),
			'first_name'             => filter_input(INPUT_POST, 'first_name'),
			'last_name'              => filter_input(INPUT_POST, 'last_name'),
			'necklace_color'         => filter_input(INPUT_POST, 'necklace_color'),
			'partner_bead_colors'    => json_encode(filter_input(INPUT_POST, 'partner_bead_colors', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? array()),
			'partner_first_name'     => filter_input(INPUT_POST, 'partner_first_name'),
			'partner_necklace_color' => filter_input(INPUT_POST, 'partner_necklace_color'),
			'phone'                  => filter_input(INPUT_POST, 'phone'),
			'display_rsvps'          => filter_input(INPUT_POST, 'display_rsvps', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
			'user_agent'             => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'             => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'member_id'              => $member->getId()
		));
		
		// Log Action
		$member->log()->setData(
			type       : Types\Log::UPDATE,
			table_name : Tables\Secrets::MEMBERS,
			table_id   : $member->getId()
		)->execute();
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'html'   => Template::Render('members/settings/success.twig')
		);
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status' => 'error',
			'errors' => $exception->getErrors()
		);
	} catch(PDOException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception)
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output JSON
	echo json_encode($json_response);