<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

$host = "localhost";       // MySQL server (same machine)
$user = "root";            // Default XAMPP MySQL username
$password = "";            // Default XAMPP has no password
$database = "exam_system"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
