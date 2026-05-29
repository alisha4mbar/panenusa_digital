<?php
// Pastikan tidak ada spasi atau baris kosong sebelum tag php ini
ob_start();

// PERBAIKAN UTAMA: Gunakan __DIR__ agar Vercel mendeteksi config.php di folder yang sama
require_once __DIR__ . '/config.php';

// PENGAMAN GANDA: Jika $conn tidak terbaca dari config.php, hentikan proses dengan pesan jelas
if (!isset($conn)) {
    die("Eror: Variabel koneksi database \$conn tidak ditemukan. Periksa kembali file api/config.php Anda.");
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// --- LOGOUT ---
if ($action == 'logout') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    
    // Hapus cookie dengan parameter yang valid
    setcookie('panenusa_auth', '', time() - 3600, '/');
    
    header("Location: login.php");
    exit();
}

// --- REGISTER ---
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
        $row = mysqli_fetch_assoc($res);
        $role = ($row['total'] == 0) ? 'Admin' : 'User';

        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";

        if (mysqli_query($conn, $query)) {
            header("Location: login.php?status=reg_success");
            exit();
        } else {
            header("Location: register.php?status=reg_failed");
            exit();
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
                
                // 1. Simpan ke Session (Sifatnya temporer di Vercel)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['nama'];
                $_SESSION['role']    = $user['role'];

                // 2. Simpan ke Cookie (Penyelamat utama state login di Vercel)
                $userData = [
                    'user_id' => $user['id'],
                    'nama'    => $user['nama'],
                    'role'    => $user['role']
                ];
                
                setcookie('panenusa_auth', json_encode($userData), time() + (86400 * 30), "/", "", false, true);

                // Pastikan buffer dibersihkan sebelum redirect
                ob_end_clean();
                header("Location: dashboard.php");
                exit();
            }
        }
        
        header("Location: login.php?status=login_failed");
        exit();
    }
}
ob_end_flush();
?>