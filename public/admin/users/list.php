<?php
// All Users List
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;

RoleMiddleware::check('admin');

$userModel = new User();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$role = isset($_GET['role']) ? $_GET['role'] : 'all';
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$sql = "SELECT * FROM users WHERE deleted_at IS NULL";
$params = [];

if ($role !== 'all') {
    $sql .= " AND role = ?";
    $params[] = $role;
}

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$stmt = $userModel->getDB()->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get total count
$countSql = "SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL";
if ($role !== 'all') {
    $countSql .= " AND role = ?";
    $stmt = $userModel->getDB()->prepare($countSql);
    $stmt->execute([$role]);
} else {
    $stmt = $userModel->getDB()->query($countSql);
}
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

$page_title = 'All Users - ' . APP_NAME;
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
</head>
<body style="background-color: #F0FBFC;">

    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="list.php" class="nav-item nav-link active">All Users</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4 mb-5" style="background-color: #06BBCC;">
        <div class="container text-center">
            <h1 class="text-white">All Users</h1>
            <p class="text-white mb-0">Manage system users</p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item"><a class="nav-link <?php echo $role === 'all' ? 'active' : ''; ?>" href="?role=all">All</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $role === 'admin' ? 'active' : ''; ?>" href="?role=admin">Admins</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $role === 'instructor' ? 'active' : ''; ?>" href="?role=instructor">Instructors</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $role === 'student' ? 'active' : ''; ?>" href="?role=student">Students</a></li>
            </ul>
            
            <div class="bg-white rounded p-4 shadow-sm">
                <?php if (empty($users)): ?>
                    <div class="text-center py-5"><i class="fa fa-users fa-4x text-muted mb-3"></i><h4>No users found</h4></div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #06BBCC; color: white;">
                                <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Last Login</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?><br><small class="text-muted">@<?php echo $user['username']; ?></small></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'instructor' ? 'info' : 'success'); ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                    <td><span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>"><?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td><?php echo $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-4"><ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?role=<?php echo $role; ?>&page=<?php echo $page-1; ?>">Previous</a></li><?php endif; ?>
                            <?php for($i=1; $i<=$totalPages; $i++): ?>
                                <li class="page-item <?php echo $i==$page ? 'active' : ''; ?>"><a class="page-link" href="?role=<?php echo $role; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?><li class="page-item"><a class="page-link" href="?role=<?php echo $role; ?>&page=<?php echo $page+1; ?>">Next</a></li><?php endif; ?>
                        </ul></nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>