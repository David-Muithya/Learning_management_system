<?php
// Display flash messages
if (isset($_SESSION['_flash'])) {
    foreach ($_SESSION['_flash'] as $key => $message) {
        $type = 'info';
        $icon = 'fa-info-circle';
        
        if (strpos($key, 'success') !== false) {
            $type = 'success';
            $icon = 'fa-check-circle';
        } elseif (strpos($key, 'error') !== false) {
            $type = 'danger';
            $icon = 'fa-exclamation-circle';
        } elseif (strpos($key, 'warning') !== false) {
            $type = 'warning';
            $icon = 'fa-exclamation-triangle';
        }
        ?>
        <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show m-3" role="alert">
            <i class="fa <?php echo $icon; ?> me-2"></i>
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php
    }
    unset($_SESSION['_flash']);
}

// Display validation errors
if (isset($errors) && !empty($errors)) {
    foreach ($errors as $error) {
        ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php
    }
}
?>