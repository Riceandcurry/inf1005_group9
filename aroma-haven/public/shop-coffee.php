<?php
require_once __DIR__ . '/../backend/init.php';
$pageTitle = 'Aroma Haven | Shop Coffee';
$bodyClass = 'ah-shop-page';

require_once __DIR__ . '/../includes/bean-catalog.php';
$shopBeans = array_values(ah_get_bean_catalog());

$roastOptions = [];
$originOptions = [];
$priceValues = [];

foreach ($shopBeans as $bean) {
  $roast = trim((string)($bean['roast'] ?? ''));
  $origin = trim((string)($bean['origin'] ?? ''));
  $priceRaw = (string)($bean['price'] ?? '0');
  $price = (float) preg_replace('/[^0-9.]/', '', $priceRaw);

  if ($roast !== '') {
    $roastOptions[$roast] = true;
  }
  if ($origin !== '') {
    $originOptions[$origin] = true;
  }
  $priceValues[] = $price;
}

$roastOptions = array_keys($roastOptions);
sort($roastOptions, SORT_NATURAL | SORT_FLAG_CASE);

$originOptions = array_keys($originOptions);
sort($originOptions, SORT_NATURAL | SORT_FLAG_CASE);

$minPrice = (int) floor((float) min($priceValues));
$maxPrice = (int) ceil((float) max($priceValues));
$defaultPageSize = 6;

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-shop-main" data-shop-page data-default-page-size="<?php echo (int) $defaultPageSize; ?>">
  <section class="container-fluid px-0">
    <header class="ah-hero">
      <img src="images/assets/banner-cafe.jpg" alt="A warm cafe table with coffee and pastries" class="ah-hero-image">
      <div class="ah-hero-overlay"></div>
      <div class="container ah-hero-content">
        <p class="ah-hero-kicker mb-2">Freshly Roasted</p>
        <h1 class="ah-hero-title mb-3">Find the coffee that matches your taste in minutes.</h1>
        <p class="ah-hero-lead mb-4">Filter by roast, origin, and price, then open product detail pages for full notes and brew guidance.</p>
      </div>
    </header>

    <div class="container ah-shop-content">
      <div class="row g-4 g-xl-5">
        <aside class="col-12 col-xl-5 col-xxl-4">
          <form class="ah-shop-filters" id="ahShopFilters" aria-labelledby="ahShopFilterHeading">
            <div class="ah-shop-filters-head">
              <h2 class="ah-shop-filters-title mb-1" id="ahShopFilterHeading">Refine your selection</h2>
              <p class="ah-shop-filters-help mb-0">Results update instantly as you adjust filters.</p>
            </div>

            <div class="ah-shop-filter-group">
              <label class="form-label" for="ahShopSearchInput">Search coffee</label>
              <input
                type="search"
                id="ahShopSearchInput"
                class="form-control ah-shop-input"
                placeholder="Name, origin, or flavor note"
                autocomplete="off"
              >
            </div>

            <fieldset class="ah-shop-filter-group">
              <legend class="ah-shop-legend">Roast</legend>
              <div class="ah-shop-checklist">
                <?php foreach ($roastOptions as $roast): ?>
                  <?php $id = 'ahRoast' . md5($roast); ?>
                  <div class="form-check">
                    <input class="form-check-input ah-shop-check-input" type="checkbox" value="<?php echo htmlspecialchars($roast, ENT_QUOTES, 'UTF-8'); ?>" id="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" data-filter-roast>
                    <label class="form-check-label ah-shop-check-label" for="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($roast, ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            </fieldset>

            <fieldset class="ah-shop-filter-group">
              <legend class="ah-shop-legend">Origin</legend>
              <div class="ah-shop-checklist">
                <?php foreach ($originOptions as $origin): ?>
                  <?php $id = 'ahOrigin' . md5($origin); ?>
                  <div class="form-check">
                    <input class="form-check-input ah-shop-check-input" type="checkbox" value="<?php echo htmlspecialchars($origin, ENT_QUOTES, 'UTF-8'); ?>" id="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" data-filter-origin>
                    <label class="form-check-label ah-shop-check-label" for="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($origin, ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            </fieldset>

            <fieldset class="ah-shop-filter-group">
              <legend class="ah-shop-legend">Price range</legend>
              <div class="ah-shop-range-wrap">
                <label for="ahShopMinPrice" class="ah-shop-range-label">Minimum price</label>
                <input type="range" class="form-range ah-shop-range" id="ahShopMinPrice" min="<?php echo (int) $minPrice; ?>" max="<?php echo (int) $maxPrice; ?>" value="<?php echo (int) $minPrice; ?>" step="1">
              </div>
              <div class="ah-shop-range-wrap">
                <label for="ahShopMaxPrice" class="ah-shop-range-label">Maximum price</label>
                <input type="range" class="form-range ah-shop-range" id="ahShopMaxPrice" min="<?php echo (int) $minPrice; ?>" max="<?php echo (int) $maxPrice; ?>" value="<?php echo (int) $maxPrice; ?>" step="1">
              </div>
              <p class="ah-shop-price-output mb-0" id="ahShopPriceOutput" aria-live="polite">$<?php echo (int) $minPrice; ?> - $<?php echo (int) $maxPrice; ?></p>
            </fieldset>

            <button type="button" class="btn ah-shop-reset-btn" id="ahShopResetFilters">Reset all filters</button>
            <noscript><p class="ah-shop-noscript mb-0">JavaScript is disabled. You can still browse all products and open detail pages.</p></noscript>
          </form>
        </aside>

        <section class="col-12 col-xl-7 col-xxl-8" aria-labelledby="ahShopResultsTitle">
          <div class="ah-shop-results-head">
            <h2 id="ahShopResultsTitle" class="ah-shop-results-title mb-0">Coffee collection</h2>
            <p class="ah-shop-results-count mb-0" id="ahShopResultsCount" aria-live="polite">
              Showing <?php echo (int) count($shopBeans); ?> coffees
            </p>
          </div>

          <div class="row g-4" id="ahShopGrid">
            <?php foreach ($shopBeans as $bean): ?>
              <?php
              $beanId = isset($bean['id']) ? (int) $bean['id'] : 0;
              $beanName = $bean['name'] ?? 'Bean name';
              $beanOrigin = $bean['origin'] ?? 'Bean origin';
              $beanPrice = $bean['price'] ?? '$0';
              $beanPriceNumeric = $bean['price_raw'] ?? (float) preg_replace('/[^0-9.]/', '', (string) $beanPrice);
              $beanTags = array_slice((array)($bean['tags'] ?? []), 0, 3);
              $productHref = $beanId > 0
                ? 'coffee-product.php?bean=' . $beanId
                : 'coffee-product.php';
              $bean['cta_label'] = 'Open detail';
              $bean['cta_href'] = $productHref;
              ?>
              <article
                class="col-12 col-md-6 ah-shop-item"
                data-name="<?php echo htmlspecialchars(strtolower($beanName), ENT_QUOTES, 'UTF-8'); ?>"
                data-origin="<?php echo htmlspecialchars(strtolower($beanOrigin), ENT_QUOTES, 'UTF-8'); ?>"
                data-roast="<?php echo htmlspecialchars(strtolower((string)($bean['roast'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"
                data-tags="<?php echo htmlspecialchars(strtolower(implode(' ', $beanTags)), ENT_QUOTES, 'UTF-8'); ?>"
                data-price="<?php echo htmlspecialchars((string) $beanPriceNumeric, ENT_QUOTES, 'UTF-8'); ?>"
              >
                <?php include __DIR__ . '/../includes/bean-card.php'; ?>
              </article>
            <?php endforeach; ?>
          </div>

          <nav class="ah-shop-pagination" id="ahShopPagination" aria-label="Shop page pagination" hidden>
            <button type="button" class="ah-shop-page-btn" id="ahShopPrevPage" aria-label="Go to previous page" aria-controls="ahShopGrid">Previous</button>
            <div class="ah-shop-page-list" id="ahShopPageList"></div>
            <button type="button" class="ah-shop-page-btn" id="ahShopNextPage" aria-label="Go to next page" aria-controls="ahShopGrid">Next</button>
          </nav>
        </section>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
