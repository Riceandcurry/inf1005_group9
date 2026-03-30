<?php
$pageTitle = 'Aroma Haven | Verify Login';
$bodyClass = 'ah-auth-body';

require_once __DIR__ . '/../backend/init.php';
require_once __DIR__ . '/../backend/otp.php';
require_once __DIR__ . '/../backend/admin_helpers.php';

if ($auth->isLogged() && empty($_SESSION['otp_pending_user_id'])) {
    header('Location: shop-coffee.php');
    exit;
}

if (empty($_SESSION['otp_pending_user_id'])) {
    header('Location: login.php');
    exit;
}

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'])) {
    $userId = (int) $_SESSION['otp_pending_user_id'];
    $email  = $_SESSION['otp_pending_email'] ?? '';
    if ($email) {
        otp_generate_and_send($userId, $email);
        $success = true;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code   = trim($_POST['otp_code'] ?? '');
    $userId = (int) $_SESSION['otp_pending_user_id'];

    if (empty($code)) {
        $error = 'Please enter the verification code.';
    } elseif (!preg_match('/^\d{6}$/', $code)) {
        $error = 'Code must be 6 digits.';
    } elseif (!otp_verify($userId, $code)) {
        $error = 'Invalid or expired code. Please try again.';
    } else {
        $email = $_SESSION['otp_pending_email'];
        $conn = connect_db();
        $userStmt = $conn->prepare("SELECT id FROM phpauth_users WHERE email = ? LIMIT 1");
        $userStmt->execute([$email]);
        $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
        $uid = (int) ($userRow['id'] ?? 0);

        if ($uid > 0) {
            if (ah_is_user_suspended($uid)) {
                unset($_SESSION['otp_pending_user_id'], $_SESSION['otp_pending_email']);
                session_regenerate_id(true);
                $_SESSION['error'] = 'Your account is suspended. Please contact support.';
                header('Location: login.php');
                exit;
            }

            $sessionHash = substr(bin2hex(random_bytes(24)), 0, 40);
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $sessionStmt = $conn->prepare(
                "INSERT INTO phpauth_sessions (uid, hash, expiredate, ip, agent, cookie_crc) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $ip    = $_SERVER['REMOTE_ADDR'] ?? '';
            $sessionStmt->execute([$uid, $sessionHash, $expiry, $ip, $agent, sha1($sessionHash . 'fghuior.)/!/jdUkd8s2!7HVHG7777ghg')]);
            setcookie('phpauth_session_cookie', $sessionHash, strtotime('+1 hour'), '/', 'aromahaven.duckdns.org', true, true);
            $_SESSION['phpauth_session_cookie'] = $sessionHash;
            $_SESSION['phpauth_session_cookie_expire'] = strtotime('+1 hour');
        }

        unset($_SESSION['otp_pending_user_id']);
        unset($_SESSION['otp_pending_email']);
        session_regenerate_id(true);
        header('Location: shop-coffee.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main id="main-content" class="ah-auth-page">
  <section class="ah-auth-layout">
    <aside class="ah-auth-media" aria-hidden="true">
      <img src="./images/assets/auth-visual.jpg" alt="">
      <div class="ah-auth-media-copy">
        <p class="ah-auth-media-kicker">Aroma Haven</p>
        <p class="ah-auth-media-title">One last step.</p>
        <p class="ah-auth-media-note">We sent a code to your email to keep your account secure.</p>
      </div>
    </aside>

    <section class="ah-auth-panel-wrap" aria-labelledby="otp-title">
      <div class="ah-auth-topbar">
        <a href="login.php" class="ah-auth-back">&larr; Back to Login</a>
        <span class="ah-auth-brand">Aroma Haven</span>
      </div>

      <article class="ah-auth-panel">
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
          <input type="hidden" name="resend" value="1">
          <button type="submit" class="btn btn-link ah-auth-link p-0">Resend code</button>
        </form>
      </article>
    </section>
  </section>
</main>

</body>
</html>
