<?php
/**
 * Enlightening All - Member QR Verification
 * Redirects verified users to their public profile page.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
date_default_timezone_set('America/New_York');

define('ALLOW_ANONYMOUS', true);

//use Items\Enums\Sizes;
use Items\Enums\Sizes\Avatar as AvatarSize;


include('includes/header.php');

try {
    // --- HASH EXTRACTION ---
    $hash = '';
    if (!empty($_GET['hash'])) {
        $hash = trim($_GET['hash']);
    } else {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $params);
        $hash = isset($params['hash']) ? trim($params['hash']) : '';
    }

// Clean up accidental "hash=" prefix
    if (str_starts_with($hash, 'hash=')) {
        $hash = substr($hash, 5);
    }

    if (!$hash || strlen($hash) < 8) {
        throw new Exception('Invalid or missing verification hash.');
    }


    // --- FETCH RECORD (type-agnostic) ---
    $record = null;
    $stmt = Database::Action("
        SELECT *
        FROM member_verify_qr_codes
        WHERE UPPER(hash) = UPPER(:hash)
        ORDER BY id DESC
        LIMIT 1
    ", ['hash' => $hash]);

    if ($stmt instanceof PDOStatement) {
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif (is_array($stmt)) {
        $record = $stmt;
    }

    if (!$record || empty($record['member_id'])) {
        throw new Exception('Verification link not found or invalid.');
    }

    // --- EXPIRATION CHECK ---
    $createdAtRaw = $record['timestamp'] ?? '';
    $tz = new DateTimeZone('America/New_York');
    $createdAtObj = new DateTime($createdAtRaw, $tz);
    $now = new DateTime('now', $tz);
    $intervalSeconds = $now->getTimestamp() - $createdAtObj->getTimestamp();
    $expiresAfter = 7 * 24 * 60 * 60; // 7 days
    $isExpired = $intervalSeconds > $expiresAfter;

    // --- LOAD MEMBER INFO ---
    $memberObj = null;
    try {
        $memberObj = Membership::Init($record['member_id']);
    } catch (Throwable $t) {
        $memberObj = null;
    }

    $fullName = $memberObj ? htmlspecialchars($memberObj->getFullName()) : "Active Member";
    //$avatar   = $memberObj?->getAvatar()?->getImage(Sizes\Avatar::L, true) ?: Items\Defaults::AVATAR_L;
    //$avatar = $memberObj?->getAvatar()?->getImage(256, true) ?: Items\Defaults::AVATAR_L;
    $avatar = $memberObj?->getAvatar()?->getImage(AvatarSize::XL, true) ?: Items\Defaults::AVATAR_L;



    // --- GET USERNAME FOR REDIRECT ---
    $username = '';
    if ($memberObj && method_exists($memberObj, 'getUsername')) {
        $username = trim($memberObj->getUsername());
    } elseif ($memberObj && property_exists($memberObj, 'username')) {
        $username = trim($memberObj->username);
    }

    if ($username) {
        $username = strtolower($username);
    }

    $redirectUrl = $username
            ? "/members/profile/" . urlencode($username)
            : "/members/";

    ?>
    <div class="container-fluid main-content">
        <div class="container py-5 text-center">
            <?php if ($isExpired): ?>
                <div class="card shadow border-danger mx-auto" style="max-width: 420px;">
                    <div class="card-body">
                        <h2 class="text-danger mb-3"><i class="fa fa-times-circle"></i> Verification Failed</h2>
                        <div class="alert alert-danger">
                            This QR code has expired.
                        </div>
                        <p class="small text-muted mb-0">
                            Please ask the member to regenerate their QR code.<br>
                            Hash: <?php echo htmlspecialchars($hash); ?><br>
                            Generated: <?php echo htmlspecialchars($createdAtRaw); ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-lg border-0 mx-auto verified-card" style="max-width: 420px; position: relative;">
                    <div id="verified-banner"
                         style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);
                         background:#28a745;color:white;padding:6px 18px;border-radius:20px;
                         font-weight:bold;animation:fadePulse 2s infinite;">
                        ✅ VERIFIED
                    </div>
                    <div class="card-body">
                        <img src="<?php echo $avatar; ?>"
                             alt="<?php echo $fullName; ?>"
                             class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <h2 class="mb-2"><?php echo $fullName; ?></h2>
                        <p class="text-success h5 mb-3"><i class="fa fa-check-circle"></i> Verified Member</p>
                        <div class="alert alert-success">
                            This QR code is valid and belongs to an active member of Enlightening All.
                        </div>
                        <p class="small text-muted mb-0">
                            Verified on <?php echo date('F j, Y, g:i a'); ?><br>
                            IP: <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']); ?>
                        </p>
                        <div id="redirect-msg" class="text-muted small mt-3">
                            Redirecting to profile page in 5 seconds...
                        </div>
                        <a href="<?php echo $redirectUrl; ?>" class="btn btn-success btn-sm mt-3">
                            Go Now
                        </a>
                    </div>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = "<?php echo $redirectUrl; ?>";
                    }, 5000);
                </script>
            <?php endif; ?>
        </div>
    </div>

    <style>
        @keyframes fadePulse {
            0% { opacity: 1; transform: translateX(-50%) scale(1); }
            50% { opacity: 0.7; transform: translateX(-50%) scale(1.05); }
            100% { opacity: 1; transform: translateX(-50%) scale(1); }
        }
        .verified-card {
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <?php

} catch (Exception $e) {
    ?>
    <div class="container-fluid main-content">
        <div class="container py-5 text-center">
            <div class="card shadow border-danger mx-auto" style="max-width: 420px;">
                <div class="card-body">
                    <h2 class="text-danger mb-3"><i class="fa fa-times-circle"></i> Verification Failed</h2>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($e->getMessage()); ?>
                    </div>
                    <p class="small text-muted">Please ask the member to regenerate their QR code.</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}

include('includes/footer.php');
include('includes/body-close.php');
?>
