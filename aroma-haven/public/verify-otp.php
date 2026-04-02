<?php
$pageTitle = 'Aroma Haven | Verify Login';
$bodyClass = 'ah-auth-body';

require_once __DIR__ . '/../backend/init.php';
require_once __DIR__ . '/../backend/otp.php';
require_once __DIR__ . '/../backend/admin_helpers.php';

if ($auth->isLogged() && empty($_SESSION['otp_pending_user_id']) && (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified'] === true)) {
    header('Location: shop-coffee.php');
    exit;
}

if (!$auth->isLogged()) {
    header('Location: login.php');
    exit;
}

$pendingUserId = (int) ($_SESSION['otp_pending_user_id'] ?? 0);
$currentUserId = (int) $auth->getCurrentUID();

if ($pendingUserId <= 0 || $pendingUserId !== $currentUserId) {
    unset($_SESSION['otp_pending_user_id'], $_SESSION['otp_pending_email'], $_SESSION['otp_verified']);
    $_SESSION['error'] = 'Your verification session has expired. Please log in again.';
    if (isset($_COOKIE['phpauth_session_cookie'])) {
        $auth->logout($_COOKIE['phpauth_session_cookie']);
    }
    header('Location: login.php');
    exit;
}

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedCsrf = (string) ($_POST['csrf_token'] ?? '');
    if (empty($_SESSION['csrf_token']) || !hash_equals((string) $_SESSION['csrf_token'], $postedCsrf)) {
        $error = 'Invalid request token. Please refresh and try again.';
    } elseif (isset($_POST['resend'])) {
        $userId = $pendingUserId;
        $email  = $_SESSION['otp_pending_email'] ?? '';
        if ($email) {
            otp_generate_and_send($userId, $email);
            $success = true;
        }
    } else {
        $code   = trim($_POST['otp_code'] ?? '');
        $userId = $pendingUserId;

        if (empty($code)) {
            $error = 'Please enter the verification code.';
        } elseif (!preg_match('/^\d{6}$/', $code)) {
            $error = 'Code must be 6 digits.';
        } elseif (!otp_verify($userId, $code)) {
            $error = 'Invalid or expired code. Please try again.';
        } else {
            if (ah_is_user_suspended($currentUserId)) {
                unset($_SESSION['otp_pending_user_id'], $_SESSION['otp_pending_email'], $_SESSION['otp_verified']);
                if (isset($_COOKIE['phpauth_session_cookie'])) {
                    $auth->logout($_COOKIE['phpauth_session_cookie']);
                }
                session_regenerate_id(true);
                $_SESSION['error'] = 'Your account is suspended. Please contact support.';
                header('Location: login.php');
                exit;
            }

            $_SESSION['otp_verified'] = true;
            unset($_SESSION['otp_pending_user_id'], $_SESSION['otp_pending_email']);
            session_regenerate_id(true);
            header('Location: shop-coffee.php');
            exit;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main id="main-content" class="ah-auth-page">
  <div class="ah-auth-layout">
    <aside class="ah-auth-media" aria-hidden="true">
      <img src="./images/assets/auth-visual.jpg" alt="">
      <div class="ah-auth-media-copy">
        <p class="ah-auth-media-kicker">Aroma Haven</p>
        <p class="ah-auth-media-title">One last step.</p>
        <p class="ah-auth-media-note">We sent a code to your email to keep your account secure.</p>
      </div>
    </aside>

    <div class="ah-auth-panel-wrap">
      <div class="ah-auth-topbar">
        <a href="login.php" class="ah-auth-back">&larr; Back to Login</a>
        <span class="ah-auth-brand">Aroma Haven</span>
      </div>

      <div class="ah-auth-panel">
        <h1 id="otp-title" class="ah-auth-title">Check your email.</h1>
        <p class="ah-auth-subtitle">
          We sent a 6-digit code to
          <strong><?php echo htmlspecialchars($_SESSION['otp_pending_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>.
          It expires in 10 minutes.
        </p>

        <?php if ($success): ?>
          <div class="alert alert-success mb-3" role="alert">A new code has been sent.</div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
          <p class="ah-auth-error mb-3" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form action="verify-otp.php" method="post" class="ah-auth-form">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
          <div>
            <label for="otp_code" class="ah-auth-label form-label mb-1">Verification Code</label>
            <input
              type="text"
              id="otp_code"
              name="otp_code"
              class="form-control ah-auth-input"
              placeholder="123456"
              inputmode="numeric"
              maxlength="6"
              autocomplete="one-time-code"
              required
            >
          </div>

          <button type="submit" class="btn btn-primary w-100 ah-auth-btn">Verify</button>
        </form>

        <form action="verify-otp.php" method="post" class="mt-3 text-center">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="resend" value="1">
          <button type="submit" class="btn btn-link ah-auth-link p-0">Resend code</button>
        </form>
      </div>
    </div>
  </div>
</main>

</body>
</html>
