<?php
// Pending Assignments Page
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Assignment;

// Only students can access
RoleMiddleware::check('student');

$assignmentModel = new Assignment();
$studentId = $_SESSION['user_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$pendingAssignments = $assignmentModel->getForStudent($studentId, 'pending', $page, 10);
$overdueAssignments = $assignmentModel->getForStudent($studentId, 'overdue', 1, 10);
$submittedAssignments = $assignmentModel->getForStudent($studentId, 'submitted', 1, 10);

$page_title = 'My Assignments - ' . APP_NAME;
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
                <a href="../courses/enrolled.php" class="nav-item nav-link">My Courses</a>
                <a href="pending.php" class="nav-item nav-link active">Assignments</a>
                <a href="../grades/index.php" class="nav-item nav-link">Grades</a>
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
                    <h1 class="text-white">My Assignments</h1>
                    <p class="text-white mb-0">Track and submit your assignments</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <div class="row g-4">
                <!-- Pending Assignments -->
                <div class="col-lg-8">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Pending Assignments</h5>
                        
                        <?php if (empty($pendingAssignments['assignments'])): ?>
                            <div class="text-center py-4">
                                <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted">No pending assignments. Great job!</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($pendingAssignments['assignments'] as $assignment): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                                <p class="mb-1 small text-muted"><?php echo htmlspecialchars($assignment['course_title']); ?></p>
                                                <small class="text-danger"><i class="fa fa-calendar-alt me-1"></i>Due: <?php echo date('M d, Y g:i A', strtotime($assignment['due_date'])); ?></small>
                                                <p class="mt-2 mb-0 small"><?php echo htmlspecialchars(substr($assignment['description'], 0, 100)); ?>...</p>
                                            </div>
                                            <a href="submit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-primary btn-sm">Submit</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($pendingAssignments['total_pages'] > 1): ?>
                                <nav class="mt-3">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($pendingAssignments['current_page'] > 1): ?>
                                            <li class="page-item"><a class="page-link" href="?page=<?php echo $pendingAssignments['current_page'] - 1; ?>">Previous</a></li>
                                        <?php endif; ?>
                                        <?php for ($i = 1; $i <= $pendingAssignments['total_pages']; $i++): ?>
                                            <li class="page-item <?php echo $i == $pendingAssignments['current_page'] ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <?php if ($pendingAssignments['current_page'] < $pendingAssignments['total_pages']): ?>
                                            <li class="page-item"><a class="page-link" href="?page=<?php echo $pendingAssignments['current_page'] + 1; ?>">Next</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Overdue Assignments -->
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3">Overdue</h5>
                        <?php if (empty($overdueAssignments['assignments'])): ?>
                            <p class="text-muted mb-0">No overdue assignments</p>
                        <?php else: ?>
                            <?php foreach ($overdueAssignments['assignments'] as $assignment): ?>
                                <div class="border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="small"><?php echo htmlspecialchars($assignment['title']); ?></span>
                                        <a href="submit.php?id=<?php echo $assignment['id']; ?>" class="text-danger small">Submit Late</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Submitted Count -->
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Submitted</h5>
                        <h2 class="mb-0"><?php echo $submittedAssignments['total']; ?></h2>
                        <p class="text-muted">Assignments submitted</p>
                        <a href="submitted.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
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
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="../../index.php">Home</a>
                        <a href="../../about.php">About</a>
                        <a href="../../contact.php">Contact</a>
                    </div>
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