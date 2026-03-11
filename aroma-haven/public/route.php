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
                if(empty($result)){
                    $_SESSION['msg'] = "Account Created Sucessfully!";
                    header("Location: login.php");
                    exit;  
                }
                else{ 
                    //echo session_status();                
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