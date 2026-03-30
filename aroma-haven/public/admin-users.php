<?php
$pageTitle = 'Aroma Haven | Admin Users';
$bodyClass = 'ah-admin-page';

require_once __DIR__ . '/../backend/admin_guard.php';
require_once __DIR__ . '/../backend/admin_helpers.php';

require_admin();

$currentUserId = ah_current_user_id();
$conn = connect_db();
$flash = ah_admin_pull_flash();
$query = trim((string) ($_GET['q'] ?? ''));

$sql = "
SELECT
  u.id,
  u.email,
  COALESCE(p.fname, '') AS fname,
  COALESCE(p.lname, '') AS lname,
  CASE WHEN admin_role.user_id IS NULL THEN 0 ELSE 1 END AS is_admin,
  COALESCE(us.is_suspended, 0) AS is_suspended,
  COALESCE(us.suspension_reason, '') AS suspension_reason
FROM phpauth_users u
LEFT JOIN user_profiles p ON p.user_id = u.id
LEFT JOIN (
  SELECT ur.user_id
  FROM user_roles ur
  INNER JOIN roles r ON r.id = ur.role_id
  WHERE r.role_key = 'admin'
) AS admin_role ON admin_role.user_id = u.id
LEFT JOIN user_status us ON us.user_id = u.id
";

$params = [];
if ($query !== '') {
    $sql .= ' WHERE u.email LIKE ? OR p.fname LIKE ? OR p.lname LIKE ? ';
    $term = '%' . $query . '%';
    $params = [$term, $term, $term];
}

$sql .= ' ORDER BY u.id DESC';
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-4 py-lg-5">
  <section class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
    <div>
      <p class="text-overline mb-2">Admin Console</p>
      <h1 class="h3 mb-0">Manage Users</h1>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-outline-primary" href="admin-dashboard.php">Dashboard</a>
      <a class="btn btn-outline-primary" href="admin-products.php">Products</a>
      <a class="btn btn-outline-primary" href="admin-contacts.php">Contacts</a>
    </div>
  </section>

  <?php if ($flash !== null): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?>" role="alert">
      <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
  <?php endif; ?>

  <section class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="get" action="admin-users.php" class="d-flex gap-2">
        <input type="search" class="form-control" name="q" placeholder="Search by email or name" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </form>
    </div>
  </section>

  <section class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>User</th>
              <th>Admin Role</th>
              <th>Suspension</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <?php
              $userId = (int) ($user['id'] ?? 0);
              $isAdmin = (int) ($user['is_admin'] ?? 0) === 1;
              $isSuspended = (int) ($user['is_suspended'] ?? 0) === 1;
              $displayName = trim((string) ($user['fname'] . ' ' . $user['lname']));
              ?>
              <tr id="user-<?php echo $userId; ?>">
                <td><?php echo $userId; ?></td>
                <td>
                  <p class="mb-1 fw-semibold"><?php echo htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                  <p class="mb-0 text-muted"><?php echo htmlspecialchars($displayName !== '' ? $displayName : 'No profile name', ENT_QUOTES, 'UTF-8'); ?></p>
                </td>
                <td>
                  <?php if ($isAdmin): ?>
                    <span class="badge text-bg-success">Admin</span>
                  <?php else: ?>
                    <span class="badge text-bg-secondary">Customer</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($isSuspended): ?>
                    <span class="badge text-bg-danger">Suspended</span>
                    <?php if (!empty($user['suspension_reason'])): ?>
                      <p class="small text-muted mb-0 mt-1"><?php echo htmlspecialchars((string) $user['suspension_reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="badge text-bg-success">Active</span>
                  <?php endif; ?>
                </td>
                <td style="min-width: 340px;">
                  <div class="d-flex flex-wrap gap-2 mb-2">
                    <form action="admin-route.php" method="post">
                      <input type="hidden" name="action" value="user_toggle_admin">
                      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="hidden" name="target_user_id" value="<?php echo $userId; ?>">
                      <button class="btn btn-sm <?php echo $isAdmin ? 'btn-outline-danger' : 'btn-outline-primary'; ?>" type="submit" <?php echo ($userId === $currentUserId && $isAdmin) ? 'title="You cannot remove your own admin role"' : ''; ?>>
                        <?php echo $isAdmin ? 'Revoke Admin' : 'Make Admin'; ?>
                      </button>
                    </form>
                  </div>

                  <form action="admin-route.php" method="post" class="d-flex flex-wrap gap-2 align-items-center">
                    <input type="hidden" name="action" value="user_toggle_suspend">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="target_user_id" value="<?php echo $userId; ?>">
                    <input class="form-control form-control-sm" style="max-width: 180px;" type="text" name="suspension_reason" placeholder="Suspension reason">
                    <button class="btn btn-sm <?php echo $isSuspended ? 'btn-outline-success' : 'btn-outline-warning'; ?>" type="submit" <?php echo ($userId === $currentUserId) ? 'title="You cannot suspend your own account"' : ''; ?>>
                      <?php echo $isSuspended ? 'Reactivate' : 'Suspend'; ?>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
