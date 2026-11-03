<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header("Location: {$url}"); exit; }
function is_logged_in(): bool { return isset($_SESSION['user_id']); }
function require_login(): void { if (!is_logged_in()) redirect('login.php'); }
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419); exit('Permintaan tidak valid. Muat ulang halaman dan coba lagi.');
        }
    }
}
function flash(string $message, string $type = 'success'): void { $_SESSION['flash'] = ['message'=>$message, 'type'=>$type]; }
function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash']; unset($_SESSION['flash']);
        echo '<div class="alert alert-'.e($f['type']).' alert-dismissible fade show">'.e($f['message']).'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}
function rupiah($number): string { return 'Rp ' . number_format((float)$number, 0, ',', '.'); }
function active_page(string $page): string { return basename($_SERVER['PHP_SELF']) === $page ? 'active' : ''; }
