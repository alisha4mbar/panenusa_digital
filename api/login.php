<?php
// Jika sudah login, langsung ke dashboard
if (isset($_COOKIE['panenusa_auth'])) {
    header("Location: /dashboard");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">
    <div class="bg-slate-800 p-8 rounded-3xl w-full max-w-md shadow-2xl">
        <h1 class="text-2xl font-bold text-white text-center mb-6">Masuk ke Panenusa</h1>
        <form action="/auth/login" method="POST" class="space-y-4">
            <input type="email" name="email" placeholder="Email" required class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500">
            <input type="password" name="password" placeholder="Password" required class="w-full p-4 rounded-xl bg-slate-700 text-white outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white p-4 rounded-xl font-bold transition">Login</button>
        </form>
    </div>
</body>
</html>