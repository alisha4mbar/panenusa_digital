<?php
session_start();

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-white text-center mb-6">Login Panenusa</h1>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-400 p-3 rounded mb-4">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="/auth/login" method="POST">
            <div class="mb-4">
                <label class="text-gray-400 block mb-2">Email</label>
                <input type="email" name="email" required class="w-full p-3 rounded bg-gray-700 text-white border border-gray-600">
            </div>
            <div class="mb-6">
                <label class="text-gray-400 block mb-2">Password</label>
                <input type="password" name="password" required class="w-full p-3 rounded bg-gray-700 text-white border border-gray-600">
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white p-3 rounded font-bold">
                Login
            </button>
        </form>
        
        <p class="text-center text-gray-400 mt-4">
            Belum punya akun? <a href="register.php" class="text-green-400">Daftar</a>
        </p>
        
        <div class="mt-6 p-3 bg-gray-700 rounded text-center text-sm text-gray-400">
            <p>Demo: admin@panenusa.com / admin123</p>
            <p>Demo: user@panenusa.com / user123</p>
        </div>
    </div>
</body>
</html>