<?php
$pageTitle = 'Aroma Haven | Shop Coffee';

require_once __DIR__ . '/../includes/bean-catalog.php';
$shopBeans = array_values(ah_get_bean_catalog());

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-shop-main py-5 py-lg-6">
  <section class="container">
    <div class="ah-shop-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 mb-lg-5">
      <h1 class="ah-shop-title mb-0">Speciality Coffee</h1>
      <button type="button" class="ah-filter-btn" aria-label="Filter coffee beans">Filter</button>
    </div>

    <div class="row g-4">
      <?php foreach ($shopBeans as $bean): ?>
        <div class="col-12 col-md-6 col-xl-4">
          <?php
          $bean['cta_label'] = '+ Add';
          include __DIR__ . '/../includes/bean-card.php';
          ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
