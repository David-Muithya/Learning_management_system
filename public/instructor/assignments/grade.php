<?php
// Grade Submissions
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Assignment;
use SkillMaster\Models\Submission;
use SkillMaster\Models\Grade;
use SkillMaster\Helpers\Security;
use SkillMaster\Services\NotificationService;

RoleMiddleware::check('instructor');

$assignmentModel = new Assignment();
$submissionModel = new Submission();
$gradeModel = new Grade();
$notificationService = new NotificationService();

$assignmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$submissionId = isset($_GET['submission_id']) ? (int)$_GET['submission_id'] : 0;

// Get assignment details
$assignment = $assignmentModel->getAssignmentWithStats($assignmentId, $_SESSION['user_id']);

if (!$assignment) {
    header('Location: list.php');
    exit;
}

// Handle grading
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $submissionId) {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $grade = (float)$_POST['grade'];
        $feedback = $_POST['feedback'] ?? '';
        
        if ($grade < 0 || $grade > $assignment['max_points']) {
            $error = "Grade must be between 0 and {$assignment['max_points']}.";
        } else {
            $result = $submissionModel->grade($submissionId, $grade, $feedback, $_SESSION['user_id']);
            
            if ($result) {
                $success = 'Submission graded successfully!';
                // Redirect back to the assignment grading page
                header("refresh:1;url=grade.php?id=$assignmentId");
            } else {
                $error = 'Failed to grade submission.';
            }
        }
    }
}

// Get submissions for this assignment
$submissionsData = $submissionModel->getForGrading($assignmentId, $_SESSION['user_id'], $page, 20);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$page_title = 'Grade Submissions - ' . $assignment['title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
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
                <a href="list.php" class="nav-item nav-link">Assignments</a>
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
                    <h1 class="text-white">Grade Submissions</h1>
                    <p class="text-white mb-0"><?php echo htmlspecialchars($assignment['title']); ?> - <?php echo htmlspecialchars($assignment['course_title']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Assignment Stats -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="bg-light rounded p-3 text-center">
                        <h5 class="mb-0"><?php echo $assignment['total_submissions']; ?></h5>
                        <small class="text-muted">Total Submissions</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded p-3 text-center">
                        <h5 class="mb-0"><?php echo $assignment['graded_count']; ?></h5>
                        <small class="text-muted">Graded</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded p-3 text-center">
                        <h5 class="mb-0"><?php echo $assignment['total_submissions'] - $assignment['graded_count']; ?></h5>
                        <small class="text-muted">Pending Grading</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded p-3 text-center">
                        <h5 class="mb-0"><?php echo $assignment['avg_grade'] ? round($assignment['avg_grade'], 1) : 'N/A'; ?></h5>
                        <small class="text-muted">Average Grade</small>
                    </div>
                </div>
            </div>
            
            <?php if (empty($submissionsData['submissions'])): ?>
                <div class="text-center py-5">
                    <i class="fa fa-inbox fa-4x text-muted mb-3"></i>
                    <h4>No submissions yet</h4>
                    <p class="text-muted">Students haven't submitted this assignment yet.</p>
                    <a href="list.php" class="btn btn-primary">Back to Assignments</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover bg-light rounded">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Student</th>
                                <th>Submitted On</th>
                                <th>Status</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissionsData['submissions'] as $submission): ?>
                                <tr class="<?php echo $submission['status'] === 'graded' ? 'table-success' : ''; ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo !empty($submission['profile_pic']) ? '../../uploads/profiles/' . $submission['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" 
                                                 class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <strong><?php echo htmlspecialchars($submission['student_name']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($submission['student_email']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo date('M d, Y g:i A', strtotime($submission['submitted_at'])); ?></td>
                                    <td>
                                        <?php if ($submission['status'] === 'graded'): ?>
                                            <span class="badge bg-success">Graded</span>
                                        <?php elseif ($submission['is_late']): ?>
                                            <span class="badge bg-danger">Late</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($submission['grade'] !== null): ?>
                                            <strong><?php echo $submission['grade']; ?> / <?php echo $assignment['max_points']; ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">Not graded</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#gradeModal<?php echo $submission['id']; ?>">
                                            <i class="fa fa-check-circle me-1"></i>Grade
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Grade Modal -->
                                <div class="modal fade" id="gradeModal<?php echo $submission['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form method="POST" action="?id=<?php echo $assignmentId; ?>">
                                                <?php echo Security::csrfField(); ?>
                                                <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Grade Submission - <?php echo htmlspecialchars($submission['student_name']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Submission</label>
                                                        <div class="bg-white rounded p-3">
                                                            <p><strong>Submitted:</strong> <?php echo date('M d, Y g:i A', strtotime($submission['submitted_at'])); ?></p>
                                                            <?php if ($submission['submission_text']): ?>
                                                                <p><strong>Text Submission:</strong></p>
                                                                <div class="border rounded p-2 bg-light">
                                                                    <?php echo nl2br(htmlspecialchars($submission['submission_text'])); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Grade (0 - <?php echo $assignment['max_points']; ?>)</label>
                                                        <input type="number" class="form-control" name="grade" step="0.5" 
                                                               min="0" max="<?php echo $assignment['max_points']; ?>" 
                                                               value="<?php echo $submission['grade'] ?? ''; ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Feedback</label>
                                                        <textarea class="form-control" name="feedback" rows="5" placeholder="Provide feedback to the student..."><?php echo htmlspecialchars($submission['feedback'] ?? ''); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Grade</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($submissionsData['total_pages'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($submissionsData['current_page'] > 1): ?>
                                <li class="page-item"><a class="page-link" href="?id=<?php echo $assignmentId; ?>&page=<?php echo $submissionsData['current_page'] - 1; ?>">Previous</a></li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $submissionsData['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $submissionsData['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?id=<?php echo $assignmentId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($submissionsData['current_page'] < $submissionsData['total_pages']): ?>
                                <li class="page-item"><a class="page-link" href="?id=<?php echo $assignmentId; ?>&page=<?php echo $submissionsData['current_page'] + 1; ?>">Next</a></li>
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