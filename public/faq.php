<?php
// Frequently Asked Questions Page
require_once __DIR__ . '/../config/config.php';

$page_title = 'FAQs - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="Frequently Asked Questions about SkillMaster LMS - Find answers to common questions">
    <meta name="keywords" content="faq, frequently asked questions, help, support">

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
        .faq-section {
            background-color: #F0FBFC;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .faq-section:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(6, 187, 204, 0.1);
        }
        .faq-question {
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: #181d38;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .faq-question i {
            color: #06BBCC;
            margin-right: 12px;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        .faq-question.active i {
            transform: rotate(90deg);
        }
        .faq-answer {
            font-family: 'Open Sans', sans-serif;
            font-size: 0.95rem;
            line-height: 1.7;
            color: #52565b;
            padding-left: 32px;
            display: none;
        }
        .faq-answer.show {
            display: block;
        }
        .faq-category {
            background-color: #06BBCC;
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            display: inline-block;
            margin-bottom: 25px;
            font-family: 'Nunito', sans-serif;
            font-weight: 600;
        }
        .search-box {
            background-color: #F0FBFC;
            border-radius: 50px;
            padding: 5px 15px;
            margin-bottom: 30px;
        }
        .search-box input {
            background: transparent;
            border: none;
            padding: 12px 0;
            font-family: 'Open Sans', sans-serif;
        }
        .search-box input:focus {
            outline: none;
            box-shadow: none;
        }
        .search-box button {
            background: #06BBCC;
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            color: white;
        }
        .category-filter .btn {
            border-radius: 30px;
            margin: 5px;
            font-family: 'Open Sans', sans-serif;
        }
        .category-filter .btn.active {
            background-color: #06BBCC;
            color: white;
            border-color: #06BBCC;
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
                    <h1 class="display-3 text-white animated slideInDown">Frequently Asked Questions</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">FAQs</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- FAQ Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Got Questions?</h6>
                <h1 class="mb-3">We Have Answers</h1>
                <p class="mb-5">Find quick answers to common questions about our platform</p>
            </div>
            
            <!-- Search Box -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <div class="search-box d-flex align-items-center">
                        <i class="fa fa-search text-primary me-2"></i>
                        <input type="text" class="form-control" id="faqSearch" placeholder="Search for answers...">
                        <button onclick="searchFAQ()">Search</button>
                    </div>
                </div>
            </div>
            
            <!-- Category Filters -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-10 text-center category-filter">
                    <button class="btn btn-outline-primary active" data-category="all">All Questions</button>
                    <button class="btn btn-outline-primary" data-category="account">Account & Registration</button>
                    <button class="btn btn-outline-primary" data-category="courses">Courses & Learning</button>
                    <button class="btn btn-outline-primary" data-category="payments">Payments & Enrollment</button>
                    <button class="btn btn-outline-primary" data-category="technical">Technical Support</button>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    
                    <!-- Account & Registration Section -->
                    <div class="faq-category">Account & Registration</div>
                    <div class="faq-section" data-category="account">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> How do I create a student account?
                        </div>
                        <div class="faq-answer">
                            To create a student account, click the "Join Now" button on the homepage or visit the registration page. Fill in your personal information, choose a password, and submit the form. You'll receive a confirmation email after successful registration.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="account">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> How do I become an instructor?
                        </div>
                        <div class="faq-answer">
                            To become an instructor, click on "Become an Instructor" in the Pages dropdown menu. Fill out the application form with your qualifications, experience, and teaching philosophy. Our team will review your application and contact you within 3-5 business days.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="account">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> I forgot my password. How do I reset it?
                        </div>
                        <div class="faq-answer">
                            Click on "Forgot Password" on the login page. Enter your registered email address, and we'll send you a password reset link. Click the link in the email and follow the instructions to create a new password.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="account">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> Can I change my profile information?
                        </div>
                        <div class="faq-answer">
                            Yes! Once logged in, go to your profile page. You can update your name, phone number, address, bio, and even upload a profile picture. Some information like email address cannot be changed for security reasons.
                        </div>
                    </div>
                    
                    <!-- Courses & Learning Section -->
                    <div class="faq-category mt-4">Courses & Learning</div>
                    <div class="faq-section" data-category="courses">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> How do I enroll in a course?
                        </div>
                        <div class="faq-answer">
                            Browse our course catalog, select a course you're interested in, click "Enroll Now," complete the mock payment process, and wait for admin verification. Once approved, you'll have access to all course materials.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="courses">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> How long do I have access to a course?
                        </div>
                        <div class="faq-answer">
                            Once enrolled, you have lifetime access to the course materials. You can learn at your own pace and revisit the content anytime, even after completing the course.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="courses">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> How do I submit assignments?
                        </div>
                        <div class="faq-answer">
                            Go to your dashboard, click on "Assignments," select the pending assignment, write your submission or upload a file, and click "Submit." You'll receive notifications when your assignment is graded.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="courses">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> Do I get a certificate after completing a course?
                        </div>
                        <div class="faq-answer">
                            Yes! Upon successful completion of all course requirements and assignments, you'll receive a certificate of completion. You can download and share your certificate from your dashboard.
                        </div>
                    </div>
                    
                    <!-- Payments & Enrollment Section -->
                    <div class="faq-category mt-4">Payments & Enrollment</div>
                    <div class="faq-section" data-category="payments">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> How does the mock payment system work?
                        </div>
                        <div class="faq-answer">
                            The mock payment system is a simulation for educational purposes. When you click "Enroll Now," you'll be taken to a demo payment page where you can complete a simulated transaction. No real money is charged. The admin will verify your enrollment and grant access.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="payments">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> How long does enrollment verification take?
                        </div>
                        <div class="faq-answer">
                            Enrollment verification typically takes 24-48 hours. You'll receive an email notification once your enrollment is approved. You can also check your enrollment status in your dashboard.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="payments">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> What payment methods are accepted?
                        </div>
                        <div class="faq-answer">
                            Since this is a mock payment system for educational purposes, we simulate card payments. In a real implementation, we would support credit/debit cards, mobile money, and bank transfers.
                        </div>
                    </div>
                    
                    <!-- Technical Support Section -->
                    <div class="faq-category mt-4">Technical Support</div>
                    <div class="faq-section" data-category="technical">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> What browsers are supported?
                        </div>
                        <div class="faq-answer">
                            <?php echo APP_NAME; ?> works best with the latest versions of Chrome, Firefox, Safari, and Edge. For the best experience, keep your browser updated to the latest version.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="technical">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> The site is loading slowly. What should I do?
                        </div>
                        <div class="faq-answer">
                            Try clearing your browser cache, checking your internet connection, or using a different browser. If the issue persists, please contact our support team with details about your device and browser version.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="technical">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> How do I contact support?
                        </div>
                        <div class="faq-answer">
                            You can contact us through the contact form on our website, send an email to <?php echo ADMIN_EMAIL; ?>, or call us at +254 712 345 678. Our support team is available Monday-Friday, 9 AM - 6 PM EAT.
                        </div>
                    </div>
                    
                    <div class="faq-section" data-category="technical">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <i class="fa fa-chevron-right"></i> Is my data secure on this platform?
                        </div>
                        <div class="faq-answer">
                            Yes! We take data security seriously. We use SSL encryption, secure password hashing, and regular security audits to protect your information. Please review our Privacy Policy for more details.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <h4>Still have questions?</h4>
                <p class="mb-4">Can't find the answer you're looking for? Please contact our support team.</p>
                <a href="contact.php" class="btn btn-primary px-5 py-3" style="background-color: #06BBCC; border-color: #06BBCC; border-radius: 30px;">
                    <i class="fa fa-envelope me-2"></i>Contact Us
                </a>
            </div>
        </div>
    </div>
    <!-- FAQ Content End -->

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
    
    <script>
        function toggleAnswer(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('i');
            
            answer.classList.toggle('show');
            element.classList.toggle('active');
            
            if (answer.classList.contains('show')) {
                icon.style.transform = 'rotate(90deg)';
            } else {
                icon.style.transform = 'rotate(0deg)';
            }
        }
        
        // Category filtering
        document.querySelectorAll('.category-filter .btn').forEach(button => {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                
                // Update active state
                document.querySelectorAll('.category-filter .btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filter FAQ sections
                document.querySelectorAll('.faq-section').forEach(section => {
                    if (category === 'all' || section.getAttribute('data-category') === category) {
                        section.style.display = 'block';
                    } else {
                        section.style.display = 'none';
                    }
                });
                
                // Show/hide category headers
                document.querySelectorAll('.faq-category').forEach(header => {
                    let hasVisibleSections = false;
                    const nextSections = [];
                    let nextElement = header.nextElementSibling;
                    
                    while (nextElement && nextElement.classList.contains('faq-section')) {
                        if (category === 'all' || nextElement.getAttribute('data-category') === category) {
                            hasVisibleSections = true;
                        }
                        nextElement = nextElement.nextElementSibling;
                    }
                    
                    header.style.display = hasVisibleSections ? 'inline-block' : 'none';
                });
            });
        });
        
        function searchFAQ() {
            const searchTerm = document.getElementById('faqSearch').value.toLowerCase();
            const allSections = document.querySelectorAll('.faq-section');
            
            allSections.forEach(section => {
                const question = section.querySelector('.faq-question').innerText.toLowerCase();
                const answer = section.querySelector('.faq-answer').innerText.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
            
            // Show all category headers that have visible sections
            document.querySelectorAll('.faq-category').forEach(header => {
                let hasVisibleSections = false;
                let nextElement = header.nextElementSibling;
                
                while (nextElement && nextElement.classList.contains('faq-section')) {
                    if (nextElement.style.display !== 'none') {
                        hasVisibleSections = true;
                    }
                    nextElement = nextElement.nextElementSibling;
                }
                
                header.style.display = hasVisibleSections ? 'inline-block' : 'none';
            });
        }
        
        // Initialize - show all sections
        document.querySelectorAll('.faq-section').forEach(section => {
            section.style.display = 'block';
        });
        
        document.querySelectorAll('.faq-category').forEach(header => {
            header.style.display = 'inline-block';
        });
    </script>
</body>
</html>