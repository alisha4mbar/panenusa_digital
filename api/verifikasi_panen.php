<?php
ob_start();
session_start();
require_once __DIR__ . '/config.php';

// Proteksi Halaman: Hanya Admin Logistik yang boleh masuk
$userData = requireLogin();
$user_id   = $userData['id'];
$nama_user = $userData['nama'];
$role_user = $userData['role'];

if (strtolower($role_user) !== 'admin') {
    header('Location: login.php');
    exit();
}

$msg = '';
$error = '';

// ==========================================
// 🚀 PROSES ACTION: APPROVAL / SORTIR (LAYAK ATAU CACAT)
// ==========================================
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_panen = mysqli_real_escape_string($conn, $_GET['id']);
    $action   = strtolower($_GET['action']); // 'layak' atau 'cacat'

    if (in_array($action, ['layak', 'cacat'])) {
        $update_query = "UPDATE transaksi_panen SET status = '$action' WHERE id = '$id_panen'";
        if (mysqli_query($conn, $update_query)) {
            $msg = "Transaksi ID #$id_panen berhasil diverifikasi sebagai: " . strtoupper($action);
        } else {
            $error = "Gagal memperbarui status verifikasi.";
        }
    }
}

// ==========================================
// 📊 DATA ENTRI ANTREAN YANG BELUM DIVERIFIKASI
// ==========================================
$sql = "SELECT tp.*, u.nama as nama_petani, l.nama_lahan 
        FROM transaksi_panen tp 
        JOIN users u ON tp.petani_id = u.id 
        LEFT JOIN lahan l ON tp.lahan_id = l.id 
        WHERE tp.status = 'pending' 
        ORDER BY tp.created_at DESC";
$query_antrean = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Data Panen | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }
    </style>
