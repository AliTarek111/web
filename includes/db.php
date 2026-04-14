<?php
// includes/db.php
// Ahmed Koshary Store - Smart Database Connection with Fallback
// If DB fails → session is marked so admin can login with hardcoded credentials.

$pdo = null;
$db_connection_failed = false;

// Load config from file
$config_path = __DIR__ . '/config.php';
if (file_exists($config_path)) {
    $cfg = require $config_path;
} else {
    // Default fallback values
    $cfg = [
        'db_host'    => 'localhost',
        'db_name'    => 'ahmed_koshary_store',
        'db_user'    => 'root',
        'db_pass'    => '',
        'db_charset' => 'utf8mb4',
    ];
}

$dsn = "mysql:host={$cfg['db_host']};dbname={$cfg['db_name']};charset={$cfg['db_charset']}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 5,
];

try {
    $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], $options);
} catch (\PDOException $e) {
    $db_connection_failed = true;
    // Log error silently — do NOT expose to frontend
    error_log('[AhmedKoshary DB Error] ' . $e->getMessage());
}
?>
