<?php
// Enroll in Course with Mock Payment
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\MockPayment;
use SkillMaster\Models\Enrollment;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('student');

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$courseModel = new Course();
$paymentModel = new MockPayment();
$enrollmentModel = new Enrollment();

// Get course details
$course = $courseModel->getCourse($courseId);

if (!$course || $course['status'] !== 'published') {
    header('Location: browse.php');
    exit;
}

// Check if already enrolled
if ($enrollmentModel->isEnrolled($_SESSION['user_id'], $courseId)) {
    header('Location: enrolled.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        // Create mock payment record
        $paymentId = $paymentModel->create($_SESSION['user_id'], $courseId, $course['price']);
        
        if ($paymentId) {
            // Complete the payment (simulate)
            $enrollmentId = $paymentModel->complete($paymentId);
            
            if ($enrollmentId) {
                $success = 'Enrollment initiated! Your enrollment is pending admin verification. You will be notified once approved.';
            } else {
                $error = 'Payment processing failed. Please try again.';
            }
        } else {
            $error = 'Failed to create payment record.';
        }
    }
}

$page_title = 'Enroll in Course - ' . APP_NAME;
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

    <!-- Simple Navbar -->
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
                <a href="enrolled.php" class="nav-item nav-link">My Courses</a>
                <a href="browse.php" class="nav-item nav-link active">Browse Courses</a>
                <a href="../assignments/pending.php" class="nav-item nav-link">Assignments</a>
                <a href="../grades/index.php" class="nav-item nav-link">Grades</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Enroll in Course</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($course['title']); ?></p>
        </div>
    </div>

    <!-- Enrollment Form -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-light rounded p-5">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                            <div class="text-center mt-3">
                                <a href="enrolled.php" class="btn btn-primary">View My Courses</a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bg-white rounded p-4">
                                        <h5>Course Details</h5>
                                        <p><strong>Course:</strong> <?php echo htmlspecialchars($course['title']); ?></p>
                                        <p><strong>Instructor:</strong> <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                                        <p><strong>Credits:</strong> <?php echo $course['credits']; ?></p>
                                        <hr>
                                        <h4 class="text-primary"><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></h4>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST">
                                        <?php echo Security::csrfField(); ?>
                                        <div class="bg-white rounded p-4">
                                            <h5>Mock Payment</h5>
                                            <p class="text-muted">This is a simulated payment for educational purposes.</p>
                                            <div class="mb-3">
                                                <label>Card Number (Demo)</label>
                                                <input type="text" class="form-control" value="4242 4242 4242 4242" disabled>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>Expiry</label>
                                                    <input type="text" class="form-control" value="12/25" disabled>
                                                </div>
                                                <div class="col-6">
                                                    <label>CVV</label>
                                                    <input type="text" class="form-control" value="123" disabled>
                                                </div>
                                            </div>
                                            <hr>
                                            <button type="submit" class="btn btn-primary w-100">Confirm Enrollment</button>
                                            <a href="browse.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
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