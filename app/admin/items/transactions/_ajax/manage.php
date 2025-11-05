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
	
	$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
	
	$length = isset($_POST['length']) ? intval($_POST['length']) : 250;
	
	$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
	
	$searchValue = $_POST['search']['value'] ?? '';
	
	// Get total number of records in the database
	$totalRecords = Database::Action("SELECT COUNT(*) FROM `transactions`")->fetchColumn();
	
	$orderColumn =
		array(
			'id',
			'',
			'type',
			'payment_status',
			'amount',
			'billing_first_name',
			'billing_last_name',
			'billing_phone',
			'billing_email',
			
			'',
			'',
			'ip_address',
			'timestamp'
		);
	
	$orderBy        = '';
	$orderDirection = 'ASC'; // Default order direction
	
	// Check if order details are provided and valid
	if(is_array($_POST['order']) && isset($_POST['order'][0]['column'])) {
		// Ensure the column index is within the range of the columns array
		if(isset($orderColumn[$_POST['order'][0]['column']])) {
			$orderBy        = $orderColumn[$_POST['order'][0]['column']];
			$orderDirection = isset($_POST['order'][0]['dir']) && $_POST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';
		}
	}
	
	// Set the ORDER BY clause of the SQL query
	$orderByquery = $orderBy ? "  ORDER BY transactions.$orderBy $orderDirection" : "  ORDER BY `timestamp` DESC";
	
	$filteredRecords = $totalRecords;
	
	/////////////////////////////////////////////
	
	$selectedOption = $_POST['selectedOption'] ?? '';
	
	/////////////////////////////////////////////
	
	$isApprovedFilter = isset($_POST['columns'][2]['search']['value']) && $_POST['columns'][2]['search']['value'] == 'Approved';
	
	$isErroredDeclinedFilter = isset($_POST['columns'][2]['search']['value']) && $_POST['columns'][2]['search']['value'] == 'Declined';
	
	$isRefundedFilter = isset($_POST['columns'][2]['search']['value']) && $_POST['columns'][2]['search']['value'] == 'Refunded';
	
	$isCapturedFilter = isset($_POST['columns'][2]['search']['value']) && $_POST['columns'][2]['search']['value'] == 'Captured';
	
	$isPendingFilter = isset($_POST['columns'][2]['search']['value']) && $_POST['columns'][2]['search']['value'] == 'Pending';
	
	$isVoidedFilter = isset($_POST['columns'][2]['search']['value']) && $_POST['columns'][2]['search']['value'] == 'Voided';
	$isTipsFilter   = isset($_POST['columns'][1]['search']['value']) && $_POST['columns'][1]['search']['value'] == 'Tips';
	
	/////////////////////////////////////////////
	
	if($isVoidedFilter) {
		
		$query = "SELECT * FROM `transactions` WHERE `payment_status` = 'Voided' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `transactions` WHERE `payment_status` = 'Voided' AND `amount` > 0")->fetchColumn();
	} elseif($isPendingFilter) {
		
		$query = "SELECT * FROM `transactions` WHERE `payment_status` = 'Pending' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `transactions` WHERE `payment_status` = 'Pending' AND `amount` > 0")->fetchColumn();
	} elseif($isCapturedFilter) {
		
		$query = "SELECT * FROM `transactions` WHERE `payment_status` = 'Captured' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `transactions` WHERE `payment_status` = 'Captured' AND `amount` > 0")->fetchColumn();
	} elseif($isRefundedFilter) {
		
		$query = "SELECT * FROM `transactions` WHERE `payment_status` = 'Refunded' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `transactions` WHERE `payment_status` = 'Refunded' AND `amount` > 0")->fetchColumn();
	} elseif($isErroredDeclinedFilter) {
		
		$query = "SELECT * FROM `transactions` WHERE `payment_status` = 'Declined' OR `payment_status` = 'Errored' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `transactions` WHERE `payment_status` = 'Declined' OR `payment_status` = 'Errored' AND `amount` > 0")->fetchColumn();
	} elseif($isApprovedFilter) {
		
		$query = "SELECT * FROM `transactions` WHERE `payment_status` = 'Approved' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `transactions` WHERE `payment_status` = 'Approved' AND `amount` > 0")->fetchColumn();
	} elseif($isTipsFilter) {
		
		$query = "SELECT * FROM `transactions` WHERE `type` = 'tips' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `transactions` WHERE `type` = 'tips' AND `amount` > 0")->fetchColumn();
	} elseif($selectedOption === 'condo-application-fee') {
		
		$query = "SELECT * FROM transactions WHERE `form` = 'condo-application-fee' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM transactions WHERE `form` = 'condo-application-fee' AND `amount` > 0")->fetchColumn();
	} elseif($selectedOption === 'purchase-pass') {
		
		$query = "SELECT * FROM transactions WHERE `form` = 'purchase-pass' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM transactions WHERE `form` = 'purchase-pass' AND `amount` > 0")->fetchColumn();
	} elseif($selectedOption === 'recurring-billing') {
		$query = "SELECT * FROM transactions WHERE `form` = 'recurring-billing' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM transactions WHERE `form` = 'recurring-billing' AND `amount` > 0")->fetchColumn();
	} elseif($selectedOption === 'sign-up') {
		$query = "SELECT * FROM transactions WHERE `form` = 'sign-up' AND `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM transactions WHERE `form` = 'sign-up' AND `amount` > 0")->fetchColumn();
	} elseif($selectedOption === 'default.show_all') {
		$query = "SELECT * FROM transactions WHERE  `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM transactions WHERE  `amount` > 0")->fetchColumn();
	} elseif(!empty($searchValue)) {
		
		$orderByquery = $orderBy ? " AND `amount` > 0 ORDER BY transactions.$orderBy $orderDirection" : "  WHERE `amount` > 0 ORDER BY `timestamp` DESC";
		
		$query = "SELECT * FROM `transactions` WHERE `id` LIKE '%$searchValue%' OR `type` LIKE '%$searchValue%' OR `payment_status` LIKE '%$searchValue%' OR `amount` LIKE '%$searchValue%' OR `billing_first_name` LIKE '%$searchValue%'  OR `billing_last_name` LIKE '%$searchValue%' OR `billing_email` LIKE '%$searchValue%' OR `billing_phone` LIKE '%$searchValue%' OR `ip_address` LIKE '%$searchValue%' $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
		
		$filteredRecords = Database::Action("SELECT COUNT(*) FROM `transactions` WHERE `id` LIKE '%$searchValue%' OR `type` LIKE '%$searchValue%' OR `payment_status` LIKE '%$searchValue%' OR `amount` LIKE '%$searchValue%' OR `billing_first_name` LIKE '%$searchValue%'  OR `billing_last_name` LIKE '%$searchValue%' LIKE '%$searchValue%' OR `billing_email` LIKE '%$searchValue%' OR `billing_phone` LIKE '%$searchValue%' OR `ip_address` LIKE '%$searchValue%'")->fetchColumn();
	} else {
		
		
		$query = "SELECT * FROM `transactions` WHERE `amount` > 0 $orderByquery";
		
		if($length != -1) {
			$query .= " LIMIT $start, $length";
		}
	}
	
	$results = Items\Transaction::FetchAll(Database::Action($query));
	
	try {
		// Set Response
		$json_response = array(
			'status'          => 'success',
			'message'         => 'DataTables loaded successfully.',
			'draw'            => $draw,
			'recordsTotal'    => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data'            => array_map(function(Items\Transaction $item) {
				return array(
					'id'                 => $item->getId(),
					'type'               => ucwords(str_replace('_', ' ', $item->getType())),
					'payment_status'     => $item->getPaymentStatus(),
					'amount'             => $item->getAmount(TRUE) ?? 'N/A',
					'billing_first_name' => Render::GetTemplate('admin/items/transactions/full-name.twig', array(
						'name' => $item->getBillingFirstName(),
						'link' => $item->getMember()?->getLink()
					)),
					'billing_last_name'  => Render::GetTemplate('admin/items/transactions/full-name.twig', array(
						'name' => $item->getBillingLastName(),
						'link' => $item->getMember()?->getLink()
					)),
					'billing_phone'      => $item->getBillingPhone(),
					'email'              => $item->getBillingEmail(),
					'reservation'        => Render::GetTemplate('admin/items/transactions/reservation.twig', array(
						'rsvp' => $item->getMemberReservation()?->toArray(),
						'link' => sprintf("/user/reservations/events/%d", $item->getId())
					)),
					'user_agent'         => array(
						'value' => $item->getUserAgent(),
						'label' => $item->getUserAgent(TRUE, array('browser'))
					),
					'ip_address'         => array(
						'value' => $item->getIpAddress(),
						'label' => Render::GetTemplate('admin/ip-address.twig', array(
							'ip_address'   => $item->getIpAddress(),
							'country_flag' => $item->getIpAddress()->getCountry()?->getEmoji(),
							'link'         => $item->getIpAddress()->getLink()
						))
					),
					'timestamp'          => array(
						'value' => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
						'label' => $item->getLastTimestamp()->format('Y-m-d H:i:s')
					),
					'form'               => $item->getForm(),
					'is_captured'        => $item->isCaptured(),
					'is_error'           => $item->isError(),
					'is_pending'         => $item->isPending(),
					'is_refunded'        => $item->isRefunded(),
					'is_voided'          => $item->isVoided(),
					'is_tip'             => $item->isTip(),
					'item'               => $item->toArray(),
					'options'            => Render::GetTemplate('admin/items/transactions/options.twig', array(
						'type'        => $item->getType(),
						'is_captured' => $item->isCaptured(),
						'is_error'    => $item->isError(),
						'is_pending'  => $item->isPending(),
						'is_refunded' => $item->isRefunded(),
						'is_voided'   => $item->isVoided(),
						'is_tip'      => $item->isTip()
					))
				);
			}, $results),
			'categories'      => call_user_func(fn($categories) => array(
				'data'   => $categories,
				'html'   => Render::GetTemplate('admin/items/transactions/categories.twig', array('categories' => $categories)),
				'filter' => 'form'
			), array(
				'default.show_all'  => 'All Transactions',
				'tips'              => 'Tips',
				'purchase-pass'     => 'Passes',
				'recurring-billing' => 'Recurring',
				'sign-up'           => 'Sign-up'
			))
		);
	} catch(Error|PDOException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception),
			'data'    => array()
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage(),
			'data'    => array()
		);
	}
	
	// Output Response
	echo json_encode($json_response);
