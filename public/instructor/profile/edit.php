<?php
// Edit Instructor Profile
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
</head>
<body>

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
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
                <a href="index.php" class="nav-item nav-link">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-4 mb-5" style="background-color: #06BBCC !important;">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="text-white">Edit Profile</h1>
                    <p class="text-white mb-0">Update your personal information</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Edit Profile Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="list-group mb-4">
                        <a href="?tab=profile" class="list-group-item list-group-item-action <?php echo $activeTab === 'profile' ? 'active' : ''; ?>">
                            <i class="fa fa-user me-2"></i>Profile Information
                        </a>
                        <a href="?tab=avatar" class="list-group-item list-group-item-action <?php echo $activeTab === 'avatar' ? 'active' : ''; ?>">
                            <i class="fa fa-image me-2"></i>Profile Picture
                        </a>
                        <a href="change-password.php" class="list-group-item list-group-item-action">
                            <i class="fa fa-key me-2"></i>Change Password
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-9">
                    <div class="bg-light rounded p-4">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($activeTab === 'profile'): ?>
                            <!-- Profile Information Form -->
                            <h5 class="mb-4">Personal Information</h5>
                            <form method="POST" action="">
                                <?php echo Security::csrfField(); ?>
                                <input type="hidden" name="action" value="update_profile">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                        <small class="text-muted">Username cannot be changed</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                        <small class="text-muted">Email cannot be changed</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Bio / Expertise</label>
                                        <textarea class="form-control" name="bio" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                        <small class="text-muted">Tell students about your expertise and teaching experience</small>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <h6 class="mb-3">Social Media Links</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Facebook</label>
                                        <input type="url" class="form-control" name="facebook_link" placeholder="https://facebook.com/username" value="<?php echo htmlspecialchars($user['facebook_link'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Twitter</label>
                                        <input type="url" class="form-control" name="twitter_link" placeholder="https://twitter.com/username" value="<?php echo htmlspecialchars($user['twitter_link'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">LinkedIn</label>
                                        <input type="url" class="form-control" name="linkedin_link" placeholder="https://linkedin.com/in/username" value="<?php echo htmlspecialchars($user['linkedin_link'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <button type="submit" class="btn btn-primary" style="background-color: #06BBCC; border-color: #06BBCC;">Save Changes</button>
                                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </form>
                            
                        <?php elseif ($activeTab === 'avatar'): ?>
                            <!-- Avatar Upload Form -->
                            <h5 class="mb-4">Update Profile Picture</h5>
                            <div class="text-center mb-4">
                                <img src="<?php echo !empty($user['profile_pic']) ? '../../uploads/profiles/' . $user['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                     class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #06BBCC;">
                                <h6>Current Profile Picture</h6>
                            </div>
                            
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?php echo Security::csrfField(); ?>
                                <input type="hidden" name="action" value="update_avatar">
                                
                                <div class="mb-3">
                                    <label class="form-label">Upload New Image</label>
                                    <input type="file" class="form-control" name="avatar" accept="image/jpeg,image/jpg,image/png,image/gif" required>
                                    <small class="text-muted">Recommended size: 400x400 pixels. Max size: 2MB. Formats: JPG, PNG, GIF</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary" style="background-color: #06BBCC; border-color: #06BBCC;">Upload New Picture</button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </form>
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

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>