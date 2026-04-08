<?php
// Terms and Conditions Page
require_once __DIR__ . '/../config/config.php';

$page_title = 'Terms and Conditions - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="Terms and Conditions for SkillMaster LMS - Legal agreement between users and the platform">
    <meta name="keywords" content="terms and conditions, legal, agreement, policies">

    <!-- Favicon -->
    <link href="assets/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">

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
        .terms-section {
            background-color: #F0FBFC;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .terms-section:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(6, 187, 204, 0.1);
        }
        .terms-section h3 {
            color: #06BBCC;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(6, 187, 204, 0.3);
        }
        .terms-section p, .terms-section li {
            font-family: 'Open Sans', sans-serif;
            font-size: 1rem;
            line-height: 1.7;
            color: #52565b;
        }
        .terms-section ul {
            padding-left: 20px;
        }
        .terms-section li {
            margin-bottom: 8px;
        }
        .highlight {
            background-color: rgba(6, 187, 204, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Open Sans', sans-serif;
        }
        .icon-circle {
            width: 50px;
            height: 50px;
            background-color: rgba(6, 187, 204, 0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .icon-circle i {
            font-size: 24px;
            color: #06BBCC;
        }
        .effective-date {
            font-family: 'Open Sans', sans-serif;
            font-style: italic;
            color: #6c757d;
            font-size: 0.9rem;
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
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="instructors.php" class="dropdown-item">Our Instructors</a>
                        <a href="testimonial.php" class="dropdown-item">testimonials</a>
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
                    <h1 class="display-3 text-white animated slideInDown">Terms & Conditions</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Terms & Conditions</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Terms Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Legal Agreement</h6>
                <h1 class="mb-3">Terms and Conditions</h1>
                <p class="mb-5 effective-date">Effective Date: <?php echo date('F d, Y'); ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="terms-section wow fadeInUp" data-wow-delay="0.1s">
                        <div class="icon-circle">
                            <i class="fa fa-handshake"></i>
                        </div>
                        <h3>1. Acceptance of Terms</h3>
                        <p>By accessing or using <?php echo APP_NAME; ?> ("the Platform"), you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our services.</p>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="0.2s">
                        <div class="icon-circle">
                            <i class="fa fa-user-check"></i>
                        </div>
                        <h3>2. Eligibility</h3>
                        <p>To use <?php echo APP_NAME; ?>, you must:</p>
                        <ul>
                            <li>Be at least 13 years of age</li>
                            <li>Provide accurate and complete registration information</li>
                            <li>Maintain the confidentiality of your account credentials</li>
                            <li>Accept responsibility for all activities under your account</li>
                        </ul>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="0.3s">
                        <div class="icon-circle">
                            <i class="fa fa-book-open"></i>
                        </div>
                        <h3>3. Course Enrollment and Payments</h3>
                        <p>When enrolling in courses on <?php echo APP_NAME; ?>:</p>
                        <ul>
                            <li>Course fees are clearly displayed before enrollment</li>
                            <li>Payments are processed through our secure mock payment system</li>
                            <li>Enrollment is confirmed after successful payment verification</li>
                            <li>Course access is granted upon enrollment approval</li>
                            <li>All sales are final unless otherwise stated</li>
                        </ul>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="0.4s">
                        <div class="icon-circle">
                            <i class="fa fa-code"></i>
                        </div>
                        <h3>4. User Conduct</h3>
                        <p>You agree not to:</p>
                        <ul>
                            <li>Share your account credentials with others</li>
                            <li>Copy, distribute, or share course materials without permission</li>
                            <li>Submit plagiarized or fraudulent assignments</li>
                            <li>Harass, abuse, or harm other users or instructors</li>
                            <li>Attempt to bypass security measures or access restricted areas</li>
                            <li>Use the platform for any illegal activities</li>
                        </ul>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="0.5s">
                        <div class="icon-circle">
                            <i class="fa fa-copyright"></i>
                        </div>
                        <h3>5. Intellectual Property</h3>
                        <p>All content on <?php echo APP_NAME; ?> is protected by copyright and intellectual property laws:</p>
                        <ul>
                            <li>Course materials are owned by <?php echo APP_NAME; ?> or our instructors</li>
                            <li>You may not reproduce, distribute, or create derivative works without permission</li>
                            <li>You retain ownership of your assignment submissions</li>
                            <li><?php echo APP_NAME; ?> grants you a limited license to access and use the platform for personal learning</li>
                        </ul>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="0.6s">
                        <div class="icon-circle">
                            <i class="fa fa-certificate"></i>
                        </div>
                        <h3>6. Certificates of Completion</h3>
                        <p>Certificates are awarded upon successful course completion:</p>
                        <ul>
                            <li>Certificates are issued only after completing all required assignments</li>
                            <li>Certificates are for personal achievement and not official accreditation</li>
                            <li><?php echo APP_NAME; ?> reserves the right to verify completion before issuing certificates</li>
                        </ul>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="0.7s">
                        <div class="icon-circle">
                            <i class="fa fa-ban"></i>
                        </div>
                        <h3>7. Account Termination</h3>
                        <p><?php echo APP_NAME; ?> reserves the right to suspend or terminate accounts that violate these terms, including:</p>
                        <ul>
                            <li>Academic dishonesty or plagiarism</li>
                            <li>Harassment of other users or instructors</li>
                            <li>Attempts to hack or disrupt the platform</li>
                            <li>Sharing of account credentials</li>
                            <li>Violation of intellectual property rights</li>
                        </ul>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="0.8s">
                        <div class="icon-circle">
                            <i class="fa fa-gavel"></i>
                        </div>
                        <h3>8. Disclaimer of Warranties</h3>
                        <p><?php echo APP_NAME; ?> is provided "as is" without warranties of any kind. We do not guarantee:</p>
                        <ul>
                            <li>That the platform will be uninterrupted or error-free</li>
                            <li>That course completion will lead to employment</li>
                            <li>That information provided is completely accurate or up-to-date</li>
                        </ul>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="0.9s">
                        <div class="icon-circle">
                            <i class="fa fa-scale-balanced"></i>
                        </div>
                        <h3>9. Limitation of Liability</h3>
                        <p>To the maximum extent permitted by law, <?php echo APP_NAME; ?> shall not be liable for any indirect, incidental, or consequential damages arising from your use of the platform.</p>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="1.0s">
                        <div class="icon-circle">
                            <i class="fa fa-pen"></i>
                        </div>
                        <h3>10. Modifications to Terms</h3>
                        <p><?php echo APP_NAME; ?> reserves the right to modify these terms at any time. We will notify users of material changes via email or platform notification. Continued use of the platform constitutes acceptance of modified terms.</p>
                    </div>

                    <div class="terms-section wow fadeInUp" data-wow-delay="1.1s">
                        <div class="icon-circle">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <h3>11. Contact Information</h3>
                        <p>For questions about these Terms and Conditions, please contact us:</p>
                        <ul>
                            <li><i class="fa fa-envelope text-primary me-2"></i> Email: <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="text-primary"><?php echo ADMIN_EMAIL; ?></a></li>
                            <li><i class="fa fa-phone-alt text-primary me-2"></i> Phone: +254 712 345 678</li>
                            <li><i class="fa fa-map-marker-alt text-primary me-2"></i> Address: Nairobi, Kenya</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary px-5 py-3" style="background-color: #06BBCC; border-color: #06BBCC; border-radius: 30px;">
                    <i class="fa fa-home me-2"></i>Back to Home
                </a>
                <a href="privacy.php" class="btn btn-outline-primary px-5 py-3 ms-2" style="border-color: #06BBCC; color: #06BBCC; border-radius: 30px;">
                    <i class="fa fa-shield-alt me-2"></i>Privacy Policy
                </a>
            </div>
        </div>
    </div>
    <!-- Terms Content End -->

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
                            <a href="privacy.php">Privacy</a>
                            <a href="faq.php">Help</a>
                            <a href="faq.php">FAQs</a>
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