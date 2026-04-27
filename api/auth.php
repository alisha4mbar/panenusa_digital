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

// --- LOGIKA REGISTER ---
if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Tetap gunakan pengecekan awal ini sebagai filter pertama
        $checkEmail = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            echo "<script>alert('Email sudah terdaftar!'); window.history.back();</script>";
            exit();
        }

        // Query simpan
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'user')";

        // --- TARUH KODE TRY-CATCH DI SINI ---
        try {
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location.href='login.php';</script>";
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // 1062 adalah kode MySQL untuk Duplicate Entry
                echo "<script>alert('Maaf, email ini sudah digunakan.'); window.history.back();</script>";
            } else {
                echo "Error lainnya: " . $e->getMessage();
            }
            exit();
        }
    
    }
}