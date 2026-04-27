<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Panenusa Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { 
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
        }
        .input-field {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            color: white;
            transition: all 0.3s ease;
        }
        .input-field:focus {
            outline: none;
            border-color: #10b981;
            background: rgba(255, 255, 255, 0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
                <i class="fas fa-leaf text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Panenusa<span class="text-emerald-400">.pro</span></h1>
            <p class="text-slate-400">Platform Digital untuk Petani Indonesia</p>
        </div>

        <!-- Login Form -->
        <div class="glass p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Masuk ke Akun</h2>
            
            <form action="auth.php?action=login" method="POST" class="space-y-5">
                <div>
                    <label class="text-slate-400 text-sm font-semibold mb-2 block">Alamat Email</label>
                    <input type="email" 
                           name="email" 
                           required 
                           class="input-field w-full"
                           placeholder="contoh@email.com">
                </div>
                
                <div>
                    <label class="text-slate-400 text-sm font-semibold mb-2 block">Kata Sandi</label>
                    <input type="password" 
                           name="password" 
                           required 
                           class="input-field w-full"
                           placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn-login w-full py-4 rounded-xl font-bold text-white text-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-slate-400 text-sm">
                    Belum punya akun? 
                    <a href="register.php" class="text-emerald-400 font-semibold hover:underline">Daftar Sekarang</a>
                </p>
            </div>

            <!-- Demo Credentials -->
            <div class="mt-8 p-4 bg-white/5 rounded-xl border border-white/10">
                <p class="text-xs text-slate-500 text-center mb-2">🔐 Akun Demo</p>
                <div class="text-xs text-slate-400 space-y-1">
                    <p>📧 Admin: <span class="text-emerald-400">admin@panenusa.com</span> | 🔑 admin123</p>
                    <p>📧 User: <span class="text-emerald-400">user@panenusa.com</span> | 🔑 user123</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto redirect jika sudah login
        <?php
        session_start();
        if (isset($_SESSION['user_id'])) {
            echo "window.location.href='dashboard.php';";
        }
        ?>
    </script>
</body>
</html>