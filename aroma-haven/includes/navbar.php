<?php
require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/admin_helpers.php';

$ahCurrentUserId = ah_current_user_id();
$ahLoggedIn = $ahCurrentUserId > 0;
$ahIsAdmin = $ahLoggedIn && ah_user_is_admin($ahCurrentUserId);
$ahUserIconHref = $ahLoggedIn
  ? ($ahIsAdmin ? 'admin-dashboard.php' : 'shop-coffee.php')
  : 'login.php';
$ahUserIconLabel = $ahLoggedIn
  ? ($ahIsAdmin ? 'Open admin dashboard' : 'Open account')
  : 'Go to login';
?>
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
        <?php if ($ahIsAdmin): ?>
          <a href="admin-dashboard.php" class="ah-nav-link text-uppercase small" style="letter-spacing:0.08em;">Admin</a>
        <?php endif; ?>
        <?php
        require_once __DIR__ . '/../backend/init.php';
        if (isset($auth) && $auth->isLogged()) {
        ?>
          <a href="/profile.php" class="ah-nav-icon" title="Profile Settings" aria-label="Go to profile" style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;object-fit:contain;">
            <img src="images/assets/profilesetting.png" alt="Profile Settings" style="width:26px;height:26px;object-fit:contain;" />
          </a>
        <?php }
        ?>
        <a href="cart.php" class="ah-nav-icon" data-cart-trigger title="Open cart" aria-label="Open cart">
          <img src="images/assets/shopping-bag.png" alt="" aria-hidden="true" style="width:36px;height:36px;object-fit:contain;">
        </a>
        <a href="<?php echo htmlspecialchars($ahUserIconHref, ENT_QUOTES, 'UTF-8'); ?>" class="ah-nav-icon" title="Account" aria-label="<?php echo htmlspecialchars($ahUserIconLabel, ENT_QUOTES, 'UTF-8'); ?>">
          <img src="images/assets/user.png" alt="" aria-hidden="true" style="width:36px;height:36px;object-fit:contain;">
        </a>
      </div>
    </div>
  </div>
</nav>
<div id="ah-page-content" tabindex="-1"></div>
