<?php
session_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

include 'config/db_connect.php';

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['table' => '', 'count' => 0]);
    exit();
}

// Filters
$center    = $_GET['center'] ?? '';
$status    = $_GET['status'] ?? '';
$search_id = $_GET['search_id'] ?? '';

// Base query
$sql = "SELECT * FROM applicants WHERE 1=1";

// Filter: Screening Center
if (!empty($center)) {
    $sql .= " AND screening_center = '" . $conn->real_escape_string($center) . "'";
}

// Filter: Application Status
if (!empty($status)) {
    $sql .= " AND application_status = '" . $conn->real_escape_string($status) . "'";
}

// Filter: Applicant ID
if (!empty($search_id)) {
    $clean_id = preg_replace('/[^0-9]/', '', $search_id);
    if (!empty($clean_id)) {
        $sql .= " AND applicant_id = " . (int)$clean_id;
    }
}

$sql .= " ORDER BY applicant_id DESC";

$result = $conn->query($sql);

$rows  = "";
$count = 0;

if ($result) {
    $count = $result->num_rows;

    if ($count > 0) {
        while ($row = $result->fetch_assoc()) {

            // Applicant ID display
            $app_id_display = "#" . str_pad($row['applicant_id'], 4, '0', STR_PAD_LEFT);

            // Name + Ghana Card Number
            $full_name = strtoupper($row['last_name'] . " " . $row['first_name']);
            $ghana_card = !empty($row['ghana_card']) ? htmlspecialchars($row['ghana_card']) : 'N/A';

            $name_display = "
                <div>
                    <div class='fw-bold'>$full_name</div>
                    <div class='text-muted small' style='font-size:0.75rem;'>$ghana_card</div>
                </div>
            ";

            // DOC REVIEW STATUS
            $status_value = trim($row['application_status']);
            if ($status_value === '' || $status_value === null) {
                $status_value = 'Pending';
            }

            $badge_class = 'badge bg-light text-dark border';
            if ($status_value === 'Qualified') {
                $badge_class = 'badge bg-success';
            } elseif ($status_value === 'Disqualified') {
                $badge_class = 'badge bg-danger';
            }

            $doc_review_html = "<span class='$badge_class'>$status_value</span>";

            // Medical status
            $medical_status = $row['medical_status'] ?? 'Pending';
            $med_class = 'badge bg-light text-dark border';
            if ($medical_status === 'Fit') {
                $med_class = 'badge bg-success';
            } elseif ($medical_status === 'Unfit') {
                $med_class = 'badge bg-danger';
            }

            // Date registered from processed_at
            $date_display = !empty($row['processed_at'])
                ? date('M d, Y', strtotime($row['processed_at']))
                : 'N/A';

            // Build table row
            $rows .= "
                <tr>
                    <td class='fw-bold text-secondary align-middle'>$app_id_display</td>
                    <td class='align-middle'>$name_display</td>
                    <td class='align-middle small'>" . ($row['screening_center'] ?? 'Not Assigned') . "</td>
                    <td class='text-center align-middle'>$doc_review_html</td>
                    <td class='text-center align-middle'><span class='$med_class'>$medical_status</span></td>
                    <td class='align-middle small fw-bold text-secondary'>$date_display</td>
                    <td class='text-center align-middle'>
                        <div class='btn-group'>
                            <a href='process_approval.php?id={$row['applicant_id']}' class='btn btn-sm btn-outline-dark'>Review</a>
                            <a href='medical_review.php?id={$row['applicant_id']}' class='btn btn-sm btn-outline-danger'>Medical</a>
                        </div>
                    </td>
                </tr>
            ";
        }
    } else {
        $rows = "<tr><td colspan='7' class='text-center py-5 text-muted fw-bold'>No applicants found</td></tr>";
    }
} else {
    $rows = "<tr><td colspan='7' class='text-center py-5 text-danger'>Database error: " . $conn->error . "</td></tr>";
}

echo json_encode([
    'table' => $rows,
    'count' => $count
]);
?>
