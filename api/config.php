<?php
/**
 * Panenusa — config/session.php
 * Session management + role guard, kompatibel dengan cookie panenusa_auth
 */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// Sinkronisasi cookie → session (wajib untuk Vercel serverless)
if (empty($_SESSION['user_id']) && isset($_COOKIE['panenusa_auth'])) {
    $data = json_decode($_COOKIE['panenusa_auth'], true);
    if (!empty($data['user_id'])) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama']    = $data['nama'];
        $_SESSION['role']    = $data['role'];
        $_SESSION['divisi']  = $data['divisi'] ?? '';
        $_SESSION['email']   = $data['email']  ?? '';
    }
}

function requireLogin(?string $role = null): array {
    if (empty($_SESSION['user_id'])) {
        header('Location: /login?msg=expired');
        exit;
    }
    if ($role !== null && ($_SESSION['role'] ?? '') !== $role) {
        redirectToDashboard();
    }
    return [
        'id'     => (int)$_SESSION['user_id'],
        'nama'   => $_SESSION['nama']   ?? '',
        'email'  => $_SESSION['email']  ?? '',
        'role'   => $_SESSION['role']   ?? 'user',
        'divisi' => $_SESSION['divisi'] ?? '',
    ];
}

function redirectToDashboard(): never {
    $map = [
        'superadmin' => '/dashboard/superadmin',
        'admin'      => '/dashboard/admin',
        'supplier'   => '/dashboard/supplier',
        'user'       => '/dashboard/user',
    ];
    header('Location: ' . ($map[$_SESSION['role'] ?? 'user'] ?? '/dashboard/user'));
    exit;
}

function setUserSession(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['divisi']  = $user['divisi'] ?? '';

    // Set cookie 30 hari (backward compat)
    $payload = json_encode([
        'user_id' => $user['id'],
        'nama'    => $user['nama'],
        'role'    => $user['role'],
        'divisi'  => $user['divisi'] ?? '',
        'email'   => $user['email'],
    ]);
    setcookie('panenusa_auth', $payload, time() + 86400 * 30, '/', '', false, true);
}

function isLoggedIn(): bool { return !empty($_SESSION['user_id']); }
function currentRole(): ?string { return $_SESSION['role'] ?? null; }
function currentUser(): array {
    return [
        'id'     => (int)($_SESSION['user_id'] ?? 0),
        'nama'   => $_SESSION['nama']   ?? '',
        'email'  => $_SESSION['email']  ?? '',
        'role'   => $_SESSION['role']   ?? 'user',
        'divisi' => $_SESSION['divisi'] ?? '',
    ];
}