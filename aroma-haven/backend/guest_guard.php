<?php
require_once __DIR__ . '/init.php';

function require_guest() {
    global $auth;

    if ($auth->isLogged()) {
        header("Location: shop-coffee.php");
        exit;
    }
}
?>