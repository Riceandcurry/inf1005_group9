<?php
require_once __DIR__ . '/../backend/auth_guard.php';
require_once __DIR__ . '/../backend/order_service.php';
require_login();

$confirmation = $_SESSION['ah_checkout_confirmation'] ?? null;
$currentUserId = (int) $auth->getCurrentUID();

$isValidConfirmation =
  is_array($confirmation)
  && ($confirmation['source'] ?? '') === 'paypal'
  && (int) ($confirmation['user_id'] ?? 0) === $currentUserId
  && isset($confirmation['created_at'])
  && (time() - (int) $confirmation['created_at']) <= 1800;

if (!$isValidConfirmation) {
  $_SESSION['error'] = 'No verified checkout confirmation found. Please complete payment first.';
  header('Location: checkout.php');
  exit;
}

$orderId = (int) ($confirmation['order_id'] ?? 0);
if ($orderId <= 0) {
  $_SESSION['error'] = 'Invalid checkout confirmation.';
  header('Location: checkout.php');
  exit;
}

$conn = connect_db();
$orderStmt = $conn->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = ? LIMIT 1');
$orderStmt->execute([$orderId, $currentUserId, 'paid']);
$order = $orderStmt->fetch(PDO::FETCH_ASSOC);
if (!is_array($order)) {
  $_SESSION['error'] = 'Paid order could not be found.';
  header('Location: checkout.php');
  exit;
}

$orderItems = ah_checkout_get_order_items($orderId);
$shippingSnapshot = ah_checkout_decode_shipping_snapshot((string) ($order['shipping_snapshot_json'] ?? ''));
$currency = strtoupper((string) ($order['currency'] ?? 'USD'));
$fmtMoney = static function ($amount) use ($currency): string {
  return $currency . ' ' . number_format((float) $amount, 2, '.', '');
};

$paypalOrderId = (string) ($order['payment_provider_order_id'] ?? ($confirmation['paypal_order_id'] ?? ''));
$displayOrderNum = 'AH-' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);
$paidLabel = $fmtMoney((float) ($order['total'] ?? 0));

$pageTitle = 'Order Confirmed - Aroma Haven';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-confirm-main">
  <div class="container py-5">
    <div class="ah-confirm-wrap mx-auto">
      <div class="ah-confirm-hero text-center">
        <div class="ah-confirm-icon mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               viewBox="0 0 24 24" aria-hidden="true">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <h1 class="ah-confirm-title">Order Confirmed!</h1>
        <p class="ah-confirm-subtitle">Your PayPal payment has been verified by the server.</p>
        <p class="ah-confirm-order-num">Order <span id="ahConfirmOrderNum">#<?php echo htmlspecialchars($displayOrderNum, ENT_QUOTES, 'UTF-8'); ?></span></p>
        <?php if ($paypalOrderId !== ''): ?>
          <p class="text-muted small mb-0">PayPal Ref: <?php echo htmlspecialchars($paypalOrderId, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
      </div>

      <div class="ah-checkout-card mb-4">
        <h2 class="ah-checkout-section-title">Order Receipt</h2>
        <div id="ahConfirmItems">
          <?php if (empty($orderItems)): ?>
            <p class="text-muted mb-0">No order items found.</p>
          <?php else: ?>
            <?php foreach ($orderItems as $item): ?>
              <div class="ah-order-item">
                <div class="ah-order-item-info">
                  <p class="ah-order-item-name mb-0"><?php echo htmlspecialchars((string) ($item['product_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                  <p class="ah-order-item-qty mb-0">Qty: <?php echo (int) ($item['quantity'] ?? 0); ?></p>
                </div>
                <span class="ah-order-item-price"><?php echo htmlspecialchars($fmtMoney((float) ($item['line_total'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="ah-confirm-totals">
          <div class="ah-confirm-total-row">
            <span>Subtotal</span>
            <span><?php echo htmlspecialchars($fmtMoney((float) ($order['subtotal'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
          <div class="ah-confirm-total-row">
            <span>Shipping</span>
            <span><?php echo htmlspecialchars($fmtMoney((float) ($order['shipping'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
          <div class="ah-confirm-total-row">
            <span>Tax</span>
            <span><?php echo htmlspecialchars($fmtMoney((float) ($order['tax'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
          <div class="ah-confirm-total-row ah-confirm-total-final">
            <span>Paid (PayPal)</span>
            <span id="ahConfirmTotal"><?php echo htmlspecialchars($paidLabel, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </div>
      </div>

      <div class="ah-checkout-card mb-5">
        <h2 class="ah-checkout-section-title">What Happens Next?</h2>
        <?php if (!empty($shippingSnapshot)): ?>
          <div class="mb-3">
            <p class="text-overline mb-1">Shipping To</p>
            <p class="mb-0"><?php echo htmlspecialchars((string) ($shippingSnapshot['full_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="mb-0 text-muted">
              <?php echo htmlspecialchars(trim((string) ($shippingSnapshot['street'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>,
              <?php echo htmlspecialchars(trim((string) ($shippingSnapshot['city'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>,
              <?php echo htmlspecialchars(trim((string) ($shippingSnapshot['state'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
              <?php echo htmlspecialchars(trim((string) ($shippingSnapshot['zip'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
            </p>
          </div>
        <?php endif; ?>
        <div class="ah-confirm-steps">
          <div class="ah-confirm-step">
            <div class="ah-confirm-step-num">1</div>
            <div>
              <p class="ah-confirm-step-title mb-0">Order Processing</p>
              <p class="ah-confirm-step-desc mb-0">We are preparing your beans for roasting and packing.</p>
            </div>
          </div>
          <div class="ah-confirm-step">
            <div class="ah-confirm-step-num">2</div>
            <div>
              <p class="ah-confirm-step-title mb-0">Shipped Within 2 Days</p>
              <p class="ah-confirm-step-desc mb-0">You will receive a tracking number once your order ships.</p>
            </div>
          </div>
          <div class="ah-confirm-step">
            <div class="ah-confirm-step-num">3</div>
            <div>
              <p class="ah-confirm-step-title mb-0">Delivered Fresh</p>
              <p class="ah-confirm-step-desc mb-0">Enjoy your coffee within 3-7 business days.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="ah-confirm-actions">
        <a href="shop-coffee.php" class="ah-confirm-shop-btn">CONTINUE SHOPPING</a>
        <a href="index.php" class="ah-confirm-home-btn">BACK TO HOME</a>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
(function () {
  localStorage.removeItem('ah_cart');
}());
</script>
