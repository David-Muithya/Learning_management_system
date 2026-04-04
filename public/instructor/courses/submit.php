<?php
// Submit Course for Approval
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Module;

RoleMiddleware::check('instructor');

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$courseModel = new Course();
$moduleModel = new Module();

if ($courseId === 0) {
    $_SESSION['error'] = 'Invalid course ID.';
    header('Location: my-courses.php');
    exit;
}

// Get the course
$course = $courseModel->getById($courseId);

// Verify instructor owns this course
if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'You do not have permission to submit this course.';
    header('Location: my-courses.php');
    exit;
}

// Check if course is in draft status
if ($course['status'] !== 'draft') {
    $_SESSION['error'] = 'Only draft courses can be submitted for approval.';
    header('Location: manage.php?id=' . $courseId);
    exit;
}

// Verify course has at least one module
$modules = $moduleModel->getByCourse($courseId);
if (empty($modules)) {
    $_SESSION['error'] = 'Your course must have at least one module before submission.';
    header('Location: manage.php?id=' . $courseId);
    exit;
}

// Submit for approval
if ($courseModel->submitForApproval($courseId)) {
    $_SESSION['success'] = 'Course submitted successfully! Your course is now pending admin approval.';
} else {
    $_SESSION['error'] = 'Failed to submit course. Please try again.';
}

header('Location: manage.php?id=' . $courseId);
exit;
