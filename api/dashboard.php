<?php
session_start();

// Proteksi Halaman: Wajib Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data dari session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pengguna';

// Penyesuaian Tema Berdasarkan Role
$accent = (strtolower($role) == 'admin') ? '#6366f1' : '#10b981'; 
$gradient = (strtolower($role) == 'admin') ? 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)';

include 'config.php';

// Data BPS dummy jika fungsi belum ada
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Panenusa Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { background-color: #080b14; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .sidebar-item { transition: all 0.3s ease; border-radius: 12px; margin-bottom: 4px; color: #94a3b8; }
        .sidebar-item:hover { background: rgba(255, 255, 255, 0.05); color: <?= $accent ?>; }
        .sidebar-active { background: rgba(255, 255, 255, 0.05); color: <?= $accent ?> !important; font-weight: 700; border-left: 4px solid <?= $accent ?>; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">
    <aside class="w-72 card-glass border-r border-white/5 p-6 hidden lg:flex flex-col">
        <div class="mb-10 px-2">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: <?= $gradient ?>">
                    <i class="fas fa-leaf text-lg"></i>
                </div>
                <span class="text-xl font-bold text-white">Panenusa<span style="color: <?= $accent ?>">.pro</span></span>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto">
            <a href="dashboard.php" class="sidebar-item sidebar-active flex items-center gap-4 p-3.5">
                <i class="fas fa-chart-pie"></i><span>Dashboard</span>
            </a>
            <a href="auth.php?action=logout" class="sidebar-item flex items-center gap-4 p-3.5 text-red-400 mt-auto">
                <i class="fas fa-power-off"></i><span>Keluar</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-6 lg:p-12">
        <header class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-bold text-white">Selamat Datang, <?= htmlspecialchars($nama) ?>!</h1>
                <p class="text-slate-500">Role: <span style="color: <?= $accent ?>"><?= ucfirst($role) ?></span></p>
            </div>
        </header>
        
        <div class="card-glass p-10 rounded-[3rem]">
            <h3 class="font-bold text-xl text-white mb-8">Statistik Produksi</h3>
            <div class="h-[350px]"><canvas id="bpsChart"></canvas></div>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('bpsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($top8Data, 'name')) ?>,
                datasets: [{
                    label: 'Produksi (Ton)',
                    data: <?= json_encode(array_column($top8Data, 'value')) ?>,
                    backgroundColor: '<?= $accent ?>',
                    borderRadius: 10
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    </script>
</body>
</html>