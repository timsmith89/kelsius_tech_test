<?php

session_start();

$mysqlHost = getenv('MYSQL_HOST') ?: 'localhost';
$mysqlDb = getenv('MYSQL_DATABASE') ?: 'kelsius_tech_test';
$mysqlUser = getenv('MYSQL_USER') ?: 'root';
$mysqlPass = getenv('MYSQL_PASSWORD') ?: 'root';

try {
    // Try MySQL first
    $dsn = "mysql:host=$mysqlHost;dbname=$mysqlDb;charset=utf8mb4";
    $pdo = new PDO($dsn, $mysqlUser, $mysqlPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Failed to connect to MySQL " . $e->getMessage());
}
