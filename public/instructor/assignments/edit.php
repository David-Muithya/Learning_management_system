<?php
// Edit Assignment
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Assignment;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

RoleMiddleware::check('instructor');

$assignmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$assignmentModel = new Assignment();

$assignment = $assignmentModel->getById($assignmentId);

// Verify instructor owns this assignment
if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
    header('Location: list.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $title = Validation::sanitize($_POST['title'] ?? '');
        $description = Validation::sanitize($_POST['description'] ?? '');
        $due_date = $_POST['due_date'] ?? '';
        $max_points = (float)($_POST['max_points'] ?? 100);
        
        if (empty($title) || empty($due_date)) {
            $error = 'Please fill in all required fields.';
        } elseif ($max_points <= 0) {
            $error = 'Max points must be greater than 0.';
        } else {
            $data = [
                'title' => $title,
                'description' => $description,
                'due_date' => date('Y-m-d H:i:s', strtotime($due_date)),
                'max_points' => $max_points
            ];
            
            $result = $assignmentModel->update($assignmentId, $data);
            
            if ($result) {
                $success = 'Assignment updated successfully!';
                $assignment = $assignmentModel->getById($assignmentId);
            } else {
                $error = 'Failed to update assignment.';
            }
        }
    }
}

$page_title = 'Edit Assignment - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="list.php" class="nav-item nav-link active">Assignments</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Edit Assignment</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($assignment['title']); ?></p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <div class="bg-light rounded p-5">
                        <form method="POST">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Assignment Title *</label>
                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea class="form-control" name="description" rows="6"><?php echo htmlspecialchars($assignment['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Due Date & Time *</label>
                                    <input type="text" class="form-control datetimepicker" name="due_date" value="<?php echo date('Y-m-d H:i:s', strtotime($assignment['due_date'])); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Maximum Points *</label>
                                    <input type="number" class="form-control" name="max_points" value="<?php echo $assignment['max_points']; ?>" step="0.5" required>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Changing the due date or points may affect existing submissions.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="list.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
    <script>
        flatpickr(".datetimepicker", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            time_24hr: true,
            altInput: true,
            altFormat: "F j, Y h:i K"
        });
    </script>
</body>
</html>