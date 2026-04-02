<?php
// View Enrolled Students
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Enrollment;
use SkillMaster\Helpers\Pagination;

RoleMiddleware::check('instructor');

$courseModel = new Course();
$enrollmentModel = new Enrollment();

$instructorId = $_SESSION['user_id'];
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get instructor's courses for dropdown
$courses = $courseModel->getByInstructor($instructorId);

// If no course selected and there are courses, select the first one
if ($courseId === 0 && !empty($courses)) {
    $courseId = $courses[0]['id'];
}

// Get enrolled students
$studentsData = ['students' => [], 'total' => 0];
if ($courseId > 0) {
    $studentsData = $enrollmentModel->getStudentsByCourse($courseId, $instructorId, $page, 20);
}

$page_title = 'Enrolled Students - ' . APP_NAME;
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
</head>
<body>

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
                <a href="enrolled.php" class="nav-item nav-link active">Students</a>
                <a href="../profile/index.php" class="nav-item nav-link">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="text-white">Enrolled Students</h1>
                    <p class="text-white mb-0">View and manage students enrolled in your courses</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Course Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="" class="d-flex gap-2">
                        <select name="course_id" class="form-select" onchange="this.form.submit()">
                            <option value="0">-- Select Course --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo $courseId == $course['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?> (<?php echo $course['enrollment_count']; ?> students)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
            
            <?php if (empty($courses)): ?>
                <div class="text-center py-5">
                    <i class="fa fa-chalkboard-user fa-4x text-muted mb-3"></i>
                    <h4>No Courses Yet</h4>
                    <p class="text-muted">You haven't created any courses yet. Create a course to see enrolled students.</p>
                    <a href="../courses/create.php" class="btn btn-primary">Create Your First Course</a>
                </div>
            <?php elseif ($courseId === 0): ?>
                <div class="text-center py-5">
                    <i class="fa fa-graduation-cap fa-4x text-muted mb-3"></i>
                    <h4>Select a Course</h4>
                    <p class="text-muted">Please select a course from the dropdown above to view enrolled students.</p>
                </div>
            <?php elseif (empty($studentsData['students'])): ?>
                <div class="text-center py-5">
                    <i class="fa fa-users fa-4x text-muted mb-3"></i>
                    <h4>No Students Enrolled</h4>
                    <p class="text-muted">No students have enrolled in this course yet.</p>
                    <a href="../courses/manage.php?id=<?php echo $courseId; ?>" class="btn btn-primary">Manage Course</a>
                </div>
            <?php else: ?>
                <!-- Students Table -->
                <div class="bg-light rounded p-4">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Enrolled Date</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $counter = ($studentsData['current_page'] - 1) * $studentsData['per_page'] + 1;
                                foreach ($studentsData['students'] as $student): 
                                ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo !empty($student['profile_pic']) ? '../../uploads/profiles/' . $student['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                                     class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['phone_number'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($student['enrolled_at'])); ?></td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" style="width: 0%"></div>
                                            </div>
                                            <small class="text-muted">0% Complete</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="progress.php?student_id=<?php echo $student['student_id']; ?>&course_id=<?php echo $courseId; ?>" class="btn btn-outline-primary" title="View Progress">
                                                    <i class="fa fa-chart-line"></i>
                                                </a>
                                                <a href="attendance.php?student_id=<?php echo $student['student_id']; ?>&course_id=<?php echo $courseId; ?>" class="btn btn-outline-info" title="Attendance">
                                                    <i class="fa fa-calendar-check"></i>
                                                </a>
                                                <a href="mailto:<?php echo $student['email']; ?>" class="btn btn-outline-secondary" title="Send Message">
                                                    <i class="fa fa-envelope"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($studentsData['total_pages'] > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($studentsData['current_page'] > 1): ?>
                                    <li class="page-item"><a class="page-link" href="?course_id=<?php echo $courseId; ?>&page=<?php echo $studentsData['current_page'] - 1; ?>">Previous</a></li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $studentsData['total_pages']; $i++): ?>
                                    <li class="page-item <?php echo $i == $studentsData['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="?course_id=<?php echo $courseId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($studentsData['current_page'] < $studentsData['total_pages']): ?>
                                    <li class="page-item"><a class="page-link" href="?course_id=<?php echo $courseId; ?>&page=<?php echo $studentsData['current_page'] + 1; ?>">Next</a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
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

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>