<?php
include 'db_connect.php';

// 1. Get the Applicant ID from the URL (e.g., success.php?id=105)
if (!isset($_GET['id'])) {
    die("❌ Error: No Applicant ID provided.");
}

$id = $_GET['id'];

// 2. Fetch their details from the Database
$sql = "SELECT p.*, a.application_status, e.institution_name, e.programme_of_study 
        FROM applicants p 
        JOIN applications a ON p.applicant_id = a.applicant_id 
        LEFT JOIN applicant_education e ON p.applicant_id = e.applicant_id
        WHERE p.applicant_id = '$id'";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("❌ Applicant not found.");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Application Success - GAF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #e9ecef; padding-top: 50px; }
        .receipt-card { background: white; max-width: 600px; margin: auto; padding: 40px; border-radius: 5px; border-top: 5px solid #28a745; box-shadow: 0px 4px 15px rgba(0,0,0,0.1); }
        .logo { width: 80px; display: block; margin: 0 auto 20px auto; }
        .print-btn { margin-top: 30px; }
    </style>
</head>
<body>

<div class="container">
    <div class="receipt-card text-center">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Coat_of_arms_of_Ghana.svg/200px-Coat_of_arms_of_Ghana.svg.png" class="logo">
        
        <h2 class="text-success fw-bold">APPLICATION SUBMITTED</h2>
        <p>Ghana Armed Forces Recruitment 2025</p>
        <hr>

        <div class="text-start mt-4">
            <div class="row mb-2">
                <div class="col-5 fw-bold text-muted">Applicant ID:</div>
                <div class="col-7 fw-bold fs-5"><?php echo $row['applicant_id']; ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-muted">Full Name:</div>
                <div class="col-7"><?php echo $row['first_name'] . " " . $row['last_name']; ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-muted">Region:</div>
                <div class="col-7"><?php echo $row['region_of_residence']; ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-muted">Education:</div>
                <div class="col-7"><?php echo $row['institution_name'] . " (" . $row['programme_of_study'] . ")"; ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-muted">Status:</div>
                <div class="col-7"><span class="badge bg-primary"><?php echo $row['application_status']; ?></span></div>
            </div>
        </div>

        <div class="alert alert-warning mt-4 text-start small">
            <strong>Next Steps:</strong><br>
            1. Print this summary slip.<br>
            2. Keep your Applicant ID safe.<br>
            3. Wait for the Screening Venue announcement via SMS.
        </div>

        <button onclick="window.print()" class="btn btn-outline-dark print-btn">🖨️ Print Summary</button>
        <a href="index.php" class="btn btn-success print-btn ms-2">Finish</a>
    </div>
</div>

</body>
</html>