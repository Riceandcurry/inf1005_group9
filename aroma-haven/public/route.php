<?php
require_once __DIR__ . '/../backend/init.php';
require_once __DIR__ . '/../backend/register.php';
require_once __DIR__ . '/../backend/login.php';
require_once __DIR__ . '/../backend/otp.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}
$action = $_POST['action'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die("Invalid CSRF token");
}
switch ($action) {
    case 'login':
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] > 5) {
            die("Too many attempts. Try again later.");
        }
        $result = login_process($_POST);
        if (empty($result)) {
            $_SESSION['csrf_token']     = bin2hex(random_bytes(32));
            $_SESSION['login_attempts'] = 0;
            $userId    = $auth->getCurrentUID();
            $userRow   = $auth->getCurrentUser();
            $userEmail = $userRow['email'] ?? '';
            if (empty($userEmail) || $userId === 0) {
                $conn      = connect_db();
                $stmt      = $conn->prepare("SELECT id, email FROM phpauth_users WHERE email = ? LIMIT 1");
                $stmt->execute([$_POST['email'] ?? '']);
                $row       = $stmt->fetch(PDO::FETCH_ASSOC);
                $userId    = (int) ($row['id'] ?? 0);
                $userEmail = $row['email'] ?? '';
            }
            $_SESSION['otp_pending_user_id'] = $userId;
            $_SESSION['otp_pending_email']   = $userEmail;
            $_SESSION['otp_verified'] = false;
            otp_generate_and_send($userId, $userEmail);
            header("Location: verify-otp.php");
            exit;
        } else {
            $_SESSION['error'] = $result;
            header("Location: login.php");
            exit;
        }
    case 'register':
        $result = register_process($_POST);
        if (empty($result)) {
            $_SESSION['msg'] = "Account created successfully!";
            header("Location: login.php");
            exit;
        } else {
            $_SESSION['error'] = $result;
            header("Location: register.php");
            exit;
        }
    case 'logout':
        if (isset($_COOKIE['phpauth_session_cookie'])) {
            $auth->logout($_COOKIE['phpauth_session_cookie']);
        }
        unset($_SESSION['otp_pending_user_id'], $_SESSION['otp_pending_email'], $_SESSION['otp_verified']);
        session_destroy();
        header("Location: login.php");
        exit;
    default:
        http_response_code(400);
        echo "Invalid action.";
        exit;
}
