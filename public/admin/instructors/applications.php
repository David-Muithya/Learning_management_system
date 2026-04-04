<?php
// View Instructor Applications
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\InstructorApplication;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Pagination;

// Only admin can access
RoleMiddleware::check('admin');

$applicationModel = new InstructorApplication();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status = isset($_GET['status']) ? $_GET['status'] : 'pending';

// Get applications
$applicationsData = $applicationModel->getAll($status, $page, 20);
$pendingCount = $applicationModel->getPendingCount();

// Handle approval/rejection
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid security token.';
        $messageType = 'danger';
    } else {
        $action = $_POST['action'] ?? '';
        $applicationId = (int)$_POST['application_id'] ?? 0;
        $notes = $_POST['notes'] ?? '';
        
        if ($action === 'approve') {
            $result = $applicationModel->approve($applicationId, $_SESSION['user_id'], $notes);
            $message = $result ? 'Application approved successfully!' : 'Failed to approve application.';
            $messageType = $result ? 'success' : 'danger';
        } elseif ($action === 'reject') {
            $result = $applicationModel->reject($applicationId, $_SESSION['user_id'], $notes);
            $message = $result ? 'Application rejected successfully!' : 'Failed to reject application.';
            $messageType = $result ? 'success' : 'danger';
        }
        
        // Refresh data
        $applicationsData = $applicationModel->getAll($status, $page, 20);
    }
}

$page_title = 'Instructor Applications - ' . APP_NAME;
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
                <a href="applications.php" class="nav-item nav-link active">Applications</a>
                <a href="../courses/pending.php" class="nav-item nav-link">Pending Courses</a>
                <a href="../payments/pending.php" class="nav-item nav-link">Pending Payments</a>
                <a href="../enrollments/pending.php" class="nav-item nav-link">Enrollments</a>
                <a href="../settings/index.php" class="nav-item nav-link">Settings</a>
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
                    <h1 class="text-white">Instructor Applications</h1>
                    <p class="text-white mb-0">Review and manage instructor applications</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Status Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'pending' ? 'active' : ''; ?>" href="?status=pending">
                        Pending <span class="badge bg-warning ms-1"><?php echo $pendingCount; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'approved' ? 'active' : ''; ?>" href="?status=approved">Approved</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'rejected' ? 'active' : ''; ?>" href="?status=rejected">Rejected</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'all' ? 'active' : ''; ?>" href="?status=all">All</a>
                </li>
            </ul>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (empty($applicationsData['applications'])): ?>
                <div class="text-center py-5">
                    <i class="fa fa-inbox fa-4x text-muted mb-3"></i>
                    <h4>No applications found</h4>
                    <p class="text-muted">There are no instructor applications to review at this time.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($applicationsData['applications'] as $app): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="bg-light rounded p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1"><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></h5>
                                        <p class="text-muted mb-0"><i class="fa fa-envelope me-2"></i><?php echo htmlspecialchars($app['email']); ?></p>
                                        <?php if ($app['phone']): ?>
                                            <p class="text-muted mb-0"><i class="fa fa-phone me-2"></i><?php echo htmlspecialchars($app['phone']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-<?php echo $app['status'] === 'pending' ? 'warning' : ($app['status'] === 'approved' ? 'success' : 'danger'); ?>">
                                        <?php echo ucfirst($app['status']); ?>
                                    </span>
                                </div>
                                
                                <hr>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <small class="text-muted">Education</small>
                                        <p class="mb-1"><strong><?php echo htmlspecialchars($app['highest_qualification'] ?? 'N/A'); ?></strong></p>
                                        <small class="text-muted"><?php echo htmlspecialchars($app['institution'] ?? ''); ?></small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Experience</small>
                                        <p class="mb-1"><strong><?php echo $app['years_experience'] ?? '0'; ?> years</strong></p>
                                        <small class="text-muted"><?php echo htmlspecialchars($app['current_role'] ?? ''); ?></small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Areas of Expertise</small>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($app['expertise_areas'])); ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Teaching Philosophy</small>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars(substr($app['teaching_philosophy'], 0, 150))); ?>...</p>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Sample Course Idea</small>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars(substr($app['sample_course_idea'], 0, 100))); ?>...</p>
                                </div>
                                
                                <?php if ($app['portfolio_link']): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Portfolio</small>
                                        <p><a href="<?php echo htmlspecialchars($app['portfolio_link']); ?>" target="_blank"><?php echo htmlspecialchars($app['portfolio_link']); ?></a></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($app['status'] === 'pending'): ?>
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $app['id']; ?>">
                                            <i class="fa fa-check me-1"></i>Approve
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $app['id']; ?>">
                                            <i class="fa fa-times me-1"></i>Reject
                                        </button>
                                    </div>
                                    
                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal<?php echo $app['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <?php echo Security::csrfField(); ?>
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Approve Application</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Approve <strong><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></strong> as an instructor?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Notes (Optional)</label>
                                                            <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this approval..."></textarea>
                                                        </div>
                                                        <p class="text-success">An email will be sent to the applicant with login credentials.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Approve</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal<?php echo $app['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <?php echo Security::csrfField(); ?>
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Application</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Reject <strong><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></strong>'s application?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Reason for Rejection</label>
                                                            <textarea name="notes" class="form-control" rows="3" placeholder="Provide feedback to the applicant..." required></textarea>
                                                        </div>
                                                        <p class="text-danger">An email will be sent to the applicant with your feedback.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ($app['review_notes']): ?>
                                    <div class="mt-3 p-2 bg-white rounded">
                                        <small class="text-muted">Review Notes:</small>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($app['review_notes'])); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($applicationsData['total_pages'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($applicationsData['current_page'] > 1): ?>
                                <li class="page-item"><a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $applicationsData['current_page'] - 1; ?>">Previous</a></li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $applicationsData['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $applicationsData['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($applicationsData['current_page'] < $applicationsData['total_pages']): ?>
                                <li class="page-item"><a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $applicationsData['current_page'] + 1; ?>">Next</a></li>
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