<?php



function login_process($data){
    global $auth;

    $email = $data['email'];
    $password = $data['password'];

    $result = $auth->login($email, $password);

    if($result['error']){
        return $result['message'];
    }

    return "";
}
?>