<?php
// Instructor Dashboard - Premium Version
require_once __DIR__ . '/../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Assignment;
use SkillMaster\Models\Submission;

// Only instructors can access
RoleMiddleware::check('instructor');

$courseModel = new Course();
$assignmentModel = new Assignment();
$submissionModel = new Submission();

$instructorId = $_SESSION['user_id'];

// Get instructor's courses
$courses = $courseModel->getByInstructor($instructorId);
$totalCourses = count($courses);

// Get total students across all courses
$totalStudents = 0;
foreach ($courses as $course) {
    $totalStudents += $course['enrollment_count'];
}

// Get pending submissions count
$pendingGrading = $submissionModel->getPendingGradingCount($instructorId);

// Get upcoming assignments
$upcomingAssignments = $assignmentModel->getByInstructor($instructorId, 1, 5);

$page_title = 'Instructor Dashboard - ' . APP_NAME;
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
        
        .welcome-avatar {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
        }
        
        .welcome-avatar i {
            font-size: 28px;
            color: white;
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
        
        /* Status Badges */
        .badge-status-published {
            background: linear-gradient(135deg, #198754, #157347);
            color: white;
        }
        
        .badge-status-pending {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #000;
        }
        
        .badge-status-draft {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
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
        
        .btn-primary-gradient {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border: none;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        
        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(6, 187, 204, 0.4);
        }
        
        .quick-action-btn {
            transition: all 0.3s ease;
            border-radius: 40px;
            padding: 10px 24px;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(6, 187, 204, 0.2);
        }
        
        /* Animated Entrance */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-card {
            animation: fadeInUp 0.6s ease forwards;
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
                <a href="courses/my-courses.php" class="nav-item nav-link">My Courses</a>
                <a href="assignments/list.php" class="nav-item nav-link">Assignments</a>
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
                    <div class="welcome-avatar">
                        <i class="fa fa-chalkboard-user"></i>
                    </div>
                    <h1 class="text-white display-4 fw-bold mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                    <p class="text-white opacity-75 fs-5 mb-0">Manage your courses, track student progress, and create engaging content</p>
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
                <div class="col-lg-3 col-md-6 animate-card" style="animation-delay: 0.1s;">
                    <div class="stat-card text-white p-4" style="background: linear-gradient(135deg, #06BBCC, #0598A6);">
                        <div class="stat-icon"><i class="fa fa-book"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Total Courses</h6>
                                <h2 class="text-white mb-0"><?php echo $totalCourses; ?></h2>
                            </div>
                            <i class="fa fa-book-open fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3">
                            <a href="courses/my-courses.php" class="btn btn-sm btn-light rounded-pill px-3">View All →</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 animate-card" style="animation-delay: 0.2s;">
                    <div class="stat-card text-white p-4" style="background: linear-gradient(135deg, #198754, #157347);">
                        <div class="stat-icon"><i class="fa fa-users"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Enrolled Students</h6>
                                <h2 class="text-white mb-0"><?php echo $totalStudents; ?></h2>
                            </div>
                            <i class="fa fa-user-graduate fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3 small text-white-50">
                            <i class="fa fa-arrow-right me-1"></i> Active learners
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 animate-card" style="animation-delay: 0.3s;">
                    <div class="stat-card text-dark p-4" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
                        <div class="stat-icon"><i class="fa fa-tasks"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-dark-50 mb-2">Pending Grading</h6>
                                <h2 class="text-dark mb-0"><?php echo $pendingGrading; ?></h2>
                            </div>
                            <i class="fa fa-clipboard-list fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3">
                            <a href="assignments/grade.php" class="btn btn-sm btn-dark rounded-pill px-3">Grade Now →</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 animate-card" style="animation-delay: 0.4s;">
                    <div class="stat-card text-white p-4" style="background: linear-gradient(135deg, #0dcaf0, #0aa5c6);">
                        <div class="stat-icon"><i class="fa fa-plus-circle"></i></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Create Course</h6>
                                <h2 class="text-white mb-0">New</h2>
                            </div>
                            <i class="fa fa-chalkboard fa-3x opacity-50"></i>
                        </div>
                        <div class="mt-3">
                            <a href="courses/create.php" class="btn btn-sm btn-light rounded-pill px-3">Create →</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- My Courses Section -->
                <div class="col-lg-6">
                    <div class="dashboard-card animate-card" style="animation-delay: 0.2s;">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="card-title"><i class="fa fa-book me-2 text-primary"></i>My Courses</h5>
                            <a href="courses/create.php" class="btn btn-sm btn-primary-gradient">
                                <i class="fa fa-plus-circle me-1"></i> New Course
                            </a>
                        </div>
                        <div class="p-4">
                            <?php if (empty($courses)): ?>
                                <div class="text-center py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa fa-book-open fa-3x text-primary"></i>
                                    </div>
                                    <p class="text-muted mb-3">You haven't created any courses yet.</p>
                                    <a href="courses/create.php" class="btn btn-primary-gradient px-4">
                                        Create Your First Course
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($courses as $course): ?>
                                    <a href="courses/manage.php?id=<?php echo $course['id']; ?>" class="course-item text-decoration-none d-block p-3 rounded mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1" style="color: #181d38;"><?php echo htmlspecialchars($course['title']); ?></h6>
                                                <small class="text-muted"><i class="fa fa-code me-1"></i> Code: <?php echo htmlspecialchars($course['code']); ?></small>
                                                <br>
                                                <span class="badge badge-status-<?php echo $course['status']; ?> mt-1 px-3 py-1">
                                                    <?php echo ucfirst(str_replace('_', ' ', $course['status'])); ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                                    <i class="fa fa-users me-1"></i> <?php echo $course['enrollment_count']; ?> Students
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Upcoming Assignments Section -->
                <div class="col-lg-6">
                    <div class="dashboard-card animate-card" style="animation-delay: 0.3s;">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="card-title"><i class="fa fa-calendar-alt me-2 text-primary"></i>Upcoming Deadlines</h5>
                            <a href="assignments/create.php" class="btn btn-sm btn-primary-gradient">
                                <i class="fa fa-plus-circle me-1"></i> New Assignment
                            </a>
                        </div>
                        <div class="p-4">
                            <?php if (empty($upcomingAssignments['assignments'])): ?>
                                <div class="text-center py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa fa-calendar-alt fa-3x text-primary"></i>
                                    </div>
                                    <p class="text-muted mb-3">No upcoming assignments due.</p>
                                    <a href="assignments/create.php" class="btn btn-primary-gradient px-4">
                                        Create Assignment
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($upcomingAssignments['assignments'] as $assignment): ?>
                                    <div class="assignment-item p-3 rounded mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1" style="color: #181d38;"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                                <small class="text-muted"><i class="fa fa-book me-1"></i> <?php echo htmlspecialchars($assignment['course_title']); ?></small>
                                                <br>
                                                <small class="text-danger">
                                                    <i class="fa fa-calendar-alt me-1"></i> Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?>
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fa fa-paper-plane me-1"></i> <?php echo $assignment['submission_count']; ?> submissions received
                                                </small>
                                            </div>
                                            <a href="assignments/grade.php?id=<?php echo $assignment['id']; ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                                Grade Now
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Section -->
            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="dashboard-card animate-card" style="animation-delay: 0.4s;">
                        <div class="card-header-custom">
                            <h5 class="card-title"><i class="fa fa-bolt me-2 text-primary"></i>Quick Actions</h5>
                        </div>
                        <div class="p-4">
                            <div class="d-flex flex-wrap gap-3 justify-content-center">
                                <a href="courses/create.php" class="btn btn-outline-teal quick-action-btn">
                                    <i class="fa fa-plus-circle me-2"></i>Create Course
                                </a>
                                <a href="assignments/create.php" class="btn btn-outline-teal quick-action-btn">
                                    <i class="fa fa-tasks me-2"></i>Create Assignment
                                </a>
                                <a href="courses/my-courses.php" class="btn btn-outline-teal quick-action-btn">
                                    <i class="fa fa-book me-2"></i>Manage Courses
                                </a>
                                <a href="assignments/grade.php" class="btn btn-outline-teal quick-action-btn">
                                    <i class="fa fa-check-circle me-2"></i>Grade Submissions
                                </a>
                                <a href="students/enrolled.php" class="btn btn-outline-teal quick-action-btn">
                                    <i class="fa fa-users me-2"></i>View Students
                                </a>
                            </div>
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
        
        // Add hover effect to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.3s ease';
            });
        });
    </script>
</body>
</html>