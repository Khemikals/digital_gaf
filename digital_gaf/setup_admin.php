<?php
<link rel="stylesheet" href="style.css">
include 'db_connect.php';


// 1. The password we want to use
$username = "commander";
$password = "admin123"; 

// 2. Hash it! (This turns 'admin123' into a scrambled string like $2y$10$...)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 3. Delete old users to avoid confusion
$conn->query("DELETE FROM users");

// 4. Insert the new SECURE user
$sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "<h1>✅ Secure Admin Created</h1>";
    echo "<p>Username: <b>$username</b></p>";
    echo "<p>Password: <b>$password</b></p>";
    echo "<p>Stored Hash: <b>$hashed_password</b> (Check your database now!)</p>";
} else {
    echo "Error: " . $conn->error;
}
?>