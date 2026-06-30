<?php
ob_start();
session_start(); 
require_once __DIR__ . '/config.php';

// BYPASS INTERCEPTOR
if (isset($_GET['bypass']) && $_GET['bypass'] === 'true') {
    session_unset();
    setcookie('panenusa_auth', '', time() - 3600, '/');
}

$error  = '';
$fields = ['nama' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $role     = $_POST['role'] ?? 'user'; // Mengambil nilai role langsung dari input Form manual
    
    $fields   = ['nama' => $nama, 'email' => $email];

    if (empty($nama) || empty($email) || empty($password) || empty($role)) { 
        $error = 'Semua kolom wajib diisi termasuk pilihan peran.'; 
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

            // Prepared Statement untuk cek duplikasi email
            $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
            mysqli_stmt_bind_param($check_stmt, "s", $email);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error = 'Email sudah terdaftar. Silakan gunakan email lain atau langsung masuk.';
                mysqli_stmt_close($check_stmt);
            } else {
                mysqli_stmt_close($check_stmt);

                $hash_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert data pendaftaran dengan role yang dipilih user
                $insert_stmt = mysqli_prepare($conn, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($insert_stmt, "ssss", $nama, $email, $hash_password, $role);
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    mysqli_stmt_close($insert_stmt);
                    
                    $role_assigned = strtoupper($role === 'user' ? 'PETANI' : ($role === 'supplier' ? 'PEMILIK LAHAN' : 'ADMIN LOGISTIK'));
                    
                    // Bersihkan session sisa agar tidak otomatis login bypass ke dashboard lama
                    session_unset();
                    if (ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000,
                            $params["path"], $params["domain"],
                            $params["secure"], $params["httponly"]
                        );
                    }
                    session_destroy();
                    
                    // Set flash message sukses pendaftaran
                    session_start();
                    $_SESSION['reg_success_flash'] = "Registrasi sukses! Akun berhasil dibuat dengan hak akses: " . $role_assigned;
                    
                    ob_end_clean();
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
    <title>Daftar Akun Otoritas | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#0b1224] flex items-center justify-center min-h-screen p-4 font-sans">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-3 mb-2">
                <div class="w-11 h-11 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-md">
                    <i class="fas fa-leaf text-white text-lg"></i>
                </div>
                <span class="text-2xl font-black text-white">Panenusa</span>
            </div>
            <p class="text-slate-400 text-xs">Pendaftaran Akun Terintegrasi Kredensial Multi-Role</p>
        </div>

        <div class="bg-[#111c35] p-8 rounded-[2rem] shadow-2xl border border-slate-800/80">
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
                           class="w-full p-4 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm outline-none focus:border-emerald-500 transition" required>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($fields['email']) ?>" placeholder="email@contoh.com"
                           class="w-full p-4 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm outline-none focus:border-emerald-500 transition" required>
                </div>
                
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Daftar Sebagai (Hak Akses Peran)</label>
                    <select name="role" required class="w-full p-4 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm outline-none focus:border-emerald-500 transition appearance-none">
                        <option value="user" selected>Petani (Input Timbangan Hasil Kebun)</option>
                        <option value="supplier">Pemilik Lahan / Investor (Pantau Tren Grafik)</option>
                        <option value="admin">Admin Logistik (Otoritas Kliring Sortir)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="pwd" name="password" placeholder="Min. 6 karakter"
                               class="w-full p-4 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm outline-none focus:border-emerald-500 transition pr-12" required>
                        <button type="button" onclick="togglePwd('pwd','eye1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                            <i class="fas fa-eye" id="eye1"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Konfirmasi Password</label>
                    <input type="password" name="confirm" placeholder="Ulangi password"
                           class="w-full p-4 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm outline-none focus:border-emerald-500 transition" required>
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white p-4 rounded-xl font-bold transition text-sm shadow-lg shadow-emerald-500/20 pt-3.5">
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
