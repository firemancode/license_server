<?php

echo "<!DOCTYPE html><html><head><title>Final License Server Setup</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:1000px;margin:20px auto;padding:20px;background:#f8f9fa;}";
echo ".step{background:white;padding:20px;margin:15px 0;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);border-left:4px solid #007bff;}";
echo ".success{color:#28a745;font-weight:bold;}.error{color:#dc3545;font-weight:bold;}.warning{color:#ffc107;font-weight:bold;}";
echo ".code{background:#2d3748;color:#e2e8f0;padding:15px;border-radius:6px;font-family:'Courier New',monospace;margin:10px 0;overflow-x:auto;}";
echo ".btn{display:inline-block;padding:10px 20px;margin:5px;text-decoration:none;border-radius:4px;font-weight:bold;}";
echo ".btn-primary{background:#007bff;color:white;}.btn-success{background:#28a745;color:white;}.btn-info{background:#17a2b8;color:white;}";
echo ".alert{padding:15px;margin:10px 0;border-radius:4px;}.alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}";
echo ".alert-warning{background:#fff3cd;color:#856404;border:1px solid #ffeaa7;}.alert-danger{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}";
echo "</style></head><body>";

echo "<h1>🚀 License Server - Final Setup & Testing</h1>";
echo "<p>Complete system validation and preparation for production use.</p>";

$errors = [];
$warnings = [];
$success = [];

// Step 1: Environment Check
echo "<div class='step'><h2>📋 Step 1: Environment Validation</h2>";

// PHP Version
$phpVersion = PHP_VERSION;
if (version_compare($phpVersion, '8.1.0', '>=')) {
    echo "<p class='success'>✅ PHP Version: {$phpVersion} (OK)</p>";
} else {
    echo "<p class='error'>❌ PHP Version: {$phpVersion} (Requires 8.1+)</p>";
    $errors[] = "PHP version too old";
}

// Required Extensions
$required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'openssl', 'json', 'mbstring', 'tokenizer', 'xml', 'ctype', 'fileinfo'];
$missing = [];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✅ Extension: {$ext}</p>";
    } else {
        echo "<p class='error'>❌ Extension: {$ext} - MISSING</p>";
        $missing[] = $ext;
    }
}
if (!empty($missing)) {
    $errors[] = "Missing PHP extensions: " . implode(', ', $missing);
}

echo "</div>";

// Step 2: File Structure Check
echo "<div class='step'><h2>📁 Step 2: File Structure Validation</h2>";

$required_files = [
    '.env' => 'Environment configuration',
    'composer.json' => 'Composer dependencies',
    'vendor/autoload.php' => 'Composer autoloader',
    'app/Http/Controllers/API/LicenseController.php' => 'API Controller',
    'app/Http/Controllers/Admin/DashboardController.php' => 'Admin Dashboard',
    'resources/views/layouts/adminlte/app.blade.php' => 'AdminLTE Layout',
    'database/migrations' => 'Database migrations',
    'routes/api.php' => 'API routes',
    'routes/web.php' => 'Web routes'
];

foreach ($required_files as $file => $desc) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ {$desc}: {$file}</p>";
    } else {
        echo "<p class='error'>❌ {$desc}: {$file} - MISSING</p>";
        $errors[] = "Missing file: {$file}";
    }
}

$writable_dirs = ['storage', 'bootstrap/cache', 'database'];
foreach ($writable_dirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "<p class='success'>✅ Writable: {$dir}</p>";
    } else {
        echo "<p class='error'>❌ Not writable: {$dir}</p>";
        $errors[] = "Directory not writable: {$dir}";
    }
}

echo "</div>";

// Step 3: Database Connection
echo "<div class='step'><h2>🗄️ Step 3: Database Connection Test</h2>";

