<?php
// Add Material to Module
// Disable all error output to prevent JSON corruption
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any stray output
ob_start();

// Set JSON header immediately
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../../../config/config.php';
    use SkillMaster\Auth\RoleMiddleware;
    use SkillMaster\Models\Course;
    use SkillMaster\Models\Module;
    use SkillMaster\Models\Material;
    use SkillMaster\Helpers\Security;
    use SkillMaster\Helpers\Validation;
    use SkillMaster\Services\FileUploadService;

    // Check if instructor is logged in
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit;
    }

    // Verify CSRF token
    $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!Security::verifyCsrfToken($csrfToken)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
        exit;
    }

    // Get and validate input
    $moduleId = isset($_POST['module_id']) ? (int)$_POST['module_id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $type = isset($_POST['type']) ? $_POST['type'] : 'document';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $contentUrl = isset($_POST['content_url']) ? trim($_POST['content_url']) : '';

    // Validate required fields
    if (empty($title)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Material title is required.']);
        exit;
    }

    if ($moduleId <= 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid module ID.']);
        exit;
    }

    if (empty($type)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Material type is required.']);
        exit;
    }

    // Get module
    $moduleModel = new Module();
    $module = $moduleModel->getById($moduleId);

    if (!$module) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Module not found.']);
        exit;
    }

    // Verify instructor owns this course
    $courseModel = new Course();
    $course = $courseModel->getById($module['course_id']);

    if (!$course) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Course not found.']);
        exit;
    }

    if ($course['instructor_id'] != $_SESSION['user_id']) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'You do not have permission to add materials to this course.']);
        exit;
    }

    // Check material upload path is defined
    if (!defined('MATERIAL_UPLOAD_PATH')) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Upload directory is not configured.']);
        exit;
    }

    // Create uploads directory if it doesn't exist
    if (!is_dir(MATERIAL_UPLOAD_PATH)) {
        if (!mkdir(MATERIAL_UPLOAD_PATH, 0777, true)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to create upload directory.']);
            exit;
        }
    }

    $fileUpload = new FileUploadService(MATERIAL_UPLOAD_PATH);
    $filePath = null;

    // Handle file upload for non-link types
    if ($type !== 'link') {
        if (!isset($_FILES['file'])) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Please upload a file.']);
            exit;
        }

        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds maximum upload size (php.ini limit)',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds maximum upload size (form limit)',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload blocked by extension'
            ];
            $errorMsg = $uploadErrors[$_FILES['file']['error']] ?? 'Unknown upload error';
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'File upload error: ' . $errorMsg]);
            exit;
        }

        $uploadResult = $fileUpload->upload($_FILES['file'], '', 'material_' . time());
        if ($uploadResult) {
            $filePath = $uploadResult['path'];
        } else {
            $errors = $fileUpload->getErrors();
            $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Unknown upload error';
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to upload file: ' . $errorMsg]);
            exit;
        }
    } elseif ($type === 'link') {
        if (empty($contentUrl)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Please enter a URL for the link.']);
            exit;
        }
        if (!filter_var($contentUrl, FILTER_VALIDATE_URL)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid URL format.']);
            exit;
        }
    }

    // Create material
    $materialModel = new Material();
    $result = $materialModel->create($moduleId, $title, $type, $filePath, $contentUrl, $description);

    if ($result) {
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Material added successfully!'
        ]);
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to add material.']);
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

exit;
}
}