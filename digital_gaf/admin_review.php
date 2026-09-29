<?php
session_start();
include 'config/db_connect.php'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM applicants WHERE applicant_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

if (!$app) {
    die("Record not found.");
}

$ref_display = "GAF-" . str_pad($app['applicant_id'], 5, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Full Document Audit | <?= $ref_display ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
:root { --gaf-gold:#d4af37; }
body { background:#1a1a1a; color:#fff; }
.doc-viewer{background:#fff;height:380px;display:flex;align-items:center;justify-content:center}
.doc-img{max-width:100%;max-height:100%;cursor:zoom-in}
.sidebar-review{background:#262626;min-height:100vh;padding:25px;border-left:5px solid var(--gaf-gold)}
.doc-label{font-size:.75rem;color:var(--gaf-gold);font-weight:bold}
.signature-input{background:#1a1a1a;color:var(--gaf-gold);border:1px solid #444}
</style>
</head>

<body>
<div class="container-fluid">
<div class="row">

<!-- DOCUMENT VIEW -->
<div class="col-md-9 p-4">
<h5 class="text-warning fw-bold mb-4">
    <img src="assets/img/GAFT.png" style="width: 35px; margin-right: 10px; vertical-align: middle;">
<i class="fas fa-file-shield"></i> Document Verification
</h5>

<div class="row g-4">
<?php
$docs = [
    'Ghana Card (Front)' => 'ghana_card_front_path',
    'Birth Certificate' => 'birth_cert_path',
    'BECE Certificate' => 'bece_cert_path',
    'WASSCE Certificate' => 'wassce_cert_path'
];
foreach ($docs as $label => $path): ?>
<div class="col-md-6">
<span class="doc-label"><?= $label ?></span>
<div class="doc-viewer">
<img src="<?= htmlspecialchars($app[$path] ?? '') ?>" class="doc-img"
     onclick="window.open(this.src)">
</div>
</div>
<?php endforeach; ?>
</div>
</div>

<!-- SIDEBAR -->
<div class="col-md-3 sidebar-review">

<div class="text-center mb-4">
<img src="<?= htmlspecialchars($app['passport_path'] ?? '') ?>"
     class="rounded border border-light mb-2"
     style="width:130px;height:130px;object-fit:cover">

<h5><?= strtoupper($app['first_name'].' '.$app['last_name']) ?></h5>
<p class="small">Reference: <?= $ref_display ?></p>

<div class="badge bg-<?= $app['application_status']==='Qualified'?'success':'warning' ?> w-100">
STATUS: <?= strtoupper($app['application_status']) ?>
</div>
</div>

<form id="reviewForm" action="process_approval.php" method="POST">

<input type="hidden" name="applicant_id" value="<?= $app['applicant_id'] ?>">
<input type="hidden" name="action" id="formAction">
<input type="hidden" name="officer_name" id="officer_name">
<input type="hidden" name="officer_staff_id" id="officer_staff_id">

<label class="doc-label">Officer Vetting Remarks <span class="text-danger">*</span></label>
<textarea id="officer_note" name="officer_note"
class="form-control bg-dark text-white mb-3"
placeholder="Enter clear vetting remarks..."
required></textarea>

<div class="d-grid gap-2">
<button type="button" class="btn btn-success"
onclick="confirmDecision('approve')">
<i class="fas fa-check"></i> APPROVE
</button>

<button type="button" class="btn btn-danger"
onclick="confirmDecision('reject')">
<i class="fas fa-ban"></i> REJECT
</button>
</div>

</form>
</div>
</div>
</div>

<!-- CONFIRM MODAL -->
<div class="modal fade" id="confirmationModal">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content bg-dark border-warning">

<div class="modal-header">
<h5 id="modalTitle" class="text-warning"></h5>
<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input id="sigName" class="form-control signature-input mb-2"
placeholder="Officer Full Name" onkeyup="validateSig()">

<input id="sigID" class="form-control signature-input"
placeholder="Staff ID" onkeyup="validateSig()">
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button id="confirmBtn" class="btn btn-success"
onclick="submitFinalForm()" disabled>
Confirm Decision
</button>
</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
const confirmBtn = document.getElementById('confirmBtn');

function confirmDecision(action){
    const note = document.getElementById('officer_note').value.trim();

    if(note.length < 5){
        alert('Officer remarks are required.');
        return;
    }

    document.getElementById('formAction').value = action;
    document.getElementById('modalTitle').innerText =
        action === 'approve' ? 'Confirm Approval' : 'Confirm Rejection';

    confirmBtn.className =
        action === 'approve' ? 'btn btn-success' : 'btn btn-danger';

    modal.show();
}

function validateSig(){
    const name = document.getElementById('sigName').value.trim();
    const id = document.getElementById('sigID').value.trim();
    confirmBtn.disabled = !(name.length >= 3 && id.length >= 3);
}

function submitFinalForm(){
    document.getElementById('officer_name').value =
        document.getElementById('sigName').value.trim();

    document.getElementById('officer_staff_id').value =
        document.getElementById('sigID').value.trim();

    document.getElementById('reviewForm').submit();
}
</script>

</body>
</html>

