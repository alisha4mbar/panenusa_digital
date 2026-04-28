<?php
session_start();
include 'config.php';

// Sinkronisasi cookie ke session (wajib untuk Vercel serverless)
if (!isset($_SESSION['user_id']) && isset($_COOKIE['panenusa_auth'])) {
    $data = json_decode($_COOKIE['panenusa_auth'], true);
    if (isset($data['user_id'])) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];
    }
}

// 1. Proteksi: Hanya Admin yang bisa masuk
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: /dashboard");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
$accent = '#6366f1'; // Warna Indigo khas Admin
$gradient = 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)';

// 2. Logika Aksi Moderasi (Terhubung ke Database)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        // Ubah status jadi Approved agar muncul di halaman Forum User
        mysqli_query($conn, "UPDATE forum_posts SET status = 'Approved' WHERE id = $id");
        echo "<script>alert('Postingan disetujui!'); window.location.href='/moderasi_forum';</script>";
    } elseif ($action == 'delete') {
        // Hapus postingan dari database
        mysqli_query($conn, "DELETE FROM forum_posts WHERE id = $id");
        echo "<script>alert('Postingan berhasil dihapus!'); window.location.href='/moderasi_forum';</script>";
    }
}

// 3. Ambil Postingan yang statusnya masih 'Pending'
$query = "SELECT forum_posts.*, users.nama as pembuat 
          FROM forum_posts 
          JOIN users ON forum_posts.user_id = users.id 
          WHERE forum_posts.status = 'Pending' 
          ORDER BY forum_posts.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderasi Forum | Panenusa Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { background-color: #080b14; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0; }
        .card-glass { background: rgba(17, 25, 40, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .btn-action { transition: all 0.2s ease; }
        .btn-action:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Moderasi Forum</h1>
                <p class="text-slate-500 text-sm mt-1">Tinjau dan validasi postingan dari para petani.</p>
            </div>
            
            <div class="flex items-center gap-4 p-2 card-glass rounded-2xl pr-6">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: <?= $gradient ?>">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-xs font-bold text-slate-200"><?= htmlspecialchars($nama) ?></p>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-widest">Administrator</p>
                </div>
            </div>
        </header>

        <div class="card-glass rounded-[2.5rem] overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-500">Pengirim</th>
                        <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-500">Konten Diskusi</th>
                        <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-500 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs">
                                        <?= substr($row['pembuat'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white"><?= htmlspecialchars($row['pembuat']) ?></p>
                                        <p class="text-[10px] text-slate-500"><?= date('d M, H:i', strtotime($row['created_at'])) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <p class="text-sm text-slate-300 leading-relaxed max-w-xl">
                                    <?= htmlspecialchars($row['konten']) ?>
                                </p>
                            </td>
                            <td class="p-6 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="moderasi_forum.php?action=approve&id=<?= $row['id'] ?>" 
                                       class="btn-action w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 flex items-center justify-center hover:bg-emerald-500 hover:text-white"
                                       title="Setujui Postingan">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    
                                    <a href="moderasi_forum.php?action=delete&id=<?= $row['id'] ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')"
                                       class="btn-action w-10 h-10 rounded-xl bg-red-500/10 text-red-500 border border-red-500/20 flex items-center justify-center hover:bg-red-500 hover:text-white"
                                       title="Hapus Postingan">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="p-20 text-center">
                                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-700">
                                    <i class="fas fa-inbox text-2xl"></i>
                                </div>
                                <p class="text-slate-500 font-medium">Tidak ada postingan yang perlu dimoderasi.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>