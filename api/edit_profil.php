<?php
ob_start();

// 1. MUAT CONFIG TERLEBIH DAHULU (Menyiapkan session_start dan variabel $conn)
require_once __DIR__ . '/config.php';

// 2. PASTIKAN VARIABEL $conn BENAR-BENAR ADA (Pengaman ganda untuk Vercel)
if (!isset($conn)) {
    die("Eror: Variabel koneksi database \$conn tidak ditemukan. Pastikan config.php mendeklarasikan \$conn.");
}

// 3. PROTEKSI HALAMAN (Dilakukan setelah config.php selesai dimuat sempurna)
if (!isset($_SESSION['user_id']) && !isset($_COOKIE['panenusa_auth'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID user dari session atau fallback ke cookie
$user_id = $_SESSION['user_id'] ?? json_decode($_COOKIE['panenusa_auth'], true)['user_id'];

// 4. PROSES UPDATE DATA LAHAN / PROFIL
if (isset($_POST['update']) && isset($_POST['nama'])) {
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama']);
    $sql = "UPDATE users SET nama = '$nama_baru' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $sql)) {
        // Sinkronisasi data Session lokal
        $_SESSION['nama'] = $nama_baru;
        
        // Sinkronisasi data Cookie agar Vercel serverless tidak kehilangan state
        $authData = [
            'user_id' => $user_id,
            'nama'    => $nama_baru,
            'role'    => $_SESSION['role'] ?? 'User'
        ];
        setcookie('panenusa_auth', json_encode($authData), time() + (86400 * 30), "/", "", false, true);
        
        // Bersihkan buffer sebelum redirect demi keamanan Vercel headers
        ob_end_clean();
        header("Location: dashboard.php?status=update_success");
        exit();
    } else {
        ob_end_clean();
        header("Location: dashboard.php?status=update_failed");
        exit();
    }
}
ob_end_flush();
?>