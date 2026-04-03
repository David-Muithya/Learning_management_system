<?php
// All Instructors List
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;

RoleMiddleware::check('admin');

$userModel = new User();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get all instructors
$stmt = $userModel->db->prepare("
    SELECT * FROM users 
    WHERE role = 'instructor' 
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$perPage, $offset]);
$instructors = $stmt->fetchAll();

// Get total count
$stmt = $userModel->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'instructor'");
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

$page_title = 'All Instructors - ' . APP_NAME;
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
                <a href="applications.php" class="nav-item nav-link">Applications</a>
                <a href="list.php" class="nav-item nav-link active">Instructors</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">All Instructors</h1>
            <p class="text-white mb-0">Manage instructor accounts</p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="bg-light rounded p-4">
                <?php if (empty($instructors)): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-chalkboard-user fa-4x text-muted mb-3"></i>
                        <h4>No instructors found</h4>
                        <p class="text-muted">Instructors will appear here once approved.</p>
                        <a href="applications.php" class="btn btn-primary">View Applications</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>ID</th>
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
                                <?php foreach ($instructors as $instructor): ?>
                                    <?php
                                    // Get instructor's course count
                                    $stmt = $userModel->db->prepare("SELECT COUNT(*) as count FROM courses WHERE instructor_id = ?");
                                    $stmt->execute([$instructor['id']]);
                                    $courseCount = $stmt->fetch()['count'];
                                    ?>
                                    <tr>
                                        <td><?php echo $instructor['id']; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']); ?><br>
                                            <small class="text-muted">@<?php echo htmlspecialchars($instructor['username']); ?></small>
                                        </td>
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
                                                <?php if ($instructor['is_active']): ?>
                                                    <a href="suspend.php?id=<?php echo $instructor['id']; ?>" class="btn btn-outline-warning" title="Suspend" onclick="return confirm('Suspend this instructor?')">
                                                        <i class="fa fa-pause"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="activate.php?id=<?php echo $instructor['id']; ?>" class="btn btn-outline-success" title="Activate" onclick="return confirm('Activate this instructor?')">
                                                        <i class="fa fa-play"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="delete.php?id=<?php echo $instructor['id']; ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this instructor? This action cannot be undone.')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                                <?php endif; ?>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
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
</body>
</html>