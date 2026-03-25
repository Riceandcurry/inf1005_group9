<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function shutdown_debug() {
    $error = error_get_last();
    if ($error !== NULL) {
        echo "<pre>";
        print_r($error);
        echo "</pre>";
    }
}
register_shutdown_function('shutdown_debug');
require_once __DIR__ . '/../backend/register.php';
//require_once __DIR__ . '/../backend/login_process.php';
session_start();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

switch ($method) {
    case 'POST':
        switch ($action) {
            case 'register':
                $result = register_process($_POST);
                if(empty($result)){
                    $_SESSION['msg'] = "Account Created Sucessfully!";
                    header("Location: login.php");
                    exit;  
                }
                else{                                 
                    $_SESSION['error'] = $result;
                    header("Location: register.php");
                    exit;
                }

            default:
                $result = "Unknown POST action.";
                break;
        }
        break;

    case 'GET':
        switch($action){
            case 'login':
                break;
               
        }        
    default:
        $result = "Unsupported HTTP method: $method";
}

// for Debugging
if (is_string($result)) {
    echo $result;
} else {
    header('Content-Type: application/json');
    echo json_encode($result);
}