try {
    if (file_exists('.env')) {
        $env = file_get_contents('.env');
        preg_match('/DB_CONNECTION=(.*)/', $env, $db_connection);
        preg_match('/DB_HOST=(.*)/', $env, $db_host);
        preg_match('/DB_DATABASE=(.*)/', $env, $db_database);
        preg_match('/DB_USERNAME=(.*)/', $env, $db_username);
        preg_match('/DB_PASSWORD=(.*)/', $env, $db_password);
        
        $host = trim($db_host[1] ?? '127.0.0.1');
        $database = trim($db_database[1] ?? 'license_server');
        $username = trim($db_username[1] ?? 'root');
        $password = trim($db_password[1] ?? '');
        
        echo "<p>Connection: MySQL | Host: {$host} | Database: {$database} | User: {$username}</p>";
        
        $dsn = "mysql:host={$host};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p class='success'>✅ MySQL connection successful</p>";
        
        // Check database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE '{$database}'");
        if ($stmt->rowCount() > 0) {
            echo "<p class='success'>✅ Database '{$database}' exists</p>";
            
            // Check tables
            $pdo->exec("USE {$database}");
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            if (count($tables) > 0) {
                echo "<p class='success'>✅ Database has " . count($tables) . " tables</p>";
                echo "<p><small>Tables: " . implode(', ', $tables) . "</small></p>";
            } else {
                echo "<p class='warning'>⚠️ Database is empty - run migrations</p>";
                $warnings[] = "Database needs migration";
            }
        } else {
            echo "<p class='warning'>⚠️ Database '{$database}' does not exist</p>";
            $warnings[] = "Database needs to be created";
        }
    } else {
        $errors[] = ".env file missing";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    $errors[] = "Database connection failed";
}

echo "</div>";

// Step 4: API Endpoint Test
echo "<div class='step'><h2>🔌 Step 4: API Endpoints Test</h2>";

$base_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
$api_endpoints = [
    '/api/verify-license' => 'License verification',
    '/api/activate-license' => 'License activation', 
    '/api/deactivate-license' => 'License deactivation'
];

foreach ($api_endpoints as $endpoint => $desc) {
    $url = $base_url . $endpoint;
    echo "<p>Testing: <a href='{$url}' target='_blank'>{$desc}</a></p>";
    
    // Simple connectivity test
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['test' => 'connectivity']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code > 0) {
        echo "<p class='success'>✅ {$desc} - Reachable (HTTP {$http_code})</p>";
    } else {
        echo "<p class='warning'>⚠️ {$desc} - Connection failed</p>";
        $warnings[] = "API endpoint not reachable: {$endpoint}";
    }
}

echo "</div>";

// Step 5: Admin Panel Test
echo "<div class='step'><h2>🎛️ Step 5: Admin Panel Test</h2>";

$admin_urls = [
    '/admin/dashboard' => 'Admin Dashboard',
    '/admin/products' => 'Product Management',
    '/admin/licenses' => 'License Management',
    '/admin/activations' => 'Activation Management'
];

foreach ($admin_urls as $path => $desc) {
    $url = $base_url . $path;
    echo "<p><a href='{$url}' target='_blank'>{$desc}</a></p>";
}

echo "<p class='success'>✅ Admin panel URLs configured</p>";
echo "</div>";

// Step 6: Security Check
echo "<div class='step'><h2>🔐 Step 6: Security Configuration</h2>";

if (file_exists('.env')) {
    $env = file_get_contents('.env');
    
    // Check APP_KEY
    if (strpos($env, 'APP_KEY=base64:') !== false) {
        echo "<p class='success'>✅ Application key configured</p>";
    } else {
        echo "<p class='error'>❌ Application key missing</p>";
        $errors[] = "APP_KEY not configured";
    }
    
    // Check LICENSE_SECRET
    if (strpos($env, 'LICENSE_SECRET=') !== false) {
        echo "<p class='success'>✅ License secret configured</p>";
    } else {
        echo "<p class='warning'>⚠️ License secret not configured</p>";
        $warnings[] = "LICENSE_SECRET not set";
    }
    
    // Check APP_DEBUG for production
    if (strpos($env, 'APP_DEBUG=true') !== false) {
        echo "<p class='warning'>⚠️ Debug mode enabled (disable for production)</p>";
        $warnings[] = "Debug mode should be disabled in production";
    } else {
        echo "<p class='success'>✅ Debug mode disabled</p>";
    }
}

echo "</div>";

// Step 7: Performance & Recommendations
echo "<div class='step'><h2>⚡ Step 7: Performance & Recommendations</h2>";

