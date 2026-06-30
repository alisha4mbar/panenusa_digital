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

// Ambil ID detail untuk mode siap cetak jika ada parameter ?print_id
$print_data = null;
if (isset($_GET['print_id'])) {
    $print_id = mysqli_real_escape_string($conn, $_GET['print_id']);
    $sql_print = "SELECT tp.*, u.nama as nama_petani, l.nama_lahan 
                  FROM transaksi_panen tp 
                  JOIN users u ON tp.petani_id = u.id 
                  LEFT JOIN lahan l ON tp.lahan_id = l.id 
                  WHERE tp.id = '$print_id' AND tp.status = 'layak'";
    $res_print = mysqli_query($conn, $sql_print);
    if ($res_print && mysqli_num_rows($res_print) > 0) {
        $print_data = mysqli_fetch_assoc($res_print);
    }
}

// ==========================================
// 📊 AMBIL DATA PANEN YANG STATUSNYA 'LAYAK' (SIAP KIRIM / CETAK SURAT JALAN)
// ==========================================
$sql_list = "SELECT tp.*, u.nama as nama_petani, l.nama_lahan 
             FROM transaksi_panen tp 
             JOIN users u ON tp.petani_id = u.id 
             LEFT JOIN lahan l ON tp.lahan_id = l.id 
             WHERE tp.status = 'layak' 
             ORDER BY tp.created_at DESC";
