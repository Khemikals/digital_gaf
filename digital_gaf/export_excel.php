<?php
session_start();
include 'db_connect.php'; 

/**
 * COMMAND PORTAL: export_excel.php
 * SECURITY GATE: Only logged-in officers can export the recruitment database.
 */
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    exit("Unauthorized Access. Please Log In.");
}

// 1. Capture the active filters from the dashboard URL
$filter_center  = $_GET['center'] ?? '';
$search_nia     = $_GET['search_nia'] ?? '';
$filter_status  = $_GET['status'] ?? ''; 
$search_officer = $_GET['search_officer'] ?? ''; 

// 2. Updated Query to match your Database Schema
// FIX: Changed 'region' back to 'screening_center' to fix the error
$query = "SELECT applicant_id, first_name, last_name, branch_choice, application_status, 
                 screening_center, screening_date, vetted_by, ghana_card 
          FROM applicants WHERE 1=1";

if ($filter_center != '') {
    // FIX: Using 'screening_center' for the filter
    $query .= " AND screening_center = '" . $conn->real_escape_string($filter_center) . "'";
}
if ($search_nia != '') {
    $query .= " AND ghana_card LIKE '%" . $conn->real_escape_string($search_nia) . "%'";
}
if ($filter_status != '') {
    $query .= " AND application_status = '" . $conn->real_escape_string($filter_status) . "'";
}
if ($search_officer != '') {
    $query .= " AND vetted_by LIKE '%" . $conn->real_escape_string($search_officer) . "%'";
}

$query .= " ORDER BY applicant_id DESC";
$result = $conn->query($query);

// 3. Set Excel Headers for GAF-DRSS Secure Export
$filename = "GAF_Recruitment_List_" . ($filter_status ?: "All") . "_" . date('Ymd_His') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// 4. Create Table for Excel
echo "<table border='1'>";
echo "<tr>
        <th style='background-color:#3d4621; color:#fff;'>REF ID</th>
        <th style='background-color:#3d4621; color:#fff;'>CANDIDATE NAME</th>
        <th style='background-color:#3d4621; color:#fff;'>GHANA CARD</th>
        <th style='background-color:#3d4621; color:#fff;'>MILITARY BRANCH</th>
        <th style='background-color:#3d4621; color:#fff;'>VETTING STATUS</th>
        <th style='background-color:#3d4621; color:#fff;'>VETTING OFFICER SIGNATURE</th>
        <th style='background-color:#3d4621; color:#fff;'>SCREENING CENTER</th>
        <th style='background-color:#3d4621; color:#fff;'>REPORTING DATE</th>
      </tr>";

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Styling status colors for visual clarity in Excel report
        $status_style = "";
        if ($row['application_status'] == 'Approved' || $row['application_status'] == 'Qualified') { 
            $status_style = "style='color:#198754; font-weight:bold;'"; 
        } elseif ($row['application_status'] == 'Rejected' || $row['application_status'] == 'Disqualified') { 
            $status_style = "style='color:#dc3545;'"; 
        }

        echo "<tr>";
        echo "<td>GAF-" . str_pad($row['applicant_id'], 5, '0', STR_PAD_LEFT) . "</td>";
        echo "<td>" . strtoupper($row['last_name'] . ", " . $row['first_name']) . "</td>";
        echo "<td>" . strtoupper($row['ghana_card']) . "</td>";
        echo "<td>" . strtoupper($row['branch_choice']) . "</td>";
        echo "<td $status_style>" . strtoupper($row['application_status']) . "</td>";
        
        // Displays the digital signature (Name + Staff ID) of the officer
        echo "<td style='font-style:italic; font-family:Courier New; color:#3d4621;'>" . ($row['vetted_by'] ?: 'AWAITING_VETTING') . "</td>";
        
        // FIX: Displaying 'screening_center'
        echo "<td>" . ($row['screening_center'] ?? 'NOT BOOKED') . "</td>";
        echo "<td>" . ($row['screening_date'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' style='text-align:center;'>No matching records found for the active filters.</td></tr>";
}
echo "</table>";
exit();
?>