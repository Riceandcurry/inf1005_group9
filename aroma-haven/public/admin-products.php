<?php
$pageTitle = 'Aroma Haven | Admin Products';
$bodyClass = 'ah-admin-page';

require_once __DIR__ . '/../backend/admin_guard.php';
require_once __DIR__ . '/../backend/admin_helpers.php';

require_admin();

$conn = connect_db();
$flash = ah_admin_pull_flash();
$query = trim((string) ($_GET['q'] ?? ''));

if ($query !== '') {
    $stmt = $conn->prepare('SELECT * FROM products WHERE name LIKE ? OR origin LIKE ? ORDER BY id DESC');
    $term = '%' . $query . '%';
    $stmt->execute([$term, $term]);
} else {
    $stmt = $conn->query('SELECT * FROM products ORDER BY id DESC');
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-4 py-lg-5">
  <section class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
    <div>
      <p class="text-overline mb-2">Admin Console</p>
      <h1 class="h3 mb-0">Manage Products</h1>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-outline-primary" href="admin-dashboard.php">Dashboard</a>
      <a class="btn btn-outline-primary" href="admin-contacts.php">Contacts</a>
      <a class="btn btn-outline-primary" href="admin-users.php">Users</a>
    </div>
  </section>

  <?php if ($flash !== null): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?>" role="alert">
      <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
  <?php endif; ?>

  <section class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h2 class="h5 mb-3">Create Product</h2>
      <form action="admin-route.php" method="post" class="row g-3">
        <input type="hidden" name="action" value="product_create">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <div class="col-md-6">
          <label class="form-label" for="create-name">Name</label>
          <input class="form-control" id="create-name" type="text" name="name" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="create-origin">Origin</label>
          <input class="form-control" id="create-origin" type="text" name="origin" required>
        </div>
        <div class="col-md-3">
          <label class="form-label" for="create-price">Price</label>
          <input class="form-control" id="create-price" type="number" name="price" min="0.01" step="0.01" required>
        </div>
        <div class="col-md-3">
          <label class="form-label" for="create-roast">Roast</label>
          <select class="form-select" id="create-roast" name="roast_level" required>
            <option value="Light">Light</option>
            <option value="Medium" selected>Medium</option>
            <option value="Dark">Dark</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="create-image">Image URL</label>
          <input class="form-control" id="create-image" type="text" name="image" placeholder="./images/products/product_1.jpg">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="create-tags">Tasting Notes (comma-separated)</label>
          <input class="form-control" id="create-tags" type="text" name="tasting_notes_csv" placeholder="Chocolate, Citrus, Floral">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="create-process">Process</label>
          <input class="form-control" id="create-process" type="text" name="process" placeholder="Washed">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="create-altitude">Altitude</label>
          <input class="form-control" id="create-altitude" type="text" name="altitude" placeholder="1200-1500 masl">
        </div>
        <div class="col-12">
          <label class="form-label" for="create-description">Description</label>
          <textarea class="form-control" id="create-description" name="description" rows="3"></textarea>
        </div>
        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" id="create-active" type="checkbox" name="is_active" value="1" checked>
            <label class="form-check-label" for="create-active">Active product</label>
          </div>
        </div>
        <div class="col-12">
          <button class="btn btn-primary" type="submit">Create Product</button>
        </div>
      </form>
    </div>
  </section>

  <section class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
        <h2 class="h5 mb-0">Existing Products</h2>
        <form method="get" action="admin-products.php" class="d-flex gap-2">
          <input type="search" class="form-control" name="q" placeholder="Search name or origin" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>">
          <button class="btn btn-outline-secondary" type="submit">Search</button>
        </form>
      </div>

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Product</th>
              <th>Pricing</th>
              <th>Meta</th>
              <th>Status</th>
              <th>Save</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <?php
              $notes = [];
              if (!empty($product['tasting_notes'])) {
                $decoded = json_decode((string) $product['tasting_notes'], true);
                if (is_array($decoded)) {
                  $notes = $decoded;
                }
              }
              ?>
              <tr id="product-<?php echo (int) $product['id']; ?>">
                <form action="admin-route.php" method="post">
                  <input type="hidden" name="action" value="product_update">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">

                  <td><?php echo (int) $product['id']; ?></td>
                  <td style="min-width: 280px;">
                    <input class="form-control form-control-sm mb-2" type="text" name="name" value="<?php echo htmlspecialchars((string) ($product['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                    <input class="form-control form-control-sm mb-2" type="text" name="origin" value="<?php echo htmlspecialchars((string) ($product['origin'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                    <input class="form-control form-control-sm" type="text" name="image" value="<?php echo htmlspecialchars((string) ($product['image'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Image URL">
                  </td>
                  <td style="min-width: 140px;">
                    <input class="form-control form-control-sm mb-2" type="number" min="0.01" step="0.01" name="price" value="<?php echo htmlspecialchars((string) ($product['price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" required>
                    <select class="form-select form-select-sm" name="roast_level" required>
                      <?php foreach (['Light', 'Medium', 'Dark'] as $level): ?>
                        <option value="<?php echo $level; ?>" <?php echo (($product['roast_level'] ?? '') === $level) ? 'selected' : ''; ?>><?php echo $level; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td style="min-width: 280px;">
                    <input class="form-control form-control-sm mb-2" type="text" name="tasting_notes_csv" value="<?php echo htmlspecialchars(implode(', ', $notes), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Tasting notes CSV">
                    <input class="form-control form-control-sm mb-2" type="text" name="process" value="<?php echo htmlspecialchars((string) ($product['process'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Process">
                    <input class="form-control form-control-sm mb-2" type="text" name="altitude" value="<?php echo htmlspecialchars((string) ($product['altitude'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Altitude">
                    <textarea class="form-control form-control-sm" name="description" rows="2" placeholder="Description"><?php echo htmlspecialchars((string) ($product['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                  </td>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_active" value="1" <?php echo ((int) ($product['is_active'] ?? 0) === 1) ? 'checked' : ''; ?>>
                      <label class="form-check-label">Active</label>
                    </div>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-primary" type="submit">Save</button>
                  </td>
                </form>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
