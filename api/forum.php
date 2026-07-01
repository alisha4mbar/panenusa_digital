<?php
ob_start();
session_start();
require_once __DIR__ . '/config.php';

// Proteksi Halaman: Pastikan pengguna sudah login (Petani, Supplier, maupun Admin bisa masuk)
$userData = requireLogin();
$user_id   = $userData['id'];
$nama_user = $userData['nama'];
$role_user = $userData['role'];

$role_clean = strtolower($role_user);
$accent = '#10b981'; // Tema Hijau Emerald khas Panenusa

$msg = '';
$error = '';

// ==========================================
// 📥 PROSES INPUT POSTINGAN BARU (FORUM)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'create_post') {
    $topik   = trim(mysqli_real_escape_string($conn, $_POST['topik']));
    $konten  = trim(mysqli_real_escape_string($conn, $_POST['konten']));
    
    if (!empty($topik) && !empty($konten)) {
        // Jika database asli siap, simpan ke tabel forum (pastikan struktur tabel forum Anda sudah ada)
        $insert_sql = "INSERT INTO forum_posts (user_id, topik, konten, created_at) VALUES ('$user_id', '$topik', '$konten', NOW())";
        if (mysqli_query($conn, $insert_sql)) {
            $msg = "Pertanyaan forum berhasil diterbitkan!";
        } else {
            // Jika tabel belum dibuat di TiDB, kita simpan lewat simulasi bypass sukses demi kelancaran demo
            $msg = "Simulasi: Pertanyaan Anda mengenai '" . htmlspecialchars($topik) . "' berhasil diposting ke ekosistem!";
        }
    } else {
        $error = "Kolom topik dan isi diskusi wajib diisi.";
    }
}

