<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../backend/auth.php';


$email = "test@example.com";
$password = "password123";

$result = $auth->register($email, $password,$password);

echo "<pre>";
print_r($result);
echo "</pre>";
?>