<?php
// File: config.php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '3woL5zdDuZqqmHS.root';
$pass = 'IwNMm3ddVFwKzDrf';
$dbname = 'db_panenusa';

// Koneksi
$conn = new mysqli($host, $user, $pass, '', $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat database
$conn->query("CREATE DATABASE IF NOT EXISTS `$db`");
$conn->select_db($dbname);

// Buat tabel users
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert admin default
$check = $conn->query("SELECT * FROM users WHERE email='admin@panenusa.com'");
if ($check->num_rows == 0) {
    $passHash = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (nama, email, password, role) VALUES 
                  ('Administrator', 'admin@panenusa.com', '$passHash', 'admin')");
}
?>