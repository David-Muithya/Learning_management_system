<?php
// Privacy Policy Page
require_once __DIR__ . '/../config/config.php';

$page_title = 'Privacy Policy - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="Privacy Policy for SkillMaster LMS - How we collect, use, and protect your information">
    <meta name="keywords" content="privacy policy, data protection, terms">

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
        .policy-section {
            background-color: #F0FBFC;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .policy-section:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(6, 187, 204, 0.1);
        }
        .policy-section h3 {
            color: #06BBCC;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(6, 187, 204, 0.3);
        }
        .policy-section p, .policy-section li {
            font-family: 'Open Sans', sans-serif;
            font-size: 1rem;
            line-height: 1.7;
            color: #52565b;
        }
        .policy-section ul {
            padding-left: 20px;
        }
        .policy-section li {
            margin-bottom: 8px;
        }
        .last-updated {
            font-family: 'Open Sans', sans-serif;
            font-style: italic;
            color: #6c757d;
            font-size: 0.9rem;
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
                    <h1 class="display-3 text-white animated slideInDown">Privacy Policy</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Privacy Policy</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Privacy Policy Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Privacy Matters</h6>
                <h1 class="mb-3">Your Privacy is Our Priority</h1>
                <p class="mb-5 last-updated">Last Updated: <?php echo date('F d, Y'); ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="policy-section wow fadeInUp" data-wow-delay="0.1s">
                        <div class="icon-circle">
                            <i class="fa fa-shield-alt"></i>
                        </div>
                        <h3>1. Information We Collect</h3>
                        <p>At <?php echo APP_NAME; ?>, we collect information to provide better services to our users. We collect the following types of information:</p>
                        <ul>
                            <li><strong>Personal Information:</strong> Name, email address, phone number, and billing information when you register for courses.</li>
                            <li><strong>Usage Data:</strong> Information about how you use our platform, including course progress, assignment submissions, and login activity.</li>
                            <li><strong>Device Information:</strong> IP address, browser type, operating system, and device identifiers.</li>
                            <li><strong>Communication Data:</strong> Messages sent through our contact forms and discussion forums.</li>
                        </ul>
                    </div>

                    <div class="policy-section wow fadeInUp" data-wow-delay="0.2s">
                        <div class="icon-circle">
                            <i class="fa fa-chart-line"></i>
                        </div>
                        <h3>2. How We Use Your Information</h3>
                        <p>We use the information we collect to:</p>
                        <ul>
                            <li>Provide, maintain, and improve our learning platform</li>
                            <li>Process your course enrollments and payments</li>
                            <li>Send you important notifications about your courses</li>
                            <li>Respond to your inquiries and support requests</li>
                            <li>Analyze usage patterns to enhance user experience</li>
                            <li>Prevent fraud and ensure platform security</li>
                        </ul>
                    </div>

                    <div class="policy-section wow fadeInUp" data-wow-delay="0.3s">
                        <div class="icon-circle">
                            <i class="fa fa-lock"></i>
                        </div>
                        <h3>3. Data Security</h3>
                        <p>We take data security seriously. We implement industry-standard security measures to protect your information:</p>
                        <ul>
                            <li>SSL encryption for all data transmission</li>
                            <li>Secure password hashing using bcrypt</li>
                            <li>Regular security audits and updates</li>
                            <li>Restricted access to personal data</li>
                            <li>Automatic session timeout for inactive users</li>
                        </ul>
                    </div>

                    <div class="policy-section wow fadeInUp" data-wow-delay="0.4s">
                        <div class="icon-circle">
                            <i class="fa fa-share-alt"></i>
                        </div>
                        <h3>4. Information Sharing</h3>
                        <p>We do not sell, trade, or rent your personal information to third parties. We may share your information in the following circumstances:</p>
                        <ul>
                            <li>With your consent or at your direction</li>
                            <li>With instructors for course-related communications</li>
                            <li>To comply with legal obligations</li>
                            <li>To protect the rights and safety of <?php echo APP_NAME; ?> and our users</li>
                        </ul>
                    </div>

                    <div class="policy-section wow fadeInUp" data-wow-delay="0.5s">
                        <div class="icon-circle">
                            <i class="fa fa-cookie-bite"></i>
                        </div>
                        <h3>5. Cookies and Tracking</h3>
                        <p>We use cookies and similar tracking technologies to enhance your experience. Cookies help us:</p>
                        <ul>
                            <li>Remember your login status and preferences</li>
                            <li>Understand how you interact with our platform</li>
                            <li>Improve site performance and functionality</li>
                        </ul>
                        <p>You can control cookie settings through your browser preferences.</p>
                    </div>

                    <div class="policy-section wow fadeInUp" data-wow-delay="0.6s">
                        <div class="icon-circle">
                            <i class="fa fa-user-graduate"></i>
                        </div>
                        <h3>6. Your Rights</h3>
                        <p>You have the following rights regarding your personal information:</p>
                        <ul>
                            <li>Access and review your personal data</li>
                            <li>Request corrections to inaccurate information</li>
                            <li>Request deletion of your account and data</li>
                            <li>Opt-out of marketing communications</li>
                            <li>Export your data in a portable format</li>
                        </ul>
                        <p>To exercise these rights, please contact us at <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="text-primary"><?php echo ADMIN_EMAIL; ?></a></p>
                    </div>

                    <div class="policy-section wow fadeInUp" data-wow-delay="0.7s">
                        <div class="icon-circle">
                            <i class="fa fa-child"></i>
                        </div>
                        <h3>7. Children's Privacy</h3>
                        <p><?php echo APP_NAME; ?> is intended for users aged 13 and above. We do not knowingly collect personal information from children under 13. If you believe we have collected information from a child under 13, please contact us immediately.</p>
                    </div>

                    <div class="policy-section wow fadeInUp" data-wow-delay="0.8s">
                        <div class="icon-circle">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <h3>8. Updates to This Policy</h3>
                        <p>We may update this privacy policy from time to time. We will notify you of any material changes by posting the new policy on this page and updating the "Last Updated" date. We encourage you to review this policy periodically.</p>
                    </div>

                    <div class="policy-section wow fadeInUp" data-wow-delay="0.9s">
                        <div class="icon-circle">
                            <i class="fa fa-phone-alt"></i>
                        </div>
                        <h3>9. Contact Us</h3>
                        <p>If you have questions about this Privacy Policy or our data practices, please contact us:</p>
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
                <a href="contact.php" class="btn btn-outline-primary px-5 py-3 ms-2" style="border-color: #06BBCC; color: #06BBCC; border-radius: 30px;">
                    <i class="fa fa-envelope me-2"></i>Contact Us
                </a>
            </div>
        </div>
    </div>
    <!-- Privacy Policy Content End -->

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