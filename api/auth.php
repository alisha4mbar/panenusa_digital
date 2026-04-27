<?php
session_start();
include 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// --- LOGIKA LOGOUT ---
if ($action == 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// --- LOGIKA REGISTER ---
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Cek apakah email sudah terdaftar untuk menghindari Fatal Error Duplicate Entry
        $checkEmail = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            echo "<script>alert('Email sudah terdaftar! Gunakan email lain.'); window.history.back();</script>";
            exit();
        }

        // Simpan ke database (menggunakan kolom 'nama' sesuai struktur TiDB terbaru)
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'user')";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location.href='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// --- LOGIKA LOGIN ---
if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            // PENTING: Mengambil data user dari hasil query
            $user = mysqli_fetch_assoc($result); 

            if (password_verify($password, $user['password'])) {
                // Menyimpan data ke Session agar dashboard mengenali user
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['nama']; 
                $_SESSION['role']    = $user['role']; 
                
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<script>alert('Password salah!'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('Email tidak terdaftar!'); window.location.href='login.php';</script>";
        }
    }
}
?>