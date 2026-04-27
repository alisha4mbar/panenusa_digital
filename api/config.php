<?php
// File: config.php
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

// Koneksi ke TiDB
if (!mysqli_real_connect($conn, $host, $user, $pass, '', $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Koneksi TiDB Gagal: " . mysqli_connect_error());
}

// Buat database jika belum ada
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_select_db($conn, $db);

// Buat semua tabel yang diperlukan
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS user_sessions (
        session_id VARCHAR(128) NOT NULL PRIMARY KEY,
        user_id INT DEFAULT NULL,
        user_nama VARCHAR(100) NOT NULL DEFAULT '',
        user_role VARCHAR(20) NOT NULL DEFAULT 'user',
        session_data TEXT,
        ip_address VARCHAR(45),
        user_agent TEXT,
        last_activity INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_last_activity (last_activity),
        INDEX idx_user_id (user_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS data_lahan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        lokasi VARCHAR(200) NOT NULL,
        luas DECIMAL(10,2) NOT NULL,
        status_lahan VARCHAR(50) DEFAULT 'Aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS forum_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        konten TEXT NOT NULL,
        status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $sql) {
    if (!mysqli_query($conn, $sql)) {
        error_log("Error creating table: " . mysqli_error($conn));
    }
}

// Insert default admin
$check = mysqli_query($conn, "SELECT id FROM users WHERE email = 'admin@panenusa.com'");
if (!$check || mysqli_num_rows($check) == 0) {
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES 
                        ('Administrator', 'admin@panenusa.com', '$adminPass', 'admin')");
}

// Insert default user
$check = mysqli_query($conn, "SELECT id FROM users WHERE email = 'user@panenusa.com'");
if (!$check || mysqli_num_rows($check) == 0) {
    $userPass = password_hash('user123', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES 
                        ('Petani Sample', 'user@panenusa.com', '$userPass', 'user')");
}

// Jangan mulai session di sini! Session akan dimulai di file masing-masing
?>