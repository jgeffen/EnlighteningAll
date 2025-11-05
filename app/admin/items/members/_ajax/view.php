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
	
	// TODO: Create API method for admin panel handling membership
	
	$start       = isset($_POST['start']) ? intval($_POST['start']) : 0;
	$length      = isset($_POST['length']) ? intval($_POST['length']) : 250;
	$draw        = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
	$searchValue = $_POST['search']['value'] ?? '';
	
	// Get total number of records in the database
	$totalRecords = Database::Action("SELECT COUNT(*) FROM `members`")->fetchColumn();
	
	$unApprovedwithAvatarFilter = isset($_POST['columns'][14]['search']['value']) && $_POST['columns'][14]['search']['value'] == 'true';
	
	$withoutAvatarFilter = isset($_POST['columns'][14]['search']['value']) && $_POST['columns'][14]['search']['value'] == 'false';
	
	$isUnverifiedFilter = isset($_POST['columns'][10]['search']['value']) && $_POST['columns'][10]['search']['value'] == 'false';
	
	$isVerifiedFilter = isset($_POST['columns'][10]['search']['value']) && $_POST['columns'][10]['search']['value'] == 'true';
	
	$isApprovedFilter = isset($_POST['columns'][11]['search']['value']) && $_POST['columns'][11]['search']['value'] == 'true';
	
	$isSubscribedFilter = isset($_POST['columns'][12]['search']['value']) && $_POST['columns'][12]['search']['value'] == 'true';
	
	$isBannedFilter = isset($_POST['columns'][13]['search']['value']) && $_POST['columns'][13]['search']['value'] == 'true';
	
	$isUnapprovedTeacherFilter = isset($_POST['columns'][15]['search']['value']) && $_POST['columns'][15]['search']['value'] == 'true';
	
	$isApprovedTeacherFilter = isset($_POST['columns'][16]['search']['value']) && $_POST['columns'][16]['search']['value'] == 'true';
	
	$isStaffFilter = isset($_POST['columns'][17]['search']['value']) && $_POST['columns'][17]['search']['value'] == 'true';
	
	///////////////////////////////////////////////////
	
	$orderColumn = array('id', 'username', 'last_name', 'email', '', '', '', 'timestamp', 'ip_address');
	
	if(isset($_POST['order'])) {
		$orderBy        = $orderColumn[$_POST['order'][0]['column']];
		$orderDirection = $_POST['order'][0]['dir'] == 'desc' ? 'DESC' : 'ASC';
		$orderByquery   = " ORDER BY members.$orderBy $orderDirection";
	} else {
		// Default Order
		$orderByquery = " ORDER BY members.last_name, members.first_name";
	}
	
	///////////////////////////////////////////////////
	
	if($unApprovedwithAvatarFilter) {
		
		
		$query = "SELECT * FROM members WHERE EXISTS (SELECT 1 FROM member_avatars WHERE member_avatars.member_id = members.id AND members.approved = 0) $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM members WHERE EXISTS (SELECT 1 FROM member_avatars WHERE member_avatars.member_id = members.id AND members.approved = 0)")->fetchColumn();
	} elseif($withoutAvatarFilter) {
		
		
		$query = "SELECT * FROM members WHERE NOT EXISTS (SELECT 1 FROM member_avatars WHERE members.id = member_avatars.member_id) $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM members WHERE NOT EXISTS (SELECT 1 FROM member_avatars WHERE members.id = member_avatars.member_id)")->fetchColumn();
	} elseif($isUnverifiedFilter) {
		
		$query = "SELECT * FROM `members` WHERE `verified` = 0 AND `banned` = 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `members` WHERE `verified` = 0 AND `banned` = 0")->fetchColumn();
	} elseif($isVerifiedFilter) {
		
		$query = "SELECT * FROM `members` WHERE `verified` = 1 AND `banned` = 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `members` WHERE `verified` = 1 AND `banned` = 0")->fetchColumn();
	} elseif($isApprovedFilter) {
		
		$query = "SELECT * FROM `members` WHERE `approved` = 1 AND `banned` = 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `members` WHERE `approved` = 1 AND `banned` = 0")->fetchColumn();
	} elseif($isSubscribedFilter) {
		
		
		$query = "SELECT `members`.* FROM `members` JOIN member_subscriptions ON members.id = member_subscriptions.member_id WHERE member_subscriptions.status = 'ACTIVE' AND members.banned = 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `members` JOIN member_subscriptions ON members.id = member_subscriptions.member_id WHERE member_subscriptions.status = 'ACTIVE' AND members.banned = 0")->fetchColumn();
	} elseif($isBannedFilter) {
		
		$query = "SELECT * FROM `members` WHERE `banned` = 1 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `members` WHERE `banned` = 1")->fetchColumn();
	} elseif($isUnapprovedTeacherFilter) {
		
		$query = "SELECT * FROM `members` WHERE `teacher` = 1 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `members` WHERE `teacher` = 1")->fetchColumn();
	} elseif($isApprovedTeacherFilter) {
		
		$query = "SELECT * FROM `members` WHERE `teacher_approved` = 1 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `members` WHERE `teacher_approved` = 1")->fetchColumn();
	} elseif($isStaffFilter) {
		
		$query = "SELECT * FROM `members` WHERE `is_staff` = 1 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `members` WHERE `is_staff` = 1")->fetchColumn();
	} elseif(!empty($searchValue)) {
		
		$query = "SELECT * FROM `members` WHERE `email` LIKE '%$searchValue%' OR `username` LIKE '%$searchValue%' OR `last_name` LIKE '%$searchValue%' OR `first_name` LIKE '%$searchValue%' $orderByquery";
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `members` WHERE `email` LIKE '%$searchValue%' OR `username` LIKE '%$searchValue%' OR `last_name` LIKE '%$searchValue%' OR `first_name` LIKE '%$searchValue%'")->fetchColumn();
	} else {
		
		$query = "SELECT * FROM `members` $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = $totalRecords;
	}
	
	$results = Membership::FetchAll(Database::Action($query));
	
	try {
		// Set Response
		$json_response = array(
			'status'          => 'success',
			'message'         => 'DataTables loaded successfully.',
			'draw'            => $draw,
			'recordsTotal'    => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data'            => array_map(function(Membership $item) use ($isSubscribedFilter, $isApprovedFilter, $isVerifiedFilter, $withoutAvatarFilter, $unApprovedwithAvatarFilter, $isUnapprovedTeacherFilter, $isApprovedTeacherFilter, $isStaffFilter) {
				return array(
					'id'                  => $item->getId(),
					'is_approved'         => $isApprovedFilter ? TRUE : $item->isApproved(),
					'is_banned'           => $item->isBanned(),
					'is_verified'         => $isVerifiedFilter ? TRUE : $item->isVerified(),
					'is_subscribed'       => $isSubscribedFilter ? TRUE : (bool)$item->subscription()?->isPaid(),
					'is_teacher'          => $isUnapprovedTeacherFilter ? TRUE : $item->isTeacher(),
					'is_teacher_approved' => $isApprovedTeacherFilter ? TRUE : $item->isTeacherApproved(),
					'is_staff'            => $isStaffFilter ? TRUE : $item->isStaff(),
					'username'            => array(
						'value' => $item->getUsername(),
						'label' => Render::GetTemplate('admin/items/members/username.twig', array(
							'username' => $item->getUsername(),
							'flags'    => array('verified' => $item->isVerified())
						))
					),
					'full_name_last'      => trim($item->getFullNameLast()),
					'email'               => $item->getEmail(),
					'user_agent'          => $item->getUserAgent(TRUE),
					'browser'             => $item->getUserAgent(TRUE, array('browser')) ?: Render::GetTemplate('admin/null.twig'),
					'device'              => $item->getUserAgent(TRUE, array('device')) ?: Render::GetTemplate('admin/null.twig'),
					'platform'            => $item->getUserAgent(TRUE, array('platform')) ?: Render::GetTemplate('admin/null.twig'),
					'language'            => $item->getUserAgent(TRUE, array('language')) ?: Render::GetTemplate('admin/null.twig'),
					'avatar'              => $withoutAvatarFilter == "false" ? FALSE : ($unApprovedwithAvatarFilter == "true" ? TRUE : !is_null($item->getAvatar())),
					'item'                => $item->toArray(),
					'options'             => Render::GetTemplate('admin/items/members/options.twig', array(
						'id'    => $item->getId(),
						'link'  => $item->getLink(),
						'flags' => array(
							'admin'            => Admin\Privilege(array(1, 2, 4)),
							'approved'         => $item->isApproved(),
							'banned'           => $item->isBanned(),
							'subscribed'       => (bool)$item->subscription()?->isPaid(),
							'verified'         => $item->isVerified(),
							'teacher_approved' => $item->isTeacherApproved(),
							'is_staff'         => $item->isStaff(),
							'free_drink'       => (bool)$item->getFreeDrink()?->getId()
						)
					)),
					'ip_address'          => array(
						'value' => $item->getIpAddress()->getValue(),
						'label' => Render::GetTemplate('admin/ip-address.twig', array(
							'ip_address'   => $item->getIpAddress(),
							'country_flag' => $item->getIpAddress()->getCountry()?->getEmoji(),
							'link'         => $item->getIpAddress()->getLink()
						))
					),
					'timestamp'           => array(
						'value' => $item->getTimestamp()->format('U'),
						'label' => $item->getTimestamp()->format('M j \'y')
					)
				);
			}, $results)
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception),
			'data'    => array()
		);
	}
	
	// Output Response
	echo json_encode($json_response);
