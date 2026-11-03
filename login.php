<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
if (is_logged_in())
    redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id,name,username,password FROM users WHERE username=? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        redirect('index.php');
    } else
        $error = 'Username atau password tidak sesuai.';
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | CRM 6012 Street Coffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>

<body class="login-body">
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-mark large">6012</div><span>STREET COFFEE</span>
        </div>
        <h1>Selamat datang</h1>
        <p class="muted">Masuk untuk mengelola hubungan pelanggan.</p>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label
                class="form-label">Username</label><input class="form-control mb-3" name="username" required autofocus
                placeholder="Masukkan username"><label class="form-label">Password</label><input
                class="form-control mb-4" type="password" name="password" required
                placeholder="Masukkan password"><button class="btn btn-coffee w-100">Masuk ke Dashboard</button></form>