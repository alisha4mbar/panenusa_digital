<?php
/**
 * Panenusa — config.php
 * Session management + Database Connection + Role Guard
 */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// Tambahkan Koneksi Database di sini agar merata ke semua file
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'panenusa';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Sinkronisasi cookie → session (Wajib untuk Vercel serverless)
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

function requireLogin(?string $role = null): array {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php?msg=expired');
        exit;
    }
    if ($role !== null && ($_SESSION['role'] ?? '') !== $role) {
        redirectToDashboard();
    }
    return [
        'id'     => (int)$_SESSION['user_id'],
        'nama'   => $_SESSION['nama']   ?? '',
        'email'  => $_SESSION['email']  ?? '',
        'role'   => $_SESSION['role']   ?? 'User',
        'divisi' => $_SESSION['divisi'] ?? '',
    ];
}

function redirectToDashboard(): never {
    $role = $_SESSION['role'] ?? 'User';
    if ($role === 'Admin') {
        header('Location: dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}
?>