<?php
// File: auth.php
session_start();
require __DIR__ . '/config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// REGISTER
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = $conn->real_escape_string($_POST['nama']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Cek email
        $cek = $conn->query("SELECT id FROM users WHERE email='$email'");
        if ($cek->num_rows > 0) {
            $_SESSION['error'] = "Email sudah terdaftar!";
            header("Location: register.php");
            exit();
        }
        
        // Tentukan role
        $count = $conn->query("SELECT COUNT(*) as total FROM users");
        $total = $count->fetch_assoc()['total'];
        $role = ($total == 0) ? 'admin' : 'user';
        
        // Simpan
        $sql = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        if ($conn->query($sql)) {
            $user_id = $conn->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['nama'] = $nama;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php");
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
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        
        $result = $conn->query("SELECT * FROM users WHERE email='$email'");
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                header("Location: /dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Password salah!";
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Email tidak terdaftar!";
            header("Location: register.php");
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