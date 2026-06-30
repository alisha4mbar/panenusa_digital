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
            
            <a href="dashboard_admin.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-th-large w-5"></i> <span>Dashboard</span>
            </a>
            
            <a href="verifikasi_panen.php" class="flex items-center gap-3 p-3 text-emerald-400 bg-emerald-400/5 rounded-xl border border-emerald-400/10">
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
            <h2 class="font-semibold text-slate-400">Verifikasi Data Panen (FR-PANEN-03)</h2>
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
            
            <div class="mb-8">
                <h1 class="text-3xl font-black text-white">Alokasi Sortir Otomatis</h1>
                <p class="text-slate-400 mt-1">Saring hasil komoditas masuk sebelum diterbitkan Surat Jalan Distribusi.</p>
            </div>

            <?php if ($msg): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl mb-6 text-sm">
                <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($msg) ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div class="bg-[#1e293b] rounded-[2rem] border border-slate-800 p-6 shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="pb-4">Petani & Lahan</th>
                                <th class="pb-4">Komoditas</th>
                                <th class="pb-4">Timbangan</th>
                                <th class="pb-4">Kerusakan</th>
                                <th class="pb-4">Nota Fisik</th>
                                <th class="pb-4 text-center">Tindakan Sortir</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/50">
                            <?php if ($query_antrean && mysqli_num_rows($query_antrean) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query_antrean)): ?>
                                <tr class="text-slate-300 hover:bg-slate-800/30 transition-colors">
                                    <td class="py-4">
                                        <p class="font-bold text-white"><?= htmlspecialchars($row['nama_petani']) ?></p>
                                        <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($row['nama_lahan'] ?? 'Lahan Utama') ?> (<?= htmlspecialchars($row['musim_tanam'] ?? '-') ?>)</p>
                                    </td>
                                    <td class="py-4">
                                        <span class="font-medium text-slate-200"><?= htmlspecialchars($row['komoditas']) ?></span>
                                        <p class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($row['varietas'] ?? '-') ?></p>
                                    </td>
                                    <td class="py-4 font-semibold text-white">
                                        <?= number_format($row['berat_tonase'], 3) ?> Ton
                                        <p class="text-[10px] text-slate-500 font-normal"><?= number_format($row['berat_tonase'] * 1000, 0) ?> Kg</p>
                                    </td>
                                    <td class="py-4">
                                        <span class="text-xs font-bold <?= (floatval($row['persentase_kerusakan']) > 10) ? 'text-red-400' : 'text-amber-400' ?>">
                                            <?= htmlspecialchars($row['persentase_kerusakan']) ?>%
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <?php if (!empty($row['foto_nota'])): ?>
                                            <a href="../public/uploads/<?= htmlspecialchars($row['foto_nota']) ?>" target="_blank" class="text-xs text-blue-400 hover:underline flex items-center gap-1">
                                                <i class="fas fa-image"></i> Lihat Nota
                                            </a>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-600 italic">Tidak Ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="inline-flex gap-2">
                                            <a href="verifikasi_panen.php?action=layak&id=<?= $row['id'] ?>" 
                                               class="text-xs font-bold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500 hover:text-white px-3 py-1.5 rounded-xl transition">
                                                <i class="fas fa-check mr-1"></i> Layak
                                            </a>
                                            <a href="verifikasi_panen.php?action=cacat&id=<?= $row['id'] ?>" 
                                               class="text-xs font-bold text-red-400 bg-red-500/10 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-xl transition">
                                                <i class="fas fa-times mr-1"></i> Cacat
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500 text-xs">
                                        <i class="fas fa-inbox text-3xl mb-3 block text-slate-600"></i> Antrean kosong. Semua data panen masuk telah selesai disortir.
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