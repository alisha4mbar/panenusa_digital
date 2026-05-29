<?php
ob_start();
session_start();

// 1. KONEKSI KE DATABASE (Gunakan __DIR__ agar tidak Forbidden di Vercel)
require_once(__DIR__ . '/config.php');

// 2. LOGIKA AUTENTIKASI SINKRON
$user_id = $_SESSION['user_id'] ?? null;

// Jika session kosong, coba ambil dari cookie
if (!$user_id && isset($_COOKIE['panenusa_auth'])) {
    $data = json_decode($_COOKIE['panenusa_auth'], true);
    if (isset($data['user_id'])) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];
        $user_id = $_SESSION['user_id'];
    }
}

// Proteksi Halaman: Jika belum login, redirect ke login.php (BUKAN folder /login)
if (!$user_id) {
    header("Location: login.php");
    exit();
}

$nama_user = $_SESSION['nama'];
$role_user = $_SESSION['role'];

// 3. QUERY DATA STATISTIK BERDASARKAN ROLE
if ($role_user === 'Admin') {
    // Admin melihat semua data
    $sql = "SELECT COUNT(*) as jml, SUM(luas) as total FROM data_lahan";
    $query_lahan = $conn->query($sql);
    $label_stat = "Total Lahan Nasional";
} else {
    // User hanya melihat miliknya sendiri (Gunakan user_id yang valid)
    $sql = "SELECT COUNT(*) as jml, SUM(luas) as total FROM data_lahan WHERE user_id = '$user_id'";
    $query_lahan = $conn->query($sql);
    $label_stat = "Total Luas Lahan Anda";
}

$total_luas = 0;
$jml_lahan = 0;
if ($query_lahan) {
    $row = $query_lahan->fetch_assoc();
    $total_luas = $row['total'] ?? 0;
    $jml_lahan = $row['jml'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Panenusa</title>
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
            
            <a href="dashboard.php" class="flex items-center gap-3 p-3 text-emerald-400 bg-emerald-400/5 rounded-xl border border-emerald-400/10">
                <i class="fas fa-grid-2 w-5"></i> <span>Dashboard</span>
            </a>
            
            <a href="data_lahan.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-map-location-dot w-5"></i> <span>Data Lahan</span>
            </a>

            <a href="forum.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-comments w-5"></i> <span>Forum Diskusi</span>
            </a>

            <?php if ($role_user === 'Admin'): ?>
                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="text-[10px] font-bold text-slate-500 px-4 mb-2 uppercase tracking-widest">Administrator</p>
                    <a href="kelola_user.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-users-gear w-5"></i> <span>Kelola Pengguna</span>
                    </a>
                    <a href="laporan_nasional.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-chart-line w-5"></i> <span>Laporan Nasional</span>
                    </a>
                </div>
            <?php endif; ?>
        </nav>

        <div class="p-4 mt-auto">
            <a href="logout.php" class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                <i class="fas fa-power-off w-5"></i> <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <div class="bg-[#1e293b]/50 backdrop-blur-md border-b border-slate-800 sticky top-0 z-10 p-4 px-8 flex justify-between items-center">
            <h2 class="font-semibold text-slate-400">Ringkasan Sistem (<?= $role_user ?>)</h2>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-[10px] text-emerald-500 font-bold uppercase"><?= $role_user ?></p>
                </div>
                <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center border border-slate-600">
                    <i class="fas fa-user text-slate-300"></i>
                </div>
            </div>
        </div>

        <div class="p-8 max-w-6xl mx-auto">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-white">Halo, <?= explode(' ', $nama_user)[0] ?>!</h1>
                <p class="text-slate-400 mt-2">Selamat datang kembali di sistem kendali Panenusa.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-gradient-to-br from-emerald-500/10 to-transparent p-6 rounded-[2rem] border border-emerald-500/20">
                    <p class="text-slate-400 text-sm font-semibold uppercase tracking-wider"><?= $label_stat ?></p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= number_format($total_luas, 1) ?> <span class="text-lg font-normal text-slate-500">Ha</span></h3>
                </div>

                <div class="bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800">
                    <p class="text-slate-400 text-sm font-semibold uppercase tracking-wider">Jumlah Petak/Lokasi</p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= $jml_lahan ?> <span class="text-lg font-normal text-slate-500">Lokasi</span></h3>
                </div>

                <div class="bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800">
                    <p class="text-slate-400 text-sm font-semibold uppercase tracking-wider">Status Akses</p>
                    <h3 class="text-2xl font-black text-emerald-500 mt-1 uppercase italic"><?= $role_user ?></h3>
                </div>
            </div>

            <div class="bg-[#1e293b] rounded-[2.5rem] border border-slate-800 p-8 mb-8">
                <h3 class="text-xl font-bold text-white mb-6"><i class="fas fa-trophy text-amber-400 mr-2"></i> Peringkat Produksi Nasional</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 bg-slate-900/50 rounded-2xl border border-slate-700">
                        <p class="text-xs text-slate-500 font-bold">WILAYAH 1</p>
                        <p class="text-lg font-bold text-white">Jawa Timur</p>
                        <p class="text-emerald-500 font-black">9,5 Juta Ton</p>
                    </div>
                    <div class="p-4 bg-slate-900/50 rounded-2xl border border-slate-700">
                        <p class="text-xs text-slate-500 font-bold">WILAYAH 2</p>
                        <p class="text-lg font-bold text-white">Jawa Tengah</p>
                        <p class="text-emerald-500 font-black">9,2 Juta Ton</p>
                    </div>
                    <div class="p-4 bg-slate-900/50 rounded-2xl border border-slate-700">
                        <p class="text-xs text-slate-500 font-bold">WILAYAH 3</p>
                        <p class="text-lg font-bold text-white">Jawa Barat</p>
                        <p class="text-emerald-500 font-black">9,1 Juta Ton</p>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-slate-800 flex justify-between items-center">
                    <p class="text-sm text-slate-400">Hubungkan dengan portal data resmi</p>
                    <a href="https://www.bps.go.id/id/statistics-any/padi" target="_blank" class="text-emerald-500 text-sm font-bold hover:underline">KONEKSI BPS <i class="fas fa-external-link-alt ml-1"></i></a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-[#1e293b] rounded-[2.5rem] border border-slate-800 p-8">
                    <h3 class="text-xl font-bold text-white mb-6">Diskusi Forum</h3>
                    <div class="space-y-4">
                        <div class="bg-slate-900/50 p-4 rounded-2xl border border-slate-800">
                            <p class="text-sm text-slate-300">"Optimasi pupuk organik untuk tanah subur..."</p>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-600 rounded-[2.5rem] p-8 relative overflow-hidden">
                    <h3 class="text-2xl font-bold text-white leading-tight">Edukasi Tani</h3>
                    <p class="text-emerald-100/70 text-sm mt-3 mb-6">Pelajari teknik irigasi terbaru di modul edukasi.</p>
                    <a href="edukasi.php" class="inline-block bg-white text-emerald-700 px-6 py-3 rounded-2xl font-black text-xs hover:scale-105 transition-transform">MULAI BELAJAR</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>