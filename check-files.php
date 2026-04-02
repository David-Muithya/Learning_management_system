<?php
$files = [
    // Config files
    'config/bootstrap.php',
    'config/constants.php',
    'config/config.php',
    'config/database.php',
    'config/session.php',
    
    // Auth files
    'src/Auth/Authenticator.php',
    'src/Auth/PasswordReset.php',
    'src/Auth/RoleMiddleware.php',
    
    // Helper files
    'src/Helpers/Pagination.php',
    'src/Helpers/Security.php',
    'src/Helpers/Session.php',
    'src/Helpers/Validation.php',
    
    // Service files
    'src/Services/ActivityLogger.php',
    'src/Services/CacheService.php',
    'src/Services/EmailService.php',
    'src/Services/FileUploadService.php',
    'src/Services/NotificationService.php',
    
    // Model files
    'src/Models/User.php',
    'src/Models/Course.php',
    'src/Models/Enrollment.php',
    'src/Models/Assignment.php',
    'src/Models/Submission.php',
    'src/Models/Grade.php',
    'src/Models/MockPayment.php',
    'src/Models/InstructorApplication.php',
    'src/Models/ContactMessage.php',
    'src/Models/SystemSetting.php',
    'src/Models/Announcement.php',
    
    // Public pages
    'public/index.php',
    'public/login.php',
    'public/register.php',
    'public/courses.php',
    'public/contact.php',
    'public/about.php',
    'public/instructors.php',
    'public/apply-instructor.php',
    'public/course-details.php',
    'public/forgot-password.php',
    'public/reset-password.php',
    'public/logout.php',
    
    // Admin files
    'public/admin/index.php',
    'public/admin/instructors/applications.php',
    'public/admin/courses/pending.php',
    'public/admin/enrollments/pending.php',
    'public/admin/settings/index.php',
    
    // Instructor files
    'public/instructor/index.php',
    'public/instructor/courses/create.php',
    'public/instructor/courses/my-courses.php',
    'public/instructor/courses/manage.php',
    'public/instructor/assignments/create.php',
    'public/instructor/assignments/list.php',
    'public/instructor/assignments/grade.php',
    'public/instructor/assignments/view.php',
    'public/instructor/students/enrolled.php',
    'public/instructor/students/progress.php',
    'public/instructor/announcements/create.php',
    'public/instructor/announcements/list.php',
    'public/instructor/profile/index.php',
    'public/instructor/profile/edit.php',
    'public/instructor/profile/change-password.php',
    
    // Student files
    'public/student/index.php',
    'public/student/courses/enrolled.php',
    'public/student/courses/browse.php',
    'public/student/courses/details.php',
    'public/student/assignments/pending.php',
    'public/student/assignments/submit.php',
    'public/student/grades/index.php',
    'public/student/payments/mock.php',
    'public/student/profile/index.php',
];

echo "<h1>File Verification Report</h1>";
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Status</th><th>File Path</th></tr>";

$missing = [];
$present = [];

foreach ($files as $file) {
    if (file_exists($file)) {
        $present[] = $file;
        echo "<tr style='color:green'><td>✅</td><td>$file</td></tr>";
    } else {
        $missing[] = $file;
        echo "<tr style='color:red'><td>❌</td><td>$file</td></tr>";
    }
}

echo "</table>";
echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>Total files checked: " . count($files) . "</p>";
echo "<p>✅ Present: " . count($present) . "</p>";
echo "<p>❌ Missing: " . count($missing) . "</p>";

if (!empty($missing)) {
    echo "<h3>Missing Files:</h3><ul>";
    foreach ($missing as $m) {
        echo "<li>$m</li>";
    }
    echo "</ul>";
}