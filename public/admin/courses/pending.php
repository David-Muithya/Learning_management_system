<?php
// Pending Courses Approval
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Helpers\Security;
use SkillMaster\Services\EmailService;
use SkillMaster\Services\NotificationService;

// Only admin can access
RoleMiddleware::check('admin');

$courseModel = new Course();
$emailService = new EmailService();
$notificationService = new NotificationService();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$message = '';
$messageType = '';

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid security token.';
        $messageType = 'danger';
    } else {
        $action = $_POST['action'] ?? '';
        $courseId = (int)$_POST['course_id'] ?? 0;
        $reason = $_POST['reason'] ?? '';
        
        $course = $courseModel->getById($courseId);
        
        if ($action === 'approve') {
            $result = $courseModel->approve($courseId, $_SESSION['user_id']);
            
            if ($result) {
                $message = 'Course approved successfully!';
                $messageType = 'success';
                
                // Notify instructor
                $notificationService->create(
                    $course['instructor_id'],
                    'Course Approved',
                    "Your course '{$course['title']}' has been approved and is now published.",
                    'success'
                );
                
                // Send email to instructor
                $instructor = (new SkillMaster\Models\User())->findById($course['instructor_id']);
                if ($instructor) {
                    $subject = "Course Approved - {$course['title']}";
                    $body = "
                        <html>
                        <head><style>body{font-family:Arial;}</style></head>
                        <body>
                            <h2>Congratulations!</h2>
                            <p>Your course <strong>{$course['title']}</strong> has been approved and is now live on " . APP_NAME . ".</p>
                            <p>Students can now enroll in your course.</p>
                            <p><a href='" . BASE_URL . "/instructor/courses/manage.php?id={$courseId}'>Manage Your Course</a></p>
                        </body>
                        </html>
                    ";
                    $emailService->send($instructor['email'], $subject, $body);
                }
            } else {
                $message = 'Failed to approve course.';
                $messageType = 'danger';
            }
        } elseif ($action === 'reject') {
            $result = $courseModel->reject($courseId, $_SESSION['user_id'], $reason);
            
            if ($result) {
                $message = 'Course rejected successfully.';
                $messageType = 'warning';
                
                // Notify instructor
                $notificationService->create(
                    $course['instructor_id'],
                    'Course Update',
                    "Your course '{$course['title']}' was not approved. Reason: " . substr($reason, 0, 100),
                    'danger'
                );
                
                // Send email to instructor
                $instructor = (new SkillMaster\Models\User())->findById($course['instructor_id']);
                if ($instructor) {
                    $subject = "Course Update - {$course['title']}";
                    $body = "
                        <html>
                        <head><style>body{font-family:Arial;}</style></head>
                        <body>
                            <h2>Course Update</h2>
                            <p>Your course <strong>{$course['title']}</strong> was not approved at this time.</p>
                            <p><strong>Reason:</strong> " . nl2br(htmlspecialchars($reason)) . "</p>
                            <p>You can edit your course and resubmit for approval.</p>
                            <p><a href='" . BASE_URL . "/instructor/courses/edit.php?id={$courseId}'>Edit Your Course</a></p>
                        </body>
                        </html>
                    ";
                    $emailService->send($instructor['email'], $subject, $body);
                }
            } else {
                $message = 'Failed to reject course.';
                $messageType = 'danger';
            }
        }
        
        // Refresh data
        $coursesData = $courseModel->getPendingCourses($page, 10);
    } else {
        $coursesData = $courseModel->getPendingCourses($page, 10);
    }
} else {
    $coursesData = $courseModel->getPendingCourses($page, 10);
}

