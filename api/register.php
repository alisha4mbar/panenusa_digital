<?php
ob_start();
session_start(); // Pastikan session dimulai jika ingin menggunakan flash message/session bypass
require_once __DIR__ . '/config.php';

// BYPASS INTERCEPTOR: Dinonaktifkan permanen agar mempermudah proses testing antrean rotasi akun baru
if (isset($_GET['bypass']) && $_GET['bypass'] === 'true') {
    session_unset();
    setcookie('panenusa_auth', '', time() - 3600, '/');
}

$error  = '';
$fields = ['nama' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Perbaikan Sanitasi: Gunakan FILTER_DEFAULT untuk string mentah, lalu trim. 
    // Menggunakan FILTER_SANITIZE_SPECIAL_CHARS pada email akan merusak karakter valid email.
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    
    $fields   = ['nama' => $nama, 'email' => $email];

    if (empty($nama) || empty($email) || empty($password)) { 
        $error = 'Semua kolom wajib diisi.'; 
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $error = 'Format email tidak valid.'; 
    } elseif (strlen($password) < 6) { 
        $error = 'Password minimal 6 karakter.'; 
    } elseif ($password !== $confirm) { 
        $error = 'Konfirmasi password tidak cocok.'; 
    } else {
        try {
            if (!$conn) {
                throw new Exception('Koneksi database ke TiDB Cloud terputus.');
            }

            // AMAN & AKURAT: Menggunakan Prepared Statement untuk cek email menghindari distorsi karakter
            $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
            mysqli_stmt_bind_param($check_stmt, "s", $email);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error = 'Email sudah terdaftar.';
                mysqli_stmt_close($check_stmt);
            } else {
                mysqli_stmt_close($check_stmt);

                // Penentuan Role Berdasarkan Urutan Pendaftaran Modulus Berputar (3 Role)
                $res_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
                if (!$res_count) {
                    throw new Exception('Gagal menghitung jumlah antrean user.');
                }
                
                $row_count = mysqli_fetch_assoc($res_count);
                $current_total = (int)$row_count['total'];
                $modulus = $current_total % 3;

                if ($modulus === 0) {
                    $role = 'admin';
                } elseif ($modulus === 1) {
                    $role = 'user'; // Sesuai dengan value database standar Anda
                } else {
                    $role = 'supplier';
                }

                $hash_password = password_hash($password, PASSWORD_DEFAULT);

                // AMAN: Menggunakan Prepared Statement untuk Insert Data
                $insert_stmt = mysqli_prepare($conn, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($insert_stmt, "ssss", $nama, $email, $hash_password, $role);
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    mysqli_stmt_close($insert_stmt);
                    ob_end_clean();
                    
                    $_SESSION['reg_success_flash'] = "Registrasi sukses! Akun Anda otomatis mendapatkan akses: " . strtoupper($role);
                    header('Location: login.php');
                    exit();
                } else {
                    $error = 'Gagal menyimpan data pendaftaran ke database.';
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
    <title>Daftar Akun Otomatis | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-3 mb-2">
                <div class="w-11 h-11 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-md">
                    <i class="fas fa-leaf text-white text-lg"></i>
                </div>
                <span class="text-2xl font-black text-white">Panenusa</span>
            </div>
            <p class="text-slate-400 text-xs">Assignment Role Otomatis Sesuai Antrean Registrasi</p>
        </div>

        <div class="bg-slate-800 p-8 rounded-3xl shadow-2xl border border-slate-700/50">
            <h1 class="text-xl font-bold text-white text-center mb-6">Buat Akun</h1>

            <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-5 text-xs">
                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4" novalidate>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($fields['nama']) ?>" placeholder="John Doe"
                           class="w-full p-4 rounded-xl bg-slate-700 text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition" required>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($fields['email']) ?>" placeholder="email@contoh.com"
                           class="w-full p-4 rounded-xl bg-slate-700 text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition" required>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="pwd" name="password" placeholder="Min. 6 karakter"
                               class="w-full p-4 rounded-xl bg-slate-700 text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition pr-12" required>
                        <button type="button" onclick="togglePwd('pwd','eye1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                            <i class="fas fa-eye" id="eye1"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Konfirmasi Password</label>
                    <input type="password" name="confirm" placeholder="Ulangi password"
                           class="w-full p-4 rounded-xl bg-slate-700 text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 transition" required>
                </div>

                <div class="p-3 bg-emerald-500/10 rounded-xl border border-emerald-500/20 text-[11px] text-emerald-400">
                    <i class="fas fa-info-circle mr-1"></i> Sistem menentukan akses otomatis: **Admin**, **User**, atau **Supplier**.
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white p-4 rounded-xl font-bold transition text-sm shadow-lg shadow-emerald-500/20">
                    Daftar Akun Sekarang
                </button>
            </form>

            <p class="text-center text-slate-400 mt-6 text-xs">
                Sudah punya akun? <a href="login.php?bypass=true" class="text-emerald-400 font-bold hover:text-emerald-300 transition">Masuk</a>
            </p>
        </div>
    </div>

<script>
function togglePwd(id,iconId){const f=document.getElementById(id);const i=document.getElementById(iconId);f.type=f.type==='password'?'text':'password';i.className=f.type==='password'?'fas fa-eye':'fas fa-eye-slash';}
</script>
</body>
</html>
