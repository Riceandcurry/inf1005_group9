<?php
$pageTitle = 'Aroma Haven | Coffee Personality Quiz';

require_once __DIR__ . '/../includes/bean-catalog.php';

/**
 * Return a normalized slug-like value from request params.
 */
function ah_quiz_slug($value)
{
  return is_string($value) ? trim(strtolower($value)) : '';
}

/**
 * Convert display price string ($24) to numeric value.
 */
function ah_quiz_price_value($priceLabel)
{
  return (float) preg_replace('/[^0-9.]/', '', (string) $priceLabel);
}

/**
 * Convert roast label into coarse roast tier for scoring.
 */
function ah_quiz_roast_tier($roastLabel)
{
  $value = strtolower((string) $roastLabel);
  if (strpos($value, 'light') !== false) {
    return 'light';
  }
  if (strpos($value, 'dark') !== false) {
    return 'dark';
  }
  return 'medium';
}

/**
 * Build stable quiz URLs preserving known answers up to a target step.
 */
function ah_quiz_build_url($targetStep, array $answers, array $stepOrder)
{
  $params = ['step' => $targetStep];

  foreach ($stepOrder as $stepKey) {
    if (!empty($answers[$stepKey])) {
      $params[$stepKey] = (string) $answers[$stepKey];
    }
    if ($stepKey === $targetStep) {
      break;
    }
  }

  return 'personality-quiz.php?' . http_build_query($params);
}

/**
 * Redirect and stop execution.
 */
function ah_quiz_redirect($url)
{
  header('Location: ' . $url);
  exit;
}

/**
 * Clear answers for all steps after a selected step.
 */
function ah_quiz_clear_future_answers(array $answers, array $stepOrder, $fromStep)
{
  $fromIndex = array_search($fromStep, $stepOrder, true);
  if ($fromIndex === false) {
    return $answers;
  }

  for ($i = $fromIndex + 1; $i < count($stepOrder); $i++) {
    $answers[$stepOrder[$i]] = '';
  }

  return $answers;
}

/**
 * Score catalog beans and return the winning bean ID.
 */
