<?php
// Instructor Application Page - Premium Version
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
    
    <style>
        :root {
            --teal-primary: #06BBCC;
            --teal-dark: #0598A6;
            --teal-light: #E6F8FA;
            --navy-dark: #181d38;
            --gray-text: #52565b;
        }
        
        body {
            background: linear-gradient(135deg, #F0FBFC 0%, #E6F8FA 100%);
        }
        
        /* Premium Header */
        .premium-header {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            position: relative;
            overflow: hidden;
        }
        
        .premium-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        
        /* Premium Card */
        .premium-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(6, 187, 204, 0.15);
        }
        
        /* Form Styling */
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-control {
            border-radius: 14px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #06BBCC;
            box-shadow: 0 0 0 3px rgba(6, 187, 204, 0.1);
            outline: none;
        }
        
        textarea.form-control {
            resize: vertical;
        }
        
        /* Section Headers */
        .section-header {
            position: relative;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        
        .section-header h4 {
            color: #181d38;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .section-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #06BBCC, #0598A6);
            border-radius: 3px;
        }
        
        /* Submit Button */
        .btn-submit {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border: none;
            border-radius: 40px;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 187, 204, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        /* Benefit Cards */
        .benefit-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(6, 187, 204, 0.1);
        }
        
        .benefit-card:hover {
            transform: translateY(-5px);
            border-color: #06BBCC;
            box-shadow: 0 10px 30px rgba(6, 187, 204, 0.1);
        }
        
        .benefit-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .benefit-icon i {
            font-size: 2rem;
            color: white;
        }
        
        /* Alert Styling */
        .alert-custom {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        /* Required Field Indicator */
        .required-field::after {
            content: '*';
            color: #dc3545;
            margin-left: 4px;
        }
        
        /* Floating Labels Enhancement */
        .form-floating > label {
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .premium-card {
                border-radius: 20px;
            }
            .benefit-card {
                padding: 1.5rem;
            }
        }
        
        /* Animated Spinner */
        .spinner-border {
            vertical-align: middle;
        }
        
        /* Progress Steps */
        .form-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .form-progress::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        
        .progress-step {
            position: relative;
            z-index: 1;
            background: white;
            text-align: center;
            flex: 1;
        }
        
        .progress-step .step-number {
            width: 40px;
            height: 40px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        
        .progress-step.active .step-number {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border-color: #06BBCC;
            color: white;
        }
        
        .progress-step .step-label {
            font-size: 0.75rem;
            margin-top: 8px;
            color: #6c757d;
        }
        
        .progress-step.active .step-label {
            color: #06BBCC;
            font-weight: 600;
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
            <h2 class="m-0 text-primary"><i class="fa fa-crown me-2"></i><?php echo APP_NAME; ?></h2>
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
                        <a href="testimonial.php" class="dropdown-item">testimonials</a>
                        <a href="apply-instructor.php" class="dropdown-item active">Become an Instructor</a>
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

    <!-- Premium Header Start -->
    <div class="container-fluid premium-header py-5 mb-5">
        <div class="container text-center position-relative" style="z-index: 2;">
            <h1 class="display-4 text-white fw-bold mb-3 animate__animated animate__fadeInDown">Become an Instructor</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center bg-transparent">
                    <li class="breadcrumb-item"><a class="text-white text-decoration-none" href="index.php">Home</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Apply</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Premium Header End -->

    <!-- Application Form Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Join Our Team</h6>
                <h1 class="display-5 fw-bold mb-3">Share Your Knowledge With the World</h1>
                <p class="lead text-muted mb-4">We're looking for passionate experts to create and teach courses. Join our community of instructors and make a difference in learners' lives.</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                    <i class="fa fa-check-circle fa-lg me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                    <i class="fa fa-exclamation-circle fa-lg me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row justify-content-center mt-4">
                <div class="col-lg-10 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="premium-card p-4 p-md-5">
                        <form method="POST" action="" id="applicationForm">
                            <?php echo Security::csrfField(); ?>
                            
                            <!-- Personal Information Section -->
                            <div class="section-header">
                                <h4><i class="fa fa-user-circle me-2 text-primary"></i>Personal Information</h4>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>" required>
                                        <label for="first_name"><i class="fa fa-user me-1 text-primary"></i> First Name <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>" required>
                                        <label for="last_name"><i class="fa fa-user me-1 text-primary"></i> Last Name <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                                        <label for="email"><i class="fa fa-envelope me-1 text-primary"></i> Email Address <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>">
                                        <label for="phone"><i class="fa fa-phone me-1 text-primary"></i> Phone Number</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Education & Experience Section -->
                            <div class="section-header mt-4">
                                <h4><i class="fa fa-graduation-cap me-2 text-primary"></i>Education & Experience</h4>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="highest_qualification" name="highest_qualification" placeholder="Highest Qualification" value="<?php echo htmlspecialchars($formData['highest_qualification'] ?? ''); ?>">
                                        <label for="highest_qualification"><i class="fa fa-certificate me-1 text-primary"></i> Highest Qualification</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="institution" name="institution" placeholder="Institution" value="<?php echo htmlspecialchars($formData['institution'] ?? ''); ?>">
                                        <label for="institution"><i class="fa fa-university me-1 text-primary"></i> Institution</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="graduation_year" name="graduation_year" placeholder="Graduation Year" value="<?php echo htmlspecialchars($formData['graduation_year'] ?? ''); ?>">
                                        <label for="graduation_year"><i class="fa fa-calendar me-1 text-primary"></i> Graduation Year</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="years_experience" name="years_experience" placeholder="Years of Experience" value="<?php echo htmlspecialchars($formData['years_experience'] ?? ''); ?>">
                                        <label for="years_experience"><i class="fa fa-briefcase me-1 text-primary"></i> Years of Experience</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="current_role" name="current_role" placeholder="Current Role" value="<?php echo htmlspecialchars($formData['current_role'] ?? ''); ?>">
                                        <label for="current_role"><i class="fa fa-badge me-1 text-primary"></i> Current Role</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="organization" name="organization" placeholder="Organization" value="<?php echo htmlspecialchars($formData['organization'] ?? ''); ?>">
                                        <label for="organization"><i class="fa fa-building me-1 text-primary"></i> Organization</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Teaching & Expertise Section -->
                            <div class="section-header mt-4">
                                <h4><i class="fa fa-chalkboard-user me-2 text-primary"></i>Teaching & Expertise</h4>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="expertise_areas" name="expertise_areas" placeholder="Areas of Expertise" style="height: 100px" required><?php echo htmlspecialchars($formData['expertise_areas'] ?? ''); ?></textarea>
                                        <label for="expertise_areas"><i class="fa fa-star me-1 text-primary"></i> Areas of Expertise <span class="text-danger">*</span></label>
                                    </div>
                                    <small class="text-muted">e.g., Web Development, Data Science, Mobile Apps, Cybersecurity</small>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="teaching_philosophy" name="teaching_philosophy" placeholder="Teaching Philosophy" style="height: 120px" required><?php echo htmlspecialchars($formData['teaching_philosophy'] ?? ''); ?></textarea>
                                        <label for="teaching_philosophy"><i class="fa fa-quote-left me-1 text-primary"></i> Teaching Philosophy <span class="text-danger">*</span></label>
                                    </div>
                                    <small class="text-muted">How do you approach teaching? What methods do you use?</small>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="sample_course_idea" name="sample_course_idea" placeholder="Sample Course Idea" style="height: 120px" required><?php echo htmlspecialchars($formData['sample_course_idea'] ?? ''); ?></textarea>
                                        <label for="sample_course_idea"><i class="fa fa-lightbulb me-1 text-primary"></i> Sample Course Idea <span class="text-danger">*</span></label>
                                    </div>
                                    <small class="text-muted">What course would you like to teach? Describe the content and structure.</small>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="why_teach" name="why_teach" placeholder="Why Do You Want to Teach?" style="height: 100px"><?php echo htmlspecialchars($formData['why_teach'] ?? ''); ?></textarea>
                                        <label for="why_teach"><i class="fa fa-heart me-1 text-primary"></i> Why Do You Want to Teach?</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="url" class="form-control" id="portfolio_link" name="portfolio_link" placeholder="Portfolio Link" value="<?php echo htmlspecialchars($formData['portfolio_link'] ?? ''); ?>">
                                        <label for="portfolio_link"><i class="fa fa-link me-1 text-primary"></i> Portfolio / LinkedIn / GitHub Link</label>
                                    </div>
                                    <small class="text-muted">Optional - Link to your professional portfolio or GitHub profile</small>
                                </div>
                            </div>
                            
                            <div class="alert alert-info rounded-4 mt-3" style="background: linear-gradient(135deg, #E6F8FA, #F0FBFC); border: none;">
                                <div class="d-flex">
                                    <i class="fa fa-info-circle fa-2x text-primary me-3"></i>
                                    <div>
                                        <strong class="text-primary">What happens next?</strong>
                                        <p class="mb-0 text-muted">Our team will review your application within 3-5 business days. If approved, you'll receive login credentials to start creating your courses.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-submit w-100 py-3 mt-3" id="submitBtn">
                                <i class="fa fa-paper-plane me-2"></i>Submit Application
                                <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
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
                <h1 class="display-5 fw-bold mb-3">Benefits of Becoming an Instructor</h1>
                <p class="lead text-muted">Join our community of expert instructors and make a real impact</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fa fa-dollar-sign"></i>
                        </div>
                        <h5 class="mb-3 fw-bold">Earn Money</h5>
                        <p class="text-muted mb-0">Earn revenue from course sales and reach a global audience of learners while building your personal brand.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fa fa-chalkboard-user"></i>
                        </div>
                        <h5 class="mb-3 fw-bold">Share Your Knowledge</h5>
                        <p class="text-muted mb-0">Make a meaningful difference by sharing your expertise with thousands of eager students worldwide.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fa fa-globe"></i>
                        </div>
                        <h5 class="mb-3 fw-bold">Global Reach</h5>
                        <p class="text-muted mb-0">Connect with learners from around the world and grow your professional network exponentially.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Benefits End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
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
        // Form submission spinner
        const form = document.getElementById('applicationForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                const spinner = submitBtn.querySelector('.spinner-border');
                if (spinner) {
                    spinner.classList.remove('d-none');
                }
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Submitting Application...';
            });
        }
        
        // Smooth back to top
        document.querySelector('.back-to-top')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Animate on scroll initialization
        new WOW().init();
    </script>
</body>
</html>