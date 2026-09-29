<?php
session_start();

/**
 * SEQUENTIAL FLOW STEP 5 (Branch B): disqualified.php
 * Terminal notice with QR Verification and Ref No.
 */

// SECURITY GATE: Prevent direct access without authentication
if (!isset($_SESSION['applicant_authenticated'])) {
    header("Location: index.php");
    exit();
}

// 1. CAPTURE DATA BEFORE SESSION DESTRUCTION
$first_name = $_SESSION['first_name'] ?? 'Applicant';
$last_name = $_SESSION['last_name'] ?? '';
$full_name = strtoupper($first_name . " " . $last_name);
$passport = $_SESSION['passport_path'] ?? 'assets/img/default-avatar.png';
$reason = $_SESSION['disqualified_reason'] ?? "Minimum entry requirements (FR7) not met.";
$ghana_card = $_SESSION['ghana_card'] ?? 'N/A';
$date_generated = date('d-M-Y H:i:s');

/**
 * LOGIC SYNC: Use the same Reference Number from the session.
 * This ensures the ID matches across all system documents.
 */
if (!isset($_SESSION['final_ref_no'])) {
    $branch_initial = strtoupper(substr($_SESSION['branch_choice'] ?? 'X', 0, 1));
    $random_id = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['final_ref_no'] = "GAF-" . $branch_initial . "-" . $random_id;
}
$ref_no = $_SESSION['final_ref_no'];

// 2. GENERATE TERMINATION QR CODE
$qr_data = "GAF-REJECTED:" . $ref_no . "|NAME:" . $first_name . "|CARD:" . $ghana_card;
$qr_api  = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($qr_data);

/**
 * SECURITY LOGIC: Clear the session
 * The session is destroyed now that we have captured all display variables.
 */
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Termination Notice | GAF Enlistment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-red: #8b0000; --gaf-olive: #3d4621; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .status-box { max-width: 750px; margin: 40px auto; border: none; border-top: 10px solid var(--gaf-red); border-radius: 10px; }
        .passport-img { width: 130px; height: 130px; object-fit: cover; border: 3px solid var(--gaf-olive); border-radius: 5px; }
        .reason-box { background-color: #fff5f5; border-left: 5px solid var(--gaf-red); padding: 15px; text-align: left; }
        .qr-code { width: 110px; border: 1px solid #ddd; padding: 5px; background: #fff; }
        @media print {
            .no-print { display: none !important; }
            body { background-color: white; }
            .status-box { margin: 0; box-shadow: none !important; border: 1px solid #ddd; border-top: 10px solid var(--gaf-red); }
        }
    </style>
</head>
<body class="bg-light">

<div class="container">
    <div class="card status-box shadow-lg p-4 bg-white">
        <div class="row align-items-center mb-4">
            <div class="col-8">
                <img src="assets/img/GAFB.jpg" style="width: 75px;"><br>
                <h4 class="fw-bold text-dark mb-1 text-uppercase">Ghana Armed Forces</h4>
                <p class="text-uppercase small mb-2 text-muted" style="letter-spacing: 1px;">Official Recruitment Terminal Notice</p>
                <div class="badge bg-danger p-2 px-3">STATUS: REJECTED / DISQUALIFIED</div>
            </div>
            <div class="col-4 text-end">
                <img src="<?php echo htmlspecialchars($passport); ?>" class="passport-img shadow-sm" alt="Applicant Passport">
            </div>
        </div>

        <hr>

        <div class="row mb-4 text-start">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <small class="text-muted text-uppercase d-block fw-bold" style="font-size: 0.7rem;">Full Name</small>
                <span class="fw-bold fs-5"><?php echo htmlspecialchars($full_name); ?></span>
                <p class="text-primary fw-bold mt-1 mb-0">Ref No: <?php echo $ref_no; ?></p>
            </div>
            <div class="col-sm-6">
                <small class="text-muted text-uppercase d-block fw-bold" style="font-size: 0.7rem;">Ghana Card Number</small>
                <span class="fw-bold"><?php echo htmlspecialchars($ghana_card); ?></span>
            </div>
        </div>
        
        <div class="reason-box mb-4">
            <h6 class="fw-bold text-danger mb-1 text-uppercase small"><i class="fas fa-exclamation-triangle me-2"></i> Termination Reason:</h6>
            <p class="mb-0 text-dark"><?php echo htmlspecialchars($reason); ?></p>
        </div>

        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <div class="alert alert-secondary text-start small mb-0">
                    <h6 class="fw-bold mb-2">NOTICE ON ENTRY STANDARDS (FR7):</h6>
                    <ul class="mb-0">
                        <li><strong>Height:</strong> Min 1.65m (M) / 1.60m (F).</li>
                        <li><strong>Academics:</strong> Credit (C6) in Core Math & English.</li>
                        <li><strong>Age:</strong> Must be 18 - 26 years.</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="small fw-bold text-muted mb-1">TERMINATION QR</div>
                <img src="<?php echo $qr_api; ?>" class="qr-code shadow-sm" alt="Verification QR">
                <p class="x-small text-muted mt-1 mb-0" style="font-size: 0.6rem;"><?php echo $ref_no; ?></p>
            </div>
        </div>

        <p class="small text-center text-muted fst-italic">Digitally Generated on: <?php echo $date_generated; ?></p>

        <div class="text-center mt-3 no-print">
            <button onclick="window.print()" class="btn btn-primary btn-lg px-4 me-2 rounded-pill shadow-sm">
                <i class="fas fa-print me-2"></i> PRINT TERMINATION SLIP
            </button>
            <a href="index.php" class="btn btn-outline-dark btn-lg px-4 rounded-pill">
                <i class="fas fa-home me-2"></i> EXIT PORTAL
            </a>
        </div>
    </div>
</div>

<footer class="text-center text-muted small mt-4 no-print">
    &copy; <?php echo date('Y'); ?> Ghana Armed Forces. Secure Recruitment Portal (DRSS).
</footer>

</body>
</html>