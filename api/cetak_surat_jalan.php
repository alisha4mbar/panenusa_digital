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

// 🌟 FAKE DATA MANIFEST SIAP KIRIM
$fake_manifest = [
    'fake_sj_1' => [
        'id' => '201',
        'nama_petani' => 'Ahmad Subarjo',
        'nama_lahan' => 'Lahan Karanganyar Blok B',
        'komoditas' => 'Padi',
        'varietas' => 'Ciherang',
        'berat_tonase' => 3.250,
        'status' => 'layak',
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
    ],
    'fake_sj_2' => [
        'id' => '202',
        'nama_petani' => 'Siti Aminah',
        'nama_lahan' => 'Lahan Petak Induk Ngawi',
        'komoditas' => 'Kedelai',
        'varietas' => 'Anjasmoro',
        'berat_tonase' => 1.800,
        'status' => 'layak',
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
    ],
    'fake_sj_3' => [
        'id' => '203',
        'nama_petani' => 'Budi Santoso',
        'nama_lahan' => 'Ladang Timur Madiun',
        'komoditas' => 'Jagung',
        'varietas' => 'Pioneer P35',
        'berat_tonase' => 5.400,
        'status' => 'layak',
        'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
    ]
];

// Ambil ID detail untuk mode siap cetak
$print_data = null;
if (isset($_GET['print_id'])) {
    $print_id = mysqli_real_escape_string($conn, $_GET['print_id']);
    
    // Jika data yang dipilih adalah Fake Data
    if (array_key_with_prefix_exists($print_id, $fake_manifest) || isset($fake_manifest[$print_id])) {
        $print_data = $fake_manifest[$print_id];
    } else {
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
}

// Ambil Data Asli DB
$sql_list = "SELECT tp.*, u.nama as nama_petani, l.nama_lahan 
             FROM transaksi_panen tp 
             JOIN users u ON tp.petani_id = u.id 
             LEFT JOIN lahan l ON tp.lahan_id = l.id 
             WHERE tp.status = 'layak' 
             ORDER BY tp.created_at DESC";
$query_layak = mysqli_query($conn, $sql_list);

$db_list = [];
if ($query_layak) {
    while ($row = mysqli_fetch_assoc($query_layak)) {
        $db_list[] = $row;
    }
}

// Gabungkan manifest asli database dengan data simulasi biar ramai
$final_manifest = array_merge($db_list, array_values($fake_manifest));
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
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            body { background: white; color: black; }
            .no-print { display: none !important; }
            .print-area { display: block !important; margin: 0; padding: 20px; width: 100%; }
            .print-card { background: white !important; border: none !important; color: black !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-[#0b1224] text-slate-200 flex min-h-screen font-sans">

    <aside class="w-64 bg-[#111c35] border-r border-slate-800/60 flex flex-col hidden md:flex no-print">
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
            <a href="dashboard_admin.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>
            <a href="verifikasi_panen.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all">
                <i class="fas fa-clipboard-check"></i> <span>Verifikasi Panen</span>
            </a>
            <a href="cetak_surat_jalan.php" class="flex items-center gap-3 px-4 py-3 text-white bg-gradient-to-r from-emerald-500/20 to-emerald-500/5 rounded-xl border border-emerald-500/20 font-semibold shadow-inner transition-all">
                <i class="fas fa-file-invoice text-emerald-400"></i> <span>Cetak Surat Jalan</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto print-area">
        <div class="p-4 px-8 bg-[#111c35]/60 border-b border-slate-800/50 flex justify-between items-center no-print">
            <h2 class="font-semibold text-slate-400">Cetak Surat Jalan Distribusi (FR-PANEN-05)</h2>
        </div>

        <div class="p-8 max-w-4xl mx-auto space-y-8">
            
            <?php if ($print_data): ?>
                <div class="bg-white text-black p-8 rounded-3xl shadow-2xl print-card border border-gray-200">
                    <div class="flex justify-between items-start border-b-2 border-black pb-4 mb-4">
                        <div>
                            <h2 class="text-xl font-black">PT. PANENUSA LOGISTIK INDONESIA</h2>
                            <p class="text-xs text-gray-500">Sistem Kliring Distribusi Pangan Nasional</p>
                        </div>
                        <div class="text-right">
                            <span class="bg-black text-white px-3 py-1 text-xs font-bold rounded">DOKUMEN SURAT JALAN</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-xs mb-6">
                        <div>
                            <p class="font-bold text-gray-500">PENGIRIM / MITRA:</p>
                            <p class="font-extrabold text-sm"><?= htmlspecialchars($print_data['nama_petani']) ?></p>
                            <p class="text-gray-600"><?= htmlspecialchars($print_data['nama_lahan']) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-500">TANGGAL KLIRING:</p>
                            <p class="font-extrabold text-sm"><?= date('d M Y') ?></p>
                        </div>
                    </div>
                    <table class="w-full text-left border-collapse border border-black text-xs mb-6">
                        <thead>
                            <tr class="bg-gray-100 font-bold border-b border-black">
                                <th class="p-2 border-r border-black">Komoditas Raya</th>
                                <th class="p-2 border-r border-black">Varietas</th>
                                <th class="p-2 text-right">Tonase Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-black">
                                <td class="p-2 border-r border-black font-bold"><?= htmlspecialchars($print_data['komoditas']) ?></td>
                                <td class="p-2 border-r border-black"><?= htmlspecialchars($print_data['varietas'] ?? 'Unggul') ?></td>
                                <td class="p-2 text-right font-bold"><?= number_format($print_data['berat_tonase'], 3) ?> Ton</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="flex justify-end gap-2 no-print">
                        <a href="cetak_surat_jalan.php" class="bg-gray-200 px-4 py-2 rounded-xl text-xs font-bold text-gray-800">Tutup</a>
                        <button onclick="window.print();" class="bg-emerald-500 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md"><i class="fas fa-print mr-1"></i> Cetak Dokumen</button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="no-print">
                <h1 class="text-3xl font-black text-white tracking-tight">Manifest Surat Jalan</h1>
                <p class="text-slate-400 text-sm mt-1">Daftar muatan komoditas petani berstatus layak yang siap diterbitkan nota jalurnya.</p>
            </div>

            <div class="bg-[#111c35] rounded-[2rem] border border-slate-800/80 shadow-2xl overflow-hidden no-print">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#162544]/60 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-800/60">
                                <th class="p-4 pl-6">Asal Muatan</th>
                                <th class="p-4">Komoditas</th>
                                <th class="p-4">Tonase Keluar</th>
                                <th class="p-4 text-right pr-6">Aksi Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/40">
                            <?php foreach ($final_manifest as $index => $row): ?>
                            <?php 
                                // Bikin ID unik untuk fake data di parameter URL
                                $row_id = isset($row['id']) && is_numeric($row['id']) ? $row['id'] : 'fake_sj_' . ($index - count($db_list) + 1);
                            ?>
                            <tr class="text-slate-300 hover:bg-[#162544]/30 transition">
                                <td class="p-4 pl-6">
                                    <p class="font-bold text-white"><?= htmlspecialchars($row['nama_petani']) ?></p>
                                    <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($row['nama_lahan']) ?></p>
                                </td>
                                <td class="p-4">
                                    <p class="font-medium text-slate-200"><?= htmlspecialchars($row['komoditas']) ?></p>
                                </td>
                                <td class="p-4 font-semibold text-emerald-400"><?= number_format($row['berat_tonase'], 3) ?> Ton</td>
                                <td class="p-4 text-right pr-6">
                                    <a href="cetak_surat_jalan.php?print_id=<?= $row_id ?>" class="inline-flex items-center gap-1 text-xs font-bold text-blue-400 bg-blue-500/10 px-3 py-1.5 rounded-xl hover:bg-blue-500 hover:text-white transition">
                                        <i class="fas fa-file-invoice"></i> Buka Dokumen
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
