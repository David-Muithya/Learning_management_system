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

// Get student info
$stmt = $enrollmentModel->db->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
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
    $submission = $submissionModel->getForGradingById($assignment['id'], $instructorId);
    // Custom query for student's submission
    $stmt = $submissionModel->db->prepare("
        SELECT s.*, g.grade_value, g.letter_grade
        FROM submissions s
        LEFT JOIN grades g ON g.assignment_id = s.assignment_id AND g.enrollment_id = 
            (SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?)
        WHERE s.assignment_id = ? AND s.student_id = ?
    ");
    $stmt->execute([$studentId, $courseId, $assignment['id'], $studentId]);
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
                <a href="enrolled.php" class="nav-item nav-link">Students</a>
                <a href="../profile/index.php" class="nav-item nav-link">Profile</a>
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
                    <h1 class="text-white">Student Progress</h1>
                    <p class="text-white mb-0"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?> - <?php echo htmlspecialchars($course['title']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Student Info Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="bg-light rounded p-3 text-center">
                        <i class="fa fa-user-graduate fa-2x text-primary mb-2"></i>
                        <h4 class="mb-0"><?php echo $completedAssignments; ?></h4>
                        <small class="text-muted">Assignments Completed</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded p-3 text-center">
                        <i class="fa fa-chart-line fa-2x text-primary mb-2"></i>
                        <h4 class="mb-0"><?php echo round($overallPercentage, 1); ?>%</h4>
                        <small class="text-muted">Overall Progress</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded p-3 text-center">
                        <i class="fa fa-star fa-2x text-primary mb-2"></i>
                        <h4 class="mb-0"><?php echo $totalEarned; ?>/<?php echo $totalPossible; ?></h4>
                        <small class="text-muted">Total Points</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded p-3 text-center">
                        <i class="fa fa-graduation-cap fa-2x text-primary mb-2"></i>
                        <h4 class="mb-0">
                            <span class="badge bg-<?php 
                                echo $letterGrade === 'A' ? 'success' : 
                                     ($letterGrade === 'B' ? 'info' : 
                                     ($letterGrade === 'C' ? 'warning' : 
                                     ($letterGrade === 'D' ? 'secondary' : 'danger'))); 
                            ?> fs-4"><?php echo $letterGrade; ?></span>
                        </h4>
                        <small class="text-muted">Current Grade</small>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="bg-light rounded p-4 mb-4">
                <h5 class="mb-3">Course Progress</h5>
                <div class="progress mb-2" style="height: 30px;">
                    <div class="progress-bar bg-success" style="width: <?php echo $overallPercentage; ?>%;">
                        <?php echo round($overallPercentage, 1); ?>% Complete
                    </div>
                </div>
                <small class="text-muted">Based on completed assignments and grades</small>
            </div>
            
            <!-- Assignments Table -->
            <div class="bg-light rounded p-4">
                <h5 class="mb-3">Assignment Performance</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-primary text-white">
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
                                            <span class="badge bg-warning">Submitted</span>
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
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Feedback - <?php echo htmlspecialchars($assignment['title']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Student:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
                                                            <p><strong>Grade:</strong> <?php echo $grade; ?> / <?php echo $assignment['max_points']; ?></p>
                                                            <hr>
                                                            <p><strong>Feedback:</strong></p>
                                                            <div class="bg-white rounded p-3">
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
                <a href="mailto:<?php echo $student['email']; ?>" class="btn btn-primary">
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

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>