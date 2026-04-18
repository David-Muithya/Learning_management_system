<?php
// View Assignment Grades - Premium Version
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Grade;

RoleMiddleware::check('student');

$gradeModel = new Grade();
$grades = $gradeModel->getByStudent($_SESSION['user_id']);

$page_title = 'My Grades - ' . APP_NAME;
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
        :root {
            --teal-primary: #06BBCC;
            --teal-dark: #0598A6;
            --navy-dark: #181d38;
        }
        
        body {
            background: linear-gradient(135deg, #F0FBFC 0%, #E6F8FA 100%);
        }
        
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
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            animation: pulse 6s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        
        .grade-card {
            background: white;
            border-radius: 24px;
            transition: all 0.3s ease;
        }
        
        .grade-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(6, 187, 204, 0.1);
        }
        
        .badge-grade-A {
            background: linear-gradient(135deg, #198754, #157347);
            color: white;
        }
        
        .badge-grade-B {
            background: linear-gradient(135deg, #0dcaf0, #0aa5c6);
            color: #000;
        }
        
        .badge-grade-C {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #000;
        }
        
        .badge-grade-D {
            background: linear-gradient(135deg, #fd7e14, #e06e0c);
            color: white;
        }
        
        .badge-grade-F {
            background: linear-gradient(135deg, #dc3545, #bb2d3b);
            color: white;
        }
        
        .table-custom {
            border-radius: 16px;
            overflow: hidden;
        }
        
        .table-custom thead th {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            color: white;
            font-weight: 600;
            border: none;
        }
        
        .table-custom tbody tr:hover {
            background-color: #E6F8FA;
        }
        
        .feedback-btn {
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        
        .feedback-btn:hover {
            transform: translateY(-2px);
        }
        
        .modal-header-premium {
            background: linear-gradient(135deg, #06BBCC, #0598A6);
            color: white;
        }
    </style>
</head>
<body>

    <!-- Premium Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-crown me-2"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="../courses/enrolled.php" class="nav-item nav-link">My Courses</a>
                <a href="pending.php" class="nav-item nav-link">Assignments</a>
                <a href="grades.php" class="nav-item nav-link active">Grades</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Premium Navbar End -->

    <!-- Premium Header Start -->
    <div class="container-fluid premium-header py-4 mb-5">
        <div class="container text-center position-relative" style="z-index: 2;">
            <div class="d-inline-flex align-items-center justify-content-center mb-3" style="background: rgba(255,255,255,0.2); width: 70px; height: 70px; border-radius: 50%;">
                <i class="fa fa-star fa-2x text-white"></i>
            </div>
            <h1 class="text-white display-5 fw-bold mb-2">My Grades</h1>
            <p class="text-white opacity-75 mb-0">Track your academic performance</p>
        </div>
    </div>
    <!-- Premium Header End -->

    <div class="container-xxl py-4">
        <div class="container">
            <div class="grade-card p-4">
                <?php if (empty($grades)): ?>
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fa fa-chart-line fa-3x text-primary"></i>
                        </div>
                        <h4>No grades available</h4>
                        <p class="text-muted">Your grades will appear here once assignments are graded.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Assignment</th>
                                    <th>Course</th>
                                    <th>Score</th>
                                    <th>Max Points</th>
                                    <th>Percentage</th>
                                    <th>Grade</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grades as $grade): 
                                    $percentage = ($grade['grade_value'] / $grade['max_points']) * 100;
                                    $gradeLetter = $grade['letter_grade'];
                                    $feedback = $grade['feedback'] ?? '';
                                ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($grade['assignment_title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($grade['course_title']); ?></td>
                                        <td><span class="fw-bold"><?php echo $grade['grade_value']; ?></span></td>
                                        <td><?php echo $grade['max_points']; ?></td>
                                        <td>
                                            <?php echo round($percentage, 1); ?>%
                                            <div class="progress mt-1" style="height: 6px; border-radius: 3px;">
                                                <div class="progress-bar bg-<?php echo $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger'); ?>" 
                                                     style="width: <?php echo $percentage; ?>%; border-radius: 3px;"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-grade-<?php echo $gradeLetter; ?> px-3 py-2 rounded-pill">
                                                <?php echo $gradeLetter; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($feedback)): ?>
                                                <button class="btn btn-sm btn-outline-primary feedback-btn" data-bs-toggle="modal" data-bs-target="#feedbackModal<?php echo $grade['id']; ?>">
                                                    <i class="fa fa-comment-dots me-1"></i> View Feedback
                                                </button>
                                                
                                                <!-- Feedback Modal -->
                                                <div class="modal fade" id="feedbackModal<?php echo $grade['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header modal-header-premium">
                                                                <h5 class="modal-title text-white">
                                                                    <i class="fa fa-comment me-2"></i>Feedback - <?php echo htmlspecialchars($grade['assignment_title']); ?>
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <strong>Assignment:</strong> <?php echo htmlspecialchars($grade['assignment_title']); ?>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <strong>Course:</strong> <?php echo htmlspecialchars($grade['course_title']); ?>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <strong>Grade:</strong> <?php echo $grade['grade_value']; ?> / <?php echo $grade['max_points']; ?> (<?php echo $gradeLetter; ?>)
                                                                </div>
                                                                <hr>
                                                                <div class="mb-3">
                                                                    <strong><i class="fa fa-commenting text-primary me-2"></i>Instructor Feedback:</strong>
                                                                </div>
                                                                <div class="bg-light p-3 rounded" style="border-left: 3px solid #06BBCC;">
                                                                    <?php echo nl2br(htmlspecialchars($feedback)); ?>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fa fa-clock me-1"></i> No feedback yet</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="../index.php">Home</a>
                        <a href="../about.php">About</a>
                        <a href="../contact.php">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" style="background-color: #06BBCC; border-color: #06BBCC;"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    
    <script>
        // Smooth back to top
        document.querySelector('.back-to-top')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>