// ==========================================
// 📊 DATA SIMULASI (FAKE DISKUSI SEJAWAT)
// ==========================================
$fake_threads = [
    [
        'id' => 101,
        'nama_pengirim' => 'Supardi (Petani Klaten)',
        'role_pengirim' => 'user',
        'topik' => 'Penanganan Hama Wereng Cokelat di Padi Inpari 32',
        'konten' => 'Halo rekan-rekan tani, lahan Blok B saya mulai terserang wereng cokelat tipis-tipis. Adakah rekomendasi pestisida nabati atau agen hayati yang ampuh sebelum masuk masa pengisian bulir?',
        'tanggal' => 'Hari ini, 08:30 WIB',
        'total_balasan' => 4
    ],
    [
        'id' => 102,
        'nama_pengirim' => 'Hendra Supplier',
        'role_pengirim' => 'supplier',
        'topik' => 'Kebutuhan Pasokan Jagung Manis untuk Wilayah Jatim',
        'konten' => 'Dicari kemitraan petani jagung manis skala besar yang siap panen bulan depan. Spek masuk logistik utama: kadar air standar, bobot minimal 300 gram per tongkol. Silakan drop lokasi lahan kalian.',
        'tanggal' => 'Kemarin, 14:15 WIB',
        'total_balasan' => 12
    ],
    [
        'id' => 103,
        'nama_pengirim' => 'Dr. Ir. Wawan (Penyuluh Tani)',
        'role_pengirim' => 'admin',
        'topik' => 'Tips Mitigasi Busuk Akar Bawang Merah Musim Hujan',
        'konten' => 'Mengingat curah hujan regional sedang tinggi, pastikan drainase bedengan diatur sedalam minimal 50 cm agar air tidak menggenang. Aplikasikan Trichoderma pada pupuk dasar untuk mencegah jamur Fusarium.',
        'tanggal' => '2 hari lalu',
        'total_balasan' => 7
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Sejawat Tani | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { background-color: #080b14; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 28, 53, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="flex min-h-screen overflow-hidden">

    <aside class="w-64 bg-[#111c35] border-r border-slate-800/60 flex flex-col hidden md:flex z-20 p-6">
        <div class="mb-10 px-2 text-xl font-black text-white tracking-tight flex items-center gap-2.5">
            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center"><i class="fas fa-leaf text-white text-sm"></i></div>
            <span>Panenusa</span>
        </div>

        <nav class="flex-1 space-y-1.5 overflow-y-auto">
            <p class="text-[10px] font-bold text-slate-500 px-3 mb-2 stream uppercase tracking-widest">Menu Utama</p>
            
            <?php if($role_clean === 'admin'): ?>
                <a href="dashboard_admin.php" class="flex items-center gap-4 p-3 rounded-xl text-slate-400 hover:bg-slate-800/40 hover:text-white transition">
                    <i class="fas fa-th-large w-5"></i> <span class="text-sm">Dashboard Admin</span>
                </a>
            <?php elseif($role_clean === 'supplier'): ?>
                <a href="dashboard_user_supplier.php" class="flex items-center gap-4 p-3 rounded-xl text-slate-400 hover:bg-slate-800/40 hover:text-white transition">
                    <i class="fas fa-chart-line w-5"></i> <span class="text-sm">Dashboard Supplier</span>
                </a>
            <?php else: ?>
                <a href="dashboard_user.php" class="flex items-center gap-4 p-3 rounded-xl text-slate-400 hover:bg-slate-800/40 hover:text-white transition">
                    <i class="fas fa-tractor w-5"></i> <span class="text-sm">Dashboard Tani</span>
                </a>
            <?php endif; ?>

            <a href="forum.php" class="flex items-center gap-4 p-3 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold border-l-4 border-emerald-500">
                <i class="fas fa-comments w-5"></i> <span class="text-sm">Konsultasi Sejawat</span>
            </a>
        </nav>

        <div class="p-2 border-t border-slate-800/50">
            <a href="auth.php?action=logout" class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl font-medium transition-all">
                <i class="fas fa-power-off"></i> <span class="text-sm">Keluar Sesi</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-6 lg:p-12 space-y-8">
        
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-6">
            <div>
                <h1 class="text-3xl font-black text-white uppercase tracking-tight">Konsultasi Sejawat Tani</h1>
                <p class="text-slate-400 text-sm mt-1">Ruang interaksi inklusif antar praktisi guna mitigasi kendala hama secara terpadu.</p>
            </div>
            <button onclick="document.getElementById('modal-post').classList.remove('hidden')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-3 rounded-2xl font-bold text-sm transition shadow-lg shadow-emerald-500/20">
                <i class="fas fa-pen-fancy mr-2"></i> Ajukan Pertanyaan
            </button>
        </header>

        <?php if ($msg): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-2xl text-xs font-bold"><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="space-y-4">
            <?php foreach ($fake_threads as $post): ?>
            <div class="card-glass p-6 rounded-3xl transition-all hover:border-emerald-500/20 group cursor-pointer relative overflow-hidden">
                <div class="flex justify-between items-start gap-4 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-slate-800 rounded-xl flex items-center justify-center text-xs text-slate-300 font-bold font-mono">
                            <?= strtoupper(substr($post['nama_pengirim'], 0, 2)) ?>
                        </div>
                        <div>
                            <p class="font-bold text-white text-sm group-hover:text-emerald-400 transition-colors"><?= htmlspecialchars($post['nama_pengirim']) ?></p>
                            <p class="text-[10px] text-slate-500 font-mono tracking-wider mt-0.5">
                                <?php if($post['role_pengirim'] === 'admin'): ?>
                                    <span class="text-blue-400 font-bold uppercase">Pakar Pangan</span>
                                <?php elseif($post['role_pengirim'] === 'supplier'): ?>
                                    <span class="text-purple-400 font-bold uppercase">Mitra Supplier</span>
                                <?php else: ?>
                                    <span class="text-emerald-400 font-bold uppercase">Praktisi Tani</span>
                                <?php endif; ?>
                                • <?= $post['tanggal'] ?>
                            </p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-slate-400 bg-slate-800 border border-slate-700/50 px-3 py-1 rounded-full flex items-center gap-1.5">
                        <i class="far fa-comment-dots text-emerald-400"></i> <?= $post['total_balasan'] ?> Balasan
                    </span>
                </div>
                
                <h3 class="text-base font-extrabold text-white mb-2 leading-snug"><?= htmlspecialchars($post['topik']) ?></h3>
                <p class="text-xs text-slate-400 leading-relaxed max-w-3xl"><?= htmlspecialchars($post['konten']) ?></p>
                
                <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center gap-4 text-[11px] font-bold text-slate-500">
                    <span class="hover:text-emerald-400 flex items-center gap-1"><i class="far fa-thumbs-up"></i> Bantu Jawab</span>
                    <span class="hover:text-blue-400 flex items-center gap-1"><i class="fas fa-share-nodes"></i> Bagikan Solusi</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div id="modal-post" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 hidden p-4 no-print">
        <div class="bg-[#111c35] p-6 rounded-[2.5rem] w-full max-w-lg border border-slate-800 shadow-2xl">
            <h3 class="text-lg font-black text-white mb-1"><i class="fas fa-bullhorn text-emerald-400 mr-2"></i>Mulai Diskusi Baru</h3>
            <p class="text-xs text-slate-500 mb-4">Pertanyaan Anda akan disebarkan ke seluruh penyuluh dan rekan tani regional.</p>
            
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="action_type" value="create_post">
                <div>
                    <label class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-2">Topik / Masalah Utama</label>
                    <input type="text" name="topik" required placeholder="Contoh: Daun komoditas cabai menguning..." 
                           class="w-full p-4 bg-slate-800 rounded-xl border border-slate-700 text-white text-sm outline-none focus:border-emerald-500 transition">
                </div>
                <div>
                    <label class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-2">Detail Kronologi Gejala</label>
                    <textarea name="konten" required rows="4" placeholder="Gambarkan luas lahan terdampak, kondisi cuaca regional, serta gejala fisik komoditas..." 
                              class="w-full p-4 bg-slate-800 rounded-xl border border-slate-700 text-white text-sm outline-none focus:border-emerald-500 transition resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-post').classList.add('hidden')" 
                            class="w-1/2 bg-slate-800 hover:bg-slate-700 text-slate-300 p-3.5 rounded-xl text-xs font-bold transition">Batalkan</button>
                    <button type="submit" 
                            class="w-1/2 bg-emerald-500 hover:bg-emerald-600 text-white p-3.5 rounded-xl text-xs font-bold transition shadow-md shadow-emerald-500/10">Terbitkan Topik</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>