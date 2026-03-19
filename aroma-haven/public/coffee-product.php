<?php
$pageTitle = 'Aroma Haven | Coffee Product';

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

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-coffee-product-page">
  <section class="container ah-coffee-breadcrumb-wrap">
    <a href="shop-coffee.php" class="ah-coffee-breadcrumb">&larr; Back to Shop | Speciality Coffee<?php if ($bean !== null): ?> / <?php echo htmlspecialchars($bean['name'], ENT_QUOTES, 'UTF-8'); ?><?php endif; ?></a>
  </section>

  <section class="container ah-coffee-bean-section">
    <?php if ($bean === null): ?>
      <div class="alert alert-light border text-center p-4" role="alert">
        <h1 class="h3 mb-2">Bean not found</h1>
        <p class="mb-3">Please select a bean from our coffee collection.</p>
        <a href="shop-coffee.php" class="btn btn-primary">Back to Shop</a>
      </div>
    <?php else: ?>
      <div class="row gx-lg-5 gy-4 align-items-start ah-coffee-bean-row mx-auto">
        <div class="col-12 col-lg-6 d-flex justify-content-center">
          <div class="ah-coffee-image-wrap">
            <img src="<?php echo htmlspecialchars($bean['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($bean['name'], ENT_QUOTES, 'UTF-8'); ?>" class="ah-coffee-image">
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="ah-coffee-details">
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
                <span class="ah-coffee-review-count">(42 reviews)</span>
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
              <p class="text-overline mb-2">Grindsize</p>
              <div class="ah-grind-grid">
                <?php foreach ($grindOptions as $option): ?>
                  <button type="button" class="ah-grind-option<?php echo $option === 'Whole bean' ? ' is-active' : ''; ?>"><?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?></button>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="d-flex align-items-stretch gap-3 ah-coffee-purchase-row">
              <div class="ah-qty-pill" role="group" aria-label="Quantity">
                <button type="button" aria-label="Decrease quantity">-</button>
                <span>1</span>
                <button type="button" aria-label="Increase quantity">+</button>
              </div>
              <a href="cart.php" class="btn btn-primary ah-coffee-add-btn">Add to Cart</a>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <section class="container ah-coffee-reviews-section">
    <h2 class="ah-coffee-reviews-title mb-4">What Our Customers Are Saying?</h2>
    <div class="row g-4">
      <?php foreach ($reviews as $review): ?>
        <div class="col-12 col-md-6 col-xl-4">
          <article class="ah-review-card h-100">
            <p class="ah-review-product mb-2"><?php echo htmlspecialchars($review['title'], ENT_QUOTES, 'UTF-8'); ?></p>
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
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

