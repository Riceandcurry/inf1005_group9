<?php
$pageTitle = 'Aroma Haven | Admin Contacts';
$bodyClass = 'ah-admin-page';

require_once __DIR__ . '/../backend/admin_guard.php';
require_once __DIR__ . '/../backend/admin_helpers.php';

require_admin();

$conn = connect_db();
$flash = ah_admin_pull_flash();
$statusFilter = trim((string) ($_GET['status'] ?? 'all'));
$contactColumns = ah_table_columns('contact_submissions');
$hasStatusColumn = isset($contactColumns['status']);
$hasInternalNoteColumn = isset($contactColumns['internal_note']);
$hasRepliedAtColumn = isset($contactColumns['replied_at']);

if ($hasStatusColumn && $statusFilter !== 'all' && in_array($statusFilter, ['new', 'in_progress', 'resolved'], true)) {
    $stmt = $conn->prepare('SELECT * FROM contact_submissions WHERE status = ? ORDER BY submitted_at DESC, id DESC');
    $stmt->execute([$statusFilter]);
} else {
    $stmt = $conn->query('SELECT * FROM contact_submissions ORDER BY submitted_at DESC, id DESC');
}

$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-4 py-lg-5">
  <section class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
    <div>
      <p class="text-overline mb-2">Admin Console</p>
      <h1 class="h3 mb-0">Manage Contact Submissions</h1>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-outline-primary" href="admin-dashboard.php">Dashboard</a>
      <a class="btn btn-outline-primary" href="admin-products.php">Products</a>
      <a class="btn btn-outline-primary" href="admin-users.php">Users</a>
    </div>
  </section>

  <?php if ($flash !== null): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?>" role="alert">
      <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
  <?php endif; ?>

  <section class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
      <h2 class="h5 mb-0">Filter</h2>
      <form method="get" action="admin-contacts.php" class="d-flex gap-2">
        <select class="form-select" name="status">
          <?php foreach (['all' => 'All', 'new' => 'New', 'in_progress' => 'In Progress', 'resolved' => 'Resolved'] as $value => $label): ?>
            <option value="<?php echo $value; ?>" <?php echo ($statusFilter === $value) ? 'selected' : ''; ?>><?php echo $label; ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-outline-secondary" type="submit" <?php echo $hasStatusColumn ? '' : 'disabled'; ?>>Apply</button>
      </form>
      <?php if (!$hasStatusColumn): ?>
        <p class="small text-muted mb-0">Status filtering is unavailable because `contact_submissions.status` is not present in this database.</p>
      <?php endif; ?>
    </div>
  </section>

  <?php if (empty($contacts)): ?>
    <section class="card border-0 shadow-sm">
      <div class="card-body text-center text-muted py-4">
        No contact submissions found.
      </div>
    </section>
  <?php endif; ?>

  <section class="d-grid gap-4">
    <?php foreach ($contacts as $contact): ?>
      <?php
      $contactId = (int) ($contact['id'] ?? 0);
      $status = (string) ($contact['status'] ?? 'new');
      ?>
      <article id="contact-<?php echo $contactId; ?>" class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3 mb-3">
            <div>
              <h2 class="h5 mb-1"><?php echo htmlspecialchars((string) ($contact['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h2>
              <p class="mb-1"><a href="mailto:<?php echo htmlspecialchars((string) ($contact['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($contact['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></a></p>
              <p class="text-muted mb-0">Topic: <?php echo htmlspecialchars((string) ($contact['topic'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div class="text-lg-end">
              <span class="badge text-bg-secondary"><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
              <p class="text-muted small mb-0 mt-1">Submitted: <?php echo htmlspecialchars((string) ($contact['submitted_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
              <?php if ($hasRepliedAtColumn && !empty($contact['replied_at'])): ?>
                <p class="text-success small mb-0">Replied: <?php echo htmlspecialchars((string) $contact['replied_at'], ENT_QUOTES, 'UTF-8'); ?></p>
              <?php endif; ?>
            </div>
          </div>

          <p class="mb-3" style="white-space: pre-wrap;"><?php echo htmlspecialchars((string) ($contact['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>

          <form action="admin-route.php" method="post" class="row g-3 mb-3">
            <input type="hidden" name="action" value="contact_update">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="submission_id" value="<?php echo $contactId; ?>">

            <?php if ($hasStatusColumn): ?>
              <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                  <?php foreach (['new' => 'New', 'in_progress' => 'In Progress', 'resolved' => 'Resolved'] as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo ($status === $value) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endif; ?>
            <?php if ($hasInternalNoteColumn): ?>
              <div class="<?php echo $hasStatusColumn ? 'col-md-8' : 'col-12'; ?>">
                <label class="form-label">Internal Note</label>
                <input class="form-control" type="text" name="internal_note" value="<?php echo htmlspecialchars((string) ($contact['internal_note'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Internal handling note">
              </div>
            <?php endif; ?>
            <?php if ($hasStatusColumn || $hasInternalNoteColumn): ?>
              <div class="col-12">
                <button class="btn btn-outline-primary" type="submit">Update Contact Record</button>
              </div>
            <?php else: ?>
              <div class="col-12">
                <p class="small text-muted mb-0">No editable contact metadata columns are available in this database.</p>
              </div>
            <?php endif; ?>
          </form>

          <form action="admin-route.php" method="post" class="row g-3">
            <input type="hidden" name="action" value="contact_reply">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="submission_id" value="<?php echo $contactId; ?>">

            <div class="col-md-6">
              <label class="form-label">Reply Subject</label>
              <input class="form-control" type="text" name="reply_subject" required placeholder="Re: Your Aroma Haven enquiry">
            </div>
            <div class="col-12">
              <label class="form-label">Reply Body</label>
              <textarea class="form-control" name="reply_body" rows="4" required placeholder="Write your response..."></textarea>
            </div>
            <div class="col-12">
              <button class="btn btn-primary" type="submit">Send Email Reply</button>
            </div>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
