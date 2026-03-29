<?php
require_once __DIR__ . '/util.php';

function ah_current_user_id(): int
{
    global $auth;

    if (!isset($auth) || !is_object($auth)) {
        return 0;
    }

    if (!$auth->isLogged()) {
        return 0;
    }

    return (int) $auth->getCurrentUID();
}

function ah_get_role_id(string $roleKey): int
{
    static $cache = [];
    if (isset($cache[$roleKey])) {
        return $cache[$roleKey];
    }

    $conn = connect_db();
    $stmt = $conn->prepare('SELECT id FROM roles WHERE role_key = ? LIMIT 1');
    $stmt->execute([$roleKey]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $cache[$roleKey] = (int) ($row['id'] ?? 0);
    return $cache[$roleKey];
}

function ah_user_is_admin(int $userId): bool
{
    if ($userId <= 0) {
        return false;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'SELECT 1
         FROM user_roles ur
         INNER JOIN roles r ON r.id = ur.role_id
         WHERE ur.user_id = ? AND r.role_key = ?
         LIMIT 1'
    );
    $stmt->execute([$userId, 'admin']);

    return (bool) $stmt->fetchColumn();
}

function ah_current_user_is_admin(): bool
{
    $userId = ah_current_user_id();
    return ah_user_is_admin($userId);
}

function ah_is_user_suspended(int $userId): bool
{
    if ($userId <= 0) {
        return false;
    }

    $conn = connect_db();
    $stmt = $conn->prepare('SELECT is_suspended FROM user_status WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $value = $stmt->fetchColumn();

    return (int) $value === 1;
}

function ah_find_user_id_by_email(string $email): int
{
    $email = trim($email);
    if ($email === '') {
        return 0;
    }

    $conn = connect_db();
    $stmt = $conn->prepare('SELECT id FROM phpauth_users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int) ($row['id'] ?? 0);
}

function ah_set_user_admin_role(int $targetUserId, bool $isAdmin, int $actorUserId = 0): bool
{
    if ($targetUserId <= 0) {
        return false;
    }

    $roleId = ah_get_role_id('admin');
    if ($roleId <= 0) {
        return false;
    }

    $conn = connect_db();

    if ($isAdmin) {
        $stmt = $conn->prepare('INSERT INTO user_roles (user_id, role_id, assigned_by) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE assigned_by = VALUES(assigned_by)');
        return $stmt->execute([$targetUserId, $roleId, $actorUserId > 0 ? $actorUserId : null]);
    }

    $stmt = $conn->prepare('DELETE FROM user_roles WHERE user_id = ? AND role_id = ?');
    return $stmt->execute([$targetUserId, $roleId]);
}

function ah_set_user_suspension(int $targetUserId, bool $suspend, string $reason = '', int $actorUserId = 0): bool
{
    if ($targetUserId <= 0) {
        return false;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'INSERT INTO user_status (user_id, is_suspended, suspension_reason, updated_by)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            is_suspended = VALUES(is_suspended),
            suspension_reason = VALUES(suspension_reason),
            updated_by = VALUES(updated_by)'
    );

    $note = $suspend ? trim($reason) : null;
    return $stmt->execute([$targetUserId, $suspend ? 1 : 0, $note, $actorUserId > 0 ? $actorUserId : null]);
}

function ah_table_exists(string $tableName): bool
{
    if ($tableName === '') {
        return false;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'SELECT 1
         FROM INFORMATION_SCHEMA.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
         LIMIT 1'
    );
    $stmt->execute([$tableName]);

    return (bool) $stmt->fetchColumn();
}

function ah_admin_audit(int $adminUserId, string $actionType, string $targetType, string $targetId, array $details = []): void
{
    if (!ah_table_exists('admin_audit_logs') || $adminUserId <= 0) {
        return;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'INSERT INTO admin_audit_logs (admin_user_id, action_type, target_type, target_id, details_json, ip_address, user_agent)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $detailsJson = !empty($details) ? json_encode($details, JSON_UNESCAPED_UNICODE) : null;
    $stmt->execute([$adminUserId, $actionType, $targetType, $targetId, $detailsJson, $ip, $agent]);
}

function ah_slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim((string) $text, '-');
    return $text !== '' ? $text : 'product';
}

function ah_unique_product_slug(string $name, int $excludeId = 0): string
{
    $base = ah_slugify($name);
    $slug = $base;
    $counter = 2;

    $conn = connect_db();

    while (true) {
        if ($excludeId > 0) {
            $stmt = $conn->prepare('SELECT id FROM products WHERE slug = ? AND id != ? LIMIT 1');
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $conn->prepare('SELECT id FROM products WHERE slug = ? LIMIT 1');
            $stmt->execute([$slug]);
        }

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return $slug;
        }

        $slug = $base . '-' . $counter;
        $counter++;
    }
}

function ah_admin_set_flash(string $type, string $message): void
{
    $_SESSION['admin_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function ah_admin_pull_flash(): ?array
{
    if (!isset($_SESSION['admin_flash']) || !is_array($_SESSION['admin_flash'])) {
        return null;
    }

    $flash = $_SESSION['admin_flash'];
    unset($_SESSION['admin_flash']);

    return $flash;
}
?>
