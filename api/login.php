<?php
ob_start();
// Mengarahkan include langsung ke config.php yang berada di folder yang sama (api/)
require_once __DIR__ . '/config.php';

// Jika session user_id sudah ada, langsung lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$msg   = '';

// Menangkap parameter status dari URL
$status = isset($_GET['status']) ? (string)$_GET['status'] : '';
$msg_type = isset($_GET['msg']) ? (string)$_GET['msg'] : '';

if ($status === 'reg_success') {
    $msg = 'Registrasi berhasil! Silakan login.';
} elseif ($status === 'login_failed') {
    $error = 'Email atau Password salah!';
} elseif ($msg_type === 'expired') {
    $msg = 'Sesi habis. Silakan login kembali.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW) ?? '');

    if (!$email || !$password) {
        $error = 'Email dan password wajib diisi.';
    } else {
        try {
            $email_clean = mysqli_real_escape_string($conn, $email);
            
            // Query menggunakan mysqli sesuai dengan config.php
            $query = "SELECT * FROM users WHERE email = '$email_clean' LIMIT 1";
            $result = mysqli_query($conn, $query);

            if ($result && $user = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $user['password'])) {
                    
                    // Set Session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nama']    = $user['nama'];
                    $_SESSION['role']    = $user['role'];

                    // Set Cookie untuk Vercel Serverless State
                    $userData = [
                        'user_id' => $user['id'],
                        'nama'    => $user['nama'],
                        'role'    => $user['role']
                    ];
                    setcookie('panenusa_auth', json_encode($userData), time() + (86400 * 30), "/", "", false, true);

                    ob_end_clean();
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = 'Email atau Password salah!';
                }
            } else {
                $error = 'Email atau Password salah!';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan sistem. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Panenusa</title>
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
            <h1 class="text-2xl font-bold text-white text-center mb-6">Masuk ke Akun</h1>

            <?php if ($msg): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl mb-5 text-sm">
                <i class="fas fa-info-circle mr-2"></i><?= htmlspecialchars($msg) ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-5 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4" novalidate>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" placeholder="email@contoh.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 focus:border-emerald-500 transition" required>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="pwd" name="password" placeholder="••••••••"
                               class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500 border border-slate-600 focus:border-emerald-500 transition pr-12" required>
                        <button type="button" onclick="togglePwd()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white p-4 rounded-xl font-bold transition shadow-lg shadow-emerald-500/20 mt-2">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </button>
            </form>

            <p class="text-center text-slate-400 mt-6 text-sm">
                Belum punya akun? <a href="register.php" class="text-emerald-400 font-bold hover:text-emerald-300 transition">Daftar Sekarang</a>
            </p>
        </div>

        <p class="text-center text-slate-600 text-xs mt-6">© <?= date('Y') ?> Panenusa. Platform Pangan Digital.</p>
    </div>

<script>
function togglePwd() {
    const f = document.getElementById('pwd');
    const i = document.getElementById('eyeIcon');
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
</body>
</html>