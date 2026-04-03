<?php
// Student Change Password Page
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;
use SkillMaster\Auth\Authenticator;

RoleMiddleware::check('student');

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate inputs
        if (empty($oldPassword)) {
            $error = 'Please enter your current password';
        } elseif (empty($newPassword)) {
            $error = 'Please enter a new password';
        } elseif (empty($confirmPassword)) {
            $error = 'Please confirm your new password';
        } elseif (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            $error = 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match';
        } elseif ($oldPassword === $newPassword) {
            $error = 'New password must be different from current password';
        } else {
            // Verify old password
            if (!password_verify($oldPassword, $user['password'])) {
                $error = 'Current password is incorrect';
            } else {
                // Validate new password strength
                $passwordValidation = Validation::password($newPassword);
                if ($passwordValidation !== true) {
                    $error = $passwordValidation;
                } else {
                    // Update password
                    if ($userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                        $success = 'Password changed successfully! You will be logged out and need to login with your new password.';
                        
                        // Log activity
                        $logger = new \SkillMaster\Services\ActivityLogger();
                        $logger->log($_SESSION['user_id'], 'password_changed', 'user', $_SESSION['user_id']);
                        
                        // Redirect to logout after 3 seconds
                        header('Refresh: 3; url=../../logout.php');
                    } else {
                        $error = 'Failed to update password. Please try again.';
                    }
                }
            }
        }
    }
}

$page_title = 'Change Password - ' . APP_NAME;
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
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Template Stylesheet -->
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="../courses/enrolled.php" class="nav-item nav-link">My Courses</a>
                <a href="../assignments/pending.php" class="nav-item nav-link">Assignments</a>
                <a href="../grades/index.php" class="nav-item nav-link">Grades</a>
                <a href="index.php" class="nav-item nav-link active">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="text-white">Change Password</h1>
                    <p class="text-white mb-0">Update your account password</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Change Password Form -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="bg-light rounded p-5">
                        <div class="text-center mb-4">
                            <i class="fa fa-lock fa-3x text-primary mb-3"></i>
                            <h3>Change Your Password</h3>
                            <p class="text-muted">Secure your account with a strong password</p>
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
                                <p class="mb-0 mt-2 small">Redirecting you to login...</p>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <?php echo Security::csrfField(); ?>
                                
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Current Password" required>
                                    <label for="old_password">Current Password</label>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h6 class="text-muted mb-3">New Password</h6>
                                
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required minlength="<?php echo PASSWORD_MIN_LENGTH; ?>">
                                    <label for="new_password">New Password</label>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
                                    <label for="confirm_password">Confirm New Password</label>
                                </div>
                                
                                <div class="alert alert-info mb-4">
                                    <i class="fa fa-info-circle me-2"></i>
                                    <strong>Password Requirements:</strong>
                                    <ul class="mb-0 mt-2 ps-3">
                                        <li>Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters</li>
                                        <li>At least one uppercase letter (A-Z)</li>
                                        <li>At least one lowercase letter (a-z)</li>
                                        <li>At least one number (0-9)</li>
                                    </ul>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                                    <i class="fa fa-save me-2"></i>Change Password
                                </button>
                                
                                <div class="text-center">
                                    <a href="index.php" class="text-primary">Back to Profile</a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Change Password Form End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="../../index.php">Home</a>
                        <a href="../../about.php">About</a>
                        <a href="../../contact.php">Contact</a>
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
    <script src="../../assets/lib/wow/wow.min.js"></script>
    <script src="../../assets/lib/easing/easing.min.js"></script>
    <script src="../../assets/lib/waypoints/waypoints.min.js"></script>
    <script src="../../assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../../assets/js/main.js"></script>
</body>
</html>
