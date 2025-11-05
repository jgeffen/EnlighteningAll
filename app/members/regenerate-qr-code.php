<?php
/*
 * Enlightening All - Regenerate Member QR Code
 * Author: Jonathon Geffen / Daerik Base Framework
 */

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

try {
    $memberId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$memberId) {
        throw new Exception('Invalid member ID.');
    }

    $profile = Items\Member::Init($memberId);
    if (!$profile) {
        throw new Exception('Member not found.');
    }

    // Create new unique hash
    $hash = strtoupper(md5(uniqid($profile->getId(), true)));

    // Store or update hash record
    Database::Action("
        INSERT INTO `member_verify_qr_codes`
        SET
            `initiated_by` = :initiated_by,
            `member_id`    = :member_id,
            `hash`         = :hash,
            `author`       = NULL,
            `user_agent`   = :user_agent,
            `ip_address`   = :ip_address,
            `created_at`   = NOW()
        ON DUPLICATE KEY UPDATE
            `hash` = VALUES(`hash`),
            `user_agent` = VALUES(`user_agent`),
            `ip_address` = VALUES(`ip_address`),
            `updated_at` = NOW()
    ", [
        'initiated_by' => $member->getId(),
        'member_id'    => $profile->getId(),
        'hash'         => $hash,
        'user_agent'   => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
        'ip_address'   => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
    ]);

    // Build new QR code
    $url = "https://enlighteningall.com/verify-member?" . $hash;
    $qr = Builder::create()
        ->writer(new PngWriter())
        ->data($url)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(600)
        ->margin(0)
        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->build();

    echo json_encode([
        'status'  => 'success',
        'data'    => $qr?->getDataUri(),
        'message' => 'New QR code generated successfully.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => Debug::Exception($e)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
