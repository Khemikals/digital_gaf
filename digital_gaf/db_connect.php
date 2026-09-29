<?php
// InfinityFree Database Configuration
$servername = "sql310.infinityfree.com"; // FROM CONTROL PANEL
$username   = "if0_40548664";             // FROM CONTROL PANEL
$password   = "rOYg3UQ4zs";        // FROM CONTROL PANEL
$dbname     = "if0_40548664_GAF
";  // FROM CONTROL PANEL

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
