<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = (int) ($_POST['customer_id'] ?? 0);
    $menu = trim($_POST['menu_name'] ?? '');
    $qty = filter_var($_POST['quantity'] ?? 0, FILTER_VALIDATE_INT);
    $total = filter_var($_POST['total_amount'] ?? null, FILTER_VALIDATE_INT);
    if ($menu === '' || !$qty || $qty < 1 || !$total || $total < 1) {
        flash('Menu, jumlah, dan total pembayaran harus diisi dengan benar.', 'danger');
        redirect('transactions.php');
    }
    if ($customer > 0) {
        $c = $pdo->prepare("SELECT id FROM customers WHERE id=?");
        $c->execute([$customer]);
        if (!$c->fetch()) {
            flash('Pelanggan tidak ditemukan.', 'danger');
            redirect('transactions.php');
        }
    }
    $points = (int) floor($total / 10000);
    try {
        $pdo->beginTransaction();
        $s = $pdo->prepare("INSERT INTO transactions(customer_id,menu_name,quantity,total_amount,points_awarded) VALUES(?,?,?,?,?)");
        $s->execute([$customer ?: null, $menu, $qty, $total, $points]);
        if ($customer > 0 && $points > 0) {
            $u = $pdo->prepare("UPDATE customers SET points=points+? WHERE id=?");
            $u->execute([$points, $customer]);
        }
        $pdo->commit();
        flash("Transaksi tersimpan. Poin yang diperoleh: {$points}.");
    } catch (Throwable $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        flash('Transaksi gagal disimpan.', 'danger');
    }
    redirect('transactions.php');
}
$customers = $pdo->query("SELECT id,name,phone FROM customers ORDER BY name")->fetchAll();
$history = $pdo->query("SELECT t.*,c.name customer_name FROM transactions t LEFT JOIN customers c ON c.id=t.customer_id ORDER BY t.transaction_date DESC,t.id DESC LIMIT 100")->fetchAll();
$pageTitle = 'Transaksi & Poin';
require 'includes/header.php';
?>
<div class="section-heading">
    <div>
        <h3>Pencatatan transaksi</h3>
        <p>Setiap transaksi menghasilkan 1 poin per Rp10.000 pembayaran.</p>
    </div><span class="rule-chip">10.000 = 1 poin</span>
</div>
<div class="form-card mb-4">
    <h4>Tambah transaksi</h4>
    <form method="post" class="row g-3"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="col-md-6"><label class="form-label">Pelanggan</label><select class="form-select" name="customer_id">
                <option value="0">Pelanggan umum / tidak terdaftar</option><?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= e($c['name'] . ' — ' . $c['phone']) ?></option><?php endforeach; ?>
            </select></div>
        <div class="col-md-6"><label class="form-label">Nama menu *</label><input class="form-control" name="menu_name"
                required maxlength="150" placeholder="Contoh: Es Kopi Susu"></div>
        <div class="col-md-3"><label class="form-label">Jumlah pembelian *</label><input class="form-control"
                type="number" name="quantity" min="1" value="1" required></div>
        <div class="col-md-5"><label class="form-label">Total pembayaran (Rp) *</label><input class="form-control"
                type="number" name="total_amount" id="totalAmount" min="1" step="1" required
                placeholder="Contoh: 35000"></div>
        <div class="col-md-4"><label class="form-label">Estimasi poin</label>
            <div class="points-preview" id="pointsPreview">0 poin</div>
        </div>
        <div class="col-12"><button class="btn btn-coffee">Simpan transaksi</button></div>
    </form>
</div>
<div class="section-heading">
    <div>
        <h3>Riwayat transaksi</h3>
        <p>100 transaksi terbaru.</p>
    </div>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Menu</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody><?php if (!$history): ?>
                    <tr>
                        <td colspan="6" class="empty-state">Belum ada transaksi tercatat.</td>
                    </tr><?php endif; ?><?php foreach ($history as $t): ?>
                    <tr>
                        <td><?= date('d M Y H:i', strtotime($t['transaction_date'])) ?></td>
                        <td><?= e($t['customer_name'] ?? 'Pelanggan umum') ?></td>
                        <td><?= e($t['menu_name']) ?></td>
                        <td><?= (int) $t['quantity'] ?></td>
                        <td><b><?= rupiah($t['total_amount']) ?></b></td>
                        <td><span class="points-tag">+<?= (int) $t['points_awarded'] ?> pts</span></td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require 'includes/footer.php'; ?>