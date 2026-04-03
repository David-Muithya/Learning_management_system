<?php
// Create Course Module
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Module;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

RoleMiddleware::check('instructor');

$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$courseModel = new Course();
$moduleModel = new Module();

// Verify course belongs to instructor
$course = $courseModel->getById($courseId);
if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
    header('Location: ../courses/my-courses.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $title = Validation::sanitize($_POST['title'] ?? '');
        $description = Validation::sanitize($_POST['description'] ?? '');
        
        if (empty($title)) {
            $error = 'Please enter a module title.';
        } else {
            $result = $moduleModel->create($courseId, $title, $description);
            
            if ($result) {
                $success = 'Module created successfully!';
                // Clear form
                $_POST = [];
            } else {
                $error = 'Failed to create module.';
            }
        }
    }
}

// Get existing modules
$modules = $moduleModel->getByCourse($courseId);

$page_title = 'Create Module - ' . APP_NAME;
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
                <a href="../courses/my-courses.php" class="nav-item nav-link">My Courses</a>
                <a href="../courses/manage.php?id=<?php echo $courseId; ?>" class="nav-item nav-link">Manage Course</a>
                <a href="create.php?course_id=<?php echo $courseId; ?>" class="nav-item nav-link active">Create Module</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Create Course Module</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($course['title']); ?></p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="bg-light rounded p-5">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Module Title *</label>
                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                <small class="text-muted">Brief description of what this module covers</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="../courses/manage.php?id=<?php echo $courseId; ?>" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Module</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="bg-light rounded p-5">
                        <h5 class="mb-3">Existing Modules</h5>
                        <?php if (empty($modules)): ?>
                            <p class="text-muted text-center py-3">No modules created yet.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($modules as $index => $module): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Module <?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($module['title']); ?>
                                                <br>
                                                <small class="text-muted"><?php echo $module['material_count']; ?> materials</small>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <a href="edit.php?id=<?php echo $module['id']; ?>&course_id=<?php echo $courseId; ?>" class="btn btn-outline-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="delete.php?id=<?php echo $module['id']; ?>&course_id=<?php echo $courseId; ?>" class="btn btn-outline-danger" onclick="return confirm('Delete this module? All materials will also be deleted.')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-3">
                                <a href="../materials/upload.php?course_id=<?php echo $courseId; ?>" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="fa fa-upload me-1"></i> Add Materials
                                </a>
                            </div>
                        <?php endif; ?>
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