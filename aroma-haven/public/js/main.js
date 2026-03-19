(function () {
  var drawer       = document.getElementById('ahCartDrawer');
  var overlay      = document.getElementById('ahCartOverlay');
  var closeBtn     = document.getElementById('ahCartClose');
  var returnBtn    = document.getElementById('ahCartReturnBtn');
  var emptyEl      = document.getElementById('ahCartEmpty');
  var itemsEl      = document.getElementById('ahCartItems');
  var checkoutWrap = document.getElementById('ahCartCheckoutWrap');

  /* ── open / close ── */
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
  }

  window.openCart  = openCart;
  window.closeCart = closeCart;

  /* ── localStorage cart ── */
  var CART_KEY = 'ah_cart';

  function loadCart() {
    try { return JSON.parse(localStorage.getItem(CART_KEY)) || {}; }
    catch (e) { return {}; }
  }

  function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
  }

  function cartAdd(product) {
    var cart = loadCart();
    var id   = String(product.id);
    if (cart[id]) {
      cart[id].qty = Math.min(cart[id].qty + 1, 99);
    } else {
      cart[id] = {
        id:     id,
        name:   product.name   || 'Unknown',
        origin: product.origin || '',
        price:  parseFloat(String(product.price).replace(/[^0-9.]/g, '')) || 0,
        image:  product.image  || '',
        roast:  product.roast  || '',
        tags:   Array.isArray(product.tags) ? product.tags : [],
        qty:    1
      };
    }
    saveCart(cart);
    return Object.values(cart);
  }

  function cartUpdate(id, qty) {
    var cart = loadCart();
    if (qty <= 0) { delete cart[id]; }
    else          { if (cart[id]) cart[id].qty = Math.min(qty, 99); }
    saveCart(cart);
    return Object.values(cart);
  }

  /* ── HTML escaping ── */
  function esc(str) {
    return String(str == null ? '' : str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  /* ── render cart ── */
  function renderCart(items) {
    var has = Array.isArray(items) && items.length > 0;
    emptyEl.hidden      = has;
    itemsEl.hidden      = !has;
    checkoutWrap.hidden = !has;

    if (!has) { itemsEl.innerHTML = ''; return; }

    itemsEl.innerHTML = items.map(function (item) {
      var price = '$' + Number(item.price).toFixed(2);
      var tags  = (item.tags || []).map(function (t) {
        return '<span class="ah-cart-item-tag">' + esc(t) + '</span>';
      }).join('');
      return '<div class="ah-cart-item" data-id="' + esc(item.id) + '">' +
        '<img class="ah-cart-item-img" src="' + esc(item.image) + '" alt="' + esc(item.name) + '" loading="lazy">' +
        '<div class="ah-cart-item-details">' +
          '<div class="ah-cart-item-top">' +
            '<div>' +
              '<p class="ah-cart-item-name mb-0">' + esc(item.name) + '</p>' +
              '<p class="ah-cart-item-origin mb-1">' + esc(item.origin) + '</p>' +
            '</div>' +
            '<span class="ah-cart-item-price">' + price + '</span>' +
          '</div>' +
          '<div class="ah-cart-item-tags">' + tags + '</div>' +
          '<div class="ah-cart-qty">' +
            '<button class="ah-cart-qty-btn" data-qty-dec data-id="' + esc(item.id) + '" aria-label="Decrease quantity">&#8722;</button>' +
            '<span class="ah-cart-qty-num">' + item.qty + '</span>' +
            '<button class="ah-cart-qty-btn" data-qty-inc data-id="' + esc(item.id) + '" aria-label="Increase quantity">&#43;</button>' +
          '</div>' +
        '</div>' +
      '</div>';
    }).join('');
  }

  /* ── quantity stepper ── */
  itemsEl.addEventListener('click', function (e) {
    var dec = e.target.closest('[data-qty-dec]');
    var inc = e.target.closest('[data-qty-inc]');
    var btn = dec || inc;
    if (!btn) return;
    var id    = btn.getAttribute('data-id');
    var numEl = btn.closest('.ah-cart-qty').querySelector('.ah-cart-qty-num');
    var qty   = parseInt(numEl.textContent, 10) + (inc ? 1 : -1);
    renderCart(cartUpdate(id, qty));
  });

  /* ── public addToCart API ── */
  window.addToCart = function (product) {
    renderCart(cartAdd(product));
    openCart();
  };

  /* ── rec card add-to-cart buttons ── */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.ah-cart-rec-add-btn[data-id]');
    if (!btn) return;
    window.addToCart({
      id:     btn.getAttribute('data-id'),
      name:   btn.getAttribute('data-name'),
      origin: btn.getAttribute('data-origin'),
      price:  btn.getAttribute('data-price'),
      image:  btn.getAttribute('data-image'),
      roast:  btn.getAttribute('data-roast'),
      tags:   (btn.getAttribute('data-tags') || '').split(',').filter(Boolean)
    });
  });

  /* ── data-cart-trigger opens drawer ── */
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-cart-trigger]')) {
      e.preventDefault();
      openCart();
    }
  });

  closeBtn.addEventListener('click', closeCart);
  overlay.addEventListener('click', closeCart);

  returnBtn.addEventListener('click', function () {
    closeCart();
    window.location.href = 'shop-coffee.php';
  });

  document.getElementById('ahCartCheckoutBtn').addEventListener('click', function () {
    window.location.href = 'checkout.php';
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeCart();
  });

  /* ── auto-open from #open-cart hash ── */
  if (window.location.hash === '#open-cart') {
    history.replaceState(null, '', window.location.pathname + window.location.search);
    openCart();
  }

  /* ── shop card & product page add-to-cart ── */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.ah-bean-add-btn[data-id], .ah-coffee-add-btn[data-id]');
    if (!btn) return;
    var qtyEl = document.getElementById('ahProductQtyNum');
    var qty   = qtyEl ? Math.max(1, parseInt(qtyEl.textContent, 10) || 1) : 1;
    var id    = String(btn.getAttribute('data-id'));
    var cart  = loadCart();
    if (cart[id]) {
      cart[id].qty = Math.min(cart[id].qty + qty, 99);
    } else {
      cart[id] = {
        id:     id,
        name:   btn.getAttribute('data-name')   || 'Unknown',
        origin: btn.getAttribute('data-origin') || '',
        price:  parseFloat(String(btn.getAttribute('data-price') || '').replace(/[^0-9.]/g, '')) || 0,
        image:  btn.getAttribute('data-image')  || '',
        roast:  btn.getAttribute('data-roast')  || '',
        tags:   (btn.getAttribute('data-tags') || '').split(',').filter(Boolean),
        qty:    qty
      };
    }
    saveCart(cart);
    renderCart(Object.values(cart));
    openCart();
  });

  /* ── product page qty stepper ── */
  (function () {
    var qtyNum = document.getElementById('ahProductQtyNum');
    var qtyDec = document.getElementById('ahProductQtyDec');
    var qtyInc = document.getElementById('ahProductQtyInc');
    if (!qtyNum || !qtyDec || !qtyInc) return;
    qtyDec.addEventListener('click', function () {
      var q = parseInt(qtyNum.textContent, 10);
      if (q > 1) qtyNum.textContent = q - 1;
    });
    qtyInc.addEventListener('click', function () {
      var q = parseInt(qtyNum.textContent, 10);
      if (q < 99) qtyNum.textContent = q + 1;
    });
  }());

  /* ── restore cart on page load ── */
  renderCart(Object.values(loadCart()));
}());
