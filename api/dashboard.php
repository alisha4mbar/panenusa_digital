<?php
// api/dashboard.php
require_once 'config.php';

// Proteksi: Cek apakah cookie autentikasi ada
if (!isset($_COOKIE['panenusa_auth'])) {
    header("Location: /login"); // Jika tidak ada, tendang ke login
    exit();
}

// Ambil dan decode data user dari cookie
$userData = json_decode($_COOKIE['panenusa_auth'], true);
$nama = $userData['nama'];
$role = $userData['role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white p-10">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold">Halo, <?= htmlspecialchars($nama) ?>!</h1>
        <p class="text-slate-400 mt-2">Anda masuk sebagai: <span class="text-emerald-400 font-bold"><?= strtoupper($role) ?></span></p>
        
        <div class="mt-10 p-6 bg-slate-800 rounded-2xl border border-white/5">
            <p>Selamat datang di panel kontrol Panenusa Digital.</p>
            <a href="/auth/logout" class="inline-block mt-6 text-red-400 font-bold hover:underline">Keluar Sistem</a>
        </div>
    </div>
</body>
</html>