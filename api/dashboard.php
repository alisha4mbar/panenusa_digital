<?php
ob_start();
// Mengarah langsung ke config.php yang berada di folder yang sama (api/)
require_once __DIR__ . '/config.php';

// CUKUP PANGGIL FUNGSI INI: Mengamankan halaman berbasis Cookie + Session Vercel Serverless State
$userData = requireLogin();

// Ambil data user yang aman dari fungsi requireLogin()
$user_id   = $userData['id'];
$nama_user = $userData['nama'];
$role_user = $userData['role'];

// Normalisasi huruf untuk pencocokan database (karena registrasi menggunakan huruf kecil)
$role_check = strtolower($role_user);

// 🚀 JIKA NAMA KOLOM LUAS DI DATABASE KAMU BERBEDA (Misal: luas_lahan), 
// Kamu cukup ganti teks di dalam tanda kutip di bawah ini saja:
$kolom_luas = "luas"; 

if ($role_check === 'admin') {
    // Memastikan query menggunakan nama kolom yang didefinisikan di atas
    $sql = "SELECT COUNT(*) as jml, SUM($kolom_luas) as total FROM lahan";
    $query_lahan = mysqli_query($conn, $sql);
    $label_stat = "Total Lahan Nasional";
} else {
    $sql = "SELECT COUNT(*) as jml, SUM($kolom_luas) as total FROM lahan WHERE user_id = '$user_id'";
    $query_lahan = mysqli_query($conn, $sql);
    $label_stat = "Total Luas Lahan Anda";
}

$total_luas = 0;
$jml_lahan = 0;

if ($query_lahan && $row = mysqli_fetch_assoc($query_lahan)) {
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
                <i class="fas fa-th-large w-5"></i> <span>Dashboard</span>
            </a>
            
            <a href="data_lahan.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-map-location-dot w-5"></i> <span>Data Lahan</span>
            </a>

            <a href="forum.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-comments w-5"></i> <span>Forum Diskusi</span>
            </a>

            <?php if ($role_check === 'admin'): ?>
                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="text-[10px] font-bold text-slate-500 px-4 mb-2 uppercase tracking-widest">Administrator</p>
                    <a href="kelola_pengguna.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-users-gear w-5"></i> <span>Kelola Pengguna</span>
                    </a>
                </div>
            <?php endif; ?>
        </nav>

        <div class="p-4 mt-auto">
            <a href="auth.php?action=logout" class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                <i class="fas fa-power-off w-5"></i> <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <div class="bg-[#1e293b]/50 backdrop-blur-md border-b border-slate-800 sticky top-0 z-10 p-4 px-8 flex justify-between items-center">
            <h2 class="font-semibold text-slate-400">Ringkasan Sistem (<?= ucfirst($role_user) ?>)</h2>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-[10px] text-emerald-500 font-bold uppercase"><?= ucfirst($role_user) ?></p>
                </div>
                <a href="edit_profil.php" class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center border border-slate-600">
                    <i class="fas fa-user text-slate-300"></i>
                </a>
            </div>
        </div>

        <div class="p-8 max-w-6xl mx-auto">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-white">Halo, <?= htmlspecialchars(explode(' ', $nama_user)[0]) ?>!</h1>
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
                    <h3 class="text-2xl font-black text-emerald-500 mt-1 uppercase italic"><?= ucfirst($role_user) ?></h3>
                </div>
            </div>
        </div>
    </main>
</body>
</html>