<?php
// Admin Dashboard - Premium Version
require_once __DIR__ . '/../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Models\Course;
use SkillMaster\Models\Enrollment;
use SkillMaster\Models\MockPayment;
use SkillMaster\Models\InstructorApplication;

// Only admin can access
RoleMiddleware::check('admin');

// Initialize models
$userModel = new User();
$courseModel = new Course();
$enrollmentModel = new Enrollment();
$paymentModel = new MockPayment();
$applicationModel = new InstructorApplication();

// Get statistics
$stats = $userModel->getStats();
$courseStats = $courseModel->getStats();
$enrollmentStats = $enrollmentModel->getStats();
$paymentStats = $paymentModel->getStats();

// Get pending counts
$pendingApplications = $applicationModel->getPendingCount();
$pendingCourses = $courseModel->getPendingCount();
$pendingEnrollments = $enrollmentModel->getPendingCount();
$pendingPayments = $paymentModel->getPendingCount();

// Get recent activities
$recentUsers = $userModel->getRecentUsers(5);
$recentCourses = $courseModel->getRecentCourses(5);
$recentEnrollments = $enrollmentModel->getRecentEnrollments(5);

$page_title = 'Admin Dashboard - ' . APP_NAME;
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
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Template Stylesheet -->
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        /* Premium Admin Styles */
        :root {
            --teal-primary: #06BBCC;
            --teal-dark: #0598A6;
            --teal-light: #E6F8FA;
            --navy-dark: #181d38;
            --gray-text: #52565b;
        }
        
        body {
            background-color: #F0FBFC;
        }
        
        /* Premium Navbar - No Teal Background on Brand */
        .navbar {
            padding: 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            background-color: #ffffff !important;
        }
        
        .navbar-brand {
            padding: 1.2rem 2rem !important;
            margin-right: 0;
            background: transparent !important;
        }
        
        .navbar-brand h2 {
            color: var(--teal-primary) !important;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        
        .navbar-brand h2 i {
            color: var(--teal-primary);
        }
        
        /* Dropdown Menu Styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 12px;
            margin-top: 10px;
            padding: 10px 0;
        }
        
        .dropdown-item {
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background-color: var(--teal-light);
            color: var(--teal-primary);
            padding-left: 28px;
        }
        
        .dropdown-item i {
            width: 24px;
            margin-right: 8px;
            color: var(--teal-primary);
        }
        
        /* Navbar Links */
        .navbar-nav .nav-link {
            font-weight: 600;
            padding: 1.5rem 1rem !important;
            transition: all 0.3s ease;
            position: relative;
            color: var(--navy-dark) !important;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--teal-primary) !important;
        }
        
        .navbar-nav .nav-link.active {
            color: var(--teal-primary) !important;
        }
        
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 15%;
            width: 70%;
            height: 3px;
            background-color: var(--teal-primary);
            border-radius: 3px;
        }
        
        /* Dropdown Toggle Arrow */
        .dropdown-toggle::after {
            transition: transform 0.3s ease;
        }
        
        .dropdown:hover .dropdown-toggle::after {
            transform: rotate(180deg);
        }
        
        /* Premium Cards */
        .stat-card {
            border: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(6, 187, 204, 0.2);
        }
        
        .stat-card .icon-bg {
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 80px;
            opacity: 0.15;
        }
        
        .pending-card {
            border-radius: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
            background-color: #ffffff;
            border: 1px solid #e9ecef;
        }
        
        .pending-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        /* Dashboard Sections */
        .dashboard-section {
            background: white;
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .dashboard-section:hover {
            box-shadow: 0 8px 25px rgba(6, 187, 204, 0.1);
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--teal-light);
            position: relative;
        }
        
        .section-title::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background-color: var(--teal-primary);
        }
        
        /* Table Styling */
        .table-custom {
            border-radius: 16px;
            overflow: hidden;
        }
        
        .table-custom thead th {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-dark));
            color: white;
            font-weight: 600;
            padding: 12px 16px;
            border: none;
        }
        
        .table-custom tbody tr {
            transition: all 0.2s ease;
        }
        
        .table-custom tbody tr:hover {
            background-color: var(--teal-light);
        }
        
        .table-custom td {
            padding: 12px 16px;
            vertical-align: middle;
        }
        
        /* Badge Styling */
        .badge-premium {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.75rem;
        }
        
        /* Button Styling */
        .btn-premium {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-dark));
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(6, 187, 204, 0.4);
            color: white;
        }
        
        .btn-outline-premium {
            border: 2px solid var(--teal-primary);
            color: var(--teal-primary);
            border-radius: 30px;
            padding: 6px 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: transparent;
        }
        
        .btn-outline-premium:hover {
            background-color: var(--teal-primary);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Avatar */
        .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--teal-primary);
        }
        
        /* Footer */
        .footer {
            margin-top: 3rem;
        }
        
        /* Responsive Navbar */
        @media (max-width: 1200px) {
            .navbar-nav .nav-link {
                padding: 1rem 0.75rem !important;
                font-size: 0.85rem;
            }
            .dropdown-item {
                padding: 8px 16px;
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 992px) {
            .navbar-nav {
                padding: 1rem 0;
            }
            .navbar-nav .nav-link {
                padding: 0.5rem 1rem !important;
            }
            .navbar-nav .nav-link.active::after {
                display: none;
            }
            .dropdown-menu {
                border: none;
                box-shadow: none;
                padding-left: 1rem;
            }
        }
        
        /* Full Width Content */
        .container-xxl {
            width: 100%;
            padding-right: 1.5rem;
            padding-left: 1.5rem;
            margin-right: auto;
            margin-left: auto;
        }
    </style>
</head>
<body>

    <!-- Premium Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow-sm sticky-top p-0">
        <a href="index.php" class="navbar-brand d-flex align-items-center">
            <h2 class="m-0"><i class="fa fa-crown me-2"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav ms-auto p-4 p-lg-0">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link active"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                </li>
                
                <!-- Users Dropdown -->
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa fa-users me-2"></i>Users
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="instructors/applications.php" class="dropdown-item">
                            <i class="fa fa-user-plus"></i> Applications
                        </a>
                        <a href="instructors/list.php" class="dropdown-item">
                            <i class="fa fa-chalkboard-user"></i> All Instructors
                        </a>
                        <a href="users/list.php" class="dropdown-item">
                            <i class="fa fa-users"></i> All Users
                        </a>
                    </div>
                </li>
                
                <!-- Courses Dropdown -->
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa fa-book me-2"></i>Courses
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="courses/pending.php" class="dropdown-item">
                            <i class="fa fa-clock"></i> Pending Approval
                        </a>
                        <a href="categories/index.php" class="dropdown-item">
                            <i class="fa fa-tags"></i> Categories
                        </a>
                    </div>
                </li>
                
                <!-- Finance Dropdown -->
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa fa-credit-card me-2"></i>Finance
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="payments/pending.php" class="dropdown-item">
                            <i class="fa fa-hourglass-half"></i> Pending Payments
                        </a>
                        <a href="enrollments/pending.php" class="dropdown-item">
                            <i class="fa fa-check-circle"></i> Pending Enrollments
                        </a>
                    </div>
                </li>
                
                <!-- Account Dropdown -->
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa fa-user-circle me-2"></i>Account
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="profile/index.php" class="dropdown-item">
                            <i class="fa fa-id-card"></i> My Profile
                        </a>
                        <a href="settings/index.php" class="dropdown-item">
                            <i class="fa fa-cog"></i> System Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="../logout.php" class="dropdown-item text-danger">
                            <i class="fa fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Premium Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid py-4 mb-4" style="background: linear-gradient(135deg, #06BBCC, #0598A6);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <h1 class="text-white display-5 fw-bold mb-2">Admin Dashboard</h1>
                    <p class="text-white mb-0 opacity-75">
                        <i class="fa fa-calendar-alt me-2"></i><?php echo date('l, F j, Y'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Dashboard Content Start -->
    <div class="container-xxl py-4">
        <div class="container-fluid">
            
            <!-- Welcome Card -->
            <div class="dashboard-section mb-4 text-center">
                <i class="fa fa-waveform fa-3x text-primary mb-3"></i>
                <h3 class="mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
                <p class="text-muted mb-0">Here's what's happening with your platform today.</p>
            </div>
            
            <!-- Stats Cards Row 1 -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-white p-4" style="background: linear-gradient(135deg, #06BBCC, #0598A6) !important;">
                        <div class="icon-bg"><i class="fa fa-users"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Total Students</h6>
                                <h2 class="text-white mb-0"><?php echo number_format($stats['total_students'] ?? 0); ?></h2>
                            </div>
                            <i class="fa fa-graduation-cap fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3 small text-white-50">
                            <i class="fa fa-arrow-up me-1"></i> Active learners
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-white p-4" style="background: linear-gradient(135deg, #198754, #157347);">
                        <div class="icon-bg"><i class="fa fa-chalkboard-user"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Total Instructors</h6>
                                <h2 class="text-white mb-0"><?php echo number_format($stats['total_instructors'] ?? 0); ?></h2>
                            </div>
                            <i class="fa fa-chalkboard fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3 small text-white-50">
                            <i class="fa fa-arrow-up me-1"></i> Expert educators
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-white p-4" style="background: linear-gradient(135deg, #0dcaf0, #0aa5c6);">
                        <div class="icon-bg"><i class="fa fa-book"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Total Courses</h6>
                                <h2 class="text-white mb-0"><?php echo number_format($stats['total_courses'] ?? 0); ?></h2>
                            </div>
                            <i class="fa fa-book-open fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3 small text-white-50">
                            <i class="fa fa-plus-circle me-1"></i> <?php echo $pendingCourses; ?> pending approval
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-dark p-4" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
                        <div class="icon-bg"><i class="fa fa-graduation-cap"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-dark-50 mb-2">Total Enrollments</h6>
                                <h2 class="text-dark mb-0"><?php echo number_format($stats['total_enrollments'] ?? 0); ?></h2>
                            </div>
                            <i class="fa fa-users fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3 small text-dark-50">
                            <i class="fa fa-clock me-1"></i> <?php echo $pendingEnrollments; ?> pending verification
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pending Actions Row -->
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <div class="dashboard-section">
                        <h5 class="section-title"><i class="fa fa-bell me-2 text-primary"></i>Pending Actions</h5>
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <div class="pending-card p-3 rounded text-center">
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px; background: linear-gradient(135deg, #06BBCC, #0598A6) !important;">
                                        <i class="fa fa-user-plus text-white fa-xl"></i>
                                    </div>
                                    <h4 class="mb-1"><?php echo $pendingApplications; ?></h4>
                                    <p class="text-muted small mb-2">Instructor Applications</p>
                                    <a href="instructors/applications.php" class="btn btn-sm btn-outline-premium">Review</a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="pending-card p-3 rounded text-center">
                                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <i class="fa fa-book-open text-white fa-xl"></i>
                                    </div>
                                    <h4 class="mb-1"><?php echo $pendingCourses; ?></h4>
                                    <p class="text-muted small mb-2">Courses Awaiting Approval</p>
                                    <a href="courses/pending.php" class="btn btn-sm btn-outline-premium">Review</a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="pending-card p-3 rounded text-center">
                                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <i class="fa fa-credit-card text-dark fa-xl"></i>
                                    </div>
                                    <h4 class="mb-1"><?php echo $pendingPayments; ?></h4>
                                    <p class="text-muted small mb-2">Pending Payments</p>
                                    <a href="payments/pending.php" class="btn btn-sm btn-outline-premium">Verify</a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="pending-card p-3 rounded text-center">
                                    <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <i class="fa fa-check-circle text-white fa-xl"></i>
                                    </div>
                                    <h4 class="mb-1"><?php echo $pendingEnrollments; ?></h4>
                                    <p class="text-muted small mb-2">Pending Enrollments</p>
                                    <a href="enrollments/pending.php" class="btn btn-sm btn-outline-premium">Verify</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Tables -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="dashboard-section h-100">
                        <h5 class="section-title"><i class="fa fa-user-clock me-2 text-primary"></i>Recent Users</h5>
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentUsers as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo !empty($user['profile_pic']) ? '../../uploads/profiles/' . $user['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" class="avatar-sm me-2">
                                                <div>
                                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge-premium bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'instructor' ? 'info' : 'success'); ?> text-white"><?php echo ucfirst($user['role']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="dashboard-section h-100">
                        <h5 class="section-title"><i class="fa fa-clock me-2 text-primary"></i>Recent Courses</h5>
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Instructor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentCourses as $course): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($course['title']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($course['code']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($course['instructor_name'] ?? 'Unknown'); ?></td>
                                        <td>
                                            <span class="badge-premium bg-<?php echo $course['status'] === 'published' ? 'success' : ($course['status'] === 'pending_approval' ? 'warning' : 'secondary'); ?> text-white">
                                                <?php echo ucfirst(str_replace('_', ' ', $course['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Enrollments - Full Width -->
            <div class="row g-4 mt-3">
                <div class="col-12">
                    <div class="dashboard-section">
                        <h5 class="section-title"><i class="fa fa-list-check me-2 text-primary"></i>Recent Enrollments</h5>
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentEnrollments as $enrollment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['course_title']); ?></td>
                                        <td>
                                            <span class="badge-premium bg-<?php echo $enrollment['status'] === 'active' ? 'success' : ($enrollment['status'] === 'pending_verification' ? 'warning' : 'secondary'); ?> text-white">
                                                <?php echo ucfirst(str_replace('_', ' ', $enrollment['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($enrollment['enrolled_at'])); ?></td>
                                        <td>
                                            <?php if ($enrollment['status'] === 'pending_verification'): ?>
                                                <a href="enrollments/verify.php?id=<?php echo $enrollment['id']; ?>" class="btn btn-sm btn-premium">Verify</a>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <!-- Dashboard Content End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="../index.php">Home</a>
                        <a href="../about.php">About</a>
                        <a href="../contact.php">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>