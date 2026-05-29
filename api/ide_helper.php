<?php
/**
 * IDE Helper — Panenusa
 * File ini HANYA untuk membantu Intelephense/VSCode mengenali fungsi-fungsi global.
 * File ini TIDAK dieksekusi oleh PHP dan TIDAK di-deploy ke Vercel.
 * Tambahkan ke .vercelignore agar tidak ikut deploy.
 */
if (false) {

    // ── Dari config/db.php ────────────────────────────────────────────
    function getDB(): PDO
    {
        return new PDO('sqlite::memory:'); // dummy return
    }

    function initSchema(): void
    {
    }

    function logActivity(int $userId, string $aksi, string $detail = ''): void
    {
    }

    function kirimNotifikasi(int $userId, string $judul, string $pesan, string $tipe = 'info'): void
    {
    }

    // ── Dari config/session.php ───────────────────────────────────────
    function isLoggedIn(): bool
    {
        return false;
    }

    function requireLogin(?string $role = null): array
    {
        return [];
    }

    function redirectToDashboard(): never
    {
        exit;
    }

    function setUserSession(array $user): void
    {
    }

    function currentRole(): ?string
    {
        return null;
    }

    function currentUser(): array
    {
        return [];
    }

}