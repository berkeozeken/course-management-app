<?php
header('Content-Type: text/plain; charset=UTF-8');
require __DIR__ . '/../vendor/autoload.php';

try {
    $app = require __DIR__ . '/../bootstrap/app.php';
    echo "BOOT OK\n";
} catch (Throwable $e) {
    echo "BOOT ERROR: ".$e->getMessage()."\n\n".$e->getTraceAsString()."\n";
    exit;
}

// İsteğe bağlı: DB testi (env DB_* ile bağlanmayı dener)
$host = getenv('DB_HOST'); $db = getenv('DB_DATABASE'); $user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD'); $port = getenv('DB_PORT') ?: '5432'; $ssl = getenv('DB_SSLMODE') ?: 'require';
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db;sslmode=$ssl", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $pdo->query('select 1'); echo "DB OK\n";
} catch (Throwable $e) {
    echo "DB ERROR: ".$e->getMessage()."\n";
}
