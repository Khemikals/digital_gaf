<?php
session_start();
include 'config/db_connect.php'; 

/**
 * SEQUENTIAL FLOW STEP 5: process_eligibility.php
 * Logic Gate: Calculates eligibility and branches to the correct terminal page.
 */

// SECURITY GATE: Ensure applicant is logged in and has reviewed data
if (!isset($_SESSION['applicant_authenticated']) || !isset($_SESSION['shs_index'])) {
    header("Location: apply_step4.php");
    exit();
}

/**
 * 1. GAF ELIGIBILITY LOGIC (FR7)
 * Automated vetting based on military recruitment standards.
 */
// Define standards (Male: 1.65m, Female: 1.60m)
$min_h = ($_SESSION['gender'] == 'Male') ? 1.65 : 1.60; 

// Passing grades array (A1 to C6)
$passing_grades = ['A1','B2','B3','C4','C5','C6'];

// Logic Check: Height and Core Academics
$is_eligible = (
    $_SESSION['height'] >= $min_h && 
    in_array($_SESSION['math_grade'], $passing_grades) &&
    in_array($_SESSION['eng_grade'], $passing_grades)
);

// Assign status for session and database
$status = $is_eligible ? 'Qualified' : 'Disqualified';
$_SESSION['application_status'] = $status;

/**
 * 2. DATABASE UPDATE
 * Finalize the vetting result in the applicants table.
 */
$app_id = $_SESSION['applicant_id'];
$stmt = $conn->prepare("UPDATE applicants SET application_status = ? WHERE applicant_id = ?");
$stmt->bind_param("si", $status, $app_id);

if ($stmt->execute()) {
    /**
     * 3. CORRECTED SEQUENTIAL REDIRECTION
     */
    if ($is_eligible) {
        // Qualified applicants proceed to the outcome summary page
        header("Location: submit_final.php");
    } else {
        // Disqualified applicants are sent to the specific terminal slip
        $_SESSION['disqualified_reason'] = "You did not meet the mandatory height or academic requirements (FR7).";
        header("Location: disqualified.php"); 
    }
    exit();
} else {
    die("Vetting System Error: " . $stmt->error);
}
?>