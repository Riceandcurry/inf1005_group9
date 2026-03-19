<?php
$pageTitle = 'Aroma Haven | Coffee Personality Quiz';

$steps = [
  'method' => [
    'title' => 'Hi There!',
    'text' => 'How do you make your coffee? Choose a method to continue.',
    'cards' => [
      ['id' => 'french_press', 'title' => 'French Press', 'image' => './images/assets/french_press_brew.jpg'],
      ['id' => 'espresso', 'title' => 'Espresso', 'image' => './images/assets/french_press_brew.jpg'],
      ['id' => 'drip', 'title' => 'Drip Coffee', 'image' => './images/assets/french_press_brew.jpg'],
    ],
  ],
  'beans' => [
    'title' => 'Sensory Taste Profile',
    'text' => 'What flavours do you like best? Pick one and we’ll show a few extra recommendations.',
    'cards' => [
      ['id' => 'chocolate', 'title' => 'Chocolatey', 'image' => './images/assets/french_press_brew.jpg'],
      ['id' => 'nutty', 'title' => 'Nutty', 'image' => './images/assets/french_press_brew.jpg'],
      ['id' => 'fruity', 'title' => 'Fruity', 'image' => './images/assets/french_press_brew.jpg'],
    ],
  ],
  'result' => [
    'title' => 'Nice pick!',
    'text' => 'Here are some items we think you might love.',
    'cards' => [
      ['id' => 'bean_1', 'title' => 'Signature Blend', 'image' => './images/assets/french_press_brew.jpg'],
      ['id' => 'bean_2', 'title' => 'Single Origin', 'image' => './images/assets/french_press_brew.jpg'],
      ['id' => 'accessory', 'title' => 'Brew Accessory', 'image' => './images/assets/french_press_brew.jpg'],
    ],
  ],
];

$step = $_GET['step'] ?? 'method';
$method = $_GET['method'] ?? null;

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main>
  <section class="personality-quiz">
    <div>
      <?php
        $displayTitle = $steps[$step]['title'] ?? $steps['method']['title'];
        $displayText = $steps[$step]['text'] ?? $steps['method']['text'];

        // If this is the final step and a choice was made, include it in the text
        if ($step === 'result' && !empty($_GET['choice'])) {
          $choice = htmlspecialchars($_GET['choice'], ENT_QUOTES, 'UTF-8');
          $displayText .= " (You chose: $choice)";
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
        } elseif ($step === 'result') {
          $cards = $steps['result']['cards'];
        }

        foreach ($cards as $card):
          $isClickable = $step !== 'result';
          $href = 'javascript:void(0)';
          if ($step === 'beans') {
            $href = 'personality-quiz.php?step=result&choice=' . urlencode($card['id']);
          } elseif ($step === 'method') {
            $href = 'personality-quiz.php?step=beans';
          }
      ?>
        <?php if ($isClickable): ?>
          <a href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>" class="card quiz-card">
        <?php else: ?>
          <div class="card quiz-card">
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
      <div class="mt-4">
        <a href="personality-quiz.php" class="btn btn-outline-secondary">Start over</a>
        <a href="shop-coffee.php" class="btn btn-primary ms-2">Go to shop</a>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>