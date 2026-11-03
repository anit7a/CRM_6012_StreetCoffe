<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
$pageTitle = 'Dashboard';
$customers = (int) $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$transactions = (int) $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$revenue = (float) $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM transactions")->fetchColumn();
$complaints = (int) $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$recent = $pdo->query("SELECT t.transaction_date,t.menu_name,t.total_amount,c.name customer_name FROM transactions t LEFT JOIN customers c ON c.id=t.customer_id ORDER BY t.transaction_date DESC,t.id DESC LIMIT 6")->fetchAll();
require 'includes/header.php';
?>
<div class="welcome-panel">
    <div><span class="eyebrow light">CUSTOMER RELATIONSHIP MANAGEMENT</span>
        <h2>Selamat datang di CRM 6012 Street Coffee</h2>
        <p>Pantau pelanggan, transaksi, loyalitas, dan masukan pelanggan dalam satu tempat.</p>
    </div>
    <div class="coffee-art">☕</div>
</div>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">♙</div><span>Total
            Pelanggan</span><strong><?= number_format($customers) ?></strong><small>Data pelanggan terdaftar</small>
    </div>
    <div class="stat-card">
        <div class="stat-icon">▤</div><span>Total
            Transaksi</span><strong><?= number_format($transactions) ?></strong><small>Seluruh transaksi
            tersimpan</small>
    </div>
    <div class="stat-card">
        <div class="stat-icon">Rp</div><span>Total Pendapatan</span><strong
            class="stat-money"><?= rupiah($revenue) ?></strong><small>Akumulasi pembayaran</small>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✉</div><span>Keluhan &
            Saran</span><strong><?= number_format($complaints) ?></strong><small>Masukan yang tercatat</small>
    </div>
</div>
<div class="section-heading">
    <div>
        <h3>Aktivitas transaksi terbaru</h3>
        <p>Catatan transaksi yang baru dimasukkan.</p>
    </div><a class="btn btn-coffee" href="transactions.php">+ Catat transaksi</a>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Menu</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$recent): ?>
                    <tr>
                        <td colspan="4" class="empty-state">Belum ada transaksi. Mulai dengan mencatat transaksi pelanggan.
                        </td>
                    </tr><?php endif; ?>
                <?php foreach ($recent as $r): ?>
                    <tr>
                        <td><b><?= e($r['customer_name'] ?? 'Pelanggan umum') ?></b></td>
                        <td><?= e($r['menu_name']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($r['transaction_date'])) ?></td>
                        <td class="fw-semibold"><?= rupiah($r['total_amount']) ?></td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require 'includes/footer.php'; ?>