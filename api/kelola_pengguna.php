<?php
ob_start();
session_start();
require_once __DIR__ . '/config.php';

// Proteksi Halaman: Hanya Admin Logistik yang boleh mengelola pengguna
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
// 🚀 PROSES ACTIONS (POST & GET CRUD)
// ==========================================

// 1. TAMBAH PENGGUNA BARU (Create)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'create') {
    $nama     = trim(mysqli_real_escape_string($conn, $_POST['nama']));
    $email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = trim($_POST['password']);
    $role     = strtolower(mysqli_real_escape_string($conn, $_POST['role']));

    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $error = "Semua kolom pendaftaran wajib diisi!";
    } else {
        // Cek apakah email sudah terdaftar
        $check_mail = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' LIMIT 1");
        if ($check_mail && mysqli_num_rows($check_mail) > 0) {
            $error = "Email tersebut sudah terdaftar di sistem.";
        } else {
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hash_password', '$role')";
            if (mysqli_query($conn, $insert)) {
                $msg = "Pengguna baru berhasil ditambahkan ke ekosistem!";
            } else {
                $error = "Gagal menyimpan akun baru.";
            }
        }
    }
}

// 2. HAPUS PENGGUNA (Delete)
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // Cegah admin menghapus dirinya sendiri saat login
    if ($delete_id == $user_id) {
        $error = "Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!";
    } else {
        if (mysqli_query($conn, "DELETE FROM users WHERE id = '$delete_id'")) {
            $msg = "Akun pengguna berhasil dihapus dari sistem.";
        } else {
            $error = "Gagal menghapus data pengguna.";
        }
    }
}

// ==========================================
// 📊 AMBIL DATA SELURUH PENGGUNA (Read)
// ==========================================
$sql_users = "SELECT id, nama, email, role FROM users ORDER BY role ASC, nama ASC";
$query_users = mysqli_query($conn, $sql_users);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna | Panenusa</title>
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
                <a href="kelola_pengguna.php" class="flex items-center gap-3 px-4 py-3 text-white bg-gradient-to-r from-emerald-500/20 to-emerald-500/5 rounded-xl border border-emerald-500/20 font-semibold shadow-inner transition-all duration-200">
                    <i class="fas fa-users-gear text-emerald-400"></i> <span>Kelola Pengguna</span>
                </a>
                <a href="data_referensi.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/40 hover:text-slate-100 rounded-xl transition-all duration-200">
                    <i class="fas fa-database"></i> <span>Data Referensi</span>
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
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Manajemen Pengguna (FR-PANEN-01)</h2>
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
                    <h1 class="text-3xl font-black text-white tracking-tight">Kredensial Aktor Sistem</h1>
                    <p class="text-slate-400 text-sm mt-1">Otorisasi penuh penambahan, pembaruan rincian, dan penghapusan hak akses pengguna ekosistem Panenusa.</p>
                </div>
                <button onclick="toggleModal('modal-add', true)" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-lg shadow-emerald-500/10">
                    <i class="fas fa-user-plus mr-1.5"></i> Tambah Aktor Baru
                </button>
            </div>

            <?php if ($msg): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm"><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm"><i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="bg-[#111c35] rounded-[2rem] border border-slate-800/80 shadow-2xl overflow-hidden relative">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#162544]/60 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-800/60">
                                <th class="p-4 pl-6">Nama Lengkap</th>
                                <th class="p-4">Email Kredensial</th>
                                <th class="p-4">Tingkat Hak Akses (Role)</th>
                                <th class="p-4 pr-6 text-right">Opsi Pengelolaan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-800/40 bg-[#111c35]">
                            <?php if ($query_users && mysqli_num_rows($query_users) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query_users)): ?>
                                <tr class="text-slate-300 hover:bg-[#162544]/30 transition duration-150">
                                    <td class="p-4 pl-6 font-bold text-white"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="p-4 font-mono text-xs text-slate-400"><?= htmlspecialchars($row['email']) ?></td>
                                    <td class="p-4">
                                        <?php 
                                        $rl = strtolower($row['role']);
                                        if ($rl === 'admin'): ?>
                                            <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">ADMIN LOGISTIK</span>
                                        <?php elseif ($rl === 'supplier'): ?>
                                            <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">PEMILIK LAHAN</span>
                                        <?php else: ?>
                                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">PETANI</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 pr-6 text-right">
                                        <a href="kelola_pengguna.php?delete_id=<?= $row['id'] ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin mencabut permanen hak akses akun ini?');"
                                           class="text-xs font-bold text-red-400 bg-red-500/10 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-xl transition">
                                            <i class="fas fa-trash-can"></i> Cabut Akses
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="p-8 text-center text-slate-500">Tidak ada aktor terdaftar.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="modal-add" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
        <div class="bg-[#111c35] border border-slate-800 w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <h3 class="text-lg font-black text-white"><i class="fas fa-user-plus text-emerald-400 mr-2"></i>Tambah Pengguna Baru</h3>
                <button onclick="toggleModal('modal-add', false)" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="action_type" value="create">
                
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="John Doe" class="w-full p-3.5 bg-slate-800 border border-slate-700 rounded-xl text-sm outline-none text-white focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Email</label>
                    <input type="email" name="email" required placeholder="aktor@panenusa.com" class="w-full p-3.5 bg-slate-800 border border-slate-700 rounded-xl text-sm outline-none text-white focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Password Awal</label>
                    <input type="password" name="password" required placeholder="Min 6 karakter" class="w-full p-3.5 bg-slate-800 border border-slate-700 rounded-xl text-sm outline-none text-white focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Tingkat Hak Akses (Role)</label>
                    <select name="role" required class="w-full p-3.5 bg-slate-800 border border-slate-700 rounded-xl text-sm outline-none text-white focus:border-emerald-500 transition">
                        <option value="user">Petani (User Tani)</option>
                        <option value="supplier">Pemilik Lahan (Supplier/Investor)</option>
                        <option value="admin">Admin Logistik (Staf Lapangan)</option>
                    </select>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="toggleModal('modal-add', false)" class="w-1/2 bg-slate-800 text-slate-300 font-bold p-3.5 rounded-xl text-xs hover:bg-slate-700 transition">Batal</button>
                    <button type="submit" class="w-1/2 bg-emerald-500 text-white font-bold p-3.5 rounded-xl text-xs hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/10">Simpan Akun</button>
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
