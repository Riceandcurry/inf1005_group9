<?php
require_once __DIR__."/../backend/util.php"; 
$conn = connect_db();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";


$result = $conn->query("SHOW DATABASES;");
while($row = $result->fetch_assoc()) {
    echo "<br>" . $row['Database'];
}
?>

//TEST LOCAL CONNECTION TO DB