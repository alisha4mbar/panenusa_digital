<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #0f172a; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md glass p-10 rounded-[2.5rem] shadow-2xl text-center">
        <div class="w-16 h-16 bg-emerald-600/20 text-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl">
            <i class="fas fa-seedling"></i>
        </div>
        <h2 class="text-3xl font-bold text-white mb-6">Selamat Datang</h2>
        <form action="auth.php?action=login" method="POST" class="space-y-5 text-left">
            <input type="email" name="email" placeholder="Email" required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white outline-none focus:border-emerald-500 transition">
            <input type="password" name="password" placeholder="Password" required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white outline-none focus:border-emerald-500 transition">
            <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-bold hover:bg-emerald-700 transition">Masuk ke Dashboard</button>
        </form>
        <p class="mt-8 text-slate-400 text-sm">Belum punya akun? <a href="register.php" class="text-emerald-400 font-bold hover:underline">Daftar</a></p>
    </div>
</body>
</html>