<?php
// View Assignment Details
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Assignment;
use SkillMaster\Models\Submission;

RoleMiddleware::check('instructor');

$assignmentModel = new Assignment();
$submissionModel = new Submission();

$assignmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get assignment details
$assignment = $assignmentModel->getAssignmentWithStats($assignmentId, $_SESSION['user_id']);

if (!$assignment) {
    header('Location: list.php');
    exit;
}

$page_title = 'Assignment Details - ' . $assignment['title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
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
                <a href="list.php" class="nav-item nav-link">Assignments</a>
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
                    <h1 class="text-white"><?php echo htmlspecialchars($assignment['title']); ?></h1>
                    <p class="text-white mb-0"><?php echo htmlspecialchars($assignment['course_title']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3">Assignment Details</h5>
                        <div class="mb-3">
                            <strong>Due Date:</strong> <?php echo date('F j, Y g:i A', strtotime($assignment['due_date'])); ?>
                            <?php if (strtotime($assignment['due_date']) < time()): ?>
                                <span class="badge bg-danger ms-2">Past Due</span>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Maximum Points:</strong> <?php echo $assignment['max_points']; ?>
                        </div>
                        <?php if ($assignment['description']): ?>
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <div class="bg-white rounded p-3 mt-2">
                                    <?php echo nl2br(htmlspecialchars($assignment['description'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3">Statistics</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Submissions:</span>
                            <strong><?php echo $assignment['total_submissions']; ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Graded:</span>
                            <strong><?php echo $assignment['graded_count']; ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pending Grading:</span>
                            <strong><?php echo $assignment['total_submissions'] - $assignment['graded_count']; ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Average Grade:</span>
                            <strong><?php echo $assignment['avg_grade'] ? round($assignment['avg_grade'], 1) . '/' . $assignment['max_points'] : 'N/A'; ?></strong>
                        </div>
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="grade.php?id=<?php echo $assignment['id']; ?>" class="btn btn-primary">
                                <i class="fa fa-check-circle me-2"></i>Grade Submissions
                            </a>
                            <a href="edit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-outline-primary">
                                <i class="fa fa-edit me-2"></i>Edit Assignment
                            </a>
                        </div>
                    </div>
                </div>
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