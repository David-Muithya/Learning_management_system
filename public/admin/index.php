<?php
// Admin Dashboard
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
</head>
<body>

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
                <a href="index.php" class="nav-item nav-link active">Dashboard</a>
                <a href="instructors/applications.php" class="nav-item nav-link">Applications</a>
                <a href="courses/pending.php" class="nav-item nav-link">Pending Courses</a>
                <a href="payments/pending.php" class="nav-item nav-link">Pending Payments</a>
                <a href="enrollments/pending.php" class="nav-item nav-link">Enrollments</a>
                <a href="settings/index.php" class="nav-item nav-link">Settings</a>
                <a href="../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="text-white">Admin Dashboard</h1>
                    <p class="text-white mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Dashboard Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="bg-primary text-white rounded p-4 text-center">
                        <i class="fa fa-users fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo number_format($stats['total_students'] ?? 0); ?></h2>
                        <p class="mb-0">Total Students</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-success text-white rounded p-4 text-center">
                        <i class="fa fa-chalkboard-user fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo number_format($stats['total_instructors'] ?? 0); ?></h2>
                        <p class="mb-0">Total Instructors</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-info text-white rounded p-4 text-center">
                        <i class="fa fa-book fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo number_format($stats['total_courses'] ?? 0); ?></h2>
                        <p class="mb-0">Total Courses</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-warning text-dark rounded p-4 text-center">
                        <i class="fa fa-graduation-cap fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo number_format($stats['total_enrollments'] ?? 0); ?></h2>
                        <p class="mb-0">Total Enrollments</p>
                    </div>
                </div>
            </div>
            
            <!-- Pending Actions Cards -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="bg-light rounded p-4 text-center">
                        <i class="fa fa-user-plus fa-3x text-primary mb-3"></i>
                        <h3 class="mb-0"><?php echo $pendingApplications; ?></h3>
                        <p class="mb-0">Pending Instructor Applications</p>
                        <a href="instructors/applications.php" class="btn btn-sm btn-primary mt-3">Review</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-light rounded p-4 text-center">
                        <i class="fa fa-book-open fa-3x text-primary mb-3"></i>
                        <h3 class="mb-0"><?php echo $pendingCourses; ?></h3>
                        <p class="mb-0">Pending Course Approvals</p>
                        <a href="courses/pending.php" class="btn btn-sm btn-primary mt-3">Review</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-light rounded p-4 text-center">
                        <i class="fa fa-credit-card fa-3x text-primary mb-3"></i>
                        <h3 class="mb-0"><?php echo $pendingPayments; ?></h3>
                        <p class="mb-0">Pending Payments</p>
                        <a href="payments/pending.php" class="btn btn-sm btn-primary mt-3">Verify</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-light rounded p-4 text-center">
                        <i class="fa fa-check-circle fa-3x text-primary mb-3"></i>
                        <h3 class="mb-0"><?php echo $pendingEnrollments; ?></h3>
                        <p class="mb-0">Pending Enrollments</p>
                        <a href="enrollments/pending.php" class="btn btn-sm btn-primary mt-3">Verify</a>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Recent Users -->
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Recent Users</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentUsers as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'instructor' ? 'info' : 'success'); ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Courses -->
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Recent Courses</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Instructor</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentCourses as $course): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['title']); ?></td>
                                        <td><?php echo htmlspecialchars($course['instructor_name'] ?? 'Unknown'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $course['status'] === 'published' ? 'success' : ($course['status'] === 'pending_approval' ? 'warning' : 'secondary'); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $course['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($course['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Enrollments -->
            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Recent Enrollments</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                            <span class="badge bg-<?php echo $enrollment['status'] === 'active' ? 'success' : ($enrollment['status'] === 'pending_verification' ? 'warning' : 'secondary'); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $enrollment['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($enrollment['enrolled_at'])); ?></td>
                                        <td>
                                            <?php if ($enrollment['status'] === 'pending_verification'): ?>
                                                <a href="enrollments/verify.php?id=<?php echo $enrollment['id']; ?>" class="btn btn-sm btn-success">Verify</a>
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