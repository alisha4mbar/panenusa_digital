<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])) header("Location: login.php");

$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $password_baru = $_POST['password'];

    if (!empty($password_baru)) {
        $hash = password_hash($password_baru, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama='$nama', password='$hash' WHERE id='$user_id'";
    } else {
        $sql = "UPDATE users SET nama='$nama' WHERE id='$user_id'";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['nama'] = $nama; // Update nama di session
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b0f1a; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="card-glass p-8 rounded-[2.5rem] w-full max-w-md">
        <h2 class="text-2xl font-bold text-white mb-6 text-center">Edit Profil</h2>
        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="text-xs text-slate-400 uppercase tracking-widest font-bold">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= $user['nama'] ?>" required 
                       class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3 mt-2 text-white outline-none focus:border-indigo-500 transition">
            </div>
            <div>
                <label class="text-xs text-slate-400 uppercase tracking-widest font-bold">Email (Tetap)</label>
                <input type="text" value="<?= $user['email'] ?>" disabled 
                       class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3 mt-2 text-slate-500 outline-none cursor-not-allowed">
            </div>
            <div>
                <label class="text-xs text-slate-400 uppercase tracking-widest font-bold">Password Baru (Kosongkan jika tidak diganti)</label>
                <input type="password" name="password" 
                       class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3 mt-2 text-white outline-none focus:border-indigo-500 transition">
            </div>
            <div class="flex gap-3 pt-4">
                <a href="dashboard.php" class="flex-1 bg-white/5 hover:bg-white/10 text-center py-4 rounded-2xl font-bold transition">Batal</a>
                <button type="submit" name="update" class="flex-1 bg-indigo-600 hover:bg-indigo-700 py-4 rounded-2xl font-bold transition shadow-lg shadow-indigo-600/20">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>