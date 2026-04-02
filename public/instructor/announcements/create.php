<?php
// Create Announcement
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Announcement;
use SkillMaster\Services\NotificationService;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

RoleMiddleware::check('instructor');

$courseModel = new Course();
$announcementModel = new Announcement();
$notificationService = new NotificationService();

$instructorId = $_SESSION['user_id'];

// Get instructor's courses for dropdown
$courses = $courseModel->getByInstructor($instructorId);

$selectedCourseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $courseId = (int)$_POST['course_id'];
        $title = Validation::sanitize($_POST['title'] ?? '');
        $content = Validation::sanitize($_POST['content'] ?? '');
        $priority = $_POST['priority'] ?? 'normal';
        
        // Validate
        if (empty($title) || empty($content)) {
            $error = 'Please fill in all required fields.';
        } else {
            // Verify course belongs to instructor
            $courseExists = false;
            foreach ($courses as $course) {
                if ($course['id'] == $courseId) {
                    $courseExists = true;
                    break;
                }
            }
            
            if (!$courseExists) {
                $error = 'Invalid course selected.';
            } else {
                $result = $announcementModel->create($courseId, $instructorId, $title, $content, $priority);
                
                if ($result) {
                    $success = 'Announcement posted successfully!';
                    
                    // Notify students enrolled in this course
                    $notificationService->createForCourseStudents(
                        $courseId,
                        'New Announcement: ' . $title,
                        substr($content, 0, 150) . '...',
                        'info'
                    );
                    
                    // Clear form or redirect
                    echo '<script>setTimeout(function(){ window.location.href = "list.php?course_id=' . $courseId . '"; }, 1500);</script>';
                } else {
                    $error = 'Failed to post announcement. Please try again.';
                }
            }
        }
    }
}

$page_title = 'Post Announcement - ' . APP_NAME;
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
                <a href="../courses/my-courses.php" class="nav-item nav-link">My Courses</a>
                <a href="../assignments/list.php" class="nav-item nav-link">Assignments</a>
                <a href="../students/enrolled.php" class="nav-item nav-link">Students</a>
                <a href="list.php" class="nav-item nav-link active">Announcements</a>
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
                    <h1 class="text-white">Post Announcement</h1>
                    <p class="text-white mb-0">Keep your students informed with important updates</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Form Start -->
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
                        
                        <form method="POST" action="">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Course *</label>
                                <select class="form-select" name="course_id" required>
                                    <option value="">-- Select Course --</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>" <?php echo $selectedCourseId == $course['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['title']); ?> (<?php echo $course['enrollment_count']; ?> students)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($courses)): ?>
                                    <small class="text-danger">You need to create a course first. <a href="../courses/create.php">Create a course</a></small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Priority Level</label>
                                <select class="form-select" name="priority">
                                    <option value="low">Low - General Information</option>
                                    <option value="normal" selected>Normal - Important Update</option>
                                    <option value="high">High - Urgent Attention</option>
                                    <option value="urgent">Urgent - Critical Information</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Announcement Title *</label>
                                <input type="text" class="form-control" name="title" required>
                                <small class="text-muted">Be clear and descriptive</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Announcement Content *</label>
                                <textarea class="form-control" name="content" rows="8" required placeholder="Write your announcement here..."></textarea>
                                <small class="text-muted">Students will be notified of this announcement</small>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>Note:</strong> Students enrolled in this course will receive a notification about this announcement.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="list.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Post Announcement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Form End -->

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