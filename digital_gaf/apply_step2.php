<?php
session_start();
include 'config/db_connect.php';

/**
 * SEQUENTIAL FLOW STEP 4: apply_step2.php
 * SECURITY GATE: Ensure applicant is authenticated AND has completed Step 1.
 */
if (!isset($_SESSION['applicant_authenticated']) || !isset($_SESSION['first_name'])) {
    header("Location: apply_step1.php?error=missing_step");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_app'])) {
    // 1. Capture JHS Data
    $_SESSION['jhs_name']   = strtoupper($_POST['jhs_name']);
    $_SESSION['jhs_index']  = $_POST['jhs_index']; 
    $_SESSION['jhs_year']   = $_POST['jhs_year']; // New Field
    $_SESSION['jhs_math']   = $_POST['jhs_math'];
    $_SESSION['jhs_eng']    = $_POST['jhs_eng'];

    // 2. Capture SHS Data
    $_SESSION['shs_name']    = strtoupper($_POST['shs_name']);
    $_SESSION['shs_index']   = $_POST['shs_index']; 
    $_SESSION['shs_year']    = $_POST['shs_year']; // New Field
    $_SESSION['shs_program'] = $_POST['shs_program'];
    $_SESSION['math_grade']  = $_POST['math_grade'];
    $_SESSION['eng_grade']   = $_POST['eng_grade'];
    $_SESSION['sci_grade']   = $_POST['sci_grade'] ?? 'C6'; 

    // 3. Capture Electives
    for($i=1; $i<=4; $i++) {
        $_SESSION["el_n_$i"] = strtoupper($_POST["el_n_$i"]);
        $_SESSION["el_g_$i"] = $_POST["el_g_$i"];
    }

    // 4. Sequential Redirection to Step 5: apply_step3.php
    header("Location: apply_step3.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Step 2: Academic Record | GAF-DRSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .gaf-header { background: var(--gaf-olive); color: white; border-bottom: 5px solid var(--gaf-gold); padding: 20px; text-align: center; }
        .page-title { color: var(--gaf-olive); font-weight: 800; letter-spacing: 1px; text-align: center; margin-top: 20px; text-transform: uppercase; }
        .section-tag { background: var(--gaf-olive); color: white; padding: 5px 15px; font-weight: bold; border-left: 5px solid var(--gaf-gold); margin: 20px 0; display: inline-block; }
        .btn-gaf { background: var(--gaf-olive); color: white; border: 1px solid var(--gaf-gold); font-weight: bold; padding: 12px; width: 100%; border-radius: 30px; }
        .btn-gaf:hover { background: #2a3116; color: var(--gaf-gold); }
        .progress-bar-container { height: 8px; background: #e9ecef; border-radius: 4px; margin-bottom: 10px; }
        .progress-fill { width: 50%; height: 100%; background: var(--gaf-gold); border-radius: 4px; }
        
        /* Enforce Uppercase visually while typing */
        input[type="text"], .sub-list { text-transform: uppercase; }
    </style>
</head>
<body>

<header class="gaf-header">
    <h2 class="fw-bold text-uppercase">Ghana Armed Forces</h2>
    <p class="mb-0 text-uppercase small" style="letter-spacing: 2px;">Recruitment & Screening System (DRSS)</p>
</header>

<div class="container mb-5">
    <div class="mt-4 text-center">
        <h3 class="page-title mb-1">Step 2: Academic Record</h3>
        <img src="assets/img/GAFB.jpg" style="width: 75px;"><br>
        <div class="mx-auto" style="max-width: 400px;">
            <div class="progress-bar-container mt-2"><div class="progress-fill"></div></div>
            <small class="text-muted fw-bold">Application Progress: 50%</small>
        </div>
    </div>
    
    <div class="card shadow p-4 mx-auto mt-4 border-0" style="max-width: 950px;">
        <form method="POST" action="apply_step2.php" id="academicForm">
            
            <div class="section-tag">JUNIOR HIGH SCHOOL (BECE)</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold">JHS School Name</label>
                    <input type="text" name="jhs_name" class="form-control" placeholder="E.G. RIDGE MODEL JHS" required>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Index Number (10 Digits)</label>
                    <input type="text" name="jhs_index" class="form-control" pattern="[0-9]{10}" title="10-digit Index Number required" placeholder="1010101010" required>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Completion Year</label>
                    <select name="jhs_year" class="form-select" required>
                        <option value="">Year</option>
                        <?php 
                        $curYear = date('Y'); 
                        for($y=$curYear; $y>=2005; $y--) echo "<option value='$y'>$y</option>"; 
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">BECE Math Grade (1-9)</label>
                    <select name="jhs_math" class="form-select">
                        <?php for($i=1;$i<=9;$i++) echo "<option value='$i'>$i</option>"; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">BECE English Grade (1-9)</label>
                    <select name="jhs_eng" class="form-select">
                        <?php for($i=1;$i<=9;$i++) echo "<option value='$i'>$i</option>"; ?>
                    </select>
                </div>
            </div>

            <div class="section-tag">SENIOR HIGH / TVET (WASSCE)</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold">SHS/Technical Institution Name</label>
                    <input type="text" name="shs_name" class="form-control" placeholder="E.G. PREMPEH COLLEGE" required>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Index Number (10 Digits)</label>
                    <input type="text" name="shs_index" class="form-control" pattern="[0-9]{10}" title="10-digit Index Number required" placeholder="0050101001" required>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Completion Year</label>
                    <select name="shs_year" class="form-select" required>
                        <option value="">Year</option>
                        <?php 
                        for($y=$curYear; $y>=2008; $y--) echo "<option value='$y'>$y</option>"; 
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Program of Study</label>
                    <select name="shs_program" id="progSelect" class="form-select" onchange="popSub()" required>
                        <option value="">-- Select Program --</option>
                        <option value="General Science">General Science</option>
                        <option value="Agricultural Science">Agricultural Science</option>
                        <option value="Business">Business</option>
                        <option value="General Arts">General Arts</option>
                        <option value="Home Economics">Home Economics</option>
                        <option value="Visual Arts">Visual Arts</option>
                        <option value="Technical/TVET">Technical/TVET</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-danger">Core Math Grade</label>
                    <select name="math_grade" class="form-select" required>
                        <?php $g=['A1','B2','B3','C4','C5','C6','D7','E8','F9']; foreach($g as $v) echo "<option value='$v'>$v</option>"; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-danger">Core English Grade</label>
                    <select name="eng_grade" class="form-select" required>
                        <?php foreach($g as $v) echo "<option value='$v'>$v</option>"; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4 fw-bold small text-muted border-bottom text-uppercase">Select 4 Elective Subjects & Grades</div>
            <div class="row g-2 mt-1">
                <?php for($i=1;$i<=4;$i++): ?>
                <div class="col-md-8">
                    <select name="el_n_<?=$i?>" class="form-select sub-list" required>
                        <option value="">-- Waiting for Program --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="el_g_<?=$i?>" class="form-select">
                        <?php foreach($g as $v) echo "<option value='$v'>$v</option>"; ?>
                    </select>
                </div>
                <?php endfor; ?>
            </div>

            <div class="text-center">
                <button type="submit" name="submit_app" class="btn btn-gaf mt-5 shadow">
                    CONTINUE TO STEP 3: DOCUMENT UPLOAD <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Subjects converted to uppercase values for consistency
const subs = {
    "General Science": ["ICT ELECT","INTEGRATED SCIENCE","ELECTIVE MATHS", "BIOLOGY", "CHEMISTRY", "PHYSICS"],
    "Agricultural Science": ["INTEGRATED SCIENCE","GENERAL AGRICULTURE", "ANIMAL HUSBANDRY", "HORTICULTURE", "CHEMISTRY", "PHYSICS", "ELECTIVE MATHS"],
    "Business": ["INTEGRATED SCIENCE","FINANCIAL ACCOUNTING", "BUSINESS MANAGEMENT", "ECONOMICS", "COSTING", "ELECTIVE MATHS"],
    "General Arts": ["INTEGRATED SCIENCE","ECONOMICS", "GEOGRAPHY", "HISTORY", "GOVERNMENT", "LITERATURE", "CRS/IRS", "DAGAARE", "TWI", "FRENCH", "ELECTIVE MATHS"],
    "Home Economics": ["INTEGRATED SCIENCE","MANAGEMENT IN LIVING", "FOOD AND NUTRITION", "CLOTHING AND TEXTILES", "GENERAL KNOWLEDGE IN ART", "BIOLOGY", "ECONOMICS"],
    "Visual Arts": ["INTEGRATED SCIENCE","GENERAL KNOWLEDGE IN ART", "GRAPHIC DESIGN", "SCULPTURE", "CERAMICS", "TEXTILES", "BASKETRY", "LEATHERWORK", "JEWELLERY", "ECONOMICS"],
    "Technical/TVET": ["INTEGRATED SCIENCE","APPLIED ELECTRICITY", "TECHNICAL DRAWING", "ELECTRONICS", "AUTO MECHANICS", "BUILDING CONSTRUCTION", "WOODWORK", "METALWORK"]
};

function popSub() {
    const p = document.getElementById('progSelect').value;
    const boxes = document.querySelectorAll('.sub-list');
    const list = subs[p] || [];
    boxes.forEach(b => {
        b.innerHTML = '<option value="">-- SELECT SUBJECT --</option>';
        list.forEach(s => { b.innerHTML += `<option value="${s}">${s}</option>`; });
    });
}
</script>
</body>
</html>