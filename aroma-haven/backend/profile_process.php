<?php
// profile_process.php
require_once 'init.php';
require_once 'util.php';

function update_profile_details($user_id, $fname, $lname, $email) {
    global $conn;
    // Update email in phpauth_users
    $stmt = $conn->prepare('UPDATE phpauth_users SET email = ? WHERE id = ?');
    $stmt->execute([$email, $user_id]);
    // Update name in user_profiles
    $stmt2 = $conn->prepare('UPDATE user_profiles SET fname = ?, lname = ? WHERE user_id = ?');
    $stmt2->execute([$fname, $lname, $user_id]);
    return true;
}

function change_profile_password($auth, $current_email, $current, $new, $confirm) {
    if ($new !== $confirm) {
        return ['error' => true, 'message' => 'New passwords do not match.'];
    }
    $result = $auth->changePassword($current_email, $current, $new, $new);
    return $result;
}
