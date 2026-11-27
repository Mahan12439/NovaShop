<?php

require_once __DIR__ . '/config.php';

// =====================================================
// Database Configuration
// =====================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'novashop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {

    $dsn = 'mysql:host=' . DB_HOST .
        ';dbname=' . DB_NAME .
        ';charset=' . DB_CHARSET;

    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE =>
                PDO::FETCH_ASSOC,

            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {

    if (APP_ENV === 'development') {

        die(
            '<h2>Database Connection Error</h2>' .
            '<p>' . htmlspecialchars($e->getMessage()) . '</p>'
        );

    }

    die('خطایی در اتصال به پایگاه داده رخ داده است.');
}