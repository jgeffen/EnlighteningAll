<?php 
use Items\Enums\Requests;
	try {
			//delete the recodes
			Database::Action("DELETE FROM `member_photos_limit` WHERE `member_id` = :member_id", array(
						'member_id'     => $member->getId()
					));
		
			//insert the new recodes
			Database::Action("INSERT INTO `member_photos_limit` SET `member_id` = :member_id, `private_photos_limit` = :private_photos_limit ON DUPLICATE KEY UPDATE `private_photos_limit` = :private_photos_limit", array(
    'member_id'       => $member->getId(),
    'private_photos_limit' => filter_input(INPUT_POST, 'private_photos_limit', FILTER_VALIDATE_INT)
));
		
		$json_response = array(
					'status'  => 'success',
					'message' => 'Successfully set the privet photos limit'
				);
	} catch(Suggestion $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'suggestion',
			'message' => $exception->getMessage(),
			'modal'   => $exception->getSuggestion()
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
?>