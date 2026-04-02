<?php
// Browse Available Courses
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Enrollment;

// Only students can access
RoleMiddleware::check('student');

$courseModel = new Course();
$enrollmentModel = new Enrollment();

$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get courses
$coursesData = $courseModel->getPublishedCourses($category, $search, $page, 9);
$categories = $courseModel->getCategoriesWithCounts();

// Get enrolled course IDs to check enrollment status
$enrolled = $enrollmentModel->getByStudent($_SESSION['user_id'], 'active');
$enrolledCourseIds = array_column($enrolled['enrollments'], 'course_id');

$page_title = 'Browse Courses - ' . APP_NAME;
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
                    <h1 class="text-white">Browse Courses</h1>
                    <p class="text-white mb-0">Discover new skills and expand your knowledge</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Search Bar -->
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <form method="GET" action="" class="d-flex gap-2">
                        <input type="text" class="form-control form-control-lg" name="search" placeholder="Search for courses..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        <button type="submit" class="btn btn-primary btn-lg">Search</button>
                        <?php if ($search || $category): ?>
                            <a href="browse.php" class="btn btn-secondary btn-lg">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Categories -->
            <div class="row g-3 mb-5">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="browse.php" class="btn <?php echo !$category ? 'btn-primary' : 'btn-outline-primary'; ?>">All Courses</a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="?category=<?php echo $cat['slug']; ?>" class="btn <?php echo $category == $cat['slug'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?> (<?php echo $cat['course_count']; ?>)
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Courses Grid -->
            <?php if (empty($coursesData['courses'])): ?>
                <div class="text-center py-5">
                    <i class="fa fa-book-open fa-4x text-muted mb-3"></i>
                    <h4>No courses found</h4>
                    <p class="text-muted">Try a different search term or browse by category.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($coursesData['courses'] as $course): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="course-item bg-light h-100">
                                <div class="position-relative overflow-hidden">
                                    <img class="img-fluid" src="<?php echo !empty($course['thumbnail']) ? '../../uploads/courses/' . $course['thumbnail'] : '../../assets/img/course-1.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($course['title']); ?>" style="height: 200px; width: 100%; object-fit: cover;">
                                    <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                        <?php if (in_array($course['id'], $enrolledCourseIds)): ?>
                                            <a href="details.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-success px-3" style="border-radius: 30px;">
                                                <i class="fa fa-play me-1"></i>Continue Learning
                                            </a>
                                        <?php else: ?>
                                            <a href="details.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary px-3" style="border-radius: 30px;">
                                                View Details
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-center p-4 pb-0">
                                    <h3 class="mb-0"><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></h3>
                                    <div class="mb-3">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <small class="fa fa-star text-primary"></small>
                                        <?php endfor; ?>
                                        <small>(4.8)</small>
                                    </div>
                                    <h5 class="mb-4"><?php echo htmlspecialchars($course['title']); ?></h5>
                                </div>
                                <div class="d-flex border-top">
                                    <small class="flex-fill text-center border-end py-2">
                                        <i class="fa fa-user-tie text-primary me-2"></i><?php echo htmlspecialchars($course['instructor_name'] ?? 'Unknown'); ?>
                                    </small>
                                    <small class="flex-fill text-center border-end py-2">
                                        <i class="fa fa-clock text-primary me-2"></i><?php echo $course['credits']; ?> Credits
                                    </small>
                                    <small class="flex-fill text-center py-2">
                                        <i class="fa fa-user text-primary me-2"></i><?php echo $course['enrollment_count']; ?> Students
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($coursesData['total_pages'] > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($coursesData['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $coursesData['current_page'] - 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $coursesData['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $coursesData['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($coursesData['current_page'] < $coursesData['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $coursesData['current_page'] + 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                        Next
                                    </a>
                                </li>
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