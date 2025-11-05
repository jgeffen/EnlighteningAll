<?php
/*
	Copyright (c) 2021–2025 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Membership        $member
 */

// TODO: Check if user is blocked

// Imports
use Items\Collections;
use Items\Enums\Types;
use Items\Enums\Options;
use Items\Enums\Sizes;
use Items\Enums\Statuses;
use Items\Members\Types as Member;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result;

try {
    // --------------------------------------------------
    // Member Initialization
    // --------------------------------------------------
    $profile = Items\Member::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));

    if (!$profile || !$profile->getId()) {
        throw new Exception('Invalid or missing member ID.');
    }

    // --------------------------------------------------
    // Generate Unique Hash
    // --------------------------------------------------
    $hash = strtoupper(md5(uniqid($profile->getId(), true)));
    $timestamp = date('Y-m-d H:i:s'); // ensure accurate timestamp (server time)

    // --------------------------------------------------
    // Insert or Update Record (Refresh Timestamp)
    // --------------------------------------------------
    Database::Action("
		INSERT INTO `member_verify_qr_codes` 
			SET 
				`initiated_by` = :initiated_by,
				`member_id`    = :member_id,
				`hash`         = :hash,
				`author`       = :author,
				`user_agent`   = :user_agent,
				`ip_address`   = :ip_address,
				`timestamp`    = :timestamp
		ON DUPLICATE KEY UPDATE 
				`hash`         = :hash,
				`user_agent`   = :user_agent,
				`ip_address`   = :ip_address,
				`timestamp`    = :timestamp
	", [
        'initiated_by' => $member->getId(),
        'member_id'    => $profile->getId(),
        'hash'         => $hash,
        'author'       => null,
        'user_agent'   => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
        'ip_address'   => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
        'timestamp'    => $timestamp
    ]);

    // --------------------------------------------------
    // Generate QR Code
    // --------------------------------------------------
    $url = "https://enlighteningall.com/verify-member?hash=" . $hash;
    //$url = "https://enlighteningall.com/app/members/verify-member.php?hash=" . urlencode($hash);

    //$url = "https://enlighteningall.com/members/verify-member.php?hash=" . urlencode($hash);


    $qr = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($url)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(600)
        ->margin(0)
        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->build();

    // --------------------------------------------------
    // Return JSON Response
    // --------------------------------------------------
    $json_response = [
        'data'    => $qr?->getDataUri(),
        'status'  => 'success',
        'message' => 'Successfully generated new verification QR code.',
        'url'     => $url,
        'hash'    => $hash,
        'timestamp' => $timestamp
    ];

} catch (PDOException $exception) {
    $json_response = [
        'status'  => 'error',
        'message' => Debug::Exception($exception)
    ];
} catch (Exception $exception) {
    $json_response = [
        'status'  => 'error',
        'message' => $exception->getMessage()
    ];
}

// --------------------------------------------------
// Output JSON
// --------------------------------------------------
header('Content-Type: application/json');
echo json_encode($json_response);
?>
