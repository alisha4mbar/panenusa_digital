<?php
// api/config.php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '3woL5zdDuZqqmHS.root';
$pass = 'IwNMm3ddVFwKzDrf';
$dbname = 'db_panenusa';

$conn = mysqli_init();

// Menentukan path sertifikat SSL
$ssl_cert = __DIR__ . "/../isrgrootx1.pem";

// Mengaktifkan konfigurasi SSL
mysqli_ssl_set($conn, NULL, NULL, $ssl_cert, NULL, NULL);

// Melakukan koneksi dengan flag SSL aktif
$success = $conn->real_connect($host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$success) {
    die("Koneksi aman gagal: " . mysqli_connect_error());
}
?>