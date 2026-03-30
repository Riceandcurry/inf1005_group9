<?php
// profile.php

session_start();
require_once '../backend/auth_guard.php'; // Ensure user is logged in

require_once '../backend/init.php'; // DB connection
require_once '../backend/profile_process.php';
require_login();

$user_id = $auth->getCurrentUID();
$msg = '';
$err = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_details') {
        $fname = sanitize_input($_POST['fname'] ?? '');
        $lname = sanitize_input($_POST['lname'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        update_profile_details($user_id, $fname, $lname, $email);
        $msg = 'Profile updated successfully!';
    } elseif ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $result = change_profile_password($auth, $auth->getCurrentUser()['email'], $current, $new, $confirm);
        if ($result['error']) {
            $err = $result['message'];
        } else {
            $msg = 'Password changed successfully!';
        }
    }
}

// Fetch user details for display
$stmt = $conn->prepare('SELECT email FROM phpauth_users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $user['email'] ?? '';

$stmt2 = $conn->prepare('SELECT fname, lname FROM user_profiles WHERE user_id = ?');
$stmt2->execute([$user_id]);
$profile = $stmt2->fetch(PDO::FETCH_ASSOC);
$fname = $profile['fname'] ?? '';
$lname = $profile['lname'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<main class="ah-profile-main py-5" style="background: var(--ah-steamed); min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="card shadow ah-profile-card mx-auto w-100" style="border-radius:1.3rem; background: #fff; max-width: 980px;">
                    <div class="d-flex flex-column flex-md-row w-100">
                        <div class="d-flex flex-column align-items-center justify-content-center py-5 px-4 w-100 w-md-33 border-end" style="background:var(--ah-steamed);border-radius:1.3rem 1.3rem 0 0; min-width:220px; min-height: 320px;">
                            <img src="images/assets/user.png" alt="Profile" style="width:72px;height:72px;border-radius:50%;background:#f6f4ed;object-fit:cover;">
                            <h5 class="mb-0 mt-3"><?php echo htmlspecialchars($fname . ' ' . $lname); ?></h5>
                            <div class="text-muted mb-2" style="font-size:0.95em;word-break:break-all;">
                                <?php echo htmlspecialchars($email); ?>
                            </div>
                            <div class="mt-4 w-100">
                                <div class="text-overline mb-1">Account Stats</div>
                                <div style="font-size:0.98em;">
                                    <div><span class="text-cortado">Member</span> <span class="text-espresso">Since</span> <span class="fw-semibold">2025</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 p-4 p-md-5" style="min-width:260px;">
                            <h1 class="display-6 mb-2" style="font-family: var(--ah-font-serif);">My Account</h1>
                            <p class="mb-4 text-cortado">Manage your Aroma Haven account settings</p>
                            <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="true">Profile Settings</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Change Password</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="profileTabContent">
                                <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                                    <?php if ($msg): ?>
                                        <div class="alert alert-success py-2 px-3"><?php echo htmlspecialchars($msg); ?></div>
                                    <?php endif; ?>
                                    <?php if ($err && !$msg): ?>
                                        <div class="alert alert-danger py-2 px-3"><?php echo htmlspecialchars($err); ?></div>
                                    <?php endif; ?>
                                    <form action="profile.php" method="POST" class="row g-3">
                                        <input type="hidden" name="action" value="update_details">
                                        <div class="col-md-6">
                                            <label for="fname" class="form-label">First Name</label>
                                            <input type="text" id="fname" name="fname" class="form-control" value="<?php echo htmlspecialchars($fname); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lname" class="form-label">Last Name</label>
                                            <input type="text" id="lname" name="lname" class="form-control" value="<?php echo htmlspecialchars($lname); ?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                    <form action="profile.php" method="POST" class="row g-3">
                                        <input type="hidden" name="action" value="change_password">
                                        <div class="col-12 col-md-4">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label for="new_password" class="form-label">New Password</label>
                                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-outline-primary px-4">Change Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
</body>
</html>
