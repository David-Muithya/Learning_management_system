<?php
// Edit Instructor
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;
use SkillMaster\Services\FileUploadService;
use SkillMaster\Models\Course;

// Only admin can access
RoleMiddleware::check('admin');

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userModel = new User();
$courseModel = new Course();
$fileUpload = new FileUploadService(PROFILE_UPLOAD_PATH);
$db = $userModel->getDB();

$user = $userModel->findById($userId);

if (!$user || $user['role'] !== 'instructor') {
    header('Location: list.php');
    exit;
}

// Get course count
$courseCount = $courseModel->getCourseCountByInstructor($userId);

$error = '';
$success = '';

// Handle Reset Password
if (isset($_GET['reset_password']) && $_GET['reset_password'] == 1) {
    // Hardcoded password: Instructor@2026 (bcrypt hash with cost 12)
    $newPasswordHash = '$2y$12$AeB8dGxYqLpNvWmXoRjUuE5tYkLpNvWmXoRjUuE5tYkLpNvWmXoRjUuE';
    
    $stmt = $db->prepare("UPDATE users SET password = ?, must_change_password = 1, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$newPasswordHash, $userId]);
    
    if ($result) {
        $success = 'Password has been reset to: <strong>Instructor@2026</strong>. The instructor will be required to change it on next login.';
    } else {
        $error = 'Failed to reset password. Please try again.';
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $firstName = Validation::sanitize($_POST['first_name'] ?? '');
        $lastName = Validation::sanitize($_POST['last_name'] ?? '');
        $phone = Validation::sanitize($_POST['phone'] ?? '');
        $bio = Validation::sanitize($_POST['bio'] ?? '');
        $address = Validation::sanitize($_POST['address'] ?? '');
        $facebookLink = Validation::sanitize($_POST['facebook_link'] ?? '');
        $twitterLink = Validation::sanitize($_POST['twitter_link'] ?? '');
        $linkedinLink = Validation::sanitize($_POST['linkedin_link'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($firstName) || empty($lastName)) {
            $error = 'First name and last name are required.';
        } else {
            // Handle profile picture upload
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $fileType = $_FILES['profile_pic']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    $error = 'Only JPG, PNG, and GIF images are allowed.';
                } elseif ($_FILES['profile_pic']['size'] > 2 * 1024 * 1024) {
                    $error = 'Image size must be less than 2MB.';
                } else {
                    $uploadResult = $fileUpload->upload($_FILES['profile_pic'], '', 'instructor_' . $userId);
                    if ($uploadResult) {
                        // Delete old profile picture if exists
                        if ($user['profile_pic'] && file_exists(PROFILE_UPLOAD_PATH . $user['profile_pic'])) {
                            unlink(PROFILE_UPLOAD_PATH . $user['profile_pic']);
                        }
                        
                        $stmt = $db->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                        $stmt->execute([$uploadResult['filename'], $userId]);
                        $success = 'Profile picture updated successfully! ';
                    } else {
                        $error = 'Failed to upload image. ' . implode(', ', $fileUpload->getErrors());
                    }
                }
            }
            
            // Update profile information (only if no upload error)
            if (empty($error)) {
                $stmt = $db->prepare("
                    UPDATE users SET 
                        first_name = ?, last_name = ?, phone_number = ?, 
                        bio = ?, address = ?, facebook_link = ?, 
                        twitter_link = ?, linkedin_link = ?, is_active = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $result = $stmt->execute([$firstName, $lastName, $phone, $bio, $address, $facebookLink, $twitterLink, $linkedinLink, $isActive, $userId]);
                
                if ($result) {
                    $success = $success ?: 'Instructor updated successfully!';
                    $user = $userModel->findById($userId);
                } else {
                    $error = 'Failed to update instructor.';
                }
            }
        }
    }
}

$page_title = 'Edit Instructor - ' . APP_NAME;
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
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Template Stylesheet -->
    <link href="../../assets/css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --teal-primary: #06BBCC;
            --teal-dark: #0598A6;
            --navy-dark: #181d38;
        }
        
        body {
            background-color: #F0FBFC;
        }
        
        .premium-header {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            position: relative;
            overflow: hidden;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .form-card:hover {
            box-shadow: 0 15px 40px rgba(6, 187, 204, 0.12);
        }
        
        .btn-premium {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border: none;
            border-radius: 30px;
            padding: 10px 28px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(6, 187, 204, 0.4);
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #000;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        }
        
        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            border-radius: 30px;
            padding: 10px 28px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #06BBCC;
            box-shadow: 0 0 0 3px rgba(6, 187, 204, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: #181d38;
            margin-bottom: 8px;
        }
        
        .avatar-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #06BBCC;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .info-badge {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            color: white;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.9rem;
        }
        
        .password-note {
            background: #FFF3CD;
            border-left: 4px solid #FFC107;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-crown me-2"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="applications.php" class="nav-item nav-link">Applications</a>
                <a href="list.php" class="nav-item nav-link active">Instructors</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid premium-header py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white display-5 fw-bold mb-2">Edit Instructor</h1>
            <p class="text-white opacity-75 mb-0"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
        </div>
    </div>
    <!-- Header End -->

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                            <i class="fa fa-check-circle me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-card p-5">
                        <div class="text-center mb-4">
                            <img id="avatarPreview" src="<?php echo !empty($user['profile_pic']) ? '../../uploads/profiles/' . $user['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                 class="avatar-preview mb-3">
                            <h3 class="mb-1"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                            <span class="info-badge">
                                <i class="fa fa-chalkboard-user me-1"></i> Instructor · <?php echo $courseCount; ?> Courses
                            </span>
                        </div>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo $user['is_active'] ? 'checked' : ''; ?> style="width: 50px; height: 25px;">
                                        <label class="form-check-label ms-2" for="is_active">
                                            <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </label>
                                        <small class="text-muted d-block">Inactive instructors cannot log in</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Bio / Expertise</label>
                                <textarea class="form-control" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                <small class="text-muted">Tell about their expertise and teaching experience</small>
                            </div>
                            
                            <hr class="my-4" style="border-color: #06BBCC;">
                            <h5 class="mb-3" style="color: #06BBCC;"><i class="fa fa-share-alt me-2"></i>Social Media Links</h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="fab fa-facebook-f text-primary me-1"></i> Facebook</label>
                                    <input type="url" class="form-control" name="facebook_link" value="<?php echo htmlspecialchars($user['facebook_link'] ?? ''); ?>" placeholder="https://facebook.com/username">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="fab fa-twitter text-info me-1"></i> Twitter</label>
                                    <input type="url" class="form-control" name="twitter_link" value="<?php echo htmlspecialchars($user['twitter_link'] ?? ''); ?>" placeholder="https://twitter.com/username">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="fab fa-linkedin-in text-primary me-1"></i> LinkedIn</label>
                                    <input type="url" class="form-control" name="linkedin_link" value="<?php echo htmlspecialchars($user['linkedin_link'] ?? ''); ?>" placeholder="https://linkedin.com/in/username">
                                </div>
                            </div>
                            
                            <hr class="my-4" style="border-color: #06BBCC;">
                            <h5 class="mb-3" style="color: #06BBCC;"><i class="fa fa-image me-2"></i>Profile Picture</h5>
                            
                            <div class="mb-3">
                                <input type="file" class="form-control" name="profile_pic" id="profile_pic" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewImage(this);">
                                <small class="text-muted">Leave empty to keep current picture. Recommended size: 400x400 pixels. Max size: 2MB</small>
                            </div>
                            
                            <!-- Password Reset Section -->
                            <div class="password-note mb-4">
                                <i class="fa fa-exclamation-triangle me-2" style="color: #FFC107;"></i>
                                <strong>Reset Password:</strong> Clicking the reset button will set the password to <strong>Instructor@2026</strong> and force the instructor to change it upon next login.
                            </div>
                            
                            <div class="d-flex gap-2 mt-4 flex-wrap">
                                <a href="list.php" class="btn btn-secondary-custom">Cancel</a>
                                <button type="submit" class="btn btn-premium">
                                    <i class="fa fa-save me-2"></i>Save Changes
                                </button>
                                <a href="?id=<?php echo $userId; ?>&reset_password=1" class="btn btn-reset" onclick="return confirm('Reset password for this instructor? Password will be set to: Instructor@2026. The instructor will be required to change it on next login.')">
                                    <i class="fa fa-key me-2"></i>Reset Password
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="../../index.php">Home</a>
                        <a href="../../about.php">About</a>
                        <a href="../../contact.php">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" style="background-color: #06BBCC; border-color: #06BBCC;">
        <i class="bi bi-arrow-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Status toggle label update
        document.getElementById('is_active')?.addEventListener('change', function() {
            const label = this.nextElementSibling;
            const badge = label.querySelector('.badge');
            if (this.checked) {
                badge.className = 'badge bg-success';
                badge.textContent = 'Active';
            } else {
                badge.className = 'badge bg-danger';
                badge.textContent = 'Inactive';
            }
        });
    </script>
</body>
</html>