<?php
// api/auth.php
require_once 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Fungsi Helper untuk set cookie
function setAuthCookie($user) {
    // Simpan data dalam JSON dan di-encode (Idealnya di-enkripsi)
    $userData = json_encode([
        'id' => $user['id'],
        'nama' => $user['nama'],
        'role' => $user['role']
    ]);
    // Cookie berlaku selama 30 hari
    setcookie('panenusa_auth', $userData, time() + (86400 * 30), "/", "", true, true);
}

// --- REGISTER ---
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = $conn->real_escape_string($_POST['nama']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'user')";
        
        if ($conn->query($sql)) {
            $userId = $conn->insert_id;
            setAuthCookie(['id' => $userId, 'nama' => $nama, 'role' => 'user']);
            header("Location: /dashboard");
            exit();
        }
    }
}

// --- LOGIN ---
if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        
        $result = $conn->query("SELECT * FROM users WHERE email='$email'");
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                setAuthCookie($user);
                header("Location: /dashboard");
                exit();
            }
        }
        header("Location: /login?error=1");
        exit();
    }
}

// --- LOGOUT ---
if ($action == 'logout') {
    // Hapus cookie dengan set waktu ke masa lalu
    setcookie('panenusa_auth', '', time() - 3600, "/");
    header("Location: /login");
    exit();
}