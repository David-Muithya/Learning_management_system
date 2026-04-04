<?php
// Testimonials Page
require_once __DIR__ . '/../config/config.php';

// For now, we'll use static testimonials from the database
// You can add a testimonials table later for dynamic content

$testimonials = [
    [
        'name' => 'Alice Wanjiku',
        'profession' => 'Web Developer',
        'image' => 'testimonial-1.jpg',
        'message' => 'SkillMaster transformed my career! The web development course was comprehensive and the instructors were incredibly supportive. I landed my dream job within 3 months of completing the course.',
        'rating' => 5
    ],
    [
        'name' => 'Brian Kamau',
        'profession' => 'Data Analyst',
        'image' => 'testimonial-2.jpg',
        'message' => 'The data science program is top-notch! The hands-on projects gave me real-world experience. I now work as a data analyst at a leading tech company.',
        'rating' => 5
    ],
    [
        'name' => 'Carol Muthoni',
        'profession' => 'Full Stack Developer',
        'image' => 'testimonial-3.jpg',
        'message' => 'I love the flexibility of learning at my own pace. The PHP and MySQL course was excellent! The community support is amazing and I\'ve made great connections.',
        'rating' => 5
    ],
    [
        'name' => 'David Omondi',
        'profession' => 'Mobile App Developer',
        'image' => 'testimonial-4.jpg',
        'message' => 'The React Native course helped me build my first mobile app! The instructors are knowledgeable and always ready to help. Highly recommend SkillMaster!',
        'rating' => 5
    ],
    [
        'name' => 'Esther Nyokabi',
        'profession' => 'UI/UX Designer',
        'image' => 'testimonial-1.jpg',
        'message' => 'The design courses at SkillMaster are outstanding. I learned modern design principles and tools that helped me advance my career.',
        'rating' => 5
    ],
    [
        'name' => 'Francis Mwangi',
        'profession' => 'Cybersecurity Analyst',
        'image' => 'testimonial-2.jpg',
        'message' => 'The cybersecurity course gave me practical skills that I use daily at work. The labs and real-world scenarios were very helpful.',
        'rating' => 5
    ]
];

$page_title = 'Testimonials - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="Read what our students say about SkillMaster LMS">
    <meta name="keywords" content="testimonials, student reviews, success stories">

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
    
    <style>
        .testimonial-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(6, 187, 204, 0.15);
        }
        .rating-star {
            color: #ffc107;
            font-size: 14px;
        }
        .quote-icon {
            font-size: 40px;
            color: #06BBCC;
            opacity: 0.3;
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
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
                    <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="instructors.php" class="dropdown-item">Our Instructors</a>
                        <a href="testimonial.php" class="dropdown-item active">Testimonials</a>
                        <a href="apply-instructor.php" class="dropdown-item">Become an Instructor</a>
                    </div>
                </div>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $_SESSION['user_role'] === 'admin' ? 'admin/' : ($_SESSION['user_role'] === 'instructor' ? 'instructor/' : 'student/'); ?>" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
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
    <div class="container-fluid bg-primary py-5 mb-5 page-header" style="background-color: #06BBCC !important;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Testimonials</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Testimonials</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Testimonials Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Testimonials</h6>
                <h1 class="mb-3">What Our Students Say</h1>
                <p class="mb-5">Don't just take our word for it - hear from our successful students</p>
            </div>
            
            <!-- Testimonials Grid -->
            <div class="row g-4">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.<?php echo ($index % 3 + 1) * 1; ?>s">
                        <div class="testimonial-card bg-light rounded p-4 h-100 position-relative shadow-sm">
                            <div class="quote-icon">
                                <i class="fa fa-quote-right"></i>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <img class="rounded-circle border border-2 border-primary" 
                                     src="assets/img/<?php echo $testimonial['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($testimonial['name']); ?>" 
                                     style="width: 70px; height: 70px; object-fit: cover;">
                                <div class="ms-3">
                                    <h5 class="mb-0" style="color: #181d38;"><?php echo htmlspecialchars($testimonial['name']); ?></h5>
                                    <p class="text-muted mb-0 small"><?php echo htmlspecialchars($testimonial['profession']); ?></p>
                                    <div class="rating-star mt-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-0 text-muted" style="font-style: italic; line-height: 1.6;">
                                "<?php echo htmlspecialchars($testimonial['message']); ?>"
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Stats Section -->
            <div class="row g-4 mt-5 pt-4">
                <div class="col-12 text-center">
                    <div class="bg-primary rounded p-5" style="background-color: #06BBCC !important;">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                                <i class="fa fa-smile fa-3x text-white mb-2"></i>
                                <h2 class="text-white mb-0">5000+</h2>
                                <p class="text-white mb-0">Happy Students</p>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                                <i class="fa fa-star fa-3x text-white mb-2"></i>
                                <h2 class="text-white mb-0">4.8</h2>
                                <p class="text-white mb-0">Average Rating</p>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                                <i class="fa fa-book fa-3x text-white mb-2"></i>
                                <h2 class="text-white mb-0">100+</h2>
                                <p class="text-white mb-0">Courses</p>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                                <i class="fa fa-chalkboard-user fa-3x text-white mb-2"></i>
                                <h2 class="text-white mb-0">50+</h2>
                                <p class="text-white mb-0">Expert Instructors</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Call to Action -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <h3 class="mb-3">Ready to Start Your Journey?</h3>
                    <p class="mb-4">Join thousands of successful students who have transformed their careers with SkillMaster</p>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-primary px-5 py-3" style="background-color: #06BBCC; border-color: #06BBCC; border-radius: 30px;">
                            <i class="fa fa-user-plus me-2"></i>Join Now
                        </a>
                    <?php else: ?>
                        <a href="courses.php" class="btn btn-primary px-5 py-3" style="background-color: #06BBCC; border-color: #06BBCC; border-radius: 30px;">
                            <i class="fa fa-book me-2"></i>Explore Courses
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonials End -->

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
                            <img class="img-fluid bg-light p-1 rounded" src="assets/img/course-1.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1 rounded" src="assets/img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1 rounded" src="assets/img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1 rounded" src="assets/img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1 rounded" src="assets/img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1 rounded" src="assets/img/course-1.jpg" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Newsletter</h4>
                    <p>Subscribe to get updates on new courses and special offers.</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2" style="background-color: #06BBCC; border-color: #06BBCC;">SignUp</button>
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
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" style="background-color: #06BBCC; border-color: #06BBCC;"><i class="bi bi-arrow-up"></i></a>

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