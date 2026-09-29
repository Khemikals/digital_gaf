<?php
session_start();
include 'db_connect.php';

$step = 1; // 1 = Check Email, 2 = Set New Password, 3 = Success, 4 = Error
$email_input = "";
$error_msg = "";

// --- STEP 1: HANDLE EMAIL CHECK ---
if (isset($_POST['request_reset'])) {
    $email_input = mysqli_real_escape_string($conn, $_POST['reset_email']);
    
    // Check if email exists in admins table
    $check = $conn->query("SELECT * FROM admins WHERE email='$email_input'");
    
    if ($check->num_rows > 0) {
        // Email found, move to Step 2 (Set New Password)
        $step = 2;
    } else {
        $step = 4;
        $error_msg = "Command Error: The email address provided is not linked to any officer account.";
    }
}

// --- STEP 2: HANDLE PASSWORD UPDATE ---
if (isset($_POST['save_new_password'])) {
    $target_email = mysqli_real_escape_string($conn, $_POST['target_email']);
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_password']);
    
    // Hash new password
    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
    
    // Update Database
    $update = $conn->query("UPDATE admins SET password='$hashed_password' WHERE email='$target_email'");
    
    if ($update) {
        $step = 3;
    } else {
        $step = 4;
        $error_msg = "System Error: Could not update credentials. " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAF | Credential Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #1a1a1a; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .reset-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border-top: 6px solid var(--gaf-gold);
            width: 100%; max-width: 400px; padding: 40px;
        }
        .btn-command { background-color: var(--gaf-olive); color: white; font-weight: bold; letter-spacing: 1px; transition: 0.3s; }
        .btn-command:hover { background-color: #2a3116; color: var(--gaf-gold); }
    </style>
</head>
<body>

<div class="reset-card text-center">
    <img src="assets/img/GAFB.jpg" style="width: 35px; margin-right: 10px; margin-bottom: 20px;">
    
    <h5 class="fw-bold mb-4 text-uppercase">CREDENTIAL RECOVERY</h5>

    <?php if ($step == 2): ?>
        <div class="alert alert-success py-2 small">
            <i class="fas fa-check-circle me-1"></i> Identity Verified.
        </div>
        <p class="text-muted small mb-3">Please establish a new command password for <strong><?php echo htmlspecialchars($email_input); ?></strong></p>
        
        <form method="POST">
            <input type="hidden" name="target_email" value="<?php echo htmlspecialchars($email_input); ?>">
            <div class="text-start mb-4">
                <label class="form-label small fw-bold text-muted">NEW PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-key"></i></span>
                    <input type="password" name="new_password" class="form-control bg-light border-start-0" placeholder="New Password" required>
                </div>
            </div>
            <button type="submit" name="save_new_password" class="btn btn-command w-100 py-2 shadow-sm">
                UPDATE CREDENTIALS
            </button>
        </form>

    <?php elseif ($step == 3): ?>
        <div class="mb-4 text-success" style="font-size: 50px;"><i class="fas fa-shield-alt"></i></div>
        <h5 class="fw-bold text-success">RESET SUCCESSFUL</h5>
        <p class="text-muted small">Your credentials have been updated in the secure registry.</p>
        <a href="admin_login.php" class="btn btn-command w-100 py-2 mt-3">RETURN TO LOGIN</a>

    <?php elseif ($step == 4): ?>
        <div class="mb-4 text-danger" style="font-size: 50px;"><i class="fas fa-times-circle"></i></div>
        <h5 class="fw-bold text-danger">VERIFICATION FAILED</h5>
        <p class="text-muted small"><?php echo $error_msg; ?></p>
        <a href="admin_login.php" class="btn btn-outline-secondary w-100 py-2 mt-3">BACK TO PORTAL</a>

    <?php endif; ?>
</div>

</body>
</html>