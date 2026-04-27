<?php
session_start();

// 1. Proteksi Halaman: Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Mengambil data dari Session (Data ini diset saat login di auth.php)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pengguna';

// 3. Penyesuaian Tema Otomatis: Admin (Ungu), User (Hijau)
$accent = ($role == 'Admin' || $role == 'admin') ? '#6366f1' : '#10b981'; 
$gradient = ($role == 'Admin' || $role == 'admin') ? 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)';

// 4. Memuat Konfigurasi & Data (Pastikan file ini ada)
include 'config.php';
// Jika kamu punya fungsi khusus ambil data BPS, pastikan filenya di-include di sini
$dataBpsRaw = (function_exists('getBpsData')) ? getBpsData() : [];
$top8Data = array_slice($dataBpsRaw, 0, 8);
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
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-50 px-1"><?= ($role == 'Admin' || $role == 'admin') ? 'Administrator' : 'User Member' ?></p>
        </div>

        <nav class="flex-1 overflow-y-auto pr-2">
            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 px-4 opacity-60">Utama</div>
            
            <a href="dashboard.php" class="sidebar-item sidebar-active flex items-center gap-4 p-3.5">
                <div class="w-5 text-center"><i class="fas fa-chart-pie"></i></div>
                <span class="text-sm">Dashboard</span>
            </a>

            <?php if($role == 'Admin' || $role == 'admin'): ?>
                <a href="konten_edukasi.php" class="sidebar-item flex items-center gap-4 p-3.5">
                    <div class="w-5 text-center"><i class="fas fa-book-open"></i></div>
                    <span class="text-sm">Konten Edukasi</span>
                </a>
                <a href="moderasi_forum.php" class="sidebar-item flex items-center gap-4 p-3.5">
                    <div class="w-5 text-center"><i class="fas fa-shield-halved"></i></div>
                    <span class="text-sm">Moderasi Forum</span>
                </a>
                <a href="monitoring_transaksi.php" class="sidebar-item flex items-center gap-4 p-3.5">
                    <div class="w-5 text-center"><i class="fas fa-receipt"></i></div>
                    <span class="text-sm">Monitoring Transaksi</span>
                </a>
                <a href="kelola_pengguna.php" class="sidebar-item flex items-center gap-4 p-3.5">
                    <div class="w-5 text-center"><i class="fas fa-users-gear"></i></div>
                    <span class="text-sm">Kelola Pengguna</span>
                </a>
            <?php else: ?>
                <a href="peta_panen.php" class="sidebar-item flex items-center gap-4 p-3.5">
                    <div class="w-5 text-center"><i class="fas fa-map-location-dot"></i></div>
                    <span class="text-sm">Peta Pangan</span>
                </a>
                <a href="data_lahan.php" class="sidebar-item flex items-center gap-4 p-3.5">
                    <div class="w-5 text-center"><i class="fas fa-mountain-sun"></i></div>
                    <span class="text-sm">Data Lahan</span>
                </a>
                <a href="forum.php" class="sidebar-item flex items-center gap-4 p-3.5">
                    <div class="w-5 text-center"><i class="fas fa-comments"></i></div>
                    <span class="text-sm">Forum Diskusi</span>
                </a>
            <?php endif; ?>
        </nav>

        <a href="auth.php?action=logout" class="sidebar-item flex items-center gap-4 p-3.5 text-red-400 font-bold hover:bg-red-500/10 mt-auto transition">
            <div class="w-5 text-center"><i class="fas fa-power-off"></i></div>
            <span class="text-sm">Keluar Sesi</span>
        </a>
    </aside>

    <main class="flex-1 overflow-y-auto p-6 lg:p-12">
        <header class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Selamat Datang, <?= htmlspecialchars($nama) ?>!</h1>
                <p class="text-slate-500 text-sm mt-1">Status Anda saat ini: <span style="color: <?= $accent ?>"><?= ucfirst($role) ?></span></p>
            </div>
            <div class="flex items-center gap-4 p-2 card-glass rounded-2xl pr-6">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white" style="background: <?= $gradient ?>">
                    <i class="fas fa-user"></i>
                </div>
                <div class="hidden md:block">
                    <p class="text-xs font-bold text-slate-200"><?= htmlspecialchars($nama) ?></p>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest"><?= $role ?></p>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="card-glass p-8 rounded-[2.5rem] border-l-4" style="border-color: <?= $accent ?>">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Pantauan Lahan</p>
                <h3 class="text-3xl font-black text-white">10.4M <span class="text-xs font-normal opacity-40">Ha</span></h3>
            </div>
            <div class="card-glass p-8 rounded-[2.5rem] border-l-4" style="border-color: <?= $accent ?>">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Produksi Beras</p>
                <h3 class="text-3xl font-black text-white">31.2M <span class="text-xs font-normal opacity-40">Ton</span></h3>
            </div>
            <div class="card-glass p-8 rounded-[2.5rem] border-l-4" style="border-color: <?= $accent ?>">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Edukasi Tersedia</p>
                <h3 class="text-3xl font-black text-white">128 <span class="text-xs font-normal opacity-40">Modul</span></h3>
            </div>
        </div>

        <div class="card-glass p-10 rounded-[3rem]">
            <h3 class="font-bold text-xl text-white mb-8">Statistik Produksi Nasional</h3>
            <div class="h-[350px]">
                <canvas id="bpsChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('bpsChart').getContext('2d');
            const dataBps = <?= json_encode($top8Data) ?>;
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dataBps.length > 0 ? dataBps.map(d => d.name) : ['Jawa Timur', 'Jawa Tengah', 'Jawa Barat', 'Sulsel'],
                    datasets: [{
                        label: 'Produksi (Ton)',
                        data: dataBps.length > 0 ? dataBps.map(d => d.value) : [9.5, 9.2, 9.0, 5.2],
                        backgroundColor: '<?= $accent ?>',
                        borderRadius: 12,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#64748b' } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    }
                }
            });
        });
    </script>
</body>
</html>