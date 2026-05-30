<?php
/**
 * Panenusa — config.php
 * Session management + Database Connection + Role Guard (Mendukung SSL TiDB)
 */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// Mengambil data dari Environment Variables Vercel
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'panenusa';

// Inisialisasi MySQLi object untuk mendukung parameter SSL
$conn = mysqli_init();

if (!$conn) {
    die("Inisialisasi MySQLi gagal");
}

// JIKA BUKAN LOCALHOST (DI VERCEL), AKTIFKAN PENGATURAN SSL WAJIB TIDB
if ($db_host !== 'localhost' && $db_host !== '127.0.0.1') {
    // Bendera ini memaksa mysqli menggunakan koneksi SSL terenkripsi yang diwajibkan TiDB Cloud
    mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
    
    // Melakukan koneksi menggunakan flag SSL pada port default TiDB (4000)
    $connected = mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, 4000, null, MYSQLI_CLIENT_SSL);
} else {
    // Fallback koneksi untuk XAMPP lokal komputer kamu (tanpa SSL)
    $connected = mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name);
}

if (!$connected) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// SINKRONISASI COOKIE → SESSION (Wajib untuk Vercel Serverless State)
if (empty($_SESSION['user_id']) && isset($_COOKIE['panenusa_auth'])) {
    $data = json_decode($_COOKIE['panenusa_auth'], true);
    if (!empty($data['user_id'])) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama']    = $data['nama'];
        $_SESSION['role']    = $data['role'];
        $_SESSION['divisi']  = $data['divisi'] ?? '';
        $_SESSION['email']   = $data['email']  ?? '';
    }
}

// FUNGSI PROTEKSI HALAMAN (Membaca data array dengan aman)
function requireLogin(?string $role = null): array {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php?msg=expired');
        exit;
    }
    if ($role !== null && strtolower($_SESSION['role'] ?? '') !== strtolower($role)) {
        redirectToDashboard();
    }
    return [
        'id'     => (int)$_SESSION['user_id'],
        'nama'   => $_SESSION['nama']   ?? '',
        'email'  => $_SESSION['email']  ?? '',
        'role'   => $_SESSION['role']   ?? 'user',
        'divisi' => $_SESSION['divisi'] ?? '',
    ];
}

function redirectToDashboard(): never {
    header('Location: dashboard.php');
    exit;
}
?>