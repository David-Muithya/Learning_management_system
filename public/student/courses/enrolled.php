<?php
// Enrolled Courses Page
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Enrollment;
use SkillMaster\Models\CourseProgress;

// Only students can access
RoleMiddleware::check('student');

$enrollmentModel = new Enrollment();
$progressModel = new CourseProgress();

$studentId = $_SESSION['user_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status = isset($_GET['status']) ? $_GET['status'] : 'active';

// Get enrollments
$enrollmentsData = $enrollmentModel->getByStudent($studentId, $status, $page, 9);

$page_title = 'My Courses - ' . APP_NAME;
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
                <a href="enrolled.php" class="nav-item nav-link active">My Courses</a>
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
                    <h1 class="text-white">My Courses</h1>
                    <p class="text-white mb-0">Continue your learning journey</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Status Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'active' ? 'active' : ''; ?>" href="?status=active">Active Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'completed' ? 'active' : ''; ?>" href="?status=completed">Completed</a>
                </li>
            </ul>
            
            <?php if (empty($enrollmentsData['enrollments'])): ?>
                <div class="text-center py-5">
                    <i class="fa fa-book-open fa-4x text-muted mb-3"></i>
                    <h4>No courses found</h4>
                    <p class="text-muted">You haven't enrolled in any <?php echo $status === 'active' ? 'active' : 'completed'; ?> courses yet.</p>
                    <?php if ($status === 'active'): ?>
                        <a href="../courses/browse.php" class="btn btn-primary mt-3">Browse Courses</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($enrollmentsData['enrollments'] as $enrollment): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="course-item bg-light h-100">
                                <div class="position-relative overflow-hidden">
                                    <img class="img-fluid" src="<?php echo !empty($enrollment['thumbnail']) ? '../../uploads/courses/' . $enrollment['thumbnail'] : '../../assets/img/course-1.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($enrollment['course_title']); ?>" style="height: 200px; width: 100%; object-fit: cover;">
                                    <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                        <a href="details.php?id=<?php echo $enrollment['course_id']; ?>" class="btn btn-sm btn-primary px-3" style="border-radius: 30px;">Continue Learning</a>
                                    </div>
                                </div>
                                <div class="text-center p-4">
                                    <h5 class="mb-2"><?php echo htmlspecialchars($enrollment['course_title']); ?></h5>
                                    <div class="d-flex justify-content-center mb-2">
                                        <small class="text-muted"><i class="fa fa-user-tie text-primary me-1"></i> <?php echo htmlspecialchars($enrollment['instructor_name']); ?></small>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <small class="text-muted"><i class="fa fa-clock text-primary me-1"></i> <?php echo $enrollment['credits']; ?> Credits</small>
                                    </div>
                                    <div class="progress mt-3" style="height: 5px;">
                                        <div class="progress-bar bg-primary" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted mt-2 d-block">0% Complete</small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($enrollmentsData['total_pages'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($enrollmentsData['current_page'] > 1): ?>
                                <li class="page-item"><a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $enrollmentsData['current_page'] - 1; ?>">Previous</a></li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $enrollmentsData['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $enrollmentsData['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($enrollmentsData['current_page'] < $enrollmentsData['total_pages']): ?>
                                <li class="page-item"><a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $enrollmentsData['current_page'] + 1; ?>">Next</a></li>
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