<?php
// Verify Enrollment (Mock Payment)
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\MockPayment;
use SkillMaster\Models\Enrollment;
use SkillMaster\Services\NotificationService;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('admin');

$paymentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$paymentModel = new MockPayment();
$enrollmentModel = new Enrollment();
$notificationService = new NotificationService();

$payment = $paymentModel->getById($paymentId);

if (!$payment || $payment['status'] !== 'completed') {
    header('Location: pending.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid security token.';
        $messageType = 'danger';
    } else {
        $action = $_POST['action'] ?? '';
        $notes = $_POST['notes'] ?? '';
        
        if ($action === 'verify') {
            $result = $paymentModel->verify($paymentId, $_SESSION['user_id'], $notes);
            
            if ($result) {
                $message = 'Enrollment verified successfully! Student now has access to the course.';
                $messageType = 'success';
                header("refresh:2;url=pending.php");
            } else {
                $message = 'Failed to verify enrollment.';
                $messageType = 'danger';
            }
        } elseif ($action === 'reject') {
            $result = $paymentModel->reject($paymentId, $_SESSION['user_id'], $notes);
            
            if ($result) {
                $message = 'Enrollment rejected.';
                $messageType = 'warning';
                header("refresh:2;url=pending.php");
            } else {
                $message = 'Failed to reject enrollment.';
                $messageType = 'danger';
            }
        }
    }
}

$page_title = 'Verify Enrollment - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="pending.php" class="nav-item nav-link active">Pending Enrollments</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Verify Enrollment</h1>
            <p class="text-white mb-0">Transaction: <?php echo htmlspecialchars($payment['transaction_id']); ?></p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="bg-light rounded p-4 mb-4">
                        <h5>Payment Details</h5>
                        <p><strong>Transaction ID:</strong> <code><?php echo htmlspecialchars($payment['transaction_id']); ?></code></p>
                        <p><strong>Student:</strong> <?php echo htmlspecialchars($payment['student_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($payment['student_email']); ?></p>
                        <p><strong>Course:</strong> <?php echo htmlspecialchars($payment['course_title']); ?></p>
                        <p><strong>Amount:</strong> <?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($payment['amount'], 2); ?></p>
                        <p><strong>Payment Date:</strong> <?php echo date('M d, Y g:i A', strtotime($payment['payment_date'])); ?></p>
                    </div>
                    
                    <div class="bg-light rounded p-4">
                        <h5>Decision</h5>
                        <form method="POST">
                            <?php echo Security::csrfField(); ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Notes (Optional)</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes about this decision..."></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="verify" class="btn btn-success">Verify & Activate</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                                <a href="pending.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
</body>
</html>