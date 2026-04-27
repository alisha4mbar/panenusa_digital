<?php
session_start();
include 'config.php';
$conn = mysqli_connect("localhost", "root", "", "panenusa_db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    // 1. Cek jumlah user
    $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
    $row = mysqli_fetch_assoc($res);
    
    // 2. Tentukan Role
    $role = ($row['total'] == 0) ? 'admin' : 'petani';

    // 3. Simpan
    $query = "INSERT INTO users (nama_lengkap, email, password, role) VALUES ('$nama', '$email', '$pass', '$role')";
    
    if (mysqli_query($conn, $query)) {
        // AMBIL ID USER YANG BARU SAJA MASUK
        $user_id = mysqli_insert_id($conn);

        // 4. OTOMATIS LOGIN-KAN (Set Session)
        $_SESSION['user_id'] = $user_id;
        $_SESSION['nama'] = $nama;
        $_SESSION['role'] = $role;

        // 5. ARAHKAN KE dashboard.php (Bukan dashboard_user.php!)
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>