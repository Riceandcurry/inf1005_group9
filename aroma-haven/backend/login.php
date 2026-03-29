<?php

require_once __DIR__ . '/admin_helpers.php';

function login_process($data){
    global $auth;

    $email = trim((string) ($data['email'] ?? ''));
    $password = (string) ($data['password'] ?? '');

    if ($email === '' || $password === '') {
        return 'Email and password are required.';
    }

    $userId = ah_find_user_id_by_email($email);
    if ($userId > 0 && ah_is_user_suspended($userId)) {
        return 'Your account is suspended. Please contact support.';
    }

    $result = $auth->login($email, $password);

    if($result['error']){
        return $result['message'];
    }

    return "";
}
?>
