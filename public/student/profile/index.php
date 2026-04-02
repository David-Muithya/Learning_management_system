<?php
// Student Profile Page
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;

RoleMiddleware::check('student');

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);

$page_title = 'My Profile - ' . APP_NAME;
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
                    <h1 class="text-white">My Profile</h1>
                    <p class="text-white mb-0">View and manage your account information</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Profile Content -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-light rounded p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                                <i class="fa fa-user fa-3x text-white"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                            <p class="text-muted"><?php echo ucfirst($user['role']); ?> Account</p>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-white rounded p-3">
                                    <small class="text-muted">Full Name</small>
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white rounded p-3">
                                    <small class="text-muted">Username</small>
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($user['username']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white rounded p-3">
                                    <small class="text-muted">Email Address</small>
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white rounded p-3">
                                    <small class="text-muted">Phone Number</small>
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($user['phone_number'] ?? 'Not provided'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white rounded p-3">
                                    <small class="text-muted">Member Since</small>
                                    <p class="mb-0 fw-bold"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white rounded p-3">
                                    <small class="text-muted">Last Login</small>
                                    <p class="mb-0 fw-bold"><?php echo $user['last_login'] ? date('F j, Y', strtotime($user['last_login'])) : 'Never'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="edit.php" class="btn btn-primary">Edit Profile</a>
                            <a href="change-password.php" class="btn btn-outline-primary">Change Password</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>