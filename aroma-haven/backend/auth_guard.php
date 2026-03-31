<?php

require_once __DIR__ . '/init.php';

function require_login() {
    global $auth;

    if (!$auth->isLogged()) {
        $_SESSION['error'] = "Please log in first.";
        header("Location: login.php");
        exit;
    }

    $currentUserId = (int) $auth->getCurrentUID();
    $pendingUserId = (int) ($_SESSION['otp_pending_user_id'] ?? 0);
    $otpVerified = isset($_SESSION['otp_verified']) && $_SESSION['otp_verified'] === true;

    if ($pendingUserId > 0) {
        if ($pendingUserId !== $currentUserId) {
            unset($_SESSION['otp_pending_user_id'], $_SESSION['otp_pending_email'], $_SESSION['otp_verified']);
            $_SESSION['error'] = "Session mismatch detected. Please log in again.";
            header("Location: login.php");
            exit;
        }

        if (!$otpVerified) {
            $_SESSION['error'] = "Please complete OTP verification first.";
            header("Location: verify-otp.php");
            exit;
        }
    }
}
?>
