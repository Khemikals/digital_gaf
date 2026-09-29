<?php
session_start();
include 'db_connect.php';

// 1. SECURITY CHECK
// We check two things: Are they logged in? AND Is their role 'SuperAdmin'?
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'SuperAdmin') {
    // If they are just an 'Officer', kill the script with this message
    die("<h2 style='color:red; text-align:center; margin-top:50px;'>⛔ ACCESS DENIED: COMMAND AUTHORIZATION REQUIRED</h2>");
}

// 2. EXECUTE WIPE
if ($conn->query("TRUNCATE TABLE civilian_access_logs")) {
    
    // 3. AUDIT THE WIPE (Record who did it)
    $admin = $_SESSION['admin_username'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $conn->query("INSERT INTO access_logs (username, ip_address, action) 
                  VALUES ('$admin', '$ip', 'CRITICAL: WIPED CIVILIAN SECURITY LOGS')");
    
    header("Location: civilian_logs.php?msg=Security logs cleared successfully.");
} else {
    echo "Error clearing logs: " . $conn->error;
}
exit();
?>