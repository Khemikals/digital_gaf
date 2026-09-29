<?php
session_start();
include 'config/db_connect.php';

/* Security check */
if (!isset($_SESSION['applicant_authenticated'])) {
    die("Access Denied: Please login via the tracking portal.");
}

$id = $_SESSION['applicant_id'];
$app = $conn->query("SELECT * FROM applicants WHERE applicant_id='$id'")->fetch_assoc();

if (!$app) { die("Error: Record not found."); }

$status = $app['application_status'];

/* Only disqualified */
if (!in_array($status, ['Rejected', 'Disqualified'])) {
    die("This slip is only available for disqualified applicants.");
}

$accent_color = "#dc3545";
$doc_title = "Disqualification Notice";
$ref = "GAF-REJ-" . str_pad($app['applicant_id'], 5, '0', STR_PAD_LEFT);

$qr_data = "GAF ID: $ref | {$app['first_name']} {$app['last_name']} | STATUS: $status";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $doc_title ?> | <?= $ref ?></title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<style>
body { font-family: 'Times New Roman', serif; background:#f0f0f0; padding:30px; }
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
        
#slip-content { width:210mm; min-height:297mm; background:#fff; padding:40px; margin:auto; }

.header { text-align:center; border-bottom:4px double <?= $accent_color ?>; margin-bottom:20px; }
.header img { width:75px; }

.top-info-row { display:flex; justify-content:space-between; margin-bottom:25px; }
.ref-box { font-weight:bold; font-size:0.95rem; }

.passport-photo {
    width:120px;
    height:120px;
    object-fit:cover;
    border:3px solid <?= $accent_color ?>;
}

.status-box {
    background:#fff5f5;
    border:1px dashed <?= $accent_color ?>;
    padding:15px;
    text-align:center;
    font-weight:bold;
    margin-bottom:25px;
}

.notice { line-height:1.6; }

.verification-footer {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:50px;
}

.signature-block { font-size:0.95rem; }
.qr-section { text-align:center; }

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
</body>

<body>
     <div class="watermark">GAF OFFICIAL DOCUMENT</div>

<div id="slip-content">

    <!-- HEADER -->
   <div class="header">
        <img src="assets/img/GAFB.jpg" style="width: 75px;"><br>
        <h2 style="margin:5px 0 0 0; letter-spacing: 2px;">GHANA ARMED FORCES</h2>
        <h4 style="margin:0; color:#3d4621; text-transform: uppercase;">Recruitment & Enlistment Board</h4>
        <p style="margin: 2px 0; font-size: 0.9rem;">Directorate of Manpower Planning, Burma Camp, Accra</p>
    </div>

    <!-- INFO ROW -->
    <div class="top-info-row">
        <div class="ref-box">
            <p>REF NO: <?= $ref ?></p>
            <p>DATE: <?= date('d F, Y') ?></p>
            <p>NAME: <?= strtoupper($app['last_name'].", ".$app['first_name']) ?></p>
            <p>GHANA CARD: <?= htmlspecialchars($app['ghana_card']) ?></p>
        </div>

        <div>
            <?php if(!empty($app['passport_path'])): ?>
                <img src="<?= $app['passport_path'] ?>" class="passport-photo">
            <?php endif; ?>
        </div>
    </div>

    <!-- STATUS -->
    <div class="status-box">
        STATUS: <?= strtoupper($status) ?>
    </div>

    <!-- NOTICE -->
    <div class="notice">
        <p><b>SUBJECT: DISQUALIFICATION NOTICE</b></p>
        <p>
            Following verification of your application, you have been
            <b>DISQUALIFIED</b> due to inconsistencies or irregularities.
        </p>
        <p>
            Remarks:
            <i><?= nl2br(htmlspecialchars($app['vetting_remarks'] ?? 'Irregularities detected.')) ?></i>
        </p>
        <p>This decision is final.</p>
    </div>

    <!-- SIGNATURE + QR -->
    <div class="verification-footer">

        <!-- LEFT -->
        <div class="signature-block">
            <p><b>Digitally Authorized by:</b></p>
            <p style="text-decoration:underline;">(Signed Electrically)</p>
            <p><b>RECRUITMENT COMMANDER</b></p>
            <p>For: Chief of the Defence Staff</p>
        </div>

        <!-- RIGHT -->
        <div class="qr-section">
            <div id="qr-verification"></div>
            <small>SCAN TO VERIFY</small>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        Verification Code: <?= md5($app['applicant_id'].$app['ghana_card']) ?><br>
        This document is system-generated and electronically authorized.
    </div>

</div>

<script>
new QRCode(document.getElementById("qr-verification"), {
    text: "<?= addslashes($qr_data) ?>",
    width: 120,
    height: 120
});
</script>

</body>
</html>


