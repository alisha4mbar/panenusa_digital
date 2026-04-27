<?php
session_start();
require_once 'config.php';

$action = $_GET['action'] ?? '';

// LOGIN
if ($action == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            header("Location: /dashboard"); // Sesuai vercel.json
            exit();
        }
    }
    $_SESSION['error'] = "Email atau Password salah!";
    header("Location: /login");
    exit();
}

// LOGOUT
if ($action == 'logout') {
    session_destroy();
    header("Location: /login");
    exit();
}
?>