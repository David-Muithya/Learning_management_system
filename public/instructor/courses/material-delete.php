<?php
// Delete Material
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Material;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('instructor');

$materialId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($materialId <= 0) {
    $_SESSION['_flash']['error'] = 'Invalid material ID.';
    header("Location: manage.php?id=$courseId");
    exit;
}

// Verify instructor owns this material's course
$materialModel = new Material();
$material = $materialModel->getById($materialId);

if (!$material) {
    $_SESSION['_flash']['error'] = 'Material not found.';
    header("Location: manage.php?id=$courseId");
    exit;
}

// Get module and course
$moduleModel = new SkillMaster\Models\Module();
$module = $moduleModel->getById($material['module_id']);

if ($module) {
    $courseModel = new Course();
    $course = $courseModel->getById($module['course_id']);
    
    if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
        $_SESSION['_flash']['error'] = 'You do not have permission to delete this material.';
        header("Location: manage.php?id=" . ($courseId ?: ($module['course_id'] ?? 0)));
        exit;
    }
}

// Delete material
$result = $materialModel->delete($materialId);

if ($result) {
    $_SESSION['_flash']['success'] = 'Material deleted successfully!';
} else {
    $_SESSION['_flash']['error'] = 'Failed to delete material.';
}

$redirectCourseId = $courseId ?: ($module['course_id'] ?? 0);
header("Location: manage.php?id=$redirectCourseId");
exit;