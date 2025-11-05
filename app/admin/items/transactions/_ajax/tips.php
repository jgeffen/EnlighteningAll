<?php
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	$start       = isset($_POST['start']) ? intval($_POST['start']) : 0;
	$length      = isset($_POST['length']) ? intval($_POST['length']) : 250;
	$draw        = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
	$searchValue = $_POST['search']['value'] ?? '';
	
	// Count all tips
	$totalRecords = Database::Action("
		SELECT COUNT(*)
		FROM `transactions`
		WHERE `type` = 'tips'
	")->fetchColumn();
	
	// Default order
	$orderColumn = array(
		'id',
		'payment_status',
		'amount',
		'billing_first_name',
		'billing_last_name',
		'billing_phone',
		'billing_email',
		'ip_address',
		'timestamp'
	);
	
	$orderBy        = '';
	$orderDirection = 'ASC';
	
	if (is_array($_POST['order']) && isset($_POST['order'][0]['column'])) {
		if (isset($orderColumn[$_POST['order'][0]['column']])) {
			$orderBy        = $orderColumn[$_POST['order'][0]['column']];
			$orderDirection = ($_POST['order'][0]['dir'] === 'desc') ? 'DESC' : 'ASC';
		}
	}
	
	$orderByquery = $orderBy ? "ORDER BY transactions.$orderBy $orderDirection" : "ORDER BY `timestamp` DESC";
	
	// Build query (tips only, with optional search)
	if (!empty($searchValue)) {
		$query = "
			SELECT *
			FROM `transactions`
			WHERE `type` = 'tips'
			  AND (
			  	`id` LIKE :search
			  	OR `payment_status` LIKE :search
			  	OR `amount` LIKE :search
			  	OR `billing_first_name` LIKE :search
			  	OR `billing_last_name` LIKE :search
			  	OR `billing_email` LIKE :search
			  	OR `billing_phone` LIKE :search
			  	OR `ip_address` LIKE :search
			  )
			$orderByquery
		";
		$params = array('search' => "%$searchValue%");
		$filteredRecords = Database::Action("
			SELECT COUNT(*)
			FROM `transactions`
			WHERE `type` = 'tips'
			  AND (
			  	`id` LIKE :search
			  	OR `payment_status` LIKE :search
			  	OR `amount` LIKE :search
			  	OR `billing_first_name` LIKE :search
			  	OR `billing_last_name` LIKE :search
			  	OR `billing_email` LIKE :search
			  	OR `billing_phone` LIKE :search
			  	OR `ip_address` LIKE :search
			  )
		", $params)->fetchColumn();
	} else {
		$query = "SELECT * FROM `transactions` WHERE `type` = 'tips' $orderByquery";
		$params = array();
		$filteredRecords = $totalRecords;
	}
	
	if ($length != -1) {
		$query .= " LIMIT $start, $length";
	}
	
	$results = Items\Transaction::FetchAll(Database::Action($query, $params));
	
	try {
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
					'billing_first_name' => $item->getBillingFirstName(),
					'billing_last_name'  => $item->getBillingLastName(),
					'billing_phone'      => $item->getBillingPhone(),
					'email'              => $item->getBillingEmail(),
					'paid_out'           => Render::GetTemplate('admin/items/transactions/paid-out.twig', array(
						'id'       => $item->getId(),
						'paid_out' => $item->isPaidOut()
					)),
					'timestamp'          => array(
						'value' => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
						'label' => $item->getLastTimestamp()->format('Y-m-d H:i:s')
					),
					'ip_address'         => array(
						'value' => $item->getIpAddress(),
						'label' => Render::GetTemplate('admin/ip-address.twig', array(
							'ip_address'   => $item->getIpAddress(),
							'country_flag' => $item->getIpAddress()->getCountry()?->getEmoji(),
							'link'         => $item->getIpAddress()->getLink()
						))
					),
				);
			}, $results)
		);
	} catch (Error|PDOException $exception) {
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception),
			'data'    => array()
		);
	} catch (Exception $exception) {
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage(),
			'data'    => array()
		);
	}
	
	echo json_encode($json_response);
