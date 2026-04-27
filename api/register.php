<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">
    <div class="bg-slate-800 p-8 rounded-3xl w-full max-w-md shadow-2xl border border-white/10">
        <h1 class="text-2xl font-bold text-white text-center mb-6">Daftar Akun Panenusa</h1>
        
        <form action="/auth/register" method="POST" class="space-y-4">
            <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full p-4 rounded-xl bg-slate-700 text-white border-none outline-none">
            <input type="email" name="email" placeholder="Alamat Email" required class="w-full p-4 rounded-xl bg-slate-700 text-white border-none outline-none">
            <input type="password" name="password" placeholder="Kata Sandi" required class="w-full p-4 rounded-xl bg-slate-700 text-white border-none outline-none">
            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white p-4 rounded-xl font-bold transition">Daftar Sekarang</button>
        </form>
    </div>
</body>
</html>