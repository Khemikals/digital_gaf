<?php
session_start();
include 'config/db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$msg = "";
$applicant = null;

// =========================================================
// 2. HANDLE FORM SUBMISSION (POST)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!isset($_POST['applicant_id']) || empty($_POST['applicant_id'])) {
        die("Error: Applicant ID is missing.");
    }

    $id = (int)$_POST['applicant_id'];
    $officer = $conn->real_escape_string($_POST['officer_name'] ?? '');
    $rank = $conn->real_escape_string($_POST['officer_rank'] ?? '');
    $action = $_POST['action'] ?? 'pending'; 
    $remarks = $conn->real_escape_string($_POST['officer_note'] ?? '');

    if ($action === 'approve') {
        $db_status = 'Qualified';
    } elseif ($action === 'reject') {
        $db_status = 'Disqualified';
    } else {
        $db_status = 'Pending';
    }

    $vetted_by_str = "$officer ($rank)";

    $sql = "UPDATE applicants SET 
            application_status = '$db_status', 
            vetting_remarks = '$remarks', 
            vetted_by = '$vetted_by_str',
            processed_at = NOW() 
            WHERE applicant_id = $id";

    if ($conn->query($sql)) {
        header("Location: dashboard.php?msg=vetting_complete");
        exit();
    } else {
        $msg = "<div class='alert alert-danger'>Database Error: " . $conn->error . "</div>";
    }
}

// =========================================================
// 3. FETCH APPLICANT DATA (GET)
// =========================================================
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sql = "SELECT 
                a.*, 
                d.passport_picture_path as doc_passport,
                d.wassce_cert_path as doc_wassce,
                d.bece_cert_path as doc_bece,
                d.birth_cert_path as doc_birth,
                d.ghana_card_path as doc_ghana
            FROM applicants a 
            LEFT JOIN applicant_documents d ON a.applicant_id = d.applicant_id 
            WHERE a.applicant_id = $id";
            
    $res = $conn->query($sql);
    
    if ($res && $res->num_rows > 0) {
        $applicant = $res->fetch_assoc();

        // Passport logic
        $raw_passport = $applicant['passport_path'] ?? $applicant['doc_passport'] ?? '';
        $passport_final = !empty($raw_passport) ? 
            ((strpos($raw_passport,'uploads/')!==false) ? $raw_passport : "uploads/passports/".$raw_passport) 
            : '';

        // Age calculation
        $dob = !empty($applicant['dob']) ? new DateTime($applicant['dob']) : null;
        $age = $dob ? (new DateTime())->diff($dob)->y : 'N/A';

        // Applicant ID display
        $app_id_display = "GAF-" . str_pad($applicant['applicant_id'], 5, '0', STR_PAD_LEFT);

        // Document paths
        $ghana_card_front = $applicant['ghana_card_front_path'] ?? $applicant['doc_ghana'] ?? '';
        $ghana_card_back  = $applicant['ghana_card_back_path'] ?? '';
        $birth_cert       = $applicant['birth_cert_path'] ?? $applicant['doc_birth'] ?? '';
        $wassce_cert      = $applicant['wassce_cert_path'] ?? $applicant['doc_wassce'] ?? '';
        $bece_cert        = $applicant['bece_cert_path'] ?? $applicant['doc_bece'] ?? '';
        
    } else {
        die("Error: Applicant not found.");
    }
} else {
    header("Location: dashboard.php");
    exit();
}

