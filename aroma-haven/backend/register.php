<?php
require 'util.php';
require_once 'auth.php';

function register_process($data){

    global $auth, $conn;

    $email = sanitize_input($data['email']);
    $password = $data['password'];
    $fname = sanitize_input($data['first_name']);
    $lname = sanitize_input($data['last_name']);
    $brew_style = isset($data['brew_method']) ? (int)$data['brew_method'] : null;

    $result = $auth->register($email, $password, $password);

    if($result['error']){
        return $result['message'];
    }

    $user_id = $result['uid'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO user_profiles (user_id, fname, lname, brew_style_id)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$user_id, $fname, $lname, $brew_style]);

    } catch (PDOException $e) {
        return $e->getMessage();
    }

    return "";
}
?>