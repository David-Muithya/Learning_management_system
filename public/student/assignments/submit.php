<?php
// Submit Assignment Page
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Assignment;
use SkillMaster\Models\Submission;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

// Only students can access
RoleMiddleware::check('student');

$assignmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$assignmentModel = new Assignment();
$submissionModel = new Submission();

// Get assignment details
$assignment = $assignmentModel->getById($assignmentId);

if (!$assignment) {
    header('Location: pending.php');
    exit;
}

// Check if already submitted
$hasSubmitted = $submissionModel->hasSubmitted($assignmentId, $_SESSION['user_id']);

if ($hasSubmitted) {
    header('Location: submitted.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $submissionText = $_POST['submission_text'] ?? '';
        $file = $_FILES['attachment'] ?? null;
        
        if (empty($submissionText) && (!$file || $file['error'] === UPLOAD_ERR_NO_FILE)) {
            $error = 'Please provide either a submission text or attach a file.';
        } else {
            $result = $submissionModel->submit($assignmentId, $_SESSION['user_id'], $submissionText, $file);
            
            if ($result['success']) {
                $success = 'Assignment submitted successfully!';
                // Redirect after 2 seconds
                header('refresh:2;url=pending.php');
            } else {
                $error = $result['message'];
            }
        }
    }
}

$page_title = 'Submit Assignment - ' . APP_NAME;
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
                <a href="pending.php" class="nav-item nav-link active">Assignments</a>
                <a href="../grades/index.php" class="nav-item nav-link">Grades</a>
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
                    <h1 class="text-white">Submit Assignment</h1>
                    <p class="text-white mb-0"><?php echo htmlspecialchars($assignment['title']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Submission Form Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-light rounded p-5">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-4 p-3 bg-white rounded">
                            <h6 class="mb-2">Assignment Details</h6>
                            <p><strong>Course:</strong> <?php echo htmlspecialchars($assignment['course_title']); ?></p>
                            <p><strong>Due Date:</strong> <?php echo date('F j, Y g:i A', strtotime($assignment['due_date'])); ?></p>
                            <p><strong>Max Points:</strong> <?php echo $assignment['max_points']; ?></p>
                            <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                        </div>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Your Submission</label>
                                <textarea class="form-control" name="submission_text" rows="8" placeholder="Write your submission here..."></textarea>
                                <small class="text-muted">You can write your answer here or upload a file below.</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Attach File (Optional)</label>
                                <input type="file" class="form-control" name="attachment">
                                <small class="text-muted">Allowed formats: PDF, DOC, DOCX, JPG, PNG. Max size: <?php echo MAX_FILE_SIZE / 1024 / 1024; ?>MB</small>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>Note:</strong> Once submitted, you cannot edit your submission. Make sure everything is correct before submitting.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="pending.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Submit Assignment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Submission Form End -->

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