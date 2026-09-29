<?php
session_start();
include 'config/db_connect.php';

// 1. SECURITY: Check for Admin Session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// 2. FETCH APPLICANT DATA
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM applicants WHERE applicant_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$app = $result->fetch_assoc();

if (!$app) {
    die("Applicant record not found.");
}

// 3. Format Reference ID
$ref_display = "GAF-" . str_pad($app['applicant_id'], 5, '0', STR_PAD_LEFT);

// 4. Calculate Age & Format DOB
if (!empty($app['date_of_birth']) && $app['date_of_birth'] != '0000-00-00') {
    $dob = new DateTime($app['date_of_birth']);
    $today = new DateTime();
    $age = $dob->diff($today)->y . " Yrs";
    $dob_display = $dob->format('M d, Y');
} elseif (!empty($app['age'])) {
    $age = $app['age'] . " Yrs";
    $dob_display = 'N/A';
} else {
    $age = "N/A";
    $dob_display = 'N/A';
}

// 5. Ghana Card Number
$ghana_card = !empty($app['ghana_card']) ? htmlspecialchars($app['ghana_card']) : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Screening | <?= $ref_display ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-purple: #6f42c1; --gaf-gold: #d4af37; }
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .medical-header { background: var(--gaf-purple); color: white; border-bottom: 5px solid var(--gaf-gold); padding: 20px; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .applicant-photo { width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--gaf-purple); border-radius: 10px; }
        .small-muted { font-size: 0.75rem; color: #6c757d; }
    </style>
</head>
<body>

<div class="medical-header text-center">
    <img src="assets/img/GAFB.jpg" style="width: 35px; margin-right: 10px;">
    <h3 class="fw-bold mb-0"><i class="fas fa-hand-holding-medical me-2"></i> MEDICAL SCREENING BUREAU</h3>
    <p class="mb-0 small text-uppercase" style="letter-spacing: 2px;">Ghana Armed Forces Recruitment Audit</p>
</div>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <a href="dashboard.php" class="btn btn-sm btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

            <div class="card card-custom">
                <div class="card-body p-5">
                    <div class="row">
                        <!-- Left Column: Applicant Info -->
                        <div class="col-md-4 text-center border-end">
                            <img src="<?= $app['passport_path'] ?? 'assets/img/default.png' ?>" class="applicant-photo mb-3 shadow-sm">
                            <h4 class="fw-bold mb-1"><?= strtoupper($app['first_name'] . " " . $app['last_name']) ?></h4>
                            <p class="text-muted fw-bold">Applicant ID: <?= $ref_display ?></p>
                            <p class="small-muted">Ghana Card: <?= $ghana_card ?></p>
                            <p class="small-muted">DOB: <?= $dob_display ?> | Age: <?= $age ?></p>

                            <ul class="list-group list-group-flush text-start small mt-3">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Branch:</span> <strong><?= strtoupper($app['branch_choice'] ?? 'N/A') ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Gender:</span> <strong><?= strtoupper($app['gender'] ?? 'N/A') ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Screening Center:</span> <strong><?= strtoupper($app['screening_center'] ?? 'N/A') ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Screening Date:</span> <strong><?= !empty($app['screening_date']) ? date('M d, Y', strtotime($app['screening_date'])) : 'N/A' ?></strong>
                                </li>
                            </ul>
                        </div>

                        <!-- Right Column: Medical Upload -->
                        <div class="col-md-8 ps-md-5">
                            <h5 class="text-uppercase fw-bold mb-4 text-primary">
                                <i class="fas fa-file-medical-alt me-2"></i> Upload Examination Results
                            </h5>

                            <form action="medical_upload.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="applicant_id" value="<?= $app['applicant_id'] ?>">

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Medical Status Decision</label>
                                    <select name="med_status" class="form-select py-3 fw-bold" required>
                                        <option value="">-- Choose Decision --</option>
                                        <option value="Fit" <?= ($app['medical_status'] == 'Fit') ? 'selected' : '' ?>>FIT (Qualified for Enlistment)</option>
                                        <option value="Unfit" <?= ($app['medical_status'] == 'Unfit') ? 'selected' : '' ?>>UNFIT (Permanent Disqualification)</option>
                                        <option value="Pending" <?= ($app['medical_status'] == 'Pending' || empty($app['medical_status'])) ? 'selected' : '' ?>>PENDING (Further Tests Required)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Scan Report File (PDF/JPG)</label>
                                    <input type="file" name="med_report" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div class="form-text">Max size: 5MB. Clear scans only.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Confidential Medical Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="4" placeholder="Enter findings e.g., Blood Pressure, Visual Acuity, General Physique..." required><?= htmlspecialchars($app['medical_remarks'] ?? '') ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Medical Officer's Full Name & Rank</label>
                                    <input type="text" name="officer_name" class="form-control" placeholder="e.g. Maj. Kwesi Mensah" value="<?= htmlspecialchars($app['officer_name'] ?? '') ?>" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="background-color: var(--gaf-purple); border:none;">
                                    <i class="fas fa-cloud-upload-alt me-2"></i> SUBMIT MEDICAL REPORT
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
