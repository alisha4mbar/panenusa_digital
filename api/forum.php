<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

$role = $_SESSION['role'] ?? 'User';
$user_id = $_SESSION['user_id'];

// Logika Simpan Postingan ke Database
if (isset($_POST['submit_post'])) {
    $konten = mysqli_real_escape_string($conn, $_POST['konten']);
    // Status default adalah 'Pending' agar divalidasi admin
    $query = "INSERT INTO forum_posts (user_id, konten, status, created_at) VALUES ('$user_id', '$konten', 'Pending', NOW())";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Postingan dikirim! Menunggu moderasi admin.'); window.location.href='/forum';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Forum Diskusi | Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #080b14; color: #e2e8f0; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">
    <?php include 'sidebar'; ?>

    <main class="flex-1 overflow-y-auto p-12">
        <h2 class="text-3xl font-bold mb-8">Forum Diskusi Petani</h2>
        
        <div class="card-glass p-6 rounded-3xl mb-8">
            <form method="POST">
                <textarea name="konten" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:outline-none focus:border-emerald-500" placeholder="Apa yang ingin Anda tanyakan atau bagikan hari ini?" rows="3" required></textarea>
                <button type="submit" name="submit_post" class="mt-4 px-6 py-2 bg-emerald-500 text-white rounded-xl font-bold hover:bg-emerald-600 transition">
                    Kirim Postingan
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <h3 class="text-slate-400 font-bold uppercase text-xs tracking-widest">Diskusi Terbaru</h3>
            <?php
            $sql = "SELECT forum_posts.*, users.nama FROM forum_posts JOIN users ON forum_posts.user_id = users.id WHERE status = 'Approved' ORDER BY created_at DESC";
            $res = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($res)):
            ?>
            <div class="card-glass p-6 rounded-3xl">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-emerald-500/20 text-emerald-500 rounded-full flex items-center justify-center font-bold text-xs">
                        <?= substr($row['nama'], 0, 1) ?>
                    </div>
                    <span class="font-bold text-sm"><?= $row['nama'] ?></span>
                    <span class="text-[10px] text-slate-500"><?= $row['created_at'] ?></span>
                </div>
                <p class="text-slate-300 text-sm"><?= $row['konten'] ?></p>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>