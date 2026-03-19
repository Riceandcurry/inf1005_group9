<?php
$pageTitle = 'Aroma Haven | Coffee Personality Quiz';

$steps = [
  'method' => [
    'title' => 'Hi There!',
    'text' => 'How do you make your coffee? Choose a method to continue.',
    'cards' => [
      ['id' => 'french_press', 'title' => 'French Press', 'image' => '/images/assets/french_press_icon.png'],
      ['id' => 'espresso', 'title' => 'Espresso', 'image' => '/images/assets/espresso_icon.png'],
      ['id' => 'drip', 'title' => 'Drip Coffee', 'image' => '/images/assets/drip_coffee_icon.png'],
      ['id' => 'drip2', 'title' => 'Drip Coffee 2', 'image' => '/images/assets/drip_coffee_icon.png'],
      ['id' => 'drip3', 'title' => 'Drip Coffee 3', 'image' => '/images/assets/drip_coffee_icon.png'],
      ['id' => 'drip4', 'title' => 'Drip Coffee 4', 'image' => '/images/assets/drip_coffee_icon.png'],
      ['id' => 'drip5', 'title' => 'Drip Coffee 5', 'image' => '/images/assets/drip_coffee_icon.png'],
    ],
  ],
  'beans' => [
    'title' => 'Sensory Taste Profile',
    'text' => 'What flavours do you like best? Pick one and we’ll show a few extra recommendations.',
    'cards' => [
      ['id' => 'chocolate', 'title' => 'Chocolatey', 'image' => '/images/assets/choco_icon.png'],
      ['id' => 'nutty', 'title' => 'Nutty', 'image' => '/images/assets/nutty_icon.png'],
      ['id' => 'fruity', 'title' => 'Fruity', 'image' => '/images/assets/fruity_icon.png'],
      ['id' => 'floral', 'title' => 'Floral', 'image' => '/images/assets/floral_icon.png'],
      ['id' => 'blue', 'title' => 'Fruity', 'image' => '/images/assets/floral_icon.png'],
      ['id' => 'blum', 'title' => 'Fruity', 'image' => '/images/assets/floral_icon.png'],
    ],
  ],
    'intensity' => [
    'title' => 'Flavour Intensity',
    'text' => 'What intensity do you prefer your coffee?',
    'cards' => [
      ['id' => 'light', 'title' => 'Light Intensity', 'image' => '/images/assets/low_intense_icon.png'],
      ['id' => 'medium', 'title' => 'Medium Intensity', 'image' => '/images/assets/med_intense_icon.png'],
      ['id' => 'dark', 'title' => 'Dark Intensity', 'image' => '/images/assets/high_intense_icon.png'],
    ],
  ],
  'result' => [
    'title' => 'Nice pick!',
    'text' => 'Here are some items we think you might love.',
    'cards' => [
      ['id' => 'bean_1', 'title' => 'Signature Blend', 'image' => '/images/assets/french_press_brew.jpg'],
      ['id' => 'bean_2', 'title' => 'Single Origin', 'image' => '/images/assets/french_press_brew.jpg'],
      ['id' => 'accessory', 'title' => 'Brew Accessory', 'image' => '/images/assets/french_press_brew.jpg'],
    ],
  ],
];

$step = $_GET['step'] ?? 'method';
$method = $_GET['method'] ?? null;
$flavor = $_GET['flavor'] ?? null;
$intensity = $_GET['intensity'] ?? null;

