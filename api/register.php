<?php
ob_start();

// PERBAIKAN JALUR: Mengarah langsung ke config.php yang ada di folder api/
require_once __DIR__ . '/config.php';

// Cek status login dari session
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error  = '';
$fields = ['nama'=>'','email'=>'','role'=>'user','divisi'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim(filter_input(INPUT_POST,'nama',    FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
    $email    = trim(filter_input(INPUT_POST,'email',   FILTER_SANITIZE_EMAIL)         ?? '');
    $password = trim(filter_input(INPUT_POST,'password',FILTER_UNSAFE_RAW)             ?? '');
    $confirm  = trim(filter_input(INPUT_POST,'confirm', FILTER_UNSAFE_RAW)             ?? '');
    $role     = filter_input(INPUT_POST,'role',  FILTER_SANITIZE_SPECIAL_CHARS) ?? 'user';
    $divisi   = filter_input(INPUT_POST,'divisi',FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
    $fields   = compact('nama','email','role','divisi');

    // Penyelarasan role dengan huruf kecil agar pas dengan ENUM database TiDB
    $allowedRoles = ['user','supplier','admin'];
    if (!in_array($role, $allowedRoles, true)) $role = 'user';

    if (!$nama || !$email || !$password) { 
        $error = 'Semua kolom wajib diisi.'; 
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $error = 'Format email tidak valid.'; 
    } elseif (strlen($password) < 6) { 
        $error = 'Password minimal 6 karakter.'; 
    } elseif ($password !== $confirm) { 
        $error = 'Konfirmasi password tidak cocok.'; 
    } else {
        try {
            // Validasi koneksi database murni menggunakan MySQLi dari config.php
            if (!$conn) {
                throw new Exception('Koneksi database ke TiDB Cloud terputus.');
            }

            $email_clean = mysqli_real_escape_string($conn, $email);
            
            // Cek apakah email sudah digunakan
            $check_query = "SELECT id FROM users WHERE email='$email_clean' LIMIT 1";
            $check_result = mysqli_query($conn, $check_query);
            
            if ($check_result && mysqli_num_rows($check_result) > 0) {
                $error = 'Email sudah terdaftar.';
            } else {
                // Ambil total user untuk menentukan Admin pertama otomatis
                $res_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
                $row_count = mysqli_fetch_assoc($res_count);
                if ((int)$row_count['total'] === 0) {
                    $role = 'admin';
                }

                $hash_password = password_hash($password, PASSWORD_DEFAULT);
                $nama_clean    = mysqli_real_escape_string($conn, $nama);
                $role_clean    = mysqli_real_escape_string($conn, $role);
                $divisi_clean  = $divisi ? "'" . mysqli_real_escape_string($conn, $divisi) . "'" : "NULL";

                // Query Insert murni MySQLi
                $insert_query = "INSERT INTO users (nama, email, password, role, divisi) VALUES ('$nama_clean', '$email_clean', '$hash_password', '$role_clean', $divisi_clean)";
                
                if (mysqli_query($conn, $insert_query)) {
                    ob_end_clean();
                    header('Location: login.php?status=reg_success');
                    exit();
                } else {
                    $error = 'Gagal menyimpan data ke database online.';
                }
            }
        } catch (Exception $e) { 
            $error = 'Terjadi kesalahan sistem: ' . $e->getMessage(); 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-leaf text-white text-xl"></i>
                </div>
                <span class="text-3xl font-black text-white">Panenusa</span>
            </div>
            <p class="text-slate-400 text-sm">Platform Pangan Digital Indonesia</p>
        </div>

        <div class="bg-slate-800 p-8 rounded-3xl shadow-2xl border border-slate-700/50">
            <h1 class="text-2xl font-bold text-white text-center mb-6">Buat Akun Baru</h1>

            <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-5 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4" novalidate>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($fields['nama']) ?>" placeholder="John Doe"
                           class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition" required>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($fields['email']) ?>" placeholder="email@contoh.com"
                           class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition" required>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="pwd" name="password" placeholder="Min. 6 karakter"
                               class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition pr-12" required>
                        <button type="button" onclick="togglePwd('pwd','eye1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                            <i class="fas fa-eye" id="eye1"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Konfirmasi Password</label>
                    <input type="password" name="confirm" placeholder="Ulangi password"
                           class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition" required>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Daftar Sebagai</label>
                    <select name="role" id="roleSelect" onchange="toggleDivisi()"
                            class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition">
                        <option value="user"     <?= $fields['role']==='user'    ?'selected':'' ?>>Pembeli (User Umum)</option>
                        <option value="supplier" <?= $fields['role']==='supplier'?'selected':'' ?>>Supplier / Petani</option>
                        <option value="admin"    <?= $fields['role']==='admin'   ?'selected':'' ?>>Admin Operasional</option>
                    </select>
                </div>
                <div id="divisiGroup" class="hidden">
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Divisi Admin</label>
                    <select name="divisi"
                            class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition">
                        <option value="cs"       <?= $fields['divisi']==='cs'      ?'selected':'' ?>>Customer Service</option>
                        <option value="gudang"   <?= $fields['divisi']==='gudang'  ?'selected':'' ?>>Gudang</option>
                        <option value="logistik" <?= $fields['divisi']==='logistik'?'selected':'' ?>>Logistik / Pengiriman</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white p-4 rounded-xl font-bold transition shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </button>
            </form>

            <p class="text-center text-slate-400 mt-6 text-sm">
                Sudah punya akun? <a href="login.php" class="text-emerald-400 font-bold hover:text-emerald-300 transition">Masuk</a>
            </p>
        </div>
    </div>

<script>
function togglePwd(id,iconId){const f=document.getElementById(id);const i=document.getElementById(iconId);f.type=f.type==='password'?'text':'password';i.className=f.type==='password'?'fas fa-eye':'fas fa-eye-slash';}
function toggleDivisi(){document.getElementById('divisiGroup').classList.toggle('hidden',document.getElementById('roleSelect').value!=='admin');}
toggleDivisi();
</script>
</body>
</html>