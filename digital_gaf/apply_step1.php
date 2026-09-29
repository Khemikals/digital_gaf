<?php
session_start();
include 'config/db_connect.php';

/**
 * SEQUENTIAL FLOW STEP 3: apply_step1.php
 * SECURITY GATE: Ensure applicant is authenticated.
 */
if (!isset($_SESSION['applicant_authenticated']) || $_SESSION['applicant_authenticated'] !== true) {
    header("Location: index.php?error=unauthorized");
    exit();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_bio'])) {
    
    // 1. Calculate Age
    $dob_input = $_POST['dob'];
    $dob = new DateTime($dob_input);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    // 2. Map Form Data to Session Variables (Matching your DB Schema)
    $_SESSION['first_name']        = strtoupper($_POST['first_name']);
    $_SESSION['last_name']         = strtoupper($_POST['last_name']);
    $_SESSION['gender']            = $_POST['gender'];
    $_SESSION['dob']               = $dob_input;
    $_SESSION['age']               = $age;
    
    // Matched to your DB columns:
    $_SESSION['ghana_card_number'] = strtoupper($_POST['ghana_card_number']); 
    $_SESSION['digital_address']   = strtoupper($_POST['digital_address']);
    $_SESSION['phone_number']      = $_POST['phone_number'];
    $_SESSION['email']             = strtolower($_POST['email']);
    
    // New fields (Ensure you ran the SQL Update above!):
    $_SESSION['marital_status']    = $_POST['marital_status'];
    $_SESSION['nationality']       = strtoupper($_POST['nationality']);
    $_SESSION['hometown']          = strtoupper($_POST['hometown']);
    $_SESSION['hometown_region']   = strtoupper($_POST['hometown_region']);
    $_SESSION['hometown_district'] = strtoupper($_POST['hometown_district']);
    $_SESSION['branch_choice']     = $_POST['branch_choice']; 
    $_SESSION['unit_choice']       = $_POST['unit_choice']; 
    $_SESSION['height']            = $_POST['height']; 
    $_SESSION['weight']            = $_POST['weight']; 
    
    // 3. Handle Passport Photo Upload
    if (isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] == 0) {
        $target_dir = "uploads/passports/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        // Clean filename using the Ghana Card Number
        $safe_id = preg_replace('/[^A-Za-z0-9\-]/', '', $_SESSION['ghana_card_number']);
        $file_ext = pathinfo($_FILES['passport_photo']['name'], PATHINFO_EXTENSION);
        $file_name = $safe_id . "_photo." . $file_ext;
        
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES['passport_photo']['tmp_name'], $target_file)) {
            $_SESSION['passport_path'] = $target_file;
        }
    }

    // 4. Proceed to Next Step
    header("Location: apply_step2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Step 1: Bio-Information | GAF Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-green: #3d4621; --gaf-red: #8b0000; --gaf-gold: #d4af37; }
        body { font-family: 'Segoe UI', sans-serif; }
        
        /* Force uppercase on text inputs */
        input[type="text"], textarea { text-transform: uppercase; }
        
        .passport-preview-container {
            width: 160px; height: 160px;
            border: 3px dashed var(--gaf-green);
            background-color: #ffffff;
            margin: 0 auto 15px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; border-radius: 8px; position: relative;
        }
        #preview-img { width: 100%; height: 100%; object-fit: cover; display: none; }
        .section-title { color: var(--gaf-red); font-weight: bold; border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; }
        .card-header { background-color: var(--gaf-green) !important; border-bottom: 4px solid var(--gaf-gold); }
        .progress-bar-container { height: 8px; background: #e9ecef; border-radius: 4px; margin-bottom: 30px; }
        .progress-fill { width: 25%; height: 100%; background: var(--gaf-gold); border-radius: 4px; }
    </style>
</head>
<body class="bg-light py-5">

<div class="container">
    <div class="card shadow-lg mx-auto border-0" style="max-width: 950px;">
        <div class="card-header text-white text-center py-3">
            <h4 class="mb-0 fw-bold text-uppercase">Application Progress: 25%</h4>
            <img src="assets/img/GAFB.jpg" style="width: 75px;"><br>
        </div>
        <div class="card-body p-5">
            <div class="progress-bar-container"><div class="progress-fill"></div></div>
            
            <form action="apply_step1.php" method="POST" enctype="multipart/form-data">
                
                <div class="text-center">
                    <div class="passport-preview-container">
                        <img id="preview-img" src="#">
                        <div id="placeholder" class="text-muted small p-2">
                            <i class="fas fa-user-tie fa-3x d-block mb-2"></i>
                            PASSPORT PHOTO<br>(2x2 SQUARE)
                        </div>
                    </div>
                    <label class="btn btn-dark btn-sm rounded-pill px-4">
                        CHOOSE IMAGE <input type="file" name="passport_photo" id="photo-input" hidden accept="image/*" required>
                    </label>
                    <p class="text-muted small mt-2">Maximum file size: 2MB (.jpg, .png)</p>
                </div>

                <div class="section-title">PERSONAL INFORMATION</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">First Name(s)</label>
                        <input type="text" name="first_name" class="form-control" placeholder="E.G. KWESI KOFI" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Surname</label>
                        <input type="text" name="last_name" class="form-control" placeholder="E.G. MENSAH" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="" disabled selected>SELECT</option>
                            <option value="Male">MALE</option>
                            <option value="Female">FEMALE</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">Marital Status</label>
                        <select name="marital_status" class="form-select" required>
                            <option value="" disabled selected>SELECT</option>
                            <option value="Single">SINGLE</option>
                            <option value="Married">MARRIED</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Nationality</label>
                        <input type="text" name="nationality" class="form-control" value="GHANAIAN" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Ghana Card Number (NIA)</label>
                        <input type="text" name="ghana_card_number" class="form-control" placeholder="GHA-000000000-0" pattern="GHA-[0-9]{9}-[0-9]{1}" required>
                    </div>
                </div>

                <div class="section-title">HOMETOWN & LOCATION DETAILS</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Hometown Region</label>
                        <select name="hometown_region" class="form-select" required>
                            <option value="" disabled selected>-- SELECT REGION --</option>
                            <option value="ASHANTI">ASHANTI</option>
                            <option value="GREATER ACCRA">GREATER ACCRA</option>
                            <option value="CENTRAL">CENTRAL</option>
                            <option value="EASTERN">EASTERN</option>
                            <option value="WESTERN">WESTERN</option>
                            <option value="VOLTA">VOLTA</option>
                            <option value="NORTHERN">NORTHERN</option>
                            <option value="UPPER EAST">UPPER EAST</option>
                            <option value="UPPER WEST">UPPER WEST</option>
                            <option value="BONO">BONO</option>
                            <option value="BONO EAST">BONO EAST</option>
                            <option value="AHAFO">AHAFO</option>
                            <option value="OTI">OTI</option>
                            <option value="NORTH EAST">NORTH EAST</option>
                            <option value="SAVANNAH">SAVANNAH</option>
                            <option value="WESTERN NORTH">WESTERN NORTH</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Hometown District</label>
                        <input type="text" name="hometown_district" class="form-control" placeholder="E.G. KWABRE EAST" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Hometown</label>
                        <input type="text" name="hometown" class="form-control" placeholder="E.G. MAMPONTENG" required>
                    </div>
                </div>

                <div class="section-title">CONTACT INFORMATION</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="example@email.com" style="text-transform: lowercase;" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Primary Phone</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">+233</span>
                            <input type="tel" name="phone_number" class="form-control" placeholder="24XXXXXXX" pattern="[0-9]{9}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Digital Address (GPS)</label>
                        <input type="text" name="digital_address" class="form-control" placeholder="BV-XXXX-XXXX" pattern="[A-Z0-9]{2}-[0-9]{3,4}-[0-9]{3,4}" title="Format: BV-000-0000" required>
                    </div>
                </div>

                <div class="section-title">SERVICE VACANCY & UNIT SELECTION</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-danger">Service Branch</label>
                        <select name="branch_choice" id="branch_select" class="form-select border-danger" required>
                            <option value="" disabled selected>-- SELECT SERVICE --</option>
                            <option value="Army">GHANA ARMY</option>
                            <option value="Navy">GHANA NAVY</option>
                            <option value="Air Force">GHANA AIR FORCE</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-danger">Preferred Unit / Corps</label>
                        <select name="unit_choice" id="unit_select" class="form-select border-danger" required disabled>
                            <option value="" disabled selected>-- SELECT BRANCH FIRST --</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold small">Height (Feet)</label>
                        <input type="number" step="0.01" name="height" class="form-control" placeholder="5.8" min="5.0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">Weight (Kg)</label>
                        <input type="number" step="0.1" name="weight" class="form-control" placeholder="65.0" min="45" required>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <button type="submit" name="save_bio" class="btn btn-success btn-lg px-5 fw-bold rounded-pill shadow">
                        CONTINUE TO ACADEMICS <i class="fas fa-chevron-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Photo Preview Logic
    const photoInput = document.getElementById('photo-input');
    const previewImg = document.getElementById('preview-img');
    const placeholder = document.getElementById('placeholder');

    photoInput.onchange = evt => {
        const [file] = photoInput.files;
        if (file) {
            previewImg.src = URL.createObjectURL(file);
            previewImg.style.display = "block";
            placeholder.style.display = "none";
        }
    }

    // 2. Dynamic Unit Selection Logic
    const units = {
        "Army": [
            "Infantry",
            "Armoured",
            "Artillery",
            "Corps of Signals",
            "Corps of Engineers",
            "Electrical & Mechanical Engineers (EME)",
            "Military Police",
            "Army Pay Corps"
        ],
        "Navy": [
            "Executive Branch (Seaman)",
            "Marine Engineering",
            "Weapon Electrical Engineering",
            "Supply & Secretariat",
            "Marine Infantry (Naval Infantry)",
            "Special Boat Squadron (SBS)",
            "Hydrographic Unit",
            "Naval Dockyard"
        ],
        "Air Force": [
            "Pilot / Aircrew",
            "Engineering Wing",
            "Supply Wing",
            "Admin Wing",
            "Air Force Regiment (Ground Defence)",
            "Transport Squadron",
            "Helicopter Squadron",
            "Fighter Ground Attack Squadron",
            "UAV Unit"
        ]
    };

    const branchSelect = document.getElementById('branch_select');
    const unitSelect = document.getElementById('unit_select');

    branchSelect.addEventListener('change', function() {
        const selectedBranch = this.value;
        
        // Reset Unit Dropdown
        unitSelect.innerHTML = '<option value="" disabled selected>-- SELECT UNIT --</option>';
        unitSelect.disabled = false;

        // Populate with specific units
        if (units[selectedBranch]) {
            units[selectedBranch].forEach(function(unit) {
                const option = document.createElement('option');
                option.value = unit;
                option.text = unit.toUpperCase();
                unitSelect.appendChild(option);
            });
        }
    });
</script>
</body>
</html>