<?php
// api/config.php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '3woL5zdDuZqqmHS.root';
$pass = 'IwNMm3ddVFwKzDrf';
$dbname = 'db_panenusa';

// 1. Inisialisasi mysqli
$conn = mysqli_init();

// 2. Konfigurasi SSL
// Pastikan file isrgrootx1.pem ada di folder utama proyek (root)
// Karena file ini di folder api, kita naik satu tingkat (..) untuk menemukan sertifikat
$ssl_cert = __DIR__ . "/../isrgrootx1.pem";

mysqli_ssl_set($conn, NULL, NULL, $ssl_cert, NULL, NULL);

// 3. Lakukan koneksi dengan flag MYSQLI_CLIENT_SSL
$success = $conn->real_connect(
    $host, 
    $user, 
    $pass, 
    $dbname, 
    $port, 
    NULL, 
    MYSQLI_CLIENT_SSL
);

if (!$success) {
    die("Koneksi aman gagal: " . mysqli_connect_error());
}

// Opsional: Pastikan tabel users sudah ada
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
?>