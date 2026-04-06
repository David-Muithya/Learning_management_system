<?php
// Admin Profile View
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Helpers\Security;

// Only admin can access
RoleMiddleware::check('admin');

$userModel = new User();
$userId = $_SESSION['user_id'];

// Get user details
$user = $userModel->findById($userId);

if (!$user) {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Admin Profile - ' . APP_NAME;
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
    
    <style>
        .profile-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(6, 187, 204, 0.1);
        }
        .info-row {
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-label {
            font-weight: 600;
            color: #181d38;
            width: 140px;
            display: inline-block;
        }
        .info-value {
            color: #52565b;
        }
    </style>
</head>
<body style="background-color: #F0FBFC;">

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
                <a href="index.php" class="nav-item nav-link active">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid py-4 mb-5" style="background-color: #06BBCC;">
        <div class="container text-center">
            <h1 class="text-white">Admin Profile</h1>
            <p class="text-white mb-0">View your account information</p>
        </div>
    </div>
    <!-- Header End -->

    <!-- Profile Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-white rounded p-5 shadow-sm profile-card">
                        <div class="text-center mb-4">
                            <img src="<?php echo !empty($user['profile_pic']) ? '../../uploads/profiles/' . $user['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                 class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #06BBCC;">
                            <h3 class="mb-0" style="color: #181d38;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                            <p class="text-muted">
                                <i class="fa fa-shield-alt me-1" style="color: #06BBCC;"></i> 
                                <?php echo ucfirst($user['role']); ?> Account
                            </p>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fa fa-user me-2" style="color: #06BBCC;"></i>Username:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fa fa-envelope me-2" style="color: #06BBCC;"></i>Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fa fa-user-tag me-2" style="color: #06BBCC;"></i>First Name:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['first_name']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fa fa-user-tag me-2" style="color: #06BBCC;"></i>Last Name:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['last_name']); ?></span>
                        </div>
                        <?php if (!empty($user['phone_number'])): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fa fa-phone me-2" style="color: #06BBCC;"></i>Phone:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['phone_number']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($user['address'])): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fa fa-map-marker-alt me-2" style="color: #06BBCC;"></i>Address:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['address']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($user['bio'])): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fa fa-info-circle me-2" style="color: #06BBCC;"></i>Bio:</span>
                            <span class="info-value"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fa fa-calendar-alt me-2" style="color: #06BBCC;"></i>Member Since:</span>
                            <span class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fa fa-clock me-2" style="color: #06BBCC;"></i>Last Login:</span>
                            <span class="info-value"><?php echo $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></span>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="edit.php" class="btn btn-primary px-4" style="background-color: #06BBCC; border-color: #06BBCC;">
                                <i class="fa fa-edit me-2"></i>Edit Profile
                            </a>
                            <a href="change-password.php" class="btn btn-outline-primary px-4" style="border-color: #06BBCC; color: #06BBCC;">
                                <i class="fa fa-key me-2"></i>Change Password
                            </a>
                            <a href="../index.php" class="btn btn-secondary px-4">
                                <i class="fa fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Profile Content End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" style="background-color: #06BBCC; border-color: #06BBCC;">
        <i class="bi bi-arrow-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>