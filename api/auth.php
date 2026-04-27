<?php
session_start();
include 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// REGISTER
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Cek email duplikat
        $check = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Email sudah terdaftar! Silakan login.'); window.location.href='login.php';</script>";
            exit();
        }
        
        // Cek apakah user pertama (jadi admin)
        $countResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
        $count = mysqli_fetch_assoc($countResult)['total'];
        $role = ($count == 0) ? 'admin' : 'user';
        
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        
        if (mysqli_query($conn, $query)) {
            $user_id = mysqli_insert_id($conn);
            $_SESSION['user_id'] = $user_id;
            $_SESSION['nama'] = $nama;
            $_SESSION['role'] = $role;
            echo "<script>alert('Registrasi berhasil! Selamat datang, $nama!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Registrasi gagal: " . mysqli_error($conn) . "'); window.history.back();</script>";
        }
    }
}

// LOGIN
if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];
        
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<script>alert('Password salah!'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('Email tidak terdaftar! Silakan daftar terlebih dahulu.'); window.location.href='register.php';</script>";
        }
    }
}

// LOGOUT
if ($action == 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>