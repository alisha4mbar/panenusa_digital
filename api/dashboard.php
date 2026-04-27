<?php
ob_start();
session_start();
require_once 'config.php';

// Proteksi: Jika tidak ada session, lempar ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['nama'] ?? 'Pengguna';
$role_user = $_SESSION['role'] ?? 'User';

// Ambil data statistik lahan
$query_lahan = $conn->query("SELECT SUM(luas) as total_luas FROM data_lahan WHERE user_id = '$user_id'");
$row_lahan = $query_lahan->fetch_assoc();
$total_luas = $row_lahan['total_luas'] ?? 0;

// Ambil data terbaru untuk tabel
$recent_data = $conn->query("SELECT * FROM data_lahan WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 text-white flex min-h-screen">
    <div class="flex-1 p-6 md:p-10 overflow-y-auto">
        <div class="max-w-5xl mx-auto">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold">Halo, <?= htmlspecialchars($nama_user) ?>!</h1>
                    <p class="text-slate-400 mt-2">Role: <span class="text-emerald-400 font-bold"><?= strtoupper($role_user) ?></span></p>
                </div>
                <a href="/auth/logout" class="bg-red-500/10 text-red-500 px-4 py-2 rounded-xl border border-red-500/20 hover:bg-red-500 hover:text-white transition-all font-semibold">
                    <i class="fas fa-power-off mr-2"></i> Keluar
                </a>
            </div>
            </div>
    </div>
</body>
</html>