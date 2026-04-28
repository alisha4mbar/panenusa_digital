<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$role = $_SESSION['role'] ?? 'User';
$nama = $_SESSION['nama'] ?? 'Pengguna';
$accent = ($role == 'Admin') ? '#6366f1' : '#10b981'; 
$gradient = ($role == 'Admin') ? 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)';

// Simulasi Data Transaksi (Nantinya ditarik dari tabel database 'transaksi')
$transactions = [
    ['id' => 'TRX-001', 'item' => 'Pupuk Urea Subsidit', 'tgl' => '2023-10-25', 'total' => 'Rp 150.000', 'status' => 'Selesai'],
    ['id' => 'TRX-002', 'item' => 'Bibit Padi Unggul', 'tgl' => '2023-10-26', 'total' => 'Rp 85.000', 'status' => 'Pending'],
    ['id' => 'TRX-003', 'item' => 'Cangkul Baja Modern', 'tgl' => '2023-10-27', 'total' => 'Rp 120.000', 'status' => 'Proses'],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Transaksi | Panenusa Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { background-color: #080b14; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .sidebar-item { transition: all 0.3s ease; border-radius: 12px; margin-bottom: 4px; color: #94a3b8; }
        .sidebar-active { background: rgba(255, 255, 255, 0.05); color: <?= $accent ?> !important; font-weight: 700; border-left: 4px solid <?= $accent ?>; }
        
        .status-badge { padding: 4px 12px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .status-selesai { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
        .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
        .status-proses { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-72 card-glass border-r border-white/5 p-6 hidden lg:flex flex-col">
        <div class="mb-10 px-2">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-lg" style="background: <?= $gradient ?>">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="text-xl font-bold text-white">Panenusa<span style="color: <?= $accent ?>">.pro</span></span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto">
            <a href="dashboard.php" class="sidebar-item flex items-center gap-4 p-3.5">
                <div class="w-5 text-center"><i class="fas fa-chart-pie"></i></div>
                <span class="text-sm">Dashboard</span>
            </a>
            <a href="monitoring_transaksi.php" class="sidebar-item sidebar-active flex items-center gap-4 p-3.5">
                <div class="w-5 text-center"><i class="fas fa-receipt"></i></div>
                <span class="text-sm">Transaksi</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-6 lg:p-12">
        <header class="mb-10">
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Riwayat Transaksi</h1>
            <p class="text-slate-500 text-sm">Pantau pengeluaran dan status pembelian perlengkapan tani Anda.</p>
        </header>

        <div class="card-glass rounded-[2.5rem] overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/[0.02] border-b border-white/5">
                    <tr>
                        <th class="p-6 text-xs font-bold uppercase text-slate-500">ID TRX</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-500">Item / Produk</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-500">Tanggal</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-500">Total</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-500 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach($transactions as $trx): ?>
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-6 font-mono text-xs text-slate-400"><?= $trx['id'] ?></td>
                        <td class="p-6 font-bold text-white"><?= $trx['item'] ?></td>
                        <td class="p-6 text-sm text-slate-400"><?= $trx['tgl'] ?></td>
                        <td class="p-6 font-bold text-emerald-400"><?= $trx['total'] ?></td>
                        <td class="p-6 text-center">
                            <span class="status-badge <?= 'status-' . strtolower($trx['status']) ?>">
                                <?= $trx['status'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="card-glass p-6 rounded-3xl border-l-4 border-emerald-500">
                <p class="text-slate-500 text-xs uppercase font-bold">Total Pengeluaran</p>
                <h3 class="text-2xl font-bold text-white mt-1">Rp 355.000</h3>
            </div>
            <div class="card-glass p-6 rounded-3xl border-l-4 border-amber-500">
                <p class="text-slate-500 text-xs uppercase font-bold">Transaksi Pending</p>
                <h3 class="text-2xl font-bold text-white mt-1">1 Item</h3>
            </div>
        </div>
    </main>

</body>
</html>