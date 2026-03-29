<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/util.php';

$conn = connect_db();

$config = new PHPAuth\Config($conn);


$config->site_url = "https://aromahaven.duckdns.org";
$config->cookie_domain = "aromahaven.duckdns.org";     
$config->secure = true;
$config->cookie_secure = true;
$auth = new PHPAuth\Auth($conn, $config);
?>
