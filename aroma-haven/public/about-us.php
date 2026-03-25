<?php
$pageTitle = 'Aroma Haven | About Us';
$bodyClass = 'ah-story-body';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="ah-story-main">
    <section class="ah-hero">
        <img src="images/assets/banner-cafe.jpg" alt="A warm cafe table set with coffee and pastries" class="ah-hero-image">
        <div class="ah-hero-overlay"></div>
        <div class="container ah-hero-content">
            <p class="ah-hero-kicker mb-2">Our Story</p>
            <h1 class="ah-hero-title mb-3">From one shared table to your daily ritual.</h1>
            <p class="ah-hero-lead mb-4">Aroma Haven began with two friends, one notebook, and an obsession with coffee that feels personal, honest, and beautifully made.</p>
            <a href="shop-coffee.php" class="btn btn-primary ah-story-hero-cta">Explore Our Coffee</a>
        </div>
    </section>

    <section id="about-aroma-haven" class="ah-story-chapter ah-story-chapter-soft">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-6">
                    <p class="ah-story-kicker mb-2">How It Started</p>
                    <h2 class="ah-story-section-title mb-3">Built around conversation, not convenience.</h2>
                    <p class="ah-story-copy mb-3">In our early days, we hosted small cuppings for friends and neighbors, learning what people truly looked for in their morning cup. We heard one thing repeatedly: great coffee should feel approachable, not intimidating.</p>
                    <p class="ah-story-copy mb-0">That became our guiding principle. Every roast, brew guide, and product decision starts with one question: does this make someone&apos;s coffee ritual better tomorrow morning?</p>
                </div>
                <div class="col-lg-6">
                    <img src="images/assets/pour_over_brew.jpg" alt="Hand-brewing coffee with a pour-over setup" class="ah-story-image">
                </div>
            </div>
        </div>
    </section>

    <section id="philosophy" class="ah-story-chapter ah-story-chapter-deep">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-5 order-lg-2">
                    <p class="ah-story-kicker mb-2">Our Craft</p>
                    <h2 class="ah-story-section-title mb-3">Quality has a rhythm.</h2>
                    <p class="ah-story-copy mb-3">We source with long-term relationships, roast in small batches, and calibrate every profile for balance and clarity. Great coffee should reveal complexity without losing comfort.</p>
                    <ul class="ah-story-list mb-0">
                        <li>Seasonal sourcing with transparent partners</li>
                        <li>Small-lot roasting for consistency and freshness</li>
                        <li>Brew education designed for real kitchens</li>
                    </ul>
                </div>
                <div class="col-lg-7 order-lg-1">
                    <img src="images/assets/french_press_brew.jpg" alt="French press coffee being poured into a cup" class="ah-story-image">
                </div>
            </div>
        </div>
    </section>

    <section id="ethos" class="ah-story-chapter ah-story-chapter-soft">
        <div class="container">
            <div class="ah-story-final">
                <p class="ah-story-kicker mb-2">Where We&apos;re Going</p>
                <h2 class="ah-story-section-title mb-3">A coffee house for people who care about the details.</h2>
                <p class="ah-story-copy mb-4">We&apos;re expanding our guides, deepening our sourcing relationships, and building a shop experience that feels as intentional as the cup in your hands.</p>
                <a href="shop-coffee.php" class="btn btn-outline-primary ah-story-final-cta">Shop Fresh Roasts</a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
