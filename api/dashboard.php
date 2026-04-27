<?php
// File: dashboard.php
session_start();

// Proteksi halaman
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'user';
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pengguna';
$accent = ($role == 'admin') ? '#6366f1' : '#10b981';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Panenusa Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            font-family: 'Inter', sans-serif;
        }
        .card-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-item {
            transition: all 0.3s;
            border-radius: 0.75rem;
        }
        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-80 bg-slate-900/50 backdrop-blur p-6 flex flex-col border-r border-white/10">
        <div class="mb-10">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: <?= $accent ?>">
                    <i class="fas fa-leaf text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Panenusa</h1>
                    <p class="text-xs text-white/50">v2.0</p>
                </div>
            </div>
        </div>
        
        <div class="flex-1">
            <a href="dashboard.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white" style="background: <?= $accent ?>">
                <i class="fas fa-chart-line w-5"></i> Dashboard
            </a>
            
            <?php if($role == 'admin'): ?>
                <a href="kelola_pengguna.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white/70 hover:text-white">
                    <i class="fas fa-users w-5"></i> Kelola Pengguna
                </a>
                <a href="moderasi_forum.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white/70 hover:text-white">
                    <i class="fas fa-shield-alt w-5"></i> Moderasi Forum
                </a>
                <a href="log_api.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white/70 hover:text-white">
                    <i class="fas fa-terminal w-5"></i> API Logs
                </a>
            <?php else: ?>
                <a href="data_lahan.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white/70 hover:text-white">
                    <i class="fas fa-map-marker-alt w-5"></i> Data Lahan
                </a>
                <a href="forum.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white/70 hover:text-white">
                    <i class="fas fa-comments w-5"></i> Forum Diskusi
                </a>
                <a href="peta_panen.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white/70 hover:text-white">
                    <i class="fas fa-map w-5"></i> Peta Panen
                </a>
                <a href="konten_edukasi.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white/70 hover:text-white">
                    <i class="fas fa-graduation-cap w-5"></i> Edukasi
                </a>
                <a href="monitoring_transaksi.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white/70 hover:text-white">
                    <i class="fas fa-receipt w-5"></i> Transaksi
                </a>
            <?php endif; ?>
        </div>
        
        <div class="pt-6 border-t border-white/10">
            <div class="flex items-center gap-3 mb-4 px-3">
                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                    <i class="fas fa-user text-white/70"></i>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm"><?= htmlspecialchars($nama) ?></p>
                    <p class="text-white/40 text-xs"><?= ucfirst($role) ?></p>
                </div>
            </div>
            <a href="edit_profil.php" class="sidebar-item flex items-center gap-3 px-4 py-3 mb-2 text-white/70 hover:text-white">
                <i class="fas fa-user-edit w-5"></i> Edit Profil
            </a>
            <a href="auth.php?action=logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                <i class="fas fa-sign-out-alt w-5"></i> Logout
            </a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="flex-1 overflow-auto p-8">
        <h1 class="text-4xl font-bold text-white mb-4">Selamat Datang, <?= htmlspecialchars($nama) ?>!</h1>
        <p class="text-white/50 mb-8">Anda login sebagai <span style="color: <?= $accent ?>"><?= ucfirst($role) ?></span></p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card-glass rounded-2xl p-6">
                <p class="text-white/50 text-sm">Total Pengguna</p>
                <p class="text-3xl font-bold text-white">1,234</p>
            </div>
            <div class="card-glass rounded-2xl p-6">
                <p class="text-white/50 text-sm">Total Lahan</p>
                <p class="text-3xl font-bold text-white">567</p>
            </div>
            <div class="card-glass rounded-2xl p-6">
                <p class="text-white/50 text-sm">Forum Posts</p>
                <p class="text-3xl font-bold text-white">89</p>
            </div>
        </div>
        
        <div class="card-glass rounded-3xl p-8 text-center">
            <i class="fas fa-check-circle text-6xl text-emerald-500 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">Login Berhasil!</h3>
            <p class="text-white/50">Anda sekarang berada di dashboard Panenusa.</p>
        </div>
    </main>
</body>
</html>