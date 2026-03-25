<?php
$pageTitle = 'Aroma Haven | Contact Us';
$bodyClass = 'ah-contact-page';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main id="contact-top" class="ah-contact-main">
  <section class="container-fluid px-0">
    <header class="ah-hero">
      <img src="images/assets/banner-cafe.jpg" alt="Coffee bar counter with warm lighting" class="ah-hero-image">
      <div class="ah-hero-overlay"></div>
      <div class="container ah-hero-content">
        <p class="ah-hero-kicker mb-2">Contact Us</p>
        <h1 class="ah-hero-title mb-3">Let's make your next cup better, together.</h1>
        <p class="ah-hero-lead mb-4">Questions about beans, brewing, or your order? Reach out and we will get back to you with clear, practical support.</p>
        <div class="d-flex flex-wrap gap-3">
          <a href="#contact-form" class="btn btn-primary ah-contact-hero-cta">Send a Message</a>
          <a href="shop-coffee.php" class="btn btn-outline-primary ah-contact-hero-cta">Shop Coffee</a>
        </div>
      </div>
    </header>
  </section>

  <section class="container ah-contact-content" aria-labelledby="contact-info-heading">
    <div class="row g-4 g-xl-5 align-items-start">
      <div class="col-12 col-lg-5">
        <article class="ah-contact-panel" id="contact-info-heading">
          <p class="ah-contact-kicker mb-2">Reach Us</p>
          <h2 class="ah-contact-title mb-3">Talk to the Aroma Haven team.</h2>
          <p class="ah-contact-copy mb-4">We reply Monday to Saturday and usually respond within one business day.</p>
          <ul class="ah-contact-list list-unstyled mb-0" aria-label="Contact channels">
            <li>
              <span class="ah-contact-label">Email</span>
              <a href="mailto:hello@aromahaven.sg">hello@aromahaven.sg</a>
            </li>
            <li>
              <span class="ah-contact-label">Phone</span>
              <a href="tel:+6561234567">+65 6123 4567</a>
            </li>
            <li>
              <span class="ah-contact-label">Instagram</span>
              <a href="https://www.instagram.com/aromahaven" target="_blank" rel="noopener noreferrer" aria-label="Open Aroma Haven Instagram page">@aromahaven</a>
            </li>
          </ul>
        </article>

        <article class="ah-contact-panel ah-contact-hours mt-4" aria-labelledby="contact-hours-heading">
          <h3 id="contact-hours-heading" class="ah-contact-subtitle mb-3">Support Hours</h3>
          <ul class="ah-contact-hours-list list-unstyled mb-0">
            <li><span>Mon - Fri</span><strong>9:00 AM - 6:00 PM</strong></li>
            <li><span>Saturday</span><strong>10:00 AM - 4:00 PM</strong></li>
            <li><span>Sunday</span><strong>Closed</strong></li>
          </ul>
        </article>
      </div>

      <div class="col-12 col-lg-7">
        <section id="contact-form" class="ah-contact-form-wrap" aria-labelledby="contact-form-title">
          <p class="ah-contact-kicker mb-2">Message Us</p>
          <h2 id="contact-form-title" class="ah-contact-title mb-2">Send a direct enquiry</h2>
          <p class="ah-contact-copy mb-4">Frontend-only form for now. We can wire backend submission later.</p>

          <form class="ah-contact-form" action="contact-us.php#contact-form" method="post">
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="contact-name" class="form-label">Full name</label>
                <input id="contact-name" name="name" type="text" class="form-control" autocomplete="name" required>
              </div>
              <div class="col-12 col-md-6">
                <label for="contact-email" class="form-label">Email address</label>
                <input id="contact-email" name="email" type="email" class="form-control" autocomplete="email" required>
              </div>
              <div class="col-12">
                <label for="contact-topic" class="form-label">Topic</label>
                <select id="contact-topic" name="topic" class="form-select" required>
                  <option value="" selected disabled>Select a topic</option>
                  <option value="beans">Bean recommendations</option>
                  <option value="brew">Brewing help</option>
                  <option value="order">Order support</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="col-12">
                <label for="contact-message" class="form-label">Message</label>
                <textarea id="contact-message" name="message" class="form-control ah-contact-textarea" rows="6" required></textarea>
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="yes" id="contact-consent" required>
                  <label class="form-check-label" for="contact-consent">
                    I agree to be contacted about this enquiry.
                  </label>
                </div>
              </div>
              <div class="col-12 d-flex flex-wrap align-items-center gap-3">
                <button type="submit" class="btn btn-primary">Submit enquiry</button>
                <p class="ah-contact-note mb-0">No data is sent yet until backend wiring is added.</p>
              </div>
            </div>
          </form>
        </section>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
