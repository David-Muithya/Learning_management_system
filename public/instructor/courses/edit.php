<?php
// Edit Course
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;
use SkillMaster\Services\FileUploadService;

RoleMiddleware::check('instructor');

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$courseModel = new Course();
$fileUpload = new FileUploadService(COURSE_UPLOAD_PATH);

$course = $courseModel->getById($courseId);

// Verify instructor owns this course
if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
    header('Location: my-courses.php');
    exit;
}

$categories = $courseModel->getCategories();
$error = '';
$success = '';

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
        
        if (empty($data['title']) || empty($data['description'])) {
            $error = 'Please fill in all required fields.';
        } else {
            // Handle thumbnail upload
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $fileUpload->upload($_FILES['thumbnail'], '', 'course_' . time());
                if ($uploadResult) {
                    // Delete old thumbnail if exists
                    if ($course['thumbnail'] && file_exists(COURSE_UPLOAD_PATH . $course['thumbnail'])) {
                        unlink(COURSE_UPLOAD_PATH . $course['thumbnail']);
                    }
                    $data['thumbnail'] = $uploadResult['filename'];
                }
            }
            
            $result = $courseModel->updateCourse($courseId, $data);
            
            if ($result) {
                $success = 'Course updated successfully!';
                $course = $courseModel->getById($courseId);
            } else {
                $error = 'Failed to update course.';
            }
        }
    }
}

$page_title = 'Edit Course - ' . APP_NAME;
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
                <a href="my-courses.php" class="nav-item nav-link active">My Courses</a>
                <a href="../../logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-primary py-4 mb-5">
        <div class="container text-center">
            <h1 class="text-white">Edit Course</h1>
            <p class="text-white mb-0"><?php echo htmlspecialchars($course['title']); ?></p>
        </div>
    </div>

    <div class="container-xxl py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <div class="bg-light rounded p-5">
                        <form method="POST" enctype="multipart/form-data">
                            <?php echo Security::csrfField(); ?>
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold">Course Title *</label>
                                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Category</label>
                                    <select class="form-select" name="category_id">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo $course['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Short Description</label>
                                <textarea class="form-control" name="short_description" rows="2"><?php echo htmlspecialchars($course['short_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Description *</label>
                                <textarea class="form-control" name="description" rows="8" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Price (<?php echo CURRENCY_SYMBOL; ?>)</label>
                                    <input type="number" class="form-control" name="price" step="0.01" value="<?php echo $course['price']; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Credits</label>
                                    <input type="number" class="form-control" name="credits" value="<?php echo $course['credits']; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Max Students</label>
                                    <input type="number" class="form-control" name="max_students" value="<?php echo $course['max_students']; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" value="<?php echo $course['start_date']; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">End Date</label>
                                    <input type="date" class="form-control" name="end_date" value="<?php echo $course['end_date']; ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Course Syllabus</label>
                                <textarea class="form-control" name="syllabus" rows="6"><?php echo htmlspecialchars($course['syllabus'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Course Thumbnail</label>
                                <?php if ($course['thumbnail']): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo '../../uploads/courses/' . $course['thumbnail']; ?>" alt="Current thumbnail" style="height: 100px;">
                                        <br><small class="text-muted">Current thumbnail</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" name="thumbnail" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="my-courses.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="manage.php?id=<?php echo $courseId; ?>" class="btn btn-info">Manage Content</a>
                                <?php if ($course['status'] === 'draft'): ?>
                                    <a href="submit.php?id=<?php echo $courseId; ?>" class="btn btn-success" onclick="return confirm('Submit this course for approval?')">Submit for Approval</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/js/all.min.js"></script>
</body>
</html>