<?php
// Delete Attendance Record
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Attendance;
use SkillMaster\Models\Course;

RoleMiddleware::check('instructor');

$attendanceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Verify course belongs to instructor
$courseModel = new Course();
$course = $courseModel->getById($courseId);

if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'You do not have permission to delete this attendance record.';
    header("Location: enrolled.php?course_id=$courseId");
    exit;
}

// Delete attendance
$attendanceModel = new Attendance();
$result = $attendanceModel->delete($attendanceId, $courseId, $studentId);

if ($result) {
    $_SESSION['success'] = 'Attendance record deleted successfully.';
} else {
    $_SESSION['error'] = 'Failed to delete attendance record.';
}

header("Location: attendance.php?student_id=$studentId&course_id=$courseId");
exit;