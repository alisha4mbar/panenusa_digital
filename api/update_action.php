<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama='$nama', password='$hash' WHERE id='$user_id'";
    } else {
        $sql = "UPDATE users SET nama='$nama' WHERE id='$user_id'";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['nama'] = $nama; // Update session nama agar langsung berubah di header
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>