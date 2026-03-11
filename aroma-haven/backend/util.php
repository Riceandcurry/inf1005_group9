<?php
    require __DIR__ . '/../vendor/autoload.php';
    function connect_db(){
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $db   = $_ENV['DB_NAME'];
        $port = $_ENV['DB_PORT'];
        $conn = new mysqli($host, $user, $pass, $db, $port);
        return $conn;
    }
    function sanitize_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>