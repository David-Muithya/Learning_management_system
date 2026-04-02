
<?php
// Instructor Profile View
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('instructor');

$userModel = new User();
$userId = $_SESSION['user_id'];

// Get user details
$user = $userModel->findById($userId);

if (!$user) {
    header('Location: ../index.php');
    exit;
}

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
                <a href="../courses/my-courses.php" class="nav-item nav-link">My Courses</a>
                <a href="../assignments/list.php" class="nav-item nav-link">Assignments</a>
                <a href="../students/enrolled.php" class="nav-item nav-link">Students</a>
                <a href="../announcements/list.php" class="nav-item nav-link">Announcements</a>
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

    <!-- Profile Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row">
                <!-- Profile Sidebar -->
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 text-center mb-4">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="<?php echo !empty($user['profile_pic']) ? '../../uploads/profiles/' . $user['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                 class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            <a href="edit.php?tab=avatar" class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-2" style="width: 40px; height: 40px;">
                                <i class="fa fa-camera text-white"></i>
                            </a>
                        </div>
                        <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                        <p class="text-muted mb-2">
                            <i class="fa fa-graduation-cap me-1"></i> <?php echo ucfirst($user['role']); ?>
                        </p>
                        <p class="text-muted">
                            <i class="fa fa-calendar-alt me-1"></i> Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                        </p>
                        <hr>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="edit.php" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit me-1"></i>Edit Profile
                            </a>
                            <a href="change-password.php" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-key me-1"></i>Change Password
                            </a>
                        </div>
                    </div>
                    
                    <!-- Social Links -->
                    <?php if ($user['facebook_link'] || $user['twitter_link'] || $user['linkedin_link']): ?>
                        <div class="bg-light rounded p-4">
                            <h5 class="mb-3">Social Profiles</h5>
                            <div class="d-flex justify-content-center gap-3">
                                <?php if ($user['facebook_link']): ?>
                                    <a href="<?php echo htmlspecialchars($user['facebook_link']); ?>" class="btn btn-primary btn-square" target="_blank">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($user['twitter_link']): ?>
                                    <a href="<?php echo htmlspecialchars($user['twitter_link']); ?>" class="btn btn-info btn-square text-white" target="_blank">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($user['linkedin_link']): ?>
                                    <a href="<?php echo htmlspecialchars($user['linkedin_link']); ?>" class="btn btn-primary btn-square" target="_blank">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Profile Details -->
                <div class="col-lg-8">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Personal Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-white rounded p-3">
                                    <small class="text-muted">First Name</small>
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($user['first_name']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white rounded p-3">
                                    <small class="text-muted">Last Name</small>
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($user['last_name']); ?></p>
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
                                    <small class="text-muted">Last Login</small>
                                    <p class="mb-0 fw-bold"><?php echo $user['last_login'] ? date('M d, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></p>
                                </div>
                            </div>
                            <?php if ($user['bio']): ?>
                                <div class="col-12">
                                    <div class="bg-white rounded p-3">
                                        <small class="text-muted">Bio / Expertise</small>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($user['address']): ?>
                                <div class="col-12">
                                    <div class="bg-white rounded p-3">
                                        <small class="text-muted">Address</small>
                                        <p class="mb-0"><?php echo htmlspecialchars($user['address']); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
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

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>