</head>
<body class="bg-[#0b1224] text-slate-200 flex min-h-screen overflow-hidden font-sans">

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
            
            <a href="dashboard_admin.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>
            
            <a href="verifikasi_panen.php" class="flex items-center gap-3 px-4 py-3 text-white bg-gradient-to-r from-emerald-500/20 to-emerald-500/5 rounded-xl border border-emerald-500/20 font-semibold shadow-inner transition-all duration-200">
                <i class="fas fa-clipboard-check text-emerald-400"></i> <span>Verifikasi Panen</span>
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
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Alokasi Sortir Otomatis (FR-PANEN-03)</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wide">Admin Logistik</p>
                </div>
                <div class="w-10 h-10 bg-[#1e2d4a] rounded-xl flex items-center justify-center border border-slate-700/50 shadow-inner">
                    <i class="fas fa-user text-slate-300"></i>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight">Pemeriksaan Gerbang Mutu</h1>
                    <p class="text-slate-400 text-sm mt-1">Saring kelayakan komoditas petani berdasarkan batas kerusakan fisik pra-distribusi.</p>
                </div>
                <div class="bg-[#111c35] border border-slate-800 px-4 py-2 rounded-2xl text-xs text-slate-400 font-medium">
                    Formulir Utama Evaluasi
                </div>
            </div>

            <?php if ($msg): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-check-circle"></i> <span><?= htmlspecialchars($msg) ?></span>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-exclamation-circle"></i> <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <div class="bg-[#111c35] rounded-[2rem] border border-slate-800/80 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent"></div>
                
                <?php if ($query_antrean && mysqli_num_rows($query_antrean) > 0): ?>
                <div class="overflow-x-auto rounded-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#162544]/60 backdrop-blur-md text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-800/60">
                                <th class="p-4 pl-6">Petani & Lahan</th>
                                <th class="p-4">Komoditas</th>
                                <th class="p-4">Timbangan</th>
                                <th class="p-4">Kerusakan</th>
                                <th class="p-4">Nota Fisik</th>
                                <th class="p-4 pr-6 text-center">Tindakan Sortir</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/40 bg-[#111c35]">
                            <?php while($row = mysqli_fetch_assoc($query_antrean)): ?>
                            <tr class="text-slate-300 hover:bg-[#162544]/30 transition-all duration-150">
                                <td class="p-4 pl-6">
                                    <p class="font-bold text-white"><?= htmlspecialchars($row['nama_petani']) ?></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($row['nama_lahan'] ?? 'Lahan Utama') ?> (<span class="text-slate-400"><?= htmlspecialchars($row['musim_tanam'] ?? '-') ?></span>)</p>
                                </td>
                                <td class="p-4">
                                    <span class="bg-slate-800/50 border border-slate-700/40 px-2.5 py-1 rounded-lg text-xs font-medium text-slate-200">
                                        <?= htmlspecialchars($row['komoditas']) ?>
                                    </span>
                                    <p class="text-[10px] text-slate-500 mt-1 pl-1"><?= htmlspecialchars($row['varietas'] ?? '-') ?></p>
                                </td>
                                <td class="p-4 font-semibold text-white">
                                    <?= number_format($row['berat_tonase'], 3) ?> <span class="text-xs font-normal text-slate-500">Ton</span>
                                    <p class="text-[10px] text-slate-500 font-normal"><?= number_format($row['berat_tonase'] * 1000, 0) ?> Kg</p>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold <?= (floatval($row['persentase_kerusakan']) > 10) ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= (floatval($row['persentase_kerusakan']) > 10) ? 'bg-red-400' : 'bg-amber-400' ?>"></span>
                                        <?= htmlspecialchars($row['persentase_kerusakan']) ?>%
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php if (!empty($row['foto_nota'])): ?>
                                        <a href="../public/uploads/<?= htmlspecialchars($row['foto_nota']) ?>" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-blue-400 hover:text-blue-300 font-medium transition">
                                            <i class="fas fa-image text-sm"></i> Periksa Nota
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-600 italic">Tidak Ada Berkas</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="inline-flex gap-2">
                                        <a href="verifikasi_panen.php?action=layak&id=<?= $row['id'] ?>" 
                                           class="text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 hover:bg-emerald-500 hover:text-white px-3.5 py-2 rounded-xl shadow-sm transition-all duration-200 hover:scale-[1.02]">
                                            <i class="fas fa-check text-[10px] mr-1"></i> Layak
                                        </a>
                                        <a href="verifikasi_panen.php?action=cacat&id=<?= $row['id'] ?>" 
                                           class="text-xs font-bold text-red-400 bg-red-500/10 border border-red-500/20 hover:bg-red-500 hover:text-white px-3.5 py-2 rounded-xl shadow-sm transition-all duration-200 hover:scale-[1.02]">
                                            <i class="fas fa-times text-[10px] mr-1"></i> Cacat
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php else: ?>
                <div class="p-16 text-center relative overflow-hidden flex flex-col items-center justify-center">
                    <div class="absolute w-48 h-48 bg-gradient-to-br from-emerald-500/10 to-transparent blur-3xl rounded-full -z-10 animate-pulse"></div>
                    
                    <div class="w-20 h-20 bg-gradient-to-br from-[#1e2d4a] to-[#162544] rounded-2xl border border-slate-800/80 flex items-center justify-center text-slate-500 shadow-inner mb-4 group hover:border-emerald-500/30 transition-all duration-500">
                        <i class="fas fa-inbox text-3xl text-slate-600 group-hover:text-emerald-400 transition-colors duration-300"></i>
                    </div>
                    
                    <h3 class="text-base font-bold text-white tracking-wide">Antrean Kliring Selesai</h3>
                    <p class="text-xs text-slate-500 max-w-xs mx-auto mt-1.5 leading-relaxed">Seluruh entri data hasil panen petani telah selesai diproses dan dialokasikan ke gerbang sortir mutasi.</p>
                </div>
                <?php endif; ?>
                
            </div>

        </div>
    </main>
</body>
</html>
