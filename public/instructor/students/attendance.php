<?php
// Student Attendance Tracking
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Enrollment;
use SkillMaster\Models\Attendance;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

RoleMiddleware::check('instructor');

$courseModel = new Course();
$enrollmentModel = new Enrollment();
$attendanceModel = new Attendance();

$instructorId = $_SESSION['user_id'];
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$success = '';
$error = '';

// Verify course belongs to instructor
$course = $courseModel->getById($courseId);
if (!$course || $course['instructor_id'] != $instructorId) {
    header('Location: enrolled.php');
    exit;
}

// Get student info
$db = $enrollmentModel->getDB();
$stmt = $db->prepare("SELECT first_name, last_name, email, profile_pic FROM users WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: enrolled.php?course_id=' . $courseId);
    exit;
}

// Handle attendance marking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $date = $_POST['date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'present';
        
        $result = $attendanceModel->markAttendance($courseId, $studentId, $date, $status, $instructorId);
        
        if ($result) {
            $success = 'Attendance marked successfully!';
        } else {
            $error = 'Failed to mark attendance.';
        }
    }
}

// Get attendance records for this student in this course
$attendanceRecords = $attendanceModel->getByStudentAndCourse($studentId, $courseId);
$attendanceStats = $attendanceModel->getStats($courseId, $studentId);

// Get dates for current month
$daysInMonth = date('t', strtotime($month . '-01'));
$firstDay = date('N', strtotime($month . '-01'));
$monthName = date('F Y', strtotime($month . '-01'));

$page_title = 'Student Attendance - ' . APP_NAME;
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
    
    <style>
        .attendance-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .attendance-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(6, 187, 204, 0.1);
        }
        .status-present { background-color: #198754; color: white; }
        .status-absent { background-color: #dc3545; color: white; }
        .status-late { background-color: #ffc107; color: #000; }
        .status-excused { background-color: #0dcaf0; color: #000; }
        .calendar-day {
            min-height: 80px;
            border: 1px solid #dee2e6;
            padding: 5px;
            position: relative;
            background-color: white;
        }
        .calendar-day:hover {
            background-color: #F0FBFC;
        }
        .calendar-header {
            background-color: #06BBCC;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
        .present-badge {
            position: absolute;
            bottom: 5px;
            right: 5px;
            font-size: 20px;
        }
        .present-badge i { color: #198754; }
        .absent-badge i { color: #dc3545; }
        .late-badge i { color: #ffc107; }
        .excused-badge i { color: #0dcaf0; }
    </style>
</head>
<body style="background-color: #F0FBFC;">

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
                <a href="../courses/my-courses.php" class="nav-item nav-link">My Courses</a>
                <a href="../assignments/list.php" class="nav-item nav-link">Assignments</a>
                <a href="enrolled.php" class="nav-item nav-link">Students</a>
                <a href="attendance.php?student_id=<?php echo $studentId; ?>&course_id=<?php echo $courseId; ?>" class="nav-item nav-link active">Attendance</a>
                <a href="../profile/index.php" class="nav-item nav-link">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid py-4 mb-5" style="background-color: #06BBCC;">
        <div class="container text-center">
            <h1 class="text-white">Student Attendance</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?> - <?php echo htmlspecialchars($course['title']); ?></p>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Success/Error Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-2"></i><?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm attendance-card">
                        <i class="fa fa-calendar-check fa-2x text-success mb-2"></i>
                        <h3 class="mb-0"><?php echo $attendanceStats['present']; ?></h3>
                        <p class="text-muted mb-0">Present</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm attendance-card">
                        <i class="fa fa-calendar-times fa-2x text-danger mb-2"></i>
                        <h3 class="mb-0"><?php echo $attendanceStats['absent']; ?></h3>
                        <p class="text-muted mb-0">Absent</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm attendance-card">
                        <i class="fa fa-clock fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0"><?php echo $attendanceStats['late']; ?></h3>
                        <p class="text-muted mb-0">Late</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded p-3 text-center shadow-sm attendance-card">
                        <i class="fa fa-check-double fa-2x text-info mb-2"></i>
                        <h3 class="mb-0"><?php echo $attendanceStats['excused']; ?></h3>
                        <p class="text-muted mb-0">Excused</p>
                    </div>
                </div>
            </div>
            
            <!-- Mark Attendance Form -->
            <div class="bg-white rounded p-4 shadow-sm mb-4">
                <h5 class="mb-3" style="color: #06BBCC;"><i class="fa fa-pen me-2"></i>Mark Attendance</h5>
                <form method="POST" action="" class="row g-3">
                    <?php echo Security::csrfField(); ?>
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="present">✅ Present</option>
                            <option value="absent">❌ Absent</option>
                            <option value="late">⏰ Late</option>
                            <option value="excused">📝 Excused</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" style="background-color: #06BBCC; border-color: #06BBCC;">
                            <i class="fa fa-save me-2"></i>Save Attendance
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Attendance Records Table -->
            <div class="bg-white rounded p-4 shadow-sm">
                <h5 class="mb-3" style="color: #06BBCC;"><i class="fa fa-list me-2"></i>Attendance Records</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #06BBCC; color: white;">
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Marked By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($attendanceRecords)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No attendance records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($attendanceRecords as $record): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($record['date'])); ?></td>
                                        <td>
                                            <span class="badge status-<?php echo $record['status']; ?> px-3 py-2">
                                                <?php echo ucfirst($record['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $stmt = $db->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                                            $stmt->execute([$record['marked_by']]);
                                            $marker = $stmt->fetch();
                                            echo htmlspecialchars($marker['first_name'] . ' ' . $marker['last_name']);
                                            ?>
                                         </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteAttendance(<?php echo $record['id']; ?>, '<?php echo $record['date']; ?>')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                         </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Back Button -->
            <div class="text-center mt-4">
                <a href="enrolled.php?course_id=<?php echo $courseId; ?>" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to Students
                </a>
            </div>
            
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
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" style="background-color: #06BBCC; border-color: #06BBCC;">
        <i class="bi bi-arrow-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    
    <script>
        function deleteAttendance(id, date) {
            if (confirm(`Delete attendance record for ${date}? This action cannot be undone.`)) {
                window.location.href = `attendance-delete.php?id=${id}&student_id=<?php echo $studentId; ?>&course_id=<?php echo $courseId; ?>`;
            }
        }
    </script>
</body>
</html>