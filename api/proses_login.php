<?php
session_start();
include 'config.php';
$conn = mysqli_connect("localhost", "root", "", "panenusa_db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sesuaikan name="" dengan yang ada di form login.php
    $email = mysqli_real_escape_string($conn, $_POST['user']);
    $password = $_POST['pass'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($query);

    // Cek password (menggunakan password_verify jika di-hash saat register)
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role']; 

        // ARAHKAN KE dashboard.php (Satu file untuk Admin & User)
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Email atau Password Salah!'); window.location.href='login.php';</script>";
    }
}
?>