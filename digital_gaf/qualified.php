<?php
session_start();
include 'config/db_connect.php'; 

/**
 * SEQUENTIAL FLOW STEP 9: qualified.php
 * SECURITY GATE: Only allow access if the applicant is Qualified.
 */
if (!isset($_SESSION['applicant_authenticated']) || $_SESSION['app_status'] !== 'Qualified') {
    header("Location: index.php");
    exit();
}

// Get applicant ID from session to ensure security
$applicant_id = $_SESSION['applicant_id'];

// Fetch details from the consolidated applicants table
$sql = "SELECT * FROM applicants WHERE applicant_id = '$applicant_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("Invalid Application Access. Record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GAF | Qualified Appointment Slip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f4f4; font-family: 'Times New Roman', serif; }
        .letter-card {
            background: white;
            padding: 50px;
            border: 1px solid #ddd;
            position: relative;
            overflow: hidden;
            min-height: 850px;
        }
        .watermark {
            position: absolute; top: 50%; left: 50%; 
            transform: translate(-50%, -50%);
            opacity: 0.05; z-index: 0; width: 450px;
            pointer-events: none;
        }
        .content-layer { position: relative; z-index: 1; }
        
        /* Print logic: Hides buttons and backgrounds when printing */
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .letter-card { border: none !important; margin: 0 !important; width: 100% !important; box-shadow: none !important; }
            .container { max-width: 100% !important; width: 100% !important; }
        }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="d-flex justify-content-between mb-4 no-print">
        <a href="index.php" class="btn btn-outline-dark fw-bold">
            <i class="fas fa-home me-2"></i> RETURN HOME
        </a>
        <div>
            <button onclick="window.print()" class="btn btn-success shadow fw-bold me-2">
                <i class="fas fa-print me-2"></i> PRINT INVITATION SLIP
            </button>
            <a href="general_application.php" class="btn btn-primary shadow fw-bold">
                VIEW FULL DATA <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>

    <div class="card shadow-lg mx-auto letter-card" style="max-width: 850px;">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/a/a2/Ghana_Armed_Forces_seal.svg/1200px-Ghana_Armed_Forces_seal.svg.png" class="watermark">
        
        <div class="content-layer">
            <div class="text-center mb-5">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Coat_of_arms_of_Ghana.svg/100px-Coat_of_arms_of_Ghana.svg.png" width="80">
                <h3 class="fw-bold mt-2 mb-0">GHANA ARMED FORCES</h3>
                <h5 class="text-uppercase border-bottom d-inline-block pb-1">Recruitment Training Command</h5>
                <p class="small text-muted">General Headquarters (GHQ), Burma Camp</p>
            </div>

            <div class="d-flex justify-content-between">
                <p>Ref: <strong>GAF/RECT/<?php echo date('Y'); ?>/<?php echo str_pad($applicant_id, 5, '0', STR_PAD_LEFT); ?></strong></p>
                <p class="fw-bold">Dated: <?php echo date('d F Y'); ?></p>
            </div>

            <h5 class="fw-bold text-center text-decoration-underline mt-4">LETTER OF INVITATION FOR REGIONAL SCREENING</h5>

            <p class="mt-4">Dear <strong><?php echo strtoupper($row['first_name'] . " " . $row['last_name']); ?></strong>,</p>
            
            <p>We are pleased to inform you that following our automated vetting process, you have been <strong>QUALIFIED</strong> to proceed to the next stage of the enlistment process for the <strong><?php echo strtoupper($row['branch_choice']); ?></strong>.</p>
            
            <p>You are hereby directed to report to the screening center listed below for physical verification and documentation.</p>

            <table class="table table-bordered mt-4 border-dark">
                <thead class="table-dark">
                    <tr><th colspan="2" class="text-center">REPORTING INSTRUCTIONS</th></tr>
                </thead>
                <tbody>
                    <tr><td width="30%" class="fw-bold">VENUE:</td><td><?php echo $row['screening_center']; ?></td></tr>
                    <tr><td class="fw-bold">DATE:</td><td><?php echo date('d-M-Y', strtotime($row['screening_date'])); ?></td></tr>
                    <tr><td class="fw-bold">TIME:</td><td>06:00 HRS GMT (SHARP)</td></tr>
                </tbody>
            </table>

            <p class="small mt-4"><strong>REQUISITE DOCUMENTS:</strong><br>
            Failure to report on the specified date and time results in automatic disqualification. You must report with the original copies of:<br>
            1. National Identification Card (Ghana Card).<br>
            2. WASSCE/SSCE and BECE Certificates.<br>
            3. Birth Certificate.</p>

            <div class="row mt-5 pt-4 align-items-center">
                <div class="col-8">
                    <div class="text-start">
                        <p class="mb-0">Electronically signed,</p>
                        <h6 class="fw-bold mb-0">Director of Manpower Planning</h6>
                        <small class="text-muted">GHQ Manpower Planning, Ghana Armed Forces</small>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=GAF-VERIFY-ID-<?php echo $applicant_id; ?>" class="border p-1 shadow-sm">
                    <p class="small fw-bold mb-0 mt-1">SCAN TO VERIFY</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($_SESSION['simulated_sms'])): ?>
<div id="sms-alert" class="shadow-lg no-print" style="position: fixed; top: 20px; right: 20px; width: 300px; z-index: 9999;">
    <div class="card border-0 bg-dark text-white shadow">
        <div class="card-header bg-secondary border-0 d-flex justify-content-between py-1">
            <span class="small fw-bold">GAF NOTIFICATION</span>
        </div>
        <div class="card-body py-2">
            <p class="small mb-0"><?php echo $_SESSION['simulated_sms']; ?></p>
        </div>
    </div>
</div>
<script>
    setTimeout(() => { document.getElementById('sms-alert').style.display = 'none'; }, 8000);
</script>
<?php unset($_SESSION['simulated_sms']); ?>
<?php endif; ?>

</body>
</html>