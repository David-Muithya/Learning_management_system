<?php
// Pending Enrollments Verification
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\MockPayment;
use SkillMaster\Models\Enrollment;
use SkillMaster\Helpers\Security;
use SkillMaster\Services\NotificationService;

// Only admin can access
RoleMiddleware::check('admin');

$paymentModel = new MockPayment();
$enrollmentModel = new Enrollment();
$notificationService = new NotificationService();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$message = '';
$messageType = '';

// Handle verification/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid security token.';
        $messageType = 'danger';
    } else {
        $action = $_POST['action'] ?? '';
        $paymentId = (int)$_POST['payment_id'] ?? 0;
        $notes = $_POST['notes'] ?? '';
        
        if ($action === 'verify') {
            $result = $paymentModel->verify($paymentId, $_SESSION['user_id'], $notes);
            
            if ($result) {
                $message = 'Enrollment verified successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to verify enrollment.';
                $messageType = 'danger';
            }
        } elseif ($action === 'reject') {
            $result = $paymentModel->reject($paymentId, $_SESSION['user_id'], $notes);
            
            if ($result) {
                $message = 'Enrollment rejected.';
                $messageType = 'warning';
            } else {
                $message = 'Failed to reject enrollment.';
                $messageType = 'danger';
            }
        }
        
        // Refresh data
        $paymentsData = $paymentModel->getPendingVerification($page, 10);
    } else {
        $paymentsData = $paymentModel->getPendingVerification($page, 10);
    }
} else {
    $paymentsData = $paymentModel->getPendingVerification($page, 10);
}

$page_title = 'Pending Enrollments - ' . APP_NAME;
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
                <a href="../instructors/applications.php" class="nav-item nav-link">Applications</a>
                <a href="../courses/pending.php" class="nav-item nav-link">Pending Courses</a>
                <a href="pending.php" class="nav-item nav-link active">Enrollments</a>
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
                    <h1 class="text-white">Pending Enrollments</h1>
                    <p class="text-white mb-0">Verify mock payments and activate student enrollments</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <i class="fa fa-<?php echo $messageType === 'success' ? 'check-circle' : ($messageType === 'danger' ? 'exclamation-circle' : 'info-circle'); ?> me-2"></i>
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (empty($paymentsData['payments'])): ?>
                <div class="text-center py-5">
                    <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
                    <h4>No Pending Enrollments</h4>
                    <p class="text-muted">All enrollments have been verified. Check back later for new enrollments.</p>
                    <a href="../index.php" class="btn btn-primary mt-3">Back to Dashboard</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover bg-light rounded">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paymentsData['payments'] as $payment): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($payment['transaction_id']); ?></code></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($payment['student_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($payment['student_email']); ?></small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($payment['course_title']); ?></strong>
                                        </td>
                                        <td><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($payment['amount'], 2); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($payment['payment_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-warning">Pending Verification</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal<?php echo $payment['id']; ?>">
                                                <i class="fa fa-check"></i> Verify
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $payment['id']; ?>">
                                                <i class="fa fa-times"></i> Reject
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Verify Modal -->
                                    <div class="modal fade" id="verifyModal<?php echo $payment['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <?php echo Security::csrfField(); ?>
                                                    <input type="hidden" name="action" value="verify">
                                                    <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Verify Enrollment</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Verify enrollment for <strong><?php echo htmlspecialchars($payment['student_name']); ?></strong> in <strong><?php echo htmlspecialchars($payment['course_title']); ?></strong>?</p>
                                                        <p class="text-muted">Transaction: <code><?php echo htmlspecialchars($payment['transaction_id']); ?></code></p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Notes (Optional)</label>
                                                            <textarea name="notes" class="form-control" rows="2" placeholder="Add any verification notes..."></textarea>
                                                        </div>
                                                        <p class="text-success">The student will be notified and will gain access to the course.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Verify Enrollment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal<?php echo $payment['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <?php echo Security::csrfField(); ?>
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Enrollment</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Reject enrollment for <strong><?php echo htmlspecialchars($payment['student_name']); ?></strong> in <strong><?php echo htmlspecialchars($payment['course_title']); ?></strong>?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Reason for Rejection</label>
                                                            <textarea name="notes" class="form-control" rows="3" required></textarea>
                                                            <small class="text-muted">This will be sent to the student.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if ($paymentsData['total_pages'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($paymentsData['current_page'] > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $paymentsData['current_page'] - 1; ?>">Previous</a></li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $paymentsData['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $paymentsData['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($paymentsData['current_page'] < $paymentsData['total_pages']): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $paymentsData['current_page'] + 1; ?>">Next</a></li>
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