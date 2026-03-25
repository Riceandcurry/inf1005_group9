<?php
$pageTitle = 'Aroma Haven | Coffee Product';
$bodyClass = 'ah-shell-xr';

require_once __DIR__ . '/../includes/bean-catalog.php';
$catalog = ah_get_bean_catalog();
$beanId = isset($_GET['bean']) ? (string) $_GET['bean'] : '';
$bean = $catalog[$beanId] ?? null;

if ($bean !== null) {
  $pageTitle = 'Aroma Haven | ' . $bean['name'];
}

$reviews = [
  [
    'title' => 'Product name',
    'copy' => 'As already said, the Three-Pack was gifted to me this past Christmas. I love coffee and for me iced coffee is the most flavorful. After much enjoyment the Three-Pack from Christmas...',
    'author' => 'Jane D. Verified Buyer',
    'date' => '10/10/19999',
  ],
  [
    'title' => 'Product name',
    'copy' => 'As already said, the Three-Pack was gifted to me this past Christmas. I love coffee and for me iced coffee is the most flavorful. After much enjoyment the Three-Pack from Christmas...',
    'author' => 'Jane D. Verified Buyer',
    'date' => '10/10/19999',
  ],
  [
    'title' => 'Product name',
    'copy' => 'As already said, the Three-Pack was gifted to me this past Christmas. I love coffee and for me iced coffee is the most flavorful. After much enjoyment the Three-Pack from Christmas...',
    'author' => 'Jane D. Verified Buyer',
    'date' => '10/10/19999',
  ],
];

$grindOptions = ['Whole bean', 'French Press', 'Filter Drip', 'Espresso'];
$relatedBeans = [];

