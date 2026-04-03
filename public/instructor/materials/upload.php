<?php
// Upload Course Material
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Module;
use SkillMaster\Models\Material;
use SkillMaster\Helpers\Security;
use SkillMaster\Services\FileUploadService;

RoleMiddleware::check('instructor');

$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$moduleId = isset($_GET['module_id']) ? (int)$_GET['module_id'] : 0;
$courseModel = new Course();
$moduleModel = new Module();
$materialModel = new Material();
$fileUpload = new FileUploadService(MATERIAL_UPLOAD_PATH);

// Verify course belongs to instructor
$course = $courseModel->getById($courseId);
if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
    header('Location: ../courses/my-courses.php');
    exit;
}

// Get modules for this course
$modules = $moduleModel->getByCourse($courseId);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $type = $_POST['type'] ?? 'document';
        $moduleId = (int)($_POST['module_id'] ?? 0);
        $contentUrl = $_POST['content_url'] ?? null;
        
        if (empty($title)) {
            $error = 'Please enter a title.';
        } elseif ($moduleId <= 0) {
            $error = 'Please select a module.';
        } else {
            $filePath = null;
            
            // Handle file upload for non-link types
            if ($type !== 'link' && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $fileUpload->upload($_FILES['file'], '', 'material_' . time());
                if ($uploadResult) {
                    $filePath = $uploadResult['path'];
                } else {
                    $error = 'Failed to upload file. ' . implode(', ', $fileUpload->getErrors());
                }
            } elseif ($type === 'link' && empty($contentUrl)) {
                $error = 'Please enter a URL for the link.';
            }
            
            if (empty($error)) {
                $result = $materialModel->create($moduleId, $title, $type, $filePath, $contentUrl, $description);
                
                if ($result) {
                    $success = 'Material uploaded successfully!';
                    // Clear form
                    $_POST = [];
                } else {
                    $error = 'Failed to upload material.';
                }
            }
        }
    }
}

$page_title = 'Upload Material - ' . APP_NAME;
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
                <a href="upload.php?course_id=<?php echo $courseId; ?>" class="nav-item nav-link active">Upload Material</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Upload Course Material</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($course['title']); ?></p>
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
                        <form method="POST" enctype="multipart/form-data">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Module *</label>
                                <select class="form-select" name="module_id" required>
                                    <option value="">-- Select Module --</option>
                                    <?php foreach ($modules as $module): ?>
                                        <option value="<?php echo $module['id']; ?>" <?php echo $moduleId == $module['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($module['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($modules)): ?>
                                    <small class="text-danger">No modules found. <a href="../modules/create.php?course_id=<?php echo $courseId; ?>">Create a module first</a></small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Material Title *</label>
                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Material Type</label>
                                <select class="form-select" name="type" id="material_type" required>
                                    <option value="document">Document (PDF, DOC, PPT)</option>
                                    <option value="video">Video (MP4, YouTube)</option>
                                    <option value="audio">Audio (MP3)</option>
                                    <option value="image">Image (JPG, PNG)</option>
                                    <option value="link">External Link</option>
                                </select>
                            </div>
                            
                            <div class="mb-3" id="file_upload_div">
                                <label class="form-label fw-bold">Upload File</label>
                                <input type="file" class="form-control" name="file">
                                <small class="text-muted">Max size: 10MB. Allowed: PDF, DOC, MP4, MP3, JPG, PNG</small>
                            </div>
                            
                            <div class="mb-3" id="url_div" style="display: none;">
                                <label class="form-label fw-bold">External URL</label>
                                <input type="url" class="form-control" name="content_url" placeholder="https://...">
                                <small class="text-muted">Link to YouTube video, article, or external resource</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description (Optional)</label>
                                <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="../courses/manage.php?id=<?php echo $courseId; ?>" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Upload Material</button>
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
        const materialType = document.getElementById('material_type');
        const fileUploadDiv = document.getElementById('file_upload_div');
        const urlDiv = document.getElementById('url_div');
        
        materialType.addEventListener('change', function() {
            if (this.value === 'link') {
                fileUploadDiv.style.display = 'none';
                urlDiv.style.display = 'block';
                document.querySelector('input[name="file"]').required = false;
                document.querySelector('input[name="content_url"]').required = true;
            } else {
                fileUploadDiv.style.display = 'block';
                urlDiv.style.display = 'none';
                document.querySelector('input[name="file"]').required = true;
                document.querySelector('input[name="content_url"]').required = false;
            }
        });
    </script>
</body>
</html>