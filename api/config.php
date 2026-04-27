<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '3woL5zdDuZqqmHS.root';
$pass = 'IwNMm3ddVFwKzDrf';
$dbname = 'db_panenusa';

$conn = mysqli_init();
// Pastikan file isrgrootx1.pem ada di folder utama (luar API)
$ssl_cert = __DIR__ . "/../isrgrootx1.pem"; 
mysqli_ssl_set($conn, NULL, NULL, $ssl_cert, NULL, NULL);

if (!$conn->real_connect($host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>