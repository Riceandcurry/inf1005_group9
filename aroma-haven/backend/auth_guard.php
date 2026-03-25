<?php
require_once 'auth.php';
session_start();

function require_login(){
    global $auth;

    if (!isset($_SESSION['user_id'], $_SESSION['hash'])) {
        header("Location: login.php");
        exit;
    }

    $check = $auth->checkSession($_SESSION['hash']);

    if (!$check) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
}