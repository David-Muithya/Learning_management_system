<?php
// Manage Course Content
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Course;
use SkillMaster\Models\Module;
use SkillMaster\Models\Material;
use SkillMaster\Helpers\Security;
use SkillMaster\Services\FileUploadService;

RoleMiddleware::check('instructor');

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$courseModel = new Course();
$moduleModel = new Module();
$materialModel = new Material();

$course = $courseModel->getById($courseId);

// Verify instructor owns this course
if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
    header('Location: my-courses.php');
    exit;
}

// Get modules for this course
$modules = $moduleModel->getByCourse($courseId);

$page_title = 'Manage Course - ' . $course['title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
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
                    <h1 class="text-white"><?php echo htmlspecialchars($course['title']); ?></h1>
                    <p class="text-white mb-0">Manage course content, modules, and materials</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Course Status Bar -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-light rounded p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Status:</strong>
                            <span class="badge bg-<?php 
                                echo $course['status'] === 'published' ? 'success' : 
                                     ($course['status'] === 'pending_approval' ? 'warning' : 
                                     ($course['status'] === 'rejected' ? 'danger' : 'secondary')); 
                            ?> ms-2">
                                <?php echo ucfirst(str_replace('_', ' ', $course['status'])); ?>
                            </span>
                        </div>
                        <div>
                            <?php if ($course['status'] === 'draft'): ?>
                                <a href="submit.php?id=<?php echo $course['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Submit this course for approval?')">
                                    <i class="fa fa-paper-plane me-1"></i>Submit for Approval
                                </a>
                            <?php elseif ($course['status'] === 'rejected'): ?>
                                <a href="edit.php?id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit me-1"></i>Edit and Resubmit
                                </a>
                            <?php endif; ?>
                            <a href="edit.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-edit me-1"></i>Edit Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Modules Section -->
                <div class="col-lg-8">
                    <div class="bg-light rounded p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Course Modules</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                                <i class="fa fa-plus-circle me-1"></i>Add Module
                            </button>
                        </div>
                        
                        <?php if (empty($modules)): ?>
                            <div class="text-center py-4">
                                <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No modules yet. Add your first module to organize course content.</p>
                            </div>
                        <?php else: ?>
                            <div class="accordion" id="modulesAccordion">
                                <?php foreach ($modules as $index => $module): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#module<?php echo $module['id']; ?>">
                                                <strong>Module <?php echo $index + 1; ?>: <?php echo htmlspecialchars($module['title']); ?></strong>
                                                <span class="ms-2 badge bg-primary"><?php echo $module['material_count']; ?> materials</span>
                                            </button>
                                        </h2>
                                        <div id="module<?php echo $module['id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#modulesAccordion">
                                            <div class="accordion-body">
                                                <p class="mb-3"><?php echo htmlspecialchars($module['description']); ?></p>
                                                
                                                <h6 class="mb-2">Materials</h6>
                                                <?php
                                                $materials = $materialModel->getByModule($module['id']);
                                                ?>
                                                <?php if (empty($materials)): ?>
                                                    <p class="text-muted small">No materials added yet.</p>
                                                <?php else: ?>
                                                    <ul class="list-group list-group-flush mb-3">
                                                        <?php foreach ($materials as $material): ?>
                                                            <li class="list-group-item bg-transparent">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <i class="fa fa-<?php echo $material['type'] === 'video' ? 'video' : ($material['type'] === 'document' ? 'file-alt' : 'link'); ?> text-primary me-2"></i>
                                                                        <?php echo htmlspecialchars($material['title']); ?>
                                                                        <span class="badge bg-secondary ms-2"><?php echo $material['type']; ?></span>
                                                                    </div>
                                                                    <div>
                                                                        <a href="material-delete.php?id=<?php echo $material['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this material?')">
                                                                            <i class="fa fa-trash"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                                
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal" data-module-id="<?php echo $module['id']; ?>" data-module-title="<?php echo htmlspecialchars($module['title']); ?>">
                                                    <i class="fa fa-plus-circle me-1"></i>Add Material
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger float-end" onclick="confirmDeleteModule(<?php echo $module['id']; ?>)">
                                                    <i class="fa fa-trash"></i> Delete Module
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Course Info Sidebar -->
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3">Course Information</h5>
                        <p><strong>Code:</strong> <?php echo htmlspecialchars($course['code']); ?></p>
                        <p><strong>Credits:</strong> <?php echo $course['credits']; ?></p>
                        <p><strong>Price:</strong> <?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?></p>
                        <p><strong>Max Students:</strong> <?php echo $course['max_students']; ?></p>
                        <p><strong>Enrolled:</strong> <?php echo $course['enrollment_count']; ?> students</p>
                        <?php if ($course['start_date']): ?>
                            <p><strong>Start Date:</strong> <?php echo date('M d, Y', strtotime($course['start_date'])); ?></p>
                        <?php endif; ?>
                        <?php if ($course['end_date']): ?>
                            <p><strong>End Date:</strong> <?php echo date('M d, Y', strtotime($course['end_date'])); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="../assignments/create.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline-primary">
                                <i class="fa fa-tasks me-2"></i>Create Assignment
                            </a>
                            <a href="../students/enrolled.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline-primary">
                                <i class="fa fa-users me-2"></i>View Enrolled Students
                            </a>
                            <a href="preview.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-secondary" target="_blank">
                                <i class="fa fa-eye me-2"></i>Preview Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content End -->

    <!-- Add Module Modal -->
    <div class="modal fade" id="addModuleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="module-add.php">
                    <?php echo Security::csrfField(); ?>
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Module</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Module Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Module</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Material Modal -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="material-add.php" enctype="multipart/form-data">
                    <?php echo Security::csrfField(); ?>
                    <input type="hidden" name="module_id" id="material_module_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Material to <span id="module_title_display"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Material Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type" id="material_type" required>
                                <option value="document">Document (PDF, DOC, etc.)</option>
                                <option value="video">Video</option>
                                <option value="audio">Audio</option>
                                <option value="image">Image</option>
                                <option value="link">External Link</option>
                            </select>
                        </div>
                        <div class="mb-3" id="file_upload_div">
                            <label class="form-label">File</label>
                            <input type="file" class="form-control" name="file" id="material_file">
                        </div>
                        <div class="mb-3" id="url_div" style="display: none;">
                            <label class="form-label">URL</label>
                            <input type="url" class="form-control" name="content_url" placeholder="https://...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Material</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?php echo date('Y'); ?> <a class="border-bottom text-primary" href="#"><?php echo APP_NAME; ?></a>, All Rights Reserved.
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script>
        // Handle module ID for material modal
        const addMaterialModal = document.getElementById('addMaterialModal');
        if (addMaterialModal) {
            addMaterialModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const moduleId = button.getAttribute('data-module-id');
                const moduleTitle = button.getAttribute('data-module-title');
                const modalTitle = addMaterialModal.querySelector('#module_title_display');
                const moduleIdInput = addMaterialModal.querySelector('#material_module_id');
                modalTitle.textContent = moduleTitle;
                moduleIdInput.value = moduleId;
            });
        }
        
        // Handle material type change
        const materialType = document.getElementById('material_type');
        const fileUploadDiv = document.getElementById('file_upload_div');
        const urlDiv = document.getElementById('url_div');
        
        if (materialType) {
            materialType.addEventListener('change', function() {
                if (this.value === 'link') {
                    fileUploadDiv.style.display = 'none';
                    urlDiv.style.display = 'block';
                    document.getElementById('material_file').required = false;
                } else {
                    fileUploadDiv.style.display = 'block';
                    urlDiv.style.display = 'none';
                    document.getElementById('material_file').required = true;
                }
            });
        }
        
        function confirmDeleteModule(moduleId) {
            if (confirm('Delete this module? All materials in this module will also be deleted.')) {
                window.location.href = 'module-delete.php?id=' + moduleId;
            }
        }
    </script>
</body>
</html>