function ah_quiz_pick_bean(array $answers, array $catalog)
{
  $flavorKeywords = [
    'chocolatey' => ['chocolate', 'cocoa', 'brown sugar', 'caramel'],
    'fruity' => ['fruit', 'citrus', 'apple', 'peach', 'berry', 'blackcurrant', 'stone'],
    'floral' => ['jasmine', 'floral', 'bergamot', 'tea-like'],
    'nutty' => ['hazelnut', 'nut', 'walnut', 'sweet'],
    'spiced' => ['spice', 'earthy', 'smoky'],
  ];

  $methodProfile = [
    'espresso' => [
      'roasts' => ['medium', 'dark'],
      'keywords' => ['chocolate', 'cocoa', 'hazelnut', 'caramel', 'sweet'],
    ],
    'french_press' => [
      'roasts' => ['medium', 'dark'],
      'keywords' => ['earthy', 'spice', 'chocolate', 'cacao', 'full-bodied'],
    ],
    'pour_over' => [
      'roasts' => ['light', 'medium'],
      'keywords' => ['floral', 'jasmine', 'citrus', 'tea-like', 'fruit'],
    ],
    'cold_brew' => [
      'roasts' => ['medium', 'dark'],
      'keywords' => ['chocolate', 'sweet', 'nut', 'cocoa'],
    ],
    'aero_press' => [
      'roasts' => ['light', 'medium', 'dark'],
      'keywords' => ['balanced', 'fruit', 'sweet', 'cocoa'],
    ],
  ];

  $intensityWeights = [
    'mellow' => ['light' => 12, 'medium' => 7, 'dark' => 2],
    'balanced' => ['light' => 7, 'medium' => 12, 'dark' => 7],
    'bold' => ['light' => 2, 'medium' => 8, 'dark' => 14],
  ];

  $roastWeights = [
    'light' => ['light' => 18, 'medium' => 8, 'dark' => 2],
    'medium' => ['light' => 10, 'medium' => 18, 'dark' => 10],
    'dark' => ['light' => 2, 'medium' => 9, 'dark' => 18],
  ];

  $tieBreakPriority = [
    'colombia-huila',
    'ethiopia-yirgacheffe',
    'sumatra-mandheling',
    'peru-cajamarca',
    'guatemala-antigua',
    'brazil-cerrado',
    'rwanda-huye',
    'kenya-aa',
    'panama-geisha',
  ];
  $tieBreakMap = array_flip($tieBreakPriority);

  $scores = [];

  foreach ($catalog as $beanId => $bean) {
    $roastTier = ah_quiz_roast_tier($bean['roast'] ?? '');
    $priceValue = ah_quiz_price_value($bean['price'] ?? '0');
    $searchText = strtolower(trim(implode(' ', [
      (string) ($bean['name'] ?? ''),
      (string) ($bean['description'] ?? ''),
      implode(' ', (array) ($bean['tags'] ?? [])),
    ])));

    $score = 0;
    $flavorMatchCount = 0;

    $method = (string) ($answers['method'] ?? '');
    if (isset($methodProfile[$method])) {
      $methodRule = $methodProfile[$method];
      if (in_array($roastTier, $methodRule['roasts'], true)) {
        $score += 14;
      } else {
        $score += 4;
      }

      foreach ($methodRule['keywords'] as $keyword) {
        if (strpos($searchText, $keyword) !== false) {
          $score += 3;
        }
      }
    }

    $flavor = (string) ($answers['flavor'] ?? '');
    if (isset($flavorKeywords[$flavor])) {
      foreach ($flavorKeywords[$flavor] as $keyword) {
        if (strpos($searchText, $keyword) !== false) {
          $score += 6;
          $flavorMatchCount++;
        }
      }
    }

    $intensity = (string) ($answers['intensity'] ?? '');
    if (isset($intensityWeights[$intensity][$roastTier])) {
      $score += $intensityWeights[$intensity][$roastTier];
    }

    $roast = (string) ($answers['roast'] ?? '');
    if (isset($roastWeights[$roast][$roastTier])) {
      $score += $roastWeights[$roast][$roastTier];
    }

    $budget = (string) ($answers['budget'] ?? '');
    if ($budget === 'value') {
      if ($priceValue <= 22) {
        $score += 14;
      } elseif ($priceValue <= 24) {
        $score += 7;
      } else {
        $score += 2;
      }
    } elseif ($budget === 'standard') {
      if ($priceValue >= 22 && $priceValue <= 27) {
        $score += 12;
      } elseif ($priceValue < 22 || $priceValue <= 29) {
        $score += 7;
      } else {
        $score += 2;
      }
    } elseif ($budget === 'premium') {
      if ($priceValue >= 26) {
        $score += 13;
      } else {
        $score += 5;
      }
    } elseif ($budget === 'splurge') {
      if ($priceValue >= 32) {
        $score += 15;
      } elseif ($priceValue >= 28) {
        $score += 8;
      } else {
        $score += 1;
      }
    }

    $scores[] = [
      'id' => $beanId,
      'score' => $score,
      'flavor_match_count' => $flavorMatchCount,
      'price' => $priceValue,
      'priority' => $tieBreakMap[$beanId] ?? 999,
    ];
  }

  if (empty($scores)) {
    return 'colombia-huila';
  }

  usort($scores, function ($left, $right) use ($answers) {
    if ($left['score'] !== $right['score']) {
      return $right['score'] <=> $left['score'];
    }

    if ($left['flavor_match_count'] !== $right['flavor_match_count']) {
      return $right['flavor_match_count'] <=> $left['flavor_match_count'];
    }

    $budget = (string) ($answers['budget'] ?? '');
    if ($budget === 'value' || $budget === 'standard') {
      if ($left['price'] !== $right['price']) {
        return $left['price'] <=> $right['price'];
      }
    } else {
      if ($left['price'] !== $right['price']) {
        return $right['price'] <=> $left['price'];
      }
    }

    if ($left['priority'] !== $right['priority']) {
      return $left['priority'] <=> $right['priority'];
    }

    return strcmp((string) $left['id'], (string) $right['id']);
  });

  return (string) ($scores[0]['id'] ?? 'colombia-huila');
}

