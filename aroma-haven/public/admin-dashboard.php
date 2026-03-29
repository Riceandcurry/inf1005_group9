<?php
$pageTitle = 'Aroma Haven | Admin Dashboard';
$bodyClass = 'ah-admin-page';

require_once __DIR__ . '/../backend/admin_guard.php';
require_once __DIR__ . '/../backend/admin_helpers.php';

require_admin();

$conn = connect_db();
$flash = ah_admin_pull_flash();

$totalProducts = (int) $conn->query('SELECT COUNT(*) FROM products')->fetchColumn();
$activeProducts = (int) $conn->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn();
$totalContacts = (int) $conn->query('SELECT COUNT(*) FROM contact_submissions')->fetchColumn();
$openContacts = (int) $conn->query("SELECT COUNT(*) FROM contact_submissions WHERE status IN ('new', 'in_progress')")->fetchColumn();
$totalUsers = (int) $conn->query('SELECT COUNT(*) FROM phpauth_users')->fetchColumn();
$suspendedUsers = (int) $conn->query('SELECT COUNT(*) FROM user_status WHERE is_suspended = 1')->fetchColumn();
$admins = (int) $conn->query(
    "SELECT COUNT(*)
     FROM user_roles ur
     INNER JOIN roles r ON r.id = ur.role_id
     WHERE r.role_key = 'admin'"
)->fetchColumn();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-4 py-lg-5">
  <section class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
    <div>
      <p class="text-overline mb-2">Admin Console</p>
      <h1 class="h3 mb-0">Management Dashboard</h1>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-outline-primary" href="admin-products.php">Manage Products</a>
      <a class="btn btn-outline-primary" href="admin-contacts.php">Manage Contacts</a>
      <a class="btn btn-outline-primary" href="admin-users.php">Manage Users</a>
    </div>
  </section>

  <?php if ($flash !== null): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?>" role="alert">
      <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
  <?php endif; ?>

  <section class="row g-3 g-lg-4 mb-4">
    <article class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <p class="text-muted mb-2">Products</p>
          <p class="h4 mb-1"><?php echo $totalProducts; ?></p>
          <p class="mb-0 text-success">Active: <?php echo $activeProducts; ?></p>
        </div>
      </div>
    </article>
    <article class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <p class="text-muted mb-2">Contact Submissions</p>
          <p class="h4 mb-1"><?php echo $totalContacts; ?></p>
          <p class="mb-0 text-warning">Open: <?php echo $openContacts; ?></p>
        </div>
      </div>
    </article>
    <article class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <p class="text-muted mb-2">Users</p>
          <p class="h4 mb-1"><?php echo $totalUsers; ?></p>
          <p class="mb-0 text-danger">Suspended: <?php echo $suspendedUsers; ?></p>
        </div>
      </div>
    </article>
    <article class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <p class="text-muted mb-2">Admins</p>
          <p class="h4 mb-0"><?php echo $admins; ?></p>
        </div>
      </div>
    </article>
  </section>

  <section class="card border-0 shadow-sm">
    <div class="card-body">
      <h2 class="h5 mb-3">Quick Actions</h2>
      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-primary" href="admin-products.php">Create or Edit Products</a>
        <a class="btn btn-primary" href="admin-contacts.php">Reply to Contacts</a>
        <a class="btn btn-primary" href="admin-users.php">Manage Admins & Suspension</a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
