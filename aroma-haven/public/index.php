<?php
$pageTitle = 'Aroma Haven | Home';
$bodyClass = 'ah-shell-xr';

require_once __DIR__ . '/../includes/bean-catalog.php';
$featuredBeans = array_slice(array_values(ah_get_bean_catalog()), 0, 6);

$methodPillars = [
  ['title' => 'Source', 'copy' => 'Micro-lot relationships that prioritize traceability and seasonal quality.'],
  ['title' => 'Roast', 'copy' => 'Profiles tuned for clarity and sweetness, not noise and bitterness.'],
  ['title' => 'Guide', 'copy' => 'Simple, precise brew paths for beginners and enthusiasts alike.'],
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="ah-xr-home">
  <section id="chapter-arrival" class="ah-xr-chapter ah-xr-hero">
    <img src="./images/assets/banner-cafe.jpg" alt="" class="ah-xr-hero-fallback" aria-hidden="true">
    <video class="ah-xr-hero-video" autoplay muted loop playsinline aria-hidden="true">
      <source src="images/assets/golden_brew.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
    <div class="container position-relative">
      <div class="ah-xr-hero-content">
        <p class="ah-xr-kicker mb-2">Aroma Haven</p>
        <h1 class="ah-xr-title mb-3">Bring better coffee home, one intentional cup at a time.</h1>
        <p class="ah-xr-lead mb-4">Freshly roasted beans, clear brew guidance, and flavor profiles you can actually understand.</p>
        <div class="ah-xr-hero-actions d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
          <a href="shop-coffee.php" class="btn btn-primary">Shop Coffee</a>
          <a href="#chapter-problem" class="btn btn-outline-light ah-xr-btn-ghost">Learn The Ritual</a>
        </div>
      </div>
    </div>
  </section>

  <section id="chapter-problem" class="ah-xr-chapter ah-xr-problem">
    <div class="container">
      <div class="row g-4 g-lg-5 align-items-center">
        <div class="col-12 col-lg-6">
          <p class="ah-xr-kicker mb-2">Why It Matters</p>
          <h2 class="ah-xr-heading mb-3">Great beans lose their magic when brewing feels confusing.</h2>
          <p class="mb-4">Most people are forced to choose between confusing coffee jargon and bland convenience. We remove that trade-off so quality feels natural.</p>
          <ul class="ah-xr-list mb-0">
            <li>Clear flavor language instead of gatekeeping.</li>
            <li>Method guidance without unnecessary complexity.</li>
            <li>A direct path from curiosity to great coffee.</li>
          </ul>
        </div>
        <div class="col-12 col-lg-6">
          <img src="./images/assets/auth-visual.jpg" alt="Coffee ritual setup" class="ah-xr-problem-image">
        </div>
      </div>
    </div>
  </section>

  <section id="chapter-method" class="ah-xr-chapter ah-xr-method">
    <div class="container">
      <header class="mb-4 mb-lg-5">
        <p class="ah-xr-kicker mb-2">Our Method</p>
        <h2 class="ah-xr-heading mb-0">Three moves. One better cup.</h2>
      </header>
      <div class="row g-4">
        <?php foreach ($methodPillars as $pillar): ?>
          <div class="col-12 col-sm-6 col-lg-4">
            <article class="ah-xr-pillar h-100">
              <h3 class="mb-2"><?php echo htmlspecialchars($pillar['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <p class="mb-0"><?php echo htmlspecialchars($pillar['copy'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="chapter-discovery" class="ah-xr-chapter ah-xr-discovery">
    <div class="container">
      <header class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4 mb-lg-5">
        <div>
          <p class="ah-xr-kicker mb-2">Featured Coffee</p>
          <h2 class="ah-xr-heading mb-0">Choose a bean that matches your mood.</h2>
        </div>
        <a href="shop-coffee.php" class="btn btn-outline-primary">Browse Full Collection</a>
      </header>

      <div class="row g-4">
        <?php foreach ($featuredBeans as $bean): ?>
          <div class="col-12 col-md-6 col-lg-4">
            <?php
            $beanId = isset($bean['id']) ? (int) $bean['id'] : 0;
            $productHref = $beanId > 0
              ? 'coffee-product.php?bean=' . $beanId
              : 'coffee-product.php';
            $bean['cta_label'] = 'Open detail';
            $bean['cta_href'] = $productHref;
            include __DIR__ . '/../includes/bean-card.php';
            ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="chapter-quiz" class="ah-xr-chapter ah-xr-quiz">
    <div class="container">
      <article class="ah-xr-quiz-card text-center">
        <p class="ah-xr-kicker mb-2">Taste Match</p>
        <h2 class="ah-xr-heading mb-3">Not sure where to begin? We can map your flavor profile.</h2>
        <p class="mb-4">Take a quick personality quiz and get an instant coffee direction tailored to your preferences.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <a href="personality-quiz.php" class="btn btn-primary">Take Personality Quiz</a>
          <a href="shop-coffee.php" class="btn btn-outline-primary">Shop Directly</a>
        </div>
      </article>
    </div>
  </section>

  <section id="chapter-close" class="ah-xr-chapter ah-xr-close">
    <div class="container">
      <article class="ah-xr-close-card text-center">
        <p class="ah-xr-kicker mb-2">Start Your Ritual</p>
        <h2 class="ah-xr-heading mb-3">Build the ritual you want to wake up to.</h2>
        <p class="mb-4">Start with beans that fit your palate, then refine your method over time with confidence.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <a href="shop-coffee.php" class="btn btn-primary">Shop Coffee</a>
          <a href="register.php" class="btn btn-outline-primary">Join Oasis</a>
        </div>
      </article>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
