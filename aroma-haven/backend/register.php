<?php
require_once __DIR__ . '/util.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin_helpers.php';

function register_process($data){

    global $auth, $conn;

    $email = sanitize_input($data['email']);
    $password = $data['password'];
    $fname = sanitize_input($data['first_name']);
    $lname = sanitize_input($data['last_name']);
    $brew_style = isset($data['brew_method']) ? (int)$data['brew_method'] : null;

    $result = $auth->register($email, $password, $password);

    if($result['error']){
        return $result['message'];
    }

    $user_id = $result['uid'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO user_profiles (user_id, fname, lname, brew_style_id)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$user_id, $fname, $lname, $brew_style]);

        $customerRoleId = ah_get_role_id('customer');
        if ($customerRoleId > 0) {
            $roleStmt = $conn->prepare(
                'INSERT INTO user_roles (user_id, role_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_id = user_roles.user_id'
            );
            $roleStmt->execute([$user_id, $customerRoleId]);
        }

        $statusStmt = $conn->prepare(
            'INSERT INTO user_status (user_id, is_suspended) VALUES (?, 0) ON DUPLICATE KEY UPDATE user_id = user_status.user_id'
        );
        $statusStmt->execute([$user_id]);

    } catch (PDOException $e) {
        ah_log_error('register_process_db_error', $e, [
            'user_id' => $user_id,
        ]);
        return 'Unable to create your account right now. Please try again later.';
    }

    return "";
}
?>
