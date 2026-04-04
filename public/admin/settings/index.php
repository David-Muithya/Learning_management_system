<?php
// System Settings
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Helpers\Security;
use SkillMaster\Models\SystemSetting;

// Only admin can access
RoleMiddleware::check('admin');

$settingModel = new SystemSetting();
$message = '';
$messageType = '';

// Load settings
$settings = $settingModel->getAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid security token.';
        $messageType = 'danger';
    } else {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'setting_') === 0) {
                $settingKey = substr($key, 8);
                $settingModel->update($settingKey, $value);
            }
        }
        $message = 'Settings saved successfully!';
        $messageType = 'success';
        
        // Refresh settings
        $settings = $settingModel->getAll();
    }
}

$page_title = 'System Settings - ' . APP_NAME;
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
                <a href="../instructors/applications.php" class="nav-item nav-link">Applications</a>
                <a href="../courses/pending.php" class="nav-item nav-link">Pending Courses</a>
                <a href="../payments/pending.php" class="nav-item nav-link">Pending Payments</a>
                <a href="../enrollments/pending.php" class="nav-item nav-link">Enrollments</a>
                <a href="index.php" class="nav-item nav-link active">Settings</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="text-white">System Settings</h1>
                    <p class="text-white mb-0">Configure your learning management system</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <i class="fa fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="bg-light rounded p-5">
                <?php echo Security::csrfField(); ?>
                
                <h4 class="mb-4">General Settings</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="setting_site_name" name="setting_site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? APP_NAME); ?>">
                            <label for="setting_site_name">Site Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="setting_site_logo" name="setting_site_logo" value="<?php echo htmlspecialchars($settings['site_logo'] ?? ''); ?>">
                            <label for="setting_site_logo">Site Logo URL</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="setting_timezone" name="setting_timezone">
                                <option value="Africa/Nairobi" <?php echo ($settings['timezone'] ?? 'Africa/Nairobi') === 'Africa/Nairobi' ? 'selected' : ''; ?>>Africa/Nairobi</option>
                                <option value="UTC" <?php echo ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                <option value="America/New_York" <?php echo ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : ''; ?>>America/New_York</option>
                                <option value="Europe/London" <?php echo ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : ''; ?>>Europe/London</option>
                            </select>
                            <label for="setting_timezone">Timezone</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="setting_max_file_size" name="setting_max_file_size" value="<?php echo htmlspecialchars($settings['max_file_size'] ?? '10'); ?>">
                            <label for="setting_max_file_size">Max File Size (MB)</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="setting_allowed_file_types" name="setting_allowed_file_types" value="<?php echo htmlspecialchars($settings['allowed_file_types'] ?? 'pdf,doc,docx,jpg,png'); ?>">
                            <label for="setting_allowed_file_types">Allowed File Types</label>
                        </div>
                        <small class="text-muted">Comma-separated list of allowed file extensions</small>
                    </div>
                </div>
                
                <h4 class="mb-4 mt-4">Maintenance Mode</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="setting_maintenance_mode" name="setting_maintenance_mode" value="true" <?php echo ($settings['maintenance_mode'] ?? 'false') === 'true' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="setting_maintenance_mode">Enable Maintenance Mode</label>
                        </div>
                        <small class="text-muted">When enabled, only administrators can access the site.</small>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary py-3 px-5">
                    <i class="fa fa-save me-2"></i>Save Settings
                </button>
            </form>
            
        </div>
    </div>
    <!-- Content End -->

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

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>