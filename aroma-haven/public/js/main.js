(function checkoutValidationAndFormatting() {
  // Only run on checkout page
  if (!document.getElementById('ahCheckoutForm')) return;
  var form = document.getElementById('ahCheckoutForm');
  var phone = document.getElementById('chkPhone');
  var zip = document.getElementById('chkZip');
  var email = document.getElementById('chkEmail');

  // Auto-format phone number as user types
  phone.addEventListener('input', function () {
    // Remove all non-numeric except +, -, (, ), and space
    var val = phone.value.replace(/[^0-9+\-() ]/g, '');
    phone.value = val;
  });

  // Auto-uppercase ZIP (for international)
  zip.addEventListener('input', function () {
    zip.value = zip.value.toUpperCase();
  });

  // Custom validation for phone and zip
  form.addEventListener('submit', function (e) {
    var valid = true;
    // Phone: must match pattern and not be all spaces
    if (!phone.value.match(/^\+?[0-9\- ()]{8,20}$/) || !phone.value.replace(/[^0-9]/g, '')) {
      phone.setCustomValidity('Please enter a valid phone number.');
      valid = false;
    } else {
      phone.setCustomValidity('');
    }
    // ZIP: must match pattern
    if (!zip.value.match(/[A-Za-z0-9\- ]{3,12}/)) {
      zip.setCustomValidity('Please enter a valid ZIP or postal code.');
      valid = false;
    } else {
      zip.setCustomValidity('');
    }
    // Email: browser handles, but add custom message
    if (!email.checkValidity()) {
      email.setCustomValidity('Please enter a valid email address.');
      valid = false;
    } else {
      email.setCustomValidity('');
    }
    if (!valid) {
      form.classList.add('was-validated');
      e.preventDefault();
      e.stopPropagation();
    }
  }, true);

  // Accessibility: focus first invalid field on submit
  form.addEventListener('invalid', function (e) {
    e.preventDefault();
    var first = form.querySelector(':invalid');
    if (first) first.focus();
  }, true);
})();
(function () {
  var drawer       = document.getElementById('ahCartDrawer');
  var overlay      = document.getElementById('ahCartOverlay');
  var closeBtn     = document.getElementById('ahCartClose');
  var returnBtn    = document.getElementById('ahCartReturnBtn');
  var emptyEl      = document.getElementById('ahCartEmpty');
  var itemsEl      = document.getElementById('ahCartItems');
  var checkoutWrap = document.getElementById('ahCartCheckoutWrap');
  var checkoutBtn  = document.getElementById('ahCartCheckoutBtn');
  var statusEl     = document.getElementById('ahCartStatus');
  var lastCartTrigger = null;

  function isCartReady() {
    return !!(drawer && overlay && closeBtn && returnBtn && emptyEl && itemsEl && checkoutWrap && checkoutBtn);
  }

  function getDrawerFocusableElements() {
    if (!drawer) return [];
    return Array.prototype.slice.call(drawer.querySelectorAll(
      'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )).filter(function (el) {
      return !el.hidden;
    });
  }

  /* â”€â”€ open / close â”€â”€ */
  function openCart(triggerEl) {
    if (!isCartReady()) return;
    lastCartTrigger = triggerEl || document.activeElement;
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    overlay.classList.add('active');
    document.body.classList.add('ah-cart-open');
    setTimeout(function () {
      closeBtn.focus();
    }, 0);
  }

  function closeCart() {
    if (!isCartReady()) return;
    drawer.classList.remove('open');
    drawer.setAttribute('aria-hidden', 'true');
    overlay.classList.remove('active');
    document.body.classList.remove('ah-cart-open');
    if (lastCartTrigger && typeof lastCartTrigger.focus === 'function') {
      lastCartTrigger.focus();
    }
  }

  window.openCart  = openCart;
  window.closeCart = closeCart;

  /* â”€â”€ localStorage cart â”€â”€ */
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

  /* â”€â”€ HTML escaping â”€â”€ */
  function esc(str) {
    return String(str == null ? '' : str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  /* â”€â”€ render cart â”€â”€ */
  function renderCart(items) {
    if (!isCartReady()) return;
    var has = Array.isArray(items) && items.length > 0;
    emptyEl.hidden      = has;
    itemsEl.hidden      = !has;
    checkoutWrap.hidden = !has;

    if (!has) {
      itemsEl.innerHTML = '';
      if (statusEl) statusEl.textContent = 'Cart is empty.';
      return;
    }

    itemsEl.innerHTML = items.map(function (item) {
      var price = '$' + Number(item.price).toFixed(2);
      var tags  = (item.tags || []).map(function (t) {
        return '<span class="ah-cart-item-tag">' + esc(t) + '</span>';
      }).join('');
      var itemKey = item.key || item.id;
      return '<div class="ah-cart-item" data-id="' + esc(itemKey) + '">' +
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
          (item.grind ? '<p class="ah-cart-item-grind mb-0">' + esc(item.grind) + '</p>' : '') +
          '<div class="ah-cart-qty">' +
            '<button class="ah-cart-qty-btn" data-qty-dec data-id="' + esc(itemKey) + '" aria-label="Decrease quantity">&#8722;</button>' +
            '<span class="ah-cart-qty-num">' + item.qty + '</span>' +
            '<button class="ah-cart-qty-btn" data-qty-inc data-id="' + esc(itemKey) + '" aria-label="Increase quantity">&#43;</button>' +
          '</div>' +
        '</div>' +
      '</div>';
    }).join('');

    if (statusEl) {
      var itemCount = items.reduce(function (total, item) {
        return total + (parseInt(item.qty, 10) || 0);
      }, 0);
      statusEl.textContent = 'Cart updated. ' + itemCount + ' item' + (itemCount === 1 ? '' : 's') + ' in cart.';
    }
  }

  /* â”€â”€ quantity stepper â”€â”€ */
  if (isCartReady()) {
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
  }

  /* â”€â”€ public addToCart API â”€â”€ */
  window.addToCart = function (product) {
    renderCart(cartAdd(product));
    openCart();
  };

    /* â”€â”€ data-cart-trigger opens drawer â”€â”€ */
  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-cart-trigger]');
    if (trigger) {
      e.preventDefault();
      openCart(trigger);
    }
  });

  if (isCartReady()) {
    closeBtn.addEventListener('click', closeCart);
    overlay.addEventListener('click', closeCart);

    returnBtn.addEventListener('click', function () {
      closeCart();
      window.location.href = 'shop-coffee.php';
    });

    checkoutBtn.addEventListener('click', function () {
      window.location.href = 'checkout.php';
    });
  }

  document.addEventListener('keydown', function (e) {
    if (!isCartReady() || drawer.getAttribute('aria-hidden') === 'true') return;

    if (e.key === 'Escape') {
      closeCart();
      return;
    }

    if (e.key !== 'Tab') return;
    var focusables = getDrawerFocusableElements();
    if (focusables.length === 0) return;

    var first = focusables[0];
    var last = focusables[focusables.length - 1];

    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  });

  /* â”€â”€ auto-open from #open-cart hash â”€â”€ */
  if (window.location.hash === '#open-cart') {
    history.replaceState(null, '', window.location.pathname + window.location.search);
    openCart();
  }

  /* â”€â”€ shop card & product page add-to-cart â”€â”€ */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.ah-coffee-add-btn[data-id]');
    if (!btn) return;
    var qtyEl = document.getElementById('ahProductQtyNum');
    var qty   = qtyEl ? Math.max(1, parseInt(qtyEl.textContent, 10) || 1) : 1;
    var productId = String(btn.getAttribute('data-id'));
    var activeGrind = document.querySelector('.ah-grind-option.is-active');
    var grind = activeGrind ? activeGrind.textContent.trim() : '';
    var cartKey = productId + (grind ? '|' + grind : '');
    var cart  = loadCart();
    if (cart[cartKey]) {
      cart[cartKey].qty = Math.min(cart[cartKey].qty + qty, 99);
    } else {
      cart[cartKey] = {
        key:    cartKey,
        id:     productId,
        name:   btn.getAttribute('data-name')   || 'Unknown',
        origin: btn.getAttribute('data-origin') || '',
        price:  parseFloat(String(btn.getAttribute('data-price') || '').replace(/[^0-9.]/g, '')) || 0,
        image:  btn.getAttribute('data-image')  || '',
        roast:  btn.getAttribute('data-roast')  || '',
        tags:   (btn.getAttribute('data-tags') || '').split(',').filter(Boolean),
        grind:  grind,
        qty:    qty
      };
    }
    saveCart(cart);
    renderCart(Object.values(cart));
    openCart();
  });

  /* â”€â”€ product page qty stepper â”€â”€ */
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

  /* â”€â”€ grind size toggle â”€â”€ */
  (function () {
    var grindGrid = document.querySelector('.ah-grind-grid');
    if (!grindGrid) return;
    grindGrid.addEventListener('click', function (e) {
      var btn = e.target.closest('.ah-grind-option');
      if (!btn) return;
      grindGrid.querySelectorAll('.ah-grind-option').forEach(function (el) {
        el.classList.remove('is-active');
        el.setAttribute('aria-pressed', 'false');
      });
      btn.classList.add('is-active');
      btn.setAttribute('aria-pressed', 'true');
    });
  }());

  /* â”€â”€ restore cart on page load â”€â”€ */
  /* -- shop page filters + pagination -- */
  (function () {
    var shopRoot = document.querySelector('[data-shop-page]');
    if (!shopRoot) return;

    var filtersForm = document.getElementById('ahShopFilters');
    var searchInput = document.getElementById('ahShopSearchInput');
    var minPriceInput = document.getElementById('ahShopMinPrice');
    var maxPriceInput = document.getElementById('ahShopMaxPrice');
    var priceOutput = document.getElementById('ahShopPriceOutput');
    var resetBtn = document.getElementById('ahShopResetFilters');
    var resultsCount = document.getElementById('ahShopResultsCount');
    var grid = document.getElementById('ahShopGrid');
    var pagination = document.getElementById('ahShopPagination');
    var prevBtn = document.getElementById('ahShopPrevPage');
    var nextBtn = document.getElementById('ahShopNextPage');
    var pageList = document.getElementById('ahShopPageList');

    if (
      !filtersForm || !searchInput || !minPriceInput || !maxPriceInput || !priceOutput ||
      !resetBtn || !resultsCount || !grid || !pagination || !prevBtn || !nextBtn || !pageList
    ) return;

    var itemNodes = Array.prototype.slice.call(grid.querySelectorAll('.ah-shop-item'));
    var pageSize = Math.max(1, parseInt(shopRoot.getAttribute('data-default-page-size') || '6', 10));
    var state = { currentPage: 1 };

    function normalize(value) {
      return String(value || '').toLowerCase().trim();
    }

    function updatePriceOutput() {
      priceOutput.textContent = '$' + minPriceInput.value + ' - $' + maxPriceInput.value;
    }

    function readCheckedValues(selector) {
      return Array.prototype.slice.call(filtersForm.querySelectorAll(selector + ':checked'))
        .map(function (el) { return normalize(el.value); });
    }

    function isPageNumberVisible(page, currentPage, totalPages) {
      if (page <= 2 || page > totalPages - 2) return true;
      return Math.abs(page - currentPage) <= 1;
    }

    function renderPageButtons(currentPage, totalPages) {
      pageList.innerHTML = '';

      var lastWasEllipsis = false;
      for (var page = 1; page <= totalPages; page += 1) {
        var visible = isPageNumberVisible(page, currentPage, totalPages);
        if (!visible) {
          if (!lastWasEllipsis) {
            var ellipsis = document.createElement('span');
            ellipsis.className = 'ah-shop-page-ellipsis';
            ellipsis.setAttribute('aria-hidden', 'true');
            ellipsis.textContent = '...';
            pageList.appendChild(ellipsis);
            lastWasEllipsis = true;
          }
          continue;
        }

        lastWasEllipsis = false;
        var pageBtn = document.createElement('button');
        pageBtn.type = 'button';
        pageBtn.className = 'ah-shop-page-btn';
        pageBtn.textContent = String(page);
        pageBtn.setAttribute('aria-label', 'Go to page ' + page);
        pageBtn.setAttribute('aria-controls', 'ahShopGrid');
        pageBtn.setAttribute('data-page', String(page));
        if (page === currentPage) {
          pageBtn.setAttribute('aria-current', 'page');
        }
        pageList.appendChild(pageBtn);
      }
    }

    function applyShopFilters() {
      var searchTerm = normalize(searchInput.value);
      var selectedRoasts = readCheckedValues('[data-filter-roast]');
      var selectedOrigins = readCheckedValues('[data-filter-origin]');
      var minPrice = Number(minPriceInput.value);
      var maxPrice = Number(maxPriceInput.value);

      var filteredItems = itemNodes.filter(function (item) {
        var name = normalize(item.getAttribute('data-name'));
        var roast = normalize(item.getAttribute('data-roast'));
        var origin = normalize(item.getAttribute('data-origin'));
        var tags = normalize(item.getAttribute('data-tags'));
        var price = Number(item.getAttribute('data-price') || 0);

        var matchesSearch = searchTerm === '' ||
          name.indexOf(searchTerm) !== -1 ||
          origin.indexOf(searchTerm) !== -1 ||
          tags.indexOf(searchTerm) !== -1;
        var matchesRoast = selectedRoasts.length === 0 || selectedRoasts.indexOf(roast) !== -1;
        var matchesOrigin = selectedOrigins.length === 0 || selectedOrigins.indexOf(origin) !== -1;
        var matchesPrice = price >= minPrice && price <= maxPrice;

        return matchesSearch && matchesRoast && matchesOrigin && matchesPrice;
      });

      var totalResults = filteredItems.length;
      var totalPages = totalResults > 0 ? Math.ceil(totalResults / pageSize) : 1;
      if (state.currentPage > totalPages) {
        state.currentPage = totalPages;
      }
      if (state.currentPage < 1) {
        state.currentPage = 1;
      }

      var startIndex = (state.currentPage - 1) * pageSize;
      var endIndex = startIndex + pageSize;
      var visibleItems = filteredItems.slice(startIndex, endIndex);
      var visibleSet = new Set(visibleItems);

      itemNodes.forEach(function (item) {
        item.hidden = !visibleSet.has(item);
      });

      if (totalResults === 0) {
        resultsCount.textContent = 'No coffees match your current filters.';
      } else {
        var from = startIndex + 1;
        var to = Math.min(startIndex + visibleItems.length, totalResults);
        resultsCount.textContent = 'Showing ' + from + '-' + to + ' of ' + totalResults + ' coffees';
      }

      pagination.hidden = totalResults <= pageSize;
      prevBtn.disabled = totalResults === 0 || state.currentPage === 1;
      nextBtn.disabled = totalResults === 0 || state.currentPage === totalPages;
      renderPageButtons(state.currentPage, totalPages);
    }

    function resetFilters() {
      searchInput.value = '';
      filtersForm.querySelectorAll('[data-filter-roast], [data-filter-origin]').forEach(function (checkbox) {
        checkbox.checked = false;
      });

      minPriceInput.value = minPriceInput.getAttribute('min');
      maxPriceInput.value = maxPriceInput.getAttribute('max');
      state.currentPage = 1;
      updatePriceOutput();
      applyShopFilters();
    }

    function handleRangeChange(source) {
      var minValue = Number(minPriceInput.value);
      var maxValue = Number(maxPriceInput.value);

      if (minValue > maxValue) {
        if (source === 'min') {
          maxPriceInput.value = String(minValue);
        } else {
          minPriceInput.value = String(maxValue);
        }
      }

      state.currentPage = 1;
      updatePriceOutput();
      applyShopFilters();
    }

    filtersForm.addEventListener('input', function (event) {
      var target = event.target;
      if (target === minPriceInput) {
        handleRangeChange('min');
        return;
      }
      if (target === maxPriceInput) {
        handleRangeChange('max');
        return;
      }
      state.currentPage = 1;
      applyShopFilters();
    });

    filtersForm.addEventListener('change', function () {
      state.currentPage = 1;
      applyShopFilters();
    });

    resetBtn.addEventListener('click', resetFilters);

    pagination.addEventListener('click', function (event) {
      var pageBtn = event.target.closest('button[data-page]');
      if (pageBtn) {
        state.currentPage = Number(pageBtn.getAttribute('data-page') || 1);
        applyShopFilters();
        return;
      }

      if (event.target === prevBtn && !prevBtn.disabled) {
        state.currentPage -= 1;
        applyShopFilters();
        return;
      }

      if (event.target === nextBtn && !nextBtn.disabled) {
        state.currentPage += 1;
        applyShopFilters();
      }
    });

    updatePriceOutput();
    applyShopFilters();
  }());

  renderCart(Object.values(loadCart()));
}());


