<?php
$pageTitle = 'Aroma Haven | Login';
$bodyClass = 'ah-auth-body';
require_once __DIR__ . '/../backend/guest_guard.php';
require_guest();
include __DIR__ . '/../includes/header.php';
?>

<main id="main-content" class="ah-auth-page">
  <section class="ah-auth-layout">
    <aside class="ah-auth-media" aria-hidden="true">
      <img src="./images/assets/auth-visual.jpg" alt="">
      <div class="ah-auth-media-copy">
        <p class="ah-auth-media-kicker">Aroma Haven</p>
        <p class="ah-auth-media-title">Return to your ritual.</p>
        <p class="ah-auth-media-note">Reorder faster, track every roast, and keep your week stocked.</p>
      </div>
    </aside>

    <section class="ah-auth-panel-wrap" aria-labelledby="login-title">
      <div class="ah-auth-topbar">
        <a href="shop-coffee.php" class="ah-auth-back">&larr; Back to Shop</a>
        <span class="ah-auth-brand">Aroma Haven</span>
      </div>

      <article class="ah-auth-panel">
        <h1 id="login-title" class="ah-auth-title">Welcome back.</h1>
        <p class="ah-auth-subtitle">Sign in to reach your coffee cabinet and checkout in seconds.</p>

        <form action="route.php" method="post" class="ah-auth-form">
          <div>
            <label for="email" class="ah-auth-label form-label mb-1">Email address</label>
            <input type="email" id="email" name="email" class="form-control ah-auth-input" placeholder="jane@example.com" autocomplete="email" required>
          </div>

          <div>
            <label for="password" class="ah-auth-label form-label mb-1">Password</label>
            <input type="password" id="password" name="password" class="form-control ah-auth-input" placeholder="********" autocomplete="current-password" required>
          </div>

          <button type="submit" class="btn btn-primary w-100 ah-auth-btn">Sign in</button>         
          <p class="ah-auth-trust">Secure session. Your account data stays protected.</p>
          <input type="hidden" name="action" value="login">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <?php
          if (isset($_SESSION['error'])) {
              echo "<p class='ah-auth-error mb-0' role='alert' aria-live='assertive'>" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "</p>";
              unset($_SESSION['error']);
          }
          ?>                     
        </form>

        <p class="ah-auth-footnote">
          Don&apos;t have an account? <a href="register.php" class="ah-auth-link">Create one now</a>
        </p>
      </article>
    </section>
  </section>
</main>

</body>
</html>
