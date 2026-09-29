<?php 
session_start(); 
include 'db_connect.php';

/**
 * COMMAND PORTAL: audit_logs.php
 * SECURITY GATE: Ensure only authorized officers can view the system's security logs.
 */
// 1. UPDATED SESSION CHECK
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { 
    header("Location: admin_login.php"); 
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Security Intelligent Audit Logs | GAF Command Intelligent Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style> 
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #f8f9fa; padding: 20px; font-family: 'Segoe UI', sans-serif; } 
        .table-header { background-color: var(--gaf-olive) !important; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-user-shield me-2 text-dark"></i> Security Audit Logs</h2>
        <a href="dashboard.php" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-header">
                        <tr>
                            <th><i class="fas fa-clock me-2"></i> Time (GMT)</th>
                            <th><i class="fas fa-user me-2"></i> Username Attempted</th>
                            <th><i class="fas fa-network-wired me-2"></i> IP Address</th>
                            <th><i class="fas fa-tasks me-2"></i> Action / Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 2. UPDATED QUERY to use 'log_time' instead of 'login_time'
                        $sql = "SELECT * FROM access_logs ORDER BY log_time DESC LIMIT 100";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                
                                // Dynamic Status Styling based on the 'action' column
                                $action_text = htmlspecialchars($row['action']);
                                $color = 'text-dark';
                                $icon = '<i class="fas fa-info-circle"></i>';

                                if (stripos($action_text, 'Success') !== false) {
                                    $color = 'text-success';
                                    $icon = '<i class="fas fa-check-circle"></i>';
                                } elseif (stripos($action_text, 'Failed') !== false || stripos($action_text, 'Error') !== false) {
                                    $color = 'text-danger';
                                    $icon = '<i class="fas fa-exclamation-triangle"></i>';
                                }
                                
                                // Format Date
                                $date_display = date("d-M-Y H:i:s", strtotime($row['log_time']));

                                echo "<tr>
                                    <td class='small font-monospace'>$date_display</td>
                                    <td class='fw-bold'>" . htmlspecialchars($row['username']) . "</td>
                                    <td class='text-muted small'>" . htmlspecialchars($row['ip_address']) . "</td>
                                    <td class='$color fw-bold small'>$icon " . $action_text . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted py-4'>No security logs recorded yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<footer class="text-center text-muted mt-5 small">
    &copy; <?php echo date('Y'); ?> Ghana Armed Forces. Official Security Intelligent Audit.
</footer>

</body>
</html>