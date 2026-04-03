<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Interactive Style Test - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .hover-test:hover { background-color: #e9ecef; cursor: pointer; }
        .card-hover { transition: transform 0.3s; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body style="background-color: #F0FBFC;">

    <div class="container py-5">
        <h1 class="text-primary text-center">Interactive Style Verification</h1>
        <p class="text-center text-muted">Hover over elements to verify interactivity</p>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card card-hover">
                    <div class="card-body bg-light text-center">
                        <i class="fa fa-mouse-pointer fa-3x text-primary mb-3"></i>
                        <h5>Hover Effect</h5>
                        <p>This card lifts up on hover</p>
                        <button class="btn btn-primary">Primary Button</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-hover">
                    <div class="card-body bg-light text-center">
                        <i class="fa fa-link fa-3x text-primary mb-3"></i>
                        <h5>Link Hover</h5>
                        <a href="#" class="text-primary">Hover over this link</a>
                        <p class="mt-2"><small>Links should change color on hover</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-hover">
                    <div class="card-body bg-light text-center">
                        <i class="fa fa-table fa-3x text-primary mb-3"></i>
                        <h5>Table Row Hover</h5>
                        <table class="table table-sm">
                            <tr class="hover-test"><td>Hover over this row</td></tr>
                            <tr class="hover-test"><td>Background changes color</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fa fa-check-circle me-2"></i> Navigation Test
                    </div>
                    <div class="card-body bg-light">
                        <p>Check if these navigation elements work:</p>
                        <ul>
                            <li><a href="index.php" class="text-primary">Home Page Link</a></li>
                            <li><a href="login.php" class="text-primary">Login Page Link</a></li>
                            <li><a href="register.php" class="text-primary">Register Page Link</a></li>
                            <li><a href="courses.php" class="text-primary">Courses Page Link</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fa fa-check-circle me-2"></i> Form Element Test
                    </div>
                    <div class="card-body bg-light">
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control" id="testInput" placeholder="Test">
                            <label for="testInput">Focus on this input</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="testCheck">
                            <label class="form-check-label" for="testCheck">Checkbox should be clickable</label>
                        </div>
                        <button class="btn btn-primary mt-2">Submit Button</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5 p-4 bg-light rounded">
            <h4>✅ Verification Complete</h4>
            <p>If you see:</p>
            <ul class="list-inline">
                <li class="list-inline-item"><span class="badge bg-primary">Primary color #06BBCC</span></li>
                <li class="list-inline-item"><span class="badge bg-success">Buttons change on hover</span></li>
                <li class="list-inline-item"><span class="badge bg-info">Cards have shadows on hover</span></li>
                <li class="list-inline-item"><span class="badge bg-warning">Links change color</span></li>
            </ul>
            <p class="mt-3">Then all styles are properly synchronized!</p>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>