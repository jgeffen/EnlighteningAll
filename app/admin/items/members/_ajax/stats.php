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
	
	try {
		// Set Response
		$json_response = array(
			'status' => 'success',
			'charts' => array_map(function($items) {
				$items = array_reduce($items, function(array $items, array $item) {
					$items['TEMP'][$item['label']][] = array('x' => $item['date'], 'y' => $item['total']);
					$items[match ($item['label']) {
						'MEMBERS (TOTAL)'        => 'Members Chart',
						'MEMBERS (APPROVED)'     => 'Members Chart',
						'MEMBERS (COUPLES)'      => 'Members Chart',
						'MEMBERS (PENDING)'      => 'Members Chart',
						'MEMBERS (SINGLES)'      => 'Members Chart',
						'MEMBERS (VERIFIED)'     => 'Members Chart',
						'POSTS (TOTAL)'          => 'Posts Chart',
						'POSTS (APPROVED)'       => 'Posts Chart',
						'POSTS (CONTEST)'        => 'Posts Chart',
						'POSTS (PENDING)'        => 'Posts Chart',
						'POSTS (PRIVATE)'        => 'Posts Chart',
						'POSTS (PUBLIC)'         => 'Posts Chart',
						'SUBSCRIPTIONS (TOTAL)'  => 'Subscriptions Chart',
						'SUBSCRIPTIONS (ACTIVE)' => 'Subscriptions Chart',
						default                  => 'Unknown Chart'
					}]['dataset'][$item['label']]    = array(
						'label' => $item['label'],
						'data'  => $items['TEMP'][$item['label']]
					);
					
					return $items;
				}, array());
				
				// Unset Temp
				unset($items['TEMP']);
				
				return $items;
			}, array(
				'all-time'    => Database::Action("WITH `date_range` AS (SELECT DISTINCT DATE_FORMAT(`timestamp`, '%Y-%m') AS `date` FROM `members`) SELECT 'MEMBERS (TOTAL)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (APPROVED)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `approved` IS TRUE AND `verified` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (PENDING)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `approved` IS FALSE AND `verified` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (VERIFIED)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `verified` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (COUPLES)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `couple` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (SINGLES)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `couple` IS FALSE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (TOTAL)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (APPROVED)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (PENDING)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS FALSE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (PRIVATE)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND `visibility` = 'FRIENDS' AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (PUBLIC)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND `visibility` = 'MEMBERS' AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (CONTEST)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND EXISTS (SELECT 1 FROM `member_post_type_social` WHERE `member_post_type_social`.`member_post_id` = `member_posts`.`id` AND `member_post_type_social`.`member_contest_id` IS NOT NULL) AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'SUBSCRIPTIONS (TOTAL)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `id` IN (SELECT `member_id` FROM `member_subscriptions`) AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` UNION ALL SELECT 'SUBSCRIPTIONS (ACTIVE)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `id` IN (SELECT `member_id` FROM `member_subscriptions` WHERE `status` = 'ACTIVE') AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date`) AS `total` FROM `date_range` ORDER BY `label` LIKE 'MEMBERS%' DESC, `label` LIKE '%(TOTAL)', `label` DESC, `date`")->fetchAll(PDO::FETCH_ASSOC),
				'six-months'  => Database::Action("WITH `date_range` AS (SELECT DISTINCT DATE_FORMAT(`timestamp`, '%Y-%m') AS `date` FROM `members` WHERE `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) SELECT 'MEMBERS (TOTAL)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (APPROVED)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `approved` IS TRUE AND `verified` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (PENDING)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `approved` IS FALSE AND `verified` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (VERIFIED)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `verified` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (COUPLES)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `couple` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (SINGLES)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `couple` IS FALSE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (TOTAL)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (APPROVED)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (PENDING)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS FALSE AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (PRIVATE)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND `visibility` = 'FRIENDS' AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (PUBLIC)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND `visibility` = 'MEMBERS' AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (CONTEST)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND EXISTS (SELECT 1 FROM `member_post_type_social` WHERE `member_post_type_social`.`member_post_id` = `member_posts`.`id` AND `member_post_type_social`.`member_contest_id` IS NOT NULL) AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'SUBSCRIPTIONS (TOTAL)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `id` IN (SELECT `member_id` FROM `member_subscriptions`) AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` UNION ALL SELECT 'SUBSCRIPTIONS (ACTIVE)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `id` IN (SELECT `member_id` FROM `member_subscriptions` WHERE `status` = 'ACTIVE') AND DATE_FORMAT(`timestamp`, '%Y-%m') <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) AS `total` FROM `date_range` ORDER BY `label` LIKE 'MEMBERS%' DESC, `label` LIKE '%(TOTAL)', `label` DESC, `date`")->fetchAll(PDO::FETCH_ASSOC),
				'thirty-days' => Database::Action("WITH `date_range` AS (SELECT DISTINCT DATE(`timestamp`) AS `date` FROM `members` WHERE `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) SELECT 'MEMBERS (TOTAL)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (APPROVED)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `approved` IS TRUE AND `verified` IS TRUE AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (PENDING)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `approved` IS FALSE AND `verified` IS TRUE AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (VERIFIED)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `verified` IS TRUE AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'SUBSCRIPTIONS (TOTAL)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `id` IN (SELECT `member_id` FROM `member_subscriptions`) AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (COUPLES)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `couple` IS TRUE AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'MEMBERS (SINGLES)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `couple` IS FALSE AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (TOTAL)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (APPROVED)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (PENDING)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS FALSE AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (PRIVATE)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND `visibility` = 'FRIENDS' AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (PUBLIC)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND `visibility` = 'MEMBERS' AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'POSTS (CONTEST)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND EXISTS (SELECT 1 FROM `member_post_type_social` WHERE `member_post_type_social`.`member_post_id` = `member_posts`.`id` AND `member_post_type_social`.`member_contest_id` IS NOT NULL) AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` UNION ALL SELECT 'SUBSCRIPTIONS (ACTIVE)' AS `label`, `date_range`.`date`, (SELECT COUNT(*) FROM `members` WHERE `id` IN (SELECT `member_id` FROM `member_subscriptions` WHERE `status` = 'ACTIVE') AND DATE(`timestamp`) <= `date_range`.`date` AND `timestamp` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) AS `total` FROM `date_range` ORDER BY `label` LIKE 'MEMBERS%' DESC, `label` LIKE '%(TOTAL)', `label` DESC, `date`")->fetchAll(PDO::FETCH_ASSOC)
			))
		);
		
		// Set Inner Properties to Value Only
		$json_response['charts'] = array_map(function($array) {
			return array_map(function($array) {
				return array_values(array_map(function($array) {
					return array_values(array_reverse($array));
				}, $array))[0];
			}, $array);
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