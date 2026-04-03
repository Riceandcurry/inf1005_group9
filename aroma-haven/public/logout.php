<?php
require_once __DIR__ . '/../backend/init.php';

if (isset($_COOKIE['phpauth_session_cookie'])) {
    $auth->logout($_COOKIE['phpauth_session_cookie']);
}
unset($_SESSION['otp_pending_user_id'], $_SESSION['otp_pending_email'], $_SESSION['otp_verified']);
session_destroy();
header('Location: login.php');
exit;
