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

// REGISTER
if ($action == 'register' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Cek Email
    $check = $conn->query("SELECT email FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar!'); window.history.back();</script>";
        exit();
    }

    try {
        $sql = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'user')";
        if ($conn->query($sql)) {
            echo "<script>alert('Registrasi Berhasil!'); window.location.href='/login';</script>";
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('Terjadi kesalahan database.'); window.history.back();</script>";
        exit();
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