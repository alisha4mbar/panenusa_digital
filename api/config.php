<?php
// api/config.php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '3woL5zdDuZqqmHS.root';
$pass = 'IwNMm3ddVFwKzDrf';
$dbname = 'db_panenusa';

$conn = mysqli_init();

// Menentukan path sertifikat SSL (isrgrootx1.pem harus ada di root folder)
$ssl_cert = __DIR__ . "/../isrgrootx1.pem";

// Mengaktifkan konfigurasi SSL
mysqli_ssl_set($conn, NULL, NULL, $ssl_cert, NULL, NULL);

// Melakukan koneksi dengan flag MYSQLI_CLIENT_SSL agar transport menjadi aman
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

// Opsional: Memastikan tabel users sudah tersedia
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
?>