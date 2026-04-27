<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com'; 
$user = '3woL5zdDuZqqmHS.root'; 
$pass = 'IwNMm3ddVFwKzDrf'; 
$db   = 'db_panenusa'; 
$port = 4000;

$conn = mysqli_init();

// SOLUSI: Gunakan __DIR__ agar Vercel mencari sertifikat di folder yang sama dengan file ini
$ssl_ca = __DIR__ . "/isrgrootx1.pem";

// Cek apakah file sertifikat benar-benar ada sebelum digunakan
if (!file_exists($ssl_ca)) {
    // Jika sertifikat hilang, coba paksa koneksi tanpa verifikasi (fail-safe)
    mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
} else {
    mysqli_ssl_set($conn, NULL, NULL, $ssl_ca, NULL, NULL);
}

// Melakukan koneksi
$connect = mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$connect) {
    die("Koneksi TiDB Gagal: " . mysqli_connect_error());
}
?>