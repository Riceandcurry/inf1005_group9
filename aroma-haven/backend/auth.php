<?php
require __DIR__ . '/../vendor/autoload.php';
require_once 'util.php';

$conn = connect_db();

$config = new PHPAuth\Config($conn);
$auth   = new PHPAuth\Auth($conn, $config);

