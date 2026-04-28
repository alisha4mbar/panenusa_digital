<?php
ob_start();
require_once 'config.php';

if (!isset($_COOKIE['panenusa_auth'])) {
    header("Location: /login");
    exit();
}

$auth = json_decode($_COOKIE['panenusa_auth'], true);
$user_id = $auth['user_id'];

if (isset($_POST['update'])) {
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama']);
    
    // PERBAIKAN: Deklarasi Query Database
    $sql = "UPDATE users SET nama = '$nama_baru' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $sql)) {
        // PERBARUI COOKIE
        $auth['nama'] = $nama_baru;
        setcookie('panenusa_auth', json_encode($auth), time() + (86400 * 30), "/", "", true, true);
        
        // PERBARUI SESSION AGAR SINKRON
        session_start();
        $_SESSION['nama'] = $nama_baru;
        
        echo "<script>alert('Profil diperbarui!'); window.location.href='/dashboard';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil!'); window.location.href='/dashboard';</script>";
    }
}
?>