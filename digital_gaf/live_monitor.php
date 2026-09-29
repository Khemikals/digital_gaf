<?php
session_start();
// FIX: Point to the main db_connect.php
include 'db_connect.php'; 

/**
 * COMMAND PORTAL: live_monitor.php
 * SECURITY GATE: Only logged-in officers can access the real-time arrival monitor.
 */
// FIX: Updated to match 'admin_login_process.php' session variable
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

/**
 * 1. FETCH RECENT CHECK-INS
 * Joins gate_logs with applicants to display name, ID, and passport photos.
 * Uses LEFT JOIN to ensure stability even if applicant data is partial.
 */
$sql = "SELECT g.check_in_time, g.officer_username, a.first_name, a.last_name, 
               a.passport_path, a.screening_center, a.applicant_id 
        FROM gate_logs g 
        LEFT JOIN applicants a ON g.applicant_id = a.applicant_id 
        ORDER BY g.check_in_time DESC LIMIT 15";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="10"> 
    <title>Live Entry Monitor | GAF-DRSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #0f1108; color: white; font-family: 'Segoe UI', sans-serif; }
        .monitor-header { background: var(--gaf-olive); border-bottom: 4px solid var(--gaf-gold); padding: 15px; position: sticky; top: 0; z-index: 1000; }
        .live-indicator { height: 12px; width: 12px; background-color: #ff0000; border-radius: 50%; display: inline-block; animation: blink 1s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
        .log-card { background: #1a1d12; border-left: 5px solid var(--gaf-gold); margin-bottom: 10px; transition: 0.3s; border-right: none; border-top: none; border-bottom: none; }
        .log-card:first-child { background: #24291a; transform: scale(1.01); border-left-color: #28a745; }
        .timestamp { color: var(--gaf-gold); font-family: 'Courier New', Courier, monospace; font-weight: bold; font-size: 1.1rem; }
        .officer-tag { font-size: 0.7rem; background: rgba(255,255,255,0.05); padding: 3px 10px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); }
        .tally-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); transition: 0.2s; }
        .tally-box:hover { background: rgba(212, 175, 55, 0.1); }
        .passport-holder { width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; background: #333; border-radius: 50%; border: 2px solid #555; }
    </style>
</head>
<body>

<div class="monitor-header d-flex justify-content-between align-items-center px-4 shadow">
    <h4 class="mb-0 fw-bold"><i class="fas fa-satellite-dish me-2"></i> GAF-DRSS LIVE SENTRY FEED</h4>
    <div class="text-end">
        <span class="live-indicator me-1"></span> <small class="text-uppercase fw-bold text-danger">Active Link</small>
        <div class="small opacity-75">Local Time: <?php echo date('H:i:s'); ?> GMT</div>
    </div>
</div>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h6 class="text-uppercase mb-3 fw-bold" style="color: var(--gaf-gold); letter-spacing: 2px;">
                <i class="fas fa-map-marked-alt me-2"></i> Regional Distribution (Today)
            </h6>
            <div class="row g-2">
                <?php
                // Tallying logic synchronized with applicant screening center data
                $tally_sql = "SELECT a.screening_center, COUNT(g.log_id) as arrivals 
                              FROM gate_logs g 
                              JOIN applicants a ON g.applicant_id = a.applicant_id 
                              WHERE DATE(g.check_in_time) = CURDATE()
                              GROUP BY a.screening_center 
                              ORDER BY arrivals DESC";
                
                // Safety check: Ensure query runs even if table is empty/missing
                $tally_res = $conn->query($tally_sql);
                
                if ($tally_res && $tally_res->num_rows > 0):
                    while($tally = $tally_res->fetch_assoc()): ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-2 rounded tally-box d-flex justify-content-between align-items-center">
                                <small class="text-truncate fw-bold" style="max-width: 140px; color: #ccc;">
                                    <?php echo htmlspecialchars($tally['screening_center'] ?? 'N/A'); ?>
                                </small>
                                <span class="badge bg-success rounded-pill"><?php echo $tally['arrivals']; ?></span>
                            </div>
                        </div>
                    <?php endwhile;
                else: ?>
                    <div class="col-12"><small class="text-muted italic"><i class="fas fa-clock me-1"></i> Waiting for first regional check-in of the day...</small></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-uppercase" style="color: var(--gaf-gold); letter-spacing: 1px;">Sentry Log History</h5>
                <a href="dashboard.php" class="btn btn-sm btn-outline-warning rounded-pill px-3">EXIT MONITOR</a>
            </div>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card log-card shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="timestamp me-4">
                                <?php echo date('H:i', strtotime($row['check_in_time'])); ?>
                            </div>
                            
                            <?php if(!empty($row['passport_path']) && file_exists($row['passport_path'])): ?>
                                <img src="<?php echo htmlspecialchars($row['passport_path']); ?>" class="rounded-circle border border-2 border-secondary me-3 shadow" style="width: 55px; height: 55px; object-fit: cover;">
                            <?php else: ?>
                                <div class="passport-holder me-3"><i class="fas fa-user text-muted"></i></div>
                            <?php endif; ?>

                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold"><?php echo strtoupper(($row['last_name'] ?? 'Unknown') . ", " . ($row['first_name'] ?? 'Applicant')); ?></h6>
                                <small class="text-muted">
                                    REF: GAF-<?php echo str_pad($row['applicant_id'] ?? 0, 5, '0', STR_PAD_LEFT); ?> 
                                    <span class="mx-2">|</span> 
                                    CENTER: <span class="text-white"><?php echo htmlspecialchars($row['screening_center'] ?? 'N/A'); ?></span>
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="officer-tag text-uppercase text-gold">Sentry ID: <?php echo htmlspecialchars($row['officer_username']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 opacity-25">
                    <i class="fas fa-radar fa-4x mb-3"></i>
                    <p class="h5">No real-time arrival data detected.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>