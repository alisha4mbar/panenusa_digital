<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Panenusa Pro</title>
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
            width: 100%;
        }
        .input-field:focus {
            outline: none;
            border-color: #10b981;
            background: rgba(255, 255, 255, 0.1);
        }
        .btn-register {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
            width: 100%;
            padding: 1rem;
            border-radius: 1rem;
            font-weight: bold;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
                <i class="fas fa-seedling text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Daftar<span class="text-emerald-400"> Akun</span></h1>
            <p class="text-slate-400">Mulai perjalanan digital pertanian Anda</p>
        </div>

        <!-- Register Form -->
        <div class="glass p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Buat Akun Baru</h2>
            
            <form action="auth.php?action=register" method="POST" class="space-y-5" onsubmit="return validateForm()">
                <div>
                    <label class="text-slate-400 text-sm font-semibold mb-2 block">
                        <i class="fas fa-user mr-2"></i> Nama Lengkap
                    </label>
                    <input type="text" 
                           name="nama" 
                           required 
                           class="input-field"
                           placeholder="Masukkan nama lengkap Anda"
                           minlength="3">
                </div>
                
                <div>
                    <label class="text-slate-400 text-sm font-semibold mb-2 block">
                        <i class="fas fa-envelope mr-2"></i> Alamat Email
                    </label>
                    <input type="email" 
                           name="email" 
                           required 
                           class="input-field"
                           placeholder="contoh@email.com">
                </div>
                
                <div>
                    <label class="text-slate-400 text-sm font-semibold mb-2 block">
                        <i class="fas fa-lock mr-2"></i> Kata Sandi
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           required 
                           class="input-field"
                           placeholder="Minimal 6 karakter"
                           minlength="6">
                    <p class="text-xs text-slate-500 mt-1" id="passwordHint"></p>
                </div>
                
                <div>
                    <label class="text-slate-400 text-sm font-semibold mb-2 block">
                        <i class="fas fa-check-circle mr-2"></i> Konfirmasi Sandi
                    </label>
                    <input type="password" 
                           id="confirm_password"
                           required 
                           class="input-field"
                           placeholder="Ketik ulang kata sandi">
                </div>
                
                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-slate-400 text-sm">
                    Sudah punya akun? 
                    <a href="login.php" class="text-emerald-400 font-semibold hover:underline">Masuk di sini</a>
                </p>
            </div>

            <div class="mt-6 p-3 bg-emerald-500/10 rounded-lg border border-emerald-500/20">
                <p class="text-xs text-emerald-400 text-center">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Pendaftar pertama akan menjadi Administrator
                </p>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            var password = document.getElementById('password').value;
            var confirm = document.getElementById('confirm_password').value;
            
            if (password !== confirm) {
                alert('Konfirmasi kata sandi tidak cocok!');
                return false;
            }
            
            if (password.length < 6) {
                alert('Kata sandi minimal 6 karakter!');
                return false;
            }
            
            return true;
        }
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            var password = this.value;
            var hint = document.getElementById('passwordHint');
            
            if (password.length === 0) {
                hint.innerHTML = '';
            } else if (password.length < 6) {
                hint.innerHTML = '⚠️ Minimal 6 karakter';
                hint.className = 'text-xs text-red-400 mt-1';
            } else {
                hint.innerHTML = '✓ Kata sandi kuat';
                hint.className = 'text-xs text-emerald-400 mt-1';
            }
        });
    </script>
</body>
</html>