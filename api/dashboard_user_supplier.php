<?php
ob_start();
session_start();
require_once __DIR__ . '/config.php';

// Proteksi Halaman
$userData = requireLogin();
$user_id   = $userData['id'];
$nama_user = $userData['nama'];
$role_user = $userData['role'];

$role_check = strtolower($role_user);

// Validasi Akses: Hanya untuk Pemilik Lahan (Supplier)
if ($role_check !== 'supplier' && $role_check !== 'pemilik') {
    // Jika ternyata masuk sebagai petani biasa, alihkan ke dashboard tani
    if ($role_check === 'user') {
        header('Location: dashboard_user.php');
        exit();
    }
    header('Location: login.php');
    exit();
}

// ==========================================
// 📊 METRICS & CHART DATA COLLECTION
// ==========================================

// 1. Total Produksi Seluruh Lahan Milik Pemilik Ini
$query_total = mysqli_query($conn, "SELECT SUM(tp.berat_tonase) as total_produksi, COUNT(DISTINCT tp.lahan_id) as total_petak FROM transaksi_panen tp JOIN lahan l ON tp.lahan_id = l.id WHERE l.pemilik_id = '$user_id'");
$res_total = mysqli_fetch_assoc($query_total);
$total_produksi = $res_total['total_produksi'] ?? 0;
$total_petak    = $res_total['total_petak'] ?? 0;

// 2. Data Grafik Kualitas: Layak vs Cacat
$query_kualitas = mysqli_query($conn, "SELECT status, SUM(berat_tonase) as jumlah FROM transaksi_panen tp JOIN lahan l ON tp.lahan_id = l.id WHERE l.pemilik_id = '$user_id' GROUP BY status");
$kualitas_data = ['layak' => 0, 'cacat' => 0, 'pending' => 0];
while ($row = mysqli_fetch_assoc($query_kualitas)) {
    $st = strtolower($row['status']);
    if (in_array($st, ['layak', 'approved'])) $kualitas_data['layak'] += $row['jumlah'];
    elseif (in_array($st, ['cacat', 'rejected'])) $kualitas_data['cacat'] += $row['jumlah'];
    else $kualitas_data['pending'] += $row['jumlah'];
}

// 3. Riwayat Musim Tanam per Lahan (Tabel Utama)
$sql_riwayat = "SELECT tp.musim_tanam, tp.komoditas, l.nama_lahan, SUM(tp.berat_tonase) as total_panen,
                COUNT(tp.id) as total_transaksi,
                SUM(CASE WHEN tp.status IN ('layak', 'approved') THEN tp.berat_tonase ELSE 0 END) as total_layak
                FROM transaksi_panen tp 
                JOIN lahan l ON tp.lahan_id = l.id 
                WHERE l.pemilik_id = '$user_id'
                GROUP BY tp.musim_tanam, tp.komoditas, l.nama_lahan
                ORDER BY tp.musim_tanam DESC";
$query_riwayat = mysqli_query($conn, $sql_riwayat);

