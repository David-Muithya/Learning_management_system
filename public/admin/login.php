<?php
// Admin Login Page - Hidden Portal
require_once __DIR__ . '/../../config/config.php';
use SkillMaster\Auth\Authenticator;
use SkillMaster\Auth\RoleMiddleware;

// Redirect if already logged in as admin
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $auth = new Authenticator();
        $result = $auth->login($email, $password);
        
        if ($result['success'] && $_SESSION['user_role'] === 'admin') {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid admin credentials';
        }
    }
}

$page_title = 'Admin Login - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #06BBCC 0%, #181d38 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
        .admin-login-card h2 {
            color: #181d38;
            margin-bottom: 10px;
        }
        .admin-login-card p {
            color: #6c757d;
            margin-bottom: 30px;
        }
        .btn-primary {
            background-color: #06BBCC;
            border-color: #06BBCC;
        }
        .btn-primary:hover {
            background-color: #0598a6;
            border-color: #0598a6;
        }
    </style>
</head>
<body>
    <div class="admin-login-card">
        <div class="text-center">
            <i class="fa fa-lock fa-3x text-primary mb-3"></i>
            <h2>Admin Portal</h2>
            <p>Restricted access - Authorized personnel only</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Login to Admin Portal</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="../index.php" class="text-muted">← Back to Main Site</a>
        </div>
    </div>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>