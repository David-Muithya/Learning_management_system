<?php
// My Courses List
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;

RoleMiddleware::check('instructor');

$courseModel = new Course();
$courses = $courseModel->getByInstructor($_SESSION['user_id']);

$page_title = 'My Courses - ' . APP_NAME;
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
                <a href="my-courses.php" class="nav-item nav-link active">My Courses</a>
                <a href="../assignments/list.php" class="nav-item nav-link">Assignments</a>
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
                    <h1 class="text-white">My Courses</h1>
                    <p class="text-white mb-0">Manage and organize your courses</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-end">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fa fa-plus-circle me-2"></i>Create New Course
                    </a>
                </div>
            </div>
            
            <?php if (empty($courses)): ?>
                <div class="text-center py-5">
                    <i class="fa fa-book-open fa-4x text-muted mb-3"></i>
                    <h4>No courses yet</h4>
                    <p class="text-muted">Start creating your first course and share your knowledge.</p>
                    <a href="create.php" class="btn btn-primary">Create Your First Course</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="bg-light rounded overflow-hidden h-100">
                                <div class="position-relative">
                                    <img class="img-fluid w-100" src="<?php echo !empty($course['thumbnail']) ? '../../uploads/courses/' . $course['thumbnail'] : '../../assets/img/course-1.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($course['title']); ?>" style="height: 180px; object-fit: cover;">
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-<?php 
                                        echo $course['status'] === 'published' ? 'success' : 
                                             ($course['status'] === 'pending_approval' ? 'warning' : 
                                             ($course['status'] === 'rejected' ? 'danger' : 'secondary')); 
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $course['status'])); ?>
                                    </span>
                                </div>
                                <div class="p-4">
                                    <h5 class="mb-2"><?php echo htmlspecialchars($course['title']); ?></h5>
                                    <p class="text-muted small">Code: <?php echo htmlspecialchars($course['code']); ?></p>
                                    <p class="mb-2"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                                    <div class="d-flex justify-content-between mb-3">
                                        <small><i class="fa fa-users me-1"></i> <?php echo $course['enrollment_count']; ?> students</small>
                                        <small><i class="fa fa-tag me-1"></i> <?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="manage.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">Manage</a>
                                        <a href="edit.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <?php if ($course['status'] === 'draft'): ?>
                                            <a href="submit.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Submit this course for approval?')">Submit</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>