// Dynamic result mapping by method + flavor + intensity.
$resultMap = [
  'french_press-chocolate-light' => [
    ['id' => 'r1', 'title' => 'Light Chocolate French Press', 'image' => '/images/assets/french_press_brew.jpg'],
    ['id' => 'r2', 'title' => 'Cocoa Bloom', 'image' => '/images/assets/french_press_brew.jpg'],
    ['id' => 'r3', 'title' => 'Creamy Filter Mugs', 'image' => '/images/assets/drip_coffee_icon.png'],
  ],
  'french_press-chocolate-dark' => [
    ['id' => 'r1', 'title' => 'Dark Cocoa Press', 'image' => '/images/assets/french_press_brew.jpg'],
    ['id' => 'r2', 'title' => 'Smoky Espresso', 'image' => '/images/assets/french_press_brew.jpg'],
    ['id' => 'r3', 'title' => 'Robust Beans', 'image' => '/images/assets/drip_coffee_icon.png'],
  ],
  'espresso-fruity-medium' => [
    ['id' => 'r1', 'title' => 'Bright Espresso', 'image' => '/images/assets/french_press_brew.jpg'],
    ['id' => 'r2', 'title' => 'Citrus Shot', 'image' => '/images/assets/french_press_brew.jpg'],
    ['id' => 'r3', 'title' => 'Fruity Roast', 'image' => '/images/assets/drip_coffee_icon.png'],
  ],
  // default fallback, defaut is the very bare basic random names, update with the new shop when done 
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main>
  <section class="personality-quiz<?php echo $step === 'result' ? ' result-step' : ''; ?><?php echo $step === 'intensity' ? ' intensity-step' : ''; ?>">
    <div>
      <?php
        $displayTitle = $steps[$step]['title'] ?? $steps['method']['title'];
        $displayText = $steps[$step]['text'] ?? $steps['method']['text'];

        // If this is the final step and method/flavor/intensity are set, include them in the text
        if ($step === 'result' && !empty($method) && !empty($flavor) && !empty($intensity)) {
          $displayText = 'Nice pick! Based on ' . htmlspecialchars($method, ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($flavor, ENT_QUOTES, 'UTF-8') . ', and ' . htmlspecialchars($intensity, ENT_QUOTES, 'UTF-8') . '.';
        }
      ?>

      <h1 class="quiz-title"><?php echo $displayTitle; ?></h1>
      <h2 class="quiz-text"><?php echo $displayText; ?></h2>
    </div>

    <div class="quiz-grid">
      <?php
        $cards = [];
        if ($step === 'method') {
          $cards = $steps['method']['cards'];
        } elseif ($step === 'beans') {
          $cards = $steps['beans']['cards'];
        } elseif ($step === 'intensity') {
          $cards = $steps['intensity']['cards'];
        } elseif ($step === 'result') {
          $key = "{$method}-{$flavor}-{$intensity}";
          if (!empty($method) && !empty($flavor) && !empty($intensity) && isset($resultMap[$key])) {
            $steps['result']['cards'] = $resultMap[$key];
          }
          $cards = $steps['result']['cards'];
        }

        foreach ($cards as $card):
          $isClickable = $step !== 'result';
          $href = 'javascript:void(0)';
          $resultClass = ($step === 'result') ? ' result-card' : '';
          if ($step === 'method') {
            $href = 'personality-quiz.php?step=beans&method=' . urlencode((string)$card['id']);
          } elseif ($step === 'beans') {
            $href = 'personality-quiz.php?step=intensity&method=' . urlencode((string)$method) . '&flavor=' . urlencode((string)$card['id']);
          } elseif ($step === 'intensity') {
            $href = 'personality-quiz.php?step=result&method=' . urlencode((string)$method) . '&flavor=' . urlencode((string)$flavor) . '&intensity=' . urlencode((string)$card['id']);
          }
      ?>
        <?php if ($isClickable): ?>
          <a href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>" class="card quiz-card<?php echo $resultClass; ?>">
        <?php else: ?>
          <div class="card quiz-card<?php echo $resultClass; ?>">
        <?php endif; ?>
            <h3 class="ah-guide-title mb-2"><?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <img src="<?php echo htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?>" class="ah-guide-image">
        <?php if ($isClickable): ?>
          </a>
        <?php else: ?>  
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <?php if ($step === 'result'): ?>
      <div class="quiz-buttons">
        <a href="personality-quiz.php" class="btn btn-outline-secondary">Start over</a>
        <a href="shop-coffee.php" class="btn btn-primary ms-2">Go to shop</a>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>