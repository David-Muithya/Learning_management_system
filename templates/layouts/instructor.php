<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title ?? APP_NAME; ?> - Instructor Portal</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Customized Bootstrap Stylesheet -->
    <link href="<?php echo BASE_URL; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Template Stylesheet -->
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
    
    <style>
        .sidebar {
            min-height: calc(100vh - 70px);
            background-color: #181d38;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #06BBCC;
            color: #fff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        .content-wrapper {
            background-color: #f4f6f9;
            min-height: calc(100vh - 70px);
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="<?php echo BASE_URL; ?>/instructor/index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="<?php echo BASE_URL; ?>/instructor/index.php" class="nav-item nav-link">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/instructor/profile/index.php" class="nav-item nav-link">Profile</a>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fa fa-chalkboard-user fa-2x text-white"></i>
                        </div>
                        <h6 class="text-white mb-0"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Instructor'); ?></h6>
                        <small class="text-white-50">Instructor</small>
                    </div>
                    <hr class="text-white-50">
                    <nav class="nav flex-column">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/instructor/index.php">
                            <i class="fa fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/instructor/courses/my-courses.php">
                            <i class="fa fa-book"></i> My Courses
                        </a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/instructor/courses/create.php">
                            <i class="fa fa-plus-circle"></i> Create Course
                        </a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/instructor/assignments/list.php">
                            <i class="fa fa-tasks"></i> Assignments
                        </a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/instructor/students/enrolled.php">
                            <i class="fa fa-users"></i> Students
                        </a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/instructor/announcements/list.php">
                            <i class="fa fa-bullhorn"></i> Announcements
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4 content-wrapper">
                <?php require_once BASE_PATH . '/templates/partials/alerts.php'; ?>
                <?php echo $content ?? '<p>Content goes here</p>'; ?>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>