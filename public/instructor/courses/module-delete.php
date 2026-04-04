<?php
// Delete Module
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Module;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('instructor');

$moduleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($moduleId <= 0) {
    $_SESSION['_flash']['error'] = 'Invalid module ID.';
    header("Location: manage.php?id=$courseId");
    exit;
}

// Verify instructor owns this module's course
$moduleModel = new Module();
$module = $moduleModel->getById($moduleId);

if (!$module) {
    $_SESSION['_flash']['error'] = 'Module not found.';
    header("Location: manage.php?id=$courseId");
    exit;
}

// Verify course ownership
$courseModel = new SkillMaster\Models\Course();
$course = $courseModel->getById($module['course_id']);

if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
    $_SESSION['_flash']['error'] = 'You do not have permission to delete this module.';
    header("Location: manage.php?id=$courseId");
    exit;
}

// Delete module
$result = $moduleModel->delete($moduleId);

if ($result) {
    $_SESSION['_flash']['success'] = 'Module deleted successfully!';
} else {
    $_SESSION['_flash']['error'] = 'Failed to delete module.';
}

header("Location: manage.php?id=" . $module['course_id']);
exit;