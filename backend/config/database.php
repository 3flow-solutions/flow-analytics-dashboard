<?php

$host = getenv('DB_HOST') ?: 'localhost';
$db = getenv('DB_DATABASE') ?: 'flow_analytics';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$port = getenv('DB_PORT') ?: 3306;

return [
    'driver' => getenv('DB_CONNECTION') ?: 'mysql',
    'host' => $host,
    'database' => $db,
    'username' => $user,
    'password' => $pass,
    'port' => $port,
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
