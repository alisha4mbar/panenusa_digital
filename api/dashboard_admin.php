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
            <p class="text-[10px] font-bold text-slate-500 px-4 mb-2 uppercase tracking-widest">Menu Utama</p>
            
            <a href="dashboard_admin.php" class="flex items-center gap-3 p-3 text-emerald-400 bg-emerald-400/5 rounded-xl border border-emerald-400/10">
                <i class="fas fa-th-large w-5"></i> <span>Dashboard</span>
            </a>
            
            <a href="verifikasi_panen.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-clipboard-check w-5"></i> <span>Verifikasi Panen</span>
                <span class="ml-auto bg-amber-500/20 text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded-full">FR-03</span>
            </a>

            <a href="cetak_surat_jalan.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-file-invoice w-5"></i> <span>Cetak Surat Jalan</span>
                <span class="ml-auto bg-blue-500/20 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded-full">FR-05</span>
            </a>

            <div class="pt-4 mt-4 border-t border-slate-800">
                <p class="text-[10px] font-bold text-slate-500 px-4 mb-2 uppercase tracking-widest">Administrator</p>
                <a href="kelola_pengguna.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                    <i class="fas fa-users-gear w-5"></i> <span>Kelola Pengguna</span>
                </a>
                <a href="data_referensi.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                    <i class="fas fa-database w-5"></i> <span>Data Referensi</span>
                </a>
            </div>
        </nav>

        <div class="p-4 mt-auto">
            <a href="auth.php?action=logout" class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                <i class="fas fa-power-off w-5"></i> <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <div class="bg-[#1e293b]/50 backdrop-blur-md border-b border-slate-800 sticky top-0 z-10 p-4 px-8 flex justify-between items-center">
            <h2 class="font-semibold text-slate-400">Ringkasan Sistem (Admin Logistik)</h2>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-[10px] text-emerald-500 font-bold uppercase">Admin Logistik</p>
                </div>
                <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center border border-slate-600">
                    <i class="fas fa-user text-slate-300"></i>
                </div>
            </div>
        </div>

        <div class="p-8 max-w-6xl mx-auto">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-white">Halo, <?= htmlspecialchars(explode(' ', $nama_user)[0]) ?>!</h1>
                <p class="text-slate-400 mt-2">Selamat datang kembali di sistem kendali logistik Panenusa.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-gradient-to-br from-emerald-500/10 to-transparent p-6 rounded-[2rem] border border-emerald-500/20">
                    <div class="flex justify-between items-start mb-4">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Tonase Harian</p>
                        <span class="text-[10px] bg-emerald-500/20 text-emerald-400 font-bold px-2 py-0.5 rounded">FR-PANEN-03</span>
                    </div>
                    <h3 class="text-4xl font-black text-white mt-1"><?= number_format($total_tonase, 1) ?> <span class="text-lg font-normal text-slate-500">Ton</span></h3>
                </div>

                <div class="bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800">
                    <div class="flex justify-between items-start mb-4">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Kapasitas Gudang Utama</p>
                        <span class="text-[10px] bg-blue-500/20 text-blue-400 font-bold px-2 py-0.5 rounded">Real-time</span>
                    </div>
                    <h3 class="text-4xl font-black text-white mt-1"><?= $kapasitas_gudang_utama ?> <span class="text-lg font-normal text-slate-500">%</span></h3>
                    <div class="w-full bg-slate-700 h-2 rounded-full mt-3 overflow-hidden">
                        <div class="bg-blue-500 h-full" style="width: <?= $kapasitas_gudang_utama ?>%"></div>
                    </div>
                </div>

                <div class="bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800">
                    <div class="flex justify-between items-start mb-4">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Kapasitas Unit Pengolahan</p>
                        <span class="text-[10px] bg-purple-500/20 text-purple-400 font-bold px-2 py-0.5 rounded">FR-PANEN-06</span>
                    </div>
                    <h3 class="text-4xl font-black text-white mt-1"><?= $kapasitas_unit_olah ?> <span class="text-lg font-normal text-slate-500">%</span></h3>
                    <div class="w-full bg-slate-700 h-2 rounded-full mt-3 overflow-hidden">
                        <div class="bg-purple-500 h-full" style="width: <?= $kapasitas_unit_olah ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-[#1e293b] rounded-[2rem] border border-slate-800 p-6 shadow-xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Butuh Verifikasi Segera</h3>
                        <p class="text-xs text-slate-400 mt-1">Daftar entri data panen yang dikirim petani dan menunggu konfirmasi sortir.</p>
                    </div>
                    <span class="text-xs font-bold text-amber-400 bg-amber-500/10 px-3 py-1 rounded-full">FR-PANEN-03</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="pb-4">Petani</th>
                                <th class="pb-4">Komoditas</th>
                                <th class="pb-4">Tonase</th>
                                <th class="pb-4">Tanggal Masuk</th>
                                <th class="pb-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/50">
                            <?php if ($query_verifikasi && mysqli_num_rows($query_verifikasi) > 0): ?>
                                <?php while($row_panen = mysqli_fetch_assoc($query_verifikasi)): ?>
                                <tr class="text-slate-300 hover:bg-slate-800/30 transition-colors">
                                    <td class="py-4 font-semibold text-white"><?= htmlspecialchars($row_panen['nama_petani']) ?></td>
                                    <td class="py-4"><?= htmlspecialchars($row_panen['komoditas']) ?></td>
                                    <td class="py-4"><?= number_format($row_panen['berat_tonase'], 1) ?> Ton</td>
                                    <td class="py-4"><?= date('d M Y', strtotime($row_panen['created_at'])) ?></td>
                                    <td class="py-4 text-right">
                                        <a href="verifikasi_panen.php?id=<?= $row_panen['id'] ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500 hover:text-white px-3 py-1.5 rounded-xl transition">
                                            <i class="fas fa-check-double"></i> Periksa Sortir
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?> <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-500 text-xs">
                                        <i class="fas fa-inbox text-2xl mb-2 block"></i> Tidak ada transaksi panen baru yang membutuhkan verifikasi.
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
