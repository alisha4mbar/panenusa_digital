<?php
ob_start();
session_start();
require_once __DIR__ . '/config.php';

// Proteksi Halaman: Hanya Admin Logistik yang boleh mengelola data referensi
$userData = requireLogin();
$user_id   = $userData['id'];
$nama_user = $userData['nama'];
$role_user = $userData['role'];

if (strtolower($role_user) !== 'admin') {
    header('Location: login.php');
    exit();
}

$msg = '';
$error = '';

// ==========================================
// 🚀 PROSES CRUD DATA REFERENSI
// ==========================================

// 1. TAMBAH DATA REFERENSI (Contoh: Tambah Komoditas / Musim Tanam Baru)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $type = $_POST['action_type'];
    
    if ($type === 'add_lahan') {
        $nama_lahan = trim(mysqli_real_escape_string($conn, $_POST['nama_lahan']));
        $lokasi     = trim(mysqli_real_escape_string($conn, $_POST['lokasi']));
        $luas_ha    = floatval($_POST['luas_ha']);
        $pemilik_id = intval($_POST['pemilik_id']);
        
        if (!empty($nama_lahan) && $luas_ha > 0) {
            $insert = "INSERT INTO lahan (nama_lahan, lokasi, luas_ha, pemilik_id, status_lahan, created_at) VALUES ('$nama_lahan', '$lokasi', '$luas_ha', '$pemilik_id', 'Aktif', NOW())";
            if (mysqli_query($conn, $insert)) { $msg = "Data master lahan berhasil ditambahkan!"; }
            else { $error = "Gagal menyimpan data lahan master."; }
        } else { $error = "Mohon isi kolom wajib dengan benar."; }
    }
}

