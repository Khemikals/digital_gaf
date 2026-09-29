<?php

session_start();
include __DIR__ . '/../config/db_connect.php';
 // Updated path to match your config folder

// If already logged in, redirect to dashboard
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: /dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    // CAPTURE THE IP ADDRESS
    $ip = $_SERVER['REMOTE_ADDR'];
    if ($ip == '::1') { $ip = '127.0.0.1'; } 

    // 1. Prepare Query (Security Patch included)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    $login_status = "Failure"; 

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Use secure password verification
        if (password_verify($pass, $row['password'])) {
            // SUCCESS
            $login_status = "Success";
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user;
            $_SESSION['role'] = 'officer'; // Define role for access control
        } else {
            $error = "❌ Invalid Password";
        }
    } else {
        $error = "❌ Invalid Username";
    }

    // --- AUDIT LOGGING (The Surveillance Camera) ---
    $log_stmt = $conn->prepare("INSERT INTO access_logs (username_attempted, ip_address, status) VALUES (?, ?, ?)");
    $log_stmt->bind_param("sss", $user, $ip, $login_status);
    $log_stmt->execute();

    // If success, redirect to the Dashboard
    if ($login_status == "Success") {
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GAF Officer Portal | Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #1a1a1a; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .login-box { background: #262626; padding: 40px; border-radius: 15px; width: 400px; text-align: center; border: 2px solid var(--gaf-gold); box-shadow: 0px 10px 30px rgba(0,0,0,0.5); color: white; }
        .form-control { background: #1a1a1a; border: 1px solid #444; color: white; }
        .form-control:focus { background: #1a1a1a; border-color: var(--gaf-gold); color: white; box-shadow: none; }
        .btn-gaf { background-color: var(--gaf-gold); color: black; font-weight: bold; border: none; }
        .btn-gaf:hover { background-color: #bfa032; }
    </style>
</head>
<body>

<div class="login-box">
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Coat_of_arms_of_Ghana.svg/200px-Coat_of_arms_of_Ghana.svg.png" width="80" class="mb-3">
    <h4 class="fw-bold mb-1">COMMAND INTELLIGENCE CENTER</h4>
    <p class="text-muted small mb-4">OFFICER AUTHORIZATION REQUIRED</p>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label class="small fw-bold text-muted mb-1">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fas fa-user"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Staff ID / Username" required>
            </div>
        </div>
        <div class="mb-4 text-start">
            <label class="small fw-bold text-muted mb-1">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>
        <button type="submit" name="login" class="btn btn-gaf w-100 py-2">ACCESS COMMAND INTELLIGENCE CENTER</button>
    </form>
    
    <div class="mt-4">
        <a href="index.php" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Return to Public Portal</a>
    </div>
</div>

</body>
</html>