$query_layak = mysqli_query($conn, $sql_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat Jalan (FR-PANEN-05) | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Mengatur tampilan cetak kertas agar bersih dari elemen web dashboard */
        @media print {
            body { background: white; color: black; }
            .no-print { display: none !important; }
            .print-area { display: block !important; margin: 0; padding: 20px; width: 100%; }
            .print-card { background: white !important; border: none !important; color: black !important; box-shadow: none !important; }
            .text-white { color: black !important; }
            .text-slate-400, .text-slate-500 { color: #475569 !important; }
        }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-200 flex min-h-screen font-sans">

    <aside class="w-64 bg-[#1e293b] border-r border-slate-800 flex flex-col hidden md:flex no-print">
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
            
            <a href="verifikasi_panen.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-clipboard-check w-5"></i> <span>Verifikasi Panen</span>
                <span class="ml-auto bg-amber-500/20 text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded-full">FR-03</span>
            </a>

            <a href="cetak_surat_jalan.php" class="flex items-center gap-3 p-3 text-emerald-400 bg-emerald-400/5 rounded-xl border border-emerald-400/10">
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

    <main class="flex-1 overflow-y-auto print-area">
        <div class="bg-[#1e293b]/50 backdrop-blur-md border-b border-slate-800 sticky top-0 z-10 p-4 px-8 flex justify-between items-center no-print">
            <h2 class="font-semibold text-slate-400">Cetak Surat Jalan Distribusi (FR-PANEN-05)</h2>
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

        <div class="p-8 max-w-4xl mx-auto">
            
            <?php if ($print_data): ?>
                <div class="bg-white text-black p-8 rounded-2xl border border-slate-300 shadow-2xl mb-10 print-card">
                    <div class="flex justify-between items-start border-b-2 border-black pb-5 mb-5">
                        <div>
                            <h2 class="text-2xl font-black tracking-tight">PT. PANENUSA LOGISTIK INDONESIA</h2>
                            <p class="text-xs text-gray-600">Sistem Integrasi Penyaluran Komoditas Tani Berkelanjutan</p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-lg font-bold uppercase tracking-wider bg-black text-white px-3 py-1 rounded no-print-bg">SURAT JALAN</h3>
                            <p class="text-xs text-gray-600 mt-1">No: SJ/<?= date('Ymd', strtotime($print_data['created_at'])) ?>/<?= $print_data['id'] ?></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase">Asal Muatan / Pihak Pertama:</p>
                            <p class="font-bold text-base mt-1"><?= htmlspecialchars($print_data['nama_petani']) ?></p>
                            <p class="text-gray-600"><?= htmlspecialchars($print_data['nama_lahan'] ?? 'Lahan Mitra Panenusa') ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 font-bold uppercase">Tanggal Pengiriman:</p>
                            <p class="font-bold text-base mt-1"><?= date('d F Y', time()) ?></p>
                            <p class="text-gray-600">Petugas: <?= htmlspecialchars($nama_user) ?></p>
                        </div>
                    </div>

                    <table class="w-full text-left border-collapse border border-black mb-8 text-sm">
                        <thead>
                            <tr class="bg-gray-100 border-b border-black font-bold">
                                <th class="p-3 border-r border-black">Deskripsi Komoditas</th>
                                <th class="p-3 border-r border-black">Varietas</th>
                                <th class="p-3 text-right">Tonase Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-black">
                                <td class="p-3 border-r border-black font-bold"><?= htmlspecialchars($print_data['komoditas']) ?></td>
                                <td class="p-3 border-r border-black"><?= htmlspecialchars($print_data['varietas'] ?? 'Standar') ?></td>
                                <td class="p-3 text-right font-bold"><?= number_format($print_data['berat_tonase'], 3) ?> Ton</td>
                            </tr>
                            <tr class="font-black bg-gray-50">
                                <td colspan="2" class="p-3 text-right border-r border-black">TOTAL DISTRIBUSI</td>
                                <td class="p-3 text-right text-base"><?= number_format($print_data['berat_tonase'] * 1000, 0) ?> Kg</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="grid grid-cols-3 gap-4 text-center text-xs mt-12">
                        <div>
                            <p>Penerima / Driver,</p>
                            <div class="h-16"></div>
                            <p class="border-t border-black pt-1 w-32 mx-auto">( ........................ )</p>
                        </div>
                        <div>
                            <p>Pengirim / Petani,</p>
                            <div class="h-16"></div>
                            <p class="border-t border-black pt-1 w-32 mx-auto">( <?= htmlspecialchars($print_data['nama_petani']) ?> )</p>
                        </div>
                        <div>
                            <p>Logistik Terkait,</p>
                            <div class="h-16"></div>
                            <p class="border-t border-black pt-1 w-32 mx-auto">( <?= htmlspecialchars($nama_user) ?> )</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-dashed border-gray-400 flex justify-between items-center no-print">
                        <span class="text-xs text-amber-600 font-medium"><i class="fas fa-info-circle mr-1"></i>Siap dicetak menuju gerbang timbangan gudang.</span>
                        <div class="flex gap-2">
                            <a href="cetak_surat_jalan.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-xl text-xs font-bold transition">Tutup Preview</a>
                            <button onclick="window.print();" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-xl text-xs font-bold shadow transition"><i class="fas fa-print mr-1"></i> Cetak Sekarang</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-6 no-print">
                <h1 class="text-3xl font-black text-white">Manifest Surat Jalan</h1>
                <p class="text-slate-400 mt-1">Daftar muatan komoditas petani yang lolos seleksi mutu dan siap didistribusikan.</p>
            </div>

            <div class="bg-[#1e293b] rounded-[2rem] border border-slate-800 p-6 shadow-xl no-print">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="pb-4">Asal Muatan</th>
                                <th class="pb-4">Komoditas</th>
                                <th class="pb-4">Tonase Keluar</th>
                                <th class="pb-4">Status</th>
                                <th class="pb-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/50">
                            <?php if ($query_layak && mysqli_num_rows($query_layak) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query_layak)): ?>
                                <tr class="text-slate-300 hover:bg-slate-800/30 transition-colors">
                                    <td class="py-4">
                                        <p class="font-bold text-white"><?= htmlspecialchars($row['nama_petani']) ?></p>
                                        <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($row['nama_lahan'] ?? 'Lahan Utama') ?></p>
                                    </td>
                                    <td class="py-4">
                                        <p class="font-medium text-slate-200"><?= htmlspecialchars($row['komoditas']) ?></p>
                                        <p class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($row['varietas'] ?? '-') ?></p>
                                    </td>
                                    <td class="py-4 font-semibold text-white">
                                        <?= number_format($row['berat_tonase'], 3) ?> Ton
                                    </td>
                                    <td class="py-4">
                                        <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-black px-2 py-1 rounded-md uppercase tracking-wider">SIAP KIRIM</span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <a href="cetak_surat_jalan.php?print_id=<?= $row['id'] ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-400 bg-blue-500/10 hover:bg-blue-500 hover:text-white px-3 py-1.5 rounded-xl transition">
                                            <i class="fas fa-file-invoice"></i> Buka Surat Jalan
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-500 text-xs">
                                        <i class="fas fa-folder-open text-3xl mb-3 block text-slate-600"></i> Belum ada data panen berstatus layak. Silakan periksa halaman verifikasi sortir terlebih dahulu.
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