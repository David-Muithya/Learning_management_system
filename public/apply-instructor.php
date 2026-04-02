<?php
// Instructor Application Page
require_once __DIR__ . '/../config/config.php';
use SkillMaster\Models\InstructorApplication;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

$error = '';
$success = '';
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $formData = [
            'first_name' => Validation::sanitize($_POST['first_name'] ?? ''),
            'last_name' => Validation::sanitize($_POST['last_name'] ?? ''),
            'email' => Validation::sanitize($_POST['email'] ?? ''),
            'phone' => Validation::sanitize($_POST['phone'] ?? ''),
            'highest_qualification' => Validation::sanitize($_POST['highest_qualification'] ?? ''),
            'institution' => Validation::sanitize($_POST['institution'] ?? ''),
            'graduation_year' => Validation::sanitize($_POST['graduation_year'] ?? ''),
            'years_experience' => Validation::sanitize($_POST['years_experience'] ?? ''),
            'current_role' => Validation::sanitize($_POST['current_role'] ?? ''),
            'organization' => Validation::sanitize($_POST['organization'] ?? ''),
            'expertise_areas' => Validation::sanitize($_POST['expertise_areas'] ?? ''),
            'teaching_philosophy' => Validation::sanitize($_POST['teaching_philosophy'] ?? ''),
            'sample_course_idea' => Validation::sanitize($_POST['sample_course_idea'] ?? ''),
            'portfolio_link' => Validation::sanitize($_POST['portfolio_link'] ?? ''),
            'why_teach' => Validation::sanitize($_POST['why_teach'] ?? '')
        ];
        
        // Validation
        $required = ['first_name', 'last_name', 'email', 'expertise_areas', 'teaching_philosophy', 'sample_course_idea'];
        $missing = [];
        foreach ($required as $field) {
            if (empty($formData[$field])) {
                $missing[] = str_replace('_', ' ', $field);
            }
        }
        
        if (!empty($missing)) {
            $error = 'Please fill in all required fields: ' . implode(', ', $missing);
        } elseif (!Validation::email($formData['email'])) {
            $error = 'Please enter a valid email address';
        } elseif ($formData['portfolio_link'] && !Validation::url($formData['portfolio_link'])) {
            $error = 'Please enter a valid portfolio URL';
        } else {
            $application = new InstructorApplication();
            $result = $application->create($formData);
            
            if ($result) {
                $success = 'Your application has been submitted successfully! We will review it and get back to you within 3-5 business days.';
                $formData = [];
            } else {
                $error = 'Failed to submit application. Please try again.';
            }
        }
    }
}

