<?php
// Student Dashboard - Premium Version
require_once __DIR__ . '/../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Enrollment;
use SkillMaster\Models\Assignment;
use SkillMaster\Models\Submission;
use SkillMaster\Models\Course;
use SkillMaster\Models\Grade;

// Only students can access
RoleMiddleware::check('student');

$enrollmentModel = new Enrollment();
$assignmentModel = new Assignment();
$submissionModel = new Submission();
$courseModel = new Course();
$gradeModel = new Grade();

$studentId = $_SESSION['user_id'];

// Get enrolled courses
$enrolledCourses = $enrollmentModel->getByStudent($studentId, 'active', 1, 5);
$completedCourses = $enrollmentModel->getByStudent($studentId, 'completed', 1, 5);

// Get pending assignments
$pendingAssignments = $assignmentModel->getForStudent($studentId, 'pending', 1, 5);
$overdueAssignments = $assignmentModel->getForStudent($studentId, 'overdue', 1, 5);

// Get recent grades
$recentGrades = $gradeModel->getByStudent($studentId);
$recentGrades = array_slice($recentGrades, 0, 5);

// Get overall progress
$totalEnrolled = $enrollmentModel->getStats()['active'] ?? 0;
$completedCount = $enrollmentModel->getStats()['completed'] ?? 0;

