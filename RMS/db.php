<?php
$host = 'localhost';
$user = 'root';
$password = '';  // default for XAMPP
$dbname = 'cpr_system'; // replace with your actual DB name

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
