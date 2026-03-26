<?php
require_once __DIR__ . '/../backend/env.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
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
    $total = isset($payload['total']) ? (float)$payload['total'] : 0;
    if ($total <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid order total']);
        exit;
    }

    $currency = strtoupper((string)($payload['currency'] ?? $defaultCurrency));

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
        ],
        'POST',
        $orderBody
    );

    if (!$result['ok']) {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Unable to create PayPal order', 'details' => $result['data']]);
        exit;
    }

    echo json_encode(['id' => $result['data']['id'] ?? null]);
    exit;
}

if ($action === 'capture') {
    $orderId = (string)($payload['orderID'] ?? '');
    if ($orderId === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Missing orderID']);
        exit;
    }

    $result = paypal_request(
        rtrim($baseUrl, '/') . '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture',
        [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ],
        'POST',
        '{}'
    );

    if (!$result['ok']) {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Unable to capture PayPal order', 'details' => $result['data']]);
        exit;
    }

    echo json_encode(['status' => $result['data']['status'] ?? 'UNKNOWN', 'data' => $result['data']]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unsupported action']);
