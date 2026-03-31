<?php
// Application Configuration
// Developer: DAVID MUITHYA
// Final Year Project - SkillMaster LMS

// Load bootstrap (handles environment, constants, sessions)
require_once __DIR__ . '/bootstrap.php';

// Load database connection
require_once BASE_PATH . '/config/database.php';

// Include helper functions if needed (will create in Phase 2)
$helpers_file = BASE_PATH . '/app/helpers/functions.php';
if (file_exists($helpers_file)) {
    require_once $helpers_file;
}

// Configuration loaded successfully