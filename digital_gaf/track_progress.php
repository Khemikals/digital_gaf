<?php
session_start();
include 'config/db_connect.php';

$status_result = null;
$error = null;

// ==================================================
// 1. Login & Track Applicant via Voucher Serial + PIN
// ==================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['serial_no']) && isset($_POST['pin_code'])) {

    // Sanitize inputs
    $serial_input = $conn->real_escape_string(trim($_POST['serial_no']));
    $pin_input = $conn->real_escape_string(trim($_POST['pin_code']));
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // ------------------------------
    // Allow vouchers that are "Used" AND linked to applicant_id
    // ------------------------------
    $v_sql = "
        SELECT * FROM vouchers 
        WHERE serial_number = '$serial_input'
          AND pin_code = '$pin_input'
          AND applicant_id IS NOT NULL
        LIMIT 1
    ";
    $v_result = $conn->query($v_sql);

    if ($v_result && $v_result->num_rows > 0) {
        $voucher = $v_result->fetch_assoc();
        $app_id = $voucher['applicant_id'];

        // Fetch applicant info
        $a_sql = "SELECT * FROM applicants WHERE applicant_id = '$app_id' LIMIT 1";
        $a_result = $conn->query($a_sql);

        if ($a_result && $a_result->num_rows > 0) {
            $app = $a_result->fetch_assoc();

            // Determine final status: Qualified only if application_status = Qualified/Approved AND medical_status = Fit
            $final_status = 'Disqualified';
            if (in_array($app['application_status'], ['Qualified', 'Approved']) && ($app['medical_status'] ?? '') === 'Fit') {
                $final_status = 'Qualified';
            }

            $status_result = [
                'applicant_id'       => $app['applicant_id'],
                'first_name'         => $app['first_name'],
                'last_name'          => $app['last_name'],
                'ghana_card_number'  => $app['ghana_card'],
                'phone_number'       => $app['phone_number'],
                'branch_choice'      => $app['branch_choice'] ?? 'N/A',
                'screening_center'   => $app['screening_center'] ?? 'Pending Allocation',
                'screening_date'     => $app['screening_date'] ?? 'Pending Date',
                'application_status' => $app['application_status'],
                'medical_status'     => $app['medical_status'] ?? 'Pending',
                'final_status'       => $final_status
            ];

            // Store session for convenience
            $_SESSION['applicant_authenticated'] = true;
            $_SESSION['applicant_id'] = $app['applicant_id'];

        } else {
            $error = "Applicant record not found for this voucher.";
        }

    } else {
        $error = "Invalid Serial Number/PIN or voucher not linked to an application.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Tracking | GAF-DRSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .track-card { max-width: 700px; margin: 40px auto; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .track-header { background: var(--gaf-olive); color: white; padding: 25px; border-bottom: 5px solid var(--gaf-gold); text-align: center; }
        .stepper { display: flex; justify-content: space-between; position: relative; margin-bottom: 30px; }
        .stepper::before { content: ""; position: absolute; top: 15px; left: 0; width: 100%; height: 2px; background: #ddd; z-index: 1; }
        .step { position: relative; z-index: 2; text-align: center; width: 33%; }
        .step-icon { width: 30px; height: 30px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; font-size: 14px; color: white; }
        .step.completed .step-icon { background: #198754; }
        .step.active .step-icon { background: var(--gaf-gold); }
        .step.failed .step-icon { background: #dc3545; }
    </style>
</head>
<body>

<div class="container">
    <div class="card track-card">
        <div class="track-header">
            <img src="assets/img/GAFB.jpg" width="80" class="mb-3 rounded-circle border border-white">
            <h4 class="fw-bold mb-0">Recruitment Progress Tracker</h4>
            <p class="small mb-0 opacity-75">GAF Digital Recruitment Support System</p>
        </div>
        <div class="card-body p-5">

            <?php if (!$status_result): ?>
                <div class="text-center mb-4">
                    <p class="text-muted small">Enter your voucher serial number and PIN to track your application status.</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 small text-center fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold">Voucher Serial Number</label>
                        <input type="text" name="serial_no" class="form-control text-center fw-bold text-uppercase" placeholder="GAF-26-XXXXXX" required>
                    </div>
                    <div class="mb-4 text-start">
                        <label class="form-label small fw-bold">Voucher PIN</label>
                        <input type="text" name="pin_code" class="form-control text-center fw-bold" placeholder="PIN Code" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold rounded-pill">
                        CHECK STATUS <i class="fas fa-search ms-2"></i>
                    </button>
                    <div class="text-center mt-3">
                        <a href="index.php" class="small text-decoration-none text-muted">Back to Home</a>
                    </div>
                </form>

            <?php else: ?>
                <div class="text-center mb-4">
                    <h5 class="text-uppercase text-muted mb-1">Applicant Info</h5>
                    <h3 class="fw-bold"><?= strtoupper($status_result['first_name'] . " " . $status_result['last_name']) ?></h3>
                    <p class="small mb-0"><strong>Ghana Card:</strong> <?= htmlspecialchars($status_result['ghana_card_number']) ?></p>
                    <p class="small mb-0"><strong>Branch:</strong> <?= strtoupper($status_result['branch_choice']) ?></p>
                    <p class="small mb-0"><strong>Screening Center:</strong> <?= strtoupper($status_result['screening_center']) ?></p>
                    <p class="small mb-0"><strong>Screening Date:</strong> <?= $status_result['screening_date'] ?></p>
                </div>

                <div class="stepper">
                    <div class="step completed">
                        <div class="step-icon"><i class="fas fa-file-invoice"></i></div>
                        <small class="fw-bold text-success">Submitted</small>
                    </div>

                    <?php
                        $step2_class = in_array($status_result['application_status'], ['Qualified','Approved']) ? 'completed' : 'active';
                        $step3_class = ($status_result['final_status'] == 'Qualified') ? 'completed' : (($status_result['final_status'] == 'Disqualified') ? 'failed' : 'active');
                    ?>
                    <div class="step <?= $step2_class ?>">
                        <div class="step-icon"><i class="fas fa-user-shield"></i></div>
                        <small class="fw-bold <?= ($step2_class=='active')?'text-warning':'text-success' ?>">Vetting</small>
                    </div>

                    <div class="step <?= $step3_class ?>">
                        <div class="step-icon"><i class="fas fa-check-double"></i></div>
                        <small class="fw-bold <?= ($step3_class=='completed')?'text-success':($step3_class=='failed'?'text-danger':'text-muted') ?>">Decision</small>
                    </div>
                </div>

                <hr>

                <?php if ($status_result['final_status'] == 'Qualified'): ?>
                    <div class="alert alert-success border-0 shadow-sm py-3 mb-4 text-start">
                        <h5 class="fw-bold"><i class="fas fa-check-circle me-2"></i> QUALIFIED</h5>
                        <p class="small mb-0">You have passed vetting and medical clearance. Please await your screening schedule.</p>
                    </div>

                <?php else: ?>
                    <div class="alert alert-danger border-0 shadow-sm py-3 mb-4 text-start">
                        <h5 class="fw-bold"><i class="fas fa-times-circle me-2"></i> DISQUALIFIED</h5>
                        <p class="small mb-0">Unfortunately, you did not meet the requirements or failed medical clearance.</p>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="track_progress.php" class="small text-decoration-none text-muted"><i class="fas fa-redo-alt me-1"></i> Check Another Application</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
