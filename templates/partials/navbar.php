<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="<?php echo BASE_URL; ?>/index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="<?php echo BASE_URL; ?>/index.php" class="nav-item nav-link <?php echo basename($_SERVER['REQUEST_URI']) == 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo BASE_URL; ?>/about.php" class="nav-item nav-link <?php echo basename($_SERVER['REQUEST_URI']) == 'about.php' ? 'active' : ''; ?>">About</a>
            <a href="<?php echo BASE_URL; ?>/courses.php" class="nav-item nav-link <?php echo basename($_SERVER['REQUEST_URI']) == 'courses.php' ? 'active' : ''; ?>">Courses</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                <div class="dropdown-menu fade-down m-0">
                    <a href="<?php echo BASE_URL; ?>/instructors.php" class="dropdown-item">Our Instructors</a>
                    <a href="<?php echo BASE_URL; ?>/testimonials.php" class="dropdown-item">Testimonials</a>
                    <a href="<?php echo BASE_URL; ?>/apply-instructor.php" class="dropdown-item">Become an Instructor</a>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>/contact.php" class="nav-item nav-link <?php echo basename($_SERVER['REQUEST_URI']) == 'contact.php' ? 'active' : ''; ?>">Contact</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>/<?php echo $_SESSION['user_role']; ?>/index.php" class="nav-item nav-link">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="nav-item nav-link">Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/login.php" class="nav-item nav-link">Login</a>
            <?php endif; ?>
        </div>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
                Join Now<i class="fa fa-arrow-right ms-3"></i>
            </a>
        <?php endif; ?>
    </div>
</nav>
<!-- Navbar End -->