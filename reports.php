<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
$customers = (int) $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$tx = (int) $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$revenue = (float) $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM transactions")->fetchColumn();
$points = (int) $pdo->query("SELECT COALESCE(SUM(points_awarded),0) FROM transactions")->fetchColumn();
$complaints = $pdo->query("SELECT category,status,COUNT(*) total FROM complaints GROUP BY category,status ORDER BY category,status")->fetchAll();
$activity = $pdo->query("SELECT c.id,c.name,COUNT(t.id) transaction_count,COALESCE(SUM(t.total_amount),0) total_spend,COALESCE(SUM(t.points_awarded),0) earned_points,MAX(t.transaction_date) last_transaction FROM customers c LEFT JOIN transactions t ON t.customer_id=c.id GROUP BY c.id ORDER BY total_spend DESC LIMIT 15")->fetchAll();
$pageTitle = 'Laporan';
require 'includes/header.php';
?>
<div class="section-heading">
    <div>
        <h3>Ringkasan laporan CRM</h3>
        <p>Rekap data berdasarkan seluruh transaksi dan masukan yang tersimpan.</p>
    </div><button class="btn btn-outline-coffee" onclick="window.print()">Cetak laporan</button>
</div>
<div class="stats-grid">
    <div class="stat-card"><span>Jumlah pelanggan</span><strong><?= $customers ?></strong></div>
    <div class="stat-card"><span>Jumlah transaksi</span><strong><?= $tx ?></strong></div>
    <div class="stat-card"><span>Total pendapatan</span><strong class="stat-money"><?= rupiah($revenue) ?></strong>
    </div>
    <div class="stat-card"><span>Total poin diperoleh</span><strong><?= number_format($points) ?></strong></div>
</div>
<div class="section-heading mt-4">
    <div>
        <h3>Rekap keluhan dan saran</h3>
        <p>Jumlah masukan berdasarkan kategori dan status.</p>
    </div>
</div>
<div class="table-card mb-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody><?php if (!$complaints): ?>
                    <tr>
                        <td colspan="3" class="empty-state">Belum ada data keluhan dan saran.</td>
                    </tr><?php endif; ?><?php foreach ($complaints as $c): ?>
                    <tr>
                        <td><?= e($c['category']) ?></td>
                        <td><?= e($c['status']) ?></td>
                        <td><?= (int) $c['total'] ?></td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="section-heading">
    <div>
        <h3>Aktivitas pelanggan</h3>
        <p>15 pelanggan dengan nilai pembelian tertinggi.</p>
    </div>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Transaksi</th>
                    <th>Total pembelian</th>
                    <th>Poin diperoleh</th>
                    <th>Terakhir transaksi</th>
                </tr>
            </thead>
            <tbody><?php foreach ($activity as $a): ?>
                    <tr>
                        <td><?= e($a['name']) ?></td>
                        <td><?= (int) $a['transaction_count'] ?></td>
                        <td><?= rupiah($a['total_spend']) ?></td>
                        <td><?= (int) $a['earned_points'] ?></td>
                        <td><?= $a['last_transaction'] ? date('d M Y', strtotime($a['last_transaction'])) : '-' ?></td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require 'includes/footer.php'; ?>