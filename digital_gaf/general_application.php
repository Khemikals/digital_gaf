<?php 
session_start();
include 'config/db_connect.php';

/**
 * SEQUENTIAL FLOW STEP 7: general_application.php
 * Official Appointment Slip & Entry Permit.
 */

// SECURITY GATE
if (!isset($_SESSION['applicant_authenticated']) || 
    ($_SESSION['application_status'] ?? '') !== 'Qualified' || 
    !isset($_SESSION['screening_center'])) { 
    header("Location: index.php"); 
    exit(); 
}

/**
 * LOGIC SYNC: Reference number
 */
if (isset($_SESSION['final_ref_no'])) {
    $ref_no = $_SESSION['final_ref_no'];
} else {
    $app_id = $_SESSION['applicant_id'] ?? rand(10000, 99999);
    $branch_char = strtoupper(substr($_SESSION['branch_choice'] ?? 'X', 0, 1));
    $ref_no = "GAF-{$branch_char}-" . str_pad($app_id, 5, '0', STR_PAD_LEFT);
    $_SESSION['final_ref_no'] = $ref_no;
}

// Safe session variables
$first_name = $_SESSION['first_name'] ?? 'N/A';
$last_name = $_SESSION['last_name'] ?? 'N/A';
$ghana_card = $_SESSION['ghana_card'] ?? 'N/A';
$passport_path = $_SESSION['passport_path'] ?? 'assets/img/placeholder.jpg';
$branch_choice = $_SESSION['branch_choice'] ?? 'N/A';
$unit_choice = $_SESSION['unit_choice'] ?? 'N/A';
$phone_number = $_SESSION['phone_number'] ?? 'N/A';
$screening_center = $_SESSION['screening_center'] ?? 'N/A';
$screening_date = $_SESSION['screening_date'] ?? date('Y-m-d');
$shs_index = $_SESSION['shs_index'] ?? 'N/A';
$math_grade = $_SESSION['math_grade'] ?? 'N/A';
$eng_grade = $_SESSION['eng_grade'] ?? 'N/A';

