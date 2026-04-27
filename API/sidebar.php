<?php
// Ambil data dari cookie yang dibuat oleh auth.php
$userData = isset($_COOKIE['panenusa_auth']) ? json_decode($_COOKIE['panenusa_auth'], true) : null;

// Jika cookie tidak ada, arahkan ke login (opsional, tergantung proteksi halaman utama)
$role = $userData['role'] ?? 'User';
$nama = $userData['nama'] ?? 'Pengguna';

// Warna identitas (Admin: Ungu, User: Hijau)
$accent = ($role == 'Admin') ? '#6366f1' : '#10b981';
?>

<aside class="w-72 card-glass border-r border-white/5 p-6 hidden lg:flex flex-col">
    <div class="mb-10 px-2">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: <?= ($role == 'Admin') ? 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)' ?>">
                <i class="fas fa-leaf text-lg"></i>
            </div>
            <span class="text-xl font-bold text-white">Panenusa<span style="color: <?= $accent ?>">.pro</span></span>
        </div>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-50 px-1"><?= $role ?> Panel</p>
    </div>

    <nav class="flex-1 overflow-y-auto">
        <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 px-4">Menu Utama</div>
        
        <a href="dashboard.php" class="sidebar-item flex items-center gap-4 p-3.5 text-slate-400 hover:text-white transition">
            <div class="w-5 text-center"><i class="fas fa-chart-pie"></i></div>
            <span class="text-sm">Dashboard</span>
        </a>

        <?php if($role == 'Admin'): ?>
            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest my-4 px-4">Kontrol Admin</div>
            <a href="moderasi_forum.php" class="sidebar-item flex items-center gap-4 p-3.5 text-slate-400 hover:text-white">
                <div class="w-5 text-center"><i class="fas fa-shield-halved"></i></div>
                <span class="text-sm">Moderasi Forum</span>
            </a>
            <a href="kelola_pengguna.php" class="sidebar-item flex items-center gap-4 p-3.5 text-slate-400 hover:text-white">
                <div class="w-5 text-center"><i class="fas fa-users-gear"></i></div>
                <span class="text-sm">Kelola User</span>
            </a>

        <?php else: ?>
            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest my-4 px-4">Fitur Petani</div>
            <a href="peta_panen.php" class="sidebar-item flex items-center gap-4 p-3.5 text-slate-400 hover:text-white">
                <div class="w-5 text-center"><i class="fas fa-map-location-dot"></i></div>
                <span class="text-sm">Peta Pangan</span>
            </a>
            <a href="data_lahan.php" class="sidebar-item flex items-center gap-4 p-3.5 text-slate-400 hover:text-white">
                <div class="w-5 text-center"><i class="fas fa-mountain-sun"></i></div>
                <span class="text-sm">Data Lahan</span>
            </a>
            <a href="forum.php" class="sidebar-item flex items-center gap-4 p-3.5 text-slate-400 hover:text-white">
                <div class="w-5 text-center"><i class="fas fa-comments"></i></div>
                <span class="text-sm">Forum Diskusi</span>
            </a>
        <?php endif; ?>
    </nav>

    <a href="auth.php?action=logout" class="sidebar-item flex items-center gap-4 p-3.5 text-red-400 font-bold mt-auto border-t border-white/5 pt-5">
        <i class="fas fa-power-off"></i> <span>Keluar</span>
    </a>
</aside>