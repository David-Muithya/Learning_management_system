<?php
// All Instructors List
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Models\Course;  // ADD THIS LINE
use SkillMaster\Helpers\Security;

RoleMiddleware::check('admin');

$userModel = new User();
$courseModel = new Course();  // ADD THIS LINE
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$sql = "SELECT * FROM users WHERE role = 'instructor'";
$params = [];

if (!empty($search)) {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
    $searchTerm = "%{$search}%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$stmt = $userModel->getDB()->prepare($sql);
$stmt->execute($params);
$instructors = $stmt->fetchAll();

// Get total count
$countSql = "SELECT COUNT(*) as total FROM users WHERE role = 'instructor'";
if (!empty($search)) {
    $countSql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
    $stmt = $userModel->getDB()->prepare($countSql);
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
} else {
    $stmt = $userModel->getDB()->query($countSql);
}
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

// Handle status toggle
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $newStatus = $_GET['status'] == '1' ? 0 : 1;
    $stmt = $userModel->getDB()->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    $stmt->execute([$newStatus, $id]);
    $_SESSION['success'] = 'Instructor status updated successfully!';
    header("Location: list.php" . ($search ? "?search=" . urlencode($search) : ""));
    exit;
}

$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

$page_title = 'All Instructors - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
        .table-actions .btn { margin: 0 2px; }
        .avatar-sm { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
    </style>
</head>
<body style="background-color: #F0FBFC;">

    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="applications.php" class="nav-item nav-link">Applications</a>
                <a href="list.php" class="nav-item nav-link active">All Instructors</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4 mb-5" style="background-color: #06BBCC;">
        <div class="container text-center">
            <h1 class="text-white">All Instructors</h1>
            <p class="text-white mb-0">Manage instructor accounts</p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            
            <!-- Search Bar -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary" style="background-color: #06BBCC;">Search</button>
                        <?php if ($search): ?>
                            <a href="list.php" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <a href="applications.php" class="btn btn-primary" style="background-color: #06BBCC;">
                        <i class="fa fa-user-plus me-2"></i>View Applications
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded p-4 shadow-sm">
                <?php if (empty($instructors)): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-chalkboard-user fa-4x text-muted mb-3"></i>
                        <h4>No instructors found</h4>
                        <a href="applications.php" class="btn btn-primary">Review Applications</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #06BBCC; color: white;">
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Courses</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($instructors as $instructor): 
                                    // Get course count for this instructor
                                    $courseCount = $courseModel->getCourseCountByInstructor($instructor['id']);
                                ?>
                                    <tr>
                                        <td><?php echo $instructor['id']; ?></td>
                                        <td>
                                            <img src="<?php echo !empty($instructor['profile_pic']) ? '../../uploads/profiles/' . $instructor['profile_pic'] : '../../assets/img/user-avatar.png'; ?>" class="avatar-sm">
                                        </td>
                                        <td><?php echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($instructor['email']); ?></td>
                                        <td><?php echo htmlspecialchars($instructor['phone_number'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $instructor['is_active'] ? 'success' : 'danger'; ?>">
                                                <?php echo $instructor['is_active'] ? 'Active' : 'Suspended'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $courseCount; ?> courses</td>
                                        <td><?php echo date('M d, Y', strtotime($instructor['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="edit.php?id=<?php echo $instructor['id']; ?>" class="btn btn-outline-primary" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="?toggle_status=1&id=<?php echo $instructor['id']; ?>&status=<?php echo $instructor['is_active']; ?>" class="btn btn-outline-<?php echo $instructor['is_active'] ? 'warning' : 'success'; ?>" title="<?php echo $instructor['is_active'] ? 'Suspend' : 'Activate'; ?>" onclick="return confirm('<?php echo $instructor['is_active'] ? 'Suspend' : 'Activate'; ?> this instructor?')">
                                                    <i class="fa fa-<?php echo $instructor['is_active'] ? 'pause' : 'play'; ?>"></i>
                                                </a>
                                                <a href="reset-password.php?id=<?php echo $instructor['id']; ?>" class="btn btn-outline-secondary" title="Reset Password" onclick="return confirm('Reset password for this instructor?')">
                                                    <i class="fa fa-key"></i>
                                                </a>
                                            </div>
                                         </td>
                                     </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">Previous</a></li>
                                <?php endif; ?>
                                <?php for($i=1; $i<=$totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i==$page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">Next</a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>