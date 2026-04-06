<?php
// Delete Category Handler
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Category;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Invalid security token.';
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid category ID.';
    header('Location: index.php');
    exit;
}

$categoryModel = new Category();
$result = $categoryModel->delete($id);

if ($result) {
    $_SESSION['success'] = 'Category deleted successfully!';
} else {
    $_SESSION['error'] = 'Failed to delete category.';
}

header('Location: index.php');
exit;