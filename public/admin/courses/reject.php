<?php
// Reject Course with Reason
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Services\EmailService;
use SkillMaster\Services\NotificationService;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('admin');

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$courseModel = new Course();
$emailService = new EmailService();
$notificationService = new NotificationService();

$course = $courseModel->getById($courseId);

if (!$course || $course['status'] !== 'pending_approval') {
    header('Location: pending.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid security token.';
        $messageType = 'danger';
    } else {
        $reason = $_POST['reason'] ?? '';
        
        if (empty($reason)) {
            $message = 'Please provide a reason for rejection.';
            $messageType = 'danger';
        } else {
            $result = $courseModel->reject($courseId, $_SESSION['user_id'], $reason);
            
            if ($result) {
                // Notify instructor
                $notificationService->create(
                    $course['instructor_id'],
                    'Course Update - Action Required',
                    "Your course '{$course['title']}' was not approved. Reason: " . substr($reason, 0, 100),
                    'danger'
                );
                
                // Send email
                $instructor = (new SkillMaster\Models\User())->findById($course['instructor_id']);
                if ($instructor) {
                    $subject = "Course Update: {$course['title']}";
                    $body = "
                        <html>
                        <head><style>body{font-family:Arial;}</style></head>
                        <body>
                            <h2>Course Update</h2>
                            <p>Dear {$instructor['first_name']},</p>
                            <p>Your course <strong>{$course['title']}</strong> was not approved at this time.</p>
                            <p><strong>Reason:</strong> " . nl2br(htmlspecialchars($reason)) . "</p>
                            <p>Please make the necessary changes and resubmit for approval.</p>
                            <p><a href='" . BASE_URL . "/instructor/courses/edit.php?id={$courseId}'>Edit Course</a></p>
                        </body>
                        </html>
                    ";
                    $emailService->send($instructor['email'], $subject, $body);
                }
                
                $message = 'Course rejected successfully.';
                $messageType = 'success';
                header("refresh:2;url=pending.php");
            } else {
                $message = 'Failed to reject course.';
                $messageType = 'danger';
            }
        }
    }
}

$page_title = 'Reject Course - ' . APP_NAME;
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
<body>

    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="pending.php" class="nav-item nav-link active">Pending Courses</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Reject Course</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($course['title']); ?></p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="bg-light rounded p-4 mb-4">
                        <h5>Course Information</h5>
                        <p><strong>Title:</strong> <?php echo htmlspecialchars($course['title']); ?></p>
                        <p><strong>Code:</strong> <?php echo htmlspecialchars($course['code']); ?></p>
                        <p><strong>Instructor:</strong> <?php 
                            $instructor = (new SkillMaster\Models\User())->findById($course['instructor_id']);
                            echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']);
                        ?></p>
                        <p><strong>Price:</strong> <?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></p>
                    </div>
                    
                    <div class="bg-light rounded p-4">
                        <h5>Rejection Reason</h5>
                        <form method="POST">
                            <?php echo Security::csrfField(); ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Reason for Rejection *</label>
                                <textarea class="form-control" name="reason" rows="5" required placeholder="Provide detailed feedback to help the instructor improve the course..."></textarea>
                                <small class="text-muted">This will be sent to the instructor via email.</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                <a href="pending.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
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