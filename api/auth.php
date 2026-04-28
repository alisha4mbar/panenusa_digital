<?php
ob_start();
session_start();
require_once 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// LOGOUT
if ($action == 'logout') {
    session_unset();
    session_destroy();
    header("Location: /login");
    exit();
}
// --- CUPLIKAN BAGIAN LOGIN DI auth.php ---
if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
    // Di dalam proses login (setelah password_verify berhasil)
       // 1. Pastikan data user sudah diambil dari database ($user)
// 2. Definisikan array data yang ingin disimpan
        $userData = [
    'user_id' => $user['id'],
    'nama'    => $user['nama'],
    'role'    => $user['role']
];

// 3. Simpan ke Session (Hanya 1x)
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['role']    = $user['role'];

// 4. Simpan ke Cookie (Hanya 1x - Cukup panggil di sini saja)
// Gunakan json_encode untuk mengubah array menjadi teks agar bisa disimpan di cookie
setcookie('panenusa_auth', json_encode($userData), time() + (86400 * 30), "/");

// 5. Alihkan ke dashboard
header("Location: /dashboard");
exit();
            }
        }
        echo "<script>alert('Login Gagal!'); window.location.href='/login';</script>";
    }
}

// LOGIN
if ($action == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: /dashboard");
            exit();
        }
    }
    echo "<script>alert('Email atau Password salah!'); window.location.href='/login';</script>";
    exit();
}