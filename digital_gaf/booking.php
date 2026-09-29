<?php
session_start();
include 'config/db_connect.php';

/**
 * SEQUENTIAL FLOW STEP 5: booking.php
 * Updated to redirect to summary_form.php
 */

// SECURITY GATE: Ensure applicant is authenticated and qualified
if (!isset($_SESSION['applicant_authenticated']) || !isset($_SESSION['applicant_id'])) {
    header("Location: apply_step4.php?error=missing_review");
    exit();
}

// Redirect if they were somehow disqualified but reached here
if (isset($_SESSION['application_status']) && $_SESSION['application_status'] !== 'Qualified') {
    header("Location: summary_final.php");
    exit();
}

$error = "";
$today = date('Y-m-d');
$oneMonthLater = date('Y-m-d', strtotime('+1 month'));

// Process Booking Submission
if (isset($_POST['book_slot'])) {
    $center = $conn->real_escape_string($_POST['center']);
    $bDate = $conn->real_escape_string($_POST['screen_date']);
    $app_id = $_SESSION['applicant_id'];
    
    $_SESSION['screening_center'] = $center;
    $_SESSION['screening_date'] = $bDate;
    
    // Update the permanent database record
    $sql = "UPDATE applicants SET 
            screening_center = '$center', 
            screening_date = '$bDate' 
            WHERE applicant_id = '$app_id'";
    
    if ($conn->query($sql)) {
        // REDIRECTION UPDATE: Point to summary_form.php
        header("Location: summary_form.php");
        exit();
    } else {
        $error = "System Error: Could not finalize booking. " . $conn->error;
    }
}

$regions = [
    "Greater Accra" => "Burma Camp, Liberation Barracks (Accra)",
    "Ashanti" => "4BN Military Barracks, Uaddara (Kumasi)",
    "Northern" => "Kamina Barracks, 6BN (Tamale)",
    "Western" => "2BN Military Barracks, Myohaung (Takoradi)",
    "Volta" => "66 Artillery Regiment (Ho)",
    "Eastern" => "1st Infantry Battalion (Michel Camp/Tema-Koforidua)",
    "Central" => "603rd Coast Guard Unit (Cape Coast)",
    "Bono" => "3BN Military Barracks (Sunyani)",
    "Upper East" => "11BN Military Barracks (Bolgatanga)",
    "Upper West" => "10BN Military Barracks (Wa)",
    "Bono East" => "Landmark Police Regional HQ (Techiman)",
    "Ahafo" => "Landmark Regional Admin Block (Goaso)",
    "Oti" => "Landmark Regional Police Command (Dambai)",
    "North East" => "Landmark Regional Admin Post (Nalerigu)",
    "Savannah" => "Landmark Regional Command (Damongo)",
    "Western North" => "Landmark Regional Admin Block (Wiawso)"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Step 5: Screening and Documentation Booking | GAF-DRSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .gaf-header { background: var(--gaf-olive); color: white; border-bottom: 5px solid var(--gaf-gold); padding: 20px; text-align: center; }
        .booking-card { max-width: 800px; margin: 40px auto; border-radius: 10px; border-top: 10px solid var(--gaf-olive); border-bottom: none; }
        .btn-gaf { background: var(--gaf-olive); color: white; font-weight: bold; padding: 12px; border: none; }
        .btn-gaf:hover { background: #2a3116; color: var(--gaf-gold); }
        .instruction-box { background: #fff3cd; border-left: 5px solid #ffc107; padding: 15px; font-size: 0.9rem; border-radius: 4px; }
        .progress-bar-container { height: 8px; background: #e9ecef; border-radius: 4px; margin-bottom: 10px; }
        .progress-fill { width: 100%; height: 100%; background: var(--gaf-gold); border-radius: 4px; }
    </style>
</head>
<body>

<header class="gaf-header">
     <img src="assets/img/GAFB.jpg" width="80" class="mb-3">
    <h2 class="fw-bold text-uppercase">Ghana Armed Forces</h2>
    <p class="mb-0 text-uppercase small" style="letter-spacing: 2px;">Recruitment & Screening System (DRSS)</p>
</header>

<div class="container">
    <div class="mt-4 text-center">
        <div class="mx-auto" style="max-width: 400px;">
            <div class="progress-bar-container"><div class="progress-fill"></div></div>
            <small class="text-muted fw-bold">FINAL DATA ENTRY: 100%</small>
        </div>
    </div>

    <div class="card booking-card shadow-lg p-4 mt-4 border-0">
        <h4 class="fw-bold text-center mb-4"><i class="fas fa-calendar-check me-2"></i> SCREENING & DOCUMENTATION BOOKING</h4>
        
        <div class="instruction-box mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Important:</strong> Report to your selected center by <strong>06:00 GMT</strong> on your selected date.
        </div>

        <?php if(!empty($error)) echo "<div class='alert alert-danger py-2 small'>$error</div>"; ?>

        <form method="POST">
            <div class="row g-4">
                <div class="col-md-12">
                    <label class="fw-bold small mb-2">Select Regional Screening Center</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt"></i></span>
                        <select name="center" class="form-select" required>
                            <option value="" selected disabled>-- Choose Center --</option>
                            <?php foreach($regions as $reg => $landmark): ?>
                                <option value="<?php echo htmlspecialchars($landmark); ?>">
                                    <?php echo htmlspecialchars($reg); ?>: <?php echo htmlspecialchars($landmark); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <label class="fw-bold small mb-2">Preferred Screening Date</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-light"><i class="fas fa-calendar-day"></i></span>
                        <input type="date" name="screen_date" class="form-control" 
                               min="<?php echo $today; ?>" 
                               max="<?php echo $oneMonthLater; ?>" required>
                    </div>
                    <div class="form-text small">Booking window: <strong><?php echo $today; ?></strong> to <strong><?php echo $oneMonthLater; ?></strong>.</div>
                </div>
            </div>

            <hr class="my-4">

            <button type="submit" name="book_slot" class="btn btn-gaf w-100 text-uppercase rounded-pill fw-bold shadow">
                Generate Summary Form <i class="fas fa-file-invoice ms-2"></i>
            </button>
        </form>
    </div>
</div>

</body>
</html>