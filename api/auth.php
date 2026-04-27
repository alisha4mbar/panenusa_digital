<?php
// File: auth.php
session_start();
require_once 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// REGISTER
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = trim($conn->real_escape_string($_POST['nama']));
        $email = trim($conn->real_escape_string($_POST['email']));
        $password = $_POST['password'];
        
        if (empty($nama) || empty($email) || empty($password)) {
            $_SESSION['error'] = "Semua field harus diisi!";
            header("Location: register.php");
            exit();
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error'] = "Password minimal 6 karakter!";
            header("Location: register.php");
            exit();
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Cek email
        $check = $conn->query("SELECT email FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $_SESSION['error'] = "Email sudah terdaftar!";
            header("Location: register.php");
            exit();
        }
        
        // Tentukan role
        $countResult = $conn->query("SELECT COUNT(*) as total FROM users");
        $count = $countResult->fetch_assoc()['total'];
        $role = ($count == 0) ? 'admin' : 'user';
        
        // Simpan
        if ($conn->query("INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashedPassword', '$role')")) {
            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = "Registrasi gagal: " . $conn->error;
            header("Location: register.php");
            exit();
        }
    }
}

// LOGIN
if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = trim($conn->real_escape_string($_POST['email']));
        $password = $_POST['password'];
        
        $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Password salah!";
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Email tidak terdaftar!";
            header("Location: login.php");
            exit();
        }
    }
}

// LOGOUT
if ($action == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>