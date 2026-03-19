<?php
$pageTitle = 'Aroma Haven | Brew Guides';

$brewMethods = [
  [
    'slug' => 'espresso-long-pull',
    'title' => 'Espresso, long pull',
    'subtitle' => 'Brighter espresso profile',
    'image' => './images/assets/auth-visual.jpg',
    'profile' => 'Fruity, Floral Espresso',
    'duration' => '3 MINUTES',
    'href' => '#',
  ],
  [
    'slug' => 'espresso-short-pull',
    'title' => 'Espresso, short pull',
    'subtitle' => 'Short, concentrated shot',
    'image' => './images/assets/banner-cafe.jpg',
    'profile' => 'Classic Italian-Style Espresso',
    'duration' => '3 MINUTES',
    'href' => '#',
  ],
  [
    'slug' => 'pour-over',
    'title' => 'Pour over',
    'subtitle' => 'Iconic cafe brew method',
    'image' => './images/assets/pour_over_brew.jpg',
    'profile' => 'Hand-Crafted Cup of Coffee',
    'duration' => '2:30-3:30 MIN',
    'href' => '#',
  ],
  [
    'slug' => 'cold-bottle-brew',
    'title' => 'Cold bottle brew',
    'subtitle' => 'Slow steep for a crisp finish',
    'image' => './images/assets/brew_guide_banner.jpg',
    'profile' => 'Low-Acid Cold Extraction',
    'duration' => '12-18 HRS',
    'href' => '#',
  ],
  [
    'slug' => 'milk-steaming',
    'title' => 'Milk steaming',
    'subtitle' => 'Silky microfoam for milk coffee',
    'image' => './images/assets/french_press_brew.jpg',
    'profile' => 'Velvety Milk Texture',
    'duration' => '1-2 MIN',
    'href' => '#',
  ],
  [
    'slug' => 'coffee-maker',
    'title' => 'Coffee maker',
    'subtitle' => 'Reliable daily brewing',
    'image' => './images/assets/banner-cafe.jpg',
    'profile' => 'Balanced Everyday Cup',
    'duration' => '4-6 MIN',
    'href' => '#',
  ],
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-brew-methods-page py-5 py-lg-6">
  <section class="container">
    <header class="ah-brew-header text-center mb-4 mb-lg-5">
      <p class="ah-brew-header-kicker mb-2">BREW GUIDES</p>
      <h1 class="ah-brew-header-title mb-0">Explore step-by-step guides to brew your perfect coffee</h1>
    </header>

    <div class="row g-4 g-xl-5">
      <?php foreach ($brewMethods as $method): ?>
        <div class="col-12 col-md-6 col-xl-4">
          <a
            href="<?php echo htmlspecialchars($method['href'], ENT_QUOTES, 'UTF-8'); ?>"
            class="ah-method-card-link"
            data-method="<?php echo htmlspecialchars($method['slug'], ENT_QUOTES, 'UTF-8'); ?>"
            aria-label="Open <?php echo htmlspecialchars($method['title'], ENT_QUOTES, 'UTF-8'); ?> guide"
          >
            <article class="ah-method-card h-100">
              <h2 class="ah-method-title"><?php echo htmlspecialchars($method['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
              <p class="ah-method-subtitle"><?php echo htmlspecialchars($method['subtitle'], ENT_QUOTES, 'UTF-8'); ?></p>
              <img
                src="<?php echo htmlspecialchars($method['image'], ENT_QUOTES, 'UTF-8'); ?>"
                alt="<?php echo htmlspecialchars($method['title'], ENT_QUOTES, 'UTF-8'); ?>"
                class="ah-method-image"
              >
              <p class="ah-method-profile"><?php echo htmlspecialchars($method['profile'], ENT_QUOTES, 'UTF-8'); ?></p>
              <p class="ah-method-duration mb-0"><?php echo htmlspecialchars($method['duration'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
