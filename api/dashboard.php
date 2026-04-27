<?php
// File: dashboard.php
require_once 'config.php';
require_once 'session_handler.php';

// Inisialisasi session
initDatabaseSession($conn);

// Proteksi halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'user';
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pengguna';

$accent = ($role == 'admin') ? '#6366f1' : '#10b981';
$gradient = ($role == 'admin') ? 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)';

// Data untuk chart
$top8Data = [
    ['name' => 'Jawa Timur', 'value' => 9.5],
    ['name' => 'Jawa Tengah', 'value' => 9.2],
    ['name' => 'Jawa Barat', 'value' => 9.0],
    ['name' => 'Sulawesi Selatan', 'value' => 5.2]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900">
    <div class="flex h-screen">
        <!-- Sidebar sederhana -->
        <div class="w-64 bg-gray-800 p-5">
            <h2 class="text-white text-xl mb-5">Panenusa</h2>
            <nav>
                <a href="dashboard.php" class="block text-gray-300 py-2">Dashboard</a>
                <?php if($role == 'admin'): ?>
                    <a href="kelola_pengguna.php" class="block text-gray-300 py-2">Kelola User</a>
                    <a href="moderasi_forum.php" class="block text-gray-300 py-2">Moderasi</a>
                <?php else: ?>
                    <a href="data_lahan.php" class="block text-gray-300 py-2">Data Lahan</a>
                    <a href="forum.php" class="block text-gray-300 py-2">Forum</a>
                <?php endif; ?>
                <a href="edit_profil.php" class="block text-gray-300 py-2">Edit Profil</a>
                <a href="auth.php?action=logout" class="block text-red-400 py-2 mt-5">Logout</a>
            </nav>
        </div>
        
        <!-- Main content -->
        <div class="flex-1 p-8 overflow-auto">
            <h1 class="text-3xl font-bold text-white mb-2">Halo, <?= htmlspecialchars($nama) ?>!</h1>
            <p class="text-gray-400 mb-8">Role: <span class="text-<?= $role == 'admin' ? 'indigo' : 'green' ?>-400"><?= ucfirst($role) ?></span></p>
            
            <div class="bg-gray-800 p-6 rounded-xl">
                <h3 class="text-white text-xl mb-4">Produksi Padi Nasional (Juta Ton)</h3>
                <canvas id="chart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <script>
        new Chart(document.getElementById('chart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($top8Data, 'name')) ?>,
                datasets: [{ label: 'Produksi', data: <?= json_encode(array_column($top8Data, 'value')) ?>, backgroundColor: '<?= $accent ?>' }]
            }
        });
    </script>
</body>
</html>