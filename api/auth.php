<?php
session_start();
require_once 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// --- REGISTER ---
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // ... kode hash password ...
        
        if ($conn->query($sql)) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['nama'] = $nama;
            $_SESSION['role'] = $role;
            header("Location: /dashboard"); // BENAR: Menggunakan route bersih
            exit();
        } else {
            $_SESSION['error'] = "Registrasi gagal.";
            header("Location: /register"); // BENAR
            exit();
        }
    }
}

// --- LOGIN ---
if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // ... kode verifikasi ...
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            header("Location: /dashboard"); // BENAR
            exit();
        } else {
            $_SESSION['error'] = "Password salah!";
            header("Location: /login"); // BENAR
            exit();
        }
    }
}
?>