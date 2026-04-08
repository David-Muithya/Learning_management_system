<?php
// Login Page - For Existing Students and Instructors
require_once __DIR__ . '/../config/config.php';
use SkillMaster\Auth\Authenticator;
use SkillMaster\Auth\RoleMiddleware;

// Redirect if already logged in as the same role
if (isset($_SESSION['user_id']) && ($_GET['role'] ?? 'user') === ($_SESSION['user_role'] ?? 'user')) {
    header('Location: ' . ($_SESSION['user_role'] === 'instructor' ? '/instructor/' : '/student/'));
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $auth = new Authenticator();
        $result = $auth->login($email, $password);

        if ($result['success']) {
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                // Prevent admin login through public portal
                $auth->logout();
                $error = 'Admin accounts must sign in through the private admin portal: /admin/login.php';
            } else {
                // Determine redirect based on user role
                $role_param = $_POST['role'] ?? $_GET['role'] ?? $_SESSION['user_role'];
                if ($role_param === 'student' || ($_SESSION['user_role'] ?? '') === 'student') {
                    $redirect_url = STUDENT_URL . '/index.php';
                } elseif ($role_param === 'instructor' || ($_SESSION['user_role'] ?? '') === 'instructor') {
                    $redirect_url = INSTRUCTOR_URL . '/index.php';
                } else {
                    $redirect_url = STUDENT_URL . '/index.php'; // Default fallback
                }

                // Redirect immediately
                header('Location: ' . $redirect_url);
                exit;
            }
        } else {
            $error = $result['message'];
        }
    }
}

$page_title = 'Login - ' . APP_NAME;
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
        
        /* Premium Login Card */
        .login-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(6, 187, 204, 0.25);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(6, 187, 204, 0.35);
        }
        
        /* Premium Header */
        .login-header {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 6s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        
        .login-icon {
            background: rgba(255,255,255,0.2);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
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
        }
        
        /* Password Toggle Button */
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
        
        /* Position relative for password wrapper */
        .password-wrapper {
            position: relative;
        }
        
        .password-wrapper .form-control {
            padding-right: 45px;
        }
        
        /* Premium Button */
        .btn-login {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border: none;
            border-radius: 40px;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: white;
            width: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 187, 204, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Links */
        .forgot-link {
            color: #06BBCC;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-link:hover {
            color: #0598A6;
            text-decoration: underline;
        }
        
        .register-link {
            color: #06BBCC;
            font-weight: 700;
            text-decoration: none;
        }
        
        .register-link:hover {
            text-decoration: underline;
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
        
        /* Alert Styling */
        .alert-custom {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

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
                        <a href="testimonials.php" class="dropdown-item">testimonials</a>
                        <a href="apply-instructor.php" class="dropdown-item">Become an Instructor</a>
                    </div>
                </div>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
                <a href="login.php" class="nav-item nav-link active">Login</a>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $_SESSION['user_role'] === 'instructor' ? '/instructor/' : '/student/'; ?>" class="btn btn-primary py-4 px-lg-5">
                    Go to Dashboard<i class="fa fa-arrow-right ms-3"></i>
                </a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary py-4 px-lg-5">
                    Join Now<i class="fa fa-arrow-right ms-3"></i>
                </a>
            <?php endif; ?>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Login Form Start -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="login-card">
                    <div class="login-header">
                        <div class="login-icon">
                            <i class="fa fa-graduation-cap fa-3x text-white"></i>
                        </div>
                        <h2 class="text-white fw-bold mb-0">Welcome Back!</h2>
                        <p class="text-white opacity-75 mb-0">Login to continue your learning journey</p>
                    </div>
                    
                    <div class="p-4 p-md-5">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                            <?php if (isset($_GET['role']) && in_array($_GET['role'], ['student', 'instructor'], true)): ?>
                                <input type="hidden" name="role" value="<?php echo htmlspecialchars($_GET['role']); ?>">
                            <?php endif; ?>
                            
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                <label for="email"><i class="fa fa-envelope me-2 text-primary"></i>Email address</label>
                            </div>
                            
                            <div class="form-floating mb-2 password-wrapper">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password"><i class="fa fa-lock me-2 text-primary"></i>Password</label>
                                <button type="button" class="password-toggle" id="togglePassword">
                                    <i class="fa fa-eye-slash"></i>
                                </button>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember" style="border-radius: 4px; border-color: #06BBCC;">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                            </div>
                            
                            <button type="submit" class="btn-login" id="loginBtn">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status" id="loginSpinner"></span>
                                <i class="fa fa-sign-in-alt me-2"></i>Login to Account
                            </button>
                        </form>
                        
                        <div class="divider">
                            <span>New to <?php echo APP_NAME; ?>?</span>
                        </div>
                        
                        <div class="text-center">
                            <p class="mb-2">
                                <a href="register.php" class="register-link">
                                    <i class="fa fa-user-plus me-1"></i>Create New Account
                                </a>
                            </p>
                            <p class="mb-0 text-muted small">
                                Want to teach? <a href="apply-instructor.php" class="register-link">Apply as Instructor</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Form End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="index.php">Home</a>
                        <a href="about.php">About</a>
                        <a href="contact.php">Help</a>
                        <a href="privacy.php">Privacy</a>
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
    <script src="assets/js/main.js"></script>
    
    <script>
        // Password Toggle Functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                // Toggle password visibility
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
        
        // Show spinner on form submit
        const loginForm = document.querySelector('form');
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                const loginBtn = document.getElementById('loginBtn');
                const loginSpinner = document.getElementById('loginSpinner');
                
                loginBtn.disabled = true;
                loginSpinner.classList.remove('d-none');
                
                // Change button text
                const btnIcon = loginBtn.querySelector('.fa-sign-in-alt');
                if (btnIcon) {
                    btnIcon.style.display = 'none';
                }
                loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Logging in...';
            });
        }
        
        // Smooth back to top
        document.querySelector('.back-to-top')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>