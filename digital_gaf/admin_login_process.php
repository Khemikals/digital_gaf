<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// FIX: Changed path from 'includes/db_connect.php' to 'db_connect.php'
include 'db_connect.php';

if (isset($_POST['admin_login'])) {
    // Check if connection works before proceeding
    if (!isset($conn)) {
        die("Database connection failed. Please check db_connect.php");
    }

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; 

    // 1. Fetch the user details based on Username ONLY first
    $sql = "SELECT * FROM admins WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // 2. Verify the hashed password
        if (password_verify($password, $row['password'])) {
            
            // --- SUCCESSFUL LOGIN ---
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_username'] = $row['username'];
            // Load the actual role from the database ('Officer' or 'SuperAdmin')
            $_SESSION['admin_role'] = $row['role'];

            // 3. Record login in access_logs
            // Check if access_logs table exists first to prevent crash
            $check_table = $conn->query("SHOW TABLES LIKE 'access_logs'");
            if($check_table->num_rows > 0) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $admin_id = $row['admin_id'];
                $log_sql = "INSERT INTO access_logs (admin_id, username, ip_address, action, log_time) 
                            VALUES ('$admin_id', '$username', '$ip', 'Login Success', NOW())";
                $conn->query($log_sql);
            }

            // Redirect to Dashboard
            header("Location: dashboard.php");
            exit();

        } else {
            // --- WRONG PASSWORD ---
            echo "<script>
                alert('Command Error: Invalid Password credentials.');
                window.location='admin_login.php';
            </script>";
        }
    } else {
        // --- USERNAME NOT FOUND ---
        echo "<script>
            alert('Command Error: Officer username not found in registry.');
            window.location='admin_login.php';
        </script>";
    }
} else {
    header("Location: admin_login.php");
    exit();
}
?>