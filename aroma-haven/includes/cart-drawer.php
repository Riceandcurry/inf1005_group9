<!-- =====================================================================
     CART DRAWER  —  present on every page via footer.php
     Open:  call window.openCart()  or click [data-cart-trigger]
     Close: #ahCartClose, backdrop, or Escape key
     ===================================================================== -->
<div class="ah-cart-overlay" id="ahCartOverlay"></div>

<aside class="ah-cart-drawer" id="ahCartDrawer"
       role="dialog" aria-modal="true" aria-label="Shopping Cart" aria-hidden="true">

  <div class="ah-cart-header">
    <span class="ah-cart-title">SHOPPING CART</span>
    <button class="ah-cart-close" id="ahCartClose" aria-label="Close cart">&#10005;</button>
  </div>
  <div class="ah-cart-divider"></div>

  <div class="ah-cart-body" id="ahCartEmpty">
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

  <div class="ah-cart-items" id="ahCartItems" hidden></div>

  <div class="ah-cart-checkout-wrap" id="ahCartCheckoutWrap" hidden>
    <button class="ah-cart-checkout-btn" type="button" id="ahCartCheckoutBtn">PROCEED TO CHECKOUT</button>
  </div>

  <hr class="ah-cart-sep">

  <p class="ah-cart-rec-label">We think you may like</p>

  <?php
    require_once __DIR__ . '/bean-catalog.php';
    $ahAllBeans  = array_values(ah_get_bean_catalog());
    $ahRecKeys   = (count($ahAllBeans) >= 2)
                     ? array_rand($ahAllBeans, 2)
                     : [0, min(1, count($ahAllBeans) - 1)];
    $ahRecBeans  = [$ahAllBeans[$ahRecKeys[0]], $ahAllBeans[$ahRecKeys[1]]];
  ?>
  <div class="ah-cart-recs">
    <?php foreach ($ahRecBeans as $ahRec): ?>
    <div class="ah-cart-rec-card">
      <div class="ah-cart-rec-img-wrap">
        <img src="<?php echo htmlspecialchars($ahRec['image'], ENT_QUOTES, 'UTF-8'); ?>"
             alt="<?php echo htmlspecialchars($ahRec['name'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
        <span class="ah-cart-rec-roast"><?php echo htmlspecialchars($ahRec['roast'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
      <div class="ah-cart-rec-info">
        <div>
          <p class="ah-cart-rec-name mb-0"><?php echo htmlspecialchars($ahRec['name'], ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="ah-cart-rec-origin mb-0"><?php echo htmlspecialchars($ahRec['origin'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <span class="ah-cart-rec-price"><?php echo htmlspecialchars($ahRec['price'] ?? '$?', ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
      <div class="ah-cart-rec-tags">
        <?php foreach (array_slice((array)($ahRec['tags'] ?? []), 0, 3) as $ahTag): ?>
          <span class="ah-cart-rec-tag"><?php echo htmlspecialchars((string)$ahTag, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endforeach; ?>
      </div>
      <button class="ah-cart-rec-add-btn" type="button"
              data-id="<?php echo htmlspecialchars($ahRec['id'], ENT_QUOTES, 'UTF-8'); ?>"
              data-name="<?php echo htmlspecialchars($ahRec['name'], ENT_QUOTES, 'UTF-8'); ?>"
              data-origin="<?php echo htmlspecialchars($ahRec['origin'], ENT_QUOTES, 'UTF-8'); ?>"
              data-price="<?php echo htmlspecialchars($ahRec['price'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>"
              data-image="<?php echo htmlspecialchars($ahRec['image'], ENT_QUOTES, 'UTF-8'); ?>"
              data-roast="<?php echo htmlspecialchars($ahRec['roast'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
              data-tags="<?php echo htmlspecialchars(implode(',', (array)($ahRec['tags'] ?? [])), ENT_QUOTES, 'UTF-8'); ?>"
      >+ Add to cart</button>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="ah-cart-footer">
    <button class="ah-cart-return-btn" id="ahCartReturnBtn" type="button">RETURN TO SHOP</button>
  </div>

</aside>
