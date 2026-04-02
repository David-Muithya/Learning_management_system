<?php
// Student Dashboard
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
                <a href="courses/enrolled.php" class="nav-item nav-link">My Courses</a>
                <a href="assignments/pending.php" class="nav-item nav-link">Assignments</a>
                <a href="grades/index.php" class="nav-item nav-link">Grades</a>
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
                    <h1 class="text-white">Welcome Back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                    <p class="text-white mb-0">Continue your learning journey</p>
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
                        <h2 class="mb-0"><?php echo $enrolledCourses['total']; ?></h2>
                        <p class="mb-0">Active Courses</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-success text-white rounded p-4 text-center">
                        <i class="fa fa-check-circle fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo $completedCount; ?></h2>
                        <p class="mb-0">Completed Courses</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-warning text-dark rounded p-4 text-center">
                        <i class="fa fa-tasks fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo $pendingAssignments['total']; ?></h2>
                        <p class="mb-0">Pending Assignments</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-info text-white rounded p-4 text-center">
                        <i class="fa fa-chart-line fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo $totalEnrolled > 0 ? round(($completedCount / $totalEnrolled) * 100) : 0; ?>%</h2>
                        <p class="mb-0">Overall Progress</p>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- My Courses -->
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">My Active Courses</h5>
                            <a href="courses/enrolled.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        
                        <?php if (empty($enrolledCourses['enrollments'])): ?>
                            <div class="text-center py-4">
                                <i class="fa fa-book-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">You haven't enrolled in any courses yet.</p>
                                <a href="courses/browse.php" class="btn btn-primary btn-sm">Browse Courses</a>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($enrolledCourses['enrollments'] as $enrollment): ?>
                                    <a href="courses/details.php?id=<?php echo $enrollment['course_id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($enrollment['course_title']); ?></h6>
                                                <small class="text-muted">Instructor: <?php echo htmlspecialchars($enrollment['instructor_name']); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-primary"><?php echo $enrollment['credits']; ?> Credits</small>
                                                <br>
                                                <small class="text-success">Active</small>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-primary" style="width: 0%"></div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Pending Assignments -->
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Pending Assignments</h5>
                            <a href="assignments/pending.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        
                        <?php if (empty($pendingAssignments['assignments'])): ?>
                            <div class="text-center py-4">
                                <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted">No pending assignments. Great job!</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($pendingAssignments['assignments'] as $assignment): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                                <small class="text-muted">Course: <?php echo htmlspecialchars($assignment['course_title']); ?></small>
                                                <br>
                                                <small class="text-danger"><i class="fa fa-calendar-alt me-1"></i>Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?></small>
                                            </div>
                                            <a href="assignments/submit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-primary btn-sm">Submit</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mt-2">
                <!-- Recent Grades -->
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Recent Grades</h5>
                            <a href="grades/index.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        
                        <?php if (empty($recentGrades)): ?>
                            <div class="text-center py-4">
                                <i class="fa fa-chart-simple fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No grades available yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Assignment</th>
                                            <th>Course</th>
                                            <th>Grade</th>
                                            <th>Letter</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentGrades as $grade): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($grade['assignment_title']); ?></td>
                                                <td><?php echo htmlspecialchars($grade['course_title']); ?></td>
                                                <td><?php echo $grade['grade_value']; ?>%</td>
                                                <td><span class="badge bg-<?php echo $grade['letter_grade'] === 'A' ? 'success' : ($grade['letter_grade'] === 'F' ? 'danger' : 'warning'); ?>"><?php echo $grade['letter_grade']; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Overdue Assignments -->
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Overdue Assignments</h5>
                        
                        <?php if (empty($overdueAssignments['assignments'])): ?>
                            <div class="text-center py-4">
                                <i class="fa fa-clock fa-3x text-success mb-3"></i>
                                <p class="text-muted">No overdue assignments. You're on track!</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($overdueAssignments['assignments'] as $assignment): ?>
                                    <div class="list-group-item list-group-item-danger">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                                <small>Course: <?php echo htmlspecialchars($assignment['course_title']); ?></small>
                                                <br>
                                                <small class="text-danger"><i class="fa fa-exclamation-triangle me-1"></i>Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?> (Overdue)</small>
                                            </div>
                                            <a href="assignments/submit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-danger btn-sm">Submit Late</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
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