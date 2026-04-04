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

// Handle success/error messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Clear session messages
unset($_SESSION['success'], $_SESSION['error']);

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
    
    <style>
        .accordion-button:not(.collapsed) {
            background-color: rgba(6, 187, 204, 0.1);
            color: #06BBCC;
        }
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(6, 187, 204, 0.5);
        }
        .module-card {
            transition: all 0.3s ease;
        }
        .module-card:hover {
            transform: translateY(-2px);
        }
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
    </style>
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
    <div class="container-fluid bg-primary py-4 mb-5" style="background-color: #06BBCC !important;">
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

    <!-- Toast Notification Container -->
    <div class="toast-notification" id="toastContainer"></div>

    <!-- Content Start -->
    <div class="container-xxl py-4">
        <div class="container">
            
            <!-- Messages -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Course Status Bar -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-light rounded p-3 d-flex justify-content-between align-items-center shadow-sm">
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
                                <a href="submit.php?id=<?php echo $course['id']; ?>" class="btn btn-success btn-sm" style="background-color: #198754; border-color: #198754;" onclick="return confirm('Submit this course for approval?')">
                                    <i class="fa fa-paper-plane me-1"></i>Submit for Approval
                                </a>
                            <?php elseif ($course['status'] === 'rejected'): ?>
                                <a href="edit.php?id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit me-1"></i>Edit and Resubmit
                                </a>
                            <?php endif; ?>
                            <a href="edit.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary btn-sm" style="border-color: #06BBCC; color: #06BBCC;">
                                <i class="fa fa-edit me-1"></i>Edit Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Modules Section -->
                <div class="col-lg-8">
                    <div class="bg-light rounded p-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0" style="color: #06BBCC;"><i class="fa fa-cubes me-2"></i>Course Modules</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal" style="background-color: #06BBCC; border-color: #06BBCC;">
                                <i class="fa fa-plus-circle me-1"></i>Add Module
                            </button>
                        </div>
                        
                        <?php if (empty($modules)): ?>
                            <div class="text-center py-5">
                                <i class="fa fa-folder-open fa-4x text-muted mb-3"></i>
                                <p class="text-muted">No modules yet. Add your first module to organize course content.</p>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModuleModal" style="background-color: #06BBCC; border-color: #06BBCC;">
                                    <i class="fa fa-plus-circle me-1"></i>Create First Module
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="accordion" id="modulesAccordion">
                                <?php foreach ($modules as $index => $module): ?>
                                    <div class="accordion-item mb-2 border-0 shadow-sm module-card">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#module<?php echo $module['id']; ?>" style="background-color: #F0FBFC;">
                                                <strong>Module <?php echo $index + 1; ?>: <?php echo htmlspecialchars($module['title']); ?></strong>
                                                <span class="ms-2 badge" style="background-color: #06BBCC;"><?php echo $module['material_count']; ?> materials</span>
                                            </button>
                                        </h2>
                                        <div id="module<?php echo $module['id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#modulesAccordion">
                                            <div class="accordion-body bg-white">
                                                <?php if (!empty($module['description'])): ?>
                                                    <p class="mb-3 text-muted"><i class="fa fa-align-left me-2 text-primary"></i><?php echo nl2br(htmlspecialchars($module['description'])); ?></p>
                                                <?php endif; ?>
                                                
                                                <h6 class="mb-2" style="color: #06BBCC;"><i class="fa fa-paperclip me-2"></i>Materials</h6>
                                                <?php
                                                $materials = $materialModel->getByModule($module['id']);
                                                ?>
                                                <?php if (empty($materials)): ?>
                                                    <div class="text-center py-3 bg-light rounded mb-3">
                                                        <p class="text-muted small mb-0">No materials added yet.</p>
                                                    </div>
                                                <?php else: ?>
                                                    <ul class="list-group list-group-flush mb-3">
                                                        <?php foreach ($materials as $material): ?>
                                                            <li class="list-group-item bg-light mb-1 rounded">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <i class="fa fa-<?php echo $material['type'] === 'video' ? 'video' : ($material['type'] === 'document' ? 'file-alt' : ($material['type'] === 'link' ? 'link' : 'image')); ?> text-primary me-2"></i>
                                                                        <?php echo htmlspecialchars($material['title']); ?>
                                                                        <span class="badge ms-2" style="background-color: #6c757d;"><?php echo ucfirst($material['type']); ?></span>
                                                                    </div>
                                                                    <div>
                                                                        <a href="material-delete.php?id=<?php echo $material['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this material?')">
                                                                            <i class="fa fa-trash"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <?php if (!empty($material['description'])): ?>
                                                                    <small class="text-muted d-block mt-1"><?php echo htmlspecialchars(substr($material['description'], 0, 100)); ?></small>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                                
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal" data-module-id="<?php echo $module['id']; ?>" data-module-title="<?php echo htmlspecialchars($module['title']); ?>" style="border-color: #06BBCC; color: #06BBCC;">
                                                        <i class="fa fa-plus-circle me-1"></i>Add Material
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteModule(<?php echo $module['id']; ?>, <?php echo $course['id']; ?>)">
                                                        <i class="fa fa-trash me-1"></i> Delete Module
                                                    </button>
                                                </div>
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
                    <div class="bg-light rounded p-4 mb-4 shadow-sm">
                        <h5 class="mb-3" style="color: #06BBCC;"><i class="fa fa-info-circle me-2"></i>Course Information</h5>
                        <div class="border-bottom pb-2 mb-2">
                            <strong>Code:</strong> <?php echo htmlspecialchars($course['code']); ?>
                        </div>
                        <div class="border-bottom pb-2 mb-2">
                            <strong>Credits:</strong> <?php echo $course['credits']; ?>
                        </div>
                        <div class="border-bottom pb-2 mb-2">
                            <strong>Price:</strong> <?php echo CURRENCY_SYMBOL; ?> <?php echo number_format($course['price'], 2); ?>
                        </div>
                        <div class="border-bottom pb-2 mb-2">
                            <strong>Max Students:</strong> <?php echo $course['max_students']; ?>
                        </div>
                        <div class="border-bottom pb-2 mb-2">
                            <strong>Enrolled:</strong> <?php echo $course['enrollment_count']; ?> students
                        </div>
                        <?php if ($course['start_date']): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <strong>Start Date:</strong> <?php echo date('M d, Y', strtotime($course['start_date'])); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($course['end_date']): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <strong>End Date:</strong> <?php echo date('M d, Y', strtotime($course['end_date'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-light rounded p-4 shadow-sm">
                        <h5 class="mb-3" style="color: #06BBCC;"><i class="fa fa-bolt me-2"></i>Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="../assignments/create.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline-primary" style="border-color: #06BBCC; color: #06BBCC;">
                                <i class="fa fa-tasks me-2"></i>Create Assignment
                            </a>
                            <a href="../students/enrolled.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline-primary" style="border-color: #06BBCC; color: #06BBCC;">
                                <i class="fa fa-users me-2"></i>View Enrolled Students
                            </a>
                            <a href="../../course-details.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-secondary" target="_blank">
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
                <form id="addModuleForm">
                    <?php echo Security::csrfField(); ?>
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                    <div class="modal-header" style="background-color: #06BBCC;">
                        <h5 class="modal-title text-white">Add New Module</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Module Title *</label>
                            <input type="text" class="form-control" name="title" id="moduleTitle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" id="moduleDescription" rows="3" placeholder="Brief description of what this module covers..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #06BBCC; border-color: #06BBCC;">Add Module</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Material Modal -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addMaterialForm" enctype="multipart/form-data">
                    <?php echo Security::csrfField(); ?>
                    <input type="hidden" name="module_id" id="material_module_id">
                    <div class="modal-header" style="background-color: #06BBCC;">
                        <h5 class="modal-title text-white">Add Material to <span id="module_title_display"></span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Material Title *</label>
                            <input type="text" class="form-control" name="title" id="materialTitle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type</label>
                            <select class="form-select" name="type" id="material_type" required>
                                <option value="document">📄 Document (PDF, DOC, etc.)</option>
                                <option value="video">🎥 Video</option>
                                <option value="audio">🎵 Audio</option>
                                <option value="image">🖼️ Image</option>
                                <option value="link">🔗 External Link</option>
                            </select>
                        </div>
                        <div class="mb-3" id="file_upload_div">
                            <label class="form-label fw-bold">File</label>
                            <input type="file" class="form-control" name="file" id="material_file" accept=".pdf,.doc,.docx,.mp4,.mp3,.jpg,.jpeg,.png">
                            <small class="text-muted">Max size: 10MB. Allowed: PDF, DOC, MP4, MP3, JPG, PNG</small>
                        </div>
                        <div class="mb-3" id="url_div" style="display: none;">
                            <label class="form-label fw-bold">URL</label>
                            <input type="url" class="form-control" name="content_url" id="material_url" placeholder="https://...">
                            <small class="text-muted">Link to YouTube video, article, or external resource</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description (Optional)</label>
                            <textarea class="form-control" name="description" id="materialDescription" rows="2" placeholder="Brief description of this material..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #06BBCC; border-color: #06BBCC;">Add Material</button>
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
                
                // Reset form
                document.getElementById('addMaterialForm').reset();
                document.getElementById('materialTitle').value = '';
                document.getElementById('materialDescription').value = '';
                document.getElementById('material_type').value = 'document';
                
                // Reset file/url visibility
                document.getElementById('file_upload_div').style.display = 'block';
                document.getElementById('url_div').style.display = 'none';
                document.getElementById('material_file').required = true;
                document.getElementById('material_url').required = false;
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
                    document.getElementById('material_url').required = true;
                } else {
                    fileUploadDiv.style.display = 'block';
                    urlDiv.style.display = 'none';
                    document.getElementById('material_file').required = true;
                    document.getElementById('material_url').required = false;
                }
            });
        }
        
        // Handle Module Form Submission with AJAX
        document.getElementById('addModuleForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('module-add.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                showToast('error', 'An error occurred. Please try again.');
            });
        });
        
        // Handle Material Form Submission with AJAX
        document.getElementById('addMaterialForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('material-add.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // First check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                // Try to parse JSON
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        showToast('success', data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast('error', data.message || 'Unknown error occurred');
                    }
                } catch (e) {
                    // If JSON parsing fails, show the raw response
                    console.error('JSON Parse Error:', e);
                    console.error('Response text:', text);
                    showToast('error', 'Server error: Invalid response. Check browser console.');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                showToast('error', 'Network error: ' + error.message);
            });
        });
        
        // Toast notification function
        function showToast(type, message) {
            const toastContainer = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show shadow`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            toastContainer.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
        
        // Confirm delete module
        function confirmDeleteModule(moduleId, courseId) {
            if (confirm('⚠️ Delete this module?\n\nAll materials in this module will also be deleted. This action cannot be undone.')) {
                window.location.href = `module-delete.php?id=${moduleId}&course_id=${courseId}`;
            }
        }
    </script>
</body>
</html>