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

// TODO: Create API method for admin panel handling membership

try {
	// Set Response
	$json_response = array(
		'status'  => 'success',
		'message' => 'DataTables loaded successfully.',
		'data'    => array_map(function (TravelAffiliateMembership $item) {
			return array(
				'id'             => $item->getId(),
				'is_approved'    => $item->isApproved(),
				'is_banned'      => $item->isBanned(),
				'is_verified'    => $item->isVerified(),
				'username'       => array(
					'value' => $item->getUsername(),
					'label' => Render::GetTemplate('admin/items/team-members/username.twig', array(
						'username' => $item->getUsername(),
						'flags'    => array('verified' => $item->isVerified())
					))
				),
				'full_name_last' => trim($item->getFullNameLast()),
				'team_member_links' => "?TeamMember=" . $item->getId(),
				'user_agent'     => $item->getUserAgent(TRUE),
				'browser'        => $item->getUserAgent(TRUE, array('browser')) ?: Render::GetTemplate('admin/null.twig'),
				'device'         => $item->getUserAgent(TRUE, array('device')) ?: Render::GetTemplate('admin/null.twig'),
				'platform'       => $item->getUserAgent(TRUE, array('platform')) ?: Render::GetTemplate('admin/null.twig'),
				'language'       => $item->getUserAgent(TRUE, array('language')) ?: Render::GetTemplate('admin/null.twig'),

				'item'           => $item->toArray(),
				'options'        => Render::GetTemplate('admin/items/team-members/options.twig', array(
					'id'    => $item->getId(),

					'flags' => array(
						'admin'      => Admin\Privilege(2),
						'approved'   => $item->isApproved(),
						'banned'     => $item->isBanned(),
						'verified'   => $item->isVerified(),
					)
				)),
				'ip_address'     => array(
					'value' => $item->getIpAddress()->getValue(),
					'label' => Render::GetTemplate('admin/ip-address.twig', array(
						'ip_address'   => $item->getIpAddress(),
						'country_flag' => $item->getIpAddress()->getCountry()?->getEmoji(),
						'link'         => $item->getIpAddress()->getLink()
					))
				),
				'timestamp'      => array(
					'value' => $item->getTimestamp()->format('U'),
					'label' => $item->getTimestamp()->format('M j \'y')
				)
			);
		}, TravelAffiliateMembership::FetchAll(Database::Action("SELECT * FROM `travel_affiliate_members` WHERE `is_employee` = 1 ORDER BY `last_name`, `first_name`")))
	);
} catch (Exception $exception) {
	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => Debug::Exception($exception),
		'data'    => array()
	);
}

// Output Response
echo json_encode($json_response);
