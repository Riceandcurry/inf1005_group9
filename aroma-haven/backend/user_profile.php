<?php
require_once 'auth.php';
require_once 'util.php';

function get_user_profile($user_id) {
    global $conn;
    $stmt = $conn->prepare('SELECT fname, lname, email FROM phpauth_users u JOIN user_profiles p ON u.id = p.user_id WHERE u.id = ?');
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function update_user_profile($user_id, $fname, $lname, $email) {
    global $conn, $auth;
    // Update email in phpauth_users
    $stmt1 = $conn->prepare('UPDATE phpauth_users SET email = ? WHERE id = ?');
    $stmt1->execute([$email, $user_id]);
    // Update profile in user_profiles
    $stmt2 = $conn->prepare('UPDATE user_profiles SET fname = ?, lname = ? WHERE user_id = ?');
    $stmt2->execute([$fname, $lname, $user_id]);
    return true;
}

function change_user_password($user_id, $current_password, $new_password) {
    global $auth, $conn;
    $user = $auth->getUser($user_id);
    $email = $user['email'] ?? '';
    $result = $auth->changePassword($email, $current_password, $new_password, $new_password);
    return $result;
}
?>