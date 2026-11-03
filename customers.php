<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'save') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $member = $_POST['member_status'] ?? 'Non-member';
    if ($name === '' || $phone === '' || !preg_match('/^[0-9+().\\- ]{8,20}$/', $phone) || !in_array($member, ['Member', 'Non-member'], true)) {
      flash('Nama, nomor telepon, atau status member tidak valid.', 'danger');
      redirect('customers.php' . ($id ? '?edit=' . $id : ''));
    }
    if ($id) {
      $s = $pdo->prepare("UPDATE customers SET name=?,phone=?,address=?,member_status=? WHERE id=?");
      $s->execute([$name, $phone, $address ?: null, $member, $id]);
      flash('Data pelanggan berhasil diperbarui.');
    } else {
      $s = $pdo->prepare("INSERT INTO customers(name,phone,address,member_status) VALUES(?,?,?,?)");
      $s->execute([$name, $phone, $address ?: null, $member]);
      flash('Pelanggan berhasil ditambahkan.');
    }
    redirect('customers.php');
  }
  if ($action === 'delete') {
    $id = (int) $_POST['id'];
    $s = $pdo->prepare("DELETE FROM customers WHERE id=?");
    $s->execute([$id]);
    flash('Data pelanggan dihapus.');
    redirect('customers.php');
  }
}
$edit = null;
if (isset($_GET['edit'])) {
  $s = $pdo->prepare("SELECT * FROM customers WHERE id=?");
  $s->execute([(int) $_GET['edit']]);
  $edit = $s->fetch();
}
$q = trim($_GET['q'] ?? '');
$sql = "SELECT c.*,COUNT(t.id) transaction_count,COALESCE(SUM(t.total_amount),0) total_spend FROM customers c LEFT JOIN transactions t ON t.customer_id=c.id";
$params = [];
if ($q !== '') {
  $sql .= " WHERE c.name LIKE ? OR c.phone LIKE ? OR CAST(c.id AS CHAR) LIKE ?";
  $params = ["%$q%", "%$q%", "%$q%"];
}
$sql .= " GROUP BY c.id ORDER BY c.created_at DESC";
$s = $pdo->prepare($sql);
$s->execute($params);
$rows = $s->fetchAll();
$pageTitle = 'Data Pelanggan';
require 'includes/header.php';
?>
<div class="section-heading">
  <div>
    <h3>Database pelanggan</h3>
    <p>Kelola profil, status keanggotaan, dan poin loyalitas.</p>
  </div><a href="#form-pelanggan" class="btn btn-coffee">+ Tambah pelanggan</a>
</div>
<div class="form-card mb-4" id="form-pelanggan">
  <h4><?= $edit ? 'Edit pelanggan' : 'Tambah pelanggan baru' ?></h4>
  <form method="post" class="row g-3"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input
      type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
    <div class="col-md-6"><label class="form-label">Nama pelanggan *</label><input class="form-control" name="name"
        required maxlength="100" value="<?= e($edit['name'] ?? '') ?>"></div>
    <div class="col-md-6"><label class="form-label">Nomor telepon *</label><input class="form-control" name="phone"
        required maxlength="20" value="<?= e($edit['phone'] ?? '') ?>"></div>
    <div class="col-md-6"><label class="form-label">Alamat (opsional)</label><input class="form-control" name="address"
        maxlength="255" value="<?= e($edit['address'] ?? '') ?>"></div>
    <div class="col-md-6"><label class="form-label">Status member</label><select class="form-select"
        name="member_status">
        <option <?= ($edit['member_status'] ?? 'Member') === 'Member' ? 'selected' : '' ?>>Member</option>
        <option <?= ($edit['member_status'] ?? '') === 'Non-member' ? 'selected' : '' ?>>Non-member</option>
      </select></div>
    <div class="col-12 d-flex gap-2"><button class="btn btn-coffee">Simpan pelanggan</button><?php if ($edit): ?><a
          href="customers.php" class="btn btn-light">Batal edit</a><?php endif; ?></div>
  </form>
</div>
<form class="search-bar mb-3" method="get"><input class="form-control" name="q"
    placeholder="Cari nama, nomor telepon, atau ID pelanggan..." value="<?= e($q) ?>"><button
    class="btn btn-outline-coffee">Cari</button><?php if ($q): ?><a class="btn btn-light"
      href="customers.php">Reset</a><?php endif; ?></form>
<div class="table-card">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama pelanggan</th>
          <th>Telepon</th>
          <th>Bergabung</th>
          <th>Member</th>
          <th>Poin</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr>
            <td colspan="7" class="empty-state">Data pelanggan tidak ditemukan.</td>
          </tr><?php endif; ?>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td>#<?= e($r['id']) ?></td>
            <td><b><?= e($r['name']) ?></b><small
                class="d-block text-muted"><?= e($r['address'] ?? 'Alamat belum diisi') ?></small></td>
            <td><?= e($r['phone']) ?></td>
            <td><?= date('d M Y', strtotime($r['join_date'])) ?></td>
            <td><span
                class="status-pill <?= $r['member_status'] === 'Member' ? 'pill-green' : 'pill-gray' ?>"><?= e($r['member_status']) ?></span>
            </td>
            <td><b><?= number_format((int) $r['points']) ?></b> pts</td>
            <td class="actions"><a class="btn btn-sm btn-light" href="customers.php?edit=<?= $r['id'] ?>">Edit</a>
              <form method="post"
                onsubmit="return confirm('Hapus pelanggan ini? Riwayat transaksi tetap tersimpan tanpa relasi pelanggan.')">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action"
                  value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button
                  class="btn btn-sm btn-outline-danger">Hapus</button></form>
            </td>
          </tr><?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="table-foot">Menampilkan <?= count($rows) ?> pelanggan. Data dummy ditandai sebagai data simulasi pada
    database SQL.</div>
</div>
<?php require 'includes/footer.php'; ?>