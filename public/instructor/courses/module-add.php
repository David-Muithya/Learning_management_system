<?php
// Add Module via AJAX
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Module;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

// Only instructors can access
RoleMiddleware::check('instructor');

// Set header for JSON response
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Verify CSRF token
if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
    exit;
}

// Get and validate input
$courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
$title = Validation::sanitize($_POST['title'] ?? '');
$description = Validation::sanitize($_POST['description'] ?? '');

if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Module title is required.']);
    exit;
}

if ($courseId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid course ID.']);
    exit;
}

// Verify instructor owns this course
$courseModel = new SkillMaster\Models\Course();
$course = $courseModel->getById($courseId);

if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to add modules to this course.']);
    exit;
}

// Create module
$moduleModel = new Module();
$moduleId = $moduleModel->create($courseId, $title, $description);

if ($moduleId) {
    echo json_encode([
        'success' => true,
        'message' => 'Module created successfully!',
        'module_id' => $moduleId,
        'module_title' => $title,
        'module_description' => $description
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create module. Please try again.']);
}