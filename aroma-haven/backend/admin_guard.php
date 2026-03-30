<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/admin_helpers.php';

function require_admin(): void
{
    require_login();

    $currentUserId = ah_current_user_id();
    if ($currentUserId <= 0 || !ah_user_is_admin($currentUserId)) {
        http_response_code(403);
        $_SESSION['error'] = 'You do not have admin access.';
        header('Location: shop-coffee.php');
        exit;
    }

    if (ah_is_user_suspended($currentUserId)) {
        if (isset($_COOKIE['phpauth_session_cookie'])) {
            global $auth;
            $auth->logout($_COOKIE['phpauth_session_cookie']);
        }
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
?>
