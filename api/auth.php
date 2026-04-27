<?php
// api/auth.php
require_once 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Fungsi Helper untuk mempermudah pembuatan Cookie
function createAuthCookie($user) {
    $data = json_encode([
        'user_id' => $user['id'],
        'nama'    => $user['nama'],
        'role'    => $user['role']
    ]);
    // Berlaku 30 hari, aman (HttpOnly & Secure)
    setcookie('panenusa_auth', $data, time() + (86400 * 30), "/", "", true, true);
}

if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = $conn->real_escape_string($_POST['nama']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $count = $conn->query("SELECT COUNT(*) as total FROM users");
        $role = ($count->fetch_assoc()['total'] == 0) ? 'admin' : 'user';
        
        $sql = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        if ($conn->query($sql)) {
            $user_id = $conn->insert_id;
            createAuthCookie(['id' => $user_id, 'nama' => $nama, 'role' => $role]);
            header("Location: /dashboard");
            exit();
        }
    }
}

if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        
        $res = $conn->query("SELECT * FROM users WHERE email='$email'");
        if ($user = $res->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                createAuthCookie($user);
                header("Location: /dashboard");
                exit();
            }
        }
        header("Location: /login?error=1");
        exit();
    }
}

if ($action == 'logout') {
    // Hapus cookie dengan mengatur waktu kadaluarsa ke masa lalu
    setcookie('panenusa_auth', '', time() - 3600, "/");
    header("Location: /login");
    exit();
}