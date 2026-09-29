<?php
// 1. FORCED ERROR VISIBILITY
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config/db_connect.php';

/**
 * SEQUENTIAL FLOW STEP 3: apply_step3.php
 * Automated Vetting Engine & Data Finalization
 */

// 2. SECURITY & INTEGRITY CHECK
// Ensure applicant is authenticated AND critical Step 1 data exists.
// If branch_choice is missing, the session is incomplete -> Redirect to Step 1.
if (!isset($_SESSION['applicant_authenticated']) || empty($_SESSION['branch_choice'])) { 
    // Redirect with error flag to prompt user to refill data
    header("Location: apply_step1.php?error=session_expired"); 
    exit(); 
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_upload'])) {
    
    // 3. Directory Check
    $target_dir = "uploads/documents/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $docs = ['ghana_card_front', 'ghana_card_back', 'birth_cert', 'wassce_cert', 'bece_cert'];
    $all_uploaded = true;

    // 4. Process File Uploads with NIA-Safe Naming
    foreach ($docs as $doc) {
        if (!empty($_FILES[$doc]["name"])) {
            $file_extension = strtolower(pathinfo($_FILES[$doc]["name"], PATHINFO_EXTENSION));
            
            // Sanitize NIA for filename
            $safe_nia = preg_replace('/[^A-Za-z0-9\-]/', '', $_SESSION['ghana_card_number']);
            
            $filename = $safe_nia . "_" . $doc . "." . $file_extension;
            $target_file = $target_dir . $filename;
            
            // Validate Extension
            if (in_array($file_extension, ['pdf', 'jpg', 'png', 'jpeg'])) {
                if (move_uploaded_file($_FILES[$doc]["tmp_name"], $target_file)) {
                    $_SESSION[$doc . "_path"] = $target_file; 
                } else {
                    $all_uploaded = false;
                    $error = "Critical error: Could not save " . str_replace('_', ' ', $doc);
                    break;
                }
            } else {
                $all_uploaded = false;
                $error = "Invalid format: " . $doc . ". Use PDF, JPG, or PNG.";
                break;
            }
        } else {
            $all_uploaded = false;
            $error = "All documents are mandatory. Please upload " . str_replace('_', ' ', $doc);
            break;
        }
    }

    // 5. IF ALL UPLOADED -> PROCESS & INSERT
    if ($all_uploaded) {
        
        // --- A. Automated Eligibility Logic (FR7) ---
        $is_qualified = true;
        
        // Height Check
        $height = (float)$_SESSION['height'];
        if ($_SESSION['gender'] == 'Male' && $height < 1.65) {
            $is_qualified = false;
        } elseif ($_SESSION['gender'] == 'Female' && $height < 1.60) {
            $is_qualified = false;
        }

        // Academic Check (No F9, E8, D7 in Core Math/English)
        $failing_grades = ['D7', 'E8', 'F9'];
        if (in_array($_SESSION['math_grade'], $failing_grades) || in_array($_SESSION['eng_grade'], $failing_grades)) {
            $is_qualified = false;
        }

        $final_status = $is_qualified ? 'Qualified' : 'Disqualified';
        $_SESSION['application_status'] = $final_status; 

        // --- B. Finalize Database Entry ---
        $sql = "INSERT INTO applicants (
            first_name, last_name, gender, dob, nationality, marital_status,
            ghana_card, phone_number, email, digital_address,
            hometown, hometown_region, hometown_district,
            branch_choice, unit_choice, height, weight, passport_path,
            ghana_card_front_path, ghana_card_back_path, birth_cert_path, 
            wassce_cert_path, bece_cert_path,
            jhs_name, jhs_index, jhs_math, jhs_eng, 
            shs_name, shs_index, shs_program, math_grade, eng_grade,
            application_status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("Database Prepare Error: " . $conn->error); 
        }

        // Bind parameters (Total: 33 items)
        $stmt->bind_param("sssssssssssssssddssssssssssssssss", 
            $_SESSION['first_name'], $_SESSION['last_name'], $_SESSION['gender'], $_SESSION['dob'], 
            $_SESSION['nationality'], $_SESSION['marital_status'],
            $_SESSION['ghana_card_number'], $_SESSION['phone_number'], $_SESSION['email'], $_SESSION['digital_address'],
            $_SESSION['hometown'], $_SESSION['hometown_region'], $_SESSION['hometown_district'],
            $_SESSION['branch_choice'], $_SESSION['unit_choice'], 
            $_SESSION['height'], $_SESSION['weight'], $_SESSION['passport_path'],
            $_SESSION['ghana_card_front_path'], $_SESSION['ghana_card_back_path'], $_SESSION['birth_cert_path'], 
            $_SESSION['wassce_cert_path'], $_SESSION['bece_cert_path'],
            $_SESSION['jhs_name'], $_SESSION['jhs_index'], $_SESSION['jhs_math'], $_SESSION['jhs_eng'],
            $_SESSION['shs_name'], $_SESSION['shs_index'], $_SESSION['shs_program'], 
            $_SESSION['math_grade'], $_SESSION['eng_grade'], 
            $final_status
        );

        if ($stmt->execute()) {
            $_SESSION['applicant_id'] = $conn->insert_id;
            
            // Invalidate Voucher
            if (isset($_SESSION['voucher_serial'])) {
                $v_serial = $_SESSION['voucher_serial'];
                $conn->query("UPDATE vouchers SET status = 'used' WHERE serial_number = '$v_serial'");
            }
            
            // --- C. Redirect to Step 4 (Final Review) ---
            header("Location: apply_step4.php");
            exit();
        } else {
            $error = "Database Execute Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Step 3: Upload Documents | GAF-DRSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .gaf-header { background: var(--gaf-olive); color: white; border-bottom: 5px solid var(--gaf-gold); padding: 20px; text-align: center; }
        .btn-gaf { background: var(--gaf-olive); color: white; border: 1px solid var(--gaf-gold); font-weight: bold; width: 100%; padding: 15px; }
        .btn-gaf:hover { background: #2a3116; color: var(--gaf-gold); }
        .progress-bar-container { height: 8px; background: #e9ecef; border-radius: 4px; margin-bottom: 30px; }
        .progress-fill { width: 75%; height: 100%; background: var(--gaf-gold); border-radius: 4px; }
    </style>
</head>
<body>

<header class="gaf-header">
    <h2 class="fw-bold text-uppercase">Ghana Armed Forces</h2>
    <img src="assets/img/GAFB.jpg" style="width: 75px;"><br>
    <p class="mb-0 small text-uppercase" style="letter-spacing:1px;">Recruitment Step 3: Mandatory Documentation</p>
</header>

<div class="container mt-5 mb-5">
    <div class="card shadow p-4 mx-auto border-0" style="max-width: 850px;">
        <div class="progress-bar-container"><div class="progress-fill"></div></div>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger shadow-sm border-2 border-danger">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <h5 class="fw-bold text-muted border-bottom pb-2 mb-4"><i class="fas fa-id-card me-2"></i> IDENTITY DOCUMENTS</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase">Ghana Card (Front View)</label>
                    <input type="file" name="ghana_card_front" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    <div class="form-text x-small">Ensure ID number is visible.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase">Ghana Card (Back View)</label>
                    <input type="file" name="ghana_card_back" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold small text-uppercase">Birth Certificate</label>
                    <input type="file" name="birth_cert" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>
            </div>

            <h5 class="fw-bold text-muted border-bottom pb-2 mb-4"><i class="fas fa-graduation-cap me-2"></i> ACADEMIC CERTIFICATES</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase">WASSCE / SSSCE / TVET Certificate</label>
                    <input type="file" name="wassce_cert" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase">BECE Certificate</label>
                    <input type="file" name="bece_cert" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>
            </div>

            <div class="alert alert-warning mt-5 small d-flex align-items-center">
                <i class="fas fa-shield-alt fa-2x me-3 text-warning"></i>
                <div>
                    <strong>System Notice:</strong> 
                    By clicking "Complete Application", your data will be locked and an automated eligibility check will run. Ensure all uploads are clear and legible.
                </div>
            </div>

            <button type="submit" name="complete_upload" class="btn btn-gaf mt-2 text-uppercase fw-bold shadow">
                COMPLETE APPLICATION & PROCEED <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>
    </div>
</div>
</body>
</html>