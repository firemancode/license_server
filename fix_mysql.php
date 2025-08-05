<?php

echo "=== License Server MySQL Setup Script ===\n\n";

// Step 1: Check PHP Extensions
echo "1. Checking PHP Extensions...\n";
$required_extensions = ['pdo', 'pdo_mysql', 'mysqli'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ {$ext} - OK\n";
    } else {
        echo "   ❌ {$ext} - MISSING\n";
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    echo "\n❌ Error: Missing required PHP extensions: " . implode(', ', $missing_extensions) . "\n";
    echo "Please enable these extensions in your php.ini file:\n";
    foreach ($missing_extensions as $ext) {
        echo "   extension={$ext}\n";
    }
    echo "\nAfter enabling extensions, restart your web server.\n";
    exit(1);
}

// Step 2: Test MySQL Connection
echo "\n2. Testing MySQL Connection...\n";
$mysql_config = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'username' => 'root',
    'password' => '',
    'database' => 'license_server'
];

try {
    // Test connection without selecting database
    $dsn = "mysql:host={$mysql_config['host']};port={$mysql_config['port']};charset=utf8mb4";
    $pdo = new PDO($dsn, $mysql_config['username'], $mysql_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✅ MySQL Connection - OK\n";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '{$mysql_config['database']}'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ Database '{$mysql_config['database']}' - EXISTS\n";
    } else {
        echo "   ⚠️  Database '{$mysql_config['database']}' - NOT FOUND\n";
        echo "   Creating database...\n";
        
        $pdo->exec("CREATE DATABASE `{$mysql_config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "   ✅ Database '{$mysql_config['database']}' - CREATED\n";
    }
    
} catch (PDOException $e) {
    echo "   ❌ MySQL Connection Failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting tips:\n";
    echo "   1. Make sure MySQL is running in Laragon\n";
    echo "   2. Check if username/password is correct\n";
    echo "   3. Verify MySQL port (default: 3306)\n";
    exit(1);
}

// Step 3: Update .env file
echo "\n3. Updating .env file...\n";
$env_content = file_get_contents('.env');

// Replace PostgreSQL config with MySQL
$replacements = [
    'DB_CONNECTION=pgsql' => 'DB_CONNECTION=mysql',
    'DB_HOST=127.0.0.1' => 'DB_HOST=127.0.0.1',
    'DB_PORT=5432' => 'DB_PORT=3306',
    'DB_DATABASE=license_db' => 'DB_DATABASE=license_server',
    'DB_USERNAME=postgres' => 'DB_USERNAME=root',
    'DB_PASSWORD=yourpassword' => 'DB_PASSWORD='
];

$updated = false;
foreach ($replacements as $old => $new) {
    if (strpos($env_content, $old) !== false) {
        $env_content = str_replace($old, $new, $env_content);
        $updated = true;
        echo "   ✅ Updated: {$old} → {$new}\n";
    }
}

if ($updated) {
    file_put_contents('.env', $env_content);
    echo "   ✅ .env file updated successfully\n";
} else {
    echo "   ⚠️  No changes needed in .env file\n";
}

// Step 4: Test Laravel Database Connection
echo "\n4. Testing Laravel Database Connection...\n";
try {
    // Load Laravel environment
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        
        // Simple connection test
        $dsn = "mysql:host=127.0.0.1;port=3306;dbname=license_server;charset=utf8mb4";
        $test_pdo = new PDO($dsn, 'root', '');
        echo "   ✅ Laravel MySQL Connection - OK\n";
        
        // Test if we can run a simple query
        $stmt = $test_pdo->query("SELECT 1");
        echo "   ✅ Database Query Test - OK\n";
        
    } else {
        echo "   ⚠️  Laravel not fully installed (vendor/autoload.php missing)\n";
    }
} catch (Exception $e) {
    echo "   ❌ Laravel Database Test Failed: " . $e->getMessage() . "\n";
}

echo "\n=== Setup Complete! ===\n";
echo "Next steps:\n";
echo "1. Run migrations: php artisan migrate\n";
echo "2. Seed data: php artisan db:seed\n";
echo "3. Clear config cache: php artisan config:clear\n";
echo "4. Test your application!\n\n";

?>
 