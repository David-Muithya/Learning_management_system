<?php
// Reset Password Page
require_once __DIR__ . '/../config/config.php';
use SkillMaster\Auth\PasswordReset;
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Helpers\Security;

// Redirect if already logged in
RoleMiddleware::requireGuest();

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$error = '';
$success = '';
$validToken = false;
$userEmail = '';

// Validate token first
if (!empty($token)) {
    $passwordReset = new PasswordReset();
    $validation = $passwordReset->validateToken($token);
    
    if ($validation['valid']) {
        $validToken = true;
        $userEmail = $validation['user']['email'];
    } else {
        $error = $validation['message'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    // Verify CSRF token
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $passwordReset = new PasswordReset();
        $result = $passwordReset->resetPassword($token, $newPassword, $confirmPassword);
        
        if ($result['success']) {
            $success = $result['message'];
            $validToken = false; // Don't show form after success
        } else {
            $error = $result['message'];
        }
    }
}

$page_title = 'Reset Password - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Template Stylesheet -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body style="background-color: #F0FBFC;">

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link">Home</a>
                <a href="about.php" class="nav-item nav-link">About</a>
                <a href="courses.php" class="nav-item nav-link">Courses</a>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
                <a href="login.php" class="nav-item nav-link">Login</a>
                <a href="register.php" class="nav-item nav-link">Register</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Reset Password Form Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-light rounded p-5 shadow">
                        <div class="text-center mb-4">
                            <i class="fa fa-lock fa-3x text-primary mb-3"></i>
                            <h1 class="display-6">Reset Your Password</h1>
                            <p class="text-muted">Create a new secure password for your account.</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="login.php" class="btn btn-primary">Login Now</a>
                            </div>
                        <?php elseif ($validToken): ?>
                            <form method="POST" action="">
                                <?php echo Security::csrfField(); ?>
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                                
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($userEmail); ?>" disabled>
                                    <label>Email Address</label>
                                    <small class="text-muted">Password reset for this account</small>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="New Password" required minlength="<?php echo PASSWORD_MIN_LENGTH; ?>">
                                    <label for="password">New Password (min <?php echo PASSWORD_MIN_LENGTH; ?> characters)</label>
                                </div>
                                
                                <div class="form-floating mb-4">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                                    <label for="confirm_password">Confirm Password</label>
                                </div>
                                
                                <div class="alert alert-info mb-4">
                                    <i class="fa fa-info-circle me-2"></i>
                                    <strong>Password Requirements:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters</li>
                                        <li>At least one uppercase letter</li>
                                        <li>At least one lowercase letter</li>
                                        <li>At least one number</li>
                                    </ul>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                                    <i class="fa fa-save me-2"></i>Reset Password
                                </button>
                                
                                <div class="text-center">
                                    <p class="mb-0"><a href="login.php" class="text-primary">Back to Login</a></p>
                                </div>
                            </form>
                        <?php elseif (empty($token)): ?>
                            <div class="alert alert-warning text-center">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                <strong>No reset token provided.</strong>
                                <p class="mb-0 mt-2">Please use the link from your email to reset your password.</p>
                                <hr>
                                <a href="forgot-password.php" class="btn btn-primary mt-2">Request New Reset Link</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Reset Password Form End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="index.php">Home</a>
                        <a href="about.php">About</a>
                        <a href="contact.php">Help</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Password validation script -->
    <script>
        document.querySelector('form')?.addEventListener('submit', function(e) {
            var password = document.getElementById('password')?.value;
            var confirm = document.getElementById('confirm_password')?.value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
            } else if (password && password.length < <?php echo PASSWORD_MIN_LENGTH; ?>) {
                e.preventDefault();
                alert('Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters!');
            } else if (password && !/[A-Z]/.test(password)) {
                e.preventDefault();
                alert('Password must contain at least one uppercase letter!');
            } else if (password && !/[a-z]/.test(password)) {
                e.preventDefault();
                alert('Password must contain at least one lowercase letter!');
            } else if (password && !/[0-9]/.test(password)) {
                e.preventDefault();
                alert('Password must contain at least one number!');
            }
        });
    </script>
</body>
</html>