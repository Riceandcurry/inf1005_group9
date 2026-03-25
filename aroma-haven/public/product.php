<?php
$pageTitle = 'Aroma Haven | Brew Guides';
$bodyClass = 'ah-guides-page';

$brewMethods = [
  [
    'slug' => 'espresso-long-pull',
    'title' => 'Espresso, long pull',
    'subtitle' => 'Brighter profile with layered sweetness.',
    'image' => './images/assets/auth-visual.jpg',
    'profile' => 'Fruity and floral',
    'duration' => '3 min',
    'grind' => 'Fine',
    'ratio' => '1:2',
    'steps' => [
      'Dose evenly and level your basket before tamping with consistent pressure.',
      'Run a long pull until you reach a balanced texture with bright aromatics.',
      'Serve immediately and taste for sweetness before adjusting grind.',
    ],
  ],
  [
    'slug' => 'espresso-short-pull',
    'title' => 'Espresso, short pull',
    'subtitle' => 'Short and concentrated with syrupy body.',
    'image' => './images/assets/banner-cafe.jpg',
    'profile' => 'Classic Italian style',
    'duration' => '3 min',
    'grind' => 'Fine',
    'ratio' => '1:1.5',
    'steps' => [
      'Preheat the portafilter and cup to keep extraction stable.',
      'Extract a short pull and stop once body and crema look dense.',
      'Dial by taste, tightening grind for more body or opening for balance.',
    ],
  ],
  [
    'slug' => 'pour-over',
    'title' => 'Pour over',
    'subtitle' => 'Clean, articulate cup with precision pouring.',
    'image' => './images/assets/pour_over_brew.jpg',
    'profile' => 'Crisp and transparent',
    'duration' => '2:30-3:30',
    'grind' => 'Medium',
    'ratio' => '1:16',
    'steps' => [
      'Bloom with a small pour for 30-40 seconds to release trapped gas.',
      'Continue with steady circular pours to keep the bed level.',
      'Finish once drawdown is even and clarity remains high.',
    ],
  ],
  [
    'slug' => 'cold-bottle-brew',
    'title' => 'Cold bottle brew',
    'subtitle' => 'Slow extraction for mellow sweetness and low acidity.',
    'image' => './images/assets/brew_guide_banner.jpg',
    'profile' => 'Low-acid and smooth',
    'duration' => '12-18 hrs',
    'grind' => 'Coarse',
    'ratio' => '1:8 concentrate',
    'steps' => [
      'Combine coarse grounds with cold water in a sealed bottle or jar.',
      'Steep in the fridge overnight for stable extraction.',
      'Strain cleanly, dilute to taste, and serve over ice.',
    ],
  ],
  [
    'slug' => 'milk-steaming',
    'title' => 'Milk steaming',
    'subtitle' => 'Microfoam texture for latte and flat white drinks.',
    'image' => './images/assets/french_press_brew.jpg',
    'profile' => 'Velvety and sweet',
    'duration' => '1-2 min',
    'grind' => 'N/A',
    'ratio' => 'Milk to cup fit',
    'steps' => [
      'Purge the wand, then place the tip just below milk surface.',
      'Stretch briefly, then roll milk to integrate silky texture.',
      'Stop at serving temperature and polish before pouring.',
    ],
  ],
  [
    'slug' => 'coffee-maker',
    'title' => 'Coffee maker',
    'subtitle' => 'Reliable daily brewing with consistent balance.',
    'image' => './images/assets/banner-cafe.jpg',
    'profile' => 'Balanced and familiar',
    'duration' => '4-6 min',
    'grind' => 'Medium',
    'ratio' => '1:15',
    'steps' => [
      'Rinse the filter and add evenly ground coffee.',
      'Use filtered water and brew on a clean machine.',
      'Serve fresh and avoid prolonged heat plate holding.',
    ],
  ],
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main id="guides-top" class="ah-guides-main">
  <section class="container-fluid px-0">
    <header class="ah-hero">
      <img src="images/assets/brew_guide_banner.jpg" alt="A coffee brewing setup with dripper and cup" class="ah-hero-image">
      <div class="ah-hero-overlay"></div>
      <div class="container ah-hero-content">
        <p class="ah-hero-kicker mb-2">Coffee Guides</p>
        <h1 class="ah-hero-title mb-3">Brew better cups with clear, practical steps.</h1>
        <p class="ah-hero-lead mb-4">From espresso to pour-over, each guide gives you the exact brew path, ratio, and timing you need.</p>
        <div class="d-flex flex-wrap gap-3">
          <a href="#guides-grid" class="btn btn-primary ah-guides-hero-cta">Browse Guides</a>
          <a href="shop-coffee.php" class="btn btn-outline-primary ah-guides-hero-cta">Find Coffee Beans</a>
        </div>
      </div>
    </header>
  </section>

  <section class="container ah-guides-content" id="guides-grid" aria-labelledby="guides-heading">
    <header class="ah-guides-section-head mb-4 mb-lg-5">
      <p class="ah-guides-kicker mb-2">Step-by-Step</p>
      <h2 id="guides-heading" class="ah-guides-title mb-2">Choose your brew method and open details.</h2>
      <p class="ah-guides-copy mb-0">Use these as your baseline, then fine-tune by taste and bean freshness.</p>
    </header>

    <div class="row g-4">
      <?php foreach ($brewMethods as $method): ?>
        <?php
        $detailAnchor = '#guide-' . $method['slug'] . '-details';
        ?>
        <div class="col-12 col-md-6 col-xl-4">
          <article class="ah-brew-guide-tile h-100">
            <div class="ah-brew-guide-tile-image-wrap">
              <span class="ah-brew-guide-badge"><?php echo htmlspecialchars($method['duration'], ENT_QUOTES, 'UTF-8'); ?></span>
              <img
                src="<?php echo htmlspecialchars($method['image'], ENT_QUOTES, 'UTF-8'); ?>"
                alt="<?php echo htmlspecialchars($method['title'], ENT_QUOTES, 'UTF-8'); ?>"
                class="ah-brew-guide-tile-image"
              >
            </div>
            <div class="ah-brew-guide-tile-body">
              <p class="ah-brew-guide-kicker mb-2">Brew guide</p>
              <h3 class="ah-brew-guide-title mb-1"><?php echo htmlspecialchars($method['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <p class="ah-brew-guide-subtitle mb-3"><?php echo htmlspecialchars($method['subtitle'], ENT_QUOTES, 'UTF-8'); ?></p>
              <ul class="ah-brew-guide-meta list-unstyled mb-0" aria-label="<?php echo htmlspecialchars($method['title'], ENT_QUOTES, 'UTF-8'); ?> guide details">
                <li><span>Flavor profile</span><strong><?php echo htmlspecialchars($method['profile'], ENT_QUOTES, 'UTF-8'); ?></strong></li>
                <li><span>Grind</span><strong><?php echo htmlspecialchars($method['grind'], ENT_QUOTES, 'UTF-8'); ?></strong></li>
                <li><span>Ratio</span><strong><?php echo htmlspecialchars($method['ratio'], ENT_QUOTES, 'UTF-8'); ?></strong></li>
              </ul>
              <a
                href="<?php echo htmlspecialchars($detailAnchor, ENT_QUOTES, 'UTF-8'); ?>"
                class="ah-bean-detail-link ah-brew-guide-open"
                aria-label="Open details for <?php echo htmlspecialchars($method['title'], ENT_QUOTES, 'UTF-8'); ?>"
              >
                Open detail
                <span aria-hidden="true">&rarr;</span>
              </a>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container ah-guides-detail-blocks" aria-labelledby="guide-quick-notes-heading">
    <header class="ah-guides-section-head mb-4">
      <p class="ah-guides-kicker mb-2">Quick Notes</p>
      <h2 id="guide-quick-notes-heading" class="ah-guides-title mb-0">Compact brewing paths for daily use.</h2>
    </header>

    <?php foreach ($brewMethods as $method): ?>
      <article id="guide-<?php echo htmlspecialchars($method['slug'], ENT_QUOTES, 'UTF-8'); ?>-details" class="ah-brew-guide-detail">
        <div class="ah-brew-guide-detail-head">
          <h3 class="ah-brew-guide-detail-title mb-0"><?php echo htmlspecialchars($method['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
          <p class="ah-brew-guide-detail-note mb-0"><?php echo htmlspecialchars($method['duration'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <ol class="ah-brew-guide-steps">
          <?php foreach ((array) $method['steps'] as $step): ?>
            <li><?php echo htmlspecialchars($step, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        </ol>
        <a href="#guides-top" class="ah-brew-guide-back">Back to top</a>
      </article>
    <?php endforeach; ?>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

