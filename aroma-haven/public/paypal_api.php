<?php
require_once __DIR__ . '/../backend/auth_guard.php';
require_once __DIR__ . '/../backend/env.php';
require_once __DIR__ . '/../backend/order_service.php';
require_once __DIR__ . '/../backend/order_mailer.php';
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

$clientId = ah_env('PAYPAL_CLIENT_ID', '');
$clientSecret = ah_env('PAYPAL_CLIENT_SECRET', '');
$baseUrl = ah_env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com');
$defaultCurrency = ah_env('PAYPAL_CURRENCY', 'USD');

if ($clientId === '' || $clientSecret === '') {
    http_response_code(500);
    echo json_encode(['error' => 'PayPal credentials are not configured']);
    exit;
}

function paypal_request(string $url, array $headers, string $method = 'GET', ?string $body = null): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $responseBody = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($responseBody === false || $curlError) {
        return ['ok' => false, 'status' => 500, 'data' => ['error' => 'PayPal connection failed']];
    }

    $decoded = json_decode($responseBody, true);
    if (!is_array($decoded)) {
        $decoded = ['raw' => $responseBody];
    }

    return ['ok' => $statusCode >= 200 && $statusCode < 300, 'status' => $statusCode, 'data' => $decoded];
}

function paypal_access_token(string $baseUrl, string $clientId, string $clientSecret): ?string
{
    $auth = base64_encode($clientId . ':' . $clientSecret);
    $result = paypal_request(
        rtrim($baseUrl, '/') . '/v1/oauth2/token',
        [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/x-www-form-urlencoded',
        ],
        'POST',
        'grant_type=client_credentials'
    );

    if (!$result['ok']) {
        return null;
    }

    return $result['data']['access_token'] ?? null;
}

$action = $payload['action'] ?? '';
$accessToken = paypal_access_token($baseUrl, $clientId, $clientSecret);

if ($accessToken === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to authenticate with PayPal']);
    exit;
}

if ($action === 'create') {
    $checkoutOrderId = (int) ($payload['checkout_order_id'] ?? 0);
    if ($checkoutOrderId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing checkout_order_id']);
        exit;
    }

    $currentUserId = (int) $auth->getCurrentUID();
    $order = ah_checkout_get_order_for_user($checkoutOrderId, $currentUserId);
    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    if ((string) ($order['status'] ?? '') !== 'pending') {
        http_response_code(409);
        echo json_encode(['error' => 'Order is not in pending state']);
        exit;
    }

    $existingProviderOrderId = trim((string) ($order['payment_provider_order_id'] ?? ''));
    if ($existingProviderOrderId !== '') {
        echo json_encode(['id' => $existingProviderOrderId]);
        exit;
    }

    $total = (float) ($order['total'] ?? 0);
    if ($total <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Order total is invalid']);
        exit;
    }

    $currency = strtoupper((string) ($order['currency'] ?? $defaultCurrency));

    $orderBody = json_encode([
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'amount' => [
                'currency_code' => $currency,
                'value' => number_format($total, 2, '.', ''),
            ],
        ]],
    ]);

    $result = paypal_request(
        rtrim($baseUrl, '/') . '/v2/checkout/orders',
        [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'PayPal-Request-Id: checkout-create-' . $checkoutOrderId,
        ],
        'POST',
        $orderBody
    );

    if (!$result['ok']) {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Unable to create PayPal order', 'details' => $result['data']]);
        exit;
    }

    $paypalOrderId = (string) ($result['data']['id'] ?? '');
    if ($paypalOrderId === '') {
        http_response_code(500);
        echo json_encode(['error' => 'PayPal order id missing in response']);
        exit;
    }

    if (!ah_checkout_attach_provider_order_id($checkoutOrderId, $currentUserId, $paypalOrderId)) {
        $freshOrder = ah_checkout_get_order_for_user($checkoutOrderId, $currentUserId);
        $resolvedProviderOrderId = trim((string) (($freshOrder['payment_provider_order_id'] ?? '')));
        if ($resolvedProviderOrderId !== '') {
            echo json_encode(['id' => $resolvedProviderOrderId]);
            exit;
        }

        http_response_code(500);
        echo json_encode(['error' => 'Unable to attach provider order id']);
        exit;
    }

    echo json_encode(['id' => $paypalOrderId]);
    exit;
}

