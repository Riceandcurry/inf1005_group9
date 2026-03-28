<?php

require_once __DIR__ . '/../backend/init.php';
require_once __DIR__ . '/../backend/register.php';
require_once __DIR__ . '/../backend/login.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$action = $_POST['action'] ?? '';
if (empty($_SESSION['csrf_token']) ||!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
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
            session_regenerate_id(true); // security
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['login_attempts'] = 0;
            header("Location: shop-coffee.php");
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
        session_destroy();
        header("Location: login.php");
        exit;

    default:
        http_response_code(400);
        echo "Invalid action.";
        exit;
}