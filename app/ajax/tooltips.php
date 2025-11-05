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
	 */
	
	// Set Tooltips
	$tooltips = array(
		'block'                  => 'Block User',
		'friend-request-accept'  => 'Accept Friend Request',
		'friend-request-cancel'  => 'Cancel Friend Request',
		'friend-request-decline' => 'Decline Friend Request',
		'friend-request-send'    => 'Send Friend Request',
		'friend-request-remove'  => 'Remove Friend',
		'message'                => 'Message User',
		'message-report'         => 'Report Message',
		'comment-report'         => 'Report Comment',
		'comment-remove'         => 'Remove Comment',
		'post-report'            => 'Report Post',
		'profile-report'         => 'Report Profile',
		'view-profile'           => 'View Profile',
	);
	
	// Output Tooltip
	echo $tooltips[$dispatcher->getOption('type')] ?? NULL;