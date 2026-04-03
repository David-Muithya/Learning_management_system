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
        
        if (empty($firstName) || empty($lastName)) {
            $error = 'First name and last name are required.';
        } else {
            // Handle profile picture upload
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $fileUpload->upload($_FILES['profile_pic'], '', 'student_' . $userId);
                if ($uploadResult) {
                    if ($user['profile_pic'] && file_exists(PROFILE_UPLOAD_PATH . $user['profile_pic'])) {
                        unlink(PROFILE_UPLOAD_PATH . $user['profile_pic']);
                    }
                    $profilePic = $uploadResult['filename'];
                    $stmt = $userModel->db->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                    $stmt->execute([$profilePic, $userId]);
                }
            }
            
            $stmt = $userModel->db->prepare("
                UPDATE users SET 
                    first_name = ?, last_name = ?, phone_number = ?, 
                    address = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $result = $stmt->execute([$firstName, $lastName, $phoneNumber, $address, $userId]);
            
            if ($result) {
                $success = 'Profile updated successfully!';
                $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                $user = $userModel->findById($userId);
            } else {
                $error = 'Failed to update profile.';
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
</head>
<body>

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

    <div class="container-fluid bg-primary py-4 mb-5">
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
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <div class="bg-light rounded p-5">
                        <div class="text-center mb-4">
                            <img src="<?php echo !empty($user['profile_pic']) ? '../../uploads/profiles/' . $user['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                 class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                            <p class="text-muted"><?php echo ucfirst($user['role']); ?> Account</p>
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
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" name="profile_pic" accept="image/*">
                                <small class="text-muted">Leave empty to keep current picture</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="change-password.php" class="btn btn-outline-primary">Change Password</a>
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
</body>
</html>