<?php
$pageTitle = 'Aroma Haven | Profile';
$bodyClass = 'bg-oat';

require_once __DIR__ . '/../backend/init.php';
require_once __DIR__ . '/../backend/user_profile.php';

// Simple session check — no PHPAuth dependency
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$msg = '';
$err = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_profile'])) {
        $fname = trim($_POST['fname'] ?? '');
        $lname = trim($_POST['lname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($fname && $lname && $email) {
            try {
                update_user_profile($user_id, $fname, $lname, $email);
                $msg = 'Profile updated successfully!';
            } catch (Exception $e) {
                $err = 'Error updating profile: ' . $e->getMessage();
            }
        } else {
            $err = 'All fields are required.';
        }
    }

    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!$current || !$new || !$confirm) {
            $err = 'All password fields are required.';
        } elseif ($new !== $confirm) {
            $err = 'New passwords do not match.';
        } else {
            $result = change_user_password($user_id, $current, $new);
            if (!empty($result['error'])) {
                $err = $result['message'];
            } else {
                $msg = 'Password changed successfully!';
            }
        }
    }
}

$profile = get_user_profile($user_id);
$fname   = $profile['fname'] ?? '';
$lname   = $profile['lname'] ?? '';
$email   = $profile['email'] ?? '';

include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<main class="container py-5" style="max-width: 640px;">

  <h1 class="mb-1" style="font-family: var(--ah-font-serif); color: var(--ah-espresso);">Your Profile</h1>
  <p class="mb-5" style="color: var(--ah-cortado);">Manage your account details and password.</p>

  <?php if ($msg): ?>
    <div class="alert mb-4" role="alert"
         style="background: #edf5ec; border: 1px solid #b6d9b3; color: #2d6a2d; border-radius: var(--ah-radius-md); padding: 1rem 1.25rem;">
      <?php echo htmlspecialchars($msg); ?>
    </div>
  <?php endif; ?>

  <?php if ($err): ?>
    <div class="alert mb-4" role="alert"
         style="background: #fdf0ed; border: 1px solid #f0b8aa; color: #a63a1e; border-radius: var(--ah-radius-md); padding: 1rem 1.25rem;">
      <?php echo htmlspecialchars($err); ?>
    </div>
  <?php endif; ?>

  <!-- Profile Details -->
  <section class="mb-5 p-4"
           style="background: #fff; border: 1px solid var(--ah-border-soft); border-radius: var(--ah-radius-lg); box-shadow: var(--ah-shadow-sm);">
    <h2 class="mb-4" style="font-size: 1.1rem; color: var(--ah-espresso);">Account Details</h2>
    <form method="POST" action="profile.php">
      <div class="row g-3 mb-3">
        <div class="col-6">
          <label for="fname" class="form-label" style="font-size: var(--ah-text-sm); color: var(--ah-cortado); font-weight: 500;">First Name</label>
          <input type="text" id="fname" name="fname" class="form-control"
                 value="<?php echo htmlspecialchars($fname); ?>" required
                 style="border-color: var(--ah-border-mid); border-radius: var(--ah-radius-sm); background: var(--ah-oat);">
        </div>
        <div class="col-6">
          <label for="lname" class="form-label" style="font-size: var(--ah-text-sm); color: var(--ah-cortado); font-weight: 500;">Last Name</label>
          <input type="text" id="lname" name="lname" class="form-control"
                 value="<?php echo htmlspecialchars($lname); ?>" required
                 style="border-color: var(--ah-border-mid); border-radius: var(--ah-radius-sm); background: var(--ah-oat);">
        </div>
      </div>
      <div class="mb-4">
        <label for="email" class="form-label" style="font-size: var(--ah-text-sm); color: var(--ah-cortado); font-weight: 500;">Email</label>
        <input type="email" id="email" name="email" class="form-control"
               value="<?php echo htmlspecialchars($email); ?>" required
               style="border-color: var(--ah-border-mid); border-radius: var(--ah-radius-sm); background: var(--ah-oat);">
      </div>
      <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
    </form>
  </section>

  <!-- Change Password -->
  <section class="p-4"
           style="background: #fff; border: 1px solid var(--ah-border-soft); border-radius: var(--ah-radius-lg); box-shadow: var(--ah-shadow-sm);">
    <h2 class="mb-4" style="font-size: 1.1rem; color: var(--ah-espresso);">Change Password</h2>
    <form method="POST" action="profile.php">
      <div class="mb-3">
        <label for="current_password" class="form-label" style="font-size: var(--ah-text-sm); color: var(--ah-cortado); font-weight: 500;">Current Password</label>
        <input type="password" id="current_password" name="current_password" class="form-control" required
               style="border-color: var(--ah-border-mid); border-radius: var(--ah-radius-sm); background: var(--ah-oat);">
      </div>
      <div class="mb-3">
        <label for="new_password" class="form-label" style="font-size: var(--ah-text-sm); color: var(--ah-cortado); font-weight: 500;">New Password</label>
        <input type="password" id="new_password" name="new_password" class="form-control" required
               style="border-color: var(--ah-border-mid); border-radius: var(--ah-radius-sm); background: var(--ah-oat);">
      </div>
      <div class="mb-4">
        <label for="confirm_password" class="form-label" style="font-size: var(--ah-text-sm); color: var(--ah-cortado); font-weight: 500;">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required
               style="border-color: var(--ah-border-mid); border-radius: var(--ah-radius-sm); background: var(--ah-oat);">
      </div>
      <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
    </form>
  </section>

  <!-- Logout -->
  <div class="mt-4 text-center">
    <form method="POST" action="route.php">
      <input type="hidden" name="action" value="logout">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <button type="submit" class="btn btn-outline-primary" style="color: var(--ah-cortado); border-color: var(--ah-border-mid);">
        Log out
      </button>
    </form>
  </div>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>