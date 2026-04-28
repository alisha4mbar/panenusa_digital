<?php
session_start();
include 'config.php';

// 1. SINKRONISASI COOKIE (PENTING AGAR TIDAK ERROR SAAT PINDAH HALAMAN)
if (!isset($_SESSION['user_id']) && isset($_COOKIE['panenusa_auth'])) {
    $data = json_decode($_COOKIE['panenusa_auth'], true);
    if (isset($data['user_id'])) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];
    }
}

// 2. PROTEKSI (Gunakan /login bukan login.php)
if(!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// 3. QUERY (Gunakan try-catch agar tidak langsung mati kalau tabel belum ada)
try {
    $query = "SELECT * FROM data_lahan WHERE user_id = '$user_id' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
} catch (Exception $e) {
    $result = false;
}
?>