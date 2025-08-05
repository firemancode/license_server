<?php

// Test database connection
try {
    // Test SQLite
    $sqlite_path = __DIR__ . '/database/database.sqlite';
    $pdo = new PDO('sqlite:' . $sqlite_path);
    echo "✅ SQLite connection successful!\n";
    echo "Database file: " . $sqlite_path . "\n";
    
    // Test if tables exist
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . count($tables) . "\n";
    
    if (count($tables) > 0) {
        echo "Existing tables: " . implode(', ', $tables) . "\n";
    } else {
        echo "No tables found. Run migrations first.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    
    // Check available PDO drivers
    echo "\nAvailable PDO drivers:\n";
    foreach (PDO::getAvailableDrivers() as $driver) {
        echo "- " . $driver . "\n";
    }
}

// Check if database file exists and is writable
$db_file = __DIR__ . '/database/database.sqlite';
echo "\nDatabase file check:\n";
echo "- File exists: " . (file_exists($db_file) ? "✅ Yes" : "❌ No") . "\n";
echo "- File writable: " . (is_writable($db_file) ? "✅ Yes" : "❌ No") . "\n";
echo "- Directory writable: " . (is_writable(dirname($db_file)) ? "✅ Yes" : "❌ No") . "\n";
?>
 