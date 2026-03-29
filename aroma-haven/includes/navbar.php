<a class="ah-global-skip-link" href="#ah-page-content">Skip to main content</a>

<nav class="navbar navbar-expand-lg ah-navbar py-0">
  <div class="container-fluid px-0">
    <a class="navbar-brand d-flex flex-column align-items-center justify-content-center ah-brand m-0" href="index.php">
      <img src="images/assets/aroma_haven_logo.png" alt="Aroma Haven logo" class="ah-brand-logo">
      <span class="ah-brand-text">Aroma Haven</span>
    </a>
    <button class="navbar-toggler ah-navbar-toggler me-3" type="button" data-bs-toggle="collapse" data-bs-target="#ahNav" aria-controls="ahNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="ahNav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-4">
        <li class="nav-item"><a class="nav-link ah-nav-link" href="shop-coffee.php">Shop Coffee</a></li>
        <li class="nav-item"><a class="nav-link ah-nav-link" href="product.php">Coffee Guides</a></li>
        <li class="nav-item"><a class="nav-link ah-nav-link" href="about-us.php">Our Story</a></li>
        <li class="nav-item"><a class="nav-link ah-nav-link" href="contact-us.php">Contact Us</a></li>
      </ul>
      <div class="d-flex align-items-center gap-4 mb-3 mb-lg-0">
        <?php
        require_once __DIR__ . '/../backend/init.php';
        if (isset($auth) && $auth->isLogged()) {
        ?>
          <a href="/profile.php" class="ah-nav-icon" title="Profile Settings" aria-label="Go to profile" style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#f0f0f0;">
            <!-- Bootstrap gear SVG icon, forced black color -->
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#222" class="bi bi-gear" viewBox="0 0 16 16">
              <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492z"/>
              <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
            </svg>
          </a>
        <?php }
        ?>
        <a href="cart.php" class="ah-nav-icon" data-cart-trigger title="Open cart" aria-label="Open cart">
          <img src="images/assets/shopping-bag.png" alt="" aria-hidden="true" style="width:36px;height:36px;object-fit:contain;">
        </a>
        <a href="login.php" class="ah-nav-icon" title="Login" aria-label="Go to login">
          <img src="images/assets/user.png" alt="" aria-hidden="true" style="width:36px;height:36px;object-fit:contain;">
        </a>
      </div>
    </div>
  </div>
</nav>
<div id="ah-page-content" tabindex="-1"></div>
