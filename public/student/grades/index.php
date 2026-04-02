<?php
// Student Grades Page
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Grade;
use SkillMaster\Models\Enrollment;

// Only students can access
RoleMiddleware::check('student');

$gradeModel = new Grade();
$enrollmentModel = new Enrollment();

$studentId = $_SESSION['user_id'];
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : null;

// Get transcript
$transcript = $gradeModel->getTranscript($studentId);

// Get grades by course if specified
if ($courseId) {
    $grades = $gradeModel->getByStudent($studentId);
    $courseGrades = array_filter($grades, function($grade) use ($courseId) {
        return $grade['course_id'] == $courseId;
    });
}

// Get all enrolled courses for filter
$enrolledCourses = $enrollmentModel->getByStudent($studentId, 'active');

$page_title = 'My Grades - ' . APP_NAME;
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
                <a href="../courses/enrolled.php" class="nav-item nav-link">My Courses</a>
                <a href="../assignments/pending.php" class="nav-item nav-link">Assignments</a>
                <a href="index.php" class="nav-item nav-link active">Grades</a>
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
                    <h1 class="text-white">My Grades</h1>
                    <p class="text-white mb-0">Track your academic progress</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- GPA Card -->
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="bg-primary text-white rounded p-4 text-center">
                        <i class="fa fa-star fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo number_format($transcript['gpa'], 2); ?></h2>
                        <p class="mb-0">Cumulative GPA</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-success text-white rounded p-4 text-center">
                        <i class="fa fa-graduation-cap fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo $transcript['total_credits']; ?></h2>
                        <p class="mb-0">Total Credits Earned</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-info text-white rounded p-4 text-center">
                        <i class="fa fa-book fa-3x mb-3"></i>
                        <h2 class="mb-0"><?php echo count($transcript['courses']); ?></h2>
                        <p class="mb-0">Courses Completed</p>
                    </div>
                </div>
            </div>
            
            <!-- Course Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="" class="d-flex gap-2">
                        <select name="course_id" class="form-select">
                            <option value="">All Courses</option>
                            <?php foreach ($enrolledCourses['enrollments'] as $course): ?>
                                <option value="<?php echo $course['course_id']; ?>" <?php echo $courseId == $course['course_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['course_title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <?php if ($courseId): ?>
                            <a href="index.php" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Transcript Table -->
            <div class="bg-light rounded p-4">
                <h5 class="mb-3">Academic Transcript</h5>
                
                <?php if (empty($transcript['courses'])): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-chart-line fa-4x text-muted mb-3"></i>
                        <h4>No grades available</h4>
                        <p class="text-muted">Your grades will appear here once you complete courses.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Title</th>
                                    <th>Credits</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transcript['courses'] as $course): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['course_code'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($course['course_title']); ?></td>
                                        <td><?php echo $course['credits']; ?></td>
                                        <td>
                                            <?php if ($course['final_grade']): ?>
                                                <span class="badge bg-<?php 
                                                    echo $course['final_grade'] === 'A' ? 'success' : 
                                                        ($course['final_grade'] === 'B' ? 'info' : 
                                                        ($course['final_grade'] === 'C' ? 'warning' : 
                                                        ($course['final_grade'] === 'D' ? 'secondary' : 'danger'))); 
                                                ?> fs-6 p-2">
                                                    <?php echo $course['final_grade']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">In Progress</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $course['status'] === 'completed' ? 'success' : 'primary'; ?>">
                                                <?php echo ucfirst($course['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $course['completed_at'] ? date('M Y', strtotime($course['completed_at'])) : '-'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr class="fw-bold">
                                    <td colspan="2">Total</td>
                                    <td><?php echo $transcript['total_credits']; ?></td>
                                    <td colspan="3">GPA: <?php echo number_format($transcript['gpa'], 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Grade Distribution for Selected Course -->
            <?php if ($courseId && isset($courseGrades) && !empty($courseGrades)): ?>
                <div class="bg-light rounded p-4 mt-4">
                    <h5 class="mb-3">Course Grade Breakdown</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Assignment</th>
                                    <th>Score</th>
                                    <th>Max Points</th>
                                    <th>Percentage</th>
                                    <th>Letter Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalEarned = 0;
                                $totalPossible = 0;
                                foreach ($courseGrades as $grade): 
                                    $totalEarned += $grade['grade_value'];
                                    $totalPossible += $grade['max_points'];
                                    $percentage = ($grade['grade_value'] / $grade['max_points']) * 100;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($grade['assignment_title']); ?></td>
                                        <td><?php echo $grade['grade_value']; ?></td>
                                        <td><?php echo $grade['max_points']; ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-<?php echo $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger'); ?>" 
                                                     style="width: <?php echo $percentage; ?>%">
                                                    <?php echo round($percentage, 1); ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary"><?php echo $grade['letter_grade']; ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold bg-light">
                                    <td>Total</td>
                                    <td><?php echo $totalEarned; ?></td>
                                    <td><?php echo $totalPossible; ?></td>
                                    <td><?php echo round(($totalEarned / $totalPossible) * 100, 1); ?>%</td>
                                    <td>
                                        <?php 
                                        $finalPercentage = ($totalEarned / $totalPossible) * 100;
                                        $finalGrade = $gradeModel->calculateLetterGrade($finalPercentage, 100);
                                        ?>
                                        <span class="badge bg-<?php echo $finalGrade === 'A' ? 'success' : ($finalGrade === 'F' ? 'danger' : 'warning'); ?> fs-6">
                                            <?php echo $finalGrade; ?>
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
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
                        <a href="../../index.php">Home</a>
                        <a href="../../about.php">About</a>
                        <a href="../../contact.php">Contact</a>
                    </div>
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