$quizSteps = [
  'method' => [
    'title' => 'How do you brew most often?',
    'description' => 'Pick your go-to brewing method.',
    'options' => [
      [
        'id' => 'espresso',
        'label' => 'Espresso Machine',
        'hint' => 'Concentrated and rich shots',
        'image' => '/images/assets/espresso_icon.png',
      ],
      [
        'id' => 'french_press',
        'label' => 'French Press',
        'hint' => 'Heavy body and full texture',
        'image' => '/images/assets/french_press_icon.png',
      ],
      [
        'id' => 'pour_over',
        'label' => 'Pour Over',
        'hint' => 'Clean and aromatic cups',
        'image' => '/images/assets/drip_coffee_icon.png',
      ],
      [
        'id' => 'cold_brew',
        'label' => 'Cold Brew',
        'hint' => 'Smooth low-acid coffee',
        'image' => '/images/assets/drip_coffee_icon.png',
      ],
      [
        'id' => 'aero_press',
        'label' => 'AeroPress',
        'hint' => 'Versatile and punchy',
        'image' => '/images/assets/drip_coffee_icon.png',
      ],
    ],
  ],
  'flavor' => [
    'title' => 'What flavor profile sounds best?',
    'description' => 'Choose the flavor direction you enjoy most.',
    'options' => [
      [
        'id' => 'chocolatey',
        'label' => 'Chocolatey',
        'hint' => 'Cocoa, caramel, dessert-like',
        'image' => '/images/assets/choco_icon.png',
      ],
      [
        'id' => 'fruity',
        'label' => 'Fruity',
        'hint' => 'Citrus, berry, bright cup',
        'image' => '/images/assets/fruity_icon.png',
      ],
      [
        'id' => 'floral',
        'label' => 'Floral',
        'hint' => 'Tea-like, fragrant, delicate',
        'image' => '/images/assets/floral_icon.png',
      ],
      [
        'id' => 'nutty',
        'label' => 'Nutty',
        'hint' => 'Hazelnut, walnut, comfort notes',
        'image' => '/images/assets/nutty_icon.png',
      ],
      [
        'id' => 'spiced',
        'label' => 'Spiced & Earthy',
        'hint' => 'Warm spice and depth',
        'image' => '/images/assets/floral_icon.png',
      ],
    ],
  ],
  'intensity' => [
    'title' => 'How intense should your cup feel?',
    'description' => 'This helps balance body and roast character.',
    'options' => [
      [
        'id' => 'mellow',
        'label' => 'Mellow',
        'hint' => 'Light, easy, and smooth',
        'image' => '/images/assets/low_intense_icon.png',
      ],
      [
        'id' => 'balanced',
        'label' => 'Balanced',
        'hint' => 'Rounded and versatile',
        'image' => '/images/assets/med_intense_icon.png',
      ],
      [
        'id' => 'bold',
        'label' => 'Bold',
        'hint' => 'Deep and full-bodied',
        'image' => '/images/assets/high_intense_icon.png',
      ],
    ],
  ],
  'roast' => [
    'title' => 'What roast level do you prefer?',
    'description' => 'Select the roast profile you usually enjoy.',
    'options' => [
      [
        'id' => 'light',
        'label' => 'Light Roast',
        'hint' => 'Floral, tea-like, bright',
        'image' => '/images/assets/low_intense_icon.png',
      ],
      [
        'id' => 'medium',
        'label' => 'Medium Roast',
        'hint' => 'Balanced sweetness and body',
        'image' => '/images/assets/med_intense_icon.png',
      ],
      [
        'id' => 'dark',
        'label' => 'Dark Roast',
        'hint' => 'Rich, smoky, and strong',
        'image' => '/images/assets/high_intense_icon.png',
      ],
    ],
  ],
  'budget' => [
    'title' => 'What price range fits your routine?',
    'description' => 'We will match beans to your comfort budget.',
    'options' => [
      [
        'id' => 'value',
        'label' => 'Value Picks',
        'hint' => 'Everyday quality under control',
        'image' => '/images/assets/tag.png',
      ],
      [
        'id' => 'standard',
        'label' => 'Standard Range',
        'hint' => 'Great balance of quality and spend',
        'image' => '/images/assets/tag.png',
      ],
      [
        'id' => 'premium',
        'label' => 'Premium Range',
        'hint' => 'High quality with extra nuance',
        'image' => '/images/assets/tag.png',
      ],
      [
        'id' => 'splurge',
        'label' => 'Special Treat',
        'hint' => 'Unique and rare cup experiences',
        'image' => '/images/assets/tag.png',
      ],
    ],
  ],
];

$stepOrder = array_keys($quizSteps);
$allSteps = array_merge($stepOrder, ['result']);
$allowedByStep = [];

foreach ($quizSteps as $stepKey => $stepConfig) {
  $allowedByStep[$stepKey] = array_column((array) $stepConfig['options'], 'id');
}

$answers = [];
foreach ($stepOrder as $answerKey) {
  $answerValue = ah_quiz_slug($_GET[$answerKey] ?? '');
  if (!in_array($answerValue, $allowedByStep[$answerKey], true)) {
    $answerValue = '';
  }
  $answers[$answerKey] = $answerValue;
}

$step = ah_quiz_slug($_GET['step'] ?? $stepOrder[0]);
if (!in_array($step, $allSteps, true)) {
  $step = $stepOrder[0];
}