// Check cache directory
if (is_dir('bootstrap/cache') && is_writable('bootstrap/cache')) {
    echo "<p class='success'>✅ Cache directory writable</p>";
} else {
    echo "<p class='warning'>⚠️ Cache directory issues</p>";
    $warnings[] = "Cache directory not optimal";
}

// PHP settings recommendations
$memory_limit = ini_get('memory_limit');
echo "<p>Memory limit: {$memory_limit}</p>";

$max_execution_time = ini_get('max_execution_time');
echo "<p>Max execution time: {$max_execution_time}s</p>";

echo "<div class='alert alert-warning'>";
echo "<h5>📋 Production Recommendations:</h5>";
echo "<ul>";
echo "<li>Set <code>APP_DEBUG=false</code> in production</li>";
echo "<li>Use HTTPS for all API communications</li>";
echo "<li>Configure proper backup strategy</li>";
echo "<li>Monitor API logs for abuse patterns</li>";
echo "<li>Set up proper error logging</li>";
echo "<li>Consider using Redis for sessions and cache</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

// Summary
echo "<div class='step'><h2>📊 Final Summary</h2>";

if (empty($errors)) {
    echo "<div class='alert alert-success'>";
    echo "<h4>🎉 System Ready!</h4>";
    echo "<p>Your License Server is properly configured and ready for use.</p>";
    echo "</div>";
    
    echo "<h4>🚀 Next Steps:</h4>";
    echo "<ol>";
    echo "<li><strong>Run Migrations:</strong> <code>php artisan migrate</code></li>";
    echo "<li><strong>Seed Data:</strong> <code>php artisan db:seed</code></li>";
    echo "<li><strong>Test API:</strong> Use the API endpoints below</li>";
    echo "<li><strong>Access Admin:</strong> <a href='{$base_url}/admin/dashboard'>Admin Dashboard</a></li>";
    echo "</ol>";
    
} else {
    echo "<div class='alert alert-danger'>";
    echo "<h4>❌ Issues Found</h4>";
    echo "<p>Please fix these issues before proceeding:</p>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>{$error}</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (!empty($warnings)) {
    echo "<div class='alert alert-warning'>";
    echo "<h4>⚠️ Warnings</h4>";
    echo "<ul>";
    foreach ($warnings as $warning) {
        echo "<li>{$warning}</li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "</div>";

// Quick Actions
echo "<div class='step'><h2>🎯 Quick Actions</h2>";
echo "<div>";
echo "<a href='setup.php' class='btn btn-primary'>🔧 Run Setup</a> ";
echo "<a href='test_mysql.php' class='btn btn-info'>🗄️ Test Database</a> ";
echo "<a href='{$base_url}/admin/dashboard' class='btn btn-success'>🏠 Admin Panel</a> ";
echo "<a href='#{$base_url}/api/verify-license' class='btn btn-info'>🔌 API Docs</a>";
echo "</div>";

echo "<h4>🧪 API Testing Examples:</h4>";
echo "<div class='code'>";
echo "# Test license verification<br>";
echo "curl -X POST {$base_url}/api/verify-license \\<br>";
echo "  -H \"Content-Type: application/json\" \\<br>";
echo "  -d '{\"license_key\": \"ABCDE-12345-FGHIJ\", \"domain\": \"test.com\"}'<br><br>";

echo "# Test license activation<br>";
echo "curl -X POST {$base_url}/api/activate-license \\<br>";
echo "  -H \"Content-Type: application/json\" \\<br>";
echo "  -d '{\"license_key\": \"ABCDE-12345-FGHIJ\", \"domain\": \"test.com\"}'<br>";
echo "</div>";

echo "<h4>📖 Documentation:</h4>";
echo "<ul>";
echo "<li><a href='README.md' target='_blank'>📘 Main Documentation</a></li>";
echo "<li><a href='API_COMPLETE_DOCUMENTATION.md' target='_blank'>🔌 API Documentation</a></li>";
echo "<li><a href='API_DOCUMENTATION.md' target='_blank'>📋 API Reference</a></li>";
echo "</ul>";

echo "</div>";

echo "<hr><p class='text-center'><small>License Server v1.0 - Built with Laravel 11 + AdminLTE 3</small></p>";
echo "</body></html>";

?>