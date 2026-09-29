<?php
session_start();
include 'db_connect.php';

// Security Gate: Only logged-in Officers can view security logs
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { 
    header("Location: admin_login.php"); 
    exit(); 
}

// Fetch the last 200 attempts to monitor for brute-force attacks
// Ensure the table 'civilian_access_logs' exists
$query = "SELECT * FROM civilian_access_logs ORDER BY attempt_time DESC LIMIT 200";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Civilian Security Monitor | GAF</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
    body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; margin: 0; }

    /* Header */
    .sec-header { 
        background: #1a1a1a; 
        color: #ff4d4d; 
        border-bottom: 4px solid var(--gaf-gold); 
        padding: 10px 20px; 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        flex-wrap: wrap;
    }
    .header-left { display: flex; align-items: center; flex-wrap: wrap; }
    .header-logo { margin-right: 15px; width: 60px; height: auto; }
    .header-title { font-weight: 800; text-transform: uppercase; margin: 5px 0; font-size: 1.2rem; letter-spacing: 1px; }

    /* Live Clock Styling */
    #liveClock { 
        font-family: 'Courier New', monospace; 
        color: var(--gaf-gold); 
        font-weight: bold; 
        font-size: 1rem; 
        background: rgba(255,255,255,0.1); 
        padding: 5px 10px; 
        border-radius: 5px; 
    }

    /* Table Container */
    .table-container { background: white; border-radius: 10px; overflow-x: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

    /* Responsive Table */
    table { min-width: 700px; }
    th, td { white-space: nowrap; }

    /* Buttons */
    .btn { font-size: 0.85rem; }

    @media (max-width: 768px) {
        .header-title { font-size: 1rem; margin-top: 5px; }
        #liveClock { font-size: 0.9rem; padding: 3px 8px; margin-top: 5px; }
        .container-fluid { padding-left: 10px; padding-right: 10px; }
    }
</style>
</head>
<body>

<div class="sec-header">
    <div class="header-left">
        <a href="dashboard.php">
            <img src="assets/img/GAFB.jpg" class="header-logo" alt="GAF Crest">
        </a>
        <h3 class="header-title">
            <i class="fas fa-user-secret me-2"></i>GAF-DRSS Civilian Access & IP Intelligence
        </h3>
    </div>
    <div id="liveClock">00:00:00</div>
</div> 

<div class="container-fluid mt-4">
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success py-2 small shadow-sm"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div class="d-flex flex-wrap gap-2">
            <a href="dashboard.php" class="btn btn-sm btn-outline-dark"><i class="fas fa-arrow-left"></i> Command Center</a>
            
            <?php if(isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'SuperAdmin'): ?>
                <a href="clear_security_logs.php" class="btn btn-sm btn-danger fw-bold" 
                   onclick="return confirm('CRITICAL WARNING: This will permanently wipe all civilian access logs. This action is irreversible. Proceed?');">
                    <i class="fas fa-trash-alt me-1"></i> CLEAR ALL LOGS
                </a>
            <?php else: ?>
                <button class="btn btn-sm btn-secondary fw-bold" disabled>
                    <i class="fas fa-lock me-1"></i> CLEAR LOGS (RESTRICTED)
                </button>
            <?php endif; ?>

        </div>
        <div class="text-muted small mt-2 mt-md-0">
            <span class="spinner-grow spinner-grow-sm text-danger me-1"></span> Live Monitoring Active
        </div>
    </div>

    <div class="card table-container border-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">Attempt Date & Time</th>
                    <th>Voucher Serial</th>
                    <th>IP Address (Intelligence)</th>
                    <th>Access Status</th>
                    <th class="text-center">Threat Level</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-3 small"><?= date('d M Y | H:i:s', strtotime($row['attempt_time'])) ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['voucher_serial']) ?></td>
                        <td>
                            <code><?= $row['ip_address'] ?></code>
                            <a href="https://whois.domaintools.com/<?= $row['ip_address'] ?>" target="_blank" class="ms-2 badge bg-light text-dark text-decoration-none border">Trace</a>
                        </td>
                        <td>
                            <?php if($row['login_status'] == 'Succeeded'): ?>
                                <span class="badge bg-success py-2 px-3"><i class="fas fa-check-circle me-1"></i> SUCCEEDED</span>
                            <?php else: ?>
                                <span class="badge bg-danger py-2 px-3"><i class="fas fa-times-circle me-1"></i> DENIED</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($row['login_status'] == 'Denied'): ?>
                                <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> Caution</span>
                            <?php else: ?>
                                <span class="text-success small">Normal</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No security logs recorded in database.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('liveClock').textContent = `${hours}:${minutes}:${seconds}`;
}
setInterval(updateClock, 1000);
updateClock();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>