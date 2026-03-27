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

switch ($action) {

    case 'login':
        $result = login_process($_POST);
        if (empty($result)) {
            session_regenerate_id(true); // security
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
        session_destroy();
        header("Location: login.php");
        exit;

    default:
        http_response_code(400);
        echo "Invalid action.";
        exit;
}