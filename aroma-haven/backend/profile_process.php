<?php
// profile_process.php
require_once 'init.php';
require_once 'util.php';

function update_profile_details($user_id, $fname, $lname) {
    global $conn;
    // Update name in user_profiles only. Email changes must go through auth-layer workflow.
    $stmt2 = $conn->prepare('UPDATE user_profiles SET fname = ?, lname = ? WHERE user_id = ?');
    $stmt2->execute([$fname, $lname, $user_id]);
    return true;
}

function change_profile_email($auth, int $user_id, string $new_email, string $current_password): array {
    $new_email = trim($new_email);
    if ($new_email === '') {
        return ['error' => true, 'message' => 'Email is required.'];
    }

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => true, 'message' => 'Please enter a valid email address.'];
    }

    if ($current_password === '') {
        return ['error' => true, 'message' => 'Current password is required to change email.'];
    }

    $currentUser = $auth->getCurrentUser();
    $currentEmail = trim((string) ($currentUser['email'] ?? ''));
    if ($currentEmail === '') {
        ah_log_error('profile_email_change_missing_current_email', null, [
            'user_id' => $user_id,
        ]);
        return ['error' => true, 'message' => 'Unable to verify your current account email.'];
    }

    if (strcasecmp($currentEmail, $new_email) === 0) {
        return ['error' => false, 'message' => 'Email is unchanged.'];
    }

    if (!method_exists($auth, 'changeEmail')) {
        ah_log_error('profile_email_change_method_missing', null, [
            'user_id' => $user_id,
        ]);
        return ['error' => true, 'message' => 'Email changes are currently unavailable. Please contact support.'];
    }

    $attempts = [
        [$currentEmail, $new_email, $current_password],
        [$new_email, $current_password],
        [$currentEmail, $new_email, $current_password, $current_password],
    ];

    $lastError = null;
    foreach ($attempts as $args) {
        try {
            $result = call_user_func_array([$auth, 'changeEmail'], $args);

            if (is_array($result)) {
                $hasError = isset($result['error']) && (bool) $result['error'] === true;
                if (!$hasError) {
                    return ['error' => false, 'message' => 'Email updated successfully.'];
                }

                $lastError = (string) ($result['message'] ?? 'Email update failed.');
                continue;
            }

            if ($result === true || $result === null) {
                return ['error' => false, 'message' => 'Email updated successfully.'];
            }

            $lastError = 'Email update failed.';
        } catch (Throwable $e) {
            $lastError = $e->getMessage();
        }
    }

    ah_log_error('profile_email_change_failed', null, [
        'user_id' => $user_id,
        'new_email' => $new_email,
        'reason' => $lastError,
    ]);

    return ['error' => true, 'message' => 'Unable to update email. Please verify your password and try again.'];
}

function change_profile_password($auth, $current_email, $current, $new, $confirm) {
    if ($new !== $confirm) {
        return ['error' => true, 'message' => 'New passwords do not match.'];
    }
    $result = $auth->changePassword($current_email, $current, $new, $new);
    return $result;
}
