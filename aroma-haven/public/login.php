<?php
$pageTitle = 'Aroma Haven | Login';
include __DIR__ . '/../includes/header.php';
?>

<main class="ah-login-page">
  <section class="ah-login-main">
    <div class="container-fluid px-0">
      <div class="row g-0">
        <div class="col-lg-6 position-relative ah-login-visual">
          <img src="./images/assets/auth-visual.jpg" alt="Coffee scene" class="w-100 h-100 object-fit-cover ah-login-image">
          <div class="ah-login-overlay-text text-center">
            <h1 class="mb-2">Your daily ritual, perfected.</h1>
            <p class="mb-0">Join the Regulars&apos; Club for 1-click reorders and exclusive micro-lots.</p>
          </div>
        </div>

        <div class="col-lg-6 bg-steamed d-flex align-items-stretch">
          <div class="ah-login-form-wrap w-100">
            <div class="d-flex justify-content-between align-items-center ah-login-top-row mb-0">
              <a href="shop-coffee.php" class="ah-login-back">&larr; Back to Shop</a>
              <span class="ah-login-brand">Aroma Haven</span>
            </div>

            <div class="ah-login-form-content">
              <h2 class="mb-3">Welcome back.</h2>
              <p class="ah-login-subtitle mb-4">Sign in to access your coffee cabinet and fast checkout.</p>

              <form action="#" method="post" class="ah-login-form">
                <div class="mb-3">
                  <label for="email" class="form-label text-overline mb-1">Email address</label>
                  <input type="email" id="email" name="email" class="form-control ah-login-input" placeholder="jane@example.com" required>
                </div>

                <div class="mb-4">
                  <label for="password" class="form-label text-overline mb-1">Password</label>
                  <input type="password" id="password" name="password" class="form-control ah-login-input" placeholder="********" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
              </form>

              <p class="ah-login-register-note text-center mt-2 mb-0">
                Don&apos;t have an account? <a href="register.php">Join the club</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>