<?php
session_start();
include 'db_connect.php';

// Initialize variables for UI state
$status = ""; 
$message = "";

// --- CONFIGURATION: SET YOUR SECRET CODE HERE ---
$SUPER_ADMIN_CODE = "GAF-ALPHA-1"; 
// -----------------------------------------------

if (isset($_POST['register_admin'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['reg_fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['reg_email']);
    $username = mysqli_real_escape_string($conn, $_POST['reg_username']);
    $password = mysqli_real_escape_string($conn, $_POST['reg_password']);
    $svc_number = mysqli_real_escape_string($conn, $_POST['reg_svc_number']);
    
    // Capture the secret code input
    $secret_input = $_POST['reg_secret_code'] ?? ''; 

    // 1. DETERMINE ROLE
    // Default role is 'Officer'
    $role = 'Officer';
    
    // If the input matches your secret code, upgrade them to SuperAdmin
    if ($secret_input === $SUPER_ADMIN_CODE) {
        $role = 'SuperAdmin';
    }

    // 2. CHECK DUPLICATES
    $check = $conn->query("SELECT * FROM admins WHERE username='$username' OR email='$email'");
    
    if ($check->num_rows > 0) {
        $status = "error";
        $message = "Access Denied: Officer username or email is already registered.";
    } else {
        // 3. HASH PASSWORD
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. INSERT WITH ROLE
        // Since you already have the 'role' column, this will work perfectly.
        $sql = "INSERT INTO admins (full_name, email, username, password, service_number, role) 
                VALUES ('$full_name', '$email', '$username', '$hashed_password', '$svc_number', '$role')";

        if ($conn->query($sql)) {
            $status = "success";
            // Custom message based on role
            if ($role === 'SuperAdmin') {
                $message = "High Command Profile created. You have SuperAdmin privileges.";
            } else {
                $message = "Officer profile created successfully. Access granted.";
            }
        } else {
            $status = "error";
            $message = "Database Error: " . $conn->error;
        }
    }
} else {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAF | Registration Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #1a1a1a; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .status-card { background: #fff; border-radius: 12px; border-top: 6px solid var(--gaf-gold); width: 100%; max-width: 400px; padding: 40px; text-align: center; }
        .btn-command { background-color: var(--gaf-olive); color: white; font-weight: bold; }
        .icon-box { font-size: 50px; margin-bottom: 20px; }
        .text-success-custom { color: var(--gaf-olive); }
    </style>
</head>
<body>

<div class="status-card">
    <img src="assets/img/GAFB.jpg" style="width: 40px; margin-bottom: 15px;">
    
    <?php if ($status == 'success'): ?>
        <div class="icon-box text-success-custom"><i class="fas fa-check-circle"></i></div>
        <h4 class="fw-bold text-success-custom">REGISTRATION COMPLETE</h4>
        <p class="text-muted mb-4"><?php echo $message; ?></p>
        <a href="admin_login.php" class="btn btn-command w-100">PROCEED TO LOGIN</a>
    <?php else: ?>
        <div class="icon-box text-danger"><i class="fas fa-exclamation-triangle"></i></div>
        <h4 class="fw-bold text-danger">REGISTRATION FAILED</h4>
        <p class="text-muted mb-4"><?php echo $message; ?></p>
        <a href="admin_login.php" class="btn btn-outline-secondary w-100">RETURN TO LOGIN</a>
    <?php endif; ?>
</div>

</body>
</html>