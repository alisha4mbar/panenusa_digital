<?php
session_start();
require_once 'config.php';

if (!isset($_COOKIE['panenusa_auth'])) {
    header("Location: /login");
    exit();
}

// Sinkronisasi cookie ke session (wajib untuk Vercel serverless)
if (!isset($_SESSION['user_id']) && isset($_COOKIE['panenusa_auth'])) {
    $data = json_decode($_COOKIE['panenusa_auth'], true);
    if (isset($data['user_id'])) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];
    }
}

$auth = json_decode($_COOKIE['panenusa_auth'], true);
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: /dashboard");
    exit();
}

$nama = $_SESSION['nama'];
$role = $_SESSION['role'];

// Ambil data user dari database
$query = mysqli_query($conn, "SELECT id, nama, email, role FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Panenusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex h-screen bg-gray-50">
    <?php include 'sidebar.php'; ?> 

    <main class="flex-1 p-10 overflow-y-auto">
        <h2 class="text-3xl font-black text-gray-900 mb-8">Manajemen Pengguna</h2>
        <div class="bg-white rounded-[40px] p-8 shadow-sm border border-gray-100">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 text-xs uppercase border-b border-gray-50">
                        <th class="pb-4">Nama</th>
                        <th class="pb-4">Email</th>
                        <th class="pb-4">Role</th>
                        <th class="pb-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($query)): ?>
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="py-4 font-bold"><?php echo $user['nama']; ?></td>
                        <td class="py-4 text-gray-500"><?php echo $user['email']; ?></td>
                        <td class="py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold <?php echo $user['role'] == 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <button class="text-emerald-600 font-bold text-sm">Edit</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>