<?php  
session_start();
include 'config/db_connect.php'; 

// SECURITY CHECK
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Officer';

// Region Configuration
$regions = [
    "Greater Accra" => "Burma Camp, Liberation Barracks (Accra)",
    "Ashanti" => "4BN Military Barracks, Uaddara (Kumasi)",
    "Northern" => "Kamina Barracks, 6BN (Tamale)",
    "Western" => "2BN Military Barracks, Myohaung (Takoradi)",
    "Volta" => "66 Artillery Regiment (Ho)",
    "Eastern" => "1st Infantry Battalion (Michel Camp/Tema-Koforidua)",
    "Central" => "603rd Coast Guard Unit (Cape Coast)",
    "Bono" => "3BN Military Barracks (Sunyani)",
    "Upper East" => "11BN Military Barracks (Bolgatanga)",
    "Upper West" => "10BN Military Barracks (Wa)",
    "Bono East" => "Landmark Police Regional HQ (Techiman)",
    "Ahafo" => "Landmark Regional Admin Block (Goaso)",
    "Oti" => "Landmark Regional Police Command (Dambai)",
    "North East" => "Landmark Regional Admin Post (Nalerigu)",
    "Savannah" => "Landmark Regional Command (Damongo)",
    "Western North" => "Landmark Regional Admin Block (Wiawso)"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>GAF Command Intelligence Center | Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<style>
:root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }

/* Navbar */
.navbar-gaf { background: var(--gaf-olive); border-bottom: 4px solid var(--gaf-gold); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.stats-card { border-top: 4px solid var(--gaf-gold); }
.table-dark { background: var(--gaf-olive) !important; }

/* Status Badges */
.badge-qualified { background-color: #198754; }
.badge-disqualified { background-color: #dc3545; }
.badge-pending { background-color: #ffc107; color: #000; }
</style>
</head>

<body>

<nav class="navbar navbar-gaf navbar-dark sticky-top px-3">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <img src="assets/img/GAFB.jpg" width="40" class="me-3 rounded-circle border border-white">
            <div class="d-flex flex-column">
                <span class="fw-bold text-white text-uppercase" style="letter-spacing: 1px;">Command Intelligence Center</span>
                <span class="text-white-50 small">Regional Oversight Dashboard</span>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-md-block text-white">
                <small class="d-block fw-bold"><?php echo htmlspecialchars($admin_username); ?></small>
                <small class="text-white-50" style="font-size: 0.75rem;">Officer Online</small>
            </div>
            <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4 px-3">
    <div class="card shadow-sm stats-card">
        <div class="card-body">
            <form id="filterForm" class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="fw-bold small text-muted">Landmark Center</label>
                    <select name="center" class="form-select form-select-sm border-secondary">
                        <option value="">-- All Regions --</option>
                        <?php foreach($regions as $region => $landmark): ?>
                            <option value="<?= $landmark ?>"><?= $region ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="fw-bold small text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm border-secondary">
                        <option value="">-- All Status --</option>
                        <option value="Qualified">Qualified</option>
                        <option value="Pending">Pending Review</option>
                        <option value="Approved">Approved</option>
                        <option value="Disqualified">Disqualified</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="fw-bold small text-muted">Search Applicant ID</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                        <input type="text" name="search_id" class="form-control" placeholder="e.g. 50">
                    </div>
                </div>
                <div class="col-6 col-md-1 d-grid">
                    <button type="submit" class="btn btn-dark btn-sm"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
                <div class="col-6 col-md-2 d-grid">
                    <a href="disqualified_db.php" class="btn btn-outline-danger btn-sm fw-bold"><i class="fas fa-user-slash me-1"></i> Disqualified</a>
                </div>
                <div class="col-6 col-md-2 d-grid">
                    <a href="export_excel.php" class="btn btn-success btn-sm fw-bold"><i class="fas fa-file-excel me-1"></i> Export Data</a>
                </div>
            </form>
            
            <div class="row mt-3 border-top pt-2 g-2">
                <div class="col-md-2"><a href="civilian_logs.php" class="btn btn-light border btn-sm w-100 text-start"><i class="fas fa-user-shield text-primary me-2"></i> Sys Logs</a></div>
                <div class="col-md-2"><a href="audit_logs.php" class="btn btn-light border btn-sm w-100 text-start"><i class="fas fa-history text-warning me-2"></i> Audit Trail</a></div>
                <div class="col-md-2"><a href="live_monitor.php" class="btn btn-light border btn-sm w-100 text-start text-danger fw-bold"><i class="fas fa-video me-2 blink"></i> Live Feed</a></div>
                <div class="col-md-2"><a href="voucher_manager.php" class="btn btn-primary btn-sm w-100 text-start fw-bold" style="background:#0d6efd;"><i class="fas fa-ticket-alt me-2"></i> Vouchers</a></div>
                <div class="col-md-2"><a href="export_vouchers.php" class="btn btn-secondary btn-sm w-100 text-start"><i class="fas fa-download me-2"></i> Get Codes</a></div>
                <div class="col-md-2 text-end align-self-center"><span class="small text-muted me-1">Total:</span><span id="totalCount" class="badge bg-dark rounded-pill px-3 py-1">0</span></div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-4 px-3 mb-5">
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom fw-bold text-uppercase small text-muted">
            <i class="fas fa-list me-2"></i> Applicant Registry
        </div>
        <div class="table-responsive">
            <table id="applicantTable" class="table table-hover align-middle mb-0 table-striped">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th>Applicant ID</th>
                        <th>Applicant Name</th>
                        <th>Region / Center</th>
                        <th class="text-center">Doc Review</th>
                        <th class="text-center">Medical Input</th>
                        <th>Date Registered</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin me-2"></i> Loading Data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Function to load data table from fetch_applicants.php
    function loadApplicants() {
        $.ajax({
            url: 'fetch_applicants.php',
            type: 'GET',
            data: $('#filterForm').serialize(),
            dataType: 'json',
            success: function(res) {
                $('#applicantTable tbody').html(res.table);
                $('#totalCount').text(res.count);
            },
            error: function(xhr, status, error) {
                console.log("Error fetching data: " + error);
            }
        });
    }

    // Initial Load
    loadApplicants();

    // Auto-refresh every 10 seconds
    setInterval(loadApplicants, 10000);

    // Handle Filters
    $('#filterForm').on('submit', function(e){ e.preventDefault(); loadApplicants(); });
    $('select[name="center"], select[name="status"]').change(function() { loadApplicants(); });
});
</script>

<style>
    /* Blinking animation for Live Monitor Icon */
    .blink { animation: blinker 1.5s linear infinite; }
    @keyframes blinker { 50% { opacity: 0; } }
</style>

</body>
</html>