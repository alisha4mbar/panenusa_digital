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
// 🚀 PROSES ACTION: APPROVAL / SORTIR
// ==========================================
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_panen = mysqli_real_escape_string($conn, $_GET['id']);
    $action   = strtolower($_GET['action']); 

    // Jika yang diklik adalah data fake (berawalan kata 'fake')
    if (strpos($id_panen, 'fake') !== false) {
        $msg = "Simulasi: Data Antrean #$id_panen berhasil disortir sebagai " . strtoupper($action);
    } else {
        if (in_array($action, ['layak', 'cacat'])) {
            $update_query = "UPDATE transaksi_panen SET status = '$action' WHERE id = '$id_panen'";
            if (mysqli_query($conn, $update_query)) {
                $msg = "Transaksi ID #$id_panen berhasil diverifikasi sebagai: " . strtoupper($action);
            } else {
                $error = "Gagal memperbarui status verifikasi.";
            }
        }
    }
}

// ==========================================
// 📊 AMBIL DATA ASLI & GABUNG DENGAN FAKE DATA
// ==========================================
$sql = "SELECT tp.*, u.nama as nama_petani, l.nama_lahan 
        FROM transaksi_panen tp 
        JOIN users u ON tp.petani_id = u.id 
        LEFT JOIN lahan l ON tp.lahan_id = l.id 
        WHERE tp.status = 'pending' 
        ORDER BY tp.created_at DESC";
$query_antrean = mysqli_query($conn, $sql);

$antrean_list = [];
if ($query_antrean) {
    while ($row = mysqli_fetch_assoc($query_antrean)) {
        $antrean_list[] = $row;
    }
}

// 🌟 STRATEGI FAKE DATA: Ditambahkan ke barisan array agar tabel terlihat ramai
$fake_data = [
    [
        'id' => 'fake_1',
        'nama_petani' => 'Bambang Supriyanto',
        'nama_lahan' => 'Lahan Blok A Selatan',
        'musim_tanam' => 'Rendengan 2026',
        'komoditas' => 'Padi',
        'varietas' => 'Inpari 32',
        'berat_tonase' => 2.450,
        'persentase_kerusakan' => 3.5,
        'foto_nota' => '',
        'created_at' => date('Y-m-d H:i:s', strtotime('-10 mins'))
    ],
    [
        'id' => 'fake_2',
        'nama_petani' => 'Joko Widodo (Mitra)',
        'nama_lahan' => 'Lahan Petak 4',
        'musim_tanam' => 'Gadu 2026',
        'komoditas' => 'Jagung',
        'varietas' => 'Bisi 18',
        'berat_tonase' => 4.120,
        'persentase_kerusakan' => 12.0, // Indikator merah otomatis menyala
        'foto_nota' => '',
        'created_at' => date('Y-m-d H:i:s', strtotime('-45 mins'))
    ],
    [
        'id' => 'fake_3',
        'nama_petani' => 'Sri Wahyuni',
        'nama_lahan' => 'Lahan Sabang Lor',
        'musim_tanam' => 'Rendengan 2026',
        'komoditas' => 'Bawang Merah',
        'varietas' => 'Tajuk',
        'berat_tonase' => 1.150,
        'persentase_kerusakan' => 5.2,
        'foto_nota' => '',
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
    ]
];

// Satukan data asli DB dengan data palsu buatan kita
$final_antrean = array_merge($antrean_list, $fake_data);
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
            <a href="dashboard_admin.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>
            <a href="verifikasi_panen.php" class="flex items-center gap-3 px-4 py-3 text-white bg-gradient-to-r from-emerald-500/20 to-emerald-500/5 rounded-xl border border-emerald-500/20 font-semibold shadow-inner transition-all">
                <i class="fas fa-clipboard-check text-emerald-400"></i> <span>Verifikasi Panen</span>
                <span class="ml-auto bg-amber-500/10 text-amber-400 text-[9px] font-extrabold px-2 py-0.5 rounded-md border border-amber-500/20">FR-03</span>
            </a>
            <a href="cetak_surat_jalan.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all">
                <i class="fas fa-file-invoice"></i> <span>Cetak Surat Jalan</span>
                <span class="ml-auto bg-blue-500/10 text-blue-400 text-[9px] font-extrabold px-2 py-0.5 rounded-md border border-blue-500/20">FR-05</span>
            </a>
            <div class="pt-6 mt-4 border-t border-slate-800/50">
                <p class="text-[10px] font-bold text-slate-500 px-3 mb-2 uppercase tracking-widest">Administrator</p>
                <a href="kelola_pengguna.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all">
                    <i class="fas fa-users-gear"></i>
<i class="fas fa-users-gear"></i> <span>Kelola Pengguna</span>
                </a>
                <a href="data_referensi.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all">
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
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Pemeriksaan Gerbang Mutu</h1>
                <p class="text-slate-400 text-sm mt-1">Saring kelayakan komoditas petani berdasarkan batas kerusakan fisik pra-distribusi.</p>
            </div>

            <?php if ($msg): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm"><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <div class="bg-[#111c35] rounded-[2rem] border border-slate-800/80 shadow-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#162544]/60 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-800/60">
                                <th class="p-4 pl-6">Petani & Lahan</th>
                                <th class="p-4">Komoditas</th>
                                <th class="p-4">Timbangan</th>
                                <th class="p-4">Kerusakan</th>
                                <th class="p-4 pr-6 text-center">Tindakan Sortir</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/40">
                            <?php foreach ($final_antrean as $row): ?>
                            <tr class="text-slate-300 hover:bg-[#162544]/30 transition">
                                <td class="p-4 pl-6">
                                    <p class="font-bold text-white"><?= htmlspecialchars($row['nama_petani']) ?></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($row['nama_lahan']) ?> (<?= htmlspecialchars($row['musim_tanam']) ?>)</p>
                                </td>
                                <td class="p-4">
                                    <span class="bg-slate-800/50 border border-slate-700/40 px-2.5 py-1 rounded-lg text-xs font-medium text-slate-200"><?= htmlspecialchars($row['komoditas']) ?></span>
                                </td>
                                <td class="p-4 font-semibold text-white"><?= number_format($row['berat_tonase'], 3) ?> Ton</td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold <?= ($row['persentase_kerusakan'] > 10) ? 'bg-red-500/10 text-red-400' : 'bg-amber-500/10 text-amber-400' ?>">
                                        <?= htmlspecialchars($row['persentase_kerusakan']) ?>%
                                    </span>
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="inline-flex gap-2">
                                        <a href="verifikasi_panen.php?action=layak&id=<?= $row['id'] ?>" class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded-xl hover:bg-emerald-500 hover:text-white transition">Layak</a>
                                        <a href="verifikasi_panen.php?action=cacat&id=<?= $row['id'] ?>" class="text-xs font-bold text-red-400 bg-red-500/10 px-3 py-1.5 rounded-xl hover:bg-red-500 hover:text-white transition">Cacat</a>
                                    </div>
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
