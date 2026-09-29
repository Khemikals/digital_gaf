<?php
session_start();
include 'config/db_connect.php';

// 1. SECURITY: Match the session variable from dashboard.php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login instead of just dying, for better UX
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['med_report'])) {
    
    // 2. Capture and Sanitize Data
    $applicant_id = (int)$_POST['applicant_id'];
    $med_status   = $conn->real_escape_string($_POST['med_status']);
    $remarks      = $conn->real_escape_string($_POST['remarks']);
    $officer      = $conn->real_escape_string($_POST['officer_name']);

    // 3. File Upload Configuration
    $target_dir = "uploads/medical_reports/";
    
    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_info = pathinfo($_FILES["med_report"]["name"]);
    $extension = strtolower($file_info['extension']);
    
    // Rename file for uniqueness: MED_ID_TIMESTAMP.ext
    $new_filename = "MED_" . $applicant_id . "_" . time() . "." . $extension;
    $target_file  = $target_dir . $new_filename;

    // 4. Validate and Move File
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    
    // Check extension
    if (!in_array($extension, $allowed_types)) {
        die("Error: Invalid file type. Only PDF, JPG, and PNG are allowed.");
    }

    // Check size (Max 5MB)
    if ($_FILES["med_report"]["size"] > 5000000) {
        die("Error: File is too large. Maximum limit is 5MB.");
    }

    if (move_uploaded_file($_FILES["med_report"]["tmp_name"], $target_file)) {
        
        // 5. Update the Database
        // Note: Using 'medical_report_file' to match previous SQL instructions
        $sql = "UPDATE applicants SET 
                medical_status = '$med_status', 
                medical_report_file = '$target_file', 
                medical_remarks = '$remarks',
                medical_officer = '$officer' 
                WHERE applicant_id = $applicant_id";

        if ($conn->query($sql)) {
            // Success: Redirect back to dashboard with message
            header("Location: dashboard.php?msg=medical_success");
            exit();
        } else {
            echo "Database Error: " . $conn->error;
        }
    } else {
        echo "Error: Failed to upload file to server. Check folder permissions.";
    }

} else {
    // If accessed directly without POST
    header("Location: dashboard.php");
    exit();
}
?>