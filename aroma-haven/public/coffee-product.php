<?php
$pageTitle = 'Aroma Haven | Coffee Product';
$bodyClass = 'ah-shell-xr';

require_once __DIR__ . '/../backend/init.php';
require_once __DIR__ . '/../includes/bean-catalog.php';

$beanId = isset($_GET['bean']) ? (int) $_GET['bean'] : 0;
$bean = $beanId > 0 ? ah_get_bean_by_id($beanId) : null;

if ($bean !== null) {
  $pageTitle = 'Aroma Haven | ' . $bean['name'];
}

$grindOptions = ['Whole bean', 'French Press', 'Filter Drip', 'Espresso'];

// Related beans — all active products except current
$relatedBeans = [];
$reviews = [];
$reviewCount = 0;
$avgRating = 0;
$userHasReviewed = false;
$userExistingReview = null;

if ($bean !== null) {
  $conn = connect_db();
  $stmt = $conn->prepare(
    "SELECT * FROM products WHERE is_active = 1 AND id != ? ORDER BY id ASC LIMIT 3"
  );
  $stmt->execute([$bean['id']]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($rows as $row) {
    $relatedBeans[] = ah_row_to_bean($row);
  }

  // Fetch reviews
  $stmt = $conn->prepare(
    "SELECT r.id, r.rating, r.body, r.created_at,
            COALESCE(CONCAT(p.fname, ' ', p.lname), 'Anonymous') AS reviewer_name
     FROM product_reviews r
     LEFT JOIN user_profiles p ON p.user_id = r.user_id
     WHERE r.product_id = ?
     ORDER BY r.created_at DESC"
  );
  $stmt->execute([$bean['id']]);
  $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $reviewCount = count($reviews);
  if ($reviewCount > 0) {
    $avgRating = round(array_sum(array_column($reviews, 'rating')) / $reviewCount, 1);
  }

  // Fetch logged-in user's existing review (if any)
  if ($auth->isLogged()) {
    $stmt = $conn->prepare(
      "SELECT id, rating, body FROM product_reviews WHERE product_id = ? AND user_id = ? LIMIT 1"
    );
    $stmt->execute([$bean['id'], (int) $auth->getCurrentUID()]);
    $userExistingReview = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    $userHasReviewed = $userExistingReview !== null;
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
                    <?php
                    $fullStars  = $reviewCount > 0 ? (int) round($avgRating) : 0;
                    $emptyStars = 5 - $fullStars;
                    echo str_repeat('<span>&#9733;</span>', $fullStars);
                    echo str_repeat('<span style="opacity:.3">&#9733;</span>', $emptyStars);
                    ?>
                  </div>
                  <span class="ah-coffee-review-count"><?php echo $reviewCount; ?> review<?php echo $reviewCount !== 1 ? 's' : ''; ?></span>
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
                  data-id="<?php echo htmlspecialchars((string)$bean['id'], ENT_QUOTES, 'UTF-8'); ?>"
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

    <?php if ($bean !== null): ?>
      <?php $flashMsg = $_SESSION['msg'] ?? ''; $flashErr = $_SESSION['error'] ?? ''; unset($_SESSION['msg'], $_SESSION['error']); ?>
      <?php if ($flashMsg): ?>
        <div class="alert alert-success mb-4"><?php echo htmlspecialchars($flashMsg, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>
      <?php if ($flashErr): ?>
        <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($flashErr, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>

      <?php if ($auth->isLogged()): ?>
        <div class="card border mb-5 p-4">
          <h3 class="h5 mb-3"><?php echo $userHasReviewed ? 'Edit Your Review' : 'Leave a Review'; ?></h3>
          <form method="POST" action="route.php">
            <input type="hidden" name="action" value="<?php echo $userHasReviewed ? 'update_review' : 'submit_review'; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="product_id" value="<?php echo (int) $bean['id']; ?>">
            <div class="mb-3">
              <label class="form-label fw-semibold">Rating</label>
              <div class="d-flex gap-2">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>"
                      <?php echo ($userExistingReview && (int)$userExistingReview['rating'] === $i) ? 'checked' : ''; ?> required>
                    <label class="form-check-label" for="star<?php echo $i; ?>"><?php echo $i; ?>&#9733;</label>
                  </div>
                <?php endfor; ?>
              </div>
            </div>
            <div class="mb-3">
              <label for="reviewBody" class="form-label fw-semibold">Your Review</label>
              <textarea class="form-control" id="reviewBody" name="body" rows="3" maxlength="1000" minlength="5" required placeholder="Share your experience with this coffee..."><?php echo $userExistingReview ? htmlspecialchars($userExistingReview['body'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $userHasReviewed ? 'Update Review' : 'Submit Review'; ?></button>
          </form>
        </div>
      <?php else: ?>
        <p class="mb-4 text-muted"><a href="login.php">Log in</a> to leave a review.</p>
      <?php endif; ?>

      <div class="row g-4">
        <?php if (empty($reviews)): ?>
          <div class="col-12 text-center text-muted py-4">
            <p class="mb-0">No reviews yet. Be the first to share your experience.</p>
          </div>
        <?php else: ?>
          <?php foreach ($reviews as $review): ?>
            <div class="col-12 col-md-6">
              <div class="card border p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="fw-semibold"><?php echo htmlspecialchars($review['reviewer_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                  <span class="text-muted small"><?php echo htmlspecialchars(date('d M Y', strtotime($review['created_at'])), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="mb-2" aria-label="Rating: <?php echo (int) $review['rating']; ?> out of 5">
                  <?php
                  echo str_repeat('&#9733;', (int) $review['rating']);
                  echo str_repeat('<span style="opacity:.3">&#9733;</span>', 5 - (int) $review['rating']);
                  ?>
                </div>
                <p class="mb-0"><?php echo htmlspecialchars($review['body'], ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
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
            $relatedProductHref = 'coffee-product.php?bean=' . (int)$relatedBeanCard['id'];
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
