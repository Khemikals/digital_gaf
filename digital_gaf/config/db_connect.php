<?php
// Database Configuration
$servername = "localhost";
$username   = "root";         // Default XAMPP username
$password   = "";             // Default XAMPP password
$dbname     = "digital_gaf";  // The name you confirmed in your phpMyAdmin

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and stop execution if it fails
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for Ghana Card special characters
$conn->set_charset("utf8mb4");
?>