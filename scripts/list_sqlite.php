<?php

// Simple script to list tables and counts from the project's SQLite DB
try {
    $path = __DIR__ . '/../database/database.sqlite';
    if (!file_exists($path)) {
        echo "SQLite file not found at: $path\n";
        exit(1);
    }

    $db = new PDO('sqlite:' . $path);
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "No tables found in SQLite database.\n";
        exit(0);
    }

    foreach ($tables as $t) {
        echo $t . PHP_EOL;
        try {
            $cstmt = $db->query('SELECT COUNT(*) as c FROM "' . $t . '"');
            $cres = $cstmt->fetch(PDO::FETCH_ASSOC);
            $count = $cres['c'] ?? 0;
        } catch (Throwable $e) {
            $count = 'n/a';
        }
        echo "  count: " . $count . PHP_EOL;
    }

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
