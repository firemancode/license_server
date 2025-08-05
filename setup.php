<?php

echo "<!DOCTYPE html><html><head><title>License Server Setup</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:20px auto;padding:20px;background:#f5f5f5;}";
echo ".step{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo ".success{color:#27ae60;font-weight:bold;}.error{color:#e74c3c;font-weight:bold;}.warning{color:#f39c12;font-weight:bold;}";
echo ".code{background:#2c3e50;color:#ecf0f1;padding:10px;border-radius:4px;font-family:monospace;margin:10px 0;}";
echo "</style></head><body>";

echo "<h1>🚀 License Server Automated Setup</h1>";

$steps = [];
$errors = [];

// Step 1: Check PHP Extensions
echo "<div class='step'><h2>Step 1: PHP Extensions Check</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'openssl', 'json', 'mbstring'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✅ {$ext} - OK</p>";
    } else {
        echo "<p class='error'>❌ {$ext} - MISSING</p>";
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    $errors[] = "Missing PHP extensions: " . implode(', ', $missing_extensions);
}
echo "</div>";

// Step 2: Check and Create .env file
echo "<div class='step'><h2>Step 2: Environment Configuration</h2>";
if (!file_exists('.env')) {
    if (file_exists('env.example')) {
        copy('env.example', '.env');
        echo "<p class='success'>✅ .env file created from example</p>";
    } else {
        $errors[] = ".env file missing and no env.example found";
    }
} else {
    echo "<p class='success'>✅ .env file exists</p>";
}

// Update .env for MySQL
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    
    // Update database configuration
    $replacements = [
        '/DB_CONNECTION=.*/m' => 'DB_CONNECTION=mysql',
        '/DB_HOST=.*/m' => 'DB_HOST=127.0.0.1',
        '/DB_PORT=.*/m' => 'DB_PORT=3306',
        '/DB_DATABASE=.*/m' => 'DB_DATABASE=license_server',
        '/DB_USERNAME=.*/m' => 'DB_USERNAME=root',
        '/DB_PASSWORD=.*/m' => 'DB_PASSWORD='
    ];
    
    $updated = false;
    foreach ($replacements as $pattern => $replacement) {
        if (preg_match($pattern, $env_content)) {
            $env_content = preg_replace($pattern, $replacement, $env_content);
            $updated = true;
        }
    }
    
    // Add APP_KEY if missing
    if (!preg_match('/APP_KEY=base64:/', $env_content)) {
        $app_key = 'base64:' . base64_encode(random_bytes(32));
        $env_content = preg_replace('/APP_KEY=.*/', "APP_KEY={$app_key}", $env_content);
        $updated = true;
    }
    
    if ($updated) {
        file_put_contents('.env', $env_content);
        echo "<p class='success'>✅ .env updated for MySQL</p>";
    }
}
echo "</div>";

// Step 3: Database Connection Test
echo "<div class='step'><h2>Step 3: Database Connection</h2>";
try {
    $dsn = "mysql:host=127.0.0.1;port=3306;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ MySQL connection successful</p>";
    
    // Check/Create database
    $stmt = $pdo->query("SHOW DATABASES LIKE 'license_server'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("CREATE DATABASE license_server CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p class='success'>✅ Database 'license_server' created</p>";
    } else {
        echo "<p class='success'>✅ Database 'license_server' exists</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    $errors[] = "Database connection failed";
}
echo "</div>";

// Step 4: Check Composer Dependencies
echo "<div class='step'><h2>Step 4: Composer Dependencies</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "<p class='success'>✅ Composer dependencies installed</p>";
} else {
    echo "<p class='error'>❌ Composer dependencies missing</p>";
    echo "<p>Run: <code>composer install</code></p>";
    $errors[] = "Composer dependencies not installed";
}
echo "</div>";

// Step 5: File Permissions
echo "<div class='step'><h2>Step 5: File Permissions</h2>";
$writable_dirs = ['storage', 'bootstrap/cache', 'database'];
foreach ($writable_dirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "<p class='success'>✅ {$dir} - writable</p>";
    } else {
        echo "<p class='error'>❌ {$dir} - not writable</p>";
        $errors[] = "{$dir} directory not writable";
    }
}
echo "</div>";

// Step 6: Generate Quick Setup Commands
echo "<div class='step'><h2>Step 6: Setup Commands</h2>";
if (empty($errors)) {
    echo "<p class='success'>✅ System ready for setup!</p>";
    echo "<p>Run these commands in order:</p>";
    echo "<div class='code'>";
    echo "# Clear any cached config<br>";
    echo "php artisan config:clear<br><br>";
    echo "# Run database migrations<br>";
    echo "php artisan migrate<br><br>";
    echo "# Seed test data<br>";
    echo "php artisan db:seed<br><br>";
    echo "# Clear and cache config<br>";
    echo "php artisan config:cache<br>";
    echo "</div>";
} else {
    echo "<p class='error'>❌ Please fix the following issues first:</p>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li class='error'>{$error}</li>";
    }
    echo "</ul>";
}
echo "</div>";

// Step 7: Test URLs
echo "<div class='step'><h2>Step 7: Test URLs</h2>";
$base_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
echo "<p>After setup, test these URLs:</p>";
echo "<ul>";
echo "<li><a href='{$base_url}/admin/dashboard' target='_blank'>Admin Dashboard</a></li>";
echo "<li><a href='{$base_url}/api/verify-license' target='_blank'>API Endpoint</a> (POST)</li>";
echo "<li><a href='{$base_url}/test_mysql.php' target='_blank'>Database Test</a></li>";
echo "</ul>";
echo "</div>";

// Auto-run setup if no errors
if (empty($errors) && isset($_GET['auto'])) {
    echo "<div class='step'><h2>🤖 Auto Setup</h2>";
    
    // Try to run artisan commands
    $commands = [
        'config:clear' => 'Clear configuration cache',
        'migrate' => 'Run database migrations', 
        'db:seed' => 'Seed test data',
        'config:cache' => 'Cache configuration'
    ];
    
    foreach ($commands as $cmd => $desc) {
        echo "<p>Running: {$desc}...</p>";
        $output = [];
        $return_var = 0;
        
        // Try to find PHP executable
        $php_paths = [
            'php',
            'C:\\laragon\\bin\\php\\php-8.3.8-Win32-vs16-x64\\php.exe',
            'C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe'
        ];
        
        $php_exe = null;
        foreach ($php_paths as $path) {
            if (@exec("where {$path} 2>nul") || @exec("which {$path} 2>/dev/null")) {
                $php_exe = $path;
                break;
            }
        }
        
        if ($php_exe) {
            exec("{$php_exe} artisan {$cmd} 2>&1", $output, $return_var);
            if ($return_var === 0) {
                echo "<p class='success'>✅ {$desc} - completed</p>";
            } else {
                echo "<p class='error'>❌ {$desc} - failed</p>";
                echo "<pre>" . implode("\n", $output) . "</pre>";
            }
        } else {
            echo "<p class='warning'>⚠️ PHP executable not found. Run manually.</p>";
        }
    }
    echo "</div>";
}

echo "<div class='step'>";
echo "<h2>🎯 Quick Actions</h2>";
echo "<a href='?auto=1' style='background:#27ae60;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;'>🤖 Auto Setup</a> ";
echo "<a href='test_mysql.php' style='background:#3498db;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;'>🔍 Test Database</a> ";
echo "<a href='admin/dashboard' style='background:#9b59b6;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;'>🏠 Admin Panel</a>";
echo "</div>";

echo "</body></html>";
?>
 