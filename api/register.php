<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md glass p-10 rounded-[2.5rem] shadow-2xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-white">Buat Akun</h2>
            <p class="text-slate-400 text-sm mt-2">Daftar sekarang untuk akses data BPS</p>
        </div>
        <form action="auth.php?action=register" method="POST" class="space-y-5">
            <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white outline-none focus:border-emerald-500 transition">
            <input type="email" name="email" placeholder="Email" required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white outline-none focus:border-emerald-500 transition">
            <input type="password" name="password" placeholder="Password" required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white outline-none focus:border-emerald-500 transition">
            <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition">Daftar Akun</button>
        </form>
        <p class="text-center mt-8 text-slate-400 text-sm">Sudah punya akun? <a href="login.php" class="text-emerald-400 font-bold hover:underline">Login</a></p>
    </div>
</body>
</html>