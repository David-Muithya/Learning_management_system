<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Style Test - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { background-color: #F0FBFC; padding: 20px; }
        .test-card { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-primary">Style Verification Test</h1>
        <p class="text-muted">This page verifies that your styling is working correctly.</p>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card test-card">
                    <div class="card-body bg-light">
                        <h5 class="text-dark">Primary Color</h5>
                        <button class="btn btn-primary">Primary Button</button>
                        <button class="btn btn-outline-primary">Outline Primary</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card test-card">
                    <div class="card-body bg-light">
                        <h5 class="text-dark">Success Color</h5>
                        <button class="btn btn-success">Success Button</button>
                        <span class="badge bg-success">Success Badge</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card test-card">
                    <div class="card-body bg-light">
                        <h5 class="text-dark">Danger Color</h5>
                        <button class="btn btn-danger">Danger Button</button>
                        <span class="badge bg-danger">Danger Badge</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card test-card">
                    <div class="card-header bg-primary text-white">Card Header</div>
                    <div class="card-body bg-light">
                        <p>This is a standard card with proper styling.</p>
                        <div class="alert alert-info">Info Alert</div>
                        <div class="alert alert-warning">Warning Alert</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card test-card">
                    <div class="card-body bg-light">
                        <h5>Form Elements</h5>
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control" id="test" placeholder="Test">
                            <label for="test">Form Input</label>
                        </div>
                        <select class="form-select mb-2">
                            <option>Select option</option>
                        </select>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check">
                            <label class="form-check-label" for="check">Checkbox</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card test-card">
                    <div class="card-body bg-light">
                        <h5>Table Styling</h5>
                        <table class="table table-hover">
                            <thead class="bg-primary text-white">
                                <tr><th>Column 1</th><th>Column 2</th><th>Column 3</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Data 1</td><td>Data 2</td><td>Data 3</td></tr>
                                <tr><td>Data 4</td><td>Data 5</td><td>Data 6</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-success">✅ If you see colored buttons and styled elements, everything is working!</p>
            <p class="text-muted">Primary color should be <strong style="color:#06BBCC">#06BBCC (Teal)</strong></p>
        </div>
    </div>
</body>
</html>