<?php
// Mock Payment Page
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\MockPayment;
use SkillMaster\Models\Enrollment;
use SkillMaster\Helpers\Security;

// Only students can access
RoleMiddleware::check('student');

$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$courseModel = new Course();
$paymentModel = new MockPayment();
$enrollmentModel = new Enrollment();

// Get course details
$course = $courseModel->getCourse($courseId);

if (!$course || $course['status'] !== 'published') {
    header('Location: ../courses/browse.php');
    exit;
}

// Check if already enrolled
if ($enrollmentModel->isEnrolled($_SESSION['user_id'], $courseId)) {
    header('Location: ../courses/enrolled.php');
    exit;
}

$error = '';
$success = '';
$paymentId = null;

// Process mock payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        // Create mock payment record
        $paymentId = $paymentModel->create($_SESSION['user_id'], $courseId, $course['price']);
        
        if ($paymentId) {
            // Complete the payment (simulate payment)
            $enrollmentId = $paymentModel->complete($paymentId);
            
            if ($enrollmentId) {
                $success = 'Payment successful! Your enrollment is pending admin verification. You will be notified once approved.';
            } else {
                $error = 'Payment processing failed. Please try again.';
            }
        } else {
            $error = 'Failed to create payment record. Please try again.';
        }
    }
}

$page_title = 'Mock Payment - ' . APP_NAME;
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
                    <h1 class="text-white">Complete Enrollment</h1>
                    <p class="text-white mb-0">Mock payment - Educational purposes only</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Payment Form Start -->
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
                            <div class="text-center mt-4">
                                <a href="../courses/enrolled.php" class="btn btn-primary">View My Courses</a>
                                <a href="../courses/browse.php" class="btn btn-secondary">Browse More Courses</a>
                            </div>
                        <?php else: ?>
                        
                        <div class="row">
                            <!-- Course Details -->
                            <div class="col-md-6">
                                <div class="bg-white rounded p-4 mb-4">
                                    <h5 class="mb-3">Course Details</h5>
                                    <img src="<?php echo !empty($course['thumbnail']) ? '../../uploads/courses/' . $course['thumbnail'] : '../../assets/img/course-1.jpg'; ?>" 
                                         class="img-fluid rounded mb-3" alt="<?php echo htmlspecialchars($course['title']); ?>" style="height: 150px; width: 100%; object-fit: cover;">
                                    <h6><?php echo htmlspecialchars($course['title']); ?></h6>
                                    <p class="small text-muted"><?php echo htmlspecialchars($course['instructor_name']); ?></p>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span>Course Price:</span>
                                        <strong><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></strong>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Form -->
                            <div class="col-md-6">
                                <div class="bg-white rounded p-4">
                                    <h5 class="mb-3">Mock Payment Details</h5>
                                    <p class="text-muted small">This is a simulated payment for educational purposes. No real money will be charged.</p>
                                    
                                    <form method="POST" action="">
                                        <?php echo Security::csrfField(); ?>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Card Number (Demo)</label>
                                            <input type="text" class="form-control" value="4242 4242 4242 4242" disabled>
                                            <small class="text-muted">Demo card number</small>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Expiry Date</label>
                                                <input type="text" class="form-control" value="12/25" disabled>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">CVV</label>
                                                <input type="text" class="form-control" value="123" disabled>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Cardholder Name</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" disabled>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="d-flex justify-content-between mb-3">
                                            <strong>Total Amount:</strong>
                                            <strong class="text-primary"><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></strong>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle me-2"></i>
                                            <strong>Demo Payment:</strong> Click "Pay Now" to simulate a successful payment. Your enrollment will be pending admin verification.
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="../courses/details.php?id=<?php echo $course['id']; ?>" class="btn btn-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary flex-grow-1">
                                                <i class="fa fa-credit-card me-2"></i>Pay <?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Form End -->

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