foreach ($catalog as $catalogBean) {
  if ($bean !== null && ($catalogBean['id'] ?? '') === ($bean['id'] ?? '')) {
    continue;
  }
  $relatedBeans[] = $catalogBean;
  if (count($relatedBeans) >= 3) {
    break;
  }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-coffee-product-page">
  <section class="container ah-coffee-breadcrumb-wrap">
    <a href="shop-coffee.php" class="ah-coffee-breadcrumb">&larr; Back to Shop</a>
  </section>

  <section class="container ah-coffee-bean-section">
    <?php if ($bean === null): ?>
      <div class="alert alert-light border text-center p-4 ah-coffee-missing" role="alert">
        <h1 class="h3 mb-2">Bean not found</h1>
        <p class="mb-3">Please select a bean from our coffee collection.</p>
        <a href="shop-coffee.php" class="btn btn-primary">Back to Shop</a>
      </div>
    <?php else: ?>
      <div class="row gx-lg-4 gy-4 align-items-start ah-coffee-top mx-auto">
        <div class="col-12 col-lg-6 d-flex justify-content-center">
          <article class="ah-coffee-media-card w-100">
            <div class="ah-coffee-image-wrap">
              <img src="<?php echo htmlspecialchars($bean['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($bean['name'], ENT_QUOTES, 'UTF-8'); ?>" class="ah-coffee-image">
            </div>
          </article>
        </div>

        <div class="col-12 col-lg-6">
          <article class="ah-coffee-details-card">
            <div class="ah-coffee-details">
              <p class="ah-coffee-kicker mb-0">Single Origin Coffee</p>
              <span class="badge badge-sage ah-coffee-roast"><?php echo htmlspecialchars($bean['roast'] ?? 'Light roast', ENT_QUOTES, 'UTF-8'); ?></span>

              <div>
                <h1 class="ah-coffee-title mb-1"><?php echo htmlspecialchars($bean['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="ah-coffee-origin mb-0"><?php echo htmlspecialchars($bean['origin'], ENT_QUOTES, 'UTF-8'); ?></p>
              </div>

              <div class="d-flex flex-wrap align-items-center gap-3">
                <p class="ah-coffee-price mb-0"><?php echo htmlspecialchars($bean['price'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="d-flex align-items-center gap-2">
                  <div class="ah-coffee-stars" aria-hidden="true">
                    <span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
                  </div>
                  <span class="ah-coffee-review-count">42 reviews</span>
                </div>
              </div>

              <div class="ah-coffee-facts" aria-label="Bean details">
                <?php if (!empty($bean['process'])): ?>
                  <div class="ah-coffee-fact">
                    <p class="text-overline mb-1">Process</p>
                    <p class="mb-0"><?php echo htmlspecialchars($bean['process'], ENT_QUOTES, 'UTF-8'); ?></p>
                  </div>
                <?php endif; ?>
                <?php if (!empty($bean['altitude'])): ?>
                  <div class="ah-coffee-fact">
                    <p class="text-overline mb-1">Altitude</p>
                    <p class="mb-0"><?php echo htmlspecialchars($bean['altitude'], ENT_QUOTES, 'UTF-8'); ?></p>
                  </div>
                <?php endif; ?>
                <div class="ah-coffee-fact">
                  <p class="text-overline mb-1">Best For</p>
                  <p class="mb-0">Filter and Espresso</p>
                </div>
              </div>

              <div>
                <p class="text-overline mb-2">Tasting Notes</p>
                <div class="d-flex flex-wrap gap-2">
                  <?php foreach (array_slice((array) ($bean['tags'] ?? []), 0, 3) as $tag): ?>
                    <span class="ah-flavour-tag"><?php echo htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php endforeach; ?>
                </div>
              </div>

              <p class="ah-coffee-description mb-0"><?php echo htmlspecialchars($bean['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

              <div>
                <p class="text-overline mb-2">Grind Size</p>
                <div class="ah-grind-grid" role="group" aria-label="Select grind size">
                  <?php foreach ($grindOptions as $option): ?>
                    <button type="button" class="ah-grind-option<?php echo $option === 'Whole bean' ? ' is-active' : ''; ?>" aria-pressed="<?php echo $option === 'Whole bean' ? 'true' : 'false'; ?>"><?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?></button>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="d-flex align-items-stretch gap-3 ah-coffee-purchase-row">
                <div class="ah-qty-pill" role="group" aria-label="Quantity">
                  <button type="button" id="ahProductQtyDec" aria-label="Decrease quantity">-</button>
                  <span id="ahProductQtyNum">1</span>
                  <button type="button" id="ahProductQtyInc" aria-label="Increase quantity">+</button>
                </div>
                <button type="button"
                  class="btn btn-primary ah-coffee-add-btn"
                  data-id="<?php echo htmlspecialchars($bean['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-name="<?php echo htmlspecialchars($bean['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-origin="<?php echo htmlspecialchars($bean['origin'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-price="<?php echo htmlspecialchars($bean['price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-image="<?php echo htmlspecialchars($bean['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-roast="<?php echo htmlspecialchars($bean['roast'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-tags="<?php echo htmlspecialchars(implode(',', (array)($bean['tags'] ?? [])), ENT_QUOTES, 'UTF-8'); ?>"
                  aria-label="Add <?php echo htmlspecialchars($bean['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> to cart"
                >Add to Cart</button>
              </div>

              <p class="ah-coffee-helper mb-0">Freshly roasted each week. Ships in 1-2 working days.</p>
            </div>
          </article>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <section class="container ah-coffee-reviews-section">
    <header class="ah-coffee-section-head mb-4 mb-lg-5">
      <p class="ah-coffee-kicker mb-2">Community Notes</p>
      <h2 class="ah-coffee-reviews-title mb-0">What Coffee Drinkers Are Saying</h2>
    </header>
    <div class="row g-4">
      <?php foreach ($reviews as $review): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <article class="ah-review-card h-100">
            <p class="ah-review-product mb-2"><?php echo htmlspecialchars($bean['name'] ?? $review['title'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="ah-review-copy mb-3"><?php echo htmlspecialchars($review['copy'], ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="d-flex justify-content-between align-items-center gap-3 mt-auto">
              <p class="ah-review-author mb-0"><?php echo htmlspecialchars($review['author'], ENT_QUOTES, 'UTF-8'); ?></p>
              <p class="ah-review-date mb-0"><?php echo htmlspecialchars($review['date'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php if (!empty($relatedBeans)): ?>
    <section class="container ah-coffee-related-section">
      <header class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4 mb-lg-5">
        <div>
          <p class="ah-coffee-kicker mb-2">You May Also Like</p>
          <h2 class="ah-coffee-reviews-title mb-0">Explore More Beans</h2>
        </div>
        <a href="shop-coffee.php" class="btn btn-outline-primary">View Full Collection</a>
      </header>
      <div class="row g-4">
        <?php $currentBean = $bean; ?>
        <?php foreach ($relatedBeans as $relatedBean): ?>
          <div class="col-12 col-md-6 col-lg-4">
            <?php
            $relatedBeanCard = $relatedBean;
            $relatedBeanId = isset($relatedBeanCard['id']) ? (string) $relatedBeanCard['id'] : '';
            $relatedProductHref = $relatedBeanId !== ''
              ? 'coffee-product.php?bean=' . rawurlencode($relatedBeanId)
              : 'coffee-product.php';
            $relatedBeanCard['cta_label'] = 'Open detail';
            $relatedBeanCard['cta_href'] = $relatedProductHref;
            $bean = $relatedBeanCard;
            include __DIR__ . '/../includes/bean-card.php';
            $bean = $currentBean;
            ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
