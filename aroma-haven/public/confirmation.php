<?php
require_once __DIR__ . '/../backend/auth_guard.php';
require_login();
$pageTitle = 'Order Confirmed — Aroma Haven';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-confirm-main">
  <div class="container py-5">

    <div class="ah-confirm-wrap mx-auto">

      <!-- Checkmark hero -->
      <div class="ah-confirm-hero text-center">
        <div class="ah-confirm-icon mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               viewBox="0 0 24 24" aria-hidden="true">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <h1 class="ah-confirm-title">Order Confirmed!</h1>
        <p class="ah-confirm-subtitle">Thank you for your purchase. Your coffee is on its way.</p>
        <p class="ah-confirm-order-num">Order <span id="ahConfirmOrderNum">#—</span></p>
      </div>

      <!-- Order summary -->
      <div class="ah-checkout-card mb-4">
        <h2 class="ah-checkout-section-title">Your Order</h2>
        <div id="ahConfirmItems"></div>

        <div class="ah-confirm-totals">
          <div class="ah-confirm-total-row">
            <span>Subtotal</span>
            <span id="ahConfirmSubtotal">—</span>
          </div>
          <div class="ah-confirm-total-row">
            <span>Shipping</span>
            <span id="ahConfirmShipping">—</span>
          </div>
          <div class="ah-confirm-total-row">
            <span>Tax (8%)</span>
            <span id="ahConfirmTax">—</span>
          </div>
          <div class="ah-confirm-total-row ah-confirm-total-final">
            <span>Total</span>
            <span id="ahConfirmTotal">—</span>
          </div>
        </div>
      </div>

      <!-- What's next -->
      <div class="ah-checkout-card mb-5">
        <h2 class="ah-checkout-section-title">What Happens Next?</h2>
        <div class="ah-confirm-steps">
          <div class="ah-confirm-step">
            <div class="ah-confirm-step-num">1</div>
            <div>
              <p class="ah-confirm-step-title mb-0">Order Processing</p>
              <p class="ah-confirm-step-desc mb-0">We're preparing your beans for roasting and packing.</p>
            </div>
          </div>
          <div class="ah-confirm-step">
            <div class="ah-confirm-step-num">2</div>
            <div>
              <p class="ah-confirm-step-title mb-0">Shipped Within 2 Days</p>
              <p class="ah-confirm-step-desc mb-0">You'll receive a tracking number once your order ships.</p>
            </div>
          </div>
          <div class="ah-confirm-step">
            <div class="ah-confirm-step-num">3</div>
            <div>
              <p class="ah-confirm-step-title mb-0">Delivered Fresh</p>
              <p class="ah-confirm-step-desc mb-0">Enjoy your coffee within 3–7 business days.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
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
  var CART_KEY = 'ah_confirm_order';
  var SHIPPING = 5.99;
  var TAX_RATE = 0.08;

  function fmt(n) { return '$' + Number(n).toFixed(2); }

  /* Generate a simple order number */
  function genOrderNum() {
    return 'AH-' + Date.now().toString(36).toUpperCase().slice(-6);
  }

  /* Read the saved order snapshot (set by checkout.php before clearing cart) */
  var raw = sessionStorage.getItem(CART_KEY);
  var items = [];
  try { items = Object.values(JSON.parse(raw) || {}); } catch (e) {}

  /* Fallback: show generic confirmed message if no data */
  var orderNum = sessionStorage.getItem('ah_confirm_num') || genOrderNum();
  document.getElementById('ahConfirmOrderNum').textContent = '#' + orderNum;

  if (items.length) {
    var itemsEl  = document.getElementById('ahConfirmItems');
    var subtotal = 0;

    itemsEl.innerHTML = items.map(function (item) {
      var lineTotal = parseFloat(item.price || 0) * (item.qty || 1);
      subtotal += lineTotal;
      return '<div class="ah-order-item">' +
        '<img class="ah-order-item-img" src="' + item.image + '" alt="' + item.name + '" loading="lazy">' +
        '<div class="ah-order-item-info">' +
          '<p class="ah-order-item-name mb-0">' + item.name + '</p>' +
          '<p class="ah-order-item-qty mb-0">Qty: ' + item.qty + '</p>' +
        '</div>' +
        '<span class="ah-order-item-price">' + fmt(lineTotal) + '</span>' +
      '</div>';
    }).join('');

    var tax   = subtotal * TAX_RATE;
    var total = subtotal + SHIPPING + tax;

    document.getElementById('ahConfirmSubtotal').textContent = fmt(subtotal);
    document.getElementById('ahConfirmShipping').textContent = fmt(SHIPPING);
    document.getElementById('ahConfirmTax').textContent      = fmt(tax);
    document.getElementById('ahConfirmTotal').textContent    = fmt(total);
  }

  /* Clean up session data */
  sessionStorage.removeItem(CART_KEY);
  sessionStorage.removeItem('ah_confirm_num');
}());
</script>
