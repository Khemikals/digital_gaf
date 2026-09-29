<?php
session_start();
include 'config/db_connect.php'; 

if (isset($_POST['login_voucher'])) {
    // 1. Capture and Sanitize
    $serial = trim($_POST['serial']);
    $pin = trim($_POST['pin']);
    
    // CAPTURE IP ADDRESS FOR SECURITY LOGS
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // 2. Prepared Statement
    $stmt = $conn->prepare("SELECT * FROM vouchers WHERE serial_number = ? AND pin_code = ? AND status = 'active'");
    $stmt->bind_param("ss", $serial, $pin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $voucher = $result->fetch_assoc();
        
        // --- LOG SUCCESSFUL ATTEMPT ---
        $log_stmt = $conn->prepare("INSERT INTO civilian_access_logs (voucher_serial, ip_address, login_status) VALUES (?, ?, 'Succeeded')");
        $log_stmt->bind_param("ss", $serial, $ip_address);
        $log_stmt->execute();

        // 3. Establish Authentication Session
        $_SESSION['applicant_authenticated'] = true;
        
        // 4. Store Serial for Step 3 linking
        $_SESSION['voucher_serial'] = $serial;
        
        // 5. Sequential Redirect to Step 1
        header("Location: /apply_step1.php");

        exit();
    } else {
        // --- LOG DENIED ATTEMPT (Hacker Check) ---
        $log_stmt = $conn->prepare("INSERT INTO civilian_access_logs (voucher_serial, ip_address, login_status) VALUES (?, ?, 'Denied')");
        $log_stmt->bind_param("ss", $serial, $ip_address);
        $log_stmt->execute();

        // 6. Detailed Error Feedback
        echo "<script>
                alert('AUTHENTICATION FAILED: Invalid Serial/PIN or Voucher already used.'); 
                window.location='index.php';
              </script>";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>