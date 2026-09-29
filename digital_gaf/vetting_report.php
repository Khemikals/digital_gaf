<?php
session_start();
require_once 'config/db_connect.php';

/**
 * OFFICER PRODUCTIVITY REPORT
 * Synced with application_status ENUM:
 * Pending | Shortlisted | Scheduled | Qualified | Rejected
 */

/* =========================
   OFFICER-LEVEL AGGREGATION
========================= */
$sql = "
    SELECT 
        vetted_by,
        COUNT(*) AS total_processed,
        SUM(CASE WHEN application_status = 'Qualified' THEN 1 ELSE 0 END) AS approved_count,
        SUM(CASE WHEN application_status = 'Rejected' THEN 1 ELSE 0 END) AS rejected_count
    FROM applicants
    WHERE vetted_by IS NOT NULL
      AND vetted_by != ''
      AND application_status IN ('Qualified','Rejected')
    GROUP BY vetted_by
    ORDER BY total_processed DESC
";

$result = $conn->query($sql);

/* =========================
   CHART + TABLE DATA
========================= */
$chart_labels     = [];
$chart_totals     = [];
$chart_approvals  = [];
$table_data       = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $table_data[]      = $row;
        $chart_labels[]    = $row['vetted_by'];
        $chart_totals[]    = (int) $row['total_processed'];
        $chart_approvals[] = (int) $row['approved_count'];
    }
}

/* =========================
   GLOBAL TOTALS
========================= */
$totals_sql = "
    SELECT
        COUNT(*) AS grand_total,
        SUM(CASE WHEN application_status = 'Qualified' THEN 1 ELSE 0 END) AS total_approved,
        SUM(CASE WHEN application_status = 'Rejected' THEN 1 ELSE 0 END) AS total_rejected
    FROM applicants
";

$totals = $conn->query($totals_sql)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vetting Analytics | GAF-DRSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .gaf-nav { background: var(--gaf-olive); border-bottom: 5px solid var(--gaf-gold); color: white; padding: 15px; }
        .stat-card { border-left: 5px solid var(--gaf-gold); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .chart-container { background: white; padding: 20px; border-radius: 8px; border-top: 4px solid var(--gaf-olive); }
    </style>
</head>
<body>

<div class="gaf-nav text-center fw-bold text-uppercase">
    <img src="assets/img/GAFB.jpg" style="width: 35px; margin-right: 10px;">
    GAF Command Center: Vetting Productivity Report
</div>

<div class="container mt-5">
    <div class="row mb-4 text-center">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm p-3">
                <h6 class="text-muted small">GRAND TOTAL APPLICATIONS</h6>
                <h2 class="fw-bold"><?php echo $totals['grand_total']; ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm p-3 border-success">
                <h6 class="text-success small">TOTAL APPROVED (FINAL)</h6>
                <h2 class="fw-bold text-success"><?php echo $totals['total_approved']; ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm p-3 border-danger">
                <h6 class="text-danger small">TOTAL REJECTED/FLAGGED</h6>
                <h2 class="fw-bold text-danger"><?php echo $totals['total_rejected']; ?></h2>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <div class="chart-container shadow-sm">
                <h5 class="fw-bold mb-4 text-uppercase small text-muted">
                    <i class="fas fa-chart-bar me-2"></i> Officer Productivity Visualization
                </h5>
                <canvas id="officerChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 mb-5">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 small text-uppercase"><i class="fas fa-list-check me-2"></i> Detailed Performance Audit</h5>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Back to Dashboard</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Officer Signature / Staff ID</th>
                        <th class="text-center">Total Decisions</th>
                        <th class="text-center">Approvals</th>
                        <th class="text-center">Rejections</th>
                        <th class="text-center">Approval Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($table_data as $row): 
                        $rate = ($row['total_processed'] > 0) ? round(($row['approved_count'] / $row['total_processed']) * 100, 1) : 0;
                    ?>
                    <tr>
                        <td class="ps-3 fw-bold">
                            <i class="fas fa-user-shield me-2 text-muted"></i> <?php echo htmlspecialchars($row['vetted_by']); ?>
                        </td>
                        <td class="text-center fw-bold text-primary"><?php echo $row['total_processed']; ?></td>
                        <td class="text-center text-success"><?php echo $row['approved_count']; ?></td>
                        <td class="text-center text-danger"><?php echo $row['rejected_count']; ?></td>
                        <td class="text-center pe-3">
                            <div class="progress" style="height: 12px; border-radius: 10px;">
                                <div class="progress-bar bg-success" style="width: <?php echo $rate; ?>%"></div>
                            </div>
                            <small class="text-muted fw-bold"><?php echo $rate; ?>% Approval</small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="text-center text-muted small mt-5 mb-4">
    &copy; 2025 Ghana Armed Forces Secure Analytics Engine
</footer>

<script>
    const ctx = document.getElementById('officerChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Total Files Processed',
                data: <?php echo json_encode($chart_totals); ?>,
                backgroundColor: '#3d4621',
                borderColor: '#d4af37',
                borderWidth: 1
            }, {
                label: 'Final Approvals',
                data: <?php echo json_encode($chart_approvals); ?>,
                backgroundColor: '#198754',
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { position: 'top', labels: { font: { weight: 'bold' } } }
            }
        }
    });
</script>

</body>
</html>
