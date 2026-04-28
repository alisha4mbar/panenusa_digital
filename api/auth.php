<?php
ob_start();
session_start();

$action = isset($_GET['action']) ? $_GET['action'] : '';

// --- LOGOUT --- (dipisah sebelum koneksi DB agar selalu berhasil)
if ($action == 'logout') {
    session_unset();
    session_destroy();
    setcookie('panenusa_auth', '', time() - 3600, '/');
    header("Location: /login");
    exit();
}

require_once 'config.php';

// --- REGISTER (Ditambahkan Kembali) ---
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Pengecekan: User pertama otomatis Admin, selanjutnya User
        $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
        $row = mysqli_fetch_assoc($res);
        $role = ($row['total'] == 0) ? 'Admin' : 'User';

        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location.href='/login';</script>";
        } else {
            echo "<script>alert('Error saat registrasi!'); window.location.href='/register';</script>";
        }
    }
}

// --- LOGIN ---
if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                
                // Simpan ke Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['nama'];
                $_SESSION['role']    = $user['role'];

                // Simpan ke Cookie
                $userData = [
                    'user_id' => $user['id'],
                    'nama'    => $user['nama'],
                    'role'    => $user['role']
                ];
                setcookie('panenusa_auth', json_encode($userData), time() + (86400 * 30), "/");

                header("Location: /dashboard");
                exit();
            }
        }
        echo "<script>alert('Email atau Password salah!'); window.location.href='/login';</script>";
        exit();
    }
}
?>