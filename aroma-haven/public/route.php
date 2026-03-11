<?php
require_once __DIR__ . '/../backend/register.php';
//require_once __DIR__ . '/../backend/login_process.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

switch ($method) {
    case 'POST':
        switch ($action) {
            case 'register':
                $result = register_process($_POST);
                break;
            default:
                $result = "Unknown POST action.";
        }
        break;

    case 'GET':
        break;
    default:
        $result = "Unsupported HTTP method: $method";
}

// Output the result
if (is_string($result)) {
    echo $result;
} else {
    header('Content-Type: application/json');
    echo json_encode($result);
}