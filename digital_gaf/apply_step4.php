<?php
session_start();
include 'config/db_connect.php';

/**
 * SEQUENTIAL FLOW STEP 4: apply_step4.php
 * Final data review before triggering the Eligibility Engine display.
 */

// SECURITY GATE: Ensure applicant has gone through previous steps
if (!isset($_SESSION['applicant_authenticated']) || !isset($_SESSION['shs_index'])) { 
    header("Location: apply_step2.php?error=incomplete_flow"); 
    exit(); 
}

$full_name = strtoupper($_SESSION['first_name'] . " " . $_SESSION['last_name']);

// Calculate age if DOB available
if (!empty($_SESSION['date_of_birth'])) {
    $dob = new DateTime($_SESSION['date_of_birth']);
    $today = new DateTime();
    $age = $dob->diff($today)->y;
} elseif (!empty($_SESSION['age'])) {
    $age = $_SESSION['age'];
} else {
    $age = 'N/A';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Step 4: Final Review | GAF-DRSS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
:root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; --gaf-red: #8b0000; }
body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
.gaf-header { background: var(--gaf-olive); color: white; border-bottom: 5px solid var(--gaf-gold); padding: 20px; text-align: center; }
.confirm-box { max-width: 950px; margin: 40px auto; border: none; background: transparent; }
.summary-header { background: #ffffff; color: var(--gaf-olive); padding: 15px 25px; font-weight: bold; border-top: 5px solid var(--gaf-olive); border-bottom: 1px solid #ddd; border-radius: 5px 5px 0 0; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
.review-section { background: #ffffff; border: 1px solid #e0e0e0; border-top: 4px solid var(--gaf-gold); border-left: 5px solid var(--gaf-olive); padding: 25px; margin-bottom: 25px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.data-label { color: #666; font-size: 0.70rem; text-transform: uppercase; font-weight: bold; margin-bottom: 1px; letter-spacing: 0.5px; }
.data-value { font-weight: 700; color: #212529; font-size: 0.95rem; text-transform: uppercase; }
.passport-display { width: 150px; height: 150px; border: 3px solid var(--gaf-gold); padding: 3px; background: white; object-fit: cover; border-radius: 4px; }
.status-badge { font-size: 0.8rem; padding: 6px 14px; border-radius: 20px; border: 1px solid #000; }
.declaration-box { background-color: #fffcf5; border: 2px solid #333; padding: 30px; margin-bottom: 25px; position: relative; }
.declaration-box::after { content: "OFFICIAL OATH"; position: absolute; top: 0; right: 0; background: var(--gaf-red); color: white; font-size: 0.7rem; padding: 5px 15px; font-weight: bold; }
.declaration-text { font-family: 'Times New Roman', Times, serif; font-size: 1.1rem; line-height: 1.6; color: #111; text-align: justify; font-weight: normal; }
.section-icon { color: var(--gaf-olive); opacity: 0.8; }
.form-check-input { border: 2px solid var(--gaf-olive); width: 1.5em; height: 1.5em; margin-top: 0.2em; cursor: pointer; }
.form-check-input:checked { background-color: var(--gaf-olive); border-color: var(--gaf-olive); }
.list-inline-item { display: inline-block; margin-right: 5px; margin-bottom: 5px; padding: 3px 7px; border-radius: 4px; background: #6c757d; color: #fff; font-size: 0.85rem; }
</style>
</head>
<body class="bg-light">

<header class="gaf-header">
    <h2 class="fw-bold text-uppercase">Ghana Armed Forces</h2>
    <p class="mb-0 text-uppercase small" style="letter-spacing: 2px;">Review & Verify Application Details</p>
</header>

<div class="container mb-5">
    <div class="confirm-box">

        <div class="summary-header">
            <div>
                <h5 class="m-0 fw-bold"><i class="fas fa-folder-open me-2"></i> APPLICATION DOSSIER</h5>
                <small class="text-muted text-uppercase">Review all details before submission</small>
            </div>
            <img src="assets/img/GAFB.jpg" style="width: 60px; height: auto;">
        </div>

        <!-- Personal Info -->
        <div class="review-section">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-3 mb-md-0 border-end">
                    <?php if(isset($_SESSION['passport_path'])): ?>
                        <img src="<?= $_SESSION['passport_path']; ?>" class="passport-display shadow-sm" alt="Passport Photo">
                    <?php else: ?>
                        <div class="passport-display bg-light d-flex align-items-center justify-content-center mx-auto">
                            <i class="fas fa-user-tie fa-4x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <div class="mt-2 small fw-bold text-muted">APPLICANT PASSPORT</div>
                </div>
                <div class="col-md-9 ps-md-4">
                    <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-3"><i class="fas fa-user-circle me-2 section-icon"></i> Personal Bio-Data</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><div class="data-label">Full Name</div><div class="data-value"><?= $full_name ?></div></div>
                        <div class="col-md-6"><div class="data-label">Ghana Card (NIA)</div><div class="data-value"><?= $_SESSION['ghana_card_number'] ?? 'N/A' ?></div></div>
                        <div class="col-md-3"><div class="data-label">Gender</div><div class="data-value"><?= $_SESSION['gender'] ?></div></div>
                        <div class="col-md-3"><div class="data-label">Marital Status</div><div class="data-value"><?= $_SESSION['marital_status'] ?? 'N/A' ?></div></div>
                        <div class="col-md-3"><div class="data-label">Age</div><div class="data-value"><?= $age ?> YEARS</div></div>
                        <div class="col-md-3"><div class="data-label">Nationality</div><div class="data-value"><?= $_SESSION['nationality'] ?? 'GHANAIAN' ?></div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location & Contact -->
        <div class="review-section">
            <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-3"><i class="fas fa-map-marked-alt me-2 section-icon"></i> Location & Contact</h6>
            <div class="row g-3">
                <div class="col-md-3"><div class="data-label">Hometown</div><div class="data-value"><?= $_SESSION['hometown'] ?? 'N/A' ?></div></div>
                <div class="col-md-3"><div class="data-label">Region</div><div class="data-value"><?= $_SESSION['hometown_region'] ?? 'N/A' ?></div></div>
                <div class="col-md-3"><div class="data-label">District</div><div class="data-value"><?= $_SESSION['hometown_district'] ?? 'N/A' ?></div></div>
                <div class="col-md-3"><div class="data-label">Digital Address</div><div class="data-value"><?= $_SESSION['digital_address'] ?? 'N/A' ?></div></div>
                <div class="col-md-6"><div class="data-label">Phone Number</div><div class="data-value">+233 <?= $_SESSION['phone_number'] ?></div></div>
                <div class="col-md-6"><div class="data-label">Email Address</div><div class="data-value" style="text-transform: lowercase;"><?= $_SESSION['email'] ?? 'N/A' ?></div></div>
            </div>
        </div>

        <!-- Service & Physicals -->
        <div class="review-section">
            <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-3"><i class="fas fa-shield-alt me-2 section-icon"></i> Service & Physicals</h6>
            <div class="row g-3">
                <div class="col-md-4"><div class="data-label">Service Branch</div><div class="data-value text-primary"><?= $_SESSION['branch_choice'] ?></div></div>
                <div class="col-md-4"><div class="data-label">Unit / Corps Preference</div><div class="data-value"><?= $_SESSION['unit_choice'] ?? 'GENERAL DUTIES' ?></div></div>
                <div class="col-md-2"><div class="data-label">Height</div><div class="data-value"><?= $_SESSION['height'] ?> FT</div></div>
                <div class="col-md-2"><div class="data-label">Weight</div><div class="data-value"><?= $_SESSION['weight'] ?> KG</div></div>
            </div>
        </div>

        <!-- Academic Records -->
        <div class="review-section">
            <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-3"><i class="fas fa-graduation-cap me-2 section-icon"></i> Academic Record</h6>
            <div class="row g-4">

                <!-- JUNIOR HIGH SCHOOL -->
                <div class="col-md-6">
                    <div class="bg-light p-3 border rounded">
                        <div class="text-muted small fw-bold mb-2 text-decoration-underline">JUNIOR HIGH SCHOOL (BECE)</div>
                        <div class="data-label">School Name</div>
                        <div class="data-value"><?= $_SESSION['jhs_name'] ?? 'N/A' ?></div>
                        <div class="row g-2 mt-2">
                            <div class="col-6"><div class="data-label">Completion Year</div><div class="data-value"><?= $_SESSION['jhs_year'] ?? 'N/A' ?></div></div>
                            <div class="col-6"><div class="data-label">BECE Index</div><div class="data-value"><?= $_SESSION['jhs_index'] ?? 'N/A' ?></div></div>
                        </div>
                    </div>
                </div>

                <!-- SENIOR HIGH SCHOOL -->
                <div class="col-md-6">
                    <div class="bg-light p-3 border rounded">
                        <div class="text-muted small fw-bold mb-2 text-decoration-underline">SENIOR HIGH SCHOOL (WASSCE)</div>
                        <div class="data-label">School Name</div>
                        <div class="data-value"><?= $_SESSION['shs_name'] ?? 'N/A' ?></div>
                        <div class="row g-2 mt-2">
                            <div class="col-6"><div class="data-label">Completion Year</div><div class="data-value"><?= $_SESSION['shs_year'] ?? 'N/A' ?></div></div>
                            <div class="col-6"><div class="data-label">WASSCE Index</div><div class="data-value"><?= $_SESSION['shs_index'] ?? 'N/A' ?></div></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Legal Declaration -->
        <form action="process_eligibility.php" method="POST">
            <div class="declaration-box shadow-sm">
                <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-family: 'Times New Roman', serif; text-decoration: underline;">Legal Declaration</h6>
                <div class="d-flex align-items-start">
                    <input class="form-check-input mt-1 me-3" type="checkbox" id="declarationCheck" required>
                    <label class="form-check-label declaration-text" for="declarationCheck">
                        I, <span class="fw-bold text-decoration-underline text-uppercase"><?= $full_name ?></span>, HEREBY SOLEMNLY DECLARE THAT THE INFORMATION PROVIDED HEREIN IS TRUE AND ACCURATE TO THE BEST OF MY KNOWLEDGE. I UNDERSTAND THAT ANY WILLFUL MISREPRESENTATION, FALSIFICATION, OR OMISSION OF FACTS SHALL CONSTITUTE GROUNDS FOR IMMEDIATE DISQUALIFICATION AND MAY RENDER ME LIABLE TO CRIMINAL PROSECUTION UNDER THE LAWS OF GHANA.
                    </label>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <a href="apply_step1.php" class="btn btn-outline-dark w-100 py-3 fw-bold rounded-pill">
                        <i class="fas fa-edit me-2"></i> EDIT INFORMATION
                    </a>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow rounded-pill">
                        CONFIRM & PROCESS APPLICATION <i class="fas fa-check-circle ms-2"></i>
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

<footer class="text-center text-muted small pb-5 mt-5">
    &copy; <?= date('Y') ?> Ghana Armed Forces. Recruitment Review Module.
</footer>

</body>
</html>
