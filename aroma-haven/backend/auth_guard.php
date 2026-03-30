<?php

require_once __DIR__ . '/init.php';

function require_login() {
    global $auth;

    if (!$auth->isLogged()) {
        $_SESSION['error'] = "Please log in first.";
        header("Location: login.php");
        exit;
    }
}
?>
