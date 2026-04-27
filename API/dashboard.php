<<?php
session_start();
include 'config.php';

// Proteksi: Jika tidak ada session, lempar ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

// Ambil data dari session (Pastikan di auth.php datanya sudah dimasukkan ke session)
$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['nama'] ?? 'Pengguna';
$role_user = $_SESSION['role'] ?? 'User';

// Ambil data statistik lahan dari database
$query_lahan = $conn->query("SELECT SUM(luas) as total_luas FROM data_lahan WHERE user_id = '$user_id'");
$row_lahan = $query_lahan->fetch_assoc();
$total_luas = $row_lahan['total_luas'] ?? 0;
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

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 p-6 md:p-10 overflow-y-auto">
        <div class="max-w-5xl mx-auto">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold">Halo, <?= htmlspecialchars($nama_user) ?>!</h1>
                    <p class="text-slate-400 mt-2">Anda masuk sebagai: <span class="text-emerald-400 font-bold"><?= strtoupper($role_user) ?></span></p>
                </div>
                <a href="/logout" class="bg-red-500/10 text-red-500 px-4 py-2 rounded-xl border border-red-500/20 hover:bg-red-500 hover:text-white transition-all font-semibold">
                    <i class="fas fa-power-off mr-2"></i> Keluar
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
                <div class="bg-slate-800 p-6 rounded-2xl border border-white/5">
                    <p class="text-slate-400 text-sm">Estimasi Produksi</p>
                    <h3 class="text-2xl font-bold mt-1">12.840 <span class="text-sm font-normal text-slate-500">Ton</span></h3>
                </div>
                <div class="bg-slate-800 p-6 rounded-2xl border border-white/5">
                    <p class="text-slate-400 text-sm">Luas Lahan Anda</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($total_luas, 0, ',', '.') ?> <span class="text-sm font-normal text-slate-500">Hektar</span></h3>
                </div>
                <div class="bg-slate-800 p-6 rounded-2xl border border-white/5">
                    <p class="text-slate-400 text-sm">Status Sistem</p>
                    <h3 class="text-2xl font-bold mt-1 text-emerald-400">Terhubung</h3>
                </div>
            </div>
            
            <div class="mt-10 p-8 bg-slate-800 rounded-3xl border border-white/5 shadow-2xl">
                <h2 class="text-xl font-bold mb-4">Aktivitas Lahan Terbaru</h2>
                <p class="text-slate-400 leading-relaxed mb-6">
                    Berikut adalah data lahan yang baru saja Anda kelola atau pantau melalui sistem Panenusa.
                </p>

                <div class="overflow-hidden rounded-xl border border-white/5">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/5 text-slate-300 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4">Lokasi</th>
                                <th class="px-6 py-4">Luas</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php if ($recent_data->num_rows > 0): ?>
                                <?php while($row = $recent_data->fetch_assoc()): ?>
                                <tr class="hover:bg-white/5">
                                    <td class="px-6 py-4 font-medium text-emerald-400"><?= htmlspecialchars($row['lokasi']) ?></td>
                                    <td class="px-6 py-4"><?= $row['luas'] ?> Ha</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-md text-xs bg-emerald-500/10 text-emerald-400">
                                            <?= htmlspecialchars($row['status_lahan']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-slate-500 italic">Belum ada data lahan terdaftar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    <a href="/data_lahan" class="text-emerald-400 hover:text-emerald-300 font-semibold text-sm">Lihat Semua Data <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>

            <footer class="mt-12 text-center text-slate-600 text-sm pb-10">
                &copy; 2024 Panenusa Digital Ecosystem. Semua Hak Dilindungi.
            </footer>
        </div>
    </div>
</body>
</html>