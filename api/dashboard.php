<?php
ob_start();
session_start();
include 'config.php';

// 1. LOGIKA AUTENTIKASI SINKRON
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

// Proteksi Halaman
if (!$user_id) {
    header("Location: /login");
    exit();
}

$nama_user = $_SESSION['nama'];
$role_user = $_SESSION['role'];

// 2. QUERY DATA UNTUK STATISTIK (Sesuai database kamu)
$total_luas = 0;
$jml_lahan = 0;
$query_lahan = $conn->query("SELECT COUNT(*) as jml, SUM(luas) as total FROM data_lahan WHERE user_id = '$user_id'");
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
        <style type="text/tailwindcss">
            .sidebar-item-active { @apply bg-emerald-500/10 text-emerald-400 border-r-4 border-emerald-500; }
        </style>
</head>
<body class="bg-[#0f172a] text-slate-200 flex min-h-screen font-sans">

    <aside class="w-64 bg-[#1e293b] border-r border-slate-800 flex flex-col hidden md:flex">
        <div class="p-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-leaf text-white text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Panenusa</h2>
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

            <a href="konten_edukasi.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-graduation-cap w-5"></i> <span>Edukasi</span>
            </a>

            <?php if ($role_user === 'Admin'): ?>
                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="text-[10px] font-bold text-slate-500 px-4 mb-2 uppercase tracking-widest">Administrator</p>
                    <a href="kelola_user.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-users-gear w-5"></i> <span>Kelola Pengguna</span>
                    </a>
                    <a href="laporan.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-file-contract w-5"></i> <span>Laporan Nasional</span>
                    </a>
                </div>
            <?php endif; ?>
        </nav>

        <div class="p-4 mt-auto">
            <a href="/auth/logout" class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                <i class="fas fa-power-off w-5"></i> <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <div class="bg-[#1e293b]/50 backdrop-blur-md border-b border-slate-800 sticky top-0 z-10 p-4 px-8 flex justify-between items-center">
            <h2 class="font-semibold text-slate-400">Ringkasan Sistem</h2>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-tighter"><?= $role_user ?></p>
                </div>
                <a href="edit_profil.php" class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center border border-slate-600 hover:border-emerald-500 transition-all">
                    <i class="fas fa-user text-slate-300"></i>
                </a>
            </div>
        </div>

        <div class="p-8 max-w-6xl mx-auto">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-white">Halo, <?= explode(' ', $nama_user)[0] ?>!</h1>
                <p class="text-slate-400 mt-2">Berikut adalah update terbaru mengenai lahan dan komunitas Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-gradient-to-br from-emerald-500/20 to-transparent p-6 rounded-[2rem] border border-emerald-500/20">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-ruler-combined text-white text-xl"></i>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm font-semibold uppercase tracking-wider">Total Luas Lahan</p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= number_format($total_luas, 1) ?> <span class="text-lg font-normal text-slate-500">Ha</span></h3>
                </div>

                <div class="bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-seedling text-white text-xl"></i>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm font-semibold uppercase tracking-wider">Jumlah Petak</p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= $jml_lahan ?> <span class="text-lg font-normal text-slate-500">Lokasi</span></h3>
                </div>

                <div class="bg-[#1e293b] p-6 rounded-[2rem] border border-slate-800 relative overflow-hidden group">
                    <i class="fas fa-shield-halved absolute -right-4 -bottom-4 text-7xl text-slate-800 group-hover:text-emerald-500/10 transition-all"></i>
                    <p class="text-slate-400 text-sm font-semibold uppercase tracking-wider">Status Akun</p>
                    <h3 class="text-2xl font-black text-emerald-500 mt-1 uppercase italic tracking-tighter">Verified</h3>
                    <p class="text-[10px] text-slate-500 mt-2">Keamanan akun tingkat tinggi aktif</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-[#1e293b] rounded-[2.5rem] border border-slate-800 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-white">Diskusi Terpopuler</h3>
                        <a href="forum.php" class="text-emerald-500 text-xs font-bold hover:underline tracking-widest">BUKA FORUM</a>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-slate-900/50 p-4 rounded-2xl border border-slate-800">
                            <p class="text-sm text-slate-300">"Bagaimana cara menangani hama wereng di musim hujan?"</p>
                            <div class="flex items-center gap-2 mt-3 text-[10px] text-slate-500">
                                <span class="bg-emerald-500/10 text-emerald-500 px-2 py-0.5 rounded-md font-bold">INFO</span>
                                <span>24 Balasan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-tr from-emerald-600 to-teal-800 rounded-[2.5rem] p-8 relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="bg-white/20 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Tips Hari Ini</span>
                        <h3 class="text-2xl font-bold text-white mt-4 leading-tight">Optimasi Pemupukan dengan Data Cuaca</h3>
                        <p class="text-emerald-100/70 text-sm mt-3 mb-6">Gunakan data BPS Nasional untuk memantau tren produksi di wilayah Anda.</p>
                        <a href="konten_edukasi.php" class="inline-block bg-white text-emerald-700 px-6 py-3 rounded-2xl font-black text-xs hover:scale-105 transition-transform shadow-xl shadow-black/20">
                            MULAI BELAJAR
                        </a>
                    </div>
                    <i class="fas fa-wheat-awn absolute -right-8 -bottom-8 text-[12rem] text-white/10 rotate-12"></i>
                </div>
            </div>
        </div>
    </main>
</body>
</html>