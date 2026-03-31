<?php
require_once __DIR__ . '/../backend/auth_guard.php';
require_once __DIR__ . '/../backend/order_service.php';

require_login();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$csrfHeader = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if (empty($_SESSION['csrf_token']) || !hash_equals((string) $_SESSION['csrf_token'], $csrfHeader)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload']);
    exit;
}

$action = (string) ($payload['action'] ?? '');
$currentUserId = (int) $auth->getCurrentUID();

if ($action === 'create_pending_order') {
    $lines = $payload['lines'] ?? null;
    if (!is_array($lines)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid cart lines']);
        exit;
    }

    $idempotencyKey = trim((string) ($payload['idempotency_key'] ?? ''));
    if ($idempotencyKey !== '' && !ah_checkout_validate_idempotency_key($idempotencyKey)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid idempotency key']);
        exit;
    }

    $shipping = is_array($payload['shipping'] ?? null) ? $payload['shipping'] : [];
    $fullName = trim((string) ($shipping['full_name'] ?? ''));
    $email = trim((string) ($shipping['email'] ?? ''));
    $street = trim((string) ($shipping['street'] ?? ''));
    $city = trim((string) ($shipping['city'] ?? ''));
    $state = trim((string) ($shipping['state'] ?? ''));
    $zip = trim((string) ($shipping['zip'] ?? ''));

    if ($fullName === '' || $email === '' || $street === '' || $city === '' || $state === '' || $zip === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required shipping fields']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid shipping email']);
        exit;
    }

    try {
        $order = ah_checkout_create_pending_order($currentUserId, $lines, 'paypal', $idempotencyKey, $shipping);
        echo json_encode([
            'order_id' => (int) $order['order_id'],
            'currency' => (string) $order['currency'],
            'subtotal' => number_format((float) $order['subtotal'], 2, '.', ''),
            'shipping' => number_format((float) $order['shipping'], 2, '.', ''),
            'tax' => number_format((float) $order['tax'], 2, '.', ''),
            'total' => number_format((float) $order['total'], 2, '.', ''),
        ]);
        exit;
    } catch (RuntimeException $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    } catch (Throwable $e) {
        error_log('[checkout_api] create_pending_order failed: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Unable to create order']);
        exit;
    }
}

http_response_code(400);
echo json_encode(['error' => 'Unsupported action']);
?>
