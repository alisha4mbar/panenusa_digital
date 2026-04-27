<?php
// API/config.php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '3woL5zdDuZqqmHS.root';
$pass = 'IwNMm3ddVFwKzDrf';
$dbname = 'db_panenusa';

$conn = mysqli_init();

// Perbaikan Path: Menggunakan __DIR__ untuk memastikan lokasi file .pem benar di Vercel
$ssl_cert = __DIR__ . "/../isrgrootx1.pem";

// Pastikan sertifikat ada sebelum mencoba koneksi
if (!file_exists($ssl_cert)) {
    die("Sertifikat SSL tidak ditemukan di: " . $ssl_cert);
}

mysqli_ssl_set($conn, NULL, NULL, $ssl_cert, NULL, NULL);

// Menambahkan flag MYSQLI_CLIENT_SSL secara eksplisit
if (!$conn->real_connect($host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>