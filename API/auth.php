<?php
// Mengaktifkan session sebagai cadangan (opsional, tapi disarankan)
session_start();
include 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

/**
 * 1. LOGIKA LOGOUT
 * Menghapus cookie agar user harus login ulang
 */
if ($action == 'logout') {
    // Hapus cookie 'panenusa_auth' dengan mengatur waktu ke masa lalu
    if (isset($_COOKIE['panenusa_auth'])) {
        setcookie('panenusa_auth', '', time() - 3600, '/');
    }
    
    // Hapus session jika ada
    session_unset();
    session_destroy();
    
    // Alihkan ke halaman login
    header("Location: /API/login.php");
    exit();
}

/**
 * 2. LOGIKA REGISTER
 * Menangani pendaftaran user baru
 */
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Cek apakah email sudah ada di database
        $checkEmail = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            echo "<script>alert('Email sudah terdaftar! Gunakan email lain.'); window.history.back();</script>";
            exit();
        }

        try {
            // Role default diset sebagai 'user'
            $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'user')";
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location.href='/API/login.php';</script>";
            }
        } catch (mysqli_sql_exception $e) {
            // Menangkap error Duplicate Entry (1062)
            if ($e->getCode() == 1062) {
                echo "<script>alert('Maaf, email ini sudah digunakan.'); window.history.back();</script>";
            } else {
                echo "Error: " . $e->getMessage();
            }
            exit();
        }
    }
}

/**
 * 3. LOGIKA LOGIN
 * Membuat cookie autentikasi jika login berhasil
 */
if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi Password Hash
            if (password_verify($password, $user['password'])) {
                
                // Simpan data user ke dalam array
                $userData = [
                    'user_id' => $user['id'],
                    'nama'    => $user['nama'],
                    'role'    => $user['role']
                ];

                // Simpan ke Cookie selama 30 hari (86400 detik * 30)
                setcookie('panenusa_auth', json_encode($userData), time() + (86400 * 30), "/");
                
                // Set Session juga agar sinkron dengan dashboard yang pakai session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                // Alihkan ke Dashboard
                header("Location: /API/dashboard.php");
                exit();
            } else {
                echo "<script>alert('Password salah!'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!'); window.history.back();</script>";
        }
    }
}
?>