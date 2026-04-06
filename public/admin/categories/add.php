<?php
// Add Category Handler
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\Category;
use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;

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

$name = Validation::sanitize($_POST['name'] ?? '');
$slug = Validation::sanitize($_POST['slug'] ?? '');
$description = Validation::sanitize($_POST['description'] ?? '');
$parentId = (int)($_POST['parent_id'] ?? 0);

if (empty($name)) {
    $_SESSION['error'] = 'Category name is required.';
    header('Location: index.php');
    exit;
}

$categoryModel = new Category();
$result = $categoryModel->create($name, $slug, $description, $parentId);

if ($result) {
    $_SESSION['success'] = 'Category created successfully!';
} else {
    $_SESSION['error'] = 'Failed to create category.';
}

header('Location: index.php');
exit;