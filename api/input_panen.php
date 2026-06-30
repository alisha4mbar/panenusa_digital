<?php
ob_start();
session_start();
require_once __DIR__ . '/config.php';

// Proteksi Halaman
$userData = requireLogin();
$user_id   = $userData['id'];
$nama_user = $userData['nama'];
$role_user = $userData['role'];

if (strtolower($role_user) !== 'user') {
    header('Location: login.php');
    exit();
}

// Ambil daftar lahan milik petani untuk pilihan di form <select>
$query_lahan = mysqli_query($conn, "SELECT id, nama_lahan FROM lahan WHERE pemilik_id = '$user_id'");

$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_panen  = mysqli_real_escape_string($conn, $_POST['tanggal_panen']);
    $lahan_id       = mysqli_real_escape_string($conn, $_POST['lahan_id']);
    $musim_tanam    = mysqli_real_escape_string($conn, $_POST['musim_tanam']);
    $komoditas      = mysqli_real_escape_string($conn, $_POST['komoditas']);
    $berat_kg       = floatval($_POST['berat_kg']);
    $varietas       = mysqli_real_escape_string($conn, $_POST['varietas']);
    $estimasi_rusak = floatval($_POST['estimasi_rusak']);
    
    // Konversi Kg ke Ton agar kompatibel dengan basis data data logistik Admin Nasional
    $berat_tonase = $berat_kg / 1000;

    // Proses Unggah Gambar Nota Timbangan (<<extend>> FR-PANEN-02)
    $foto_nota = '';
    if (isset($_FILES['foto_nota']) && $_FILES['foto_nota']['error'] === 0) {
        $target_dir = __DIR__ . '/../public/uploads/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['foto_nota']['name'], PATHINFO_EXTENSION);
        $file_name = 'nota_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['foto_nota']['tmp_name'], $target_file)) {
            $foto_nota = $file_name;
        }
    }

    if (empty($tanggal_panen) || empty($lahan_id) || empty($komoditas) || $berat_kg <= 0) {
        $error_msg = "Kolom bertanda bintang (*) wajib diisi dengan benar!";
    } else {
        // Insert ke database dengan status awal 'pending' agar bisa disortir Admin
        $query_insert = "INSERT INTO transaksi_panen (petani_id, lahan_id, tanggal_panen, musim_tanam, komoditas, berat_tonase, varietas, persentase_kerusakan, foto_nota, status, created_at) 
                         VALUES ('$user_id', '$lahan_id', '$tanggal_panen', '$musim_tanam', '$komoditas', '$berat_tonase', '$varietas', '$estimasi_rusak', '$foto_nota', 'pending', NOW())";
        
        if (mysqli_query($conn, $query_insert)) {
            $success_msg = "Data berhasil dikirim! Mengalihkan halaman...";
            header("refresh:1.5;url=dashboard_user.php");
        } else {
            $error_msg = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Hasil Panen | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#0f172a] text-slate-100 font-sans min-h-screen pb-12">

    <header class="bg-[#1e293b] border-b border-slate-800 p-5 sticky top-0 z-10 shadow-md">
        <div class="max-w-md mx-auto flex items-center gap-4">
            <a href="dashboard_user.php" class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-white transition">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-lg font-black text-white tracking-tight">INPUT DATA PANEN</h1>
                <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-wide">Formulir FR-PANEN-02</p>
            </div>
        </div>
    </header>

    <main class="max-w-md mx-auto px-4 pt-6">
        
        <?php if ($error_msg): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-4 text-sm font-bold">
                <i class="fas fa-exclamation-triangle mr-2"></i> <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <?php if ($success_msg): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-4 text-sm font-bold">
                <i class="fas fa-check-circle mr-2"></i> <?= $success_msg ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-5">
            
            <div>
                <label class="block text-base font-bold text-slate-300 mb-2">Tanggal Panen <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_panen" required value="<?= date('Y-m-d') ?>"
                       class="w-full bg-[#1e293b] border-2 border-slate-800 focus:border-emerald-500 rounded-xl p-4 text-lg font-semibold text-white outline-none">
            </div>

            <div>
                <label class="block text-base font-bold text-slate-300 mb-2">Lahan Lokasi <span class="text-red-500">*</span></label>
                <select name="lahan_id" required 
                        class="w-full bg-[#1e293b] border-2 border-slate-800 focus:border-emerald-500 rounded-xl p-4 text-lg font-semibold text-white outline-none appearance-none">
                    <option value="" disabled selected>-- Pilih Lokasi Petak --</option>
                    <?php if ($query_lahan && mysqli_num_rows($query_lahan) > 0): ?>
                        <?php while($lahan = mysqli_fetch_assoc($query_lahan)): ?>
                            <option value="<?= $lahan['id'] ?>"><?= htmlspecialchars($lahan['nama_lahan']) ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="1">Lahan Utama Ladang</option>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label class="block text-base font-bold text-slate-300 mb-2">Musim Tanam</label>
                <input type="text" name="musim_tanam" placeholder="Contoh: Gadu 2026 / Rendengan"
                       class="w-full bg-[#1e293b] border-2 border-slate-800 focus:border-emerald-500 rounded-xl p-4 text-base font-semibold placeholder-slate-600 text-white outline-none">
            </div>

            <div>
                <label class="block text-base font-bold text-slate-300 mb-2">Komoditas <span class="text-red-500">*</span></label>
                <select name="komoditas" required 
                        class="w-full bg-[#1e293b] border-2 border-slate-800 focus:border-emerald-500 rounded-xl p-4 text-lg font-semibold text-white outline-none">
                    <option value="Padi">Padi</option>
                    <option value="Jagung">Jagung</option>
                    <option value="Kedelai">Kedelai</option>
                    <option value="Bawang Merah">Bawang Merah</option>
                </select>
            </div>

            <div>
                <label class="block text-base font-bold text-slate-300 mb-2">Berat Timbangan (Kg) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" step="0.1" name="berat_kg" required placeholder="0.0"
                           class="w-full bg-[#1e293b] border-2 border-slate-800 focus:border-emerald-500 rounded-xl p-4 pr-16 text-2xl font-black text-white outline-none">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-base font-bold text-slate-500">KG</span>
                </div>
            </div>

            <div>
                <label class="block text-base font-bold text-slate-300 mb-2">Varietas Benih</label>
                <input type="text" name="varietas" placeholder="Contoh: Ciherang, Inpari 32"
                       class="w-full bg-[#1e293b] border-2 border-slate-800 focus:border-emerald-500 rounded-xl p-4 text-base font-semibold placeholder-slate-600 text-white outline-none">
            </div>

            <div>
                <label class="block text-base font-bold text-slate-300 mb-2">Estimasi Kerusakan (%)</label>
                <div class="relative">
                    <input type="number" step="1" name="estimasi_rusak" placeholder="0" min="0" max="100"
                           class="w-full bg-[#1e293b] border-2 border-slate-800 focus:border-emerald-500 rounded-xl p-4 pr-16 text-xl font-bold text-white outline-none">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-lg font-bold text-slate-500">%</span>
                </div>
            </div>

            <div>
                <label class="block text-base font-bold text-slate-300 mb-2">Foto Nota Timbangan <span class="text-xs font-normal text-slate-500">(<<extend>> FR-PANEN-02)</label>
                <div class="relative w-full bg-[#1e293b] border-2 border-dashed border-slate-800 hover:border-slate-700 rounded-xl p-5 text-center cursor-pointer transition">
                    <input type="file" name="foto_nota" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="space-y-1">
                        <i class="fas fa-camera text-2xl text-slate-500"></i>
                        <p class="text-sm font-semibold text-slate-400">Ketuk untuk Ambil Foto Nota</p>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" 
                        class="w-full bg-emerald-500 hover:bg-emerald-600 text-white p-5 rounded-2xl font-black text-xl shadow-xl transition active:scale-[0.98]">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Data Panen
                </button>
            </div>

        </form>
    </main>
</body>
</html>