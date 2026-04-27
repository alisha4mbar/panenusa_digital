<?php
session_start();
include 'config.php';

// Proteksi Session: Jika belum login, arahkan ke login.php
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'] ?? 'User';
$user_id = $_SESSION['user_id'];
$accent = ($role == 'Admin') ? '#6366f1' : '#10b981'; 

// --- LOGIC DATABASE DINAMIS ---
try {
    if($role == 'Admin') {
        // Admin: Ambil semua data lahan + Nama Pemiliknya dari tabel users
        $query = "SELECT data_lahan.*, users.nama as pemilik 
                  FROM data_lahan 
                  JOIN users ON data_lahan.user_id = users.id 
                  ORDER BY data_lahan.created_at DESC";
    } else {
        // User: Hanya ambil lahan milik user yang sedang login
        $query = "SELECT * FROM data_lahan 
                  WHERE user_id = '$user_id' 
                  ORDER BY created_at DESC";
    }
    $result = mysqli_query($conn, $query);
} catch (mysqli_sql_exception $e) {
    // Jika tabel belum dibuat di database, buat variabel result false agar tidak fatal error
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Lahan | Panenusa Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { background-color: #080b14; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Manajemen Lahan</h1>
                <p class="text-slate-500 text-sm mt-1">
                    <?= ($role == 'Admin') ? 'Monitoring seluruh aset lahan petani terdaftar.' : 'Kelola dan pantau produktivitas lahan Anda.' ?>
                </p>
            </div>
            
            <?php if($role != 'Admin'): ?>
            <button class="px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-sm hover:bg-emerald-500 transition shadow-lg shadow-emerald-500/20">
                <i class="fas fa-plus mr-2"></i> Tambah Lahan
            </button>
            <?php endif; ?>
        </header>

        <div class="card-glass rounded-[2.5rem] overflow-hidden">
            <div class="p-8 border-b border-white/5">
                <h3 class="font-bold text-white">Daftar Plot Lahan Aktif</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/5">
                        <tr>
                            <?php if($role == 'Admin'): ?>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Pemilik</th>
                            <?php endif; ?>
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Lokasi</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Luas (Ha)</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Status Lahan</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-white/[0.02] transition">
                                    <?php if($role == 'Admin'): ?>
                                        <td class="p-6 text-sm font-bold text-indigo-400"><?= htmlspecialchars($row['pemilik']) ?></td>
                                    <?php endif; ?>
                                    <td class="p-6 text-sm text-slate-300"><?= htmlspecialchars($row['lokasi']) ?></td>
                                    <td class="p-6 text-sm font-mono"><?= number_format($row['luas'], 1) ?></td>
                                    <td class="p-6">
                                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-[10px] font-bold uppercase">
                                            <?= htmlspecialchars($row['status_lahan']) ?>
                                        </span>
                                    </td>
                                    <td class="p-6 text-center text-slate-500">
                                        <button class="hover:text-white transition"><i class="fas fa-ellipsis-h"></i></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-20 text-center text-slate-500">
                                    <i class="fas fa-folder-open text-4xl mb-4 opacity-20"></i>
                                    <p class="text-sm italic">Belum ada data lahan tersedia.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>