$page_title = 'Pending Courses - ' . APP_NAME;
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
                <a href="../instructors/applications.php" class="nav-item nav-link">Applications</a>
                <a href="pending.php" class="nav-item nav-link active">Pending Courses</a>
                <a href="../enrollments/pending.php" class="nav-item nav-link">Enrollments</a>
                <a href="../settings/index.php" class="nav-item nav-link">Settings</a>
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
                    <h1 class="text-white">Pending Courses</h1>
                    <p class="text-white mb-0">Review and approve courses submitted by instructors</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <i class="fa fa-<?php echo $messageType === 'success' ? 'check-circle' : ($messageType === 'danger' ? 'exclamation-circle' : 'info-circle'); ?> me-2"></i>
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (empty($coursesData['courses'])): ?>
                <div class="text-center py-5">
                    <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
                    <h4>No Pending Courses</h4>
                    <p class="text-muted">All courses have been reviewed. Check back later for new submissions.</p>
                    <a href="../index.php" class="btn btn-primary mt-3">Back to Dashboard</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($coursesData['courses'] as $course): ?>
                        <div class="col-lg-12 mb-4">
                            <div class="bg-light rounded p-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="<?php echo !empty($course['thumbnail']) ? '../../uploads/courses/' . $course['thumbnail'] : '../../assets/img/course-1.jpg'; ?>" 
                                             class="img-fluid rounded" alt="<?php echo htmlspecialchars($course['title']); ?>" style="height: 150px; width: 100%; object-fit: cover;">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h4 class="mb-1"><?php echo htmlspecialchars($course['title']); ?></h4>
                                                <p class="text-muted mb-2">
                                                    <i class="fa fa-code me-1"></i> <?php echo htmlspecialchars($course['code']); ?> |
                                                    <i class="fa fa-user-tie me-1"></i> <?php echo htmlspecialchars($course['instructor_name'] ?? 'Unknown'); ?> |
                                                    <i class="fa fa-tag me-1"></i> <?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?>
                                                </p>
                                                <p class="mb-2"><?php echo htmlspecialchars(substr($course['short_description'] ?? $course['description'], 0, 200)); ?>...</p>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <small class="text-muted"><i class="fa fa-clock me-1"></i> Credits: <?php echo $course['credits']; ?></small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted"><i class="fa fa-users me-1"></i> Max Students: <?php echo $course['max_students']; ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-warning mb-2">Pending Approval</span>
                                                <div class="mt-2">
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $course['id']; ?>">
                                                        <i class="fa fa-eye"></i> View Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 d-flex gap-2">
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $course['id']; ?>">
                                                <i class="fa fa-check me-1"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $course['id']; ?>">
                                                <i class="fa fa-times me-1"></i> Reject
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- View Details Modal -->
                        <div class="modal fade" id="viewModal<?php echo $course['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Course Code:</strong> <?php echo htmlspecialchars($course['code']); ?></p>
                                        <p><strong>Instructor:</strong> <?php echo htmlspecialchars($course['instructor_name']); ?> (<?php echo htmlspecialchars($course['instructor_email'] ?? ''); ?>)</p>
                                        <p><strong>Price:</strong> <?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></p>
                                        <p><strong>Credits:</strong> <?php echo $course['credits']; ?></p>
                                        <p><strong>Max Students:</strong> <?php echo $course['max_students']; ?></p>
                                        <hr>
                                        <h6>Description:</h6>
                                        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                                        <?php if ($course['syllabus']): ?>
                                            <h6>Syllabus:</h6>
                                            <p><?php echo nl2br(htmlspecialchars($course['syllabus'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal<?php echo $course['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <?php echo Security::csrfField(); ?>
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approve Course</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Approve <strong><?php echo htmlspecialchars($course['title']); ?></strong> for publication?</p>
                                            <p class="text-success">The instructor will be notified and the course will be available to students.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal<?php echo $course['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <?php echo Security::csrfField(); ?>
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Course</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Reject <strong><?php echo htmlspecialchars($course['title']); ?></strong>?</p>
                                            <div class="mb-3">
                                                <label class="form-label">Reason for Rejection</label>
                                                <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                <small class="text-muted">This will be sent to the instructor.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($coursesData['total_pages'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($coursesData['current_page'] > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $coursesData['current_page'] - 1; ?>">Previous</a></li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $coursesData['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $coursesData['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($coursesData['current_page'] < $coursesData['total_pages']): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $coursesData['current_page'] + 1; ?>">Next</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
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