<?php
require __DIR__ . '/../vendor/autoload.php';
require_once 'util.php';

$conn = connect_db();

$config = new PHPAuth\Config($conn);


$config->site_url = "http://35.212.194.207";
$config->cookie_domain = "35.212.194.207";     
$config->cookie_path = "/";
$config->secure = false;
$config->cookie_secure = false;
$auth = new PHPAuth\Auth($conn, $config);
?>