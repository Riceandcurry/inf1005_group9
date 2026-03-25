<?php
$pageTitle = 'Aroma Haven | Register';
$bodyClass = 'ah-auth-body';
include __DIR__ . '/../includes/header.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<main class="ah-auth-page">
    <section class="ah-auth-layout">
        <aside class="ah-auth-media" aria-hidden="true">
            <img src="./images/assets/auth-visual.jpg" alt="">
            <div class="ah-auth-media-copy">
                <p class="ah-auth-media-kicker">Aroma Haven</p>
                <p class="ah-auth-media-title">Build your coffee profile.</p>
                <p class="ah-auth-media-note">Save brew preferences and get straight to the beans that fit your taste.</p>
            </div>
        </aside>

        <section class="ah-auth-panel-wrap" aria-labelledby="register-title">
            <div class="ah-auth-topbar">
                <a href="shop-coffee.php" class="ah-auth-back">&larr; Back to Shop</a>
                <span class="ah-auth-brand">Aroma Haven</span>
            </div>

            <article class="ah-auth-panel">
                <h1 id="register-title" class="ah-auth-title">Join the Regulars.</h1>
                <p class="ah-auth-subtitle">Create your account to save favorites, speed up checkout, and keep your ritual consistent.</p>

                <form action="route.php" method="post" class="ah-auth-form">
                    <div class="ah-auth-grid-2">
                        <div>
                            <label for="first_name" class="ah-auth-label form-label mb-1">First name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control ah-auth-input" placeholder="Jane" autocomplete="given-name" required>
                        </div>
                        <div>
                            <label for="last_name" class="ah-auth-label form-label mb-1">Last name</label>
                            <input type="text" id="last_name" name="last_name" class="form-control ah-auth-input" placeholder="Doe" autocomplete="family-name" required>
                        </div>
                    </div>

                    <div>
                        <label for="register_email" class="ah-auth-label form-label mb-1">Email address</label>
                        <input type="email" id="register_email" name="email" class="form-control ah-auth-input" placeholder="jane@example.com" autocomplete="email" required>
                    </div>

                    <div>
                        <label for="register_password" class="ah-auth-label form-label mb-1">Password</label>
                        <input type="password" id="register_password" name="password" class="form-control ah-auth-input" placeholder="********" autocomplete="new-password" required>
                    </div>

                    <div>
                        <label for="register_confirm_password" class="ah-auth-label form-label mb-1">Confirm password</label>
                        <input type="password" id="register_confirm_password" name="confirm_password" class="form-control ah-auth-input" placeholder="********" autocomplete="new-password" required>
                    </div>

                    <div>
                        <label for="brew_method" class="ah-auth-label form-label mb-1">How do you usually brew?</label>
                        <select id="brew_method" name="brew_method" class="form-select ah-auth-input" required>
                            <option value="" selected disabled>Select an option...</option>
                            <option value="1">Espresso</option>
                            <option value="2">Pour Over</option>
                            <option value="3">French Press</option>
                            <option value="4">Aeropress</option>
                            <option value="5">Drip Machine</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 ah-auth-btn">Create account</button>
                    <input type="hidden" name="action" value="register">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo "<p class='ah-auth-error mb-0' role='alert' aria-live='assertive'>" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "</p>";
                        unset($_SESSION['error']);
                    }
                    ?>
                    <p class="ah-auth-trust">We only use your details to manage your account and orders.</p>
                </form>

                <p class="ah-auth-footnote">
                    Already a regular? <a href="login.php" class="ah-auth-link">Sign in</a>
                </p>
            </article>
        </section>
    </section>
</main>

</body>
</html>
