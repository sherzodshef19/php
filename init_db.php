<?php

require_once 'Database.php';

$db = Database::getInstance()->getConnection();

try {
    $db->exec("CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT,
        status TEXT DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Database and 'tasks' table initialized successfully.\n";
} catch (PDOException $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
}
