<?php
session_start();
include 'config/db_connect.php';

/**
 * SEQUENTIAL FLOW STEP 6: summary_form.php
 * Final Internal Review before generating the Appointment Slip.
 */

// SECURITY GATE
if (!isset($_SESSION['applicant_authenticated']) || $_SESSION['application_status'] !== 'Qualified' || !isset($_SESSION['screening_center'])) {
    header("Location: index.php");
    exit();
}

/**
 * CONSISTENT REFERENCE NUMBER
 */
if (!isset($_SESSION['final_ref_no'])) {
    $branch_initial = strtoupper(substr($_SESSION['branch_choice'] ?? 'X', 0, 1));
    $random_id = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['final_ref_no'] = "GAF-" . $branch_initial . "-" . $random_id;
}

$ref_no = $_SESSION['final_ref_no'];
$full_name = strtoupper($_SESSION['first_name'] . " " . $_SESSION['last_name']);

/* ============================
   QR CODE DATA
   ============================ */
$qr_data = "ID: $ref_no | NAME: $full_name | STATUS: QUALIFIED | CTR: " . $_SESSION['screening_center'];
$qr_api  = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Summary Report | <?php echo $ref_no; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --gaf-olive: #3d4621;
            --gaf-gold: #d4af37;
        }

        body {
            background: #f0f0f0;
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12px;
            color: #000;
        }

        /* A4 Paper Styling */
        .printable-area {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 45px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            position: relative;
            border: 1px solid #ddd;
            overflow: hidden;
        }

        /* WATERMARK */
        .gaf-seal {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 450px;
            opacity: 0.04; 
            z-index: 0;
            pointer-events: none;
        }

        /* HEADER BOX */
        .header-box {
            border-bottom: 3px double var(--gaf-olive);
            margin-bottom: 25px;
            padding-bottom: 10px;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .gaf-logo-top { width: 75px; margin-bottom: 5px; }

        /* SECTION HEADERS (Official Style) */
        .section-header { 
            background: #f9f9f9;
            color: #000;
            font-weight: 900; 
            padding: 5px 10px; 
            margin-top: 20px; 
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 12px;
            border-left: 5px solid var(--gaf-olive);
            border-bottom: 1px solid #ddd;
        }

        /* TOP SUMMARY LAYOUT */
        .top-summary-grid { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            margin-bottom: 15px; 
            position: relative; z-index: 1;
        }
        
        .passport-box img { 
            width: 140px; 
            height: 150px; 
            object-fit: cover; 
            border: 2px solid #100f0fff; 
            padding: 2px;
            display: block;
        }
        
        .qr-box img { 
            width: 120px; 
            height: 120px; 
            border: 1px solid #000; 
            padding: 2px; 
            background: white;
        }

        /* DATA LABELS (Restored Contrast) */
        .data-label { 
            font-weight: bold; 
            font-size: 11px;
            color: #555; /* Professional Gray */
            text-transform: uppercase;
            width: 140px;
            display: inline-block;
        }
        
        .data-value { 
            font-weight: 900; 
            color: #000; /* Pitch Black */
            text-transform: uppercase; 
            font-size: 12px;
        }
        
        .text-green { color: #02cd02ff !important; } 
        .text-red { color: #eb0000ff !important; }

        /* GRID SYSTEM */
        .data-row { display: flex; position: relative; z-index: 1; margin-bottom: 4px; }
        .col-left { width: 50%; padding-right: 15px; border-right: 1px dashed #ccc; }
        .col-right { width: 50%; padding-left: 15px; }

        /* DECLARATION */
        .declaration-text { 
            font-size: 12px; 
            text-align: justify; 
            line-height: 1.5; 
            margin-top: 10px; 
            position: relative; z-index: 1;
        }

        /* SIGNATURE LINES (Restored) */
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            position: relative; 
            z-index: 1;
        }
        .sig-box {
            width: 40%;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #000;
            margin-bottom: 5px;
        }
        .sig-label {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        @media print {
            body { background: white; margin: 0; }
            .no-print { display: none !important; }
            .printable-area { margin: 0; box-shadow: none; border: none; width: 100%; max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container no-print mt-3 text-center mb-4">
    <button onclick="window.print()" class="btn btn-dark btn-sm fw-bold"><i class="fas fa-print me-2"></i> PRINT REPORT</button>
    <a href="general_application.php" class="btn btn-success btn-sm fw-bold ms-2">FINISH <i class="fas fa-check ms-1"></i></a>
</div>

<div class="printable-area">
    <img src="assets/img/GAFB.jpg" class="gaf-seal" alt="GAF Watermark">

    <div class="header-box">
        <img src="assets/img/GAFB.jpg" class="gaf-logo-top" alt="GAF Logo">
        <h2 class="fw-bold mb-0 text-uppercase">Ghana Armed Forces</h2>
        <h5 class="text-uppercase mb-0">Recruitment Application Summary</h5>
        <h6 class="text-muted small">2025 Enlistment Phase 1</h6>
    </div>

    <div class="top-summary-grid">
        <div class="passport-box">
            <?php if(!empty($_SESSION['passport_path'])): ?>
                <img src="<?php echo $_SESSION['passport_path']; ?>">
            <?php else: ?>
                <img src="assets/img/placeholder.jpg">
            <?php endif; ?>
        </div>

        <div style="flex-grow: 1; padding-left: 20px; padding-top: 5px;">
            <div class="mb-2"><span class="data-label">APPLICANT ID:</span> <span class="data-value text-red"><?php echo $ref_no; ?></span></div>
            <div class="mb-2"><span class="data-label">CATEGORY:</span> <span class="data-value"><?php echo $_SESSION['branch_choice']; ?></span></div>
            <div class="mb-2"><span class="data-label">SCREENING CENTER:</span> <span class="data-value"><?php echo strtoupper($_SESSION['screening_center']); ?></span></div>
            <div class="mb-2"><span class="data-label">UNIT PREFERENCE:</span> <span class="data-value"><?php echo strtoupper($_SESSION['unit_choice'] ?? 'GENERAL RECRUIT'); ?></span></div>
            <div class="mb-2"><span class="data-label">AGE:</span> <span class="data-value"><?php echo $_SESSION['age']; ?> YEARS</span></div>
            <div class="mb-2"><span class="data-label">STATUS:</span> <span class="data-value text-green">QUALIFIED</span></div>
        </div>

        <div class="qr-box">
            <img src="<?php echo $qr_api; ?>">
        </div>
    </div>

    <div class="section-header">BIO DATA</div>
    <div class="data-row">
        <div class="col-left">
            <div class="mb-1"><span class="data-label">SURNAME:</span> <span class="data-value"><?php echo $_SESSION['last_name']; ?></span></div>
            <div class="mb-1"><span class="data-label">FIRST NAME:</span> <span class="data-value"><?php echo $_SESSION['first_name']; ?></span></div>
            <div class="mb-1"><span class="data-label">GENDER:</span> <span class="data-value"><?php echo substr($_SESSION['gender'], 0, 1); ?></span></div>
            <div class="mb-1"><span class="data-label">DOB:</span> <span class="data-value"><?php echo $_SESSION['dob']; ?></span></div>
            <div class="mb-1"><span class="data-label">MARITAL STATUS:</span> <span class="data-value"><?php echo strtoupper($_SESSION['marital_status'] ?? 'SINGLE'); ?></span></div>
            <div class="mb-1"><span class="data-label">EMAIL:</span> <span class="data-value" style="text-transform: lowercase; font-size:11px;"><?php echo $_SESSION['email']; ?></span></div>
        </div>
        <div class="col-right">
            <div class="mb-1"><span class="data-label">HOMETOWN REGION:</span> <span class="data-value"><?php echo strtoupper($_SESSION['hometown_region'] ?? 'N/A'); ?></span></div>
            <div class="mb-1"><span class="data-label">HOMETOWN DISTRICT:</span> <span class="data-value"><?php echo strtoupper($_SESSION['hometown_district'] ?? 'N/A'); ?></span></div>
            <div class="mb-1"><span class="data-label">HOMETOWN:</span> <span class="data-value"><?php echo strtoupper($_SESSION['hometown'] ?? 'N/A'); ?></span></div>
            <div class="mb-1"><span class="data-label">NATIONALITY:</span> <span class="data-value"><?php echo strtoupper($_SESSION['nationality'] ?? 'GHANAIAN'); ?></span></div>
            <div class="mb-1"><span class="data-label">PHONE:</span> <span class="data-value"><?php echo $_SESSION['phone_number']; ?></span></div>
            <div class="mb-1"><span class="data-label">HEIGHT / WEIGHT:</span> <span class="data-value"><?php echo $_SESSION['height']; ?> FT / <?php echo $_SESSION['weight']; ?> KG</span></div>
        </div>
    </div>

    <div class="section-header">WASSCE / ACADEMIC RECORD</div>
    
    <div class="data-row" style="border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 5px;">
        <div style="width: 50%;"><span class="data-label" style="width: auto;">SCHOOL:</span> <span class="data-value"><?php echo $_SESSION['shs_name']; ?></span></div>
        <div style="width: 25%;"><span class="data-label" style="width: auto;">INDEX NO:</span> <span class="data-value"><?php echo $_SESSION['shs_index']; ?></span></div>
        <div style="width: 25%; text-align: right;"><span class="data-label" style="width: auto;">YEAR:</span> <span class="data-value"><?php echo $_SESSION['shs_year'] ?? 'N/A'; ?></span></div>
    </div>
    
    <div class="row g-0 ps-2">
        <div class="col-6 mb-1"><span class="data-label">ENGLISH:</span> <span class="data-value"><?php echo $_SESSION['eng_grade']; ?></span></div>
        <div class="col-6 mb-1"><span class="data-label">CORE MATHEMATICS:</span> <span class="data-value"><?php echo $_SESSION['math_grade']; ?></span></div>
        <?php for($i=1; $i<=4; $i++): ?>
            <?php if(isset($_SESSION["el_n_$i"])): ?>
            <div class="col-6 mb-1">
                <span class="data-label"><?php echo substr($_SESSION["el_n_$i"], 0, 20); ?>:</span> 
                <span class="data-value"><?php echo $_SESSION["el_g_$i"]; ?></span>
            </div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>

    <div class="section-header">DECLARATION</div>
    <div class="declaration-text">
        1. I, <span style="font-weight: 900; text-decoration: underline; text-transform: uppercase; color: #000;"><?php echo $full_name; ?></span>, DECLARE THAT ALL THE INFORMATION PROVIDED ON THIS FORM IS CORRECT TO THE BEST OF MY KNOWLEDGE AND UNDERSTAND THAT ANY FALSE STATEMENT OR OMISSION MAY RENDER ME LIABLE TO PROSECUTION.
    </div>

    <div class="signature-section">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Applicant's Signature</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Vetting Officer Seal</div>
        </div>
    </div>

</div>

</body>
</html>