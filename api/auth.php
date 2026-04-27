<?php
// api/auth.php
require_once 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Fungsi untuk menyimpan data user ke dalam Cookie selama 30 hari
function buatAuthCookie($user) {
    $data = json_encode([
        'id' => $user['id'],
        'nama' => $user['nama'],
        'role' => $user['role']
    ]);
    setcookie('panenusa_auth', $data, time() + (86400 * 30), "/", "", true, true);
}

// LOGIKA LOGIN
if ($action == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            buatAuthCookie($user);
            header("Location: /dashboard"); // Redirect ke route bersih
            exit();
        }
    }
    header("Location: /login?error=1");
    exit();
}

// LOGIKA REGISTER
if ($action == 'register' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'user')";
    if ($conn->query($sql)) {
        $userId = $conn->insert_id;
        buatAuthCookie(['id' => $userId, 'nama' => $nama, 'role' => 'user']);
        header("Location: /dashboard");
        exit();
    }
}

// LOGIKA LOGOUT
if ($action == 'logout') {
    setcookie('panenusa_auth', '', time() - 3600, "/");
    header("Location: /login");
    exit();
}