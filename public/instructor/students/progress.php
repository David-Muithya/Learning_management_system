<?php
// Student Progress Tracking
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Enrollment;
use SkillMaster\Models\Assignment;
use SkillMaster\Models\Submission;
use SkillMaster\Models\Grade;

RoleMiddleware::check('instructor');

$courseModel = new Course();
$enrollmentModel = new Enrollment();
$assignmentModel = new Assignment();
$submissionModel = new Submission();
$gradeModel = new Grade();

$instructorId = $_SESSION['user_id'];
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

// Verify course belongs to instructor
$course = $courseModel->getById($courseId);
if (!$course || $course['instructor_id'] != $instructorId) {
    header('Location: enrolled.php');
    exit;
}

// Get student info - FIXED: Use getDB() method instead of direct property access
$db = $enrollmentModel->getDB();
$stmt = $db->prepare("SELECT first_name, last_name, email, profile_pic FROM users WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: enrolled.php?course_id=' . $courseId);
    exit;
}

// Get assignments for this course
$assignments = $assignmentModel->getByCourse($courseId);

// Get submissions and grades
$gradesData = [];
$totalEarned = 0;
$totalPossible = 0;
$completedAssignments = 0;

foreach ($assignments as $assignment) {
    // Get submission for this student
    $stmt = $submissionModel->getDB()->prepare("
        SELECT s.*, g.grade_value, g.letter_grade
        FROM submissions s
        LEFT JOIN grades g ON g.assignment_id = s.assignment_id 
        WHERE s.assignment_id = ? AND s.student_id = ?
    ");
    $stmt->execute([$assignment['id'], $studentId]);
    $submissionData = $stmt->fetch();
    
    $gradesData[] = [
        'assignment' => $assignment,
        'submission' => $submissionData,
        'grade' => $submissionData['grade_value'] ?? null
    ];
    
    if ($submissionData && $submissionData['grade_value'] !== null) {
        $totalEarned += $submissionData['grade_value'];
        $totalPossible += $assignment['max_points'];
        $completedAssignments++;
    }
}

$overallPercentage = $totalPossible > 0 ? ($totalEarned / $totalPossible) * 100 : 0;
$letterGrade = $gradeModel->calculateLetterGrade($overallPercentage, 100);

$page_title = 'Student Progress - ' . APP_NAME;
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
        .progress-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .progress-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(6, 187, 204, 0.1);
        }
        .grade-A { background-color: #198754; color: white; }
        .grade-B { background-color: #0dcaf0; color: #000; }
        .grade-C { background-color: #ffc107; color: #000; }
        .grade-D { background-color: #fd7e14; color: white; }
        .grade-F { background-color: #dc3545; color: white; }
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
                <a href="../courses/my-courses.php" class="nav-item nav-link">My Courses</a>
                <a href="../assignments/list.php" class="nav-item nav-link">Assignments</a>
                <a href="enrolled.php" class="nav-item nav-link">Students</a>
                <a href="../profile/index.php" class="nav-item nav-link">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid py-4 mb-5" style="background-color: #06BBCC;">
        <div class="container text-center">
            <h1 class="text-white">Student Progress</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?> - <?php echo htmlspecialchars($course['title']); ?></p>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Student Info Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm progress-card">
                        <i class="fa fa-check-circle fa-2x text-success mb-2"></i>
                        <h3 class="mb-0"><?php echo $completedAssignments; ?></h3>
                        <p class="text-muted mb-0">Assignments Completed</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm progress-card">
                        <i class="fa fa-chart-line fa-2x text-primary mb-2" style="color: #06BBCC !important;"></i>
                        <h3 class="mb-0"><?php echo round($overallPercentage, 1); ?>%</h3>
                        <p class="text-muted mb-0">Overall Progress</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm progress-card">
                        <i class="fa fa-star fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0"><?php echo $totalEarned; ?>/<?php echo $totalPossible; ?></h3>
                        <p class="text-muted mb-0">Total Points</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm progress-card">
                        <i class="fa fa-graduation-cap fa-2x text-primary mb-2" style="color: #06BBCC !important;"></i>
                        <h3 class="mb-0">
                            <span class="badge grade-<?php echo $letterGrade; ?> fs-5 px-3 py-2">
                                <?php echo $letterGrade; ?>
                            </span>
                        </h3>
                        <p class="text-muted mb-0">Current Grade</p>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="bg-white rounded p-4 shadow-sm mb-4">
                <h5 class="mb-3" style="color: #06BBCC;">Course Progress</h5>
                <div class="progress mb-2" style="height: 25px; border-radius: 30px;">
                    <div class="progress-bar bg-success" style="width: <?php echo $overallPercentage; ?>%; border-radius: 30px;">
                        <?php echo round($overallPercentage, 1); ?>% Complete
                    </div>
                </div>
                <small class="text-muted">Based on completed assignments and grades</small>
            </div>
            
            <!-- Assignments Table -->
            <div class="bg-white rounded p-4 shadow-sm">
                <h5 class="mb-3" style="color: #06BBCC;">Assignment Performance</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #06BBCC; color: white;">
                            <tr>
                                <th>Assignment</th>
                                <th>Due Date</th>
                                <th>Submitted</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gradesData as $item): 
                                $assignment = $item['assignment'];
                                $submission = $item['submission'];
                                $grade = $item['grade'];
                                $isSubmitted = $submission && $submission['submitted_at'];
                                $isGraded = $grade !== null;
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($assignment['title']); ?></strong></td>
                                    <td><?php echo date('M d, Y', strtotime($assignment['due_date'])); ?></td>
                                    <td>
                                        <?php if ($isSubmitted): ?>
                                            <i class="fa fa-check-circle text-success"></i> <?php echo date('M d', strtotime($submission['submitted_at'])); ?>
                                        <?php else: ?>
                                            <i class="fa fa-times-circle text-danger"></i> Not submitted
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isGraded): ?>
                                            <strong><?php echo $grade; ?> / <?php echo $assignment['max_points']; ?></strong>
                                            <br>
                                            <small><?php echo round(($grade / $assignment['max_points']) * 100, 1); ?>%</small>
                                        <?php elseif ($isSubmitted): ?>
                                            <span class="text-warning">Pending Grade</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isGraded): ?>
                                            <span class="badge bg-success">Graded</span>
                                        <?php elseif ($isSubmitted): ?>
                                            <span class="badge bg-warning text-dark">Submitted</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Not Started</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($submission && $submission['feedback']): ?>
                                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#feedbackModal<?php echo $assignment['id']; ?>">
                                                <i class="fa fa-comment"></i> View
                                            </button>
                                            
                                            <!-- Feedback Modal -->
                                            <div class="modal fade" id="feedbackModal<?php echo $assignment['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="background-color: #06BBCC;">
                                                            <h5 class="modal-title text-white">Feedback - <?php echo htmlspecialchars($assignment['title']); ?></h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Student:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
                                                            <p><strong>Grade:</strong> <?php echo $grade; ?> / <?php echo $assignment['max_points']; ?></p>
                                                            <hr>
                                                            <p><strong>Feedback:</strong></p>
                                                            <div class="bg-light p-3 rounded">
                                                                <?php echo nl2br(htmlspecialchars($submission['feedback'])); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">No feedback</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="enrolled.php?course_id=<?php echo $courseId; ?>" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to Students
                </a>
                <a href="mailto:<?php echo $student['email']; ?>" class="btn btn-primary" style="background-color: #06BBCC; border-color: #06BBCC;">
                    <i class="fa fa-envelope me-2"></i>Contact Student
                </a>
            </div>
            
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