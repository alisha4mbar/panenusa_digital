<?php
session_start();
include 'config.php';

// Proteksi: Hanya Admin yang bisa mengakses log sistem
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
$accent = '#6366f1'; 
$gradient = 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)';

// Simulasi Data Log API (Biasanya ditarik dari tabel 'sys_logs' atau file .log)
$logs = [
    ['time' => '2023-10-27 10:45:22', 'endpoint' => '/api/v1/bps-data', 'method' => 'GET', 'status' => 200, 'res_time' => '120ms'],
    ['time' => '2023-10-27 10:46:05', 'endpoint' => '/api/v1/weather-update', 'method' => 'POST', 'status' => 201, 'res_time' => '350ms'],
    ['time' => '2023-10-27 10:48:12', 'endpoint' => '/api/v1/user-auth', 'method' => 'POST', 'status' => 401, 'res_time' => '45ms'],
    ['time' => '2023-10-27 10:50:00', 'endpoint' => '/api/v1/market-prices', 'method' => 'GET', 'status' => 500, 'res_time' => '1.2s'],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Logs | Panenusa Pro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;600&family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { background-color: #080b14; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .font-mono { font-family: 'Fira Code', monospace; }
        .sidebar-item { transition: all 0.3s ease; border-radius: 12px; margin-bottom: 4px; color: #94a3b8; }
        .sidebar-active { background: rgba(255, 255, 255, 0.05); color: <?= $accent ?> !important; font-weight: 700; border-left: 4px solid <?= $accent ?>; }
        
        .status-200 { color: #10b981; }
        .status-401 { color: #f59e0b; }
        .status-500 { color: #ef4444; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-72 card-glass border-r border-white/5 p-6 hidden lg:flex flex-col">
        <div class="mb-10 px-2 text-xl font-bold text-white">
            Admin<span style="color: <?= $accent ?>">.Panel</span>
        </div>

        <nav class="flex-1 overflow-y-auto">
            <a href="dashboard.php" class="sidebar-item flex items-center gap-4 p-3.5">
                <div class="w-5 text-center"><i class="fas fa-chart-pie"></i></div>
                <span class="text-sm">Dashboard</span>
            </a>
            <a href="log_api.php" class="sidebar-item sidebar-active flex items-center gap-4 p-3.5">
                <div class="w-5 text-center"><i class="fas fa-terminal"></i></div>
                <span class="text-sm">API Logs</span>
            </a>
            <a href="kelola_pengguna.php" class="sidebar-item flex items-center gap-4 p-3.5">
                <div class="w-5 text-center"><i class="fas fa-users"></i></div>
                <span class="text-sm">Kelola Pengguna</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-6 lg:p-12">
        <header class="mb-12">
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">System API Logs</h1>
            <p class="text-slate-500 text-sm mt-1">Monitoring lalu lintas data dan kesehatan integrasi API.</p>
        </header>

        <div class="card-glass rounded-3xl overflow-hidden border border-white/10">
            <div class="bg-white/5 p-4 border-b border-white/10 flex justify-between items-center">
                <div class="flex gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500/50"></div>
                </div>
                <span class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Live System Feed</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left font-mono text-xs">
                    <thead>
                        <tr class="text-slate-500 border-b border-white/5">
                            <th class="p-4 font-bold">TIMESTAMP</th>
                            <th class="p-4 font-bold">METHOD</th>
                            <th class="p-4 font-bold">ENDPOINT</th>
                            <th class="p-4 font-bold">STATUS</th>
                            <th class="p-4 font-bold">LATENCY</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach($logs as $log): ?>
                        <tr class="hover:bg-white/[0.03] transition">
                            <td class="p-4 text-slate-500"><?= $log['time'] ?></td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded bg-white/5 text-white font-bold text-[10px]">
                                    <?= $log['method'] ?>
                                </span>
                            </td>
                            <td class="p-4 text-indigo-300"><?= $log['endpoint'] ?></td>
                            <td class="p-4 font-bold status-<?= $log['status'] ?>">
                                <?= $log['status'] ?>
                            </td>
                            <td class="p-4 text-slate-500"><?= $log['res_time'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <button class="px-4 py-2 bg-white/5 hover:bg-white/10 rounded-xl text-xs font-bold text-slate-400 transition">
                <i class="fas fa-download mr-2"></i> Export Log
            </button>
            <button class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 rounded-xl text-xs font-bold text-red-500 transition border border-red-500/20">
                <i class="fas fa-eraser mr-2"></i> Clear Logs
            </button>
        </div>
    </main>

</body>
</html>