if ($action === 'capture') {
    $orderId = (string)($payload['orderID'] ?? '');
    if ($orderId === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Missing orderID']);
        exit;
    }

    $currentUserId = (int) $auth->getCurrentUID();
    $localOrder = ah_checkout_get_order_by_provider_id($orderId, $currentUserId);
    if (!$localOrder) {
        http_response_code(404);
        echo json_encode(['error' => 'Local checkout order not found for this PayPal order']);
        exit;
    }

    $localOrderId = (int) ($localOrder['id'] ?? 0);
    $localTotal = number_format((float) ($localOrder['total'] ?? 0), 2, '.', '');
    $localCurrency = strtoupper((string) ($localOrder['currency'] ?? $defaultCurrency));
    $localStatus = (string) ($localOrder['status'] ?? '');

    if ($localStatus === 'paid') {
        $_SESSION['ah_checkout_confirmation'] = [
            'source' => 'paypal',
            'order_id' => $localOrderId,
            'paypal_order_id' => $orderId,
            'paypal_capture_id' => '',
            'amount' => $localTotal,
            'currency' => $localCurrency,
            'created_at' => time(),
            'user_id' => $currentUserId,
        ];

        echo json_encode([
            'status' => 'COMPLETED',
            'confirmation_url' => 'confirmation.php',
        ]);
        exit;
    }

    if ($localStatus !== 'pending') {
        http_response_code(409);
        echo json_encode(['error' => 'Order is not pending and cannot be captured']);
        exit;
    }

    $result = paypal_request(
        rtrim($baseUrl, '/') . '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture',
        [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'PayPal-Request-Id: checkout-capture-' . $orderId,
        ],
        'POST',
        '{}'
    );

    if (!$result['ok']) {
        if ((int) $result['status'] === 422) {
            $lookup = paypal_request(
                rtrim($baseUrl, '/') . '/v2/checkout/orders/' . rawurlencode($orderId),
                [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                ],
                'GET',
                null
            );

            if ($lookup['ok']) {
                $result = $lookup;
            }
        }
    }

    if (!$result['ok']) {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Unable to capture PayPal order', 'details' => $result['data']]);
        exit;
    }

    $status = (string) ($result['data']['status'] ?? 'UNKNOWN');
    if ($status !== 'COMPLETED') {
        ah_checkout_mark_order_failed($localOrderId, $currentUserId);
        ah_checkout_add_payment_log(
            $localOrderId,
            'paypal',
            $orderId,
            null,
            $status,
            $localCurrency,
            (float) ($localOrder['total'] ?? 0),
            $result['data']
        );
        echo json_encode(['status' => $status, 'data' => $result['data']]);
        exit;
    }

    $purchaseUnit = $result['data']['purchase_units'][0] ?? [];
    $captures = $purchaseUnit['payments']['captures'] ?? [];
    $captureData = (is_array($captures) && isset($captures[0]) && is_array($captures[0])) ? $captures[0] : [];

    $amountData = [];
    if (isset($captureData['amount']) && is_array($captureData['amount'])) {
        $amountData = $captureData['amount'];
    } elseif (isset($purchaseUnit['amount']) && is_array($purchaseUnit['amount'])) {
        $amountData = $purchaseUnit['amount'];
    }

    $amountValue = (string) ($amountData['value'] ?? '');
    $currencyCode = strtoupper((string) ($amountData['currency_code'] ?? $defaultCurrency));

    if ($amountValue === '' || number_format((float) $amountValue, 2, '.', '') !== $localTotal || $currencyCode !== $localCurrency) {
        ah_checkout_mark_order_failed($localOrderId, $currentUserId);
        ah_checkout_add_payment_log(
            $localOrderId,
            'paypal',
            $orderId,
            (string) ($captureData['id'] ?? ''),
            'amount_mismatch',
            $currencyCode !== '' ? $currencyCode : $localCurrency,
            (float) ($amountValue !== '' ? $amountValue : 0),
            $result['data']
        );

        http_response_code(409);
        echo json_encode(['error' => 'Captured amount does not match local order total']);
        exit;
    }

    ah_checkout_mark_order_paid($localOrderId, $currentUserId);
    ah_send_order_confirmation($localOrderId, $currentUserId);
    ah_checkout_add_payment_log(
        $localOrderId,
        'paypal',
        $orderId,
        (string) ($captureData['id'] ?? ''),
        $status,
        $currencyCode,
        (float) $amountValue,
        $result['data']
    );

    $_SESSION['ah_checkout_confirmation'] = [
        'source' => 'paypal',
        'order_id' => $localOrderId,
        'paypal_order_id' => $orderId,
        'paypal_capture_id' => (string) ($captureData['id'] ?? ''),
        'amount' => $amountValue,
        'currency' => $currencyCode,
        'created_at' => time(),
        'user_id' => (int) $auth->getCurrentUID(),
    ];

    echo json_encode([
        'status' => $status,
        'data' => $result['data'],
        'confirmation_url' => 'confirmation.php',
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unsupported action']);
