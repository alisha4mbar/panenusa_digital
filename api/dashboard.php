<?php
// Cek jika pengguna sudah login via cookie
if (isset($_COOKIE['panenusa_auth'])) {
    header("Location: /dashboard");
    exit();
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0f172a; }
        .glass-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="glass-card p-8 rounded-[2.5rem] w-full max-w-md shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg shadow-emerald-500/20">
                <i class="fas fa-leaf text-xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Buat Akun Baru</h1>
            <p class="text-slate-400 text-sm mt-2">Mulai perjalanan pertanian digital Anda</p>
        </div>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <span>Email sudah terdaftar atau terjadi kesalahan.</span>
            </div>
        <?php endif; ?>
        
        <form action="/auth/register" method="POST" class="space-y-5">
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="Contoh: Budi Santoso" 
                       class="w-full bg-slate-800/50 border border-slate-700 rounded-2xl px-5 py-4 text-white outline-none focus:border-emerald-500 transition-all mt-2">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Alamat Email</label>
                <input type="email" name="email" required placeholder="nama@email.com" 
                       class="w-full bg-slate-800/50 border border-slate-700 rounded-2xl px-5 py-4 text-white outline-none focus:border-emerald-500 transition-all mt-2">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Kata Sandi</label>
                <input type="password" name="password" required placeholder="••••••••" 
                       class="w-full bg-slate-800/50 border border-slate-700 rounded-2xl px-5 py-4 text-white outline-none focus:border-emerald-500 transition-all mt-2">
            </div>
            
            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-emerald-500/20 transition-all transform hover:-translate-y-1 active:scale-95 mt-4">
                Daftar Sekarang
            </button>
        </form>
        
        <p class="text-center text-slate-400 mt-8 text-sm font-medium">
            Sudah memiliki akun? <a href="/login" class="text-emerald-400 hover:underline">Masuk di sini</a>
        </p>
    </div>
</body>
</html>