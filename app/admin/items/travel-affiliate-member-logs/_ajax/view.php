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
	// Variable Defaults
	$member = TravelAffiliateMembership::Init($dispatcher->getTableId());

	// Check Member
	if (is_null($member)) throw new Exception('Travel Affiliate Member not found in database.');

	// Set Response
	$json_response = array(
		'status'     => 'success',
		'message'    => 'DataTables loaded successfully.',
		'data'       => array_map(fn (Items\TravelAffiliateMembers\Log $item) => array(
			'id'                 => $item->getId(),
			'action'             => $item->getType()?->getLabel(),
			'table_name'         => $item->getTableName(),
			'table_id'           => $item->getTableId(),
			'user_agent'         => $item->getUserAgent(TRUE, array('browser')),
			'timestamp'          => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
			'item'               => $item->toArray(),
			'ip_address'         => $item->getIpAddress(),
			'ip_address_display' => Render::GetTemplate('admin/ip-address.twig', array(
				'ip_address'   => $item->getIpAddress(),
				'country_flag' => $item->getIpAddress()->getCountry()?->getEmoji(),
				'link'         => $item->getIpAddress()->getLink()
			))
		), Items\TravelAffiliateMembers\Log::FetchAll(Database::Action("SELECT * FROM `travel_affiliate_member_logs` WHERE `member_id` = :member_id ORDER BY `timestamp` DESC", array(
			'member_id' => $member->getId()
		)))),
		'categories' => call_user_func(fn ($categories) => !$categories['TABLE NAMES'] ? array() : array(
			'data'   => $categories['TABLE NAMES'],
			'html'   => Render::GetTemplate('admin/items/travel-affiliate-members/logs/categories.twig', array('categories' => $categories)),
			'filter' => 'table_name'
		), array(
			'DEFAULTS'    => array('default.show_all' => 'SHOW ALL'),
			'ACTIONS'     => Database::Action("SELECT CONCAT('action.', `type`) AS `value`, UPPER(`type`) AS `text` FROM `travel_affiliate_member_logs` GROUP BY `type` ORDER BY `type`")->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE),
			'TABLE NAMES' => Database::Action("SELECT `table_name` AS `value`, UPPER(REPLACE(REPLACE(`table_name`, 'travel_affiliate_member_', ''), '_', ' ')) AS `text` FROM `information_schema`.`tables` WHERE `table_schema` = :table_schema AND LOCATE('travel_affiliate_member_', `table_name`) ORDER BY `table_name`", array(
				'table_schema' => Database::Action("SELECT IFNULL(DATABASE(), '_') AS `table_schema`")->fetch(PDO::FETCH_COLUMN)
			))->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE)
		))
	);
} catch (Error | PDOException $exception) {
	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => Debug::Exception($exception),
		'data'    => array()
	);
} catch (Exception $exception) {
	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => $exception->getMessage(),
		'data'    => array()
	);
}

// Output Response
echo json_encode($json_response);
