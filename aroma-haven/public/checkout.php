<?php
$pageTitle = 'Checkout — Aroma Haven';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-checkout-main">
  <div class="container py-5">

    <!-- Page heading -->
    <div class="text-center mb-5">
      <h1 class="ah-checkout-title">Checkout</h1>
      <p class="ah-checkout-subtitle">Complete your order</p>
    </div>

    <!-- Order Summary card -->
    <div class="ah-checkout-card mb-4" id="ahOrderSummary">
      <h2 class="ah-checkout-section-title">Order Summary</h2>

      <div id="ahOrderItems">
        <!-- JS renders items here -->
        <p class="ah-checkout-empty text-center py-3">Your cart is empty. <a href="shop-coffee.php">Go back to shop</a></p>
      </div>

      <hr class="ah-checkout-divider">

      <div class="ah-checkout-totals">
        <div class="ah-checkout-total-row">
          <span>Subtotal</span>
          <span id="ahSubtotal">$0.00</span>
        </div>
        <div class="ah-checkout-total-row">
          <span>Shipping</span>
          <span id="ahShipping">$5.99</span>
        </div>
        <div class="ah-checkout-total-row">
          <span>Tax <small>(8%)</small></span>
          <span id="ahTax">$0.00</span>
        </div>
        <hr class="ah-checkout-divider">
        <div class="ah-checkout-total-row ah-checkout-grand-total">
          <span>Total</span>
          <span id="ahTotal">$0.00</span>
        </div>
      </div>
    </div>

    <!-- Shipping Information -->
    <div class="ah-checkout-card mb-4">
      <h2 class="ah-checkout-section-title">Shipping Information</h2>
      <form id="ahCheckoutForm" novalidate>

        <div class="ah-checkout-field">
          <label for="chkFullName">Full Name</label>
          <input type="text" id="chkFullName" name="full_name" autocomplete="name" required>
        </div>

        <div class="ah-checkout-field">
          <label for="chkEmail">Email Address</label>
          <input type="email" id="chkEmail" name="email" autocomplete="email" required>
        </div>

        <div class="ah-checkout-field">
          <label for="chkStreet">Street Address</label>
          <input type="text" id="chkStreet" name="street" autocomplete="street-address" required>
        </div>

        <div class="row g-4">
          <div class="col-12 col-sm-6">
            <div class="ah-checkout-field">
              <label for="chkCity">City</label>
              <input type="text" id="chkCity" name="city" autocomplete="address-level2" required>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="ah-checkout-field">
              <label for="chkState">State</label>
              <input type="text" id="chkState" name="state" autocomplete="address-level1" required>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="ah-checkout-field">
              <label for="chkZip">ZIP Code</label>
              <input type="text" id="chkZip" name="zip" autocomplete="postal-code" required>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="ah-checkout-field">
              <label for="chkPhone">Phone Number</label>
              <input type="tel" id="chkPhone" name="phone" autocomplete="tel">
            </div>
          </div>
        </div>

        <!-- Payment Method -->
        <h2 class="ah-checkout-section-title mt-5">Payment Method</h2>

        <div class="ah-payment-options">
          <label class="ah-payment-option">
            <input type="radio" name="payment" value="credit_card" checked>
            <div class="ah-payment-option-box">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none"
                   stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                   viewBox="0 0 24 24" aria-hidden="true">
                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                <line x1="1" y1="10" x2="23" y2="10"/>
              </svg>
              <span>Credit Card</span>
            </div>
          </label>
          <label class="ah-payment-option">
            <input type="radio" name="payment" value="paypal">
            <div class="ah-payment-option-box">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                   fill="currentColor" aria-hidden="true">
                <path d="M7.076 21.337H4.93l1.33-8.438h2.146l-1.33 8.438zm9.712-8.194c-.308-1.97-1.842-2.8-3.65-2.8H9.89l-1.785 11.33h2.147l.48-3.044h1.172c2.417 0 4.005-1.386 4.36-3.583.18-1.113.07-1.99-.476-2.903zm-2.07 2.79c-.185 1.18-.993 1.845-2.127 1.845h-.93l.547-3.466h.987c1.08 0 1.696.544 1.523 1.621zm7.25-4.77h-2.04l-2.77 8.438h2.19l.44-1.385h2.59l.21 1.385h1.932l-1.88-8.438h-.672zm-1.72 5.52l1.21-3.832.59 3.831h-1.8z"/>
              </svg>
              <span>PayPal</span>
            </div>
          </label>
        </div>

        <!-- Credit Card fields panel -->
        <div class="ah-cc-fields" id="ahCreditCardFields">
          <div class="ah-checkout-field">
            <label for="chkCardNumber">Card Number</label>
            <input type="text" id="chkCardNumber" name="card_number" placeholder="1234 5678 9012 3456"
                   inputmode="numeric" maxlength="19" autocomplete="cc-number">
          </div>
          <div class="ah-checkout-field">
            <label for="chkCardName">Cardholder Name</label>
            <input type="text" id="chkCardName" name="card_name" autocomplete="cc-name">
          </div>
          <div class="row g-4">
            <div class="col-12 col-sm-6">
              <div class="ah-checkout-field">
                <label for="chkExpiry">Expiry Date</label>
                <input type="text" id="chkExpiry" name="expiry" placeholder="MM/YY"
                       inputmode="numeric" maxlength="5" autocomplete="cc-exp">
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <div class="ah-checkout-field">
                <label for="chkCvv">CVV</label>
                <input type="text" id="chkCvv" name="cvv" placeholder="123"
                       inputmode="numeric" maxlength="4" autocomplete="cc-csc">
              </div>
            </div>
          </div>
          <p class="ah-checkout-secure-note mb-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                 viewBox="0 0 24 24" aria-hidden="true">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Your payment information is secure and encrypted
          </p>
        </div>

        <!-- PayPal notice panel -->
        <div class="ah-paypal-notice" id="ahPaypalNotice" hidden>
          <p class="mb-2">You <em>will</em> be redirected to PayPal to complete your purchase securely.</p>
          <p class="mb-0 ah-paypal-secure">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                 viewBox="0 0 24 24" aria-hidden="true">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Secure PayPal payment
          </p>
        </div>

        <!-- PayPal email (shown only when PayPal selected) -->
        <div class="ah-checkout-field" id="ahPaypalEmailWrap" hidden>
          <label for="chkPaypalEmail">PayPal Email (Optional)</label>
          <input type="email" id="chkPaypalEmail" name="paypal_email" placeholder="your@email.com" autocomplete="email">
        </div>

        <!-- Actions -->
        <div class="ah-checkout-actions">
          <a href="shop-coffee.php" class="ah-checkout-back-btn">BACK TO SHOP</a>
          <button type="submit" class="ah-checkout-place-btn">PLACE ORDER</button>
        </div>

      </form>
    </div>

  </div><!-- /container -->
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
(function () {
  var CART_KEY = 'ah_cart';
  var SHIPPING = 5.99;
  var TAX_RATE = 0.08;

  function loadCart() {
    try { return Object.values(JSON.parse(localStorage.getItem(CART_KEY)) || {}); }
    catch (e) { return []; }
  }

  function fmt(n) { return '$' + n.toFixed(2); }

  function renderSummary() {
    var items   = loadCart();
    var itemsEl = document.getElementById('ahOrderItems');
    var subtotal = 0;

    if (!items.length) return;

    itemsEl.innerHTML = items.map(function (item) {
      var lineTotal = parseFloat(item.price || 0) * item.qty;
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

    document.getElementById('ahSubtotal').textContent = fmt(subtotal);
    document.getElementById('ahTax').textContent      = fmt(tax);
    document.getElementById('ahTotal').textContent    = fmt(total);
  }

  /* Payment toggle */
  document.querySelectorAll('input[name="payment"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
      var isPaypal = this.value === 'paypal';
      document.getElementById('ahCreditCardFields').hidden = isPaypal;
      document.getElementById('ahPaypalNotice').hidden    = !isPaypal;
      document.getElementById('ahPaypalEmailWrap').hidden = !isPaypal;
    });
  });

  /* Form submit */
  document.getElementById('ahCheckoutForm').addEventListener('submit', function (e) {
    e.preventDefault();
    if (!this.checkValidity()) {
      this.classList.add('was-validated');
      return;
    }
    /* Snapshot the cart into sessionStorage so confirmation.php can display it */
    var cartRaw = localStorage.getItem(CART_KEY);
    sessionStorage.setItem('ah_confirm_order', cartRaw || '{}');
    sessionStorage.setItem('ah_confirm_num', Date.now().toString(36).toUpperCase().slice(-6));
    localStorage.removeItem(CART_KEY);
    window.location.href = 'confirmation.php';
  });

  renderSummary();
}());
</script>
