<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
butuh_role(['Super Admin', 'Admin', 'Customer Service']);

$judul = 'Pelanggan';

if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $s = $koneksi->prepare('SELECT status FROM pelanggan WHERE id_pelanggan = ?');
    $s->bind_param('i', $id);
    $s->execute();
    $row = $s->get_result()->fetch_assoc();
    if ($row) {
        $baru = $row['status'] === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $u = $koneksi->prepare('UPDATE pelanggan SET status = ? WHERE id_pelanggan = ?');
        $u->bind_param('si', $baru, $id);
        $u->execute();
        set_flash('success', 'Status pelanggan diubah menjadi ' . $baru . '.');
    }
    header('Location: pelanggan.php');
    exit;
}

$cari = trim($_GET['cari'] ?? '');
$sql = "SELECT pl.*, COUNT(p.id_pesanan) AS jml_pesanan, COALESCE(SUM(p.total),0) AS total_belanja
        FROM pelanggan pl
        LEFT JOIN pesanan p ON p.id_pelanggan = pl.id_pelanggan
                           AND p.status_pesanan NOT IN ('Menunggu Pembayaran','Dibatalkan','Retur')";
$args = [];
if ($cari !== '') {
    $sql .= ' WHERE pl.nama_lengkap LIKE ? OR pl.email LIKE ?';
    $args[] = "%$cari%";
    $args[] = "%$cari%";
}
$sql .= ' GROUP BY pl.id_pelanggan ORDER BY pl.id_pelanggan DESC';

$stmt = $koneksi->prepare($sql);
if ($args) {
    // bind_param mewajibkan variabel, jadi bind lewat referensi
    $bind = [];
    foreach ($args as $i => $v) {
        $bind[$i] = &$args[$i];
    }
    $stmt->bind_param('ss', ...$bind);
}
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/layout/header.php';
?>

<div class="card-panel p-3 mb-3">
    <form method="get" class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small fw-semibold mb-1">Cari Pelanggan</label>
            <input type="search" name="cari" class="form-control form-control-sm"
                   value="<?= e($cari) ?>" placeholder="Nama atau email">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i> Cari</button>
            <a href="pelanggan.php" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="card-panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Pelanggan</th><th>Email</th><th>No. HP</th>
                    <th class="text-center">Pesanan</th><th class="text-end">Total Belanja</th>
                    <th>Bergabung</th><th>Status</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$data): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada pelanggan.</td></tr>
                <?php endif; ?>
                <?php foreach ($data as $d): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($d['nama_lengkap']) ?></td>
                        <td class="text-muted small"><?= e($d['email']) ?></td>
                        <td class="text-muted small"><?= e($d['nomor_hp'] ?? '-') ?></td>
                        <td class="text-center"><span class="badge bg-secondary"><?= (int)$d['jml_pesanan'] ?></span></td>
                        <td class="text-end fw-semibold"><?= rupiah($d['total_belanja']) ?></td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($d['dibuat_pada'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $d['status'] === 'Aktif' ? 'success' : 'secondary' ?>">
                                <?= e($d['status']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="pelanggan.php?toggle=<?= (int)$d['id_pelanggan'] ?>"
                               class="btn btn-sm btn-outline-<?= $d['status'] === 'Aktif' ? 'danger' : 'success' ?>"
                               onclick="return confirm('Ubah status pelanggan ini?')">
                                <i class="bi bi-<?= $d['status'] === 'Aktif' ? 'slash-circle' : 'check-circle' ?>"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
