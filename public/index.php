<?php
// Landing Page - Homepage
require_once __DIR__ . '/../config/config.php';
use SkillMaster\Models\User;
use SkillMaster\Models\Course;

$userModel = new User();
$courseModel = new Course();

// Get statistics
$stats = $userModel->getStats();

// Get featured courses (published, limit 3)
$featuredCourses = $courseModel->getFeaturedCourses(3);

// Get course categories with counts
$categories = $courseModel->getCategoriesWithCounts();

// Get active instructors (limit 4)
$instructors = $userModel->getActiveInstructors(4);

// Get testimonials (if you have a testimonials table)
// For now, we'll use static testimonials

$page_title = APP_NAME . ' - Best Online Learning Platform';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="SkillMaster LMS - Learn from expert instructors, access quality courses, and advance your career.">
    <meta name="keywords" content="online learning, courses, education, skillmaster, e-learning">

    <!-- Favicon -->
    <link href="assets/img/favicon.ico" rel="icon">

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
            <a href="index.php" class="nav-item nav-link active">Home</a>
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
        <!-- Join Now button - Always visible, links to login page -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo $_SESSION['user_role'] === 'admin' ? '/admin/' : ($_SESSION['user_role'] === 'instructor' ? '/instructor/' : '/student/'); ?>" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
                <i class="fa fa-user me-2"></i>Dashboard
            </a>
        <?php else: ?>
            <div class="dropdown d-none d-lg-block">
                <button class="btn btn-primary py-4 px-lg-5 dropdown-toggle" type="button" id="joinDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Join Now<i class="fa fa-arrow-right ms-3"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="joinDropdown">
                    <li><a class="dropdown-item" href="login.php?role=student">Login as Student</a></li>
                    <li><a class="dropdown-item" href="login.php?role=instructor">Login as Instructor</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="register.php">Register as Student</a></li>
                    <li><a class="dropdown-item" href="apply-instructor.php">Apply as Instructor</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>
