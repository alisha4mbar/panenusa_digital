<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com'; 
$user = '3woL5zdDuZqqmHS.root'; 
$pass = 'IwNMm3ddVFwKzDrf'; 
$db   = 'db_panenusa'; 
$port = 4000;

$conn = mysqli_init();

// Konfigurasi SSL
$ssl_ca = __DIR__ . "/isrgrootx1.pem";

if (file_exists($ssl_ca)) {
    mysqli_ssl_set($conn, NULL, NULL, $ssl_ca, NULL, NULL);
    mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
} else {
    mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
}

mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 30);

// Koneksi ke database (tanpa pilih database dulu)
$connect = mysqli_real_connect($conn, $host, $user, $pass, '', $port, NULL, MYSQLI_CLIENT_SSL);

if (!$connect) {
    die("Koneksi TiDB Gagal: " . mysqli_connect_error());
}

// Buat database jika belum ada
if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    die("Gagal membuat database: " . mysqli_error($conn));
}

// Pilih database
if (!mysqli_select_db($conn, $db)) {
    die("Gagal memilih database: " . mysqli_error($conn));
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// ========== TABEL users ==========
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $sql_users)) {
    die("Gagal membuat tabel users: " . mysqli_error($conn));
}

// ========== TABEL user_sessions ==========
$sql_sessions = "CREATE TABLE IF NOT EXISTS user_sessions (
    session_id VARCHAR(128) NOT NULL PRIMARY KEY,
    user_id INT DEFAULT NULL,
    user_nama VARCHAR(100) NOT NULL,
    user_role VARCHAR(20) NOT NULL,
    session_data TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_last_activity (last_activity),
    INDEX idx_user_id (user_id)
)";

if (!mysqli_query($conn, $sql_sessions)) {
    die("Gagal membuat tabel user_sessions: " . mysqli_error($conn));
}

// ========== TABEL data_lahan ==========
$sql_lahan = "CREATE TABLE IF NOT EXISTS data_lahan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lokasi VARCHAR(200) NOT NULL,
    luas DECIMAL(10,2) NOT NULL,
    status_lahan VARCHAR(50) DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id)
)";

if (!mysqli_query($conn, $sql_lahan)) {
    die("Gagal membuat tabel data_lahan: " . mysqli_error($conn));
}

// ========== TABEL forum_posts ==========
$sql_forum = "CREATE TABLE IF NOT EXISTS forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    konten TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
)";

if (!mysqli_query($conn, $sql_forum)) {
    die("Gagal membuat tabel forum_posts: " . mysqli_error($conn));
}

// ========== INSERT DATA DEFAULT ==========

// Insert default admin (jika belum ada)
$checkAdmin = mysqli_query($conn, "SELECT id FROM users WHERE email = 'admin@panenusa.com'");
if (!$checkAdmin || mysqli_num_rows($checkAdmin) == 0) {
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $sql_insert_admin = "INSERT INTO users (nama, email, password, role) VALUES 
                         ('Administrator', 'admin@panenusa.com', '$adminPassword', 'admin')";
    mysqli_query($conn, $sql_insert_admin);
}

// Insert default user (jika belum ada)
$checkUser = mysqli_query($conn, "SELECT id FROM users WHERE email = 'user@panenusa.com'");
if (!$checkUser || mysqli_num_rows($checkUser) == 0) {
    $userPassword = password_hash('user123', PASSWORD_DEFAULT);
    $sql_insert_user = "INSERT INTO users (nama, email, password, role) VALUES 
                        ('Petani Sample', 'user@panenusa.com', '$userPassword', 'user')";
    mysqli_query($conn, $sql_insert_user);
}

// Insert sample data lahan (jika belum ada)
$checkLahan = mysqli_query($conn, "SELECT id FROM data_lahan LIMIT 1");
if (!$checkLahan || mysqli_num_rows($checkLahan) == 0) {
    $getUser = mysqli_query($conn, "SELECT id FROM users WHERE role = 'user' LIMIT 1");
    if ($getUser && $userData = mysqli_fetch_assoc($getUser)) {
        $userId = $userData['id'];
        $sql_insert_lahan = "INSERT INTO data_lahan (user_id, lokasi, luas, status_lahan) VALUES 
                            ('$userId', 'Desa Sukamakmur, Jawa Barat', 2.5, 'Aktif'),
                            ('$userId', 'Kecamatan Tani Maju, Jawa Timur', 3.0, 'Aktif')";
        mysqli_query($conn, $sql_insert_lahan);
    }
}

// Insert sample forum posts (jika belum ada)
$checkForum = mysqli_query($conn, "SELECT id FROM forum_posts LIMIT 1");
if (!$checkForum || mysqli_num_rows($checkForum) == 0) {
    $getUser = mysqli_query($conn, "SELECT id FROM users WHERE role = 'user' LIMIT 1");
    if ($getUser && $userData = mysqli_fetch_assoc($getUser)) {
        $userId = $userData['id'];
        $sql_insert_forum = "INSERT INTO forum_posts (user_id, konten, status) VALUES 
                            ('$userId', 'Bagaimana cara meningkatkan hasil panen padi di musim kemarau?', 'Approved'),
                            ('$userId', 'Rekomendasi pupuk organik terbaik untuk sawah?', 'Approved')";
        mysqli_query($conn, $sql_insert_forum);
    }
}

// Cek apakah koneksi berhasil
echo "<script>console.log('Database connected successfully!');</script>";
?>