$page_title = 'Become an Instructor - ' . APP_NAME;
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
                    <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="instructors.php" class="dropdown-item">Our Instructors</a>
                        <a href="testimonials.php" class="dropdown-item">Testimonials</a>
                        <a href="apply-instructor.php" class="dropdown-item active">Become an Instructor</a>
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
                    <h1 class="display-3 text-white animated slideInDown">Become an Instructor</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Apply</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Application Form Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Join Our Team</h6>
                <h1 class="mb-5">Share Your Knowledge With the World</h1>
                <p class="mb-4">We're looking for passionate experts to create and teach courses. Join our community of instructors and make a difference in learners' lives.</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row justify-content-center">
                <div class="col-lg-10 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="bg-light rounded p-5">
                        <form method="POST" action="">
                            <?php echo Security::csrfField(); ?>
                            
                            <h4 class="mb-4">Personal Information</h4>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>" required>
                                        <label for="first_name">First Name *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>" required>
                                        <label for="last_name">Last Name *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                                        <label for="email">Email Address *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>">
                                        <label for="phone">Phone Number</label>
                                    </div>
                                </div>
                            </div>
                            
                            <h4 class="mb-4">Education & Experience</h4>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="highest_qualification" name="highest_qualification" placeholder="Highest Qualification" value="<?php echo htmlspecialchars($formData['highest_qualification'] ?? ''); ?>">
                                        <label for="highest_qualification">Highest Qualification</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="institution" name="institution" placeholder="Institution" value="<?php echo htmlspecialchars($formData['institution'] ?? ''); ?>">
                                        <label for="institution">Institution</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="graduation_year" name="graduation_year" placeholder="Graduation Year" value="<?php echo htmlspecialchars($formData['graduation_year'] ?? ''); ?>">
                                        <label for="graduation_year">Graduation Year</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="years_experience" name="years_experience" placeholder="Years of Experience" value="<?php echo htmlspecialchars($formData['years_experience'] ?? ''); ?>">
                                        <label for="years_experience">Years of Experience</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="current_role" name="current_role" placeholder="Current Role" value="<?php echo htmlspecialchars($formData['current_role'] ?? ''); ?>">
                                        <label for="current_role">Current Role</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="organization" name="organization" placeholder="Organization" value="<?php echo htmlspecialchars($formData['organization'] ?? ''); ?>">
                                        <label for="organization">Organization</label>
                                    </div>
                                </div>
                            </div>
                            
                            <h4 class="mb-4">Teaching & Expertise</h4>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="expertise_areas" name="expertise_areas" placeholder="Areas of Expertise" style="height: 100px" required><?php echo htmlspecialchars($formData['expertise_areas'] ?? ''); ?></textarea>
                                        <label for="expertise_areas">Areas of Expertise * (e.g., Web Development, Data Science, Mobile Apps)</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="teaching_philosophy" name="teaching_philosophy" placeholder="Teaching Philosophy" style="height: 120px" required><?php echo htmlspecialchars($formData['teaching_philosophy'] ?? ''); ?></textarea>
                                        <label for="teaching_philosophy">Teaching Philosophy * (How do you approach teaching?)</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="sample_course_idea" name="sample_course_idea" placeholder="Sample Course Idea" style="height: 120px" required><?php echo htmlspecialchars($formData['sample_course_idea'] ?? ''); ?></textarea>
                                        <label for="sample_course_idea">Sample Course Idea * (What course would you like to teach?)</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="why_teach" name="why_teach" placeholder="Why Do You Want to Teach?" style="height: 100px"><?php echo htmlspecialchars($formData['why_teach'] ?? ''); ?></textarea>
                                        <label for="why_teach">Why Do You Want to Teach?</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="url" class="form-control" id="portfolio_link" name="portfolio_link" placeholder="Portfolio Link" value="<?php echo htmlspecialchars($formData['portfolio_link'] ?? ''); ?>">
                                        <label for="portfolio_link">Portfolio / LinkedIn / GitHub Link</label>
                                    </div>
                                    <small class="text-muted">Optional - Link to your professional portfolio or GitHub profile</small>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>What happens next?</strong> Our team will review your application within 3-5 business days. If approved, you'll receive login credentials to start creating your courses.
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="fa fa-paper-plane me-2"></i>Submit Application
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Application Form End -->

    <!-- Benefits Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="section-title bg-white text-center text-primary px-3">Why Join Us</h6>
                <h1 class="mb-5">Benefits of Becoming an Instructor</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="bg-light rounded p-4 text-center h-100">
                        <i class="fa fa-dollar-sign fa-3x text-primary mb-3"></i>
                        <h5 class="mb-3">Earn Money</h5>
                        <p class="mb-0">Earn revenue from course sales and reach a global audience of learners.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="bg-light rounded p-4 text-center h-100">
                        <i class="fa fa-chalkboard-user fa-3x text-primary mb-3"></i>
                        <h5 class="mb-3">Share Your Knowledge</h5>
                        <p class="mb-0">Make a difference by sharing your expertise with thousands of students.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="bg-light rounded p-4 text-center h-100">
                        <i class="fa fa-globe fa-3x text-primary mb-3"></i>
                        <h5 class="mb-3">Global Reach</h5>
                        <p class="mb-0">Connect with learners from around the world and grow your professional network.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Benefits End -->

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