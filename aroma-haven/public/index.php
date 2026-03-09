<?php
$pageTitle = 'Aroma Haven | Home';
$bestSellerCards = [
  [
    'name' => 'Bean name',
    'origin' => 'Bean origin',
    'price' => '$?',
    'roast' => 'Light roast',
    'image' => './images/products/product_1.jpg'
  ],
  [
    'name' => 'Bean name',
    'origin' => 'Bean origin',
    'price' => '$?',
    'roast' => 'Light roast',
    'image' => './images/products/product_1.jpg'
  ],
  [
    'name' => 'Bean name',
    'origin' => 'Bean origin',
    'price' => '$?',
    'roast' => 'Light roast',
    'image' => './images/products/product_1.jpg'
  ],
];

$featureTags = [
  ['label' => 'Meticulous Quality', 'icon' => './images/assets/achievement.png'],
  ['label' => 'Unpretentious', 'icon' => './images/assets/theatre.png'],
  ['label' => 'Ethically Sourced', 'icon' => './images/assets/tag.png'],
  ['label' => 'By coffee lovers', 'icon' => './images/assets/love.png'],
];

$brewGuides = [
  [
    'title' => 'French Press',
    'text' => 'Forgiving, rich, and beginner-friendly. Great first method.',
    'image' => './images/assets/french_press_brew.jpg'
  ],
  [
    'title' => 'Pour Over',
    'text' => 'Clean and sweet cup with easy timing prompts',
    'image' => './images/assets/pour_over_brew.jpg'
  ],
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main>
  <section class="ah-hero">
    <div class="container-fluid px-0">
      <div class="row g-0 align-items-stretch">
        <div class="col-lg-6">
          <video class="w-100 ah-hero-image" autoplay muted loop playsinline>
            <source src="images/assets/golden_brew.mp4" type="video/mp4">
            Your browser does not support the video tag.
          </video>
        </div>
        <div class="col-lg-6 d-flex align-items-center bg-oat">
          <div class="ah-hero-content mx-auto">
            <h1 class="ah-display mb-3 mb-md-4">Your 10 minute oasis.</h1>
            <p class="ah-lead mb-4 mb-md-5">We source the finest beans and meticulously roast them so you can slow down, breathe, and enjoy a perfect cup right in your kitchen. No snobbery. Just great coffee.</p>
            <a href="shop-coffee.php" class="btn btn-outline-primary d-table mx-auto">To Shop</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="ah-features bg-steamed">
    <div class="container">
      <div class="row justify-content-center g-4 g-md-5">
        <?php foreach ($featureTags as $tag): ?>
          <div class="col-6 col-md-3 text-center">
            <img src="<?php echo htmlspecialchars($tag['icon'], ENT_QUOTES, 'UTF-8'); ?>" alt="" class="ah-feature-icon mb-3">
            <p class="ah-feature-text mb-0"><?php echo htmlspecialchars($tag['label'], ENT_QUOTES, 'UTF-8'); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="ah-best-sellers py-5 py-lg-6">
    <div class="container">
      <h2 class="mb-4 mb-lg-5">New Arrivals and Best Sellers</h2>
      <div class="row g-4">
        <?php foreach ($bestSellerCards as $card): ?>
          <div class="col-12 col-md-6 col-xl-4">
            <article class="ah-bean-card h-100 position-relative">
              <span class="badge badge-sage ah-roast-badge"><?php echo htmlspecialchars($card['roast'], ENT_QUOTES, 'UTF-8'); ?></span>
              <img src="<?php echo htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($card['name'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid w-100 ah-bean-image">
              <div class="d-flex justify-content-between align-items-start mt-3">
                <div>
                  <h3 class="ah-card-title mb-1"><?php echo htmlspecialchars($card['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                  <p class="ah-card-subtitle mb-0"><?php echo htmlspecialchars($card['origin'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <p class="ah-card-price mb-0"><?php echo htmlspecialchars($card['price'], ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
              <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="ah-flavour-tag">Add</span>
                <span class="ah-flavour-tag">Add</span>
                <span class="ah-flavour-tag">Add</span>
              </div>
              <div class="d-flex justify-content-end mt-3">
                <a href="shop-coffee.php" class="btn btn-primary btn-sm ah-add-btn">+ Add</a>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-4 mt-lg-5">
        <a href="shop-coffee.php" class="btn btn-primary">To Shop</a>
      </div>
    </div>
  </section>

  <section class="ah-brew-guides bg-steamed py-5 py-lg-6">
    <div class="container">
      <h2 class="mb-4 mb-lg-5">Explore Brew Guides</h2>
      <div class="row g-4 g-lg-5">
        <?php foreach ($brewGuides as $guide): ?>
          <div class="col-12 col-lg-6">
            <article class="d-flex flex-column flex-md-row gap-3 ah-guide-card h-100">
              <img src="<?php echo htmlspecialchars($guide['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($guide['title'], ENT_QUOTES, 'UTF-8'); ?>" class="ah-guide-image">
              <div class="d-flex flex-column justify-content-center">
                <h3 class="ah-guide-title mb-2"><?php echo htmlspecialchars($guide['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="ah-guide-copy mb-2"><?php echo htmlspecialchars($guide['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                <a href="product.php" class="ah-guide-link">Learn More</a>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-4 mt-lg-5">
        <a href="product.php" class="btn btn-outline-dark ah-outline-square">Explore Brew Guides</a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
