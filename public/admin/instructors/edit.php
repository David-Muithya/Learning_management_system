<?php
// Edit Instructor
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

RoleMiddleware::check('admin');

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userModel = new User();
$user = $userModel->findById($userId);

if (!$user || $user['role'] !== 'instructor') {
    header('Location: list.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $firstName = Validation::sanitize($_POST['first_name'] ?? '');
        $lastName = Validation::sanitize($_POST['last_name'] ?? '');
        $phone = Validation::sanitize($_POST['phone'] ?? '');
        $bio = Validation::sanitize($_POST['bio'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($firstName) || empty($lastName)) {
            $error = 'First name and last name are required.';
        } else {
            $stmt = $userModel->db->prepare("
                UPDATE users SET 
                    first_name = ?, last_name = ?, phone_number = ?, 
                    bio = ?, is_active = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $result = $stmt->execute([$firstName, $lastName, $phone, $bio, $isActive, $userId]);
            
            if ($result) {
                $success = 'Instructor updated successfully!';
                $user = $userModel->findById($userId);
            } else {
                $error = 'Failed to update instructor.';
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
                <a href="applications.php" class="nav-item nav-link">Applications</a>
                <a href="list.php" class="nav-item nav-link active">Instructors</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Edit Instructor</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
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
                    
                    <div class="bg-light rounded p-4">
                        <form method="POST">
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
                                <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Bio / Expertise</label>
                                <textarea class="form-control" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?php echo $user['is_active'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">Active Account</label>
                                <small class="text-muted d-block">Inactive instructors cannot log in</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="list.php" class="btn btn-secondary">Cancel</a>
                                <a href="reset-password.php?id=<?php echo $userId; ?>" class="btn btn-outline-warning">Reset Password</a>
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