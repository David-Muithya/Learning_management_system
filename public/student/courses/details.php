<?php
// Course Details Page (Student View)
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Enrollment;

// Only students can access
RoleMiddleware::check('student');

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$courseModel = new Course();
$enrollmentModel = new Enrollment();

$course = $courseModel->getCourse($courseId);

if (!$course || $course['status'] !== 'published') {
    header('Location: browse.php');
    exit;
}

$isEnrolled = $enrollmentModel->isEnrolled($_SESSION['user_id'], $courseId);
$modules = $courseModel->getCourseContent($courseId);

$page_title = $course['title'] . ' - ' . APP_NAME;
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
                <a href="enrolled.php" class="nav-item nav-link">My Courses</a>
                <a href="../assignments/pending.php" class="nav-item nav-link">Assignments</a>
                <a href="../grades/index.php" class="nav-item nav-link">Grades</a>
                <a href="browse.php" class="nav-item nav-link active">Browse Courses</a>
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
                    <h1 class="text-white"><?php echo htmlspecialchars($course['title']); ?></h1>
                    <p class="text-white mb-0"><?php echo htmlspecialchars($course['short_description'] ?? $course['description']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Course Details Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row g-5">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="bg-light rounded p-4">
                        <img src="<?php echo !empty($course['thumbnail']) ? '../../uploads/courses/' . $course['thumbnail'] : '../../assets/img/course-1.jpg'; ?>" 
                             class="img-fluid rounded mb-4 w-100" alt="<?php echo htmlspecialchars($course['title']); ?>" style="height: 300px; object-fit: cover;">
                        
                        <h4>Course Description</h4>
                        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                        
                        <?php if ($course['syllabus']): ?>
                            <h4 class="mt-4">Syllabus</h4>
                            <div class="bg-white rounded p-3">
                                <?php echo nl2br(htmlspecialchars($course['syllabus'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($modules)): ?>
                            <h4 class="mt-4">Course Content</h4>
                            <div class="accordion" id="courseContent">
                                <?php foreach ($modules as $index => $module): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>">
                                                <strong>Module <?php echo $index + 1; ?>: <?php echo htmlspecialchars($module['title']); ?></strong>
                                            </button>
                                        </h2>
                                        <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#courseContent">
                                            <div class="accordion-body">
                                                <p><?php echo htmlspecialchars($module['description']); ?></p>
                                                <?php
                                                $materials = $courseModel->getMaterialsByModule($module['id']);
                                                if (!empty($materials)):
                                                ?>
                                                    <ul class="list-unstyled">
                                                        <?php foreach ($materials as $material): ?>
                                                            <li class="mb-2">
                                                                <i class="fa fa-<?php echo $material['type'] === 'video' ? 'video' : ($material['type'] === 'document' ? 'file-alt' : 'link'); ?> text-primary me-2"></i>
                                                                <?php echo htmlspecialchars($material['title']); ?>
                                                                <?php if ($isEnrolled && $material['file_path']): ?>
                                                                    <a href="../../<?php echo $material['file_path']; ?>" class="btn btn-sm btn-outline-primary ms-2" target="_blank">View</a>
                                                                <?php elseif (!$isEnrolled): ?>
                                                                    <span class="badge bg-secondary ms-2">Locked</span>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="bg-primary text-white rounded p-4 mb-4 text-center">
                        <h2 class="text-white mb-3"><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></h2>
                        <p><i class="fa fa-users me-2"></i><?php echo $course['enrollment_count']; ?> students enrolled</p>
                        <p><i class="fa fa-clock me-2"></i><?php echo $course['credits']; ?> credits</p>
                        
                        <?php if ($isEnrolled): ?>
                            <a href="enrolled.php" class="btn btn-light w-100 py-2">
                                <i class="fa fa-play-circle me-2"></i>Continue Learning
                            </a>
                        <?php else: ?>
                            <a href="../payments/mock.php?course_id=<?php echo $course['id']; ?>" class="btn btn-light w-100 py-2">
                                <i class="fa fa-shopping-cart me-2"></i>Enroll Now
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-light rounded p-4 mb-4">
                        <h5>Course Includes</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Lifetime access</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Certificate of completion</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Downloadable resources</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Access on mobile and TV</li>
                        </ul>
                    </div>
                    
                    <div class="bg-light rounded p-4">
                        <h5>About the Instructor</h5>
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?php echo !empty($course['instructor_pic']) ? '../../uploads/profiles/' . $course['instructor_pic'] : '../../assets/img/team-1.jpg'; ?>" 
                                 class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($course['instructor_name']); ?></h6>
                                <small class="text-muted">Expert Instructor</small>
                            </div>
                        </div>
                        <p class="small"><?php echo htmlspecialchars($course['instructor_bio'] ?? 'Passionate educator with industry experience.'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Course Details End -->

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