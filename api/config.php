<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com'; 
$user = '3woL5zdDuZqqmHS.root'; // Ambil dari menu Connect
$pass = 'IwNMm3ddVFwKzDrf'; 
$db   = 'db_panenusa'; 
$port = 4000;

$conn = mysqli_init();
// Hubungkan ke file sertifikat yang kamu download tadi
mysqli_ssl_set($conn, NULL, NULL, "isrgrootx1.pem", NULL, NULL);

if (!mysqli_real_connect($conn, $host, $user, $pass, $db, $port)) {
    die("Koneksi TiDB Gagal: " . mysqli_connect_error());
}
?>