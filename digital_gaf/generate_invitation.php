<?php
session_start();
include 'config/db_connect.php';

// Security check
if (!isset($_SESSION['applicant_authenticated']) || ($_SESSION['app_status'] !== 'Approved' && $_SESSION['app_status'] !== 'Qualified')) {
    die("Access Denied: Only approved candidates may generate this document.");
}

$id = $_SESSION['applicant_id'];
$app = $conn->query("SELECT * FROM applicants WHERE applicant_id = '$id'")->fetch_assoc();
$ref = "GAF-INV-" . str_pad($app['applicant_id'], 5, '0', STR_PAD_LEFT);

// Generate Verification URL for QR Code
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$verify_url = "$protocol://$host/digital_gaf/track_progress.php?verify_id=" . $app['applicant_id'];
$qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($verify_url);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Invitation | <?php echo $ref; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Times New Roman', serif; padding: 40px; line-height: 1.5; color: #1a1a1a; background: #fff; }
        
        /* Floating Print Button Styling */
        .print-nav {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3d4621;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
            border: 2px solid #d4af37;
            font-weight: bold;
            transition: 0.3s;
        }
        .print-nav:hover { background: #d4af37; color: #1a1a1a; transform: scale(1.05); }

        /* Header & Branding */
        .header { text-align: center; border-bottom: 3px double #3d4621; margin-bottom: 25px; padding-bottom: 10px; position: relative; }
        .watermark { position: fixed; top: 35%; left: 10%; font-size: 7rem; opacity: 0.04; transform: rotate(-45deg); pointer-events: none; z-index: 0; font-weight: bold; color: #3d4621; }
        
        /* Layout Grid */
        .bio-container { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .applicant-info { flex: 1; }
        .passport-photo { width: 140px; height: 140px; border: 2px solid #333; object-fit: cover; padding: 2px; background: #fff; }

        .ref-row { display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .content { text-align: justify; margin-bottom: 30px; z-index: 1; position: relative; }
        
        .verification-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 40px; }
        .qr-section { text-align: center; }
        .qr-label { font-size: 0.7rem; font-weight: bold; margin-top: 5px; display: block; color: #3d4621; }
        
        .footer { margin-top: 40px; font-size: 0.8rem; border-top: 1px solid #ccc; padding-top: 10px; text-align: center; font-style: italic; }
        
        /* PRINT RULES: Hide UI elements when printing */
        @media print { 
            .no-print { display: none !important; } 
            body { padding: 0; margin: 0; }
            .print-nav { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="print-nav no-print" onclick="window.print()">
        <i class="fas fa-print me-2"></i> PRINT INVITATION
    </div>

    <div class="watermark">GAF OFFICIAL DOCUMENT</div>

    <div class="header">
        <img src="assets/img/GAFB.jpg" style="width: 75px;"><br>
        <h2 style="margin:5px 0 0 0; letter-spacing: 2px;">GHANA ARMED FORCES</h2>
        <h4 style="margin:0; color:#3d4621; text-transform: uppercase;">Recruitment & Enlistment Board</h4>
        <p style="margin: 2px 0; font-size: 0.9rem;">Directorate of Manpower Planning, Burma Camp, Accra</p>
    </div>

    <div class="ref-row">
        <div>REF: <?php echo $ref; ?></div>
        <div>ISSUED: <?php echo date('d F, Y'); ?></div>
    </div>

    <div class="bio-container">
        <div class="applicant-info">
            <p style="margin: 0;"><b>NAME:</b> <?php echo strtoupper($app['last_name'] . ", " . $app['first_name']); ?></p>
            <p style="margin: 5px 0;"><b>GHANA CARD:</b> <?php echo htmlspecialchars($app['ghana_card']); ?></p>
            <p style="margin: 5px 0;"><b>SERIAL NO:</b> <?php echo str_pad($app['applicant_id'], 6, '0', STR_PAD_LEFT); ?></p>
            <p style="margin: 5px 0;"><b>CENTER:</b> <?php echo $app['screening_center'] ?? 'Burma Camp, Accra'; ?></p>
        </div>
        <div class="passport-wrap">
            <?php if(!empty($app['passport_path'])): ?>
                <img src="<?php echo $app['passport_path']; ?>" class="passport-photo">
            <?php else: ?>
                <div class="passport-photo" style="display:flex; align-items:center; justify-content:center; background:#f0f0f0; font-size: 10px; text-align:center;">PHOTO NOT FOUND</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <p><b>SUBJECT: FORMAL INVITATION FOR PHYSICAL BODY SCREENING AND DOCUMENT VERIFICATION</b></p>
        
        <p>1. You are hereby informed that following the <b>SUCCESSFUL</b> audit of your digital credentials, you have been shortlisted for the final physical screening phase of the GAF recruitment cycle.</p>
        
        <p>2. You are requested to report to the designated center on your scheduled date. You <b>MUST</b> present the following original documents for physical inspection:</p>
        <ul style="margin-left: 20px;">
            <li>This Official Invitation Letter (Printed Copy).</li>
            <li>Original National Identification Card (Ghana Card).</li>
            <li>Original Birth Certificate and Educational Certificates (BECE/WASSCE).</li>
            <li>Two (2) recent passport-sized photographs (White background).</li>
        </ul>

        <p>3. <b>UNIT ASSIGNMENT:</b> Your preferred choice of service is recorded as <b><?php echo strtoupper($app['branch_choice']); ?></b>. Final placement is subject to performance in the physical tests.</p>
        
        <p>4. <b>WARNING:</b> Presentation of forged documents or false statements is a criminal offense under the GAF recruitment code and will lead to immediate arrest and prosecution.</p>
    </div>

    <div class="verification-footer">
        <div class="signature-block">
            <p><b>Digitally Authorized by:</b></p>
            <div style="margin-top: 10px;">
                <p style="text-decoration: underline; margin: 0;">(Signed Electrically)</p>
                <p style="margin: 0;"><b>RECRUITMENT COMMANDER</b></p>
                <p style="margin: 0; font-size: 0.9rem;">For: Chief of the Defence Staff</p>
            </div>
        </div>

        <div class="qr-section">
            <img src="<?php echo $qr_api_url; ?>" alt="Verification QR Code" style="border: 1px solid #ddd; padding: 5px;">
            <span class="qr-label">SCAN TO VERIFY</span>
        </div>
    </div>

    <div class="footer">
        Verification Code: <?php echo md5($app['applicant_id'] . $app['ghana_card']); ?> <br>
        This document is valid only when presented with a matching National ID.
    </div>
</body>
</html>