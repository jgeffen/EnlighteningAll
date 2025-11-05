<?php
	// Imports
	use Items\Enums\Requests;
	use Items\Members\Actions;
	
	try {
		// Check Logged In
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Variable Defaults
		$member = new Membership();
		$avatar = $member->getAvatar();
		
		// Check Post
		if(is_null($avatar)) throw new Exception('Avatar not found.');
		
		// Remove Avatar
		Actions\Avatar::Init($member, $avatar)->setRequest(Requests\Avatar::DELETE)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Avatar has been removed.'
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);