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
    <style>
        body { background-color: #080b14; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-white">Manajemen Lahan</h1>
                <p class="text-slate-500 text-sm mt-1">Kelola dan pantau produktivitas lahan Anda.</p>
            </div>
            <?php if($role != 'Admin'): ?>
            <button class="px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-sm hover:bg-emerald-500 transition shadow-lg shadow-emerald-500/20">
                <i class="fas fa-plus mr-2"></i> Tambah Lahan
            </button>
            <?php endif; ?>
        </header>

        <div class="card-glass rounded-[2.5rem] overflow-hidden p-8 border-b border-white/5">
            <h3 class="font-bold text-white mb-4">Daftar Plot Lahan Aktif</h3>
            <table class="w-full text-left">
                <thead class="bg-white/5">
                    <tr>
                        <th class="p-6 text-[10px] font-black uppercase text-slate-500">Lokasi</th>
                        <th class="p-6 text-[10px] font-black uppercase text-slate-500">Luas (Ha)</th>
                        <th class="p-6 text-[10px] font-black uppercase text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="p-6 text-sm text-slate-300"><?= htmlspecialchars($row['lokasi']) ?></td>
                                <td class="p-6 text-sm font-mono"><?= number_format($row['luas'], 1) ?></td>
                                <td class="p-6 text-emerald-500 font-bold text-xs uppercase"><?= htmlspecialchars($row['status_lahan']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="p-10 text-center text-slate-500">Belum ada data lahan tersedia.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>