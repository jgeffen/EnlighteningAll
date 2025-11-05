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
	 * @var Admin\User        $admin
	 */
	
	try {
		// Set Response
		$json_response = array(
			'status' => 'success',
			'charts' => array_reduce(Database::Action("SELECT `title` AS `label`, DATE_FORMAT(`timestamp`, '%Y-%m') AS `date`, MAX(`total`) AS `total` FROM (SELECT UPPER(CONCAT(`form`, ' (', `type`, ')')) AS `title`, `timestamp`, SUM(`amount`) OVER (PARTITION BY CONCAT(`form`, `type`) ORDER BY `timestamp` ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS `total` FROM `transactions` WHERE `payment_status` = 'Approved') AS `subquery` WHERE `total` > 0 AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH) GROUP BY `label`, `date`")->fetchAll(PDO::FETCH_ASSOC), function(array $items, array $item) {
				$items['TEMP'][$item['label']][] = array('x' => $item['date'], 'y' => $item['total']);
				$items[match ($item['label']) {
					'PURCHASE-PASS (SALE)'     => 'Purchases Chart',
					'RECURRING-BILLING (SALE)' => 'Membership Chart',
					'SIGN-UP (SALE)'           => 'Membership Chart',
					default                    => 'Sales Chart'
				}]['dataset'][$item['label']]    = array(
					'label' => $item['label'],
					'data'  => $items['TEMP'][$item['label']]
				);
				
				return $items;
			}, array())
		);
		
		// Unset Temp
		unset($json_response['charts']['TEMP']);
		
		// Set Inner Properties to Value Only
		$json_response['charts'] = array_map(function($array) {
			return array_values(array_map(function($array) {
				return array_values(array_reverse($array));
			}, $array))[0];
		}, $json_response['charts']);
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