// QR Data
$qr_data = "PERMIT:$ref_no|ID:$ghana_card|CTR:$screening_center|DATE:$screening_date";
$qr_api  = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GAF Appointment Slip - <?= $ref_no ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #f4f4f4; font-family: 'Times New Roman', serif; }
        
        .form-container { 
            background: #fff; 
            max-width: 850px; 
            margin: 30px auto; 
            padding: 50px; 
            border: 1px solid #ccc; 
            position: relative; 
            overflow: hidden; 
        }
        
        .gaf-seal { 
            position: absolute; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            width: 400px; 
            opacity: 0.05; 
            z-index: 0; 
            pointer-events: none; 
        }
        
        .header-section { 
            border-bottom: 3px solid var(--gaf-olive); 
            margin-bottom: 25px; 
            padding-bottom: 10px; 
            position: relative; 
            z-index: 1; 
            text-align: center;
        }
        .gaf-logo { width: 85px; margin-bottom: 10px; }
        .top-grid { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; position: relative; z-index: 1; }
        .passport-img { width: 150px; height: 150px; border: 2px solid #000; object-fit: cover; display: block; }
        .qr-img { width: 130px; height: 130px; border: 1px solid #ccc; padding: 2px; background: white; }
        .section-title { background: #eee; font-weight: bold; padding: 5px 10px; border-left: 5px solid var(--gaf-olive); margin-top: 25px; position: relative; z-index: 1; text-transform: uppercase; font-size: 0.9rem; }
        .booking-box { border: 2px dashed var(--gaf-olive); background: #f9fbf2; padding: 20px; margin-top: 15px; position: relative; z-index: 1; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .form-container { border: none; margin: 0; width: 100%; box-shadow: none; padding: 20px; }
            .header-section { border-bottom: 3px solid #3d4621 !important; }
        }
    </style>
</head>
<body>

<div class="container no-print mt-4 text-center">
    <div class="d-flex gap-2 justify-content-center no-print mt-4">
        <button class="btn btn-dark fw-bold" onclick="window.print()">
            <i class="fas fa-print me-2"></i> PRINT APPOINTMENT SLIP
        </button>
        <a href="logout_1.php" class="btn btn-success fw-bold">
            FINALIZE & LOGOUT <i class="fas fa-check-double ms-2"></i>
        </a>
    </div>
</div>

<div class="form-container shadow-lg">
    <img src="assets/img/GAFB.jpg" class="gaf-seal" alt="GAF Watermark">

    <div class="header-section text-center">
        <img src="assets/img/GAFB.jpg" class="gaf-logo" alt="GAF Badge">
        <h3 class="fw-bold mb-0">GHANA ARMED FORCES</h3>
        <p class="text-uppercase mb-1 fw-bold">Recruitment Command - 2025 Intake</p>
        <p class="small text-muted mb-0">Official Appointment & Entry Permit Slip</p>
    </div>

    <div class="top-grid">
        <div>
            <img src="<?= $passport_path ?>" class="passport-img shadow-sm" alt="Passport">
        </div>

        <div class="flex-grow-1 ps-4 pt-2">
            <h5 class="fw-bold text-success border-bottom pb-2" style="font-size: 1.2rem;">REF NO: <?= $ref_no ?></h5>
            <p class="mb-1"><strong>NAME:</strong> <?= strtoupper($first_name . " " . $last_name) ?></p>
            <p class="mb-1"><strong>GHANA CARD:</strong> <?= $ghana_card ?></p>
            <p class="mb-1"><strong>BRANCH:</strong> <span class="text-uppercase fw-bold text-primary"><?= $branch_choice ?></span></p>
            <p class="mb-1"><strong>UNIT PREFERENCE:</strong> <?= strtoupper($unit_choice) ?></p>
            <p class="mb-1"><strong>CONTACT:</strong> +233 <?= $phone_number ?></p>
        </div>

        <div class="text-center">
            <img src="<?= $qr_api ?>" class="qr-img shadow-sm">
            <div class="small fw-bold mt-1 text-muted">GATE PASS</div>
        </div>
    </div>

    <div class="section-title">Screening & Documentation Details (Requirement FR8)</div>
    <div class="booking-box">
        <div class="row text-center align-items-center">
            <div class="col-md-7 border-end">
                <p class="small text-muted mb-1 text-uppercase">Regional Landmark Center</p>
                <h5 class="fw-bold"><?= $screening_center ?></h5>
            </div>
            <div class="col-md-5">
                <p class="small text-muted mb-1 text-uppercase">Appointment Date</p>
                <h5 class="fw-bold text-danger"><?= date('l, d F Y', strtotime($screening_date)) ?></h5>
                <h6 class="fw-bold">06:00 AM GMT (Reporting Time)</h6>
            </div>
        </div>
    </div>

    <div class="section-title">Verified Academic Summary</div>
    <div class="row mt-3 position-relative" style="z-index: 1;">
        <div class="col-6">
            <p class="mb-1"><strong>WASSCE Index:</strong> <?= $shs_index ?></p>
        </div>
        <div class="col-3">
            <p class="mb-1"><strong>Math Grade:</strong> <?= $math_grade ?></p>
        </div>
        <div class="col-3">
            <p class="mb-1"><strong>English Grade:</strong> <?= $eng_grade ?></p>
        </div>
    </div>

    <div class="section-title">Official Instructions</div>
    <div class="mt-2 position-relative" style="z-index: 1; font-size: 0.95rem;">
        <ul class="mb-0">
            <li>Present this <strong>Original Slip</strong> and your <strong>Ghana Card</strong> at the screening center gate.</li>
            <li>Dress Code: Physical Training (PT) Gear (White T-shirt, Shorts, Canvas/Sneakers).</li>
            <li>Bring original copies of all academic certificates and birth certificate.</li>
            <li>Lateness implies automatic disqualification.</li>
        </ul>
    </div>

    <div class="row mt-5 text-center position-relative" style="z-index: 1;">
        <div class="col-6">
            <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto 5px;"></div>
            <p class="small fw-bold">Applicant Signature</p>
        </div>
        <div class="col-6">
            <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto 5px;"></div>
            <p class="small fw-bold">Regional Recruiting Officer Seal</p>
        </div>
    </div>
    
    <div class="text-center mt-4 small text-muted">
        Digital Security Hash: <code><?= md5($ref_no . $ghana_card) ?></code>
    </div>

</div>

</body>
</html>
