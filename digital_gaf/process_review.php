<?php
session_start();
include 'config/db_connect.php'; // Application Layer connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['applicant_id'])) {
    
    // Sanitize inputs to prevent SQL injection
    $id = $conn->real_escape_string($_POST['applicant_id']);
    $action = $_POST['action'] ?? ''; 
    $officer_note = $conn->real_escape_string($_POST['officer_note'] ?? '');
    
    // Capture the Digital Signature from the modal
    $officer_signature = $conn->real_escape_string($_POST['officer_signature'] ?? 'Unknown Officer');
    
    // Determine the new status based on recruiter input
    if ($action === 'approve') {
        $new_status = 'Approved';
    } elseif ($action === 'reject') {
        $new_status = 'Rejected';
    } else {
        $_SESSION['error'] = "Invalid action performed.";
        header("Location: dashboard.php");
        exit();
    }

    // Update the record with the new status, notes, and digital signature
    $sql = "UPDATE applicants SET 
            application_status = '$new_status',
            vetting_notes = '$officer_note',
            authorized_by = '$officer_signature',
            processed_at = NOW() 
            WHERE applicant_id = '$id'";

    if ($conn->query($sql)) {
        // Set success notification
        $_SESSION['msg'] = "Applicant GAF-" . str_pad($id, 5, '0', STR_PAD_LEFT) . " successfully $new_status by $officer_signature.";
        header("Location: dashboard.php");
    } else {
        die("Database Error: " . $conn->error);
    }
} else {
    header("Location: dashboard.php");
}
exit();
?>