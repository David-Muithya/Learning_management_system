<?php
// View Instructor Applications - Premium Version
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
    
    <style>
        /* Premium Styles */
        :root {
            --teal-primary: #06BBCC;
            --teal-dark: #0598A6;
            --teal-light: #E6F8FA;
            --navy-dark: #181d38;
        }
        
        body {
            background-color: #F0FBFC;
        }
        
        /* Premium Navbar */
        .navbar {
            padding: 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .navbar-brand {
            padding: 1.2rem 2rem !important;
        }
        
        .navbar-brand h2 {
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        
        /* Dropdown Menu Styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 12px;
            margin-top: 10px;
            padding: 10px 0;
        }
        
        .dropdown-item {
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background-color: var(--teal-light);
            color: var(--teal-primary);
            padding-left: 28px;
        }
        
        .dropdown-item i {
            width: 24px;
            margin-right: 8px;
            color: var(--teal-primary);
        }
        
        /* Navbar Links */
        .navbar-nav .nav-link {
            font-weight: 600;
            padding: 1.5rem 1rem !important;
            transition: all 0.3s ease;
            position: relative;
            color: var(--navy-dark) !important;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--teal-primary) !important;
        }
        
        .navbar-nav .nav-link.active {
            color: var(--teal-primary) !important;
        }
        
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 15%;
            width: 70%;
            height: 3px;
            background-color: var(--teal-primary);
            border-radius: 3px;
        }
        
        /* Header */
        .premium-header {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            position: relative;
            overflow: hidden;
        }
        
        .premium-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        
        /* Tabs */
        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
        }
        
        .nav-tabs .nav-link {
            border: none;
            font-weight: 600;
            padding: 12px 24px;
            color: #6c757d;
            transition: all 0.3s ease;
            border-radius: 30px;
            margin-right: 8px;
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--teal-primary);
            background-color: var(--teal-light);
        }
        
        .nav-tabs .nav-link.active {
            color: white;
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            box-shadow: 0 4px 15px rgba(6, 187, 204, 0.3);
        }
        
        /* Application Cards */
        .application-card {
            background: white;
            border-radius: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            height: 100%;
        }
        
        .application-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(6, 187, 204, 0.15);
        }
        
        .card-header-premium {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            padding: 1.25rem;
            border-bottom: 2px solid var(--teal-light);
        }
        
        .applicant-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 0.25rem;
        }
        
        .applicant-detail {
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }
        
        .applicant-detail i {
            width: 20px;
            color: var(--teal-primary);
        }
        
        .status-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #000;
        }
        
        .status-approved {
            background: linear-gradient(135deg, #198754, #157347);
            color: white;
        }
        
        .status-rejected {
            background: linear-gradient(135deg, #dc3545, #bb2d3b);
            color: white;
        }
        
        .info-section {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }
        
        .info-section:hover {
            background-color: var(--teal-light);
        }
        
        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: var(--teal-primary);
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--navy-dark);
            margin-bottom: 0;
        }
        
        /* Buttons */
        .btn-approve {
            background: linear-gradient(135deg, #198754, #157347);
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.4);
        }
        
        .btn-reject {
            background: linear-gradient(135deg, #dc3545, #bb2d3b);
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }
        
        .modal-header-premium {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            color: white;
            padding: 1.25rem;
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, #181d38, #0f1325);
        }
        
        /* Pagination */
        .pagination .page-item .page-link {
            border-radius: 30px;
            margin: 0 4px;
            color: var(--teal-primary);
            border: none;
            transition: all 0.3s ease;
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            color: white;
            box-shadow: 0 4px 10px rgba(6, 187, 204, 0.3);
        }
        
        .pagination .page-item .page-link:hover {
            background-color: var(--teal-light);
            color: var(--teal-dark);
            transform: translateY(-2px);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .navbar-nav .nav-link {
                padding: 0.5rem 1rem !important;
            }
            .navbar-nav .nav-link.active::after {
                display: none;
            }
            .dropdown-menu {
                box-shadow: none;
                padding-left: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Premium Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow-sm sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center">
            <h2 class="m-0 text-primary"><i class="fa fa-crown me-2"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav ms-auto p-4 p-lg-0">
                <li class="nav-item"><a href="../index.php" class="nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-users me-2"></i>Users</a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="applications.php" class="dropdown-item active"><i class="fa fa-user-plus"></i> Applications</a>
                        <a href="list.php" class="dropdown-item"><i class="fa fa-chalkboard-user"></i> All Instructors</a>
                        <a href="../users/list.php" class="dropdown-item"><i class="fa fa-users"></i> All Users</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-book me-2"></i>Courses</a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="../courses/pending.php" class="dropdown-item"><i class="fa fa-clock"></i> Pending Approval</a>
                        <a href="../categories/index.php" class="dropdown-item"><i class="fa fa-tags"></i> Categories</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-credit-card me-2"></i>Finance</a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="../payments/pending.php" class="dropdown-item"><i class="fa fa-hourglass-half"></i> Pending Payments</a>
                        <a href="../enrollments/pending.php" class="dropdown-item"><i class="fa fa-check-circle"></i> Pending Enrollments</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-user-circle me-2"></i>Account</a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="../profile/index.php" class="dropdown-item"><i class="fa fa-id-card"></i> My Profile</a>
                        <a href="../settings/index.php" class="dropdown-item"><i class="fa fa-cog"></i> Settings</a>
                        <div class="dropdown-divider"></div>
                        <a href="../../logout.php" class="dropdown-item text-danger"><i class="fa fa-sign-out-alt"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Premium Navbar End -->

    <!-- Premium Header Start -->
    <div class="container-fluid premium-header py-5 mb-5">
        <div class="container text-center position-relative" style="z-index: 2;">
            <h1 class="text-white display-4 fw-bold mb-3">Instructor Applications</h1>
            <p class="text-white opacity-75 fs-5 mb-0">Review and manage instructor applications</p>
        </div>
    </div>
    <!-- Premium Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Status Tabs -->
            <div class="row mb-4">
                <div class="col-12">
                    <ul class="nav nav-tabs justify-content-center flex-wrap">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $status === 'pending' ? 'active' : ''; ?>" href="?status=pending">
                                <i class="fa fa-hourglass-half me-2"></i>Pending 
                                <span class="badge bg-white text-dark ms-1 rounded-pill"><?php echo $pendingCount; ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $status === 'approved' ? 'active' : ''; ?>" href="?status=approved">
                                <i class="fa fa-check-circle me-2"></i>Approved
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $status === 'rejected' ? 'active' : ''; ?>" href="?status=rejected">
                                <i class="fa fa-times-circle me-2"></i>Rejected
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $status === 'all' ? 'active' : ''; ?>" href="?status=all">
                                <i class="fa fa-list me-2"></i>All Applications
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Alert Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show shadow-sm rounded-3" role="alert">
                    <i class="fa fa-<?php echo $messageType === 'success' ? 'check-circle' : ($messageType === 'danger' ? 'exclamation-circle' : 'info-circle'); ?> me-2"></i>
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Applications Grid -->
            <?php if (empty($applicationsData['applications'])): ?>
                <div class="text-center py-5">
                    <div class="bg-white rounded-4 p-5 shadow-sm">
                        <i class="fa fa-inbox fa-5x text-muted mb-4"></i>
                        <h4 class="mb-2">No applications found</h4>
                        <p class="text-muted">There are no instructor applications to review at this time.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($applicationsData['applications'] as $app): ?>
                        <div class="col-lg-6">
                            <div class="application-card">
                                <div class="card-header-premium">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="applicant-name mb-1">
                                                <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?>
                                            </h5>
                                            <p class="applicant-detail mb-1">
                                                <i class="fa fa-envelope me-2"></i><?php echo htmlspecialchars($app['email']); ?>
                                            </p>
                                            <?php if ($app['phone']): ?>
                                                <p class="applicant-detail mb-0">
                                                    <i class="fa fa-phone me-2"></i><?php echo htmlspecialchars($app['phone']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <span class="status-badge status-<?php echo $app['status']; ?>">
                                            <i class="fa fa-<?php echo $app['status'] === 'pending' ? 'clock' : ($app['status'] === 'approved' ? 'check' : 'times'); ?> me-1"></i>
                                            <?php echo ucfirst($app['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="p-4">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="info-section">
                                                <div class="info-label">
                                                    <i class="fa fa-graduation-cap me-1"></i> Education
                                                </div>
                                                <p class="info-value mb-0">
                                                    <strong><?php echo htmlspecialchars($app['highest_qualification'] ?? 'N/A'); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($app['institution'] ?? ''); ?></small>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-section">
                                                <div class="info-label">
                                                    <i class="fa fa-briefcase me-1"></i> Experience
                                                </div>
                                                <p class="info-value mb-0">
                                                    <strong><?php echo $app['years_experience'] ?? '0'; ?> years</strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($app['current_role'] ?? ''); ?></small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="info-section mb-3">
                                        <div class="info-label">
                                            <i class="fa fa-star me-1"></i> Areas of Expertise
                                        </div>
                                        <p class="info-value mb-0"><?php echo nl2br(htmlspecialchars($app['expertise_areas'])); ?></p>
                                    </div>
                                    
                                    <div class="info-section mb-3">
                                        <div class="info-label">
                                            <i class="fa fa-quote-left me-1"></i> Teaching Philosophy
                                        </div>
                                        <p class="info-value mb-0"><?php echo nl2br(htmlspecialchars(substr($app['teaching_philosophy'], 0, 120))); ?>...</p>
                                    </div>
                                    
                                    <div class="info-section mb-3">
                                        <div class="info-label">
                                            <i class="fa fa-lightbulb me-1"></i> Sample Course Idea
                                        </div>
                                        <p class="info-value mb-0"><?php echo nl2br(htmlspecialchars(substr($app['sample_course_idea'], 0, 100))); ?>...</p>
                                    </div>
                                    
                                    <?php if ($app['portfolio_link']): ?>
                                        <div class="info-section mb-3">
                                            <div class="info-label">
                                                <i class="fa fa-link me-1"></i> Portfolio
                                            </div>
                                            <a href="<?php echo htmlspecialchars($app['portfolio_link']); ?>" target="_blank" class="info-value text-primary">
                                                <?php echo htmlspecialchars($app['portfolio_link']); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($app['status'] === 'pending'): ?>
                                        <div class="d-flex gap-2 mt-4">
                                            <button type="button" class="btn btn-approve flex-fill" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $app['id']; ?>">
                                                <i class="fa fa-check me-2"></i>Approve Application
                                            </button>
                                            <button type="button" class="btn btn-reject flex-fill" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $app['id']; ?>">
                                                <i class="fa fa-times me-2"></i>Reject Application
                                            </button>
                                        </div>
                                    <?php elseif ($app['review_notes']): ?>
                                        <div class="mt-3 p-3 bg-light rounded-3">
                                            <small class="text-muted"><i class="fa fa-pen me-1"></i> Review Notes:</small>
                                            <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($app['review_notes'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal<?php echo $app['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header-premium">
                                        <h5 class="modal-title text-white">
                                            <i class="fa fa-check-circle me-2"></i>Approve Application
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <?php echo Security::csrfField(); ?>
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                        <div class="modal-body p-4">
                                            <p class="mb-3">Approve <strong><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></strong> as an instructor?</p>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Notes (Optional)</label>
                                                <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this approval..."></textarea>
                                            </div>
                                            <div class="alert alert-success">
                                                <i class="fa fa-envelope me-2"></i>
                                                An email will be sent to the applicant with login credentials.
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 pe-4">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-approve">Approve Application</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal<?php echo $app['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header-premium" style="background: linear-gradient(135deg, #dc3545, #bb2d3b);">
                                        <h5 class="modal-title text-white">
                                            <i class="fa fa-times-circle me-2"></i>Reject Application
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <?php echo Security::csrfField(); ?>
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                        <div class="modal-body p-4">
                                            <p class="mb-3">Reject <strong><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></strong>'s application?</p>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-danger">Reason for Rejection *</label>
                                                <textarea name="notes" class="form-control" rows="3" placeholder="Provide feedback to the applicant..." required></textarea>
                                                <small class="text-muted">This feedback will be sent to the applicant.</small>
                                            </div>
                                            <div class="alert alert-danger">
                                                <i class="fa fa-envelope me-2"></i>
                                                An email will be sent to the applicant with your feedback.
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 pe-4">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-reject">Reject Application</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($applicationsData['total_pages'] > 1): ?>
                    <div class="row mt-5">
                        <div class="col-12">
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php if ($applicationsData['current_page'] > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $applicationsData['current_page'] - 1; ?>">
                                                <i class="fa fa-chevron-left me-1"></i> Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $applicationsData['total_pages']; $i++): ?>
                                        <li class="page-item <?php echo $i == $applicationsData['current_page'] ? 'active' : ''; ?>">
                                            <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($applicationsData['current_page'] < $applicationsData['total_pages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $applicationsData['current_page'] + 1; ?>">
                                                Next <i class="fa fa-chevron-right ms-1"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
        </div>
    </div>
    <!-- Content End -->

    <!-- Premium Footer Start -->
    <div class="container-fluid footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="text-white-50 mb-0">
                        &copy; <?php echo date('Y'); ?> <a class="text-primary text-decoration-none" href="#"><?php echo APP_NAME; ?></a>. All Rights Reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="../../index.php" class="text-white-50 text-decoration-none me-3">Home</a>
                        <a href="../../about.php" class="text-white-50 text-decoration-none me-3">About</a>
                        <a href="../../contact.php" class="text-white-50 text-decoration-none">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Premium Footer End -->

    <!-- Back to Top Button -->
    <a href="#" class="btn btn-lg rounded-circle back-to-top" style="position: fixed; bottom: 30px; right: 30px; background: linear-gradient(135deg, #06BBCC, #0598A6); color: white; width: 50px; height: 50px; display: none; align-items: center; justify-content: center; z-index: 99;">
        <i class="bi bi-arrow-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    
    <script>
        // Back to top button
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('.back-to-top').fadeIn('slow');
            } else {
                $('.back-to-top').fadeOut('slow');
            }
        });
        
        $('.back-to-top').click(function() {
            $('html, body').animate({scrollTop: 0}, 500, 'easeInOutExpo');
            return false;
        });
    </script>
</body>
</html>