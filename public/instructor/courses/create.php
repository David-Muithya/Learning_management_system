<?php
// Create Course
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;
use SkillMaster\Services\FileUploadService;

RoleMiddleware::check('instructor');

$courseModel = new Course();
$fileUpload = new FileUploadService(COURSE_UPLOAD_PATH);

$error = '';
$success = '';
$categories = $courseModel->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $data = [
            'title' => Validation::sanitize($_POST['title'] ?? ''),
            'description' => Validation::sanitize($_POST['description'] ?? ''),
            'short_description' => Validation::sanitize($_POST['short_description'] ?? ''),
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'price' => (float)($_POST['price'] ?? 0),
            'credits' => (int)($_POST['credits'] ?? 3),
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'max_students' => (int)($_POST['max_students'] ?? 50),
            'syllabus' => Validation::sanitize($_POST['syllabus'] ?? '')
        ];
        
        // Validate required fields
        if (empty($data['title']) || empty($data['description'])) {
            $error = 'Please fill in all required fields.';
        } else {
            // Handle thumbnail upload
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $fileUpload->upload($_FILES['thumbnail'], '', 'course_' . time());
                if ($uploadResult) {
                    $data['thumbnail'] = $uploadResult['filename'];
                } else {
                    $error = 'Failed to upload thumbnail. ' . implode(', ', $fileUpload->getErrors());
                }
            }
            
            if (empty($error)) {
                $result = $courseModel->createCourse($data, $_SESSION['user_id']);
                
                if ($result) {
                    $courseId = $courseModel->db->lastInsertId();
                    $success = 'Course created successfully! You can now add content to your course.';
                    // Redirect to manage page after 2 seconds
                    header("refresh:2;url=manage.php?id=$courseId");
                } else {
                    $error = 'Failed to create course. Please try again.';
                }
            }
        }
    }
}

$page_title = 'Create Course - ' . APP_NAME;
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
    
    <!-- Summernote CSS (for rich text editor) -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i><?php echo APP_NAME; ?></h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link">Dashboard</a>
                <a href="my-courses.php" class="nav-item nav-link">My Courses</a>
                <a href="../assignments/list.php" class="nav-item nav-link">Assignments</a>
                <a href="../profile/index.php" class="nav-item nav-link">Profile</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="text-white">Create New Course</h1>
                    <p class="text-white mb-0">Share your knowledge with the world</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Form Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="bg-light rounded p-5">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold">Course Title *</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Category</label>
                                    <select class="form-select" name="category_id">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Short Description</label>
                                <textarea class="form-control" name="short_description" rows="2" placeholder="Brief description (max 500 characters)"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Description *</label>
                                <textarea class="form-control summernote" name="description" rows="8" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Price (<?php echo CURRENCY_SYMBOL; ?>)</label>
                                    <input type="number" class="form-control" name="price" step="0.01" value="0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Credits</label>
                                    <input type="number" class="form-control" name="credits" value="3">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Max Students</label>
                                    <input type="number" class="form-control" name="max_students" value="50">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Start Date</label>
                                    <input type="date" class="form-control" name="start_date">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">End Date</label>
                                    <input type="date" class="form-control" name="end_date">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Course Syllabus</label>
                                <textarea class="form-control" name="syllabus" rows="6" placeholder="Week 1: Introduction&#10;Week 2: Advanced Topics&#10;Week 3: Final Project"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Course Thumbnail</label>
                                <input type="file" class="form-control" name="thumbnail" accept="image/*">
                                <small class="text-muted">Recommended size: 800x450 pixels. Max size: 2MB</small>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>Note:</strong> Your course will be saved as a draft. You can edit and submit for approval later.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="my-courses.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save as Draft</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Form End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview']]
                ]
            });
        });
    </script>
</body>
</html>