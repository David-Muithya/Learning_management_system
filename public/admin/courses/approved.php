<?php
// Approved Courses List
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;

RoleMiddleware::check('admin');

$courseModel = new Course();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get approved/published courses
$stmt = $courseModel->db->prepare("
    SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as instructor_name
    FROM courses c
    LEFT JOIN users u ON c.instructor_id = u.id
    WHERE c.status = 'published' AND c.deleted_at IS NULL
    ORDER BY c.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$perPage, $offset]);
$courses = $stmt->fetchAll();

// Get total count
$stmt = $courseModel->db->query("SELECT COUNT(*) as total FROM courses WHERE status = 'published'");
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

$page_title = 'Approved Courses - ' . APP_NAME;
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
                <a href="approved.php" class="nav-item nav-link active">Approved</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Approved Courses</h1>
            <p class="text-white mb-0">All published courses</p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="bg-light rounded p-4">
                <?php if (empty($courses)): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-book-open fa-4x text-muted mb-3"></i>
                        <h4>No approved courses</h4>
                        <p class="text-muted">Courses will appear here once approved.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Code</th>
                                    <th>Instructor</th>
                                    <th>Price</th>
                                    <th>Students</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?php echo $course['id']; ?></td>
                                        <td><?php echo htmlspecialchars($course['title']); ?></td>
                                        <td><?php echo htmlspecialchars($course['code']); ?></td>
                                        <td><?php echo htmlspecialchars($course['instructor_name']); ?></td>
                                        <td><?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></td>
                                        <td><?php echo $course['enrollment_count']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($course['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="view.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="../instructor/courses/edit.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-secondary" title="Edit">
                                                    <i class="fa fa-edit"></i>
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