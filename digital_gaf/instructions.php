<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Entry Requirements | GAF Recruitment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gaf-olive: #3d4621; --gaf-gold: #d4af37; }
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .req-header { background: var(--gaf-olive); color: white; padding: 40px 0; border-bottom: 5px solid var(--gaf-gold); }
        .req-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .icon-box { color: var(--gaf-olive); font-size: 2rem; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="req-header text-center">
    <img src="assets/img/GAFT.png" width="80">
    <div class="container">
        <h2 class="fw-bold">GENERAL ENTRY REQUIREMENTS</h2>
        <p class="mb-0">Please read these carefully before starting your application.</p>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card req-card p-4 text-center">
                <div class="icon-box"><i class="fas fa-flag"></i></div>
                <h5 class="fw-bold">Nationality</h5>
                <p class="small text-muted">Must be a Ghanaian citizen by birth with a valid Ghana Card (NIA).</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card req-card p-4 text-center">
                <div class="icon-box"><i class="fas fa-calendar-alt"></i></div>
                <h5 class="fw-bold">Age Limit</h5>
                <p class="small text-muted">Regular: 18 - 26 years.<br>Professionals: Up to 30 years.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card req-card p-4 text-center">
                <div class="icon-box"><i class="fas fa-ruler-vertical"></i></div>
                <h5 class="fw-bold">Height</h5>
                <p class="small text-muted">Males: 1.68m (5'6") minimum.<br>Females: 1.60m (5'3") minimum.</p>
            </div>
        </div>
    </div>

    <div class="card req-card p-5 mt-4">
        <h4 class="fw-bold mb-4 border-bottom pb-2">Educational Qualifications</h4>
        <ul>
            <li><strong>BECE:</strong> Minimum of six (6) passes including English and Mathematics.</li>
            <li><strong>WASSCE/SSCE:</strong> Six (6) passes (A1-C6) including Core English, Core Mathematics, and Core Science.</li>
            <li>No pending results will be accepted.</li>
        </ul>
        
        <div class="mt-4 text-center">
            <a href="index.php" class="btn btn-dark rounded-pill px-5">I UNDERSTAND, GO BACK</a>
        </div>
    </div>
</div>

</body>
</html>