// Helper function to render documents safely
function renderDoc($path, $title) {
    if (empty($path)) {
        return '
        <div class="card mb-3 border-danger shadow-sm">
            <div class="card-header bg-danger text-white small fw-bold"><i class="fas fa-times-circle"></i> ' . $title . '</div>
            <div class="card-body text-center text-muted p-4">
                <i class="fas fa-file-excel fa-2x mb-2 text-secondary"></i><br>
                <span class="small">Not Uploaded</span>
            </div>
        </div>';
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
        $viewer = '<img src="'.$path.'" class="img-fluid border rounded" style="width: 100%; max-height: 400px; object-fit: contain;">';
    } elseif ($ext === 'pdf') {
        $viewer = '<iframe src="'.$path.'" class="w-100 border rounded" style="height: 400px;"></iframe>';
    } else {
        $viewer = '<div class="alert alert-info py-2">File type ('.$ext.') preview unavailable. <a href="'.$path.'" target="_blank" class="fw-bold">Download</a></div>'; 
    }
    return '
    <div class="card mb-4 shadow-sm">
        <div class="card-header fw-bold bg-secondary text-white small d-flex justify-content-between align-items-center">
            <span><i class="fas fa-file-image me-2"></i> ' . $title . '</span>
            <a href="'.$path.'" target="_blank" class="btn btn-light btn-sm py-0" style="font-size: 0.7rem;">Open Original</a>
        </div>
        <div class="card-body text-center p-1 bg-dark">
            ' . $viewer . '
        </div>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vetting: <?php echo htmlspecialchars($applicant['ghana_card'] ?? 'N/A'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #e9ecef; font-family: 'Segoe UI', sans-serif; }
        .vetting-header { background: #343a40; color: white; padding: 15px 0; border-bottom: 4px solid #d4af37; margin-bottom: 20px; }
        .sticky-panel { position: sticky; top: 20px; }
        .doc-section-title { border-left: 5px solid #d4af37; padding-left: 10px; font-weight: bold; margin: 30px 0 15px 0; text-transform: uppercase; color: #333; background: #fff; padding: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .passport-frame { width: 150px; height: 160px; object-fit: cover; border: 4px solid #d4af37; border-radius: 6px; background: #fff; }
    </style>
</head>
<body>

<header class="vetting-header">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="assets/img/GAFB.jpg" width="40" class="me-3 border border-white rounded-circle">
            <div>
                <h5 class="mb-0 fw-bold text-uppercase">Recruitment Vetting Station</h5>
                <small class="text-white-50">Document Verification & Approval Module</small>
            </div>
        </div>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm fw-bold"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>
    </div>
</header>

<div class="container-fluid px-4 pb-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4 border-0 border-start border-5 border-primary">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-3 text-center">
                             <?php if(!empty($passport_final)): ?>
                                <img src="<?php echo htmlspecialchars($passport_final); ?>" class="passport-frame shadow-sm" alt="Passport Photo">
                             <?php else: ?>
                                <div class="passport-frame d-flex align-items-center justify-content-center mx-auto bg-light">
                                    <i class="fas fa-user fa-3x text-secondary"></i>
                                </div>
                                <small class="text-danger d-block mt-1 fw-bold">No Photo Found</small>
                             <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h2 class="fw-bold mb-1 text-uppercase text-dark"><?php echo htmlspecialchars($applicant['first_name'] . " " . $applicant['last_name']); ?></h2>
                            <span class="badge bg-dark fs-6 mb-3">APPLICANT ID: <?php echo htmlspecialchars($app_id_display); ?></span>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-white shadow-sm">
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Ghana Card ID</small>
                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($applicant['ghana_card'] ?? 'N/A'); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-white shadow-sm">
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Unit Preference</small>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($applicant['branch_choice'] ?? 'N/A'); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-white shadow-sm">
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Current Status</small>
                                        <div class="fw-bold">
                                            <span class="badge <?php 
                                                echo ($applicant['application_status']=='Qualified')?'bg-success':
                                                     (($applicant['application_status']=='Disqualified')?'bg-danger':'bg-warning text-dark'); ?>">
                                                <?php echo htmlspecialchars($applicant['application_status'] ?? 'Pending'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="doc-section-title"><i class="fas fa-id-card me-2"></i> Identity Documents</div>
            <div class="row">
                <div class="col-md-6"><?php echo renderDoc($ghana_card_front, 'Ghana Card (Front)'); ?></div>
                <div class="col-md-6"><?php echo renderDoc($ghana_card_back, 'Ghana Card (Back)'); ?></div>
            </div>
            <div class="row">
                <div class="col-md-12"><?php echo renderDoc($birth_cert, 'Birth Certificate'); ?></div>
            </div>

            <div class="doc-section-title"><i class="fas fa-graduation-cap me-2"></i> Academic Certificates</div>
            <div class="row">
                <div class="col-md-12"><?php echo renderDoc($wassce_cert, 'WASSCE Certificate'); ?></div>
            </div>
            <?php if(!empty($bece_cert)): ?>
            <div class="row">
                <div class="col-md-12"><?php echo renderDoc($bece_cert, 'BECE Certificate'); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="sticky-panel">
                <?php echo $msg; ?>
                <div class="card shadow border-0">
                    <div class="card-header bg-dark text-white fw-bold text-uppercase p-3">
                        <i class="fas fa-gavel me-2"></i> Official Verdict
                    </div>
                    <div class="card-body p-4 bg-white">
                        <form method="POST">
                            <input type="hidden" name="applicant_id" value="<?php echo $applicant['applicant_id']; ?>">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-muted text-uppercase small">Officer Verification</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Vetting Officer Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-user-shield"></i></span>
                                    <input type="text" name="officer_name" class="form-control fw-bold" required placeholder="e.g. Maj. J. Doe" value="<?php echo $_SESSION['admin_username'] ?? ''; ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold">Rank / Staff ID</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-id-badge"></i></span>
                                    <input type="text" name="officer_rank" class="form-control fw-bold" required placeholder="e.g. Major / 99021">
                                </div>
                            </div>

                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-muted text-uppercase small">Decision</h6>
                            <div class="mb-3">
                                <div class="d-grid gap-2">
                                    <input type="radio" class="btn-check" name="action" id="approve" value="approve" required>
                                    <label class="btn btn-outline-success fw-bold text-start p-3 shadow-sm" for="approve">
                                        <i class="fas fa-check-circle fa-lg me-2"></i> APPROVE
                                        <div class="small fw-normal ms-4">Documents are Original & Valid</div>
                                    </label>

                                    <input type="radio" class="btn-check" name="action" id="reject" value="reject">
                                    <label class="btn btn-outline-danger fw-bold text-start p-3 shadow-sm" for="reject">
                                        <i class="fas fa-times-circle fa-lg me-2"></i> DISQUALIFY
                                        <div class="small fw-normal ms-4">Fake / Mismatch / Incomplete</div>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold">Official Remarks</label>
                                <textarea name="officer_note" class="form-control bg-light" rows="4" placeholder="Mandatory: State reasons for your decision..." required><?php echo htmlspecialchars($applicant['vetting_remarks'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 py-3 fw-bold shadow-sm">
                                CONFIRM & SAVE DECISION <i class="fas fa-save ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
