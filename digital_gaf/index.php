<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAF Enlistment Portal | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --gaf-green: #4B5320; --gaf-red: #B22222; --gaf-navy: #0b1a2a; --gaf-gold: #d4af37; }
        body { font-family: 'Montserrat', sans-serif; color: #333; overflow-x: hidden; }
        
        /* Responsive Hero Section */
        .hero { 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
            url('https://gafonline.mil.gh/images/recruitment/1766151831_enlistment_04l1aFIrRf.png'); 
            background-size: cover; background-position: center; 
            min-height: 70vh; display: flex; align-items: center; color: white; 
            padding: 60px 0;
        }
        
        @media (max-width: 768px) {
            .hero { min-height: 50vh; text-align: center; }
            .hero h1 { font-size: 2.5rem !important; }
        }

        .service-card { border: none; transition: 0.3s; padding: 30px; border-radius: 15px; background: #fff; text-align: center; height: 100%; }
        .service-card:hover { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        /* Responsive Guide Step Cards */
        .guide-step-card { 
            background: #fff; border-radius: 10px; padding: 30px; 
            height: 100%; transition: 0.3s; border: 1px solid #eee;
        }
        .guide-step-card:hover { border-color: var(--gaf-gold); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .step-icon-wrapper { margin-bottom: 20px; color: #333; }
        .step-number { font-weight: 800; color: #1a1a1a; margin-bottom: 15px; font-size: 1.2rem; }
        .step-desc { font-size: 0.9rem; color: #666; line-height: 1.6; }

        .footer { background: var(--gaf-navy); color: #adb5bd; padding: 60px 0; }
        .footer h5 { color: #fff; margin-bottom: 25px; }

        /* Fix for mobile navigation alignment */
        @media (max-width: 991px) {
            .navbar-nav { text-align: center; padding: 20px 0; }
            .nav-item { margin-bottom: 10px; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand" href="index.php"> <img src="assets/img/GAFB.jpg" width="80"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="index.php">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3" href="#" data-bs-toggle="dropdown">Read More</a>
                    <ul class="dropdown-menu border-0 shadow"><li><a class="dropdown-item" href="instructions.php">How to Apply</a></li></ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3" href="#" data-bs-toggle="dropdown">Track Progress</a>
                    <ul class="dropdown-menu border-0 shadow">
                        <li><a class="dropdown-item" href="track_progress.php">Recruits</a></li>
                        <li><a class="dropdown-item" href="track_progress.php">Officer Cadets</a></li>
                    </ul>
                </li>
                <li class="nav-item ms-lg-3">
                    <button class="btn btn-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#applyModal">Apply</button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h5 class="text-warning fw-bold">WELCOME TO</h5>
                <h1 class="display-2 fw-bold mb-4">GAF-DRSS</h1>
                <button class="btn btn-danger btn-lg rounded-pill px-5 py-3 shadow fw-bold" data-bs-toggle="modal" data-bs-target="#applyModal">GET STARTED</button>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light" id="guide">
    <div class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8 text-center text-lg-start">
                <h6 class="text-danger fw-bold">GAF-DRSS REQUIREMENT</h6>
                <h2 class="display-5 fw-bold">APPLICATION GUIDE</h2>
                <div class="bg-danger mb-3 mx-auto mx-lg-0" style="height: 3px; width: 60px;"></div>
                <p class="text-muted">Ensure you meet all prerequisites before proceeding with the enlistment process.</p>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <a href="instructions.php" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow-sm">
                    <i class="fas fa-info-circle me-2"></i> Learn More
                </a>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-lg-3 col-md-6">
                <div class="guide-step-card text-center">
                    <div class="step-icon-wrapper">
                        <i class="fas fa-file-invoice fa-3x"></i>
                    </div>
                    <div class="step-number text-uppercase">Step 1</div>
                    <p class="step-desc">All applicants are required to carefully read through the entry requirements for better understanding before applying. Take note of all required data in the subsequent steps.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="guide-step-card text-center">
                    <div class="step-icon-wrapper">
                        <i class="fas fa-credit-card fa-3x"></i>
                    </div>
                    <div class="step-number text-uppercase">Step 2</div>
                    <p class="step-desc">Click on the Apply Now tab and fill in your card serial and pincode details. Select your Arm of Service and other details then click on the start application.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="guide-step-card text-center">
                    <div class="step-icon-wrapper">
                        <i class="fas fa-user-edit fa-3x"></i>
                    </div>
                    <div class="step-number text-uppercase">Step 3</div>
                    <p class="step-desc">You are to select your preferred branch and enter your Bio Data details, Educational details as well as upload a passport sized picture of dimension (200x180) pixels before submitting.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="guide-step-card text-center">
                    <div class="step-icon-wrapper">
                        <i class="fas fa-print fa-3x"></i>
                    </div>
                    <div class="step-number text-uppercase">Step 4</div>
                    <p class="step-desc">Finally Print out a copy of the application summary report, which contains details of your Application Identification Number (GAF ID Number) and other relevant data.</p>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-5 border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex flex-column flex-md-row align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-md-3 mb-3 mb-md-0 text-danger"></i>
                <p class="mb-0 fw-bold text-center text-md-start">The Ghana Armed Forces is an equal opportunity employer. There are no authorized middlemen and advertised vacancies are not for sale.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container py-5 text-center">
        <h2 class="fw-bold mb-5">Service Portals</h2>
        <div class="row g-4">

            <!-- GHQ -->
            <div class="col-6 col-md-3">
                <a href="ghq_portal.php" class="text-decoration-none">
                    <div class="service-card shadow-sm border h-100 p-4 position-relative">
                        <img src="assets/img/GAFB.jpg" width="60" class="mb-3 img-fluid">
                        <h5 class="fw-bold small text-dark">GHQ</h5>
                        <p class="text-muted small mt-2">Central command of the Ghana Armed Forces, coordinating operations across all branches and strategic decisions.</p>
                        <div class="overlay position-absolute top-0 start-0 w-100 h-100 rounded-3"></div>
                    </div>
                </a>
            </div>

            <!-- Army -->
            <div class="col-6 col-md-3">
                <a href="army_portal.php" class="text-decoration-none">
                    <div class="service-card shadow-sm border h-100 p-4 position-relative">
                        <img src="assets/img/GARMY.png" width="60" class="mb-3 img-fluid">
                        <h5 class="fw-bold small text-dark">Army</h5>
                        <p class="text-muted small mt-2">Responsible for land-based military operations, ensuring national security and defense on the ground.</p>
                        <div class="overlay position-absolute top-0 start-0 w-100 h-100 rounded-3"></div>
                    </div>
                </a>
            </div>

            <!-- Navy -->
            <div class="col-6 col-md-3">
                <a href="navy_portal.php" class="text-decoration-none">
                    <div class="service-card shadow-sm border h-100 p-4 position-relative">
                        <img src="assets/img/GAFN.jpg" width="60" class="mb-3 img-fluid">
                        <h5 class="fw-bold small text-dark">Navy</h5>
                        <p class="text-muted small mt-2">Safeguards Ghana’s territorial waters and maritime interests while executing naval operations.</p>
                        <div class="overlay position-absolute top-0 start-0 w-100 h-100 rounded-3"></div>
                    </div>
                </a>
            </div>

            <!-- Airforce -->
            <div class="col-6 col-md-3">
                <a href="airforce_portal.php" class="text-decoration-none">
                    <div class="service-card shadow-sm border h-100 p-4 position-relative">
                        <img src="assets/img/GAFA.png" width="60" class="mb-3 img-fluid">
                        <h5 class="fw-bold small text-dark">Airforce</h5>
                        <p class="text-muted small mt-2">Handles aerial defense, airspace monitoring, and air operations for the Ghana Armed Forces.</p>
                        <div class="overlay position-absolute top-0 start-0 w-100 h-100 rounded-3"></div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

<style>
.service-card {
    background: #fff;
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.service-card .overlay {
    background: rgba(212, 175, 55, 0.05); /* subtle gold highlight on hover */
    opacity: 0;
    transition: opacity 0.3s ease;
}

.service-card:hover .overlay {
    opacity: 1;
}
</style>



<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <img src="assets/img/GAFT.png" width="80">
                <p class="mt-3">Official Ghana Armed Forces Digital Recruitment And Scheduling Portal. Dedicated to serving our nation with integrity.</p>
            </div>
            <div class="col-lg-4 col-md-6">
                <h5>Helpful Links</h5>
                <ul class="list-unstyled">
                    <li><a href="instructions.php" class="text-white text-decoration-none">How to Apply</a></li>
                    <li><a href="track_progress.php" class="text-white text-decoration-none">Check Application Status</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-12">
                <h5>Contact Info</h5>
                <p><i class="fas fa-phone me-2 text-warning"></i> (+233) 509 215 775</p>
                <p><i class="fas fa-envelope me-2 text-warning"></i> recruitment@gaf.mil.gh</p>
            </div>
        </div>
        <hr class="mt-5 border-secondary">
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <div class="small">© <?php echo date('Y'); ?> GAF DIGITAL APPLICATION AND SCHEDULING SYSTEM. Designed by Group 21</div>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="applyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-bold text-uppercase"><i class="fas fa-shield-alt me-2"></i> Voucher Authentication</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <form action="login_process.php" method="POST">
                    <input type="text" name="serial" class="form-control mb-3 p-3" placeholder="Voucher Serial Number" required>
                    <input type="password" name="pin" class="form-control mb-3 p-3" placeholder="Voucher PIN Code" required>
                    <button type="submit" name="login_voucher" class="btn btn-danger w-100 py-3 fw-bold rounded-pill shadow">LOGIN TO APPLY</button>
                </form>
                <p class="small text-muted mt-3 mb-0">Ensure you have a valid voucher before attempting login.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>