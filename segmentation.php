<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
$rows = $pdo->query("SELECT c.id,c.name,c.phone,c.member_status,c.points,COUNT(t.id) transaction_count,COALESCE(SUM(t.total_amount),0) total_spend,MAX(t.transaction_date) last_transaction FROM customers c LEFT JOIN transactions t ON t.customer_id=c.id GROUP BY c.id ORDER BY transaction_count DESC,total_spend DESC")->fetchAll();
$counts = ['Loyal' => 0, 'Aktif' => 0, 'Sesekali' => 0, 'Belum bertransaksi' => 0];
foreach ($rows as &$r) {
    $n = (int) $r['transaction_count'];
    $r['segment'] = $n >= 10 ? 'Loyal' : ($n >= 4 ? 'Aktif' : ($n >= 1 ? 'Sesekali' : 'Belum bertransaksi'));
    $counts[$r['segment']]++;
}
unset($r);
$pageTitle = 'Segmentasi Pelanggan';
require 'includes/header.php';
?>
<div class="section-heading">
    <div>
        <h3>Segmentasi berdasarkan aktivitas</h3>
        <p>Kategori dihitung otomatis dari jumlah transaksi yang tersimpan.</p>
    </div>
</div>
<div class="stats-grid segment-stats"><?php foreach ($counts as $name => $count): ?>
        <div class="stat-card compact"><span><?= e($name) ?></span><strong><?= $count ?></strong><small>pelanggan</small>
        </div><?php endforeach; ?>
</div>
<div class="note-panel"><b>Aturan segmentasi:</b> Loyal ≥ 10 transaksi · Aktif 4–9 transaksi · Sesekali 1–3 transaksi ·
    Belum bertransaksi = 0 transaksi.</div>
<div class="table-card mt-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Jumlah transaksi</th>
                    <th>Total pembelian</th>
                    <th>Poin</th>
                    <th>Transaksi terakhir</th>
                    <th>Segmen</th>
                </tr>
            </thead>
            <tbody><?php foreach ($rows as $r): ?>
                    <tr>
                        <td><b><?= e($r['name']) ?></b><small class="d-block text-muted"><?= e($r['phone']) ?></small></td>
                        <td><?= (int) $r['transaction_count'] ?></td>
                        <td><?= rupiah($r['total_spend']) ?></td>
                        <td><?= (int) $r['points'] ?></td>
                        <td><?= $r['last_transaction'] ? date('d M Y', strtotime($r['last_transaction'])) : '-' ?></td>
                        <td><span
                                class="status-pill <?= $r['segment'] === 'Loyal' ? 'pill-green' : ($r['segment'] === 'Aktif' ? 'pill-blue' : 'pill-gray') ?>"><?= e($r['segment']) ?></span>
                        </td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require 'includes/footer.php'; ?>