<!-- Navbar End -->

    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-5">
        <div class="owl-carousel header-carousel position-relative">
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="assets/img/carousel-1.jpg" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">Best Online Courses</h5>
                                <h1 class="display-3 text-white animated slideInDown">The Best Online Learning Platform</h1>
                                <p class="fs-5 text-white mb-4 pb-2">Empower yourself with expert-led courses in programming, data science, web development, and more. Start your learning journey today!</p>
                                <a href="courses.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Explore Courses</a>
                                <a href="register.php" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Join Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="assets/img/carousel-2.jpg" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">Expert Instructors</h5>
                                <h1 class="display-3 text-white animated slideInDown">Learn From Industry Professionals</h1>
                                <p class="fs-5 text-white mb-4 pb-2">Our instructors bring real-world experience to the classroom. Get practical knowledge that prepares you for the job market.</p>
                                <a href="instructors.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Meet Instructors</a>
                                <a href="register.php" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Get Started</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Features/Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-graduation-cap text-primary mb-4"></i>
                            <h5 class="mb-3">Skilled Instructors</h5>
                            <p>Learn from industry experts with years of practical experience in their fields.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-globe text-primary mb-4"></i>
                            <h5 class="mb-3">Online Classes</h5>
                            <p>Access your courses anywhere, anytime. Learn at your own pace from home.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-project-diagram text-primary mb-4"></i>
                            <h5 class="mb-3">Hands-on Projects</h5>
                            <p>Build real-world projects that showcase your skills to potential employers.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-certificate text-primary mb-4"></i>
                            <h5 class="mb-3">Certification</h5>
                            <p>Earn recognized certificates upon course completion to boost your career.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features End -->

    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100" src="assets/img/about.jpg" alt="" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start text-primary pe-3">About Us</h6>
                    <h1 class="mb-4">Welcome to <?php echo APP_NAME; ?></h1>
                    <p class="mb-4">SkillMaster is a premier online learning platform dedicated to empowering individuals with the skills they need to succeed in today's digital economy. Our mission is to make quality education accessible to everyone, everywhere.</p>
                    <p class="mb-4">With expert instructors, hands-on projects, and a supportive community, we've helped thousands of students advance their careers and achieve their goals.</p>
                    <div class="row gy-2 gx-4 mb-4">
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Skilled Instructors</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Online Classes</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>International Certificate</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Hands-on Projects</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Flexible Learning</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Career Support</p>
                        </div>
                    </div>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="about.php">Read More</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Categories Start -->
    <div class="container-xxl py-5 category">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Categories</h6>
                <h1 class="mb-5">Courses Categories</h1>
            </div>
            <div class="row g-3">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $index => $cat): ?>
                        <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.<?php echo ($index + 1) * 2; ?>s">
                            <a class="position-relative d-block overflow-hidden" href="courses.php?category=<?php echo $cat['slug']; ?>">
                                <img class="img-fluid" src="assets/img/cat-<?php echo ($index % 4) + 1; ?>.jpg" alt="<?php echo htmlspecialchars($cat['name']); ?>" style="height: 200px; width: 100%; object-fit: cover;">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3" style="margin: 1px;">
                                    <h5 class="m-0"><?php echo htmlspecialchars($cat['name']); ?></h5>
                                    <small class="text-primary"><?php echo $cat['course_count']; ?> Courses</small>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No categories available yet. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Categories End -->

    <!-- Popular Courses Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Courses</h6>
                <h1 class="mb-5">Popular Courses</h1>
            </div>
            <div class="row g-4 justify-content-center">
                <?php if (!empty($featuredCourses)): ?>
                    <?php foreach ($featuredCourses as $course): ?>
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="course-item bg-light">
                                <div class="position-relative overflow-hidden">
                                    <img class="img-fluid" src="<?php echo !empty($course['thumbnail']) ? 'uploads/courses/' . $course['thumbnail'] : 'assets/img/course-1.jpg'; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="height: 200px; width: 100%; object-fit: cover;">
                                    <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                        <a href="course-details.php?id=<?php echo $course['id']; ?>" class="flex-shrink-0 btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">Read More</a>
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'student'): ?>
                                            <a href="student/courses/enroll.php?id=<?php echo $course['id']; ?>" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Enroll Now</a>
                                        <?php else: ?>
                                            <a href="login.php" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Enroll Now</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-center p-4 pb-0">
                                    <h3 class="mb-0"><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></h3>
                                    <div class="mb-3">
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small>(4.8)</small>
                                    </div>
                                    <h5 class="mb-4"><?php echo htmlspecialchars($course['title']); ?></h5>
                                </div>
                                <div class="d-flex border-top">
                                    <small class="flex-fill text-center border-end py-2"><i class="fa fa-user-tie text-primary me-2"></i><?php echo htmlspecialchars($course['instructor_name'] ?? 'Unknown'); ?></small>
                                    <small class="flex-fill text-center border-end py-2"><i class="fa fa-clock text-primary me-2"></i><?php echo $course['credits']; ?> Credits</small>
                                    <small class="flex-fill text-center py-2"><i class="fa fa-user text-primary me-2"></i><?php echo $course['enrollment_count']; ?> Students</small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No courses available yet. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="courses.php" class="btn btn-primary py-3 px-5">View All Courses</a>
            </div>
        </div>
    </div>
    <!-- Popular Courses End -->

    <!-- Statistics Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="bg-light rounded p-4">
                        <i class="fa fa-users fa-3x text-primary mb-3"></i>
                        <h1 class="display-4"><?php echo number_format($stats['total_students'] ?? 0); ?></h1>
                        <p class="mb-0">Happy Students</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="bg-light rounded p-4">
                        <i class="fa fa-book fa-3x text-primary mb-3"></i>
                        <h1 class="display-4"><?php echo number_format($stats['total_courses'] ?? 0); ?></h1>
                        <p class="mb-0">Online Courses</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="bg-light rounded p-4">
                        <i class="fa fa-chalkboard-user fa-3x text-primary mb-3"></i>
                        <h1 class="display-4"><?php echo number_format($stats['total_instructors'] ?? 0); ?></h1>
                        <p class="mb-0">Expert Instructors</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="bg-light rounded p-4">
                        <i class="fa fa-clock fa-3x text-primary mb-3"></i>
                        <h1 class="display-4">24/7</h1>
                        <p class="mb-0">Online Support</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Statistics End -->

    <!-- Instructors Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Instructors</h6>
                <h1 class="mb-5">Expert Instructors</h1>
            </div>
            <div class="row g-4">
                <?php if (!empty($instructors)): ?>
                    <?php foreach ($instructors as $instructor): ?>
                        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="team-item bg-light">
                                <div class="overflow-hidden">
                                    <img class="img-fluid" src="<?php echo !empty($instructor['profile_pic']) ? 'uploads/profiles/' . $instructor['profile_pic'] : 'assets/img/team-1.jpg'; ?>" alt="<?php echo htmlspecialchars($instructor['first_name']); ?>" style="height: 250px; width: 100%; object-fit: cover;">
                                </div>
                                <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                                    <div class="bg-light d-flex justify-content-center pt-2 px-1">
                                        <?php if (!empty($instructor['facebook_link'])): ?>
                                            <a class="btn btn-sm-square btn-primary mx-1" href="<?php echo $instructor['facebook_link']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($instructor['twitter_link'])): ?>
                                            <a class="btn btn-sm-square btn-primary mx-1" href="<?php echo $instructor['twitter_link']; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($instructor['linkedin_link'])): ?>
                                            <a class="btn btn-sm-square btn-primary mx-1" href="<?php echo $instructor['linkedin_link']; ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-center p-4">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']); ?></h5>
                                    <small><?php echo htmlspecialchars($instructor['bio'] ?? 'Expert Instructor'); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No instructors available yet. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="instructors.php" class="btn btn-primary py-3 px-5">Meet All Instructors</a>
            </div>
        </div>
    </div>
    <!-- Instructors End -->

    <!-- Testimonial Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="text-center">
                <h6 class="section-title bg-white text-center text-primary px-3">Testimonial</h6>
                <h1 class="mb-5">Our Students Say!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel position-relative">
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="assets/img/testimonial-1.jpg" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Alice Wanjiku</h5>
                    <p>Web Developer</p>
                    <div class="testimonial-text bg-light text-center p-4">
                        <p class="mb-0">"SkillMaster transformed my career! The web development course was comprehensive and the instructors were incredibly supportive. I landed my dream job within 3 months of completing the course."</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="assets/img/testimonial-2.jpg" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Brian Kamau</h5>
                    <p>Data Analyst</p>
                    <div class="testimonial-text bg-light text-center p-4">
                        <p class="mb-0">"The data science program is top-notch! The hands-on projects gave me real-world experience. I now work as a data analyst at a leading tech company."</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="assets/img/testimonial-3.jpg" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Carol Muthoni</h5>
                    <p>Full Stack Developer</p>
                    <div class="testimonial-text bg-light text-center p-4">
                        <p class="mb-0">"I love the flexibility of learning at my own pace. The PHP and MySQL course was excellent! The community support is amazing and I've made great connections."</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="assets/img/testimonial-4.jpg" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">David Omondi</h5>
                    <p>Mobile App Developer</p>
                    <div class="testimonial-text bg-light text-center p-4">
                        <p class="mb-0">"The React Native course helped me build my first mobile app! The instructors are knowledgeable and always ready to help. Highly recommend SkillMaster!"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->

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
                    <a class="btn btn-link" href="faq.php">FAQs & Help</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Contact</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Nairobi, Kenya</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+254 712 345 678</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@skillmaster.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Gallery</h4>
                    <div class="row g-2 pt-2">
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="assets/img/course-1.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="assets/img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="assets/img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="assets/img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="assets/img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="assets/img/course-1.jpg" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Newsletter</h4>
                    <p>Subscribe to get updates on new courses and special offers.</p>
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
                        &copy; <?php echo date('Y'); ?> <a class="border-bottom" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-menu">
                            <a href="index.php">Home</a>
                            <a href="privacy.php">Cookies</a>
                            <a href="faq.php">Help</a>
                            <a href="faq.php">FQAs</a>
                        </div>
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