<?php
// Approve/Reject Instructor Application
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\InstructorApplication;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('admin');

$applicationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

$applicationModel = new InstructorApplication();
$application = $applicationModel->getById($applicationId);

if (!$application || $application['status'] !== 'pending') {
    header('Location: applications.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid security token.';
        $messageType = 'danger';
    } else {
        $notes = $_POST['notes'] ?? '';
        
        if ($action === 'approve') {
            $result = $applicationModel->approve($applicationId, $_SESSION['user_id'], $notes);
            $message = $result ? 'Application approved successfully!' : 'Failed to approve application.';
            $messageType = $result ? 'success' : 'danger';
        } elseif ($action === 'reject') {
            $result = $applicationModel->reject($applicationId, $_SESSION['user_id'], $notes);
            $message = $result ? 'Application rejected.' : 'Failed to reject application.';
            $messageType = $result ? 'warning' : 'danger';
        }
        
        if ($result) {
            header("refresh:2;url=applications.php");
        }
    }
}

$page_title = 'Review Application - ' . APP_NAME;
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
                <a href="applications.php" class="nav-item nav-link active">Applications</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Review Application</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <div class="bg-light rounded p-4 mb-4">
                        <h5>Personal Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($application['phone'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <div class="bg-light rounded p-4 mb-4">
                        <h5>Qualifications</h5>
                        <p><strong>Highest Qualification:</strong> <?php echo htmlspecialchars($application['highest_qualification'] ?? 'N/A'); ?></p>
                        <p><strong>Institution:</strong> <?php echo htmlspecialchars($application['institution'] ?? 'N/A'); ?></p>
                        <p><strong>Graduation Year:</strong> <?php echo htmlspecialchars($application['graduation_year'] ?? 'N/A'); ?></p>
                        <p><strong>Years Experience:</strong> <?php echo htmlspecialchars($application['years_experience'] ?? 'N/A'); ?></p>
                        <p><strong>Current Role:</strong> <?php echo htmlspecialchars($application['current_role'] ?? 'N/A'); ?></p>
                        <p><strong>Organization:</strong> <?php echo htmlspecialchars($application['organization'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <div class="bg-light rounded p-4 mb-4">
                        <h5>Teaching Information</h5>
                        <p><strong>Expertise Areas:</strong><br><?php echo nl2br(htmlspecialchars($application['expertise_areas'])); ?></p>
                        <p><strong>Teaching Philosophy:</strong><br><?php echo nl2br(htmlspecialchars($application['teaching_philosophy'])); ?></p>
                        <p><strong>Sample Course Idea:</strong><br><?php echo nl2br(htmlspecialchars($application['sample_course_idea'])); ?></p>
                        <?php if ($application['portfolio_link']): ?>
                            <p><strong>Portfolio:</strong> <a href="<?php echo htmlspecialchars($application['portfolio_link']); ?>" target="_blank">View</a></p>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST">
                        <?php echo Security::csrfField(); ?>
                        <div class="mb-3">
                            <label class="form-label">Review Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes about this decision..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                            <a href="applications.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
</body>
</html>