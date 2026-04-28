<?php
session_start();
include 'config.php';

// 1. SINKRONISASI COOKIE 
if (!isset($_SESSION['user_id']) && isset($_COOKIE['panenusa_auth'])) {
    $data = json_decode($_COOKIE['panenusa_auth'], true);
    if (isset($data['user_id'])) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];
    }
}

// 2. PROTEKSI 
if(!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// 3. QUERY 
try {
    if($role == 'Admin') {
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
            
            <a href="/dashboard" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-grid-2 w-5"></i> <span>Dashboard</span>
            </a>
            
            <a href="/data_lahan" class="flex items-center gap-3 p-3 text-emerald-400 bg-emerald-400/5 rounded-xl border border-emerald-400/10">
                <i class="fas fa-map-location-dot w-5"></i> <span>Data Lahan</span>
            </a>

            <a href="/forum" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-comments w-5"></i> <span>Forum Diskusi</span>
            </a>

            <a href="/konten_edukasi" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-graduation-cap w-5"></i> <span>Edukasi</span>
            </a>

            <?php if ($role === 'Admin'): ?>
                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="text-[10px] font-bold text-slate-500 px-4 mb-2 uppercase tracking-widest">Administrator</p>
                    <a href="/kelola_user" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-users-gear w-5"></i> <span>Kelola Pengguna</span>
                    </a>
                    <a href="/laporan" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
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
            <h2 class="font-semibold text-slate-400">Manajemen Lahan</h2>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></p>
                    <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-tighter"><?= $role ?></p>
                </div>
                <a href="/edit_profil" class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center border border-slate-600 hover:border-emerald-500 transition-all">
                    <i class="fas fa-user text-slate-300"></i>
                </a>
            </div>
        </div>

        <div class="p-8 max-w-6xl mx-auto">
            <div class="mb-10 flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-black text-white">Data Lahan</h1>
                    <p class="text-slate-400 mt-2">Kelola dan pantau produktivitas lahan Anda.</p>
                </div>
                <?php if($role != 'Admin'): ?>
                <button class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-bold text-sm transition shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-plus mr-2"></i> Tambah Lahan
                </button>
                <?php endif; ?>
            </div>

            <div class="bg-[#1e293b] rounded-[2rem] border border-slate-800 overflow-hidden">
                <div class="p-6 border-b border-slate-800">
                    <h3 class="font-bold text-white">Daftar Plot Lahan Aktif</h3>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="p-6 text-[10px] font-black uppercase text-slate-500 tracking-widest">Lokasi</th>
                            <th class="p-6 text-[10px] font-black uppercase text-slate-500 tracking-widest">Luas (Ha)</th>
                            <th class="p-6 text-[10px] font-black uppercase text-slate-500 tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-800/30 transition">
                                    <td class="p-6 text-sm text-slate-300"><?= htmlspecialchars($row['lokasi']) ?></td>
                                    <td class="p-6 text-sm font-mono text-white"><?= number_format($row['luas'], 1) ?></td>
                                    <td class="p-6">
                                        <span class="text-emerald-400 font-bold text-xs uppercase bg-emerald-500/10 px-3 py-1 rounded-lg"><?= htmlspecialchars($row['status_lahan']) ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="p-10 text-center text-slate-500">Belum ada data lahan tersedia.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>