<?php
// api/auth.php
session_start();
require_once 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// --- LOGIKA REGISTER ---
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = $conn->real_escape_string($_POST['nama']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        
        // Hash password untuk keamanan
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // Cek apakah email sudah terdaftar
        $cek = $conn->query("SELECT id FROM users WHERE email='$email'");
        if ($cek->num_rows > 0) {
            $_SESSION['error'] = "Email sudah terdaftar!";
            header("Location: /register"); // Menggunakan route bersih sesuai vercel.json
            exit();
        }
        
        // Tentukan role: Admin jika user pertama, sisanya user
        $count = $conn->query("SELECT COUNT(*) as total FROM users");
        $total = $count->fetch_assoc()['total'];
        $role = ($total == 0) ? 'admin' : 'user';
        
        // Simpan data user baru
        $sql = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password_hashed', '$role')";
        
        if ($conn->query($sql)) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['nama'] = $nama;
            $_SESSION['role'] = $role;
            
            header("Location: /dashboard"); // Redirect ke route dashboard bersih
            exit();
        } else {
            $_SESSION['error'] = "Registrasi gagal, silakan coba lagi.";
            header("Location: /register");
            exit();
        }
    }
}

// --- LOGIKA LOGIN ---
if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        
        $result = $conn->query("SELECT * FROM users WHERE email='$email'");
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password hash
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: /dashboard"); // Redirect ke route dashboard bersih
                exit();
            } else {
                $_SESSION['error'] = "Password salah!";
                header("Location: /login"); // Kembali ke route login bersih
                exit();
            }
        } else {
            $_SESSION['error'] = "Email tidak terdaftar!";
            header("Location: /login");
            exit();
        }
    }
}

// --- LOGIKA LOGOUT ---
if ($action == 'logout') {
    session_destroy();
    header("Location: /login"); // Redirect ke route login bersih
    exit();
}

// Jika akses langsung tanpa action yang valid
header("Location: /login");
exit();
?>