<?php
// All Enrollments List
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Enrollment;

RoleMiddleware::check('admin');

$enrollmentModel = new Enrollment();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$sql = "
    SELECT e.*, c.title as course_title, 
           CONCAT(u.first_name, ' ', u.last_name) as student_name,
           u.email as student_email
    FROM enrollments e
    LEFT JOIN courses c ON e.course_id = c.id
    LEFT JOIN users u ON e.student_id = u.id
";
$params = [];

if ($status !== 'all') {
    $sql .= " WHERE e.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY e.enrolled_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$stmt = $enrollmentModel->db->prepare($sql);
$stmt->execute($params);
$enrollments = $stmt->fetchAll();

// Get total count
$countSql = "SELECT COUNT(*) as total FROM enrollments e";
if ($status !== 'all') {
    $countSql .= " WHERE e.status = ?";
    $stmt = $enrollmentModel->db->prepare($countSql);
    $stmt->execute([$status]);
} else {
    $stmt = $enrollmentModel->db->query($countSql);
}
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

$page_title = 'All Enrollments - ' . APP_NAME;
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
                <a href="pending.php" class="nav-item nav-link">Pending</a>
                <a href="list.php" class="nav-item nav-link active">All Enrollments</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">All Enrollments</h1>
            <p class="text-white mb-0">Manage student enrollments</p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Status Filter -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'all' ? 'active' : ''; ?>" href="?status=all">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'active' ? 'active' : ''; ?>" href="?status=active">Active</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'pending_verification' ? 'active' : ''; ?>" href="?status=pending_verification">Pending Verification</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'completed' ? 'active' : ''; ?>" href="?status=completed">Completed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $status === 'dropped' ? 'active' : ''; ?>" href="?status=dropped">Dropped</a>
                </li>
            </ul>
            
            <div class="bg-light rounded p-4">
                <?php if (empty($enrollments)): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-users fa-4x text-muted mb-3"></i>
                        <h4>No enrollments found</h4>
                        <p class="text-muted">No enrollments match your filter.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Enrolled Date</th>
                                    <th>Status</th>
                                    <th>Final Grade</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?php echo $enrollment['id']; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($enrollment['student_name']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($enrollment['student_email']); ?></small>
                                         </td>
                                        <td><?php echo htmlspecialchars($enrollment['course_title']); ?> </td>
                                        <td><?php echo date('M d, Y', strtotime($enrollment['enrolled_at'])); ?> </td>
                                        <td>
                                            <?php
                                            $badgeClass = match($enrollment['status']) {
                                                'active' => 'success',
                                                'pending_verification' => 'warning',
                                                'completed' => 'info',
                                                'dropped' => 'danger',
                                                default => 'secondary'
                                            };
                                            ?>
                                            <span class="badge bg-<?php echo $badgeClass; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $enrollment['status'])); ?>
                                            </span>
                                         </td>
                                        <td>
                                            <?php if ($enrollment['final_grade']): ?>
                                                <span class="badge bg-primary"><?php echo $enrollment['final_grade']; ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                         </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($enrollment['status'] === 'pending_verification'): ?>
                                                    <a href="verify.php?id=<?php echo $enrollment['mock_payment_id']; ?>" class="btn btn-success" title="Verify">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="../../instructor/students/progress.php?student_id=<?php echo $enrollment['student_id']; ?>&course_id=<?php echo $enrollment['course_id']; ?>" class="btn btn-outline-primary" title="View Progress">
                                                    <i class="fa fa-chart-line"></i>
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
                                    <li class="page-item"><a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $page - 1; ?>">Previous</a></li>
                                <?php endif; ?>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item"><a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $page + 1; ?>">Next</a></li>
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