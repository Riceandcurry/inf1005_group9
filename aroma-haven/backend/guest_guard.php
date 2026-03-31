<?php
require_once __DIR__ . '/init.php';

function require_guest() {
    global $auth;

    if ($auth->isLogged()) {
        $pendingUserId = (int) ($_SESSION['otp_pending_user_id'] ?? 0);
        $currentUserId = (int) $auth->getCurrentUID();
        $otpVerified = isset($_SESSION['otp_verified']) && $_SESSION['otp_verified'] === true;

        if ($pendingUserId > 0 && $pendingUserId === $currentUserId && !$otpVerified) {
            header("Location: verify-otp.php");
            exit;
        }

        header("Location: shop-coffee.php");
        exit;
    }
}
?>
