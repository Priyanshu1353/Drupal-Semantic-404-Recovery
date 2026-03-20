<?php

/**
 * @file
 * Quick SQLite database status checker.
 *
 * Usage (from drpy root):
 *   C:\php\php.exe web\check_db.php
 */

$dbPath = __DIR__ . '/sites/default/files/.drupal.sqlite';

echo PHP_EOL . '--- Drupal SQLite DB Check ---' . PHP_EOL;
echo 'DB Path: ' . $dbPath . PHP_EOL . PHP_EOL;

if (!file_exists($dbPath)) {
    echo '[NOT FOUND] The SQLite database does not exist yet.' . PHP_EOL;
    echo 'Run install_drupal.ps1 or visit http://localhost:8080/core/install.php' . PHP_EOL;
    exit(0);
}

if (!extension_loaded('pdo_sqlite')) {
    echo '[ERROR] pdo_sqlite extension is not loaded.' . PHP_EOL;
    echo 'Enable it in C:\\php\\php.ini by uncommenting: extension=pdo_sqlite' . PHP_EOL;
    exit(1);
}

try {
    $pdo    = new PDO('sqlite:' . $dbPath);
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);

    echo '[OK] Database found. Tables (' . count($tables) . '):' . PHP_EOL;
    foreach ($tables as $t) {
        echo '  - ' . $t . PHP_EOL;
    }

    // Quick sanity: check users_field_data exists
    if (in_array('users_field_data', $tables)) {
        $user = $pdo->query("SELECT name, mail FROM users_field_data WHERE uid=1")->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            echo PHP_EOL . 'Admin user: ' . $user['name'] . ' <' . $user['mail'] . '>' . PHP_EOL;
        }
    }

    echo PHP_EOL . '--- DB OK ---' . PHP_EOL;
}
catch (PDOException $e) {
    echo '[ERROR] ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo PHP_EOL;