if ($step !== 'result') {
  $currentStepIndex = array_search($step, $stepOrder, true);
  if ($currentStepIndex === false) {
    ah_quiz_redirect('personality-quiz.php');
  }

  for ($i = 0; $i < $currentStepIndex; $i++) {
    $requiredStep = $stepOrder[$i];
    if ($answers[$requiredStep] === '') {
      ah_quiz_redirect(ah_quiz_build_url($requiredStep, $answers, $stepOrder));
    }
  }
} else {
  foreach ($stepOrder as $requiredStep) {
    if ($answers[$requiredStep] === '') {
      ah_quiz_redirect('personality-quiz.php');
    }
  }

  $catalog = ah_get_bean_catalog();
  $winnerBeanId = ah_quiz_pick_bean($answers, $catalog);

  if (!isset($catalog[$winnerBeanId])) {
    if (isset($catalog['colombia-huila'])) {
      $winnerBeanId = 'colombia-huila';
    } else {
      $catalogKeys = array_keys($catalog);
      $winnerBeanId = (string) ($catalogKeys[0] ?? '');
    }
  }

  if ($winnerBeanId === '') {
    ah_quiz_redirect('shop-coffee.php');
  }

  $query = http_build_query([
    'bean' => $winnerBeanId,
    'src' => 'quiz',
  ]);

  ah_quiz_redirect('coffee-product.php?' . $query);
}

$currentStep = $quizSteps[$step];
$currentStepIndex = array_search($step, $stepOrder, true);
$totalSteps = count($stepOrder);
$previousStep = $currentStepIndex > 0 ? $stepOrder[$currentStepIndex - 1] : '';
$nextStep = $currentStepIndex < ($totalSteps - 1) ? $stepOrder[$currentStepIndex + 1] : 'result';
$progressPercent = (int) round((($currentStepIndex + 1) / $totalSteps) * 100);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="ah-quiz-main">
  <section class="container ah-quiz-shell">
    <header class="ah-quiz-head">
      <div class="ah-quiz-actions">
        <?php if ($previousStep !== ''): ?>
          <a class="ah-quiz-link" href="<?php echo htmlspecialchars(ah_quiz_build_url($previousStep, $answers, $stepOrder), ENT_QUOTES, 'UTF-8'); ?>">&larr; Back</a>
        <?php else: ?>
          <a class="ah-quiz-link" href="shop-coffee.php">&larr; Back to Shop</a>
        <?php endif; ?>
        <a class="ah-quiz-link" href="personality-quiz.php">Start Over</a>
      </div>

      <p class="ah-quiz-kicker mb-2">Coffee Personality Quiz</p>
      <h1 class="ah-quiz-title mb-2"><?php echo htmlspecialchars($currentStep['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
      <p class="ah-quiz-copy mb-0"><?php echo htmlspecialchars($currentStep['description'], ENT_QUOTES, 'UTF-8'); ?></p>

      <div class="ah-quiz-progress-wrap" aria-label="Quiz progress">
        <div class="ah-quiz-progress" role="progressbar" aria-valuemin="1" aria-valuemax="<?php echo (int) $totalSteps; ?>" aria-valuenow="<?php echo (int) ($currentStepIndex + 1); ?>">
          <span class="ah-quiz-progress-fill" style="width: <?php echo (int) $progressPercent; ?>%;"></span>
        </div>
        <p class="ah-quiz-progress-copy mb-0">Step <?php echo (int) ($currentStepIndex + 1); ?> of <?php echo (int) $totalSteps; ?></p>
      </div>
    </header>

    <ul class="ah-quiz-grid" role="list">
      <?php foreach ($currentStep['options'] as $option): ?>
        <?php
        $nextAnswers = $answers;
        $nextAnswers[$step] = (string) $option['id'];
        $nextAnswers = ah_quiz_clear_future_answers($nextAnswers, $stepOrder, $step);
        $optionHref = ah_quiz_build_url($nextStep, $nextAnswers, $stepOrder);
        ?>
        <li>
          <a href="<?php echo htmlspecialchars($optionHref, ENT_QUOTES, 'UTF-8'); ?>" class="ah-quiz-option">
            <img
              src="<?php echo htmlspecialchars((string) $option['image'], ENT_QUOTES, 'UTF-8'); ?>"
              alt="<?php echo htmlspecialchars((string) $option['label'], ENT_QUOTES, 'UTF-8'); ?>"
              class="ah-quiz-option-image"
            >
            <h2 class="ah-quiz-option-title"><?php echo htmlspecialchars((string) $option['label'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="ah-quiz-option-hint mb-0"><?php echo htmlspecialchars((string) $option['hint'], ENT_QUOTES, 'UTF-8'); ?></p>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
