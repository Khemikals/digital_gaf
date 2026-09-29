<?php
session_start();
// Adjust path if needed. Based on previous steps, it's in the root folder.
include 'db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['table' => '<tr><td colspan="7" class="text-danger text-center">Session Expired. Please Login.</td></tr>', 'count' => 0]);
    exit();
}

// 2. BUILD QUERY WITH FILTERS
$whereClause = "WHERE 1=1";

// Filter by Center / Region
if (isset($_GET['center']) && !empty($_GET['center'])) {
    $center = $conn->real_escape_string($_GET['center']);
    // Assuming your database column for the center is 'region' or 'examination_center'
    $whereClause .= " AND region = '$center'"; 
}

// Filter by Status
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    if ($status == 'Pending') {
         // Show items that are not yet approved or qualified
         $whereClause .= " AND application_status = 'Pending'";
    } else {
         $whereClause .= " AND application_status = '$status'";
    }
}

// Search by NIA or ID
if (isset($_GET['search_nia']) && !empty($_GET['search_nia'])) {
    $search = $conn->real_escape_string($_GET['search_nia']);
    // Checks Ref ID, NIA, or Name
    $whereClause .= " AND (applicant_id LIKE '%$search%' OR nia_number LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%')";
}

// 3. FETCH DATA
$sql = "SELECT * FROM applicants $whereClause ORDER BY applicant_id DESC LIMIT 50";
$result = $conn->query($sql);

$table_rows = "";
$count = 0;

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $count++; // Just counting the loaded rows (or use SQL COUNT for total)

        // Status Badges Logic
        $app_status_badge = '<span class="badge bg-secondary">Pending</span>';
        if ($row['application_status'] == 'Qualified') {
            $app_status_badge = '<span class="badge bg-success">Qualified</span>';
        } elseif ($row['application_status'] == 'Disqualified') {
            $app_status_badge = '<span class="badge bg-danger">Disqualified</span>';
        }

        $med_status_badge = '<span class="badge bg-secondary">Pending</span>';
        if ($row['medical_status'] == 'Fit') {
            $med_status_badge = '<span class="badge bg-success">Fit</span>';
        } elseif ($row['medical_status'] == 'Unfit') {
            $med_status_badge = '<span class="badge bg-danger">Unfit</span>';
        }

        // Date Formatting
        $date = date("M d, Y", strtotime($row['created_at'] ?? 'now')); // Assuming 'created_at' or 'screening_date'

        // Build HTML Row
        $table_rows .= '
        <tr>
            <td class="fw-bold text-primary">#' . $row['applicant_id'] . '</td>
            <td class="fw-bold">' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>
            <td><small>' . htmlspecialchars($row['region'] ?? 'N/A') . '</small></td>
            <td class="text-center">' . $app_status_badge . '</td>
            <td class="text-center">' . $med_status_badge . '</td>
            <td><small>' . $date . '</small></td>
            <td class="text-center">
                <a href="view_applicant.php?id=' . $row['applicant_id'] . '" class="btn btn-sm btn-outline-dark">
                    <i class="fas fa-eye"></i> View
                </a>
            </td>
        </tr>';
    }
} else {
    $table_rows = '<tr><td colspan="7" class="text-center py-4 text-muted">No applicants found matching your criteria.</td></tr>';
}

// 4. RETURN JSON FOR DASHBOARD
header('Content-Type: application/json');
echo json_encode([
    'table' => $table_rows,
    'count' => $result ? $result->num_rows : 0
]);
?>