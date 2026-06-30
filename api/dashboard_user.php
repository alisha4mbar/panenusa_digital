<?php
ob_start();
session_start();
require_once __DIR__ . '/config.php';

// Proteksi Halaman: Ambil state data user dari Session/Cookie Vercel
$userData = requireLogin();
$user_id   = $userData['id'];
$nama_user = $userData['nama'];
$role_user = $userData['role'];

// Tolak akses jika yang masuk bukan role 'user' (Petani)
if (strtolower($role_user) !== 'user') {
    header('Location: login.php');
    exit();
}

// ==========================================
// 📊 DATA AKURAT HISTORI PANEN PETANI (FR-PANEN-02 & FR-03)
// ==========================================
// 1. Ambil Ringkasan Status Panen Paling Terakhir
$query_terakhir = mysqli_query($conn, "SELECT tp.*, l.nama_lahan FROM transaksi_panen tp LEFT JOIN lahan l ON tp.lahan_id = l.id WHERE tp.petani_id = '$user_id' ORDER BY tp.created_at DESC LIMIT 1");
$panen_terakhir = ($query_terakhir && mysqli_num_rows($query_terakhir) > 0) ? mysqli_fetch_assoc($query_terakhir) : null;

// 2. Riwayat Panen Saya: Ambil Seluruh Histori Transaksi Panen Milik Petani Ini
$query_riwayat = mysqli_query($conn, "SELECT tp.*, l.nama_lahan FROM transaksi_panen tp LEFT JOIN lahan l ON tp.lahan_id = l.id WHERE tp.petani_id = '$user_id' ORDER BY tp.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Utama Petani | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#0f172a] text-slate-100 font-sans min-h-screen pb-12">

    <header class="bg-[#1e293b] border-b border-slate-800 p-5 sticky top-0 z-10 shadow-md">
        <div class="max-w-md mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-leaf text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-black text-white tracking-tight">PANENUSA</h1>
                    <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-wide">Aktor: Petani</p>
                </div>
            </div>
            <a href="auth.php?action=logout" class="w-10 h-10 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-xl flex items-center justify-center transition border border-red-500/20">
                <i class="fas fa-power-off text-sm"></i>
            </a>
        </div>
    </header>

    <main class="max-w-md mx-auto px-4 pt-6 space-y-6">

        <div>
            <h2 class="text-3xl font-black text-white">Halo, <?= htmlspecialchars(explode(' ', $nama_user)[0]) ?>!</h2>
            <p class="text-slate-400 text-sm mt-1">Pantau status hasil petikan dan timbangan ladang Anda.</p>
        </div>

        <a href="input_panen.php" class="block w-full bg-emerald-500 hover:bg-emerald-600 text-white p-5 rounded-2xl font-bold text-center text-lg shadow-xl shadow-emerald-500/10 transition active:scale-[0.98]">
            <i class="fas fa-plus-circle mr-2 text-xl align-middle"></i> <span class="align-middle">Mulai Input Panen Baru</span>
            <span class="block text-[11px] font-medium text-emerald-100 mt-1 opacity-80">Formulir FR-PANEN-02</span>
        </a>

        <div class="bg-[#1e293b] rounded-2xl border border-slate-800 p-5 shadow-lg">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Status Panen Terakhir</p>
            
            <?php if ($panen_terakhir): ?>
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-xl font-bold text-white"><?= htmlspecialchars($panen_terakhir['komoditas']) ?></h4>
                        <p class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($panen_terakhir['nama_lahan'] ?? 'Lahan Utama') ?> • <?= number_format($panen_terakhir['berat_tonase'] * 1000, 0) ?> Kg</p>
                    </div>
                    <div>
                        <?php 
                        $status = strtolower($panen_terakhir['status'] ?? 'pending');
                        if ($status === 'layak' || $status === 'approved'): ?>
                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-extrabold px-3 py-1.5 rounded-xl uppercase">LAYAK</span>
                        <?php elseif ($status === 'cacat' || $status === 'rejected'): ?>
                            <span class="bg-red-500/10 text-red-400 border border-red-500/20 text-xs font-extrabold px-3 py-1.5 rounded-xl uppercase">CACAT</span>
                        <?php else: ?>
                            <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-extrabold px-3 py-1.5 rounded-xl uppercase">DIPERIKSA</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-xs text-slate-500 py-2 italic"><i class="fas fa-info-circle mr-1"></i> Belum ada rekaman data panen.</p>
            <?php endif; ?>
        </div>

        <div class="space-y-3">
            <div class="flex justify-between items-center px-1">
                <h3 class="font-bold text-white text-base">Riwayat Panen Saya</h3>
                <span class="text-[10px] font-bold text-slate-400 bg-slate-800 px-2 py-1 rounded">FR-PANEN-03</span>
            </div>

            <div class="space-y-2">
                <?php if ($query_riwayat && mysqli_num_rows($query_riwayat) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($query_riwayat)): ?>
                        <div class="bg-[#1e293b]/60 border border-slate-800/80 p-4 rounded-xl flex justify-between items-center">
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-white"><?= htmlspecialchars($row['komoditas']) ?></p>
                                <p class="text-[11px] text-slate-400"><?= date('d M Y', strtotime($row['tanggal_panen'])) ?> • <span class="font-semibold text-slate-300"><?= number_format($row['berat_tonase'] * 1000, 0) ?> Kg</span></p>
                            </div>
                            <div>
                                <?php 
                                $st = strtolower($row['status'] ?? 'pending');
                                if ($st === 'layak' || $st === 'approved'): ?>
                                    <span class="text-xs font-bold text-emerald-400"><i class="fas fa-circle text-[8px] mr-1"></i> Layak</span>
                                <?php elseif ($st === 'cacat' || $st === 'rejected'): ?>
                                    <span class="text-xs font-bold text-red-400"><i class="fas fa-circle text-[8px] mr-1"></i> Cacat</span>
                                <?php else: ?>
                                    <span class="text-xs font-bold text-amber-400"><i class="fas fa-circle text-[8px] mr-1"></i> Pending</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="bg-[#1e293b]/30 text-center py-8 rounded-xl border border-dashed border-slate-800 text-xs text-slate-500">
                        <i class="fas fa-folder-open text-xl mb-2 block"></i> Histori penyerahan panen kosong.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </main>
</body>
</html>