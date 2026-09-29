<?php
session_start();
include 'db_connect.php'; 

// 1. Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// 2. Capture Filter Parameters
$search_nia = $_GET['search_nia'] ?? '';

// 3. Query for DISQUALIFIED applicants from either application_status or medical_status
$query = "SELECT applicant_id, first_name, last_name, ghana_card, application_status, medical_status,
          vetting_remarks, vetted_by, branch_choice 
          FROM applicants 
          WHERE application_status IN ('Rejected','Disqualified') 
             OR medical_status = 'Unfit'";

if ($search_nia != '') {
    $query .= " AND ghana_card LIKE '%" . $conn->real_escape_string($search_nia) . "%'";
}

$query .= " ORDER BY applicant_id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GAF Archive | Ineligible Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-crimson: #8b0000; --gaf-gold: #d4af37; }
        body { background-color: #f4f4f4; font-family: 'Segoe UI', sans-serif; }
        .gaf-nav { background: var(--gaf-crimson); border-bottom: 5px solid var(--gaf-gold); color: white; padding: 15px; }
        .table-header { background-color: #000000; color: #fff; }
        .remark-box { font-size: 0.85rem; color: #dc3545; font-style: italic; max-width: 250px; }
        .medical-disqualified { background-color: #f8d7da; } /* optional highlight for medical disqualified */
    </style>
</head>
<body>

<div class="gaf-nav text-center fw-bold text-uppercase">
    <img src="assets/img/GAFT.png" style="width: 35px; margin-right: 10px;"><i class="fas fa-database me-2"></i> Ineligible & Disqualified Applicant Database
</div>

<div class="container-fluid mt-4 px-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="dashboard.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Qualified Dashboard
        </a>
        
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search_nia" value="<?= htmlspecialchars($search_nia) ?>" 
                   class="form-control form-control-sm" placeholder="Search Ghana Card...">
            <button type="submit" class="btn btn-dark btn-sm">Search</button>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-header">
                    <tr>
                        <th class="ps-3">Serial No</th>
                        <th>Full Name</th>
                        <th>Ghana Card</th>
                        <th>Branch</th>
                        <th>Rejection Reason</th>
                        <th>Authorized By</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="<?= ($row['medical_status'] == 'Unfit') ? 'medical-disqualified' : '' ?>">
                            <td class="ps-3 small fw-bold text-muted">GAF-<?= str_pad($row['applicant_id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="fw-bold"><?= strtoupper($row['last_name'] . " " . $row['first_name']) ?></td>
                            <td><small><?= htmlspecialchars($row['ghana_card']) ?></small></td>
                            <td><small><?= isset($row['branch_choice']) ? strtoupper($row['branch_choice']) : 'N/A' ?></small></td>
                            <td class="remark-box">
                                <?php 
                                    if (!empty($row['vetting_remarks'])) {
                                        echo htmlspecialchars($row['vetting_remarks']);
                                    } elseif ($row['application_status'] == 'Disqualified' || $row['application_status'] == 'Rejected') {
                                        echo 'Failed Eligibility Check (Application)';
                                    } elseif ($row['medical_status'] == 'Unfit') {
                                        echo 'Failed Medical Screening';
                                    } else {
                                        echo 'N/A';
                                    }
                                ?>
                            </td>
                            <td><small class="text-muted fw-bold"><?= $row['vetted_by'] ?? 'System Eligibility' ?></small></td>
                            <td class="text-center">
                                <!-- Re-Audit now redirects back to dashboard -->
                                <a href="dashboard.php" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-folder-open"></i> Re-Audit
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">No records found in the disqualified database.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
