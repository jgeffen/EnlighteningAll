<?php
/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single web site may be purchased from Daerik.com
	@Author: Daerik
	*/

// TODO: Show country flag with IP [https://github.com/chrislim2888/IP2Location-PHP-Module]
// TODO: Create API method for admin panel handling membership

// Imports
use Jenssegers\Agent\Agent;

// Output Json
echo json_encode(array(
    'data' => array_map(function ($item) {
        return array(
            'id'             => $item['id'],
            'is_approved'    => $item['is_approved'],
            'username'       => $item['username'],
            'full_name_last' => $item['full_name_last'],
            'email'          => $item['email'],
            'is_verified'    => $item['is_verified'],
            'country'        => $item['country'],
            'user_agent'     => call_user_func(function ($agent) {
                return implode(' | ', array_filter(array(
                    'browser'  => implode(' ', array_filter(array($agent->browser(), $agent->version($agent->browser())))),
                    'device'   => $agent->device(),
                    'platform' => implode(' ', array_filter(array($agent->platform(), $agent->version($agent->platform())))),
                    'language' => !empty($agent->languages()[0]) ? Locale::getDisplayLanguage($agent->languages()[0]) : ''
                )));
            }, new Agent(NULL, $item['user_agent'])),
            'ip_address'     => $item['ip_address'],
            'last_logged_in' => $item['last_logged_in'] ?? 'Never Logged In'
        );
    }, Database::Action(
        "SELECT CONCAT_WS(' ', `travel_affiliate_members`.`first_name`, `travel_affiliate_members`.`last_name`) AS `full_name`, 
    CONCAT_WS(', ', `travel_affiliate_members`.`last_name`, `travel_affiliate_members`.`first_name`) AS `full_name_last`,  
    MAX(`travel_affiliate_member_logs`.`timestamp`) AS `last_logged_in`, `travel_affiliate_members`.* 
    FROM `travel_affiliate_members` 
    LEFT JOIN `travel_affiliate_member_logs` ON (`travel_affiliate_member_logs`.`member_id` = `travel_affiliate_members`.`id` AND `travel_affiliate_member_logs`.`action` IN ('Login')) 
    GROUP BY `travel_affiliate_members`.`id`"
    )->fetchAll(PDO::FETCH_ASSOC))
));
