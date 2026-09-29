<?php
session_start();
include 'db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$msg = "";
$admin_user = $_SESSION['admin_username'];

// 2. GENERATE VOUCHERS LOGIC
if (isset($_POST['generate_batch'])) {
    // Capture the custom number entered by the admin
    $quantity = (int)$_POST['quantity'];
    
    // Safety check: Prevent generating 0 or negative numbers
    if ($quantity < 1) {
        $msg = "<div class='alert alert-danger'>Error: Please enter a valid quantity greater than 0.</div>";
    } else {
        $batch_year = date('y'); // e.g., 25 for 2025
        $count = 0;

        for ($i = 0; $i < $quantity; $i++) {
            // Format: GAF-25-XXXXXX (Random 6 digits)
            $random_serial = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $serial = "GAF-{$batch_year}-{$random_serial}";
            
            // Format: 10 digit PIN
            $pin = "";
            for ($j = 0; $j < 10; $j++) { $pin .= mt_rand(0, 9); }

            // Insert into DB (Ignore duplicates if they happen rarely)
            $sql = "INSERT INTO vouchers (serial_number, pin_code, generated_by) VALUES ('$serial', '$pin', '$admin_user')";
            if ($conn->query($sql)) {
                $count++;
            }
        }
        $msg = "<div class='alert alert-success'>Successfully generated $count new vouchers.</div>";
    }
}

// 3. FETCH VOUCHERS
$limit = 50;
$result = $conn->query("SELECT * FROM vouchers ORDER BY voucher_id DESC LIMIT $limit");

// 4. STATS
$total_active = $conn->query("SELECT COUNT(*) as c FROM vouchers WHERE status='Active'")->fetch_assoc()['c'];
$total_used   = $conn->query("SELECT COUNT(*) as c FROM vouchers WHERE status='Used'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voucher Command | GAF-DRSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .nav-header { background: var(--gaf-olive); color: white; border-bottom: 4px solid var(--gaf-gold); padding: 15px; }
        .card-stats { border-left: 5px solid var(--gaf-gold); }
        .serial-font { font-family: 'Courier New', monospace; font-weight: bold; letter-spacing: 1px; }
        .pin-blur { filter: blur(3px); transition: 0.3s; cursor: pointer; }
        .pin-blur:hover { filter: none; }
    </style>
</head>
<body>

<div class="nav-header d-flex justify-content-between align-items-center">
    <h4 class="m-0 fw-bold"><i class="fas fa-ticket-alt me-2"></i>E-VOUCHER MANAGEMENT CENTER</h4>
    <a href="dashboard.php" class="btn btn-outline-light btn-sm">Back to Dashboard</a>
</div>

<div class="container mt-4">
    <?php echo $msg; ?>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white fw-bold"><i class="fas fa-cogs me-2"></i>Generate Batch</div>
                <div class="card-body">
                    <form method="POST">
                        <label class="form-label small fw-bold">Quantity to Generate</label>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-calculator"></i></span>
                            <input type="number" name="quantity" class="form-control" placeholder="Enter amount (e.g. 200)" min="1" required>
                        </div>

                        <button type="submit" name="generate_batch" class="btn btn-success w-100 fw-bold">
                            <i class="fas fa-plus-circle me-2"></i> GENERATE CODES
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm card-stats h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small fw-bold">Available (Unsold)</h6>
                    <h2 class="display-4 fw-bold text-success"><?php echo $total_active; ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm card-stats h-100" style="border-left-color: #dc3545;">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small fw-bold">Redeemed (Used)</h6>
                    <h2 class="display-4 fw-bold text-danger"><?php echo $total_used; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold text-muted"><i class="fas fa-list me-2"></i>Recent Vouchers (Last 50)</h5>
            <a href="export_vouchers.php" class="btn btn-sm btn-outline-dark"><i class="fas fa-file-excel me-1"></i> Export Active List</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Serial Number</th>
                        <th>PIN Code (Hover to Reveal)</th>
                        <th>Status</th>
                        <th>Generated Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-muted small">#<?php echo $row['voucher_id']; ?></td>
                                <td class="serial-font text-primary"><?php echo $row['serial_number']; ?></td>
                                <td><span class="badge bg-secondary serial-font pin-blur"><?php echo $row['pin_code']; ?></span></td>
                                <td>
                                    <?php if($row['status'] == 'Active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Used</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?php echo $row['created_at']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="py-4 text-muted">No vouchers generated yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>