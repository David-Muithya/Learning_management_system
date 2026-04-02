<?php
// List Announcements
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Announcement;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('instructor');

$courseModel = new Course();
$announcementModel = new Announcement();

$instructorId = $_SESSION['user_id'];
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get instructor's courses for dropdown
$courses = $courseModel->getByInstructor($instructorId);

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (!Security::verifyCsrfToken($_GET['csrf_token'] ?? '')) {
        $deleteError = 'Invalid security token.';
    } else {
        $announcementId = (int)$_GET['id'];
        if ($announcementModel->delete($announcementId, $courseId)) {
            $deleteSuccess = 'Announcement deleted successfully.';
            // Refresh page
            header("refresh:1;url=list.php?course_id=$courseId");
        } else {
            $deleteError = 'Failed to delete announcement.';
        }
    }
}

// Get announcements
$announcements = [];
$totalAnnouncements = 0;

if ($courseId > 0) {
    $announcements = $announcementModel->getByCourse($courseId, $perPage, $offset);
    $totalAnnouncements = $announcementModel->getCountByCourse($courseId);
}

$totalPages = ceil($totalAnnouncements / $perPage);

$page_title = 'Course Announcements - ' . APP_NAME;
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
                <a href="../students/enrolled.php" class="nav-item nav-link">Students</a>
                <a href="list.php" class="nav-item nav-link active">Announcements</a>
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
                    <h1 class="text-white">Course Announcements</h1>
                    <p class="text-white mb-0">Manage announcements for your courses</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <?php if (isset($deleteSuccess)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-2"></i><?php echo $deleteSuccess; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($deleteError)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i><?php echo $deleteError; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Course Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="" class="d-flex gap-2">
                        <select name="course_id" class="form-select" onchange="this.form.submit()">
                            <option value="0">-- Select Course --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo $courseId == $course['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <a href="create.php<?php echo $courseId ? '?course_id=' . $courseId : ''; ?>" class="btn btn-primary">
                            <i class="fa fa-plus-circle me-2"></i>New Announcement
                        </a>
                    </form>
                </div>
            </div>
            
            <?php if (empty($courses)): ?>
                <div class="text-center py-5">
                    <i class="fa fa-bullhorn fa-4x text-muted mb-3"></i>
                    <h4>No Courses Yet</h4>
                    <p class="text-muted">You haven't created any courses yet. Create a course to post announcements.</p>
                    <a href="../courses/create.php" class="btn btn-primary">Create Your First Course</a>
                </div>
            <?php elseif ($courseId === 0): ?>
                <div class="text-center py-5">
                    <i class="fa fa-bullhorn fa-4x text-muted mb-3"></i>
                    <h4>Select a Course</h4>
                    <p class="text-muted">Please select a course from the dropdown above to view announcements.</p>
                </div>
            <?php elseif (empty($announcements)): ?>
                <div class="text-center py-5">
                    <i class="fa fa-bullhorn fa-4x text-muted mb-3"></i>
                    <h4>No Announcements Yet</h4>
                    <p class="text-muted">You haven't posted any announcements for this course.</p>
                    <a href="create.php?course_id=<?php echo $courseId; ?>" class="btn btn-primary">Post Your First Announcement</a>
                </div>
            <?php else: ?>
                <!-- Announcements List -->
                <div class="row">
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="col-12 mb-4">
                            <div class="bg-light rounded p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="mb-2">
                                            <?php 
                                            $priorityColors = [
                                                'urgent' => 'danger',
                                                'high' => 'warning',
                                                'normal' => 'info',
                                                'low' => 'secondary'
                                            ];
                                            $priorityIcons = [
                                                'urgent' => 'fa-exclamation-triangle',
                                                'high' => 'fa-arrow-up',
                                                'normal' => 'fa-info-circle',
                                                'low' => 'fa-arrow-down'
                                            ];
                                            ?>
                                            <span class="badge bg-<?php echo $priorityColors[$announcement['priority']]; ?> me-2">
                                                <i class="fa <?php echo $priorityIcons[$announcement['priority']]; ?> me-1"></i>
                                                <?php echo ucfirst($announcement['priority']); ?>
                                            </span>
                                            <small class="text-muted">
                                                <i class="fa fa-calendar-alt me-1"></i>
                                                Posted on <?php echo date('M d, Y g:i A', strtotime($announcement['created_at'])); ?>
                                            </small>
                                        </div>
                                        <h5 class="mb-2"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                                        <div class="mb-3">
                                            <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fa fa-user me-1"></i> Posted by: <?php echo htmlspecialchars($announcement['posted_by_name']); ?>
                                            <?php if ($announcement['updated_at'] != $announcement['created_at']): ?>
                                                <span class="ms-3"><i class="fa fa-edit me-1"></i> Edited: <?php echo date('M d, Y', strtotime($announcement['updated_at'])); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="delete.php?id=<?php echo $announcement['id']; ?>&course_id=<?php echo $courseId; ?>&csrf_token=<?php echo Security::generateCsrfToken(); ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this announcement?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item"><a class="page-link" href="?course_id=<?php echo $courseId; ?>&page=<?php echo $page - 1; ?>">Previous</a></li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?course_id=<?php echo $courseId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item"><a class="page-link" href="?course_id=<?php echo $courseId; ?>&page=<?php echo $page + 1; ?>">Next</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
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