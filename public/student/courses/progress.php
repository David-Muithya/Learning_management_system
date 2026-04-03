<?php
// Course Progress Tracking
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Enrollment;
use SkillMaster\Models\CourseProgress;

RoleMiddleware::check('student');

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$courseModel = new Course();
$enrollmentModel = new Enrollment();
$progressModel = new CourseProgress();

// Get course details
$course = $courseModel->getCourse($courseId);

if (!$course) {
    header('Location: enrolled.php');
    exit;
}

// Check if enrolled
if (!$enrollmentModel->isEnrolled($_SESSION['user_id'], $courseId)) {
    header('Location: browse.php');
    exit;
}

// Get progress
$progress = $progressModel->getStudentProgress($_SESSION['user_id'], $courseId);
$modules = $courseModel->getCourseContent($courseId);

$page_title = 'Course Progress - ' . $course['title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
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
                <a href="enrolled.php" class="nav-item nav-link active">My Courses</a>
                <a href="browse.php" class="nav-item nav-link">Browse Courses</a>
                <a href="../assignments/pending.php" class="nav-item nav-link">Assignments</a>
                <a href="../grades/index.php" class="nav-item nav-link">Grades</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white"><?php echo htmlspecialchars($course['title']); ?></h1>
            <p class="text-white mb-0">Track your learning progress</p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Course Content</h5>
                        <?php foreach ($modules as $index => $module): ?>
                            <div class="mb-4">
                                <h6>Module <?php echo $index + 1; ?>: <?php echo htmlspecialchars($module['title']); ?></h6>
                                <p class="text-muted"><?php echo htmlspecialchars($module['description']); ?></p>
                                <?php
                                $materials = $courseModel->getMaterialsByModule($module['id']);
                                if (!empty($materials)):
                                ?>
                                    <ul class="list-group">
                                        <?php foreach ($materials as $material): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fa fa-<?php echo $material['type'] === 'video' ? 'video' : 'file-alt'; ?> text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($material['title']); ?>
                                                </div>
                                                <span class="badge bg-secondary"><?php echo ucfirst($material['type']); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Your Progress</h5>
                        <div class="text-center mb-3">
                            <h2 class="display-4 text-primary"><?php echo $progress['percentage']; ?>%</h2>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-primary" style="width: <?php echo $progress['percentage']; ?>%"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Completed: <?php echo $progress['completed_materials']; ?></span>
                            <span>Total: <?php echo $progress['total_materials']; ?></span>
                        </div>
                        <hr>
                        <a href="../assignments/pending.php?course_id=<?php echo $courseId; ?>" class="btn btn-outline-primary w-100">View Assignments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
</body>
</html>