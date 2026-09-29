<?php
include 'config/db_connect.php';

// 1. Define your first Admin credentials
$admin_user = 'GAF_ADMIN_01'; // Your desired username
$admin_pass = 'Command2025!'; // Your desired password

// 2. Hash the password securely
// This creates a 60+ character string that cannot be reversed
$hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);

// 3. Insert into the database
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $admin_user, $hashed_pass);

echo "<div style='font-family:sans-serif; padding:20px; border:2px solid #3d4621; background:#f0f0f0;'>";
echo "<h2 style='color:#3d4621;'>GAF System: Admin Creation</h2>";

try {
    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ <b>Success!</b> Admin account <b>$admin_user</b> created.</p>";
        echo "<p>Password Hashed as: <code style='background:#ddd;'>$hashed_pass</code></p>";
        echo "<hr><p style='color:red;'><b>SECURITY WARNING:</b> Delete this file (register_admin.php) from your server immediately!</p>";
        echo "<a href='login.php' style='padding:10px 20px; background:#3d4621; color:white; text-decoration:none; border-radius:5px;'>Proceed to Login</a>";
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        echo "<p style='color:orange;'>⚠️ Account already exists.</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}
echo "</div>";
?>