// 2. HAPUS LAHAN MASTER (Delete)
if (isset($_GET['delete_lahan_id'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete_lahan_id']);
    if (mysqli_query($conn, "DELETE FROM lahan WHERE id = '$del_id'")) {
        $msg = "Referensi data master lahan berhasil dihapus.";
    } else {
        $error = "Gagal menghapus data master referensi.";
    }
}

// ==========================================
// 📊 AMBIL DATA REFERENSI UNTUK DITAMPILKAN
// ==========================================
// Tarik data master lahan untuk tabel referensi utama
$query_lahan_master = mysqli_query($conn, "SELECT l.*, u.nama as nama_pemilik FROM lahan l LEFT JOIN users u ON l.pemilik_id = u.id ORDER BY l.id DESC");

// Tarik data user untuk dropdown Pemilik Lahan (Aktor Supplier)
$query_owners = mysqli_query($conn, "SELECT id, nama FROM users WHERE LOWER(role) = 'supplier' ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Referensi | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0b1224] text-slate-200 flex min-h-screen overflow-hidden font-sans">

    <aside class="w-64 bg-[#111c35] border-r border-slate-800/60 flex flex-col hidden md:flex z-20">
        <div class="p-6 border-b border-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-leaf text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight">Panenusa</h2>
                    <p class="text-[9px] text-emerald-400 font-bold tracking-wider uppercase">Logistics Core</p>
                </div>
            </div>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <p class="text-[10px] font-bold text-slate-500 px-3 mb-2 uppercase tracking-widest">Menu Utama</p>
            
            <a href="dashboard_admin.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>
            
            <a href="verifikasi_panen.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200">
                <i class="fas fa-clipboard-check"></i> <span>Verifikasi Panen</span>
                <span class="ml-auto bg-amber-500/10 text-amber-400 text-[9px] font-extrabold px-2 py-0.5 rounded-md">FR-03</span>
            </a>

            <a href="cetak_surat_jalan.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200">
                <i class="fas fa-file-invoice"></i> <span>Cetak Surat Jalan</span>
                <span class="ml-auto bg-blue-500/10 text-blue-400 text-[9px] font-extrabold px-2 py-0.5 rounded-md">FR-05</span>
            </a>

            <div class="pt-6 mt-4 border-t border-slate-800/50">
                <p class="text-[10px] font-bold text-slate-500 px-3 mb-2 uppercase tracking-widest">Administrator</p>
                <a href="kelola_pengguna.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200">
                    <i class="fas fa-users-gear"></i> <span>Kelola Pengguna</span>
                </a>
                <a href="data_referensi.php" class="flex items-center gap-3 px-4 py-3 text-white bg-gradient-to-r from-emerald-500/20 to-emerald-500/5 rounded-xl border border-emerald-500/20 font-semibold shadow-inner transition-all duration-200">
                    <i class="fas fa-database text-emerald-400"></i> <span>Data Referensi</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-slate-800/50">
            <a href="auth.php?action=logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl font-medium transition-all duration-200">
                <i class="fas fa-power-off"></i> <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-[#111c35]/60 backdrop-blur-xl border-b border-slate-800/50 p-4 px-8 flex justify-between items-center z-10">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Manajemen Data Referensi (FR-PANEN-02)</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wide">Admin Logistik</p>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight">Data Master Referensi</h1>
                    <p class="text-slate-400 text-sm mt-1">Kelola data master lahan, siklus musim tanam, dan varietas komoditas nasional.</p>
                </div>
                <button onclick="toggleModal('modal-add-lahan', true)" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-lg shadow-emerald-500/10">
                    <i class="fas fa-plus mr-1.5"></i> Daftarkan Lahan Master
                </button>
            </div>

            <?php if ($msg): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm"><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm"><i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="bg-[#111c35] rounded-[2rem] border border-slate-800/80 shadow-2xl overflow-hidden relative">
                <div class="p-5 border-b border-slate-800/60 bg-[#162544]/30">
                    <h3 class="text-base font-bold text-white"><i class="fas fa-map-marked-alt text-emerald-400 mr-2"></i>Data Master Lahan Registrasi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#162544]/60 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-800/60">
                                <th class="p-4 pl-6">Nama Lahan Master</th>
                                <th class="p-4">Lokasi Wilayah</th>
                                <th class="p-4">Luas Wilayah</th>
                                <th class="p-4">Investor / Pemilik</th>
                                <th class="p-4 pr-6 text-right">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/40 bg-[#111c35]">
                            <?php if ($query_lahan_master && mysqli_num_rows($query_lahan_master) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query_lahan_master)): ?>
                                <tr class="text-slate-300 hover:bg-[#162544]/30 transition duration-150">
                                    <td class="p-4 pl-6 font-bold text-white"><?= htmlspecialchars($row['nama_lahan']) ?></td>
                                    <td class="p-4 text-slate-400"><?= htmlspecialchars($row['lokasi'] ?? '-') ?></td>
                                    <td class="p-4 font-semibold text-emerald-400"><?= number_format($row['luas_ha'], 1) ?> Ha</td>
                                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['nama_pemilik'] ?? 'Tidak Terikat') ?></td>
                                    <td class="p-4 pr-6 text-right">
                                        <a href="data_referensi.php?delete_lahan_id=<?= $row['id'] ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus referensi lahan master ini?');"
                                           class="text-xs font-bold text-red-400 bg-red-500/10 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-xl transition">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="p-12 text-center text-slate-500 text-xs"><i class="fas fa-database text-xl mb-2 block"></i> Belum ada data referensi lahan terdaftar.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="modal-add-lahan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
        <div class="bg-[#111c35] border border-slate-800 w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <h3 class="text-lg font-black text-white"><i class="fas fa-map-location-dot text-emerald-400 mr-2"></i>Registrasi Lahan Baru</h3>
                <button onclick="toggleModal('modal-add-lahan', false)" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="action_type" value="add_lahan">
                
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Nama Blok / Lahan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_lahan" required placeholder="Contoh: Lahan Blok C Barat" class="w-full p-3.5 bg-slate-800 border border-slate-700 rounded-xl text-sm outline-none text-white focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Lokasi Regional</label>
                    <input type="text" name="lokasi" placeholder="Contoh: Madiun, Jawa Timur" class="w-full p-3.5 bg-slate-800 border border-slate-700 rounded-xl text-sm outline-none text-white focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Luas Lahan (Ha) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.1" name="luas_ha" required placeholder="0.0" class="w-full p-3.5 bg-slate-800 border border-slate-700 rounded-xl text-sm outline-none text-white focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Delegasi Pemilik (Supplier)</label>
                    <select name="pemilik_id" required class="w-full p-3.5 bg-slate-800 border border-slate-700 rounded-xl text-sm outline-none text-white focus:border-emerald-500 transition">
                        <option value="" disabled selected>-- Pilih Pemilik Aset --</option>
                        <?php if ($query_owners && mysqli_num_rows($query_owners) > 0): ?>
                            <?php while($owner = mysqli_fetch_assoc($query_owners)): ?>
                                <option value="<?= $owner['id'] ?>"><?= htmlspecialchars($owner['nama']) ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="toggleModal('modal-add-lahan', false)" class="w-1/2 bg-slate-800 text-slate-300 font-bold p-3.5 rounded-xl text-xs hover:bg-slate-700 transition">Batal</button>
                    <button type="submit" class="w-1/2 bg-emerald-500 text-white font-bold p-3.5 rounded-xl text-xs hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/10">Simpan Referensi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id, show) {
            const m = document.getElementById(id);
            if (show) { m.classList.remove('hidden'); } 
            else { m.classList.add('hidden'); }
        }
    </script>
</body>
</html>