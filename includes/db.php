<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'encolzgh_nexussynced_db');
define('DB_USER', 'encolzgh_nexus_user');
define('DB_PASS', 'nexus_user');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("DB connection failed.");
}
