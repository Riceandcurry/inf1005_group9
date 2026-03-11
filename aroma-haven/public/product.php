<?php
$pageTitle = 'Aroma Haven | Brew Guides';
$brewGuidesCards = [
  [
    'title' => 'French Press',
    'description' => 'Forgiving, rich, and beginner-friendly. Great first method.',
    'image' => './images/assets/french_press_brew.jpg',
    'style' => 'french-press',
    'time' => '4 minutes'

  ],
  [
    'title' => 'Pour Over',
    'description' => 'Clean and sweet cup with easy timing prompts',
    'image' => './images/assets/pour_over_brew.jpg',
    'style' => 'french-press',
    'time' => '4 minutes'
  ],
  [
    'title' => 'Pour Over',
    'description' => 'Clean and sweet cup with easy timing prompts',
    'image' => './images/assets/pour_over_brew.jpg',
    'style' => 'french-press',
    'time' => '4 minutes'
  ],
  [
    'title' => 'Pour Over',
    'description' => 'Clean and sweet cup with easy timing prompts',
    'image' => './images/assets/pour_over_brew.jpg',
    'style' => 'french-press',
    'time' => '4 minutes'
  ],
  [
    'title' => 'Pour Over',
    'description' => 'Clean and sweet cup with easy timing prompts',
    'image' => './images/assets/pour_over_brew.jpg',
    'style' => 'french-press',
    'time' => '4 minutes'
  ],
];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main>
    <section>
        <div class="brew-guides-banner">
            <img class="about-us-image" src="images/assets/brew_guide_banner.jpg" alt="Brew Guide banner">
        </div>
        <div>
            <div class="products-grid">
                <?php foreach ($brewGuidesCards as $guide): ?>
                <div class="card">
                    <h3 class="ah-guide-title mb-2"><?php echo htmlspecialchars($guide['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="ah-guide-copy mb-2"><?php echo htmlspecialchars($guide['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <img src="<?php echo htmlspecialchars($guide['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($guide['title'], ENT_QUOTES, 'UTF-8'); ?>" class="ah-guide-image">
                    <p class="ah-guide-copy mb-2"><?php echo htmlspecialchars($guide['style'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="ah-guide-copy mb-2"><?php echo htmlspecialchars($guide['time'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>