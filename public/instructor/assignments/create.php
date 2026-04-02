<?php
// Create Assignment
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Assignment;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

RoleMiddleware::check('instructor');

$courseModel = new Course();
$assignmentModel = new Assignment();

$instructorId = $_SESSION['user_id'];

// Get instructor's courses for dropdown
$courses = $courseModel->getByInstructor($instructorId);

// Pre-select course if passed in URL
$selectedCourseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $courseId = (int)$_POST['course_id'];
        $title = Validation::sanitize($_POST['title'] ?? '');
        $description = Validation::sanitize($_POST['description'] ?? '');
        $due_date = $_POST['due_date'] ?? '';
        $max_points = (float)($_POST['max_points'] ?? 100);
        
        // Validate
        if (empty($title) || empty($due_date)) {
            $error = 'Please fill in all required fields.';
        } elseif ($max_points <= 0) {
            $error = 'Max points must be greater than 0.';
        } else {
            // Verify course belongs to instructor
            $courseExists = false;
            foreach ($courses as $course) {
                if ($course['id'] == $courseId) {
                    $courseExists = true;
                    break;
                }
            }
            
            if (!$courseExists) {
                $error = 'Invalid course selected.';
            } else {
                $data = [
                    'course_id' => $courseId,
                    'instructor_id' => $instructorId,
                    'title' => $title,
                    'description' => $description,
                    'due_date' => date('Y-m-d H:i:s', strtotime($due_date)),
                    'max_points' => $max_points
                ];
                
                $result = $assignmentModel->create($data);
                
                if ($result) {
                    $success = 'Assignment created successfully!';
                    // Clear form or redirect
                    echo '<script>setTimeout(function(){ window.location.href = "list.php"; }, 1500);</script>';
                } else {
                    $error = 'Failed to create assignment. Please try again.';
                }
            }
        }
    }
}

$page_title = 'Create Assignment - ' . APP_NAME;
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
    
    <!-- Flatpickr for date picking -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
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
                <a href="list.php" class="nav-item nav-link active">Assignments</a>
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
                    <h1 class="text-white">Create New Assignment</h1>
                    <p class="text-white mb-0">Create assignments for your students</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Form Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-light rounded p-5">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Course *</label>
                                <select class="form-select" name="course_id" required>
                                    <option value="">-- Select Course --</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>" <?php echo $selectedCourseId == $course['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['title']); ?> (<?php echo htmlspecialchars($course['code']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($courses)): ?>
                                    <small class="text-danger">You need to create a course first. <a href="../courses/create.php">Create a course</a></small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Assignment Title *</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea class="form-control" name="description" rows="6" placeholder="Describe the assignment requirements, instructions, and grading criteria..."></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Due Date & Time *</label>
                                    <input type="text" class="form-control datetimepicker" name="due_date" placeholder="YYYY-MM-DD HH:MM" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Maximum Points *</label>
                                    <input type="number" class="form-control" name="max_points" value="100" step="0.5" required>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>Note:</strong> Students will be able to submit their work until the due date. Late submissions will be marked accordingly.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="list.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Assignment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Form End -->

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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="../../assets/js/main.js"></script>
    <script>
        flatpickr(".datetimepicker", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            minDate: "today",
            time_24hr: true,
            altInput: true,
            altFormat: "F j, Y h:i K"
        });
    </script>
</body>
</html>