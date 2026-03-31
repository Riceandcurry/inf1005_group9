<?php
require_once __DIR__ . '/util.php';
require_once __DIR__ . '/env.php';

function ah_checkout_shipping_amount(): float
{
    $raw = ah_env('CHECKOUT_SHIPPING', '5.99');
    $value = is_numeric($raw) ? (float) $raw : 5.99;
    return max(0, round($value, 2));
}

function ah_checkout_tax_rate(): float
{
    $raw = ah_env('CHECKOUT_TAX_RATE', '0.08');
    $value = is_numeric($raw) ? (float) $raw : 0.08;
    if ($value < 0) {
        return 0.0;
    }
    if ($value > 1) {
        return 1.0;
    }
    return $value;
}

function ah_checkout_currency(): string
{
    $currency = strtoupper(trim((string) ah_env('PAYPAL_CURRENCY', 'USD')));
    if (!preg_match('/^[A-Z]{3}$/', $currency)) {
        return 'USD';
    }
    return $currency;
}

function ah_money(float $value): float
{
    return round($value + 1e-9, 2);
}

function ah_checkout_validate_idempotency_key(string $key): bool
{
    return (bool) preg_match('/^[A-Za-z0-9_-]{8,80}$/', $key);
}

function ah_checkout_normalize_shipping_snapshot(array $shipping): array
{
    $map = [
        'full_name' => 120,
        'email' => 160,
        'phone' => 40,
        'street' => 160,
        'city' => 120,
        'state' => 120,
        'zip' => 32,
    ];

    $normalized = [];
    foreach ($map as $field => $maxLen) {
        $value = trim((string) ($shipping[$field] ?? ''));
        if ($value === '') {
            continue;
        }
        if (function_exists('mb_substr')) {
            $value = mb_substr($value, 0, $maxLen);
        } else {
            $value = substr($value, 0, $maxLen);
        }
        $normalized[$field] = $value;
    }

    return $normalized;
}

function ah_checkout_order_summary_from_row(array $order): array
{
    return [
        'order_id' => (int) ($order['id'] ?? 0),
        'currency' => strtoupper((string) ($order['currency'] ?? 'USD')),
        'subtotal' => ah_money((float) ($order['subtotal'] ?? 0)),
        'shipping' => ah_money((float) ($order['shipping'] ?? 0)),
        'tax' => ah_money((float) ($order['tax'] ?? 0)),
        'total' => ah_money((float) ($order['total'] ?? 0)),
    ];
}

function ah_checkout_normalize_lines(array $lines): array
{
    $normalized = [];
    foreach ($lines as $line) {
        if (!is_array($line)) {
            continue;
        }

        $productId = (int) ($line['product_id'] ?? 0);
        $quantity = (int) ($line['quantity'] ?? 0);
        if ($productId <= 0 || $quantity <= 0) {
            continue;
        }

        $quantity = min(99, $quantity);
        if (!isset($normalized[$productId])) {
            $normalized[$productId] = 0;
        }
        $normalized[$productId] = min(99, $normalized[$productId] + $quantity);
    }
    return $normalized;
}

function ah_checkout_fetch_products(array $productIds): array
{
    if (empty($productIds)) {
        return [];
    }

    $conn = connect_db();
    $placeholders = implode(', ', array_fill(0, count($productIds), '?'));
    $stmt = $conn->prepare('SELECT id, name, price, is_active FROM products WHERE id IN (' . $placeholders . ')');
    $stmt->execute($productIds);

    $products = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = (int) ($row['id'] ?? 0);
        if ($id <= 0) {
            continue;
        }
        $isActive = isset($row['is_active']) ? ((int) $row['is_active'] === 1) : true;
        if (!$isActive) {
            continue;
        }

        $products[$id] = [
            'id' => $id,
            'name' => (string) ($row['name'] ?? ''),
            'price' => ah_money((float) ($row['price'] ?? 0)),
        ];
    }

    return $products;
}

function ah_checkout_get_pending_order_by_idempotency(int $userId, string $idempotencyKey): ?array
{
    if ($userId <= 0 || $idempotencyKey === '' || !ah_checkout_validate_idempotency_key($idempotencyKey)) {
        return null;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'SELECT *
         FROM orders
         WHERE user_id = ? AND idempotency_key = ? AND status = ?
         ORDER BY id DESC
         LIMIT 1'
    );
    $stmt->execute([$userId, $idempotencyKey, 'pending']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? $row : null;
}

function ah_checkout_get_order_by_idempotency(int $userId, string $idempotencyKey): ?array
{
    if ($userId <= 0 || $idempotencyKey === '' || !ah_checkout_validate_idempotency_key($idempotencyKey)) {
        return null;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'SELECT *
         FROM orders
         WHERE user_id = ? AND idempotency_key = ?
         ORDER BY id DESC
         LIMIT 1'
    );
    $stmt->execute([$userId, $idempotencyKey]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? $row : null;
}

function ah_checkout_create_pending_order(
    int $userId,
    array $lines,
    string $paymentMethod = 'paypal',
    string $idempotencyKey = '',
    array $shippingSnapshot = []
): array
{
    if ($userId <= 0) {
        throw new RuntimeException('Invalid user.');
    }

    if ($idempotencyKey !== '' && !ah_checkout_validate_idempotency_key($idempotencyKey)) {
        throw new RuntimeException('Invalid idempotency key.');
    }

    if ($idempotencyKey !== '') {
        $existingOrder = ah_checkout_get_order_by_idempotency($userId, $idempotencyKey);
        if (is_array($existingOrder)) {
            $existingStatus = (string) ($existingOrder['status'] ?? '');
            if ($existingStatus === 'pending') {
                return ah_checkout_order_summary_from_row($existingOrder);
            }
            throw new RuntimeException('Checkout session already finalized. Please refresh and try again.');
        }
    }

    $normalizedLines = ah_checkout_normalize_lines($lines);
    if (empty($normalizedLines)) {
        throw new RuntimeException('Cart is empty or invalid.');
    }

    $productIds = array_keys($normalizedLines);
    $products = ah_checkout_fetch_products($productIds);
    if (count($products) !== count($normalizedLines)) {
        throw new RuntimeException('One or more cart items are unavailable.');
    }

    $subtotal = 0.0;
    $itemRows = [];
    foreach ($normalizedLines as $productId => $qty) {
        $product = $products[$productId];
        $lineTotal = ah_money($product['price'] * $qty);
        $subtotal = ah_money($subtotal + $lineTotal);
        $itemRows[] = [
            'product_id' => $productId,
            'product_name' => $product['name'],
            'unit_price' => $product['price'],
            'quantity' => $qty,
            'line_total' => $lineTotal,
        ];
    }

    $shipping = ah_checkout_shipping_amount();
    $tax = ah_money($subtotal * ah_checkout_tax_rate());
    $total = ah_money($subtotal + $shipping + $tax);
    $currency = ah_checkout_currency();
    $shippingJson = json_encode(ah_checkout_normalize_shipping_snapshot($shippingSnapshot), JSON_UNESCAPED_UNICODE);

    $conn = connect_db();
    $conn->beginTransaction();
    try {
        $orderStmt = $conn->prepare(
            'INSERT INTO orders (user_id, idempotency_key, status, payment_method, currency, subtotal, shipping, tax, total, shipping_snapshot_json)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $orderStmt->execute([
            $userId,
            $idempotencyKey !== '' ? $idempotencyKey : null,
            'pending',
            $paymentMethod,
            $currency,
            $subtotal,
            $shipping,
            $tax,
            $total,
            $shippingJson !== false ? $shippingJson : null,
        ]);

        $orderId = (int) $conn->lastInsertId();
        $itemStmt = $conn->prepare(
            'INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total)
             VALUES (?, ?, ?, ?, ?, ?)'
        );

        foreach ($itemRows as $item) {
            $itemStmt->execute([
                $orderId,
                $item['product_id'],
                $item['product_name'],
                $item['unit_price'],
                $item['quantity'],
                $item['line_total'],
            ]);
        }

        $conn->commit();
    } catch (Throwable $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        if ($idempotencyKey !== '' && $e instanceof PDOException && (string) $e->getCode() === '23000') {
            $existingOrder = ah_checkout_get_order_by_idempotency($userId, $idempotencyKey);
            if (is_array($existingOrder)) {
                $existingStatus = (string) ($existingOrder['status'] ?? '');
                if ($existingStatus === 'pending') {
                    return ah_checkout_order_summary_from_row($existingOrder);
                }
                throw new RuntimeException('Checkout session already finalized. Please refresh and try again.');
            }
        }
        throw new RuntimeException('Unable to create pending order.');
    }

    return ah_checkout_order_summary_from_row([
        'id' => $orderId,
        'currency' => $currency,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'tax' => $tax,
        'total' => $total,
    ]);
}

function ah_checkout_get_order_for_user(int $orderId, int $userId): ?array
{
    if ($orderId <= 0 || $userId <= 0) {
        return null;
    }

    $conn = connect_db();
    $stmt = $conn->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$orderId, $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? $row : null;
}

function ah_checkout_get_order_by_provider_id(string $providerOrderId, int $userId): ?array
{
    $providerOrderId = trim($providerOrderId);
    if ($providerOrderId === '' || $userId <= 0) {
        return null;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'SELECT * FROM orders WHERE payment_provider_order_id = ? AND user_id = ? LIMIT 1'
    );
    $stmt->execute([$providerOrderId, $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? $row : null;
}

function ah_checkout_attach_provider_order_id(int $orderId, int $userId, string $providerOrderId): bool
{
    if ($orderId <= 0 || $userId <= 0 || trim($providerOrderId) === '') {
        return false;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'UPDATE orders
         SET payment_provider_order_id = ?, updated_at = NOW()
         WHERE id = ? AND user_id = ? AND status = ? AND (payment_provider_order_id IS NULL OR payment_provider_order_id = \'\')'
    );
    $stmt->execute([$providerOrderId, $orderId, $userId, 'pending']);
    if ($stmt->rowCount() > 0) {
        return true;
    }

    $current = ah_checkout_get_order_for_user($orderId, $userId);
    if (!is_array($current)) {
        return false;
    }

    return trim((string) ($current['payment_provider_order_id'] ?? '')) === trim($providerOrderId);
}

function ah_checkout_add_payment_log(
    int $orderId,
    string $provider,
    ?string $providerOrderId,
    ?string $providerCaptureId,
    string $status,
    string $currency,
    float $amount,
    array $rawResponse
): void {
    if ($orderId <= 0) {
        return;
    }

    $rawJson = json_encode($rawResponse, JSON_UNESCAPED_UNICODE);
    $conn = connect_db();
    $stmt = $conn->prepare(
        'INSERT INTO order_payments (order_id, provider, provider_order_id, provider_capture_id, status, currency, amount, raw_response_json)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $orderId,
        $provider,
        $providerOrderId,
        $providerCaptureId,
        $status,
        $currency,
        ah_money($amount),
        $rawJson !== false ? $rawJson : null,
    ]);
}

function ah_checkout_mark_order_paid(int $orderId, int $userId): bool
{
    if ($orderId <= 0 || $userId <= 0) {
        return false;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'UPDATE orders
         SET status = ?, placed_at = NOW(), updated_at = NOW()
         WHERE id = ? AND user_id = ? AND status = ?'
    );
    return $stmt->execute(['paid', $orderId, $userId, 'pending']);
}

function ah_checkout_mark_order_failed(int $orderId, int $userId): bool
{
    if ($orderId <= 0 || $userId <= 0) {
        return false;
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'UPDATE orders
         SET status = ?, updated_at = NOW()
         WHERE id = ? AND user_id = ? AND status = ?'
    );
    return $stmt->execute(['failed', $orderId, $userId, 'pending']);
}

function ah_checkout_get_order_items(int $orderId): array
{
    if ($orderId <= 0) {
        return [];
    }

    $conn = connect_db();
    $stmt = $conn->prepare(
        'SELECT product_id, product_name, unit_price, quantity, line_total
         FROM order_items
         WHERE order_id = ?
         ORDER BY id ASC'
    );
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function ah_checkout_decode_shipping_snapshot(?string $json): array
{
    if (!is_string($json) || trim($json) === '') {
        return [];
    }

    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : [];
}
?>
