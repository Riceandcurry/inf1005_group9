<?php
require_once 'auth.php';
session_start();

function login_process($data){
    global $auth;

    $email = $data['email'];
    $password = $data['password'];

    $result = $auth->login($email, $password);

    if($result['error']){
        return $result['message'];
    }
    
    $_SESSION['user_id'] = $result['uid'];
    $_SESSION['hash'] = $result['hash'];

    return "";
}