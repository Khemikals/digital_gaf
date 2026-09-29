<?php
session_start();

/**
 * SEQUENTIAL FLOW STEP 6: submit_final.php
 * Updated to sync the Internal Ref No with summary_form.php
 */

// Use the correct session keys established in previous steps
$status  = $_SESSION['application_status'] ?? 'Disqualified'; 
$fname   = $_SESSION['first_name'] ?? 'Applicant';
$lname   = $_SESSION['last_name'] ?? '';
$branch  = $_SESSION['branch_choice'] ?? 'Not Selected';
$app_id  = $_SESSION['applicant_id'] ?? 0;
$contact = $_SESSION['phone_number'] ?? 'N/A'; 

/**
 * LOGIC SYNC: Use the same Reference Number from the Session.
 * If it was already generated in summary_form.php, we use it here.
 */
if (!isset($_SESSION['final_ref_no'])) {
    $branch_initial = strtoupper(substr($branch, 0, 1));
    $random_id = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['final_ref_no'] = "GAF-" . $branch_initial . "-" . $random_id;
}

$ref_no = $_SESSION['final_ref_no'];

// Generate dynamic QR data with the official Internal Ref No
$qr_data = "GAF-VERIFY:" . $fname . "_" . $lname . "|STATUS:" . $status . "|REF:" . $ref_no;
$qr_api  = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($qr_data);

// Capture the specific reason stored in the vetting engine
$reason = $_SESSION['disqualified_reason'] ?? "One or more of your qualifications did not meet the GAF entry standards (FR7).";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Outcome | GAF-DRSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; --gaf-red: #8b0000; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .outcome-card { max-width: 750px; margin: 40px auto; padding: 40px; border-radius: 10px; border-top: 10px solid; position: relative; }
        .status-qualified { border-color: var(--gaf-olive); }
        .status-disqualified { border-color: var(--gaf-red); }
        .btn-gaf { background: var(--gaf-olive); color: white; border: none; font-weight: bold; }
        .btn-gaf:hover { background: #2a3116; color: var(--gaf-gold); }
        .passport-display { width: 140px; height: 140px; border: 3px solid var(--gaf-olive); object-fit: cover; border-radius: 5px; }
        .qr-code { width: 100px; border: 1px solid #ddd; padding: 5px; background: #fff; }
        .gaf-logo { width: 80px; height: auto; margin-bottom: 10px; }
        .info-label { font-size: 0.75rem; text-transform: uppercase; color: #666; font-weight: bold; margin-bottom: 0; }
        .info-value { font-size: 1rem; color: #111; font-weight: 600; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .outcome-card { margin: 0; border: 1px solid #ccc; box-shadow: none; max-width: 100%; border-top: 10px solid; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card outcome-card shadow-lg bg-white <?php echo ($status == 'Qualified') ? 'status-qualified' : 'status-disqualified'; ?>">
        
        <div class="text-center">
            <img src="assets/img/GAFB.jpg" alt="GAF Badge" class="gaf-logo">
            <h4 class="fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">Ghana Armed Forces</h4>
            <p class="small text-muted mb-0">Official Recruitment Notification</p>
            <hr>
        </div>

        <div class="row align-items-center text-md-start text-center">
            <div class="col-md-3 mb-3 mb-md-0 d-flex justify-content-center">
                <?php if(isset($_SESSION['passport_path'])): ?>
                    <img src="<?php echo $_SESSION['passport_path']; ?>" class="passport-display shadow-sm" alt="Passport Photo">
                <?php else: ?>
                    <div class="passport-display bg-light d-flex align-items-center justify-content-center border">
                        <i class="fas fa-user-tie fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <?php if ($status == 'Qualified'): ?>
                    <h2 class="fw-bold text-success mb-1">CONGRATULATIONS</h2>
                    <p class="lead mb-2"><strong><?php echo htmlspecialchars($fname . " " . $lname); ?></strong></p>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="info-label">Internal Ref No</p>
                            <p class="info-value text-primary"><?php echo $ref_no; ?></p>
                        </div>
                        <div class="col-6">
                            <p class="info-label">Contact</p>
                            <p class="info-value"><?php echo htmlspecialchars($contact); ?></p>
                        </div>
                    </div>

                    <p class="small mb-3">You have passed the preliminary vetting for <strong><?php echo htmlspecialchars($branch); ?></strong>.</p>
                    
                    <div class="alert alert-success py-2 px-3 mb-3 d-inline-block shadow-sm">
                        <i class="fas fa-check-double me-2"></i> STATUS: <strong>QUALIFIED</strong>
                    </div>
                    
                    <div class="no-print d-flex gap-2">
                         <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm">
                            <i class="fas fa-print me-2"></i> Print Result
                        </button>
                        <a href="booking.php" class="btn btn-gaf btn-sm px-3 shadow-sm">
                            PROCEED TO BOOKING <i class="fas fa-calendar-alt ms-2"></i>
                        </a>
                    </div>

                <?php else: ?>
                    <h2 class="fw-bold text-danger mb-1">UNSUCCESSFUL</h2>
                    <p class="lead mb-2">Dear <strong><?php echo htmlspecialchars($fname . " " . $lname); ?></strong></p>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <p class="info-label">Internal Ref No</p>
                            <p class="info-value"><?php echo $ref_no; ?></p>
                        </div>
                    </div>

                    <div class="alert alert-danger py-2 px-3 mb-3">
                        <i class="fas fa-times-circle me-2"></i> <strong>REASON:</strong> <?php echo htmlspecialchars($reason); ?>
                    </div>
                    
                    <div class="no-print d-flex gap-2">
                        <button onclick="window.print()" class="btn btn-outline-danger btn-sm shadow-sm">
                            <i class="fas fa-print me-2"></i> Print Notice
                        </button>
                        <a href="index.php" class="btn btn-dark btn-sm px-4 shadow-sm text-uppercase">
                            Exit Portal
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-3 text-center border-start">
                <div class="small fw-bold text-muted mb-1">VERIFICATION</div>
                <img src="<?php echo $qr_api; ?>" class="qr-code shadow-sm" alt="Verification QR">
                <p class="small text-muted mt-2 mb-0 fw-bold"><?php echo $ref_no; ?></p>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top small text-muted text-center">
            Generated on: <?php echo date('Y-m-d H:i:s'); ?> | Secure System GAF-DRSS
        </div>
    </div>
</div>

</body>
</html>