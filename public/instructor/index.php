<?php
// Instructor Dashboard
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
                <a href="courses/my-courses.php" class="nav-item nav-link">My Courses</a>
                <a href="assignments/list.php" class="nav-item nav-link">Assignments</a>
                <a href="profile/index.php" class="nav-item nav-link">Profile</a>
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
                    <h1 class="text-white">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                    <p class="text-white mb-0">Manage your courses and engage with students</p>
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
                        <i class="fa fa-book fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo $totalCourses; ?></h2>
                        <p class="mb-0">Total Courses</p>
                        <a href="courses/my-courses.php" class="btn btn-sm btn-light mt-2">View All</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-success text-white rounded p-4 text-center">
                        <i class="fa fa-users fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo $totalStudents; ?></h2>
                        <p class="mb-0">Enrolled Students</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-warning text-dark rounded p-4 text-center">
                        <i class="fa fa-tasks fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo $pendingGrading; ?></h2>
                        <p class="mb-0">Pending Grading</p>
                        <a href="assignments/grade.php" class="btn btn-sm btn-dark mt-2">Grade Now</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-info text-white rounded p-4 text-center">
                        <i class="fa fa-plus-circle fa-3x mb-3"></i>
                        <h2 class="mb-0">New</h2>
                        <p class="mb-0">Create Course</p>
                        <a href="courses/create.php" class="btn btn-sm btn-light mt-2">Create</a>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- My Courses -->
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">My Courses</h5>
                            <a href="courses/create.php" class="btn btn-sm btn-primary">+ New Course</a>
                        </div>
                        
                        <?php if (empty($courses)): ?>
                            <div class="text-center py-4">
                                <i class="fa fa-book-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">You haven't created any courses yet.</p>
                                <a href="courses/create.php" class="btn btn-primary">Create Your First Course</a>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($courses as $course): ?>
                                    <a href="courses/manage.php?id=<?php echo $course['id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($course['title']); ?></h6>
                                                <small class="text-muted">Code: <?php echo htmlspecialchars($course['code']); ?></small>
                                                <br>
                                                <small class="text-<?php echo $course['status'] === 'published' ? 'success' : ($course['status'] === 'pending_approval' ? 'warning' : 'secondary'); ?>">
                                                    Status: <?php echo ucfirst(str_replace('_', ' ', $course['status'])); ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary"><?php echo $course['enrollment_count']; ?> Students</span>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Upcoming Assignments -->
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Upcoming Deadlines</h5>
                            <a href="assignments/create.php" class="btn btn-sm btn-primary">+ New Assignment</a>
                        </div>
                        
                        <?php if (empty($upcomingAssignments['assignments'])): ?>
                            <div class="text-center py-4">
                                <i class="fa fa-calendar-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No upcoming assignments due.</p>
                                <a href="assignments/create.php" class="btn btn-primary">Create Assignment</a>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($upcomingAssignments['assignments'] as $assignment): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                                <small class="text-muted">Course: <?php echo htmlspecialchars($assignment['course_title']); ?></small>
                                                <br>
                                                <small class="text-danger">
                                                    <i class="fa fa-calendar-alt me-1"></i>Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?>
                                                </small>
                                                <br>
                                                <small><?php echo $assignment['submission_count']; ?> submissions received</small>
                                            </div>
                                            <a href="assignments/grade.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-warning">Grade</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Quick Actions</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="courses/create.php" class="btn btn-outline-primary">
                                <i class="fa fa-plus-circle me-2"></i>Create Course
                            </a>
                            <a href="assignments/create.php" class="btn btn-outline-primary">
                                <i class="fa fa-tasks me-2"></i>Create Assignment
                            </a>
                            <a href="courses/my-courses.php" class="btn btn-outline-primary">
                                <i class="fa fa-book me-2"></i>Manage Courses
                            </a>
                            <a href="assignments/grade.php" class="btn btn-outline-warning">
                                <i class="fa fa-check-circle me-2"></i>Grade Submissions
                            </a>
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