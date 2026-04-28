<?php
ob_start();
session_start();
include 'config.php';

// Cek apakah ada session ATAU cookie
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id && isset($_COOKIE['panenusa_auth'])) {
    $data = json_decode($_COOKIE['panenusa_auth'], true);
    if (isset($data['user_id'])) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];
        $user_id = $_SESSION['user_id'];
    }
}

// HANYA redirect ke login jika BENAR-BENAR tidak ada session & cookie
if (!$user_id) {
    header("Location: /login");
    exit();
}
// Jika ada $user_id, biarkan halaman dashboard tampil (jangan di-redirect lagi)

/** * PROTEKSI HALAMAN
 * Jika session dan cookie tidak ada, paksa kembali ke login
 */
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

// Data siap digunakan untuk query database
$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['nama'];
$role_user = $_SESSION['role'];

// Contoh pengambilan data lahan berdasarkan user_id dari cookie/session tadi
$query_lahan = $conn->query("SELECT SUM(luas) as total_luas FROM data_lahan WHERE user_id = '$user_id'");
$row_lahan = $query_lahan->fetch_assoc();
$total_luas = $row_lahan['total_luas'] ?? 0;
?>
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
    <aside class="w-64 bg-slate-800 border-r border-slate-700 p-6 flex flex-col hidden md:flex">
        <h2 class="text-2xl font-bold mb-10 text-emerald-500">Panenusa</h2>
        <nav class="flex-1 space-y-2">
            <a href="/dashboard" class="block p-3 bg-emerald-500/10 text-emerald-400 rounded-xl border border-emerald-500/20">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
            <a href="/data-lahan" class="block p-3 text-slate-400 hover:bg-slate-700 rounded-xl transition">
                <i class="fas fa-seedling mr-2"></i> Data Lahan
            </a>
        </nav>
        <a href="/auth/logout" class="p-3 text-red-400 hover:bg-red-500/10 rounded-xl mt-auto border border-transparent hover:border-red-500/20">
            <i class="fas fa-sign-out-alt mr-2"></i> Keluar
        </a>
    </aside>

    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        <div class="max-w-5xl mx-auto">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-3xl font-bold">Selamat Datang, <?= htmlspecialchars($nama_user) ?>!</h1>
                    <p class="text-slate-400">Ringkasan aktivitas pertanian Anda hari ini.</p>
                </div>
                <div class="px-4 py-2 bg-emerald-500/10 text-emerald-400 rounded-full text-xs font-bold border border-emerald-500/20">
                    <?= strtoupper($role_user) ?>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-slate-800 p-6 rounded-3xl border border-slate-700">
                    <p class="text-slate-400 text-sm">Total Luas Lahan</p>
                    <h3 class="text-3xl font-bold mt-2"><?= $total_luas ?> <span class="text-sm font-normal text-slate-500">Ha</span></h3>
                </div>
            </div>
            
            <div class="bg-slate-800 p-8 rounded-3xl border border-slate-700 text-center">
                <p class="text-slate-500 italic">Gunakan menu di samping untuk mengelola data lahan Anda.</p>
            </div>
        </div>
    </main>
</body>
</html>