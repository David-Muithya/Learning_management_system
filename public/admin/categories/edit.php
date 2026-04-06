<?php
// Edit Category Handler
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

$id = (int)($_POST['id'] ?? 0);
$name = Validation::sanitize($_POST['name'] ?? '');
$slug = Validation::sanitize($_POST['slug'] ?? '');
$description = Validation::sanitize($_POST['description'] ?? '');
$parentId = (int)($_POST['parent_id'] ?? 0);

if ($id <= 0 || empty($name)) {
    $_SESSION['error'] = 'Invalid category data.';
    header('Location: index.php');
    exit;
}

// Prevent setting parent to itself
if ($parentId == $id) {
    $_SESSION['error'] = 'A category cannot be its own parent.';
    header('Location: index.php');
    exit;
}

$categoryModel = new Category();
$result = $categoryModel->update($id, $name, $slug, $description, $parentId);

if ($result) {
    $_SESSION['success'] = 'Category updated successfully!';
} else {
    $_SESSION['error'] = 'Failed to update category.';
}

header('Location: index.php');
exit;