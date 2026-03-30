<?php
require_once __DIR__ . '/../backend/init.php';
require_once __DIR__ . '/../backend/admin_guard.php';
require_once __DIR__ . '/../backend/admin_helpers.php';
require_once __DIR__ . '/../backend/env.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function admin_redirect_with_flash(string $location, string $type, string $message): void
{
    ah_admin_set_flash($type, $message);
    header('Location: ' . $location);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

require_admin();

if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string) ($_POST['csrf_token'] ?? ''))) {
    admin_redirect_with_flash('admin-dashboard.php', 'danger', 'Invalid CSRF token. Please refresh and try again.');
}

$action = (string) ($_POST['action'] ?? '');
$currentUserId = ah_current_user_id();
$conn = connect_db();

try {
    switch ($action) {
    case 'product_create':
    case 'product_update':
        $productId = (int) ($_POST['product_id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $origin = trim((string) ($_POST['origin'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);
        $roastLevel = trim((string) ($_POST['roast_level'] ?? ''));
        $image = trim((string) ($_POST['image'] ?? ''));
        $tagsCsv = (string) ($_POST['tasting_notes_csv'] ?? '');
        $description = trim((string) ($_POST['description'] ?? ''));
        $process = trim((string) ($_POST['process'] ?? ''));
        $altitude = trim((string) ($_POST['altitude'] ?? ''));
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '' || $origin === '' || $price <= 0) {
            admin_redirect_with_flash('admin-products.php', 'danger', 'Name, origin, and a valid price are required.');
            exit;
        }

        if (!in_array($roastLevel, ['Light', 'Medium', 'Dark'], true)) {
            admin_redirect_with_flash('admin-products.php', 'danger', 'Roast level must be Light, Medium, or Dark.');
            exit;
        }

        $productColumns = ah_table_columns('products');
        foreach (['name', 'origin', 'price'] as $requiredColumn) {
            if (!isset($productColumns[$requiredColumn])) {
                admin_redirect_with_flash('admin-products.php', 'danger', 'Products table is missing required column: ' . $requiredColumn);
            }
        }

        $tags = array_values(array_filter(array_map('trim', explode(',', $tagsCsv)), function ($value) {
            return $value !== '';
        }));
        $tagsJson = !empty($tags) ? json_encode($tags, JSON_UNESCAPED_UNICODE) : null;

        $productData = [
            'name' => $name,
            'origin' => $origin,
            'price' => $price,
            'roast_level' => $roastLevel,
            'image' => $image,
            'tasting_notes' => $tagsJson,
            'description' => $description,
            'process' => $process,
            'altitude' => $altitude,
            'is_active' => $isActive,
        ];

        if ($action === 'product_create') {
            $insertColumns = [];
            $insertValues = [];

            if (isset($productColumns['slug'])) {
                $insertColumns[] = 'slug';
                $insertValues[] = ah_unique_product_slug($name);
            }

            foreach ($productData as $column => $value) {
                if (isset($productColumns[$column])) {
                    $insertColumns[] = $column;
                    $insertValues[] = $value;
                }
            }

            if (empty($insertColumns)) {
                admin_redirect_with_flash('admin-products.php', 'danger', 'No compatible product columns found for insert.');
            }

            $columnSql = implode(', ', array_map(function ($column) {
                return '`' . $column . '`';
            }, $insertColumns));
            $placeholderSql = implode(', ', array_fill(0, count($insertColumns), '?'));
            $stmt = $conn->prepare('INSERT INTO products (' . $columnSql . ') VALUES (' . $placeholderSql . ')');
            $stmt->execute($insertValues);

            $newId = (int) $conn->lastInsertId();
            ah_admin_audit($currentUserId, 'product_create', 'product', (string) $newId, [
                'name' => $name,
                'is_active' => $isActive,
            ]);

            admin_redirect_with_flash('admin-products.php', 'success', 'Product created successfully.');
            exit;
        }

        if ($productId <= 0) {
            admin_redirect_with_flash('admin-products.php', 'danger', 'Invalid product selected.');
            exit;
        }

        $updateData = $productData;
        if (isset($productColumns['slug'])) {
            $updateData['slug'] = ah_unique_product_slug($name, $productId);
        }

        $setClauses = [];
        $setValues = [];
        foreach ($updateData as $column => $value) {
            if (isset($productColumns[$column])) {
                $setClauses[] = '`' . $column . '` = ?';
                $setValues[] = $value;
            }
        }

        if (empty($setClauses)) {
            admin_redirect_with_flash('admin-products.php', 'danger', 'No compatible product columns found for update.');
        }

        $setValues[] = $productId;
        $stmt = $conn->prepare('UPDATE products SET ' . implode(', ', $setClauses) . ' WHERE id = ?');
        $stmt->execute($setValues);

        ah_admin_audit($currentUserId, 'product_update', 'product', (string) $productId, [
            'name' => $name,
            'is_active' => $isActive,
        ]);

        admin_redirect_with_flash('admin-products.php', 'success', 'Product updated successfully.');
        exit;
        
    case 'contact_update':
        $submissionId = (int) ($_POST['submission_id'] ?? 0);
        $status = trim((string) ($_POST['status'] ?? 'new'));
        $internalNote = trim((string) ($_POST['internal_note'] ?? ''));

        if ($submissionId <= 0 || !in_array($status, ['new', 'in_progress', 'resolved'], true)) {
            admin_redirect_with_flash('admin-contacts.php', 'danger', 'Invalid contact update request.');
            exit;
        }

        $contactColumns = ah_table_columns('contact_submissions');
        $setClauses = [];
        $setValues = [];

        if (isset($contactColumns['status'])) {
            $setClauses[] = '`status` = ?';
            $setValues[] = $status;
        }
        if (isset($contactColumns['internal_note'])) {
            $setClauses[] = '`internal_note` = ?';
            $setValues[] = $internalNote;
        }

        if (empty($setClauses)) {
            admin_redirect_with_flash('admin-contacts.php', 'danger', 'contact_submissions table is missing editable admin columns (status/internal_note).');
            exit;
        }

        $setValues[] = $submissionId;
        $stmt = $conn->prepare('UPDATE contact_submissions SET ' . implode(', ', $setClauses) . ' WHERE id = ?');
        $stmt->execute($setValues);

        ah_admin_audit($currentUserId, 'contact_update', 'contact_submission', (string) $submissionId, [
            'status' => $status,
        ]);

        admin_redirect_with_flash('admin-contacts.php#contact-' . $submissionId, 'success', 'Contact record updated.');
        exit;

    case 'contact_reply':
        $submissionId = (int) ($_POST['submission_id'] ?? 0);
        $subject = trim((string) ($_POST['reply_subject'] ?? ''));
        $body = trim((string) ($_POST['reply_body'] ?? ''));

        if ($submissionId <= 0 || $subject === '' || $body === '') {
            admin_redirect_with_flash('admin-contacts.php', 'danger', 'Reply subject and body are required.');
            exit;
        }

        $stmt = $conn->prepare('SELECT id, name, email FROM contact_submissions WHERE id = ? LIMIT 1');
        $stmt->execute([$submissionId]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$submission) {
            admin_redirect_with_flash('admin-contacts.php', 'danger', 'Contact submission not found.');
            exit;
        }

        $mail = new PHPMailer(true);
        $sent = false;
        $error = null;

        try {
            $mailHost = (string) ah_env('MAIL_HOST', '');
            $mailUser = (string) ah_env('MAIL_USER', '');
            $mailPass = (string) ah_env('MAIL_PASS', '');
            $mailPort = (int) ah_env('MAIL_PORT', '587');
            $mailEncryption = strtolower((string) ah_env('MAIL_ENCRYPTION', 'tls'));
            $mailAuthRaw = strtolower((string) ah_env('MAIL_SMTP_AUTH', 'true'));
            $mailAuth = !in_array($mailAuthRaw, ['0', 'false', 'no', 'off'], true);

            if ($mailHost === '') {
                throw new Exception('MAIL_HOST is empty.');
            }
            if ($mailAuth && $mailUser === '') {
                throw new Exception('MAIL_USER is empty while SMTP auth is enabled.');
            }

            $mail->isSMTP();
            $mail->Host = $mailHost;
            $mail->SMTPAuth = $mailAuth;
            $mail->Username = $mailUser;
            $mail->Password = $mailPass;
            $mail->Port = $mailPort > 0 ? $mailPort : 587;
            $mail->CharSet = 'UTF-8';

            if (in_array($mailEncryption, ['ssl', 'smtps'], true)) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif (in_array($mailEncryption, ['tls', 'starttls'], true)) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            }

            $fromAddress = (string) ah_env('MAIL_FROM_ADDRESS', $mailUser !== '' ? $mailUser : 'no-reply@localhost');
            $fromName = (string) ah_env('MAIL_FROM_NAME', 'Aroma Haven');
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress((string) $submission['email'], (string) ($submission['name'] ?? ''));
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
            $mail->AltBody = $body;
            $mail->send();
            $sent = true;
        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }

        if ($sent) {
            $contactColumns = ah_table_columns('contact_submissions');
            $setClauses = [];
            $setValues = [];

            if (isset($contactColumns['status'])) {
                $setClauses[] = '`status` = ?';
                $setValues[] = 'resolved';
            }
            if (isset($contactColumns['replied_at'])) {
                $setClauses[] = '`replied_at` = NOW()';
            }
            if (isset($contactColumns['replied_by'])) {
                $setClauses[] = '`replied_by` = ?';
                $setValues[] = $currentUserId;
            }

            if (!empty($setClauses)) {
                $setValues[] = $submissionId;
                $stmt = $conn->prepare('UPDATE contact_submissions SET ' . implode(', ', $setClauses) . ' WHERE id = ?');
                $stmt->execute($setValues);
            }

            if (ah_table_exists('contact_replies')) {
                $replyStmt = $conn->prepare(
                    'INSERT INTO contact_replies (submission_id, replied_by, reply_subject, reply_body, sent_success, error_message)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $replyStmt->execute([$submissionId, $currentUserId, $subject, $body, 1, null]);                
            }

            ah_admin_audit($currentUserId, 'contact_reply_sent', 'contact_submission', (string) $submissionId, [
                'subject' => $subject,
            ]);

            admin_redirect_with_flash('admin-contacts.php#contact-' . $submissionId, 'success', 'Reply email sent successfully.');
            exit;
        }

        if (ah_table_exists('contact_replies')) {
            $replyStmt = $conn->prepare(
                'INSERT INTO contact_replies (submission_id, replied_by, reply_subject, reply_body, sent_success, error_message)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $replyStmt->execute([$submissionId, $currentUserId, $subject, $body, 0, $error]);            
        }

        ah_admin_audit($currentUserId, 'contact_reply_failed', 'contact_submission', (string) $submissionId, [
            'subject' => $subject,
            'error' => $error,
        ]);

        $safeError = trim((string) $error);
        if ($safeError !== '') {
            $safeError = preg_replace('/\s+/', ' ', $safeError);
            $safeError = substr($safeError, 0, 220);
        }
        $message = $safeError !== ''
            ? 'Reply could not be sent. Mail error: ' . $safeError
            : 'Reply could not be sent. Please check mail settings.';

        admin_redirect_with_flash('admin-contacts.php#contact-' . $submissionId, 'danger', $message);
        exit;

    case 'user_toggle_admin':
        $targetUserId = (int) ($_POST['target_user_id'] ?? 0);
        if ($targetUserId <= 0) {
            admin_redirect_with_flash('admin-users.php', 'danger', 'Invalid user selected.');
            exit;
        }

        $currentlyAdmin = ah_user_is_admin($targetUserId);
        $makeAdmin = !$currentlyAdmin;

        if ($targetUserId === $currentUserId && !$makeAdmin) {
            admin_redirect_with_flash('admin-users.php#user-' . $targetUserId, 'danger', 'You cannot remove your own admin access.');
            exit;
        }

        if (!ah_set_user_admin_role($targetUserId, $makeAdmin, $currentUserId)) {
            admin_redirect_with_flash('admin-users.php#user-' . $targetUserId, 'danger', 'Unable to update admin role.');
            exit;
        }

        ah_admin_audit($currentUserId, $makeAdmin ? 'user_promote_admin' : 'user_demote_admin', 'user', (string) $targetUserId);

        admin_redirect_with_flash(
            'admin-users.php#user-' . $targetUserId,
            'success',
            $makeAdmin ? 'User promoted to admin.' : 'Admin access revoked.'
        );
        exit;

    case 'user_toggle_suspend':
        $targetUserId = (int) ($_POST['target_user_id'] ?? 0);
        $reason = trim((string) ($_POST['suspension_reason'] ?? ''));

        if ($targetUserId <= 0) {
            admin_redirect_with_flash('admin-users.php', 'danger', 'Invalid user selected.');
            exit;
        }

        if ($targetUserId === $currentUserId) {
            admin_redirect_with_flash('admin-users.php#user-' . $targetUserId, 'danger', 'You cannot suspend your own account.');
            exit;
        }

        $currentlySuspended = ah_is_user_suspended($targetUserId);
        $suspend = !$currentlySuspended;

        if (!ah_set_user_suspension($targetUserId, $suspend, $reason, $currentUserId)) {
            admin_redirect_with_flash('admin-users.php#user-' . $targetUserId, 'danger', 'Unable to update suspension state.');
            exit;
        }

        ah_admin_audit($currentUserId, $suspend ? 'user_suspend' : 'user_unsuspend', 'user', (string) $targetUserId, [
            'reason' => $reason,
        ]);

        admin_redirect_with_flash(
            'admin-users.php#user-' . $targetUserId,
            'success',
            $suspend ? 'User suspended.' : 'User reactivated.'
        );
        exit;

    default:
        admin_redirect_with_flash('admin-dashboard.php', 'danger', 'Unknown admin action.');
    }
} catch (Throwable $e) {
    error_log('[admin-route] action=' . $action . ' failed: ' . $e->getMessage());

    $fallback = 'admin-dashboard.php';
    if (in_array($action, ['product_create', 'product_update'], true)) {
        $fallback = 'admin-products.php';
    } elseif (in_array($action, ['contact_update', 'contact_reply'], true)) {
        $fallback = 'admin-contacts.php';
    } elseif (in_array($action, ['user_toggle_admin', 'user_toggle_suspend'], true)) {
        $fallback = 'admin-users.php';
    }

    admin_redirect_with_flash($fallback, 'danger', 'Admin action failed. Please verify database columns and try again.');
    exit;
}
?>
