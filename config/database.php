<?php
declare(strict_types=1);

$host = 'localhost';
$dbname = 'crm_6012';
$username = 'root';
$password = '';
$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    // Membuat akun admin awal secara otomatis jika tabel users masih kosong.
    $check = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ((int)$check === 0) {
        $stmt = $pdo->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?)");
        $stmt->execute(['Administrator', 'admin', password_hash('admin123', PASSWORD_DEFAULT)]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan database crm_6012 sudah diimpor melalui phpMyAdmin. Detail: ' . htmlspecialchars($e->getMessage()));
}
