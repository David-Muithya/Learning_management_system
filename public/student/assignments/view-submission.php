<?php
// View Single Submission Details
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Submission;

RoleMiddleware::check('student');

$submissionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$submissionModel = new Submission();

$submission = $submissionModel->getById($submissionId);

// Verify student owns this submission
if (!$submission || $submission['student_id'] != $_SESSION['user_id']) {
    header('Location: submitted.php');
    exit;
}

$page_title = 'Submission Details - ' . APP_NAME;
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
                <a href="pending.php" class="nav-item nav-link">Pending</a>
                <a href="submitted.php" class="nav-item nav-link active">Submitted</a>
                <a href="grades.php" class="nav-item nav-link">Grades</a>
                <a href="../profile/index.php" class="nav-item nav-link">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5" style="background-color: #06BBCC !important;">
        <div class="container text-center">
            <h1 class="text-white">Submission Details</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($submission['assignment_title']); ?></p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-white rounded p-5 shadow-sm">
                        <div class="mb-4">
                            <h4><?php echo htmlspecialchars($submission['assignment_title']); ?></h4>
                            <p class="text-muted">Course: <?php echo htmlspecialchars($submission['course_title']); ?></p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <small class="text-muted">Submitted On</small>
                                <p class="fw-bold"><?php echo date('F j, Y g:i A', strtotime($submission['submitted_at'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Due Date</small>
                                <p class="fw-bold"><?php echo date('F j, Y g:i A', strtotime($submission['due_date'])); ?></p>
                            </div>
                        </div>
                        
                        <?php if ($submission['grade'] !== null): ?>
                            <div class="alert alert-success">
                                <strong>Grade:</strong> <?php echo $submission['grade']; ?> / <?php echo $submission['max_points']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($submission['feedback'])): ?>
                            <div class="mb-4">
                                <h5>Instructor Feedback</h5>
                                <div class="bg-light p-3 rounded">
                                    <?php echo nl2br(htmlspecialchars($submission['feedback'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($submission['submission_text'])): ?>
                            <div class="mb-4">
                                <h5>Your Submission</h5>
                                <div class="bg-light p-3 rounded">
                                    <?php echo nl2br(htmlspecialchars($submission['submission_text'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2">
                            <a href="submitted.php" class="btn btn-secondary">Back to Submissions</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>