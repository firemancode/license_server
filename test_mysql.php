<?php

// Test MySQL Connection
echo "<!DOCTYPE html><html><head><title>MySQL Test</title></head><body>";
echo "<h1>MySQL Connection Test</h1>";

// Check PHP extensions
echo "<h2>1. PHP Extensions Check</h2>";
$extensions = ['pdo', 'pdo_mysql', 'mysqli'];
foreach ($extensions as $ext) {
    $status = extension_loaded($ext) ? "✅ OK" : "❌ MISSING";
    echo "<p>{$ext}: {$status}</p>";
}

// Test MySQL connection
echo "<h2>2. MySQL Connection Test</h2>";
try {
    $dsn = "mysql:host=127.0.0.1;port=3306;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ MySQL Connection: OK</p>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'license_server'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Database 'license_server': EXISTS</p>";
    } else {
        echo "<p>⚠️ Database 'license_server': NOT FOUND</p>";
        try {
            $pdo->exec("CREATE DATABASE license_server CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "<p>✅ Database 'license_server': CREATED</p>";
        } catch (Exception $e) {
            echo "<p>❌ Cannot create database: " . $e->getMessage() . "</p>";
        }
    }
    
    // Test connection to specific database
    $dsn2 = "mysql:host=127.0.0.1;port=3306;dbname=license_server;charset=utf8mb4";
    $pdo2 = new PDO($dsn2, 'root', '');
    echo "<p>✅ Database Connection: OK</p>";
    
} catch (PDOException $e) {
    echo "<p>❌ MySQL Error: " . $e->getMessage() . "</p>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure Laragon MySQL service is running</li>";
    echo "<li>Check if username/password is correct (default: root with no password)</li>";
    echo "<li>Verify MySQL port (default: 3306)</li>";
    echo "</ul>";
}

// Show current .env database config
echo "<h2>3. Current .env Configuration</h2>";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    $db_lines = [];
    foreach (explode("\n", $env_content) as $line) {
        if (strpos($line, 'DB_') === 0) {
            $db_lines[] = htmlspecialchars($line);
        }
    }
    echo "<pre>" . implode("\n", $db_lines) . "</pre>";
} else {
    echo "<p>❌ .env file not found!</p>";
}

echo "<h2>4. Next Steps</h2>";
echo "<ol>";
echo "<li>Update your .env file with MySQL configuration</li>";
echo "<li>Run: <code>php artisan config:clear</code></li>";
echo "<li>Run: <code>php artisan migrate</code></li>";
echo "<li>Run: <code>php artisan db:seed</code></li>";
echo "</ol>";

echo "</body></html>";
?>
 