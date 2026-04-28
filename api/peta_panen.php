<?php
session_start();
include 'config.php';

// Proteksi Login
if(!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

$role = $_SESSION['role'] ?? 'User';
$accent = ($role == 'Admin') ? '#6366f1' : '#10b981'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta Pangan | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #080b14; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">
    
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-12">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-white">Peta Sebaran Pangan</h1>
                <p class="text-slate-500 text-sm">Data ketersediaan komoditas nasional secara realtime.</p>
            </div>
            
            <?php if($role == 'Admin'): ?>
            <button class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold text-sm hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">
                <i class="fas fa-edit mr-2"></i> Update Data Peta
            </button>
            <?php endif; ?>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 card-glass p-6 rounded-[2.5rem] min-h-[450px] flex items-center justify-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/world-map.png');"></div>
                <div class="text-center relative">
                    <i class="fas fa-map-location-dot text-6xl mb-4 opacity-20" style="color: <?= $accent ?>"></i>
                    <p class="text-slate-400 italic text-sm">[ Map Interface Aktif ]</p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card-glass p-8 rounded-[2rem]">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 mb-4">Status Wilayah</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Jawa Barat</span>
                            <span class="text-emerald-400 font-bold">Surplus</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Kalimantan</span>
                            <span class="text-amber-400 font-bold">Waspada</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>