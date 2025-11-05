<?php
/**
 * @var Router\Dispatcher $dispatcher
 * @var Membership        $member
 */

try {
    $parsed = parse_url($_SERVER['REQUEST_URI']);
    $hash = $parsed['query'] ?? '';

    if (!$hash || strlen($hash) < 8) {
        throw new Exception('Invalid or missing QR hash.');
    }

    // Redirect cleanly to the actual handler
    header("Location: /members/verify-member?hash=" . urlencode($hash));
    exit;

} catch (Exception $e) {
    Render::ErrorDocument(404);
}
?>
