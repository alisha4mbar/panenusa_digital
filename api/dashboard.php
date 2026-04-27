<?php
// File: dashboard.php
session_start();
require __DIR__ . '/config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
$accent = ($role == 'admin') ? '#6366f1' : '#10b981';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 p-5">
            <h2 class="text-white text-xl mb-5">Panenusa</h2>
            <nav>
                <a href="dashboard.php" class="block text-white bg-<?= $accent ?>-600 p-2 rounded mb-2">Dashboard</a>
                <?php if($role == 'admin'): ?>
                    <a href="kelola_pengguna.php" class="block text-gray-300 p-2 rounded mb-2">Kelola User</a>
                <?php else: ?>
                    <a href="data_lahan.php" class="block text-gray-300 p-2 rounded mb-2">Data Lahan</a>
                    <a href="forum.php" class="block text-gray-300 p-2 rounded mb-2">Forum</a>
                <?php endif; ?>
                <a href="edit_profil.php" class="block text-gray-300 p-2 rounded mb-2">Edit Profil</a>
                <a href="auth.php?action=logout" class="block text-red-400 p-2 rounded">Logout</a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold text-white mb-5">Halo, <?= htmlspecialchars($nama) ?>!</h1>
            <p class="text-gray-400 mb-8">Anda login sebagai <span class="text-<?= $accent ?>-400"><?= $role ?></span></p>
            
            <div class="bg-gray-800 p-8 rounded-lg">
                <i class="fas fa-check-circle text-5xl text-green-500 mb-4 block"></i>
                <h3 class="text-xl font-bold text-white">Login Berhasil!</h3>
                <p class="text-gray-400">Selamat datang di dashboard Panenusa.</p>
            </div>
        </div>
    </div>
</body>
</html>