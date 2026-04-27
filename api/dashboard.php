<?php
session_start();
include 'config.php';

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

$nama_user = $_SESSION['nama'];
$role_user = $_SESSION['role'];
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
<body class="bg-slate-900 text-white p-6 md:p-10">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-bold">Halo, <?= htmlspecialchars($nama) ?>!</h1>
                <p class="text-slate-400 mt-2">Anda masuk sebagai: <span class="text-emerald-400 font-bold"><?= strtoupper($role) ?></span></p>
            </div>
            <a href="/auth/logout" class="bg-red-500/10 text-red-500 px-4 py-2 rounded-xl border border-red-500/20 hover:bg-red-500 hover:text-white transition-all font-semibold">
                <i class="fas fa-power-off mr-2"></i> Keluar
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
            <div class="bg-slate-800 p-6 rounded-2xl border border-white/5">
                <p class="text-slate-400 text-sm">Total Produksi</p>
                <h3 class="text-2xl font-bold mt-1">12.840 <span class="text-sm font-normal text-slate-500">Ton</span></h3>
            </div>
            <div class="bg-slate-800 p-6 rounded-2xl border border-white/5">
                <p class="text-slate-400 text-sm">Luas Lahan</p>
                <h3 class="text-2xl font-bold mt-1">450 <span class="text-sm font-normal text-slate-500">Hektar</span></h3>
            </div>
            <div class="bg-slate-800 p-6 rounded-2xl border border-white/5">
                <p class="text-slate-400 text-sm">Status Sistem</p>
                <h3 class="text-2xl font-bold mt-1 text-emerald-400">Aktif</h3>
            </div>
        </div>
        
        <div class="mt-10 p-8 bg-slate-800 rounded-3xl border border-white/5 shadow-2xl">
            <h2 class="text-xl font-bold mb-4">Panel Kontrol Panenusa Digital</h2>
            <p class="text-slate-400 leading-relaxed">
                Melalui panel ini, Anda dapat memantau seluruh aktivitas digital pertanian secara real-time. 
                Gunakan menu navigasi untuk mengelola data produksi dan distribusi.
            </p>

            <div class="mt-8 overflow-hidden rounded-xl border border-white/5">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white/5 text-slate-300 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Komoditas</th>
                            <th class="px-6 py-4">Wilayah</th>
                            <th class="px-6 py-4">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr class="hover:bg-white/5">
                            <td class="px-6 py-4 font-medium text-emerald-400">Padi Unggul</td>
                            <td class="px-6 py-4">Jawa Timur</td>
                            <td class="px-6 py-4">85%</td>
                        </tr>
                        <tr class="hover:bg-white/5">
                            <td class="px-6 py-4 font-medium text-emerald-400">Jagung Hibrida</td>
                            <td class="px-6 py-4">Lampung</td>
                            <td class="px-6 py-4">60%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="mt-12 text-center text-slate-600 text-sm">
            &copy; 2024 Panenusa Digital Ecosystem. Semua Hak Dilindungi.
        </footer>
    </div>
</body>
</html>