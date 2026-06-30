<?php
session_start();
include 'config.php';

// Proteksi: Hanya Admin yang bisa mengakses halaman ini
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: /dashboard");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
$accent = '#6366f1'; 
$gradient = 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)';

// Simulasi Data Pengguna dari Database
$users = [
    ['id' => 1, 'nama' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'role' => 'User', 'status' => 'Aktif'],
    ['id' => 2, 'nama' => 'Admin Utama', 'email' => 'admin@panenusa.pro', 'role' => 'Admin', 'status' => 'Aktif'],
    ['id' => 3, 'nama' => 'Siti Aminah', 'email' => 'siti@outlook.com', 'role' => 'User', 'status' => 'Non-Aktif'],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna | Panenusa Pro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { background-color: #080b14; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .sidebar-item { transition: all 0.3s ease; border-radius: 12px; margin-bottom: 4px; color: #94a3b8; }
        .sidebar-active { background: rgba(255, 255, 255, 0.05); color: <?= $accent ?> !important; font-weight: 700; border-left: 4px solid <?= $accent ?>; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-72 card-glass border-r border-white/5 p-6 hidden lg:flex flex-col">
        <div class="mb-10 px-2 text-xl font-bold tracking-tighter text-white">
            Admin<span style="color: <?= $accent ?>">.Panel</span>
        </div>

        <nav class="flex-1 overflow-y-auto">
            <a href="dashboard" class="sidebar-item flex items-center gap-4 p-3.5">
                <div class="w-5 text-center"><i class="fas fa-chart-pie text-sm"></i></div>
                <span class="text-sm">Dashboard</span>
            </a>
            <a href="kelola_pengguna" class="sidebar-item sidebar-active flex items-center gap-4 p-3.5">
                <div class="w-5 text-center"><i class="fas fa-users text-sm"></i></div>
                <span class="text-sm">Kelola Pengguna</span>
            </a>
            <a href="moderasi_forum" class="sidebar-item flex items-center gap-4 p-3.5">
                <div class="w-5 text-center"><i class="fas fa-shield-halved text-sm"></i></div>
                <span class="text-sm">Moderasi</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-6 lg:p-12">
        <header class="mb-12 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Database Pengguna</h1>
                <p class="text-slate-500 text-sm mt-1">Manajemen akun, peran, dan hak akses sistem.</p>
            </div>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition shadow-lg shadow-indigo-500/20">
                <i class="fas fa-plus mr-2"></i> Tambah Admin
            </button>
        </header>

        <div class="card-glass rounded-[2.5rem] overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/5 border-b border-white/5">
                    <tr>
                        <th class="p-6 text-xs font-bold uppercase text-slate-500 tracking-widest">Nama & Email</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-500 tracking-widest">Role</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-500 tracking-widest">Status</th>
                        <th class="p-6 text-xs font-bold uppercase text-slate-500 tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach($users as $user): ?>
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-6">
                            <div class="font-bold text-white"><?= $user['nama'] ?></div>
                            <div class="text-xs text-slate-500"><?= $user['email'] ?></div>
                        </td>
                        <td class="p-6">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $user['role'] == 'Admin' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-white/5 text-slate-400' ?>">
                                <?= $user['role'] ?>
                            </span>
                        </td>
                        <td class="p-6">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full <?= $user['status'] == 'Aktif' ? 'bg-emerald-500' : 'bg-red-500' ?>"></div>
                                <span class="text-sm text-slate-300"><?= $user['status'] ?></span>
                            </div>
                        </td>
                        <td class="p-6">
                            <div class="flex justify-center gap-2">
                                <button class="w-9 h-9 rounded-lg bg-white/5 text-slate-400 hover:bg-indigo-500 hover:text-white transition">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button class="w-9 h-9 rounded-lg bg-white/5 text-slate-400 hover:bg-red-500 hover:text-white transition">
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