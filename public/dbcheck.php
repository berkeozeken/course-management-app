<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');
$ssl  = getenv('DB_SSLMODE') ?: 'disable';

echo "TRY pgsql:host=$host;port=$port;dbname=$db sslmode=$ssl\n<br>";

try {
  $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  $one = $pdo->query('select 1')->fetchColumn();
  echo "DB OK ($one)\n";
} catch (Throwable $e) {
  echo "DB ERROR: ".$e->getMessage()."\n";
}

