<?php
// Submitted Assignments Page
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Submission;
use SkillMaster\Models\Assignment;
use SkillMaster\Helpers\Pagination;

RoleMiddleware::check('student');

$submissionModel = new Submission();
$assignmentModel = new Assignment();

$studentId = $_SESSION['user_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Get submitted assignments
$submissionsData = $submissionModel->getByStudent($studentId, $page, 10);

// Get all courses the student is enrolled in for filtering
$enrollmentModel = new SkillMaster\Models\Enrollment();
$enrolledCourses = $enrollmentModel->getByStudent($studentId, 'active');

// Filter by course if selected
$filteredSubmissions = $submissionsData['submissions'];
if ($courseId > 0) {
    $filteredSubmissions = array_filter($filteredSubmissions, function($sub) use ($courseId) {
        return $sub['course_id'] == $courseId;
    });
}

$page_title = 'Submitted Assignments - ' . APP_NAME;
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
        .submission-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid #06BBCC;
        }
        .submission-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(6, 187, 204, 0.1);
        }
        .grade-badge {
            font-size: 1.2rem;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 30px;
        }
        .grade-A { background-color: #198754; color: white; }
        .grade-B { background-color: #0dcaf0; color: #000; }
        .grade-C { background-color: #ffc107; color: #000; }
        .grade-D { background-color: #fd7e14; color: white; }
        .grade-F { background-color: #dc3545; color: white; }
        .grade-pending { background-color: #6c757d; color: white; }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-graded { background-color: #198754; color: white; }
        .status-submitted { background-color: #0dcaf0; color: #000; }
        .status-late { background-color: #dc3545; color: white; }
        .filter-active {
            background-color: #06BBCC !important;
            color: white !important;
            border-color: #06BBCC !important;
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
                <a href="../courses/enrolled.php" class="nav-item nav-link">My Courses</a>
                <a href="pending.php" class="nav-item nav-link">Pending</a>
                <a href="submitted.php" class="nav-item nav-link active">Submitted</a>
                <a href="grades.php" class="nav-item nav-link">Grades</a>
                <a href="../profile/index.php" class="nav-item nav-link">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-4 mb-5" style="background-color: #06BBCC !important;">
        <div class="container text-center">
            <h1 class="text-white">Submitted Assignments</h1>
            <p class="text-white mb-0">Track your submitted assignments and grades</p>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm">
                        <i class="fa fa-check-circle fa-2x text-success mb-2"></i>
                        <h3 class="mb-0"><?php echo $submissionsData['total']; ?></h3>
                        <p class="text-muted mb-0">Total Submissions</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm">
                        <i class="fa fa-star fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0">
                            <?php 
                            $gradedCount = count(array_filter($submissionsData['submissions'], function($s) {
                                return $s['grade'] !== null;
                            }));
                            echo $gradedCount;
                            ?>
                        </h3>
                        <p class="text-muted mb-0">Graded</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm">
                        <i class="fa fa-clock fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0">
                            <?php 
                            $pendingCount = count(array_filter($submissionsData['submissions'], function($s) {
                                return $s['grade'] === null && $s['status'] !== 'late';
                            }));
                            echo $pendingCount;
                            ?>
                        </h3>
                        <p class="text-muted mb-0">Pending Grade</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm">
                        <i class="fa fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                        <h3 class="mb-0">
                            <?php 
                            $lateCount = count(array_filter($submissionsData['submissions'], function($s) {
                                return $s['is_late'] == 1;
                            }));
                            echo $lateCount;
                            ?>
                        </h3>
                        <p class="text-muted mb-0">Late Submissions</p>
                    </div>
                </div>
            </div>
            
            <!-- Course Filter -->
            <?php if (!empty($enrolledCourses['enrollments'])): ?>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <a href="?course_id=0" class="btn btn-outline-primary <?php echo $courseId == 0 ? 'filter-active' : ''; ?>" style="border-color: #06BBCC;">
                            All Courses
                        </a>
                        <?php foreach ($enrolledCourses['enrollments'] as $course): ?>
                            <a href="?course_id=<?php echo $course['course_id']; ?>" class="btn btn-outline-primary <?php echo $courseId == $course['course_id'] ? 'filter-active' : ''; ?>" style="border-color: #06BBCC;">
                                <?php echo htmlspecialchars(substr($course['course_title'], 0, 15)); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (empty($filteredSubmissions)): ?>
                <div class="text-center py-5">
                    <i class="fa fa-inbox fa-4x text-muted mb-3"></i>
                    <h4>No submitted assignments</h4>
                    <p class="text-muted">You haven't submitted any assignments yet.</p>
                    <a href="pending.php" class="btn btn-primary" style="background-color: #06BBCC; border-color: #06BBCC;">
                        <i class="fa fa-tasks me-2"></i>View Pending Assignments
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($filteredSubmissions as $submission): ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="submission-card bg-white rounded p-4 shadow-sm h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="mb-0 text-primary"><?php echo htmlspecialchars($submission['assignment_title']); ?></h5>
                                    <span class="status-badge status-<?php echo $submission['grade'] !== null ? 'graded' : ($submission['is_late'] ? 'late' : 'submitted'); ?>">
                                        <?php 
                                        if ($submission['grade'] !== null) echo 'Graded';
                                        elseif ($submission['is_late']) echo 'Late';
                                        else echo 'Submitted';
                                        ?>
                                    </span>
                                </div>
                                
                                <p class="text-muted small mb-2">
                                    <i class="fa fa-book me-1"></i> <?php echo htmlspecialchars($submission['course_title']); ?>
                                </p>
                                
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fa fa-calendar-alt me-1"></i> Submitted: 
                                        <?php echo date('M d, Y g:i A', strtotime($submission['submitted_at'])); ?>
                                    </small>
                                    <?php if ($submission['due_date']): ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fa fa-hourglass-half me-1"></i> Due: 
                                            <?php echo date('M d, Y', strtotime($submission['due_date'])); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Grade Display -->
                                <div class="text-center my-3">
                                    <?php if ($submission['grade'] !== null): ?>
                                        <?php 
                                        $percentage = ($submission['grade'] / $submission['max_points']) * 100;
                                        $letterGrade = '';
                                        if ($percentage >= 80) $letterGrade = 'A';
                                        elseif ($percentage >= 70) $letterGrade = 'B';
                                        elseif ($percentage >= 60) $letterGrade = 'C';
                                        elseif ($percentage >= 50) $letterGrade = 'D';
                                        else $letterGrade = 'F';
                                        ?>
                                        <div class="grade-badge grade-<?php echo $letterGrade; ?> d-inline-block">
                                            <?php echo $submission['grade']; ?> / <?php echo $submission['max_points']; ?>
                                            <br>
                                            <small>Grade: <?php echo $letterGrade; ?></small>
                                        </div>
                                        <div class="progress mt-2" style="height: 8px;">
                                            <div class="progress-bar bg-<?php echo $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger'); ?>" 
                                                 style="width: <?php echo $percentage; ?>%">
                                            </div>
                                        </div>
                                        <small class="text-muted"><?php echo round($percentage, 1); ?>%</small>
                                    <?php else: ?>
                                        <div class="grade-badge grade-pending d-inline-block">
                                            Pending Grade
                                        </div>
                                        <div class="progress mt-2" style="height: 8px;">
                                            <div class="progress-bar bg-secondary" style="width: 0%"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Feedback Section -->
                                <?php if (!empty($submission['feedback'])): ?>
                                    <div class="mt-3 p-2 bg-light rounded">
                                        <small class="text-primary fw-bold">
                                            <i class="fa fa-comment-dots me-1"></i> Instructor Feedback:
                                        </small>
                                        <p class="small mb-0 mt-1"><?php echo nl2br(htmlspecialchars($submission['feedback'])); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Submission Text Preview -->
                                <?php if (!empty($submission['submission_text'])): ?>
                                    <div class="mt-2">
                                        <small class="text-primary fw-bold">
                                            <i class="fa fa-file-alt me-1"></i> Your Submission:
                                        </small>
                                        <p class="small text-muted mb-0">
                                            <?php echo htmlspecialchars(substr($submission['submission_text'], 0, 100)); ?>
                                            <?php if (strlen($submission['submission_text']) > 100): ?>...<?php endif; ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-3">
                                    <a href="view-submission.php?id=<?php echo $submission['id']; ?>" class="btn btn-sm btn-outline-primary w-100" style="border-color: #06BBCC; color: #06BBCC;">
                                        <i class="fa fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($submissionsData['total_pages'] > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($submissionsData['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $submissionsData['current_page'] - 1; ?><?php echo $courseId ? '&course_id=' . $courseId : ''; ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $submissionsData['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $submissionsData['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $courseId ? '&course_id=' . $courseId : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($submissionsData['current_page'] < $submissionsData['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $submissionsData['current_page'] + 1; ?><?php echo $courseId ? '&course_id=' . $courseId : ''; ?>">
                                        Next
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
            
        </div>
    </div>
    <!-- Content End -->

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
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" style="background-color: #06BBCC; border-color: #06BBCC;">
        <i class="bi bi-arrow-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>