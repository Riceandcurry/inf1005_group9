<?php
if (!isset($bean) || !is_array($bean)) {
    return;
}

$beanId = isset($bean['id']) ? (string) $bean['id'] : '';
$beanName = $bean['name'] ?? 'Bean name';
$beanOrigin = $bean['origin'] ?? 'Bean origin';
$beanPrice = $bean['price'] ?? '$?';
$beanImage = $bean['image'] ?? './images/products/product_1.jpg';
$beanRoast = $bean['roast'] ?? null;
$beanTags = $bean['tags'] ?? [];
$beanCtaLabel = $bean['cta_label'] ?? '+ Add';

$defaultProductHref = $beanId !== ''
    ? 'coffee-product.php?bean=' . rawurlencode($beanId)
    : 'coffee-product.php';
$defaultAddHref = 'cart.php';

$beanProductHref = $bean['product_href'] ?? $defaultProductHref;
$beanCtaHref = $bean['cta_href'] ?? $defaultAddHref;
?>
<article class="ah-bean-card h-100 position-relative">
  <a href="<?php echo htmlspecialchars($beanProductHref, ENT_QUOTES, 'UTF-8'); ?>" class="ah-bean-card-link d-block text-reset text-decoration-none">
    <?php if (!empty($beanRoast)): ?>
      <span class="badge badge-sage ah-roast-badge"><?php echo htmlspecialchars($beanRoast, ENT_QUOTES, 'UTF-8'); ?></span>
    <?php endif; ?>
    <img src="<?php echo htmlspecialchars($beanImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($beanName, ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid w-100 ah-bean-image">
    <div class="d-flex justify-content-between align-items-start mt-3">
      <div>
        <h3 class="ah-card-title mb-1"><?php echo htmlspecialchars($beanName, ENT_QUOTES, 'UTF-8'); ?></h3>
        <p class="ah-card-subtitle mb-0"><?php echo htmlspecialchars($beanOrigin, ENT_QUOTES, 'UTF-8'); ?></p>
      </div>
      <p class="ah-card-price mb-0"><?php echo htmlspecialchars($beanPrice, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <?php if (!empty($beanTags) && is_array($beanTags)): ?>
      <div class="d-flex flex-wrap gap-2 mt-3">
        <?php foreach ($beanTags as $tag): ?>
          <span class="ah-flavour-tag"><?php echo htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </a>
  <div class="d-flex justify-content-end mt-3 position-relative z-1">
    <button type="button"
      class="btn btn-primary btn-sm ah-add-btn ah-bean-add-btn"
      data-id="<?php echo htmlspecialchars($beanId, ENT_QUOTES, 'UTF-8'); ?>"
      data-name="<?php echo htmlspecialchars($beanName, ENT_QUOTES, 'UTF-8'); ?>"
      data-origin="<?php echo htmlspecialchars($beanOrigin, ENT_QUOTES, 'UTF-8'); ?>"
      data-price="<?php echo htmlspecialchars($beanPrice, ENT_QUOTES, 'UTF-8'); ?>"
      data-image="<?php echo htmlspecialchars($beanImage, ENT_QUOTES, 'UTF-8'); ?>"
      data-roast="<?php echo htmlspecialchars($beanRoast ?? '', ENT_QUOTES, 'UTF-8'); ?>"
      data-tags="<?php echo htmlspecialchars(implode(',', $beanTags), ENT_QUOTES, 'UTF-8'); ?>"
    ><?php echo htmlspecialchars($beanCtaLabel, ENT_QUOTES, 'UTF-8'); ?></button>
  </div>
</article>
