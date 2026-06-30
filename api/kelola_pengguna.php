<?php
ob_start();
session_start();
require_once __DIR__ . '/config.php';

// Proteksi: Ambil state data user dari Session/Cookie Vercel
$userData = requireLogin();
$user_id   = $userData['id'];
$nama_user = $userData['nama'];
$role_user = $userData['role'];

$role_check = strtolower($role_user);

// Sinkronisasi Proteksi: Jika bukan admin logistik, tendang kembali ke gerbang login resmi
if ($role_check !== 'admin') {
    header("Location: login.php");
    exit;
}

$accent = '#10b981'; // Ubah jadi Emerald Green agar senada dengan UI Panenusa Logistik
$gradient = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';

// ==========================================
// 📊 AMBIL DATA ASLI DARI DATABASE
// ==========================================
$sql_users = "SELECT id, nama, email, role FROM users ORDER BY role ASC, nama ASC";
$query_users = mysqli_query($conn, $sql_users);

$users_list = [];
if ($query_users) {
    while ($row = mysqli_fetch_assoc($query_users)) {
        $users_list[] = [
            'id' => $row['id'],
            'nama' => $row['nama'],
            'email' => $row['email'],
            'role' => strtolower($row['role']) === 'admin' ? 'Admin' : (strtolower($row['role']) === 'supplier' ? 'Pemilik Lahan' : 'Petani'),
            'status' => 'Aktif'
        ];
    }
}

// Simulasi Data Cadangan Tambahan jika database sedang kosong (Agar tetap ramai saat demo)
if (empty($users_list)) {
    $users_list = [
        ['id' => 1, 'nama' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'role' => 'Petani', 'status' => 'Aktif'],
        ['id' => 2, 'nama' => $nama_user . ' (Anda)', 'email' => 'admin@panenusa.pro', 'role' => 'Admin', 'status' => 'Aktif'],
        ['id' => 3, 'nama' => 'Siti Aminah', 'email' => 'siti@outlook.com', 'role' => 'Pemilik Lahan', 'status' => 'Aktif'],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna | Panenusa Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { background-color: #0b1224; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 28, 53, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .sidebar-item { transition: all 0.2s ease; border-radius: 12px; margin-bottom: 4px; color: #94a3b8; }
        .sidebar-active { background: rgba(16, 185, 129, 0.1); color: <?= $accent ?> !important; font-weight: 700; border-left: 4px solid <?= $accent ?>; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#111c35] border-r border-slate-800/60 flex flex-col hidden md:flex z-20 p-6">
        <div class="mb-10 px-2 text-xl font-bold tracking-tighter text-white">
            Panenusa<span style="color: <?= $accent ?>">.Core</span>
        </div>

        <nav class="flex-1 space-y-1.5 overflow-y-auto">
            <p class="text-[10px] font-bold text-slate-500 px-3 mb-2 uppercase tracking-widest">Menu Utama</p>
            <a href="dashboard_admin.php" class="sidebar-item flex items-center gap-4 p-3.5 hover:bg-slate-800/40 hover:text-white">
                <div class="w-5 text-center"><i class="fas fa-th-large text-sm"></i></div>
                <span class="text-sm">Dashboard</span>
            </a>
            <a href="verifikasi_panen.php" class="sidebar-item flex items-center gap-4 p-3.5 hover:bg-slate-800/40 hover:text-white">
                <div class="w-5 text-center"><i class="fas fa-clipboard-check text-sm"></i></div>
                <span class="text-sm">Verifikasi Panen</span>
            </a>
            <a href="cetak_surat_jalan.php" class="sidebar-item flex items-center gap-4 p-3.5 hover:bg-slate-800/40 hover:text-white">
                <div class="w-5 text-center"><i class="fas fa-file-invoice text-sm"></i></div>
                <span class="text-sm">Cetak Surat Jalan</span>
            </a>
            <div class="pt-6 mt-4 border-t border-slate-800/50">
                <p class="text-[10px] font-bold text-slate-500 px-3 mb-2 uppercase tracking-widest">Administrator</p>
                <a href="kelola_pengguna.php" class="sidebar-item sidebar-active flex items-center gap-4 p-3.5">
                    <div class="w-5 text-center"><i class="fas fa-users text-sm"></i></div>
                    <span class="text-sm">Kelola Pengguna</span>
                </a>
                <a href="data_referensi.php" class="sidebar-item flex items-center gap-4 p-3.5 hover:bg-slate-800/40 hover:text-white">
                    <div class="w-5 text-center"><i class="fas fa-database text-sm"></i></div>
                    <span class="text-sm">Data Referensi</span>
                </a>
            </div>
        </nav>

        <div class="p-2 border-t border-slate-800/50">
            <a href="auth.php?action=logout" class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl font-medium transition-all">
                <i class="fas fa-power-off"></i> <span class="text-sm">Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-6 lg:p-12">
        <header class="mb-12 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Database Pengguna</h1>
                <p class="text-slate-500 text-sm mt-1">Manajemen akun kredensial, hak akses, dan lisensi aktor ekosistem.</p>
            </div>
            <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold text-sm transition shadow-lg shadow-emerald-500/20">
                <i class="fas fa-plus mr-2"></i> Tambah Aktor Baru
            </button>
        </header>

        <div class="card-glass rounded-[2.5rem] overflow-hidden shadow-2xl">
            <table class="w-full text-left">
                <thead class="bg-white/5 border-b border-white/5">
                    <tr>
                        <th class="p-6 text-xs font-bold uppercase text-slate-400 tracking-widest pl-8">Nama & Email</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-400 tracking-widest">Otoritas Peran</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-400 tracking-widest">Status Konfigurasi</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-400 tracking-widest text-center pr-8">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach($users_list as $user): ?>
                    <tr class="hover:bg-white/[0.02] transition text-slate-300">
                        <td class="p-6 pl-8">
                            <div class="font-bold text-white text-base"><?= htmlspecialchars($user['nama']) ?></div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5"><?= htmlspecialchars($user['email']) ?></div>
                        </td>
                        <td class="p-6">
                            <?php if($user['role'] == 'Admin'): ?>
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-blue-500/10 text-blue-400 border border-blue-500/20">ADMIN LOGISTIK</span>
                            <?php elseif($user['role'] == 'Pemilik Lahan'): ?>
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-purple-500/10 text-purple-400 border border-purple-500/20">PEMILIK LAHAN</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">PETANI</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-6">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-sm shadow-emerald-400"></div>
                                <span class="text-sm text-slate-300"><?= $user['status'] ?></span>
                            </div>
                        </td>
                        <td class="p-6 pr-8">
                            <div class="flex justify-center gap-2">
                                <button class="w-9 h-9 rounded-xl bg-slate-800 text-slate-400 hover:bg-emerald-500 hover:text-white border border-slate-700/50 transition flex items-center justify-center">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button class="w-9 h-9 rounded-xl bg-slate-800 text-slate-400 hover:bg-red-500 hover:text-white border border-slate-700/50 transition flex items-center justify-center">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
