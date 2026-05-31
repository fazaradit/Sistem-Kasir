<?php
$host   = getenv('DB_HOST')     ?: 'localhost';
$port   = getenv('DB_PORT')     ?: '5432';
$dbname = getenv('DB_NAME')     ?: 'kasir_db';
$user   = getenv('DB_USER')     ?: 'postgres';
$pass   = getenv('DB_PASS')     ?: '';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Jangan tampilkan detail error ke user di production
    error_log("DB Error: " . $e->getMessage());
    die(json_encode(['error' => 'Koneksi database gagal.']));
}