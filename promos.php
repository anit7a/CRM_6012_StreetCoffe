<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
$days = max(1, min(365, (int) ($_GET['days'] ?? 30)));
$stmt = $pdo->prepare("SELECT c.id,c.name,c.phone,c.points,MAX(t.transaction_date) last_transaction,COUNT(t.id) transaction_count,COALESCE(SUM(t.total_amount),0) total_spend FROM customers c LEFT JOIN transactions t ON t.customer_id=c.id GROUP BY c.id HAVING last_transaction IS NULL OR last_transaction < DATE_SUB(NOW(), INTERVAL ? DAY) ORDER BY last_transaction ASC");
$stmt->execute([$days]);
$rows = $stmt->fetchAll();
$pageTitle = 'Promo Pelanggan Pasif';
require 'includes/header.php';
?>
<div class="welcome-panel promo-panel">
    <div><span class="eyebrow light">CUSTOMER RETENTION</span>
        <h2>Jangkau kembali pelanggan</h2>
        <p>Identifikasi pelanggan yang tidak bertransaksi dalam periode tertentu untuk dipertimbangkan menerima promo
            atau program loyalitas.</p>
    </div>
    <div class="coffee-art">◇</div>
</div>
<form method="get" class="filter-inline mb-3"><label class="form-label mb-0">Tidak transaksi selama</label><select
        class="form-select" name="days">
        <option value="14" <?= $days === 14 ? 'selected' : '' ?>>14 hari</option>
        <option value="30" <?= $days === 30 ? 'selected' : '' ?>>30 hari</option>
        <option value="60" <?= $days === 60 ? 'selected' : '' ?>>60 hari</option>
        <option value="90" <?= $days === 90 ? 'selected' : '' ?>>90 hari</option>
        <option value="180" <?= $days === 180 ? 'selected' : '' ?>>180 hari</option>
    </select><button class="btn btn-coffee">Terapkan</button></form>
<div class="stats-grid">
    <div class="stat-card"><span>Pelanggan berpotensi promo</span><strong><?= count($rows) ?></strong><small>Ambang
            <?= $days ?> hari tanpa transaksi</small></div>
</div>
<div class="table-card mt-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Telepon</th>
                    <th>Transaksi</th>
                    <th>Total pembelian</th>
                    <th>Poin</th>
                    <th>Transaksi terakhir</th>
                </tr>
            </thead>
            <tbody><?php if (!$rows): ?>
                    <tr>
                        <td colspan="6" class="empty-state">Tidak ada pelanggan yang memenuhi kriteria saat ini.</td>
                    </tr><?php endif; ?><?php foreach ($rows as $r): ?>
                    <tr>
                        <td><b><?= e($r['name']) ?></b></td>
                        <td><?= e($r['phone']) ?></td>
                        <td><?= (int) $r['transaction_count'] ?></td>
                        <td><?= rupiah($r['total_spend']) ?></td>
                        <td><?= (int) $r['points'] ?></td>
                        <td><?= $r['last_transaction'] ? date('d M Y', strtotime($r['last_transaction'])) : 'Belum pernah transaksi' ?>
                        </td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="note-panel mt-3">Saran tindak lanjut: admin dapat mempertimbangkan voucher kunjungan kembali, promo menu
    favorit, atau penawaran khusus komunitas. Pengiriman promo dilakukan di luar aplikasi ini.</div>
<?php require 'includes/footer.php'; ?>