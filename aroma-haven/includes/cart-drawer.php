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

  <div class="ah-cart-recs">
    <div class="ah-cart-rec-card">
      <div class="ah-cart-rec-img-wrap">
        <img src="images/products/product_1.jpg" alt="Bean name" loading="lazy">
        <span class="ah-cart-rec-roast">Light Roast</span>
      </div>
      <div class="ah-cart-rec-info">
        <div>
          <p class="ah-cart-rec-name mb-0">Bean name</p>
          <p class="ah-cart-rec-origin mb-0">Bean origin</p>
        </div>
        <span class="ah-cart-rec-price">$?</span>
      </div>
      <div class="ah-cart-rec-tags">
        <span class="ah-cart-rec-tag">Add</span>
        <span class="ah-cart-rec-tag">Taste</span>
        <span class="ah-cart-rec-tag">Notes</span>
      </div>
      <button class="ah-cart-rec-add-btn" type="button"
              data-id="rec-1" data-name="Bean name" data-origin="Bean origin"
              data-price="0" data-image="images/products/product_1.jpg"
              data-roast="Light Roast" data-tags="Add,Taste,Notes">+ Add to cart</button>
    </div>
    <div class="ah-cart-rec-card">
      <div class="ah-cart-rec-img-wrap">
        <img src="images/products/product_1.jpg" alt="Bean name" loading="lazy">
        <span class="ah-cart-rec-roast">Light Roast</span>
      </div>
      <div class="ah-cart-rec-info">
        <div>
          <p class="ah-cart-rec-name mb-0">Bean name</p>
          <p class="ah-cart-rec-origin mb-0">Bean origin</p>
        </div>
        <span class="ah-cart-rec-price">$?</span>
      </div>
      <div class="ah-cart-rec-tags">
        <span class="ah-cart-rec-tag">Add</span>
        <span class="ah-cart-rec-tag">Taste</span>
        <span class="ah-cart-rec-tag">Notes</span>
      </div>
      <button class="ah-cart-rec-add-btn" type="button"
              data-id="rec-2" data-name="Bean name" data-origin="Bean origin"
              data-price="0" data-image="images/products/product_1.jpg"
              data-roast="Light Roast" data-tags="Add,Taste,Notes">+ Add to cart</button>
    </div>
  </div>

  <div class="ah-cart-footer">
    <button class="ah-cart-return-btn" id="ahCartReturnBtn" type="button">RETURN TO SHOP</button>
  </div>

</aside>
