<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $customer = (int) ($_POST['customer_id'] ?? 0);
        $category = $_POST['category'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $pic = trim($_POST['pic'] ?? '');
        $status = $_POST['status'] ?? 'Baru';
        if (!in_array($category, ['Keluhan', 'Saran'], true) || $description === '' || !in_array($status, ['Baru', 'Diproses', 'Selesai'], true)) {
            flash('Periksa kembali kategori, deskripsi, dan status.', 'danger');
            redirect('complaints.php');
        }
        $s = $pdo->prepare("INSERT INTO complaints(customer_id,category,description,pic,status) VALUES(?,?,?,?,?)");
        $s->execute([$customer ?: null, $category, $description, $pic ?: null, $status]);
        flash('Keluhan atau saran berhasil dicatat.');
        redirect('complaints.php');
    }
    if ($action === 'status') {
        $id = (int) $_POST['id'];
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['Baru', 'Diproses', 'Selesai'], true)) {
            $s = $pdo->prepare("UPDATE complaints SET status=? WHERE id=?");
            $s->execute([$status, $id]);
            flash('Status penanganan diperbarui.');
        }
        redirect('complaints.php');
    }
}
$customers = $pdo->query("SELECT id,name FROM customers ORDER BY name")->fetchAll();
$rows = $pdo->query("SELECT x.*,c.name customer_name FROM complaints x LEFT JOIN customers c ON c.id=x.customer_id ORDER BY x.complaint_date DESC,x.id DESC")->fetchAll();
$pageTitle = 'Keluhan & Saran';
require 'includes/header.php';
?>
<div class="section-heading">
    <div>
        <h3>Suara pelanggan</h3>
        <p>Catat masukan dan pantau tindak lanjutnya.</p>
    </div>
</div>
<div class="form-card mb-4">
    <h4>Catat keluhan atau saran</h4>
    <form method="post" class="row g-3"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input
            type="hidden" name="action" value="save">
        <div class="col-md-6"><label class="form-label">Pelanggan</label><select class="form-select" name="customer_id">
                <option value="0">Tidak diketahui / anonim</option><?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
            </select></div>
        <div class="col-md-3"><label class="form-label">Kategori</label><select class="form-select" name="category">
                <option>Keluhan</option>
                <option>Saran</option>
            </select></div>
        <div class="col-md-3"><label class="form-label">Status awal</label><select class="form-select" name="status">
                <option>Baru</option>
                <option>Diproses</option>
                <option>Selesai</option>
            </select></div>
        <div class="col-md-8"><label class="form-label">Deskripsi *</label><textarea class="form-control"
                name="description" rows="2" required maxlength="2000"></textarea></div>
        <div class="col-md-4"><label class="form-label">Penanggung jawab (PIC)</label><input class="form-control"
                name="pic" maxlength="100" placeholder="Nama petugas"></div>
        <div class="col-12"><button class="btn btn-coffee">Simpan masukan</button></div>
    </form>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ID / Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Kategori & Deskripsi</th>
                    <th>PIC</th>
                    <th>Status penanganan</th>
                </tr>
            </thead>
            <tbody><?php if (!$rows): ?>
                    <tr>
                        <td colspan="5" class="empty-state">Belum ada keluhan atau saran.</td>
                    </tr><?php endif; ?><?php foreach ($rows as $r): ?>
                    <tr>
                        <td>#<?= $r['id'] ?><small
                                class="d-block text-muted"><?= date('d M Y', strtotime($r['complaint_date'])) ?></small></td>
                        <td><?= e($r['customer_name'] ?? 'Anonim') ?></td>
                        <td><span class="status-pill pill-gray"><?= e($r['category']) ?></span>
                            <div class="description-cell"><?= e($r['description']) ?></div>
                        </td>
                        <td><?= e($r['pic'] ?? '-') ?></td>
                        <td>
                            <form method="post" class="status-form"><input type="hidden" name="csrf_token"
                                    value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="status"><input
                                    type="hidden" name="id" value="<?= $r['id'] ?>"><select
                                    class="form-select form-select-sm" name="status"
                                    onchange="this.form.submit()"><?php foreach (['Baru', 'Diproses', 'Selesai'] as $st): ?>
                                        <option <?= $r['status'] === $st ? 'selected' : '' ?>><?= $st ?></option><?php endforeach; ?>
                                </select></form>
                        </td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require 'includes/footer.php'; ?>