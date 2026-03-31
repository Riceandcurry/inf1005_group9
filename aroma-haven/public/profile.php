<?php
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
        $postedCsrf = (string) ($_POST['csrf_token'] ?? '');
        if (empty($_SESSION['csrf_token']) || !hash_equals((string) $_SESSION['csrf_token'], $postedCsrf)) {
                $err = 'Invalid request token. Please refresh and try again.';
        } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'update_details') {
                $fname = sanitize_input($_POST['fname'] ?? '');
                $lname = sanitize_input($_POST['lname'] ?? '');
                $email = sanitize_input($_POST['email'] ?? '');
                $emailPassword = (string) ($_POST['email_current_password'] ?? '');

                $currentUser = $auth->getCurrentUser();
                $currentEmail = trim((string) ($currentUser['email'] ?? ''));
                $emailChangeRequested = $email !== '' && strcasecmp($email, $currentEmail) !== 0;

                if ($emailChangeRequested && trim($emailPassword) === '') {
                        $err = 'Current password is required to change email.';
                } else {
                        update_profile_details($user_id, $fname, $lname);

                        if ($emailChangeRequested) {
                                $emailResult = change_profile_email($auth, (int) $user_id, $email, $emailPassword);
                                if (!empty($emailResult['error'])) {
                                        $err = (string) ($emailResult['message'] ?? 'Unable to update email.');
                                } else {
                                        $msg = 'Profile and email updated successfully!';
                                }
                        } else {
                                $msg = 'Profile updated successfully!';
                        }
                }
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

$pageTitle = 'My Account | Aroma Haven';
$bodyClass = 'ah-profile-main';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-profile-main">
        <!-- Hero/banner section -->
        <section class="ah-hero" style="background: var(--ah-steamed); min-height: 220px;">
            <div class="container h-100 d-flex flex-column justify-content-center align-items-center" style="min-height:220px;">
                <h1 class="ah-hero-title mb-1 mx-auto" style="font-family: var(--ah-font-serif);font-size:2.2rem;">My Account</h1>
                <p class="ah-hero-lead mb-0 text-cortado mx-auto">Manage your Aroma Haven account settings</p>
            </div>
        </section>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="card shadow ah-profile-card mx-auto w-100" style="border-radius:1.3rem; background: #fff; max-width: 980px;">
                    <div class="d-flex flex-column flex-md-row w-100">
                        <div class="d-flex flex-column align-items-center justify-content-center py-5 px-4 w-100 w-md-33 border-end" style="background:var(--ah-steamed);border-radius:1.3rem 1.3rem 0 0; min-width:220px; min-height: 320px; box-shadow:0 2px 16px 0 rgba(0,0,0,0.04); border:1.5px solid #ede9e2;">
                            <div class="d-flex flex-column align-items-center w-100 mb-3">
                                <div style="width:64px;height:64px;border-radius:50%;background:#e8e5dd;display:flex;align-items:center;justify-content:center;margin-bottom:1.1rem;">
                                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8.5" r="4.5" fill="#b6a98c"/><rect x="4" y="15" width="16" height="5" rx="2.5" fill="#b6a98c"/></svg>
                                </div>
                                <h5 class="mb-1" style="font-family:var(--ah-font-serif);font-size:1.35rem;font-weight:600;letter-spacing:-0.5px;line-height:1.2;">
                                    <?php echo htmlspecialchars($fname . ' ' . $lname); ?>
                                </h5>
                                <div class="text-muted mb-0" style="font-size:1.01em;word-break:break-all;letter-spacing:0.01em;">
                                    <?php echo htmlspecialchars($email); ?>
                                </div>
                            </div>
                            <div class="mt-4 w-100 text-center">
                                <div class="text-overline mb-1" style="letter-spacing:0.08em;color:#a08c6b;">ACCOUNT STATS</div>
                                <div style="font-size:1.05em;">
                                    <span class="text-cortado">Member</span> <span class="text-espresso">Since</span> <span class="fw-semibold">2025</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 p-4 p-md-5" style="min-width:260px;">
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
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
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
                                        <div class="col-12">
                                            <label for="email_current_password" class="form-label">Current Password (required only if changing email)</label>
                                            <input type="password" id="email_current_password" name="email_current_password" class="form-control" autocomplete="current-password">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                    <form action="profile.php" method="POST">
                                        <input type="hidden" name="action" value="change_password">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                        <div class="row align-items-end g-3">
                                            <div class="col-12 col-md-4 mb-3 mb-md-0">
                                                <label for="current_password" class="form-label">Current Password</label>
                                                <input type="password" id="current_password" name="current_password" class="form-control" required style="background:#f5f3ee;">
                                            </div>
                                            <div class="col-12 col-md-4 mb-3 mb-md-0">
                                                <label for="new_password" class="form-label" style="margin-top:6px;">New Password</label>
                                                <input type="password" id="new_password" name="new_password" class="form-control" required style="background:#f5f3ee;">
                                            </div>
                                            <div class="col-12 col-md-4 mb-3 mb-md-0">
                                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required style="background:#f5f3ee;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 mt-3">
                                                <button type="submit" class="btn btn-outline-primary px-4">Change Password</button>
                                            </div>
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
<?php include __DIR__ . '/../includes/footer.php'; ?>