// 4. Data Tren Per Musim Tanam (Untuk Chart Batang)
$tren_musim = [];
$tren_tonase = [];
$query_tren = mysqli_query($conn, "SELECT tp.musim_tanam, SUM(tp.berat_tonase) as total FROM transaksi_panen tp JOIN lahan l ON tp.lahan_id = l.id WHERE l.pemilik_id = '$user_id' GROUP BY tp.musim_tanam ORDER BY tp.musim_tanam ASC LIMIT 6");
while ($t = mysqli_fetch_assoc($query_tren)) {
    $tren_musim[] = $t['musim_tanam'] ?? 'Tanpa Musim';
    $tren_tonase[] = (float)$t['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produksi Pemilik Lahan | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#0f172a] text-slate-200 flex min-h-screen font-sans">

    <aside class="w-64 bg-[#1e293b] border-r border-slate-800 flex flex-col hidden md:flex">
        <div class="p-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-leaf text-white text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Panenusa</h2>
            </div>
        </div>
        
        <nav class="flex-1 px-4 space-y-1">
            <p class="text-[10px] font-bold text-slate-500 px-4 mb-2 uppercase tracking-widest">Menu Eksekutif</p>
            
            <a href="#" class="flex items-center gap-3 p-3 text-emerald-400 bg-emerald-400/5 rounded-xl border border-emerald-400/10">
                <i class="fas fa-chart-line w-5"></i> <span>Laporan Produksi</span>
            </a>
            
            <a href="data_lahan.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-map-location-dot w-5"></i> <span>Aset Lahan Saya</span>
            </a>

            <a href="forum.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-comments w-5"></i> <span>Forum Kemitraan</span>
            </a>
        </nav>

        <div class="p-4 mt-auto">
            <a href="auth.php?action=logout" class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                <i class="fas fa-power-off w-5"></i> <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <div class="bg-[#1e293b]/50 backdrop-blur-md border-b border-slate-800 sticky top-0 z-10 p-4 px-8 flex justify-between items-center">
            <h2 class="font-semibold text-slate-400">Dashboard Pemilik Lahan (Investor/Supplier)</h2>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-[10px] text-emerald-500 font-bold uppercase">Pemilik Aset Lahan</p>
                </div>
                <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center border border-slate-600">
                    <i class="fas fa-user text-slate-300"></i>
                </div>
            </div>
        </div>

        <div class="p-8 max-w-6xl mx-auto space-y-8">
            <div>
                <h1 class="text-4xl font-black text-white tracking-tight">ANALISIS PRODUKTIVITAS ASET</h1>
                <p class="text-slate-400 mt-2">Ringkasan hasil komparasi, mutu panen, dan performa investasi pertanian terpadu.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800">
                    <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Aset Lahan Aktif</p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= $total_petak ?> <span class="text-lg font-normal text-slate-500">Petak</span></h3>
                </div>
                <div class="bg-gradient-to-br from-emerald-500/10 to-transparent p-6 rounded-[2rem] border border-emerald-500/20">
                    <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Akumulasi Hasil Panen</p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= number_format($total_produksi, 1) ?> <span class="text-lg font-normal text-slate-500">Ton</span></h3>
                </div>
                <div class="bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800">
                    <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Rasio Mutu Layak</p>
                    <?php 
                        $rasio = $total_produksi > 0 ? ($kualitas_data['layak'] / $total_produksi) * 100 : 0;
                    ?>
                    <h3 class="text-4xl font-black text-emerald-400 mt-1"><?= number_format($rasio, 1) ?> <span class="text-lg font-normal text-slate-500">%</span></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800 shadow-xl">
                    <h3 class="text-base font-bold text-white mb-4"><i class="fas fa-chart-bar mr-2 text-emerald-500"></i> Tren Tonase per Musim Tanam</h3>
                    <div class="h-64 relative">
                        <canvas id="chartTrenMusim"></canvas>
                    </div>
                </div>

                <div class="bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800 shadow-xl">
                    <h3 class="text-base font-bold text-white mb-4"><i class="fas fa-chart-pie mr-2 text-emerald-500"></i> Komposisi Kualitas Panen</h3>
                    <div class="h-64 relative flex items-center justify-center">
                        <canvas id="chartKualitas"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-[#1e293b] rounded-[2rem] border border-slate-800 p-6 shadow-xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Riwayat Musim Tanam per Lahan</h3>
                        <p class="text-xs text-slate-400 mt-1">Rekapitulasi total produksi komoditas dan rasio distribusi lolos seleksi.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="pb-4">Musim Tanam</th>
                                <th class="pb-4">Nama Lahan</th>
                                <th class="pb-4">Komoditas</th>
                                <th class="pb-4">Total Produksi</th>
                                <th class="pb-4">Lolos Sortir</th>
                                <th class="pb-4 text-right">Status Distribusi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/50">
                            <?php if ($query_riwayat && mysqli_num_rows($query_riwayat) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query_riwayat)): ?>
                                <tr class="text-slate-300 hover:bg-slate-800/30 transition-colors">
                                    <td class="py-4 font-bold text-white"><?= htmlspecialchars($row['musim_tanam'] ?? 'Umum') ?></td>
                                    <td class="py-4 text-slate-400"><?= htmlspecialchars($row['nama_lahan']) ?></td>
                                    <td class="py-4 font-medium"><?= htmlspecialchars($row['komoditas']) ?></td>
                                    <td class="py-4 font-semibold text-white"><?= number_format($row['total_panen'], 2) ?> Ton</td>
                                    <td class="py-4 text-emerald-400 font-semibold"><?= number_format($row['total_layak'], 2) ?> Ton</td>
                                    <td class="py-4 text-right">
                                        <?php if ($row['total_layak'] == $row['total_panen']): ?>
                                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-extrabold px-2.5 py-1 rounded-md uppercase">TERDISTRIBUSI</span>
                                        <?php else: ?>
                                            <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-extrabold px-2.5 py-1 rounded-md uppercase">PARSIAL</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500 text-xs">
                                        <i class="fas fa-chart-bar text-3xl mb-3 block text-slate-600"></i> Belum ada rekaman siklus musim tanam atau pengiriman hasil panen terdaftar.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        // 1. Script Render Tren Musim Tanam (Chart Bar)
        const ctxTren = document.getElementById('chartTrenMusim').getContext('2d');
        new Chart(ctxTren, {
            type: 'bar',
            data: {
                labels: <?= json_encode($tren_musim) ?>,
                datasets: [{
                    label: 'Produksi (Ton)',
                    data: <?= json_encode($tren_tonase) ?>,
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        // 2. Script Render Kualitas Mutu (Chart Doughnut)
        const ctxKualitas = document.getElementById('chartKualitas').getContext('2d');
        new Chart(ctxKualitas, {
            type: 'doughnut',
            data: {
                labels: ['Layak', 'Cacat', 'Pending'],
                datasets: [{
                    data: [
                        <?= (float)$kualitas_data['layak'] ?>, 
                        <?= (float)$kualitas_data['cacat'] ?>, 
                        <?= (float)$kualitas_data['pending'] ?>
                    ],
                    backgroundColor: [
                        '#10b981', // Hijau Emerald untuk Layak
                        '#ef4444', // Merah untuk Cacat
                        '#f59e0b'  // Amber untuk Pending
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', boxWidth: 12 } }
                }
            }
        });
    </script>
</body>
</html>