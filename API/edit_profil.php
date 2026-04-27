<?php
// api/edit_profil.php
require_once 'config.php';

if (!isset($_COOKIE['panenusa_auth'])) {
    header("Location: /login");
    exit();
}

$auth = json_decode($_COOKIE['panenusa_auth'], true);
$user_id = $auth['user_id'];

if (isset($_POST['update'])) {
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama']);
    // ... logika update database ...
    
    if (mysqli_query($conn, $sql)) {
        // PERBARUI COOKIE
        $auth['nama'] = $nama_baru;
        setcookie('panenusa_auth', json_encode($auth), time() + (86400 * 30), "/", "", true, true);
        
        echo "<script>alert('Profil diperbarui!'); window.location.href='/dashboard';</script>";
    }
}
?>