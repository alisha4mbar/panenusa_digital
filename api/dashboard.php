<?php
session_start();

// Proteksi Halaman: Wajib Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pengguna';

// Penyesuaian Warna: Admin (Indigo/Biru), User (Emerald/Hijau)
$accent = (strtolower($role) == 'admin') ? '#6366f1' : '#10b981'; 
$gradient = (strtolower($role) == 'admin') ? 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)';

include 'config.php';
$top8Data = [['name' => 'Jawa Timur', 'value' => 9.5], ['name' => 'Jawa Tengah', 'value' => 9.2], ['name' => 'Jawa Barat', 'value' => 9.0]];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard <?= ucfirst($role) ?> | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #080b14; color: #e2e8f0; font-family: sans-serif; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="flex h-screen">
    <aside class="w-72 card-glass p-6 flex flex-col">
        <div class="mb-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: <?= $gradient ?>">
                <i class="fas fa-leaf"></i>
            </div>
            <span class="text-xl font-bold">Panenusa<span style="color: <?= $accent ?>">.pro</span></span>
        </div>
        <nav class="flex-1">
            <div class="p-3 rounded-lg text-white font-bold" style="background: rgba(255,255,255,0.05); border-left: 4px solid <?= $accent ?>">
                <i class="fas fa-chart-pie mr-3"></i> Dashboard
            </div>
            <?php if (strtolower($role) == 'admin'): ?>
                <div class="mt-4 p-3 text-slate-400 hover:text-white transition cursor-pointer">
                    <i class="fas fa-users-cog mr-3"></i> Kelola User
                </div>
            <?php endif; ?>
            <a href="auth.php?action=logout" class="block mt-10 p-3 text-red-400 hover:bg-red-500/10 rounded-lg">
                <i class="fas fa-power-off mr-3"></i> Keluar
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-12 overflow-y-auto">
        <header class="mb-12">
            <h1 class="text-3xl font-bold text-white">Selamat Datang, <?= htmlspecialchars($nama) ?>!</h1>
            <p class="text-slate-500">Status Akun: <span style="color: <?= $accent ?>"><?= ucfirst($role) ?></span></p>
        </header>
        <div class="card-glass p-8 rounded-3xl">
            <h3 class="text-white font-bold mb-6">Statistik Produksi Nasional</h3>
            <canvas id="bpsChart" height="100"></canvas>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('bpsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($top8Data, 'name')) ?>,
                datasets: [{
                    label: 'Ton',
                    data: <?= json_encode(array_column($top8Data, 'value')) ?>,
                    backgroundColor: '<?= $accent ?>',
                    borderRadius: 8
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    </script>
</body>
</html>