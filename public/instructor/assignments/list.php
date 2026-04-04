<?php
// List All Assignments
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Assignment;
use SkillMaster\Helpers\Pagination;

RoleMiddleware::check('instructor');

$assignmentModel = new Assignment();
$instructorId = $_SESSION['user_id'];

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Get assignments
$assignmentsData = $assignmentModel->getByInstructor($instructorId, $page, 10);

// Filter by status if needed
$filteredAssignments = $assignmentsData['assignments'];
if ($status !== 'all') {
    $filteredAssignments = array_filter($filteredAssignments, function($a) use ($status) {
        if ($status === 'active') return strtotime($a['due_date']) > time();
        if ($status === 'past') return strtotime($a['due_date']) < time();
        return true;
    });
}

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
                    <h1 class="text-white">My Assignments</h1>
                    <p class="text-white mb-0">Manage and grade student assignments</p>
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
                        <i class="fa fa-plus-circle me-2"></i>Create New Assignment
                    </a>
                </div>
            </div>
            
            <!-- Status Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'all' ? 'active' : ''; ?>" href="?status=all">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'active' ? 'active' : ''; ?>" href="?status=active">Active</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'past' ? 'active' : ''; ?>" href="?status=past">Past Due</a>
                </li>
            </ul>
            
            <?php if (empty($filteredAssignments)): ?>
                <div class="text-center py-5">
                    <i class="fa fa-tasks fa-4x text-muted mb-3"></i>
                    <h4>No assignments found</h4>
                    <p class="text-muted">Create your first assignment to get started.</p>
                    <a href="create.php" class="btn btn-primary">Create Assignment</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover bg-light rounded">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Assignment</th>
                                <th>Course</th>
                                <th>Due Date</th>
                                <th>Max Points</th>
                                <th>Submissions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filteredAssignments as $assignment): ?>
                                <?php
                                $isPast = strtotime($assignment['due_date']) < time();
                                $totalStudents = $assignment['total_students'] ?? 0;
                                $submissionCount = $assignment['submission_count'] ?? 0;
                                $gradedCount = $assignment['graded_count'] ?? 0;
                                $submissionRate = $totalStudents > 0 ? round(($submissionCount / $totalStudents) * 100) : 0;
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($assignment['title']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($assignment['course_title']); ?></td>
                                    <td>
                                        <?php echo date('M d, Y g:i A', strtotime($assignment['due_date'])); ?>
                                        <?php if ($isPast): ?>
                                            <span class="badge bg-danger ms-1">Past Due</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $assignment['max_points']; ?></td>
                                    <td>
                                        <?php echo $submissionCount; ?> / <?php echo $totalStudents; ?>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar bg-success" style="width: <?php echo $submissionRate; ?>%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($gradedCount > 0): ?>
                                            <span class="badge bg-info"><?php echo $gradedCount; ?> Graded</span>
                                        <?php endif; ?>
                                        <?php if ($submissionCount - $gradedCount > 0): ?>
                                            <span class="badge bg-warning"><?php echo $submissionCount - $gradedCount; ?> Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view.php?id=<?php echo $assignment['id']; ?>" class="btn btn-outline-primary" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="grade.php?id=<?php echo $assignment['id']; ?>" class="btn btn-outline-warning" title="Grade">
                                                <i class="fa fa-check-circle"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($assignmentsData['total_pages'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($assignmentsData['current_page'] > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $assignmentsData['current_page'] - 1; ?>&status=<?php echo $status; ?>">Previous</a></li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $assignmentsData['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $assignmentsData['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($assignmentsData['current_page'] < $assignmentsData['total_pages']): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $assignmentsData['current_page'] + 1; ?>&status=<?php echo $status; ?>">Next</a></li>
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