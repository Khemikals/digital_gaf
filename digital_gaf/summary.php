<?php
session_start();
include 'config/db_connect.php';

/**
 * SEQUENTIAL FLOW STEP 16: summary.php
 * FINAL STEP: This is the end of the applicant's journey.
 */

// Security Gate: Ensure the applicant has an ID and has reached the end
if (!isset($_SESSION['applicant_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['applicant_id'];

// Fetch the permanent, finalized record for this applicant
$query = "SELECT * FROM applicants WHERE applicant_id = '$id'";
$result = $conn->query($query);
$app = $result->fetch_assoc();

if (!$app) {
    die("Error: Final application record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Summary | GAF-DRSS Final</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #f4f4f4; font-family: 'Segoe UI', sans-serif; }
        .summary-card { background: white; border-top: 10px solid var(--gaf-olive); border-radius: 8px; padding: 40px; }
        .slip-header { border-bottom: 2px solid #eee; margin-bottom: 25px; padding-bottom: 15px; }
        .passport-img { width: 150px; height: 150px; object-fit: cover; border: 3px solid var(--gaf-gold); }
        .data-label { font-weight: bold; color: #555; font-size: 0.85rem; text-transform: uppercase; }
        .data-value { font-size: 1.1rem; color: #000; margin-bottom: 15px; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; opacity: 0.03; z-index: 0; pointer-events: none; }
        .content-layer { position: relative; z-index: 1; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .summary-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
            .container { max-width: 100% !important; width: 100% !important; margin: 0 !important; }
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="summary-card shadow-lg mx-auto position-relative" style="max-width: 850px;">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/a/a2/Ghana_Armed_Forces_seal.svg/1200px-Ghana_Armed_Forces_seal.svg.png" class="watermark">

        <div class="content-layer">
            <div class="slip-header d-flex align-items-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Coat_of_arms_of_Ghana.svg/100px-Coat_of_arms_of_Ghana.svg.png" style="width: 70px; margin-right: 20px;">
                <div>
                    <h2 class="fw-bold mb-0">GHANA ARMED FORCES</h2>
                    <h5 class="text-muted small">Digital Recruitment & Scheduling System (DRSS)</h5>
                </div>
                <div class="ms-auto text-end">
                    <div class="badge bg-success p-2 mb-2">APPLICATION FINALIZED</div>
                    <div class="fw-bold">REF: GAF-<?php echo str_pad($app['applicant_id'], 5, '0', STR_PAD_LEFT); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 text-center border-end">
                    <?php if(!empty($app['passport_path'])): ?>
                        <img src="<?php echo $app['passport_path']; ?>" class="passport-img rounded shadow-sm mb-3">
                    <?php endif; ?>
                    <div class="data-label">Ghana Card (NIA)</div>
                    <div class="fw-bold"><?php echo $app['ghana_card']; ?></div>
                </div>

                <div class="col-md-9 ps-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="data-label">Full Name</div>
                            <div class="data-value"><?php echo strtoupper($app['first_name'] . " " . $app['last_name']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Branch Choice</div>
                            <div class="data-value text-success fw-bold"><?php echo strtoupper($app['branch_choice']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">JHS Index Number</div>
                            <div class="data-value"><?php echo $app['jhs_index']; ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">SHS Index Number</div>
                            <div class="data-value"><?php echo $app['shs_index']; ?></div>
                        </div>
                    </div>

                    <div class="p-3 mt-3 rounded" style="background: #fff8e1; border: 1px solid var(--gaf-gold);">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-map-marker-alt me-2"></i> OFFICIAL REPORTING DETAILS</h6>
                        <div class="row">
                            <div class="col-7">
                                <div class="data-label">Screening Landmark</div>
                                <div class="fw-bold small"><?php echo $app['screening_center']; ?></div>
                            </div>
                            <div class="col-5">
                                <div class="data-label">Reporting Date</div>
                                <div class="fw-bold text-danger"><?php echo date('D, d-M-Y', strtotime($app['screening_date'])); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4 py-2 small no-print">
                <i class="fas fa-info-circle me-2"></i> <strong>Note:</strong> Your application is now securely logged in the GAF database. Please print this slip for physical verification.
            </div>

            <div class="mt-5 pt-3 border-top no-print d-flex justify-content-between">
                <button onclick="window.print()" class="btn btn-dark btn-lg px-5 shadow">
                    <i class="fas fa-print me-2"></i> PRINT FINAL SLIP
                </button>
                <a href="logout.php" class="btn btn-outline-danger btn-lg px-5 shadow-sm">LOGOUT & EXIT PORTAL</a>
            </div>
            
            <p class="text-center text-muted mt-4 x-small" style="font-size: 0.7rem;">
                Official Digital Recruitment Summary. Valid for the 2025 Enlistment Intake only.
            </p>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>