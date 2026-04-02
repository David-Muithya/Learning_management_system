<?php
// Course Details Page
require_once __DIR__ . '/../config/config.php';
use SkillMaster\Models\Course;
use SkillMaster\Models\Enrollment;

$courseModel = new Course();
$enrollmentModel = new Enrollment();

// Get course ID or slug from URL
$identifier = $_GET['id'] ?? $_GET['slug'] ?? null;

if (!$identifier) {
    header('Location: courses.php');
    exit;
}

$course = $courseModel->getCourse($identifier);

if (!$course) {
    header('Location: courses.php');
    exit;
}

// Check if user is enrolled (if logged in)
$isEnrolled = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'student') {
    $isEnrolled = $courseModel->isEnrolled($course['id'], $_SESSION['user_id']);
}

// Get course content (modules and materials)
$modules = $courseModel->getCourseContent($course['id']);

// Get related courses
$relatedCourses = $courseModel->getRelatedCourses($course['id'], $course['category_id']);

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
    
    <!-- Libraries Stylesheet -->
    <link href="assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    
    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Template Stylesheet -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link">Home</a>
                <a href="about.php" class="nav-item nav-link">About</a>
                <a href="courses.php" class="nav-item nav-link">Courses</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="instructors.php" class="dropdown-item">Our Instructors</a>
                        <a href="testimonials.php" class="dropdown-item">Testimonials</a>
                        <a href="apply-instructor.php" class="dropdown-item">Become an Instructor</a>
                    </div>
                </div>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $_SESSION['user_role'] === 'admin' ? '/admin/' : ($_SESSION['user_role'] === 'instructor' ? '/instructor/' : '/student/'); ?>" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
                    <i class="fa fa-user me-2"></i>Dashboard
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
                    Join Now<i class="fa fa-arrow-right ms-3"></i>
                </a>
            <?php endif; ?>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown"><?php echo htmlspecialchars($course['title']); ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="courses.php">Courses</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page"><?php echo htmlspecialchars($course['title']); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Course Details Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Course Main Content -->
                <div class="col-lg-8">
                    <div class="bg-light rounded p-4 mb-4">
                        <div class="position-relative mb-4">
                            <img class="img-fluid rounded w-100" src="<?php echo !empty($course['thumbnail']) ? 'uploads/courses/' . $course['thumbnail'] : 'assets/img/course-1.jpg'; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="height: 400px; object-fit: cover;">
                        </div>
                        
                        <h1 class="mb-3"><?php echo htmlspecialchars($course['title']); ?></h1>
                        <p class="text-muted mb-4">
                            <i class="fa fa-user-tie text-primary me-2"></i>Instructor: <?php echo htmlspecialchars($course['instructor_name'] ?? 'Unknown'); ?>
                            <span class="mx-2">|</span>
                            <i class="fa fa-clock text-primary me-2"></i><?php echo $course['credits']; ?> Credits
                            <span class="mx-2">|</span>
                            <i class="fa fa-tag text-primary me-2"></i><?php echo htmlspecialchars($course['category_name'] ?? 'Uncategorized'); ?>
                        </p>
                        
                        <h4 class="mb-3">Course Description</h4>
                        <p class="mb-4"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                        
                        <h4 class="mb-3">What You'll Learn</h4>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Master core concepts and techniques</p>
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Hands-on projects and exercises</p>
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Real-world applications</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Expert guidance from instructors</p>
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Certificate of completion</p>
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Lifetime access to materials</p>
                            </div>
                        </div>
                        
                        <?php if ($course['syllabus']): ?>
                            <h4 class="mb-3">Course Syllabus</h4>
                            <div class="bg-white rounded p-4 mb-4">
                                <?php echo nl2br(htmlspecialchars($course['syllabus'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($modules)): ?>
                            <h4 class="mb-3">Course Content</h4>
                            <div class="accordion" id="courseContent">
                                <?php foreach ($modules as $index => $module): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>">
                                                <strong>Module <?php echo $index + 1; ?>: <?php echo htmlspecialchars($module['title']); ?></strong>
                                                <span class="ms-2 badge bg-primary"><?php echo $module['material_count']; ?> lessons</span>
                                            </button>
                                        </h2>
                                        <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#courseContent">
                                            <div class="accordion-body">
                                                <p class="mb-3"><?php echo htmlspecialchars($module['description']); ?></p>
                                                <ul class="list-unstyled">
                                                    <?php
                                                    $materials = $courseModel->getMaterialsByModule($module['id']);
                                                    foreach ($materials as $material):
                                                    ?>
                                                        <li class="mb-2">
                                                            <i class="fa fa-<?php echo $material['type'] === 'video' ? 'video' : ($material['type'] === 'document' ? 'file-alt' : 'link'); ?> text-primary me-2"></i>
                                                            <?php echo htmlspecialchars($material['title']); ?>
                                                            <?php if ($material['type'] === 'video'): ?>
                                                                <span class="badge bg-info ms-2">Video</span>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Course Sidebar -->
                <div class="col-lg-4">
                    <div class="bg-primary text-white rounded p-4 mb-4 text-center">
                        <h2 class="text-white mb-3"><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></h2>
                        <p class="mb-3"><i class="fa fa-users me-2"></i><?php echo $course['enrollment_count']; ?> students enrolled</p>
                        <p class="mb-4"><i class="fa fa-clock me-2"></i><?php echo $course['credits']; ?> credit hours</p>
                        
                        <?php if ($isEnrolled): ?>
                            <a href="student/courses/progress.php?id=<?php echo $course['id']; ?>" class="btn btn-light w-100 py-2">
                                <i class="fa fa-play-circle me-2"></i>Continue Course
                            </a>
                        <?php elseif (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'student'): ?>
                            <a href="student/courses/enroll.php?id=<?php echo $course['id']; ?>" class="btn btn-light w-100 py-2">
                                <i class="fa fa-shopping-cart me-2"></i>Enroll Now
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-light w-100 py-2">
                                <i class="fa fa-sign-in-alt me-2"></i>Login to Enroll
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3">Course Includes</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Lifetime access</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Certificate of completion</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Downloadable resources</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Access on mobile and TV</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Support from instructors</li>
                        </ul>
                    </div>
                    
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">About the Instructor</h5>
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?php echo !empty($course['instructor_pic']) ? 'uploads/profiles/' . $course['instructor_pic'] : 'assets/img/team-1.jpg'; ?>" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($course['instructor_name']); ?></h6>
                                <small class="text-muted">Expert Instructor</small>
                            </div>
                        </div>
                        <p class="mb-3"><?php echo htmlspecialchars($course['instructor_bio'] ?? 'Passionate educator with industry experience.'); ?></p>
                        <?php if ($course['facebook_link'] || $course['twitter_link'] || $course['linkedin_link']): ?>
                            <div class="d-flex gap-2">
                                <?php if ($course['facebook_link']): ?>
                                    <a href="<?php echo $course['facebook_link']; ?>" class="btn btn-sm btn-primary" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <?php endif; ?>
                                <?php if ($course['twitter_link']): ?>
                                    <a href="<?php echo $course['twitter_link']; ?>" class="btn btn-sm btn-primary" target="_blank"><i class="fab fa-twitter"></i></a>
                                <?php endif; ?>
                                <?php if ($course['linkedin_link']): ?>
                                    <a href="<?php echo $course['linkedin_link']; ?>" class="btn btn-sm btn-primary" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Related Courses -->
            <?php if (!empty($relatedCourses)): ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <h3 class="mb-4">You May Also Like</h3>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($relatedCourses as $related): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="course-item bg-light h-100">
                                    <div class="position-relative overflow-hidden">
                                        <img class="img-fluid" src="<?php echo !empty($related['thumbnail']) ? 'uploads/courses/' . $related['thumbnail'] : 'assets/img/course-1.jpg'; ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" style="height: 200px; width: 100%; object-fit: cover;">
                                        <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                            <a href="course-details.php?id=<?php echo $related['id']; ?>" class="btn btn-sm btn-primary px-3" style="border-radius: 30px;">View Course</a>
                                        </div>
                                    </div>
                                    <div class="text-center p-4">
                                        <h3 class="mb-0"><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($related['price'], 2); ?></h3>
                                        <h5 class="mt-3 mb-0"><?php echo htmlspecialchars($related['title']); ?></h5>
                                        <div class="d-flex justify-content-center mt-3 pt-2 border-top">
                                            <small><i class="fa fa-user-tie text-primary me-2"></i><?php echo htmlspecialchars($related['instructor_name'] ?? 'Unknown'); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Course Details End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Quick Links</h4>
                    <a class="btn btn-link" href="about.php">About Us</a>
                    <a class="btn btn-link" href="contact.php">Contact Us</a>
                    <a class="btn btn-link" href="privacy.php">Privacy Policy</a>
                    <a class="btn btn-link" href="terms.php">Terms & Condition</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Contact</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Nairobi, Kenya</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+254 712 345 678</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@skillmaster.com</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Gallery</h4>
                    <div class="row g-2 pt-2">
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1 rounded" src="assets/img/course-1.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1 rounded" src="assets/img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1 rounded" src="assets/img/course-3.jpg" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Newsletter</h4>
                    <p>Subscribe to get updates on new courses.</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/lib/wow/wow.min.js"></script>
    <script src="assets/lib/easing/easing.min.js"></script>
    <script src="assets/lib/waypoints/waypoints.min.js"></script>
    <script src="assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>
</body>
</html>