<?php
// Student Registration Only - Premium Version
require_once __DIR__ . '/../config/config.php';
use SkillMaster\Auth\Authenticator;
use SkillMaster\Auth\RoleMiddleware;

// Redirect if already logged in
RoleMiddleware::requireGuest();

$error = '';
$success = '';
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];
    
    // Validation
    if (empty($formData['first_name']) || empty($formData['last_name']) || empty($formData['email']) || empty($formData['password'])) {
        $error = 'All fields are required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($formData['password']) < PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
    } elseif ($formData['password'] !== $formData['confirm_password']) {
        $error = 'Passwords do not match';
    } else {
        $auth = new Authenticator();
        $result = $auth->registerStudent($formData);
        
        if ($result['success']) {
            $success = $result['message'];
            $formData = [];
        } else {
            $error = $result['message'];
        }
    }
}

$page_title = 'Join SkillMaster - ' . APP_NAME;
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
        }
        
        body {
            background: linear-gradient(135deg, #F0FBFC 0%, #E6F8FA 100%);
            min-height: 100vh;
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
            box-shadow: 0 25px 50px -12px rgba(6, 187, 204, 0.25);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(6, 187, 204, 0.35);
        }
        
        /* Form Styling */
        .form-floating {
            margin-bottom: 1.25rem;
        }
        
        .form-control {
            border-radius: 14px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            height: 58px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #06BBCC;
            box-shadow: 0 0 0 3px rgba(6, 187, 204, 0.1);
            outline: none;
        }
        
        .form-floating label {
            padding: 1rem 1rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Password Toggle Button */
        .password-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: #06BBCC;
        }
        
        .password-wrapper .form-control {
            padding-right: 45px;
        }
        
        /* Premium Button */
        .btn-register {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border: none;
            border-radius: 40px;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 187, 204, 0.4);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        /* Alert Styling */
        .alert-custom {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .divider span {
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        /* Feature List */
        .feature-list {
            list-style: none;
            padding-left: 0;
        }
        
        .feature-list li {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        
        .feature-list li i {
            width: 24px;
            color: #06BBCC;
            margin-right: 12px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .premium-card {
                border-radius: 20px;
            }
            .display-6 {
                font-size: 1.5rem;
            }
        }
        
        /* Animated Spinner */
        .spinner-border-sm {
            vertical-align: middle;
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
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="instructors.php" class="dropdown-item">Our Instructors</a>
                        <a href="testimonial.php" class="dropdown-item">testimonials</a>
                        <a href="apply-instructor.php" class="dropdown-item">Become an Instructor</a>
                    </div>
                </div>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
                <a href="login.php" class="nav-item nav-link">Login</a>
            </div>
            <a href="register.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block active">
                Join Now<i class="fa fa-arrow-right ms-3"></i>
            </a>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Premium Header Start -->
    <div class="container-fluid premium-header py-5 mb-5">
        <div class="container text-center position-relative" style="z-index: 2;">
            <h1 class="display-4 text-white fw-bold mb-3 animate__animated animate__fadeInDown">Start Your Journey</h1>
            <p class="text-white opacity-75 fs-5 mb-0">Join thousands of learners and transform your career</p>
        </div>
    </div>
    <!-- Premium Header End -->

    <!-- Register Form Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="premium-card">
                        <div class="row g-0">
                            <!-- Left Side - Benefits -->
                            <div class="col-lg-5 d-none d-lg-block" style="background: linear-gradient(135deg, #06BBCC, #0598A6);">
                                <div class="p-5 text-white h-100 d-flex flex-column justify-content-center">
                                    <div class="text-center mb-4">
                                        <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                            <i class="fa fa-graduation-cap fa-3x text-white"></i>
                                        </div>
                                        <h3 class="text-white fw-bold mb-3">Why Join Us?</h3>
                                    </div>
                                    <ul class="feature-list">
                                        <li><i class="fa fa-check-circle fa-lg"></i> Access 100+ expert-led courses</li>
                                        <li><i class="fa fa-check-circle fa-lg"></i> Learn at your own pace</li>
                                        <li><i class="fa fa-check-circle fa-lg"></i> Get certified upon completion</li>
                                        <li><i class="fa fa-check-circle fa-lg"></i> 24/7 support from instructors</li>
                                        <li><i class="fa fa-check-circle fa-lg"></i> Join a community of learners</li>
                                        <li><i class="fa fa-check-circle fa-lg"></i> Lifetime access to materials</li>
                                    </ul>
                                    <div class="mt-4 text-center">
                                        <div class="d-flex justify-content-center gap-3 mb-3">
                                            <div class="text-center">
                                                <h2 class="text-white fw-bold mb-0">10K+</h2>
                                                <small>Active Students</small>
                                            </div>
                                            <div class="text-center">
                                                <h2 class="text-white fw-bold mb-0">100+</h2>
                                                <small>Expert Courses</small>
                                            </div>
                                            <div class="text-center">
                                                <h2 class="text-white fw-bold mb-0">50+</h2>
                                                <small>Instructors</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Side - Registration Form -->
                            <div class="col-lg-7">
                                <div class="p-4 p-md-5">
                                    <div class="text-center mb-4">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                            <i class="fa fa-user-plus fa-2x text-primary"></i>
                                        </div>
                                        <h2 class="fw-bold mb-2" style="color: #181d38;">Create Free Account</h2>
                                        <p class="text-muted">Join our learning community today</p>
                                    </div>
                                    
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                                            <i class="fa fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($success): ?>
                                        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                                            <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="login.php" class="btn btn-register px-5">Login Now</a>
                                        </div>
                                    <?php else: ?>
                                        <form method="POST" action="" id="registerForm">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>" required>
                                                        <label for="first_name"><i class="fa fa-user me-1 text-primary"></i> First Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>" required>
                                                        <label for="last_name"><i class="fa fa-user me-1 text-primary"></i> Last Name</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-floating mt-3">
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                                                <label for="email"><i class="fa fa-envelope me-1 text-primary"></i> Email Address</label>
                                            </div>
                                            
                                            <div class="form-floating mt-3">
                                                <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Phone" value="<?php echo htmlspecialchars($formData['phone_number'] ?? ''); ?>">
                                                <label for="phone_number"><i class="fa fa-phone me-1 text-primary"></i> Phone Number (Optional)</label>
                                            </div>
                                            
                                            <div class="row g-3 mt-2">
                                                <div class="col-md-6">
                                                    <div class="password-wrapper">
                                                        <div class="form-floating">
                                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                                            <label for="password"><i class="fa fa-lock me-1 text-primary"></i> Password</label>
                                                        </div>
                                                        <button type="button" class="password-toggle" id="togglePassword">
                                                            <i class="fa fa-eye-slash"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted">Min. <?php echo PASSWORD_MIN_LENGTH; ?> characters</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="password-wrapper">
                                                        <div class="form-floating">
                                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                                                            <label for="confirm_password"><i class="fa fa-check-circle me-1 text-primary"></i> Confirm Password</label>
                                                        </div>
                                                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                                            <i class="fa fa-eye-slash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="alert alert-info rounded-4 mt-4" style="background: linear-gradient(135deg, #E6F8FA, #F0FBFC); border: none;">
                                                <div class="d-flex">
                                                    <i class="fa fa-info-circle fa-lg text-primary me-3"></i>
                                                    <div>
                                                        <small class="text-muted">By registering, you agree to our <a href="terms.php" class="text-primary">Terms of Service</a> and <a href="privacy.php" class="text-primary">Privacy Policy</a>. You'll receive a confirmation email after registration.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-register w-100 py-3 mt-3" id="registerBtn">
                                                <i class="fa fa-user-plus me-2"></i>Create Free Account
                                                <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
                                            </button>
                                        </form>
                                        
                                        <div class="divider mt-4">
                                            <span>Already have an account?</span>
                                        </div>
                                        
                                        <div class="text-center">
                                            <p class="mb-2">
                                                <a href="login.php" class="text-primary fw-bold text-decoration-none">
                                                    <i class="fa fa-sign-in-alt me-1"></i>Login to Your Account
                                                </a>
                                            </p>
                                            <p class="mb-0 text-muted small">
                                                Want to teach? <a href="apply-instructor.php" class="text-primary fw-bold text-decoration-none">Apply as Instructor</a>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Register Form End -->

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
        // Password Toggle Functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
        
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        if (toggleConfirmPassword && confirmPasswordInput) {
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
        
        // Form submission spinner
        const registerForm = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
        
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                const password = document.getElementById('password')?.value;
                const confirm = document.getElementById('confirm_password')?.value;
                
                if (password !== confirm) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                } else if (password && password.length < <?php echo PASSWORD_MIN_LENGTH; ?>) {
                    e.preventDefault();
                    alert('Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters!');
                } else {
                    registerBtn.disabled = true;
                    const spinner = registerBtn.querySelector('.spinner-border');
                    if (spinner) {
                        spinner.classList.remove('d-none');
                    }
                    registerBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Creating Account...';
                }
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