<?php
session_start();
include 'config.php';
include 'data_bps.php'; // Diubah dari api_bps.php

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') { // Admin huruf besar
    header("Location: dashboard.php");
    exit();
}

// Menghitung total produksi dari data BPS
$data_bps = function_exists('getBpsData') ? getBpsData() : [];
$total_produksi_8 = 0;
foreach($data_bps as $provinsi) {
    $total_produksi_8 += $provinsi['value'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Nasional - Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex h-screen bg-gray-50">
    <?php include 'sidebar.php'; ?>
    <main class="flex-1 p-10">
        <h2 class="text-3xl font-black text-gray-900 mb-8">Laporan Produksi Nasional</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-8 rounded-[40px] shadow-sm">
                <h3 class="font-bold text-gray-400 uppercase text-xs">Total Produksi (8 Prov)</h3>
                <p class="text-4xl font-black text-emerald-600 mt-2"><?php echo number_format($total_produksi_8, 2, ',', '.'); ?> Ton</p>
            </div>
            </div>
    </main>
</body>
</html>