<?php
$pageTitle = 'Aroma Haven | Register';
include __DIR__ . '/../includes/header.php';
?>

<main class="ah-login-page ah-register-page">
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
                            <h2 class="mb-3">Join the Regulars</h2>
                            <p class="ah-login-subtitle mb-4">Create an account to save your brew preferences and order history.</p>

                            <form action="route.php" method="post" class="ah-register-form">
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label for="first_name" class="form-label text-overline mb-1">First name</label>
                                        <input type="text" id="first_name" name="first_name" class="form-control ah-login-input" placeholder="Jane" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="last_name" class="form-label text-overline mb-1">Last name</label>
                                        <input type="text" id="last_name" name="last_name" class="form-control ah-login-input" placeholder="Doe" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="register_email" class="form-label text-overline mb-1">Email address</label>
                                    <input type="email" id="register_email" name="email" class="form-control ah-login-input" placeholder="jane@example.com" required>
                                </div>

                                <div class="mb-3">
                                    <label for="register_password" class="form-label text-overline mb-1">Password</label>
                                    <input type="password" id="register_password" name="password" class="form-control ah-login-input" placeholder="********" required>
                                </div>

                                <div class="mb-4">
                                    <label for="brew_method" class="form-label text-overline mb-1">How do you usually brew?</label>
                                    <select id="brew_method" name="brew_method" class="form-select ah-login-input" required>
                                        <option value="" selected disabled>Select an option...</option>
                                        <option value="1">Espresso</option>
                                        <option value="2">Pour Over</option>
                                        <option value="3">French Press</option>
                                        <option value="4">Aeropress</option>
                                        <option value="5">Drip Machine</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Register</button>
                                <input type="hidden" name="action" value="register">
                            </form>

                            <p class="ah-login-register-note text-center mt-3 mb-0">
                                Already a regular? <a href="login.php">Sign in</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
