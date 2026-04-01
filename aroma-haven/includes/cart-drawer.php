<!-- =====================================================================
     CART DRAWER - present on every page via footer.php
     Open:  call window.openCart()  or click [data-cart-trigger]
     Close: #ahCartClose, backdrop, or Escape key
     ===================================================================== -->
<div class="ah-cart-overlay" id="ahCartOverlay"></div>

<div class="ah-cart-drawer" id="ahCartDrawer"
     role="dialog" aria-modal="true" aria-labelledby="ahCartTitle" aria-hidden="true" tabindex="-1">

  <div class="ah-cart-header">
    <h2 class="ah-cart-title mb-0" id="ahCartTitle">Shopping cart</h2>
    <button class="ah-cart-close" id="ahCartClose" aria-label="Close cart">&#10005;</button>
  </div>
  <div class="ah-cart-divider"></div>

  <div class="ah-cart-scroll">
    <section class="ah-cart-primary" aria-label="Cart items">
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
          <p class="ah-cart-empty-sub">Start adding some delicious coffee.</p>
        </div>
      </div>

      <div class="ah-cart-items" id="ahCartItems" hidden></div>
      <p id="ahCartStatus" class="visually-hidden" aria-live="polite" aria-atomic="true"></p>
    </section>

    <div class="ah-cart-checkout-wrap" id="ahCartCheckoutWrap" hidden>
      <button class="ah-cart-checkout-btn" type="button" id="ahCartCheckoutBtn">Proceed to checkout</button>
    </div>

    <div class="ah-cart-footer">
      <button class="ah-cart-return-btn" id="ahCartReturnBtn" type="button">Return to shop</button>
    </div>
  </div>

</div>