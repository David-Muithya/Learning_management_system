<?php
// View Assignment Grades
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Grade;

RoleMiddleware::check('student');

$gradeModel = new Grade();
$grades = $gradeModel->getByStudent($_SESSION['user_id']);

$page_title = 'My Grades - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body style="background-color: #F0FBFC;">

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
                <a href="pending.php" class="nav-item nav-link">Assignments</a>
                <a href="grades.php" class="nav-item nav-link active">Grades</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">My Grades</h1>
            <p class="text-white mb-0">View your assignment grades</p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="bg-light rounded p-4">
                <?php if (empty($grades)): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-chart-line fa-4x text-muted mb-3"></i>
                        <h4>No grades available</h4>
                        <p class="text-muted">Your grades will appear here once assignments are graded.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Assignment</th>
                                    <th>Course</th>
                                    <th>Score</th>
                                    <th>Max Points</th>
                                    <th>Percentage</th>
                                    <th>Grade</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grades as $grade): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($grade['assignment_title']); ?></td>
                                        <td><?php echo htmlspecialchars($grade['course_title']); ?></td>
                                        <td><?php echo $grade['grade_value']; ?></td>
                                        <td><?php echo $grade['max_points']; ?></td>
                                        <td>
                                            <?php 
                                            $percentage = ($grade['grade_value'] / $grade['max_points']) * 100;
                                            echo round($percentage, 1) . '%';
                                            ?>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-<?php echo $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger'); ?>" 
                                                     style="width: <?php echo $percentage; ?>%"></div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary"><?php echo $grade['letter_grade']; ?></span></td>
                                        <td>
                                            <?php if ($grade['feedback']): ?>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#feedbackModal<?php echo $grade['id']; ?>">
                                                    <i class="fa fa-comment"></i> View
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">No feedback</span>
                                            <?php endif; ?>
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

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
</body>
</html>