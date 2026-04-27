<?php
// File: config.php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '3woL5zdDuZqqmHS.root';
$pass = 'IwNMm3ddVFwKzDrf';
$dbname = 'db_panenusa';

// Buat koneksi
$conn = new mysqli($host, $user, $pass, '', $port);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat database jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($dbname);
$conn->set_charset("utf8mb4");

// Buat tabel users
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert default admin
$result = $conn->query("SELECT id FROM users WHERE email = 'admin@panenusa.com'");
if ($result->num_rows == 0) {
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (nama, email, password, role) VALUES 
                  ('Administrator', 'admin@panenusa.com', '$adminPass', 'admin')");
}

// Insert default user
$result = $conn->query("SELECT id FROM users WHERE email = 'user@panenusa.com'");
if ($result->num_rows == 0) {
    $userPass = password_hash('user123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (nama, email, password, role) VALUES 
                  ('Petani Sample', 'user@panenusa.com', '$userPass', 'user')");
}

// Buat tabel data_lahan jika belum ada
$conn->query("CREATE TABLE IF NOT EXISTS data_lahan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lokasi VARCHAR(200) NOT NULL,
    luas DECIMAL(10,2) NOT NULL,
    status_lahan VARCHAR(50) DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Buat tabel forum_posts
$conn->query("CREATE TABLE IF NOT EXISTS forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    konten TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
?>