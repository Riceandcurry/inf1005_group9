<?php
require 'util.php';

function register_process($data){    
    $conn = connect_db();
    if($conn->connect_error)
        die("Connection Failed:".$conn->connect_error);        


    $errorMsg= "";        
    $fname = sanitize_input($data['first_name']);
    $lname = sanitize_input($data['last_name']);
    $email = sanitize_input($data['email']);
    $pw_hash = password_hash(sanitize_input($data['password']),PASSWORD_DEFAULT);
    $brew_style = $data['brew_method'];

    if (empty($email))
        $errorMsg .= "Email is required.<br>";
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errorMsg .= "Invalid email format.";

    if(empty($fname))
        $errorMsg .= "First Name is required.<br>";            
    if(empty($lname))
        $errorMsg .= "Last Name is required.<br>";            
    if(empty($data['password']))
        $errorMsg .= "Password is required.<br>";
    if(empty($brew_style))
        $errorMsg .= "Please select a brewstyle.<br>";          

    if(empty($errorMsg)){
        try {
            $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, password, brew_style_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $fname, $lname, $email, $pw_hash, $brew_style);
            $stmt->execute();            
        } catch (mysqli_sql_exception $e) {            
            echo $errorMsg = $e->getMessage();
            return;
        }
        $stmt->close();
        $conn->close();
    }
    return $errorMsg;

}
?>