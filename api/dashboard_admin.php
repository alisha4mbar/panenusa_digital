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

// Redirect jika bukan admin (karena halaman ini khusus Dashboard Admin Logistik)
if ($role_check !== 'admin') {
    header('Location: login.php');
    exit();
}

// ==========================================
// 📊 QUERY STATISTIK LOGISTIK (MOCK/REAL)
// ==========================================
// 1. Total Tonase Harian
$query_tonase = mysqli_query($conn, "SELECT SUM(berat_tonase) as total FROM transaksi_panen WHERE DATE(created_at) = CURDATE()");
$total_tonase = ($query_tonase && $row = mysqli_fetch_assoc($query_tonase)) ? ($row['total'] ?? 0) : 0;

// 2. Simulasi/Query Kapasitas Gudang (Bisa disesuaikan dengan tabel gudang Anda jika ada)
$kapasitas_gudang_utama = 75.5; // dalam persen (%)
$kapasitas_unit_olah   = 42.0; // dalam persen (%)

// 3. Ambil Transaksi Panen Terbaru yang Butuh Verifikasi (FR-PANEN-03)
$query_verifikasi = mysqli_query($conn, "SELECT tp.*, u.nama as nama_petani FROM transaksi_panen tp JOIN users u ON tp.petani_id = u.id WHERE tp.status = 'pending' ORDER BY tp.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Logistik | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-[#0b1224] text-slate-200 flex min-h-screen overflow-hidden">

    <aside class="w-64 bg-[#111c35] border-r border-slate-800/60 flex flex-col hidden md:flex z-20">
        <div class="p-6 border-b border-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-leaf text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight">Panenusa</h2>
                    <p class="text-[9px] text-emerald-400 font-bold tracking-wider uppercase">Logistics Core</p>
                </div>
            </div>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <p class="text-[10px] font-bold text-slate-500 px-3 mb-2 uppercase tracking-widest">Menu Utama</p>
            
            <a href="dashboard_admin.php" class="flex items-center gap-3 px-4 py-3 text-white bg-gradient-to-r from-emerald-500/20 to-emerald-500/5 rounded-xl border border-emerald-500/20 font-semibold shadow-inner transition-all duration-200">
                <i class="fas fa-th-large text-emerald-400"></i> <span>Dashboard</span>
            </a>
            
            <a href="verifikasi_panen.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200 group">
                <i class="fas fa-clipboard-check group-hover:text-amber-400 transition-colors"></i> <span>Verifikasi Panen</span>
                <span class="ml-auto bg-amber-500/10 text-amber-400 text-[9px] font-extrabold px-2 py-0.5 rounded-md border border-amber-500/20">FR-03</span>
            </a>

            <a href="cetak_surat_jalan.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200 group">
                <i class="fas fa-file-invoice group-hover:text-blue-400 transition-colors"></i> <span>Cetak Surat Jalan</span>
                <span class="ml-auto bg-blue-500/10 text-blue-400 text-[9px] font-extrabold px-2 py-0.5 rounded-md border border-blue-500/20">FR-05</span>
            </a>

            <div class="pt-6 mt-4 border-t border-slate-800/50">
                <p class="text-[10px] font-bold text-slate-500 px-3 mb-2 uppercase tracking-widest">Administrator</p>
                <a href="kelola_pengguna.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200">
                    <i class="fas fa-users-gear"></i> <span>Kelola Pengguna</span>
                </a>
                <a href="data_referensi.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200">
                    <i class="fas fa-database"></i> <span>Data Referensi</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-slate-800/50">
            <a href="auth.php?action=logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl font-medium transition-all duration-200">
                <i class="fas fa-power-off"></i> <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        
        <header class="bg-[#111c35]/60 backdrop-blur-xl border-b border-slate-800/50 p-4 px-8 flex justify-between items-center z-10">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sistem Kendali Logistik</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wide">Admin Logistik</p>
                </div>
                <div class="w-10 h-10 bg-[#1e2d4a] rounded-xl flex items-center justify-center border border-slate-700/50 shadow-inner group cursor-pointer hover:border-emerald-500/50 transition-all duration-300">
                    <i class="fas fa-user text-slate-300 group-hover:text-emerald-400 transition-colors"></i>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-8">
            
            <div class="relative overflow-hidden bg-gradient-to-r from-[#162544] to-[#111c35] p-6 md:p-8 rounded-[2rem] border border-slate-800/60 shadow-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/5 blur-3xl rounded-full"></div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Selamat Datang, <?= htmlspecialchars(explode(' ', $nama_user)[0]) ?>!</h1>
                    <p class="text-slate-400 text-sm mt-1.5">Semua kontrol gerbang masuk gudang dan alokasi sortir otomatis berada dalam kendali Anda.</p>
                </div>
                <div class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-2xl flex items-center gap-2 shadow-sm">
                    <i class="far fa-calendar-alt text-emerald-400 text-sm"></i>
                    <span class="text-xs font-bold text-emerald-300"><?= date('d M Y') ?></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="group bg-gradient-to-br from-[#162544] to-[#111c35] p-6 rounded-[2rem] border border-slate-800/80 hover:border-emerald-500/30 shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-scale-balanced text-lg"></i>
                        </div>
                        <span class="text-[9px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-extrabold px-2 py-0.5 rounded-md">FR-PANEN-03</span>
                    </div>
                    <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Tonase Harian</p>
                    <h3 class="text-3xl font-black text-white mt-1.5 tracking-tight">
                        <?= number_format($total_tonase, 1) ?> <span class="text-base font-medium text-slate-500">Ton</span>
                    </h3>
                </div>

                <div class="group bg-gradient-to-br from-[#162544] to-[#111c35] p-6 rounded-[2rem] border border-slate-800/80 hover:border-blue-500/30 shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-blue-500/10 rounded-2xl border border-blue-500/20 flex items-center justify-center text-blue-400 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-warehouse text-lg"></i>
                        </div>
                        <span class="text-[9px] bg-blue-500/10 text-blue-400 border border-blue-500/20 font-extrabold px-2 py-0.5 rounded-md">Real-time</span>
                    </div>
                    <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Gudang Utama</p>
                    <h3 class="text-3xl font-black text-white mt-1.5 tracking-tight">
                        <?= $kapasitas_gudang_utama ?> <span class="text-base font-medium text-slate-500">%</span>
                    </h3>
                    <div class="w-full bg-slate-800 h-2 rounded-full mt-4 overflow-hidden p-[1px]">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-400 h-full rounded-full transition-all duration-500" style="width: <?= $kapasitas_gudang_utama ?>%"></div>
                    </div>
                </div>

                <div class="group bg-gradient-to-br from-[#162544] to-[#111c35] p-6 rounded-[2rem] border border-slate-800/80 hover:border-purple-500/30 shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-purple-500/10 rounded-2xl border border-purple-500/20 flex items-center justify-center text-purple-400 group-hover:bg-purple-500 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-industry text-lg"></i>
                        </div>
                        <span class="text-[9px] bg-purple-500/10 text-purple-400 border border-purple-500/20 font-extrabold px-2 py-0.5 rounded-md">FR-PANEN-06</span>
                    </div>
                    <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Unit Pengolahan</p>
                    <h3 class="text-3xl font-black text-white mt-1.5 tracking-tight">
                        <?= $kapasitas_unit_olah ?> <span class="text-base font-medium text-slate-500">%</span>
                    </h3>
                    <div class="w-full bg-slate-800 h-2 rounded-full mt-4 overflow-hidden p-[1px]">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-full rounded-full transition-all duration-500" style="width: <?= $kapasitas_unit_olah ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-[#111c35] rounded-[2rem] border border-slate-800/80 p-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent"></div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-hourglass-half text-amber-400 text-sm animate-spin [animation-duration:3s]"></i> 
                            Antrean Verifikasi Masuk
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">Daftar entri data panen petani ter-update yang siap dialokasikan oleh sistem.</p>
                    </div>
                    <span class="text-[10px] font-extrabold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full tracking-wider uppercase">FR-PANEN-03</span>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-800/40">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#162544]/60 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-800/60">
                                <th class="p-4 pl-6">Nama Petani</th>
                                <th class="p-4">Komoditas</th>
                                <th class="p-4">Tonase Masuk</th>
                                <th class="p-4">Tanggal Masuk</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/40 bg-[#111c35]">
                            <?php if ($query_verifikasi && mysqli_num_rows($query_verifikasi) > 0): ?>
                                <?php while($row_panen = mysqli_fetch_assoc($query_verifikasi)): ?>
                                <tr class="text-slate-300 hover:bg-[#162544]/40 transition-all duration-200 group">
                                    <td class="p-4 pl-6 font-bold text-white group-hover:text-emerald-400 transition-colors">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 bg-slate-800 rounded-lg flex items-center justify-center text-[11px] text-slate-400 font-medium">
                                                <?= strtoupper(substr($row_panen['nama_petani'], 0, 2)) ?>
                                            </div>
                                            <?= htmlspecialchars($row_panen['nama_petani']) ?>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="bg-slate-800/60 border border-slate-700/40 px-2.5 py-1 rounded-lg text-xs font-medium text-slate-200">
                                            <?= htmlspecialchars($row_panen['komoditas']) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 font-semibold text-white">
                                        <?= number_format($row_panen['berat_tonase'], 1) ?> <span class="text-xs font-normal text-slate-500">Ton</span>
                                    </td>
                                    <td class="p-4 text-xs text-slate-400">
                                        <?= date('d M Y', strtotime($row_panen['created_at'])) ?>
                                    </td>
                                    <td class="p-4 pr-6 text-right">
                                        <a href="verifikasi_panen.php?id=<?= $row_panen['id'] ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 hover:bg-emerald-500 hover:text-white px-3.5 py-2 rounded-xl shadow-sm transition-all duration-200 group-hover:scale-[1.02]">
                                            <i class="fas fa-check-double text-[11px]"></i> Periksa Sortir
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?> 
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-slate-500 text-xs">
                                        <div class="w-16 h-16 bg-slate-800/40 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-800">
                                            <i class="fas fa-inbox text-xl text-slate-600"></i>
                                        </div>
                                        Tidak ada transaksi panen baru yang membutuhkan verifikasi.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
