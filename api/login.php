<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Panenusa Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            font-family: 'Inter', sans-serif;
        }
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
        }
        .input-field {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1rem;
            padding: 0.9rem 1.2rem;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }
        .input-field:focus {
            outline: none;
            border-color: #10b981;
            background: rgba(255, 255, 255, 0.12);
        }
        .btn-login {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            width: 100%;
            padding: 0.9rem;
            border-radius: 1rem;
            font-weight: 600;
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #fca5a5;
            padding: 0.75rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .success-message {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.5);
            color: #6ee7b7;
            padding: 0.75rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
                <i class="fas fa-leaf text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Panenusa<span class="text-emerald-400">.pro</span></h1>
            <p class="text-slate-400">Platform Digital untuk Petani Indonesia</p>
        </div>

        <div class="glass p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Masuk ke Akun</h2>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle mr-2"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <form action="auth.php?action=login" method="POST" class="space-y-5">
                <div>
                    <label class="text-slate-400 text-sm font-semibold mb-2 block">Alamat Email</label>
                    <input type="email" name="email" required class="input-field" placeholder="admin@panenusa.com" autocomplete="email">
                </div>
                
                <div>
                    <label class="text-slate-400 text-sm font-semibold mb-2 block">Kata Sandi</label>
                    <input type="password" name="password" required class="input-field" placeholder="••••••••" autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-slate-400 text-sm">
                    Belum punya akun? 
                    <a href="register.php" class="text-emerald-400 font-semibold hover:underline">Daftar Sekarang</a>
                </p>
            </div>

            <div class="mt-8 p-4 bg-white/5 rounded-xl border border-white/10">
                <p class="text-xs text-slate-500 text-center mb-2">🔐 Akun Demo</p>
                <div class="text-xs text-slate-400 space-y-1 text-center">
                    <p>Admin: <span class="text-emerald-400">admin@panenusa.com</span> / admin123</p>
                    <p>User: <span class="text-emerald-400">user@panenusa.com</span> / user123</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>