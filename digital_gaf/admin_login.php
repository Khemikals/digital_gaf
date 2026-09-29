<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAF | Command Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --gaf-olive: #3d4621; /* Army Green */
            --gaf-gold: #d4af37;  /* Military Gold */
        }
        body {
            background-color: #1a1a1a; /* Dark background for focus */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 1);
            border-top: 6px solid var(--gaf-gold);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        .btn-command {
            background-color: var(--gaf-olive);
            color: white;
            font-weight: bold;
            letter-spacing: 1px;
            transition: 0.3s;
        }
        .btn-command:hover {
            background-color: #2a3116;
            color: var(--gaf-gold);
        }
        .link-action {
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            color: var(--gaf-olive);
            transition: 0.2s;
        }
        .link-action:hover {
            color: var(--gaf-gold);
            text-decoration: underline;
        }
        /* Modal Header Style */
        .modal-header {
            background-color: var(--gaf-olive);
            color: white;
        }
        .btn-close {
            filter: invert(1);
        }
    </style>
</head>
<body>

<div class="login-card text-center">
    <img src="assets/img/GAFB.jpg" style="width: 35px; margin-right: 10px;">
    
    <h4 class="fw-bold mb-1">COMMAND INTELLIGENCE CENTER</h4>
    <p class="text-muted small mb-4 text-uppercase" style="letter-spacing: 2px;">Authorized Personnel Only</p>

    <form action="admin_login_process.php" method="POST">
        <div class="text-start mb-3">
            <label class="form-label small fw-bold text-muted">OFFICER USERNAME</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-shield"></i></span>
                <input type="text" name="username" class="form-control bg-light border-start-0" placeholder="Username" required>
            </div>
        </div>

        <div class="text-start mb-2">
            <label class="form-label small fw-bold text-muted">COMMAND PASSWORD</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-key"></i></span>
                <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" required>
            </div>
        </div>

        <div class="text-end mb-4">
            <a href="#" class="link-action small" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
        </div>

        <button type="submit" name="admin_login" class="btn btn-command w-100 py-2 mb-3 shadow-sm">
            ACCESS DASHBOARD <i class="fas fa-sign-in-alt ms-2"></i>
        </button>
        
        <div class="mb-3">
            <span class="text-muted small">New Officer?</span> 
            <a href="#" class="link-action fw-bold" data-bs-toggle="modal" data-bs-target="#registerModal">Request Access</a>
        </div>

        <hr class="my-3">

        <a href="index.php" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Back to Applicant Portal
        </a>
    </form>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>New Officer Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="admin_register_process.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name (Rank & Name)</label>
                        <input type="text" name="reg_fullname" class="form-control" placeholder="e.g., Capt. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Official Email</label>
                        <input type="email" name="reg_email" class="form-control" placeholder="officer@gaf.mil.gh" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="reg_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="reg_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Service Number (Verification)</label>
                        <input type="text" name="reg_svc_number" class="form-control" placeholder="Enter SVC Number" required>
                    </div>

                    <div class="mb-3 p-3 bg-light border rounded">
                        <label class="form-label small fw-bold text-danger">Command Authorization Code (Optional)</label>
                        <input type="password" name="reg_secret_code" class="form-control form-control-sm" placeholder="Leave empty for Standard Officer">
                        <div class="form-text x-small">Enter the High Command secret to register as SuperAdmin.</div>
                    </div>

                    <button type="submit" name="register_admin" class="btn btn-command w-100">Submit Registration</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-lock-open me-2"></i>Reset Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">Enter your registered email address. Instructions to reset your command password will be sent to you.</p>
                <form action="admin_reset_password.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Registered Email</label>
                        <input type="email" name="reset_email" class="form-control" placeholder="officer@gaf.mil.gh" required>
                    </div>
                    <button type="submit" name="request_reset" class="btn btn-warning w-100 fw-bold">Send Reset Link</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>