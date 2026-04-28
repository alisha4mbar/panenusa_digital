<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

$role = $_SESSION['role'] ?? 'User';
$nama = $_SESSION['nama'] ?? 'Pengguna';
$accent = ($role == 'Admin') ? '#6366f1' : '#10b981'; 
$gradient = ($role == 'Admin') ? 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konten Edukasi | Panenusa Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { background-color: #080b14; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .sidebar-item { transition: all 0.3s ease; border-radius: 12px; margin-bottom: 4px; color: #94a3b8; }
        .sidebar-item:hover { background: rgba(255, 255, 255, 0.05); color: <?= $accent ?>; }
        .sidebar-active { background: rgba(255, 255, 255, 0.05); color: <?= $accent ?> !important; font-weight: 700; border-left: 4px solid <?= $accent ?>; }
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 1.5rem; }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-72 card-glass border-r border-white/5 p-6 hidden lg:flex flex-col">
        <div class="mb-10 px-2">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-lg" style="background: <?= $gradient ?>">
                    <i class="fas fa-leaf text-lg"></i>
                </div>
                <span class="text-xl font-bold tracking-tighter text-white">Panenusa<span style="color: <?= $accent ?>">.pro</span></span>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto pr-2">
            <a href="dashboard" class="sidebar-item flex items-center gap-4 p-3.5"><div class="w-5 text-center"><i class="fas fa-chart-pie"></i></div> Dashboard</a>
            <a href="konten_edukasi" class="sidebar-item sidebar-active flex items-center gap-4 p-3.5"><div class="w-5 text-center"><i class="fas fa-book-open"></i></div> Konten Edukasi</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-6 lg:p-12">
        <header class="mb-12">
            <h1 class="text-3xl font-bold text-white">Video Tutorial Pertanian</h1>
            <p class="text-slate-500 text-sm">Pelajari teknik modern melalui video YouTube pilihan</p>
        </header>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-2 space-y-6">
                <div class="card-glass p-6 rounded-[2.5rem]">
                    <div class="video-container mb-6">
                        <iframe src="https://www.youtube.com/embed/F802LVONUvA" allowfullscreen></iframe>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">4 Sistem Hidroponik untuk Pemula</h3>
                    <p class="text-slate-400 text-sm mb-6">Penjelasan mendalam tentang cara memulai sistem hidroponik secara efisien.</p>
                    <a href="https://youtu.be/F802LVONUvA" target="_blank" class="inline-flex items-center gap-3 px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold transition">
                        <i class="fab fa-youtube text-lg"></i> Buka di YouTube
                    </a>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-white font-bold px-2">Video Lainnya</h3>
                
                <div class="card-glass p-4 rounded-[2rem] flex gap-4 items-center hover:bg-white/5 transition cursor-pointer">
                    <div class="w-24 h-16 bg-white/10 rounded-lg overflow-hidden flex-shrink-0">
                        <img src="https://img.youtube.com/vi/1NzSYee053U/mqdefault.jpg" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-xs line-clamp-1">Manajemen Pupuk</h4>
                        <p class="text-[10px] text-slate-500 mt-1">Klik untuk tonton</p>
                    </div>
                </div>

                <div class="card-glass p-4 rounded-[2rem] flex gap-4 items-center hover:bg-white/5 transition cursor-pointer">
                    <div class="w-24 h-16 bg-white/10 rounded-lg overflow-hidden flex-shrink-0">
                        <img src="https://img.youtube.com/vi/BdD3y7Ese_g/mqdefault.jpg" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-xs line-clamp-1">Tips Hama Padi</h4>
                        <p class="text-[10px] text-slate-500 mt-1">Klik untuk tonton</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>