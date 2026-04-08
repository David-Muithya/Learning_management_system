<?php
// Edit Instructor Profile - Premium Version
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;
use SkillMaster\Services\FileUploadService;

RoleMiddleware::check('instructor');

$userModel = new User();
$userId = $_SESSION['user_id'];
$fileUpload = new FileUploadService(PROFILE_UPLOAD_PATH);

// Get user details
$user = $userModel->findById($userId);

if (!$user) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'];
        
        if ($action === 'update_profile') {
            $firstName = Validation::sanitize($_POST['first_name'] ?? '');
            $lastName = Validation::sanitize($_POST['last_name'] ?? '');
            $phoneNumber = Validation::sanitize($_POST['phone_number'] ?? '');
            $bio = Validation::sanitize($_POST['bio'] ?? '');
            $address = Validation::sanitize($_POST['address'] ?? '');
            $facebookLink = Validation::sanitize($_POST['facebook_link'] ?? '');
            $twitterLink = Validation::sanitize($_POST['twitter_link'] ?? '');
            $linkedinLink = Validation::sanitize($_POST['linkedin_link'] ?? '');
            
            if (empty($firstName) || empty($lastName)) {
                $error = 'First name and last name are required.';
            } else {
                $db = $userModel->getDB();
                $stmt = $db->prepare("
                    UPDATE users SET 
                        first_name = ?, last_name = ?, phone_number = ?, 
                        bio = ?, address = ?, facebook_link = ?, 
                        twitter_link = ?, linkedin_link = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $result = $stmt->execute([
                    $firstName, $lastName, $phoneNumber,
                    $bio, $address, $facebookLink,
                    $twitterLink, $linkedinLink, $userId
                ]);
                
                if ($result) {
                    $success = 'Profile updated successfully!';
                    // Refresh user data
                    $user = $userModel->findById($userId);
                    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                } else {
                    $error = 'Failed to update profile.';
                }
            }
        } elseif ($action === 'update_avatar') {
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $fileType = $_FILES['avatar']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    $error = 'Only JPG, PNG, and GIF images are allowed.';
                } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                    $error = 'Image size must be less than 2MB.';
                } else {
                    $uploadResult = $fileUpload->upload($_FILES['avatar'], '', 'instructor_' . $userId);
                    if ($uploadResult) {
                        // Delete old avatar if exists
                        if ($user['profile_pic'] && file_exists(PROFILE_UPLOAD_PATH . $user['profile_pic'])) {
                            unlink(PROFILE_UPLOAD_PATH . $user['profile_pic']);
                        }
                        
                        $db = $userModel->getDB();
                        $stmt = $db->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                        $result = $stmt->execute([$uploadResult['filename'], $userId]);
                        
                        if ($result) {
                            $success = 'Profile picture updated successfully!';
                            $user = $userModel->findById($userId);
                        } else {
                            $error = 'Failed to update profile picture.';
                        }
                    } else {
                        $error = 'Failed to upload image. ' . implode(', ', $fileUpload->getErrors());
                    }
                }
            } else {
                $error = 'Please select an image to upload.';
            }
        }
    }
}

$page_title = 'Edit Profile - ' . APP_NAME;
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
            --teal-light: #E6F8FA;
            --navy-dark: #181d38;
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
        
        /* Sidebar Navigation */
        .nav-pills-custom .nav-link {
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 8px;
            color: var(--navy-dark);
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-pills-custom .nav-link i {
            width: 24px;
            margin-right: 10px;
            color: var(--teal-primary);
        }
        
        .nav-pills-custom .nav-link:hover {
            background-color: var(--teal-light);
            transform: translateX(5px);
        }
        
        .nav-pills-custom .nav-link.active {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            color: white;
        }
        
        .nav-pills-custom .nav-link.active i {
            color: white;
        }
        
        /* Form Card */
        .form-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .form-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 50px rgba(6, 187, 204, 0.12);
        }
        
        /* Form Styling */
        .form-control {
            border-radius: 12px;
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
        
        .form-label {
            font-weight: 600;
            color: var(--navy-dark);
            margin-bottom: 8px;
        }
        
        /* Avatar Section */
        .avatar-container {
            position: relative;
            display: inline-block;
        }
        
        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--teal-primary);
            box-shadow: 0 10px 25px rgba(6, 187, 204, 0.2);
            transition: all 0.3s ease;
        }
        
        .avatar-overlay {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0.9;
        }
        
        .avatar-overlay:hover {
            transform: scale(1.1);
            opacity: 1;
        }
        
        .avatar-overlay i {
            color: white;
            font-size: 18px;
        }
        
        /* Buttons */
        .btn-save {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            border: none;
            border-radius: 40px;
            padding: 10px 28px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 187, 204, 0.4);
        }
        
        .btn-cancel {
            background: #6c757d;
            border: none;
            border-radius: 40px;
            padding: 10px 28px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        /* Social Inputs */
        .social-input {
            transition: all 0.3s ease;
        }
        
        .social-input:hover {
            transform: translateX(5px);
        }
        
        /* Divider */
        .divider-custom {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }
        
        .divider-custom::before,
        .divider-custom::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .divider-custom span {
            padding: 0 1rem;
            color: var(--teal-primary);
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Premium Navbar Start -->
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
                <a href="../courses/my-courses.php" class="nav-item nav-link">My Courses</a>
                <a href="../assignments/list.php" class="nav-item nav-link">Assignments</a>
                <a href="../students/enrolled.php" class="nav-item nav-link">Students</a>
                <a href="../announcements/list.php" class="nav-item nav-link">Announcements</a>
                <a href="index.php" class="nav-item nav-link active">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Premium Navbar End -->

    <!-- Premium Header Start -->
    <div class="container-fluid premium-header py-4 mb-5">
        <div class="container text-center position-relative" style="z-index: 2;">
            <h1 class="text-white display-5 fw-bold mb-2">Edit Profile</h1>
            <p class="text-white opacity-75 mb-0">Update your personal information and profile picture</p>
        </div>
    </div>
    <!-- Premium Header End -->

    <!-- Edit Profile Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="nav nav-pills flex-column nav-pills-custom">
                        <a class="nav-link <?php echo $activeTab === 'profile' ? 'active' : ''; ?>" href="?tab=profile">
                            <i class="fa fa-user-circle"></i> Profile Information
                        </a>
                        <a class="nav-link <?php echo $activeTab === 'avatar' ? 'active' : ''; ?>" href="?tab=avatar">
                            <i class="fa fa-image"></i> Profile Picture
                        </a>
                        <a class="nav-link" href="change-password.php">
                            <i class="fa fa-key"></i> Change Password
                        </a>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="col-lg-9">
                    <div class="form-card p-4 p-md-5">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                                <i class="fa fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($activeTab === 'profile'): ?>
                            <!-- Profile Information Form -->
                            <div class="text-center mb-4">
                                <div class="avatar-container mb-3">
                                    <img src="<?php echo !empty($user['profile_pic']) ? '../../uploads/profiles/' . $user['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                         class="avatar-preview" id="profilePreview">
                                    <div class="avatar-overlay" onclick="document.getElementById('avatar_upload').click();">
                                        <i class="fa fa-camera"></i>
                                    </div>
                                    <input type="file" id="avatar_upload" style="display: none;" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewAndUpload(this)">
                                </div>
                                <h3 class="mb-1"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                                <p class="text-muted"><i class="fa fa-envelope me-1"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            
                            <form method="POST" action="" id="profileForm">
                                <?php echo Security::csrfField(); ?>
                                <input type="hidden" name="action" value="update_profile">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fa fa-user me-1 text-primary"></i> First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fa fa-user me-1 text-primary"></i> Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fa fa-tag me-1 text-primary"></i> Username</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                        <small class="text-muted">Username cannot be changed</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fa fa-envelope me-1 text-primary"></i> Email Address</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                        <small class="text-muted">Email cannot be changed</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fa fa-phone me-1 text-primary"></i> Phone Number</label>
                                        <input type="tel" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><i class="fa fa-align-left me-1 text-primary"></i> Bio / Expertise</label>
                                        <textarea class="form-control" name="bio" rows="3" placeholder="Tell students about your expertise and teaching experience..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><i class="fa fa-map-marker-alt me-1 text-primary"></i> Address</label>
                                        <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="divider-custom">
                                            <span><i class="fa fa-share-alt me-2"></i>Social Media Links</span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label"><i class="fab fa-facebook-f text-primary me-1"></i> Facebook</label>
                                        <input type="url" class="form-control social-input" name="facebook_link" placeholder="https://facebook.com/username" value="<?php echo htmlspecialchars($user['facebook_link'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"><i class="fab fa-twitter text-info me-1"></i> Twitter</label>
                                        <input type="url" class="form-control social-input" name="twitter_link" placeholder="https://twitter.com/username" value="<?php echo htmlspecialchars($user['twitter_link'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"><i class="fab fa-linkedin-in text-primary me-1"></i> LinkedIn</label>
                                        <input type="url" class="form-control social-input" name="linkedin_link" placeholder="https://linkedin.com/in/username" value="<?php echo htmlspecialchars($user['linkedin_link'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-12">
                                        <hr>
                                        <div class="d-flex gap-2">
                                            <a href="index.php" class="btn btn-cancel">Cancel</a>
                                            <button type="submit" class="btn btn-save">
                                                <i class="fa fa-save me-2"></i>Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            
                        <?php elseif ($activeTab === 'avatar'): ?>
                            <!-- Avatar Upload Form -->
                            <div class="text-center">
                                <h5 class="mb-4" style="color: #06BBCC;"><i class="fa fa-image me-2"></i>Update Profile Picture</h5>
                                
                                <div class="avatar-container mb-4">
                                    <img id="avatarPreview" src="<?php echo !empty($user['profile_pic']) ? '../../uploads/profiles/' . $user['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                         class="avatar-preview" style="width: 180px; height: 180px;">
                                </div>
                                
                                <p class="text-muted mb-4">Current Profile Picture</p>
                                
                                <form method="POST" action="" enctype="multipart/form-data" id="avatarForm">
                                    <?php echo Security::csrfField(); ?>
                                    <input type="hidden" name="action" value="update_avatar">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Choose New Image</label>
                                        <input type="file" class="form-control" name="avatar" id="avatarInput" accept="image/jpeg,image/jpg,image/png,image/gif" required onchange="previewImage(this)">
                                        <small class="text-muted">Recommended size: 400x400 pixels. Max size: 2MB. Formats: JPG, PNG, GIF</small>
                                    </div>
                                    
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="index.php" class="btn btn-cancel">Cancel</a>
                                        <button type="submit" class="btn btn-save">
                                            <i class="fa fa-upload me-2"></i>Upload New Picture
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Profile Content End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
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
    <script src="../../assets/js/main.js"></script>
    
    <script>
        // Preview image before upload
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Preview and auto-upload for quick avatar change from profile tab
        function previewAndUpload(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
                
                // Auto-submit the avatar form
                var formData = new FormData();
                formData.append('avatar', input.files[0]);
                formData.append('action', 'update_avatar');
                formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                }).then(response => response.text())
                  .then(() => {
                      location.reload();
                  });
            }
        }
        
        // Smooth back to top
        document.querySelector('.back-to-top')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>