$page_title = 'Student Dashboard - ' . APP_NAME;
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
        :root {
            --teal-primary: #06BBCC;
            --teal-dark: #0598A6;
            --teal-light: #E6F8FA;
            --navy-dark: #181d38;
        }
        
        body {
            background: linear-gradient(135deg, #F0FBFC 0%, #E6F8FA 100%);
        }
        
        /* Premium Header */
        .premium-header {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            position: relative;
            overflow: hidden;
        }
        
        .premium-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        
        /* Stats Cards */
        .stat-card {
            border: none;
            border-radius: 24px;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(6, 187, 204, 0.2);
        }
        
        .stat-card .stat-icon {
            position: absolute;
            right: -15px;
            bottom: -15px;
            font-size: 80px;
            opacity: 0.15;
        }
        
        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 24px;
            transition: all 0.3s ease;
            overflow: hidden;
            border: none;
        }
        
        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(6, 187, 204, 0.1);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            padding: 1rem 1.5rem;
            border-bottom: 2px solid var(--teal-light);
        }
        
        .card-title {
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 0;
        }
        
        /* Course Items */
        .course-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .course-item:hover {
            background-color: var(--teal-light);
            border-left-color: var(--teal-primary);
            transform: translateX(5px);
        }
        
        /* Assignment Items */
        .assignment-item {
            transition: all 0.2s ease;
        }
        
        .assignment-item:hover {
            background-color: var(--teal-light);
            transform: translateX(5px);
        }
        
        /* Progress Bar */
        .progress-custom {
            height: 8px;
            border-radius: 10px;
            background-color: #e0e0e0;
        }
        
        .progress-bar-custom {
            background: linear-gradient(90deg, #06BBCC, #0598A6);
            border-radius: 10px;
        }
        
        /* Table Styling */
        .table-custom {
            border-radius: 16px;
            overflow: hidden;
        }
        
        .table-custom thead th {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            color: white;
            font-weight: 600;
            border: none;
        }
        
        .table-custom tbody tr:hover {
            background-color: var(--teal-light);
        }
        
        /* Badge Styling */
        .badge-grade-A { background: linear-gradient(135deg, #198754, #157347); color: white; }
        .badge-grade-B { background: linear-gradient(135deg, #0dcaf0, #0aa5c6); color: #000; }
        .badge-grade-C { background: linear-gradient(135deg, #ffc107, #e0a800); color: #000; }
        .badge-grade-D { background: linear-gradient(135deg, #fd7e14, #e06e0c); color: white; }
        .badge-grade-F { background: linear-gradient(135deg, #dc3545, #bb2d3b); color: white; }
        
        /* Button Styling */
        .btn-outline-teal {
            border: 2px solid var(--teal-primary);
            color: var(--teal-primary);
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-teal:hover {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Welcome Avatar */
        .welcome-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .welcome-avatar i {
            font-size: 28px;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Premium Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-crown me-2"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link active">Dashboard</a>
                <a href="courses/enrolled.php" class="nav-item nav-link">My Courses</a>
                <a href="assignments/pending.php" class="nav-item nav-link">Assignments</a>
                <a href="grades/index.php" class="nav-item nav-link">Grades</a>
                <a href="profile/index.php" class="nav-item nav-link">Profile</a>
                <a href="../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Premium Navbar End -->

    <!-- Premium Header Start -->
    <div class="container-fluid premium-header py-5 mb-5">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <div class="welcome-avatar d-inline-flex mb-3">
                        <i class="fa fa-user-graduate"></i>
                    </div>
                    <h1 class="text-white display-4 fw-bold mb-2">Welcome Back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                    <p class="text-white opacity-75 fs-5 mb-0">Continue your learning journey and achieve your goals</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Premium Header End -->

    <!-- Dashboard Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Stats Cards Row -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-white p-4" style="background: linear-gradient(135deg, #06BBCC, #0598A6);">
                        <div class="stat-icon"><i class="fa fa-book"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Active Courses</h6>
                                <h2 class="text-white mb-0"><?php echo $enrolledCourses['total']; ?></h2>
                            </div>
                            <i class="fa fa-book-open fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3 small text-white-50">
                            <i class="fa fa-arrow-right me-1"></i> Currently enrolled
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-white p-4" style="background: linear-gradient(135deg, #198754, #157347);">
                        <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Completed Courses</h6>
                                <h2 class="text-white mb-0"><?php echo $completedCount; ?></h2>
                            </div>
                            <i class="fa fa-graduation-cap fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3 small text-white-50">
                            <i class="fa fa-trophy me-1"></i> Successfully completed
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-dark p-4" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
                        <div class="stat-icon"><i class="fa fa-tasks"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-dark-50 mb-2">Pending Assignments</h6>
                                <h2 class="text-dark mb-0"><?php echo $pendingAssignments['total']; ?></h2>
                            </div>
                            <i class="fa fa-clipboard-list fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3 small text-dark-50">
                            <i class="fa fa-hourglass-half me-1"></i> Awaiting submission
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-white p-4" style="background: linear-gradient(135deg, #0dcaf0, #0aa5c6);">
                        <div class="stat-icon"><i class="fa fa-chart-line"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Overall Progress</h6>
                                <h2 class="text-white mb-0"><?php echo $totalEnrolled > 0 ? round(($completedCount / $totalEnrolled) * 100) : 0; ?>%</h2>
                            </div>
                            <i class="fa fa-chart-simple fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3 small text-white-50">
                            <div class="progress progress-custom mt-2">
                                <div class="progress-bar progress-bar-custom" style="width: <?php echo $totalEnrolled > 0 ? round(($completedCount / $totalEnrolled) * 100) : 0; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- My Courses Section -->
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="card-title"><i class="fa fa-book me-2 text-primary"></i>My Active Courses</h5>
                            <a href="courses/enrolled.php" class="btn btn-sm btn-outline-teal">View All</a>
                        </div>
                        <div class="p-4">
                            <?php if (empty($enrolledCourses['enrollments'])): ?>
                                <div class="text-center py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa fa-book-open fa-3x text-primary"></i>
                                    </div>
                                    <p class="text-muted mb-3">You haven't enrolled in any courses yet.</p>
                                    <a href="courses/browse.php" class="btn btn-primary" style="background: linear-gradient(135deg, #06BBCC, #0598A6); border: none;">
                                        Browse Courses
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($enrolledCourses['enrollments'] as $enrollment): ?>
                                    <a href="courses/details.php?id=<?php echo $enrollment['course_id']; ?>" class="course-item text-decoration-none d-block p-3 rounded mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1" style="color: #181d38;"><?php echo htmlspecialchars($enrollment['course_title']); ?></h6>
                                                <small class="text-muted"><i class="fa fa-user-tie me-1"></i> <?php echo htmlspecialchars($enrollment['instructor_name']); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary rounded-pill"><?php echo $enrollment['credits']; ?> Credits</span>
                                                <br>
                                                <small class="text-success"><i class="fa fa-check-circle me-1"></i> Active</small>
                                            </div>
                                        </div>
                                        <div class="progress progress-custom mt-2">
                                            <div class="progress-bar progress-bar-custom" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted">0% Complete</small>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Pending Assignments Section -->
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="card-title"><i class="fa fa-tasks me-2 text-primary"></i>Pending Assignments</h5>
                            <a href="assignments/pending.php" class="btn btn-sm btn-outline-teal">View All</a>
                        </div>
                        <div class="p-4">
                            <?php if (empty($pendingAssignments['assignments'])): ?>
                                <div class="text-center py-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa fa-check-circle fa-3x text-success"></i>
                                    </div>
                                    <p class="text-muted mb-0">No pending assignments. Great job!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($pendingAssignments['assignments'] as $assignment): ?>
                                    <div class="assignment-item p-3 rounded mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1" style="color: #181d38;"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                                <small class="text-muted"><i class="fa fa-book me-1"></i> <?php echo htmlspecialchars($assignment['course_title']); ?></small>
                                                <br>
                                                <small class="text-danger"><i class="fa fa-calendar-alt me-1"></i> Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?></small>
                                            </div>
                                            <a href="assignments/submit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary" style="background: linear-gradient(135deg, #06BBCC, #0598A6); border: none;">
                                                Submit
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mt-2">
                <!-- Recent Grades Section -->
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="card-title"><i class="fa fa-star me-2 text-primary"></i>Recent Grades</h5>
                            <a href="grades/index.php" class="btn btn-sm btn-outline-teal">View All</a>
                        </div>
                        <div class="p-4">
                            <?php if (empty($recentGrades)): ?>
                                <div class="text-center py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa fa-chart-line fa-3x text-muted"></i>
                                    </div>
                                    <p class="text-muted mb-0">No grades available yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-custom table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Assignment</th>
                                                <th>Course</th>
                                                <th>Grade</th>
                                             </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentGrades as $grade): ?>
                                                <tr>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($grade['assignment_title']); ?></td>
                                                    <td><?php echo htmlspecialchars($grade['course_title']); ?></td>
                                                    <td>
                                                        <span class="badge badge-grade-<?php echo $grade['letter_grade']; ?> px-3 py-2">
                                                            <?php echo $grade['grade_value']; ?>% (<?php echo $grade['letter_grade']; ?>)
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Overdue Assignments Section -->
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="card-title"><i class="fa fa-exclamation-triangle me-2 text-danger"></i>Overdue Assignments</h5>
                        </div>
                        <div class="p-4">
                            <?php if (empty($overdueAssignments['assignments'])): ?>
                                <div class="text-center py-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa fa-clock fa-3x text-success"></i>
                                    </div>
                                    <p class="text-muted mb-0">No overdue assignments. You're on track!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($overdueAssignments['assignments'] as $assignment): ?>
                                    <div class="border border-danger rounded p-3 mb-2" style="background-color: rgba(220, 53, 69, 0.05);">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 text-danger"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                                <small class="text-muted"><i class="fa fa-book me-1"></i> <?php echo htmlspecialchars($assignment['course_title']); ?></small>
                                                <br>
                                                <small class="text-danger"><i class="fa fa-exclamation-triangle me-1"></i> Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?> (Overdue)</small>
                                            </div>
                                            <a href="assignments/submit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-danger btn-sm">
                                                Submit Late
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" style="background-color: #06BBCC; border-color: #06BBCC;"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <script>
        // Smooth back to top
        document.querySelector('.back-to-top')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Add animation to stats cards on load
        document.querySelectorAll('.stat-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    </script>
</body>
</html>