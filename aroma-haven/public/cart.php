<?php
// cart.php — opens the cart drawer on whatever page the user came from.
// The navbar cart icon now uses data-cart-trigger and never navigates here;
// this file is a fallback for direct URL visits only.
if (!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER'])) {
    header('Location: index.php#open-cart');
} else {
    header('Location: ' . $_SERVER['HTTP_REFERER'] . '#open-cart');
}
exit;
?>
<!-- (dead code below this line - kept only for reference) -->
<!-- Cart backdrop -->
<div class="ah-cart-overlay" id="ahCartOverlay"></div>

<!-- Cart drawer -->
<aside class="ah-cart-drawer" id="ahCartDrawer"
       role="dialog" aria-modal="true" aria-label="Shopping Cart" aria-hidden="true">

  <!-- Header -->
  <div class="ah-cart-header">
    <span class="ah-cart-title">SHOPPING CART</span>
    <button class="ah-cart-close" id="ahCartClose" aria-label="Close cart">&#10005;</button>
  </div>
  <div class="ah-cart-divider"></div>

  <!-- Empty-cart state -->
  <div class="ah-cart-body">
    <div class="ah-cart-empty text-center">
      <div class="ah-cart-empty-icon mx-auto mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="1.4"
             stroke-linecap="round" stroke-linejoin="round"
             width="48" height="48" aria-hidden="true">
          <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 01-8 0"/>
        </svg>
      </div>
      <p class="ah-cart-empty-title">Your cart is empty</p>
      <p class="ah-cart-empty-sub">Start adding some delicious coffee!</p>
    </div>
  </div>

  <hr class="ah-cart-sep">

  <!-- Recommendations -->
  <p class="ah-cart-rec-label">We think you may like</p>

  <div class="ah-cart-recs">
    <?php foreach ($recommendations as $item): ?>
    <div class="ah-cart-rec-card">
      <div class="ah-cart-rec-img-wrap">
        <img src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($item['name'],  ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
        <span class="ah-cart-rec-roast"><?= htmlspecialchars($item['roast'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="ah-cart-rec-info">
        <div>
          <p class="ah-cart-rec-name mb-0"><?= htmlspecialchars($item['name'],   ENT_QUOTES, 'UTF-8') ?></p>
          <p class="ah-cart-rec-origin mb-0"><?= htmlspecialchars($item['origin'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <span class="ah-cart-rec-price"><?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="ah-cart-rec-tags">
        <?php foreach ($item['tags'] as $tag): ?>
          <span class="ah-cart-rec-tag"><?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?></span>
        <?php endforeach; ?>
      </div>
      <button class="ah-cart-rec-add-btn" type="button">+ Add to cart</button>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Return to shop -->
  <div class="ah-cart-footer">
    <a href="shop-coffee.php" class="ah-cart-return-btn">RETURN TO SHOP</a>
  </div>

</aside>

<script>
(function () {
  var drawer   = document.getElementById('ahCartDrawer');
  var overlay  = document.getElementById('ahCartOverlay');
  var closeBtn = document.getElementById('ahCartClose');

  function openCart() {
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    overlay.classList.add('active');
    document.body.classList.add('ah-cart-open');
  }

  function closeCart() {
    drawer.classList.remove('open');
    drawer.setAttribute('aria-hidden', 'true');
    overlay.classList.remove('active');
    document.body.classList.remove('ah-cart-open');
    // Wait for slide-out animation, then navigate back
    setTimeout(function () {
      if (history.length > 1) {
        history.back();
      } else {
        window.location.href = 'index.php';
      }
    }, 320);
  }

  // Slide in immediately on page load
  document.addEventListener('DOMContentLoaded', openCart);

  closeBtn.addEventListener('click', closeCart);
  overlay.addEventListener('click', closeCart);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeCart();
  });
}());
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
