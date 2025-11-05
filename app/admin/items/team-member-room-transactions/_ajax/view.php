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
        'data'       => array_map(fn (Items\TravelAffiliateMembers\AffiliateRoomTransaction $item) => array(



            'affiliate_id'  => $item->getAffiliateId(),

            'id'  => $item->getId(),

            'amount' => "$" . $item->getTransactionAmount(),

            'ticket_commission_rate' => $item->getTicketCommissionRate() . "%",

            'affiliate_earned_commission' => "$" . round(
                $item->getTransactionAmount() * ($item->getTicketCommissionRate() / 100),
                2
            ),

            'date_time' => $item->getTimestamp()->format('M jS, Y, g:iA'),

            'purchaser_profile_link' => (
                ($username = Database::Action("SELECT `username` FROM `members` WHERE `id` = :id", array(
                    'id' => $item->getPurchaserSocialMemberId()
                ))->fetchAll(PDO::FETCH_COLUMN, 0)[0] ?? null) !== null
                ? '<a target="_blank" href="/members/profile/' . $username . '">Profile Link</a>'
                : 'N/A'
            ),

            'booking_dates' => $item->getBookingDates(),

            'room_name' => $item->getRoomName(),

            'date_end' => (new DateTime($item->getDateEnd()))->format('F jS, Y'),

            'admin_approved' => '<input class="admin-approved-affiliate-room-transaction-checkbox" type="checkbox" ' . ($item->isAdminApproved() ? 'checked' : '') . ' style="transform: scale(1.5)" />',

            'is_banned' => '<input class="admin-banned-affiliate-room-transaction-checkbox" type="checkbox" ' . ($item->isAdminBanned() ? 'checked' : '') . ' style="transform: scale(1.5)" />',


        ), Items\TravelAffiliateMembers\AffiliateRoomTransaction::FetchAll(Database::Action("SELECT * FROM `affiliate_room_transactions` WHERE `affiliate_id` = :affiliate_id AND `admin_approved` = :admin_approved AND `is_banned` = :is_banned ORDER BY `timestamp` DESC", array(
            'affiliate_id' => $member->getId(),
            'admin_approved' => 0,
            'is_banned' => 0,
        )))),

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
