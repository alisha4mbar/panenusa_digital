<?php
// api/config.php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '3woL5zdDuZqqmHS.root';
$pass = 'IwNMm3ddVFwKzDrf';
$dbname = 'db_panenusa';

$conn = mysqli_init();

// Path ke sertifikat (asumsi file ada di root proyek)
$ssl_cert = __DIR__ . "/../isrgrootx1.pem";

mysqli_ssl_set($conn, NULL, NULL, __DIR__ . "/isrgrootx1.pem", NULL, NULL);

// Gunakan MYSQLI_CLIENT_SSL agar TiDB menerima koneksi
$success = $conn->real_connect($host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$success) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>