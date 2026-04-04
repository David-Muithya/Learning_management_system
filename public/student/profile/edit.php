<?php
// Edit Student Profile
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;
use SkillMaster\Services\FileUploadService;

RoleMiddleware::check('student');

$userModel = new User();
$userId = $_SESSION['user_id'];
$fileUpload = new FileUploadService(PROFILE_UPLOAD_PATH);

$user = $userModel->findById($userId);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $firstName = Validation::sanitize($_POST['first_name'] ?? '');
        $lastName = Validation::sanitize($_POST['last_name'] ?? '');
        $phoneNumber = Validation::sanitize($_POST['phone_number'] ?? '');
        $address = Validation::sanitize($_POST['address'] ?? '');
        $bio = Validation::sanitize($_POST['bio'] ?? '');
        $facebookLink = Validation::sanitize($_POST['facebook_link'] ?? '');
        $twitterLink = Validation::sanitize($_POST['twitter_link'] ?? '');
        $linkedinLink = Validation::sanitize($_POST['linkedin_link'] ?? '');
        
        if (empty($firstName) || empty($lastName)) {
            $error = 'First name and last name are required.';
        } else {
            // Handle profile picture upload
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $fileType = $_FILES['profile_pic']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    $error = 'Only JPG, PNG, and GIF images are allowed.';
                } elseif ($_FILES['profile_pic']['size'] > 2 * 1024 * 1024) {
                    $error = 'Image size must be less than 2MB.';
                } else {
                    $uploadResult = $fileUpload->upload($_FILES['profile_pic'], '', 'student_' . $userId);
                    if ($uploadResult) {
                        // Delete old profile picture if exists
                        if ($user['profile_pic'] && file_exists(PROFILE_UPLOAD_PATH . $user['profile_pic'])) {
                            unlink(PROFILE_UPLOAD_PATH . $user['profile_pic']);
                        }
                        $profilePic = $uploadResult['filename'];
                        $userModel->updateProfilePicture($userId, $profilePic);
                        $success = 'Profile picture updated successfully! ';
                    } else {
                        $error = 'Failed to upload image. ' . implode(', ', $fileUpload->getErrors());
                    }
                }
            }
            
            // Update profile information (only if no upload error)
            if (empty($error)) {
                $profileData = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone_number' => $phoneNumber,
                    'address' => $address,
                    'bio' => $bio,
                    'facebook_link' => $facebookLink,
                    'twitter_link' => $twitterLink,
                    'linkedin_link' => $linkedinLink
                ];
                
                $result = $userModel->updateProfile($userId, $profileData);
                
                if ($result) {
                    $success = $success ?: 'Profile updated successfully!';
                    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                    $user = $userModel->findById($userId);
                } else {
                    $error = 'Failed to update profile.';
                }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
        .bg-primary-custom { background-color: #06BBCC; }
        .btn-primary-custom { background-color: #06BBCC; border-color: #06BBCC; }
        .btn-primary-custom:hover { background-color: #0598A6; border-color: #0598A6; }
        .text-primary-custom { color: #06BBCC; }
        .border-primary-custom { border-color: #06BBCC; }
        .profile-img-container { position: relative; display: inline-block; }
        .profile-img-container:hover .overlay {
            opacity: 1;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(6, 187, 204, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }
        .overlay i {
            color: white;
            font-size: 30px;
        }
    </style>
</head>
<body style="background-color: #F0FBFC;">

    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="index.php" class="nav-item nav-link active">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5" style="background-color: #06BBCC !important;">
        <div class="container text-center">
            <h1 class="text-white">Edit Profile</h1>
            <p class="text-white mb-0">Update your personal information</p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="bg-light rounded p-5 shadow">
                        <div class="text-center mb-4">
                            <div class="profile-img-container" style="position: relative; display: inline-block;">
                                <img id="profilePreview" src="<?php echo !empty($user['profile_pic']) ? '../../uploads/profiles/' . $user['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                     class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #06BBCC;">
                                <div class="overlay" onclick="document.getElementById('profile_pic_input').click();">
                                    <i class="fa fa-camera"></i>
                                </div>
                            </div>
                            <h4 class="mt-2" style="color: #181d38;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                            <p class="text-muted"><i class="fa fa-envelope me-1"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        
                        <form method="POST" enctype="multipart/form-data" id="profileForm">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" style="color: #181d38;">First Name</label>
                                    <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" style="color: #181d38;">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required style="border-radius: 8px;">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #181d38;">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background-color: #e9ecef; border-radius: 8px;">
                                <small class="text-muted">Email cannot be changed</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #181d38;">Phone Number</label>
                                <input type="tel" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" style="border-radius: 8px;">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #181d38;">Address</label>
                                <textarea class="form-control" name="address" rows="2" style="border-radius: 8px;"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #181d38;">Bio</label>
                                <textarea class="form-control" name="bio" rows="4" placeholder="Tell us about yourself..." style="border-radius: 8px;"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                <small class="text-muted">Brief description about yourself (optional)</small>
                            </div>
                            
                            <hr style="border-color: #06BBCC;">
                            <h5 class="mb-3" style="color: #06BBCC;"><i class="fa fa-share-alt me-2"></i>Social Media Links</h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="fab fa-facebook-f text-primary me-1"></i> Facebook</label>
                                    <input type="url" class="form-control" name="facebook_link" value="<?php echo htmlspecialchars($user['facebook_link'] ?? ''); ?>" placeholder="https://facebook.com/yourprofile" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="fab fa-twitter text-info me-1"></i> Twitter</label>
                                    <input type="url" class="form-control" name="twitter_link" value="<?php echo htmlspecialchars($user['twitter_link'] ?? ''); ?>" placeholder="https://twitter.com/yourprofile" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="fab fa-linkedin-in text-primary me-1"></i> LinkedIn</label>
                                    <input type="url" class="form-control" name="linkedin_link" value="<?php echo htmlspecialchars($user['linkedin_link'] ?? ''); ?>" placeholder="https://linkedin.com/in/yourprofile" style="border-radius: 8px;">
                                </div>
                            </div>
                            
                            <hr style="border-color: #06BBCC;">
                            <h5 class="mb-3" style="color: #06BBCC;"><i class="fa fa-image me-2"></i>Profile Picture</h5>
                            
                            <div class="mb-3">
                                <input type="file" class="form-control" id="profile_pic_input" name="profile_pic" accept="image/jpeg,image/jpg,image/png,image/gif" style="border-radius: 8px;" onchange="previewImage(this);">
                                <small class="text-muted">Allowed formats: JPG, PNG, GIF. Max size: 2MB</small>
                            </div>
                            
                            <div class="d-flex gap-2 mt-4">
                                <a href="index.php" class="btn btn-secondary px-4 py-2" style="border-radius: 8px;">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 py-2" style="background-color: #06BBCC; border-color: #06BBCC; border-radius: 8px;">
                                    <i class="fa fa-save me-2"></i>Save Changes
                                </button>
                                <a href="change-password.php" class="btn btn-outline-primary px-4 py-2" style="border-color: #06BBCC; color: #06BBCC; border-radius: 8px;">
                                    <i class="fa fa-key me-2"></i>Change Password
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
    
    <script>
        // Preview image before upload
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Form validation
        document.getElementById('profileForm')?.addEventListener('submit', function(e) {
            var fileInput = document.getElementById('profile_pic_input');
            if (fileInput.files.length > 0) {
                var fileSize = fileInput.files[0].size;
                var maxSize = 2 * 1024 * 1024; // 2MB
                if (fileSize > maxSize) {
                    e.preventDefault();
                    alert('File size must be less than 2MB!');
                }
            }
        });
    </script>
</body>
</html>