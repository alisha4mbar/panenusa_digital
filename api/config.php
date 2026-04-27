<?php
// api/config.php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '3woL5zdDuZqqmHS.root';
$pass = 'IwNMm3ddVFwKzDrf';
$dbname = 'db_panenusa';

$conn = mysqli_init();
// Jika menggunakan SSL, pastikan path file .pem benar
// mysqli_ssl_set($conn, NULL, NULL, __DIR__ . '/../isrgrootx1.pem', NULL, NULL);

if (!$conn->real_connect($host, $user, $pass, $dbname, $port)) {
    die("Koneksi database gagal.");
}

// Pastikan tabel users tersedia
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
?>