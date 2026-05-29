<?php
ob_start();

// 1. MUAT CONFIG TERLEBIH DAHULU (Menyiapkan session_start dan variabel $conn)
require_once __DIR__ . '/config.php';

// 2. PASTIKAN VARIABEL $conn BERHASIL DIINISIALISASI
if (!isset($conn)) {
    die("Eror: Variabel koneksi database \$conn tidak ditemukan. Periksa kembali api/config.php Anda.");
}

// 3. PROTEKSI HALAMAN (Dilakukan setelah session diaktifkan oleh config.php)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'User';

try {
    if ($role == 'Admin') {
        $query = "SELECT data_lahan.*, users.nama as pemilik FROM data_lahan JOIN users ON data_lahan.user_id = users.id ORDER BY data_lahan.created_at DESC";
    } else {
        $query = "SELECT * FROM data_lahan WHERE user_id = '$user_id' ORDER BY created_at DESC";
    }
    $result = mysqli_query($conn, $query);
} catch (Exception $e) {
    $result = false;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Lahan | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#0f172a] text-slate-200 flex min-h-screen font-sans">

    <aside class="w-64 bg-[#1e293b] border-r border-slate-800 flex flex-col hidden md:flex">
        <div class="p-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-leaf text-white text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Panenusa</h2>
            </div>
        </div>
        
        <nav class="flex-1 px-4 space-y-1">
            <p class="text-[10px] font-bold text-slate-500 px-4 mb-2 uppercase tracking-widest">Menu Utama</p>
            <a href="dashboard.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-grid-2 w-5"></i> <span>Dashboard</span>
            </a>
            <a href="data_lahan.php" class="flex items-center gap-3 p-3 text-emerald-400 bg-emerald-400/5 rounded-xl border border-emerald-400/10">
                <i class="fas fa-map-location-dot w-5"></i> <span>Data Lahan</span>
            </a>
            <a href="forum.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-comments w-5"></i> <span>Forum Diskusi</span>
            </a>
        </nav>

        <div class="p-4 mt-auto">
            <a href="auth.php?action=logout" class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                <i class="fas fa-power-off w-5"></i> <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <h1 class="text-4xl font-black text-white mb-6">Daftar Plot Lahan Aktif</h1>
        <div class="bg-[#1e293b] rounded-[2rem] border border-slate-800 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-800/50">
                    <tr>
                        <th class="p-6 text-xs text-slate-500 uppercase">Lokasi</th>
                        <th class="p-6 text-xs text-slate-500 uppercase">Luas (Ha)</th>
                        <th class="p-6 text-xs text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="p-6 text-sm text-slate-300"><?= htmlspecialchars($row['lokasi']) ?></td>
                                <td class="p-6 text-sm text-white"><?= number_format($row['luas'], 1) ?></td>
                                <td class="p-6"><span class="text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-lg text-xs font-bold"><?= htmlspecialchars($row['status_lahan']) ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="p-10 text-center text-slate-500">Belum ada data lahan tersedia.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>