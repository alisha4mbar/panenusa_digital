<?php
ob_start();
session_start();
include 'config.php';

// 1. Logika Autentikasi Sinkron (Session & Cookie)
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id && isset($_COOKIE['panenusa_auth'])) {
    $data = json_decode($_COOKIE['panenusa_auth'], true);
    if (isset($data['user_id'])) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];
        $user_id = $_SESSION['user_id'];
    }
}

// Proteksi Halaman: Jika benar-benar tidak ada akses, tendang ke login
if (!$user_id) {
    header("Location: /login");
    exit();
}

$nama_user = $_SESSION['nama'];
$role_user = $_SESSION['role'];

// 2. Fitur Sinkronisasi Data Lahan (Ambil data statistik)
$query_lahan = $conn->query("SELECT COUNT(*) as jml_lahan, SUM(luas) as total_luas FROM data_lahan WHERE user_id = '$user_id'");
$data_lahan = $query_lahan->fetch_assoc();
$total_luas = $data_lahan['total_luas'] ?? 0;
$jml_lahan = $data_lahan['jml_lahan'] ?? 0;
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
<body class="bg-slate-900 text-white flex min-h-screen">
    
    <aside class="w-64 bg-slate-800 border-r border-slate-700 p-6 flex flex-col hidden md:flex">
        <div class="flex items-center gap-3 mb-10">
            <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <i class="fas fa-leaf text-white text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-emerald-500">Panenusa</h2>
        </div>
        
        <nav class="flex-1 space-y-2">
            <a href="/dashboard" class="flex items-center p-3 bg-emerald-500/10 text-emerald-400 rounded-xl border border-emerald-500/20 transition-all">
                <i class="fas fa-home w-6"></i> <span>Dashboard</span>
            </a>
            <a href="/data-lahan" class="flex items-center p-3 text-slate-400 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all">
                <i class="fas fa-seedling w-6"></i> <span>Data Lahan</span>
            </a>
            <a href="/laporan" class="flex items-center p-3 text-slate-400 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all">
                <i class="fas fa-chart-line w-6"></i> <span>Laporan</span>
            </a>
            <a href="/profil" class="flex items-center p-3 text-slate-400 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all">
                <i class="fas fa-user-circle w-6"></i> <span>Profil Saya</span>
            </a>
        </nav>

        <a href="/auth/logout" class="flex items-center p-3 text-red-400 hover:bg-red-500/10 rounded-xl border border-transparent hover:border-red-500/20 transition-all">
            <i class="fas fa-sign-out-alt w-6"></i> <span>Keluar</span>
        </a>
    </aside>

    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        <div class="max-w-5xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Selamat Datang, <?= htmlspecialchars($nama_user) ?>! 👋</h1>
                    <p class="text-slate-400 mt-1">Pantau dan kelola lahan pertanian Anda dalam satu pintu.</p>
                </div>
                <div class="flex items-center gap-3 bg-slate-800 p-2 pr-4 rounded-2xl border border-slate-700">
                    <div class="w-10 h-10 bg-slate-700 rounded-xl flex items-center justify-center font-bold text-emerald-400">
                        <?= strtoupper(substr($nama_user, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider"><?= htmlspecialchars($role_user) ?></p>
                        <p class="text-sm font-semibold"><?= htmlspecialchars($nama_user) ?></p>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-slate-800 p-6 rounded-3xl border border-slate-700 hover:border-emerald-500/50 transition-all group">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-map-marked-alt text-xl"></i>
                    </div>
                    <p class="text-slate-400 text-sm font-medium">Total Luas Lahan</p>
                    <h3 class="text-3xl font-bold mt-1"><?= number_format($total_luas, 1) ?> <span class="text-sm font-normal text-slate-500">Ha</span></h3>
                </div>

                <div class="bg-slate-800 p-6 rounded-3xl border border-slate-700 hover:border-blue-500/50 transition-all group">
                    <div class="w-12 h-12 bg-blue-500/10 text-blue-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                    <p class="text-slate-400 text-sm font-medium">Jumlah Unit Lahan</p>
                    <h3 class="text-3xl font-bold mt-1"><?= $jml_lahan ?> <span class="text-sm font-normal text-slate-500">Petak</span></h3>
                </div>

                <div class="bg-slate-800 p-6 rounded-3xl border border-slate-700 hover:border-amber-500/50 transition-all group">
                    <div class="w-12 h-12 bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <p class="text-slate-400 text-sm font-medium">Status Akun</p>
                    <h3 class="text-xl font-bold mt-2 text-emerald-400">Aktif & Terverifikasi</h3>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-slate-800 p-6 rounded-3xl border border-slate-700">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg"><i class="fas fa-history mr-2 text-emerald-500"></i> Aktivitas Terbaru</h3>
                        <a href="/data-lahan" class="text-emerald-500 text-xs font-bold hover:underline">LIHAT SEMUA</a>
                    </div>
                    <div class="space-y-4">
                        <p class="text-slate-500 italic text-sm text-center py-10 border-2 border-dashed border-slate-700 rounded-2xl">
                            Belum ada aktivitas terbaru hari ini.
                        </p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 p-8 rounded-3xl shadow-xl relative overflow-hidden group">
                    <i class="fas fa-seedling absolute -right-4 -bottom-4 text-9xl text-white/10 group-hover:rotate-12 transition-transform"></i>
                    <h3 class="text-2xl font-bold mb-2">Tips Pertanian</h3>
                    <p class="text-emerald-50/80 mb-6 leading-relaxed">Jangan lupa untuk memeriksa kelembapan tanah Anda di pagi hari untuk hasil panen yang lebih maksimal.</p>
                    <button class="bg-white text-emerald-700 px-6 py-2 rounded-xl font-bold text-sm shadow-lg hover:bg-emerald-50 transition-all">
                        Pelajari Selengkapnya
                    </button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>