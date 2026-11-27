<?php
/**
 * TESTIMONI - moderasi testimoni dari sisi admin.
 *
 * Pelanggan mengirim testimoni dengan status 'Pending'. Di sini admin
 * bisa menyetujui (Tampil) atau menolak (Ditolak), serta menghapus
 * testimoni yang tidak pantas.
 */
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
butuh_role(['Super Admin', 'Admin', 'Customer Service']);

$judul = 'Testimoni';

$warna_status = ['Pending' => 'warning', 'Tampil' => 'success', 'Ditolak' => 'danger'];

/* ================= aksi moderasi ================= */
if (isset($_GET['aksi'])) {
    $aksi = $_GET['aksi'];
    $id   = (int)($_GET['id'] ?? 0);

    if ($id > 0 && $aksi === 'tampil') {
        $u = $koneksi->prepare("UPDATE testimoni SET status = 'Tampil' WHERE id_testimoni = ?");
        $u->bind_param('i', $id);
        $u->execute();
        set_flash('success', 'Testimoni disetujui dan akan tampil di beranda.');
    } elseif ($id > 0 && $aksi === 'tolak') {
        $u = $koneksi->prepare("UPDATE testimoni SET status = 'Ditolak' WHERE id_testimoni = ?");
        $u->bind_param('i', $id);
        $u->execute();
        set_flash('warning', 'Testimoni ditolak.');
    } elseif ($id > 0 && $aksi === 'hapus') {
        $d = $koneksi->prepare('DELETE FROM testimoni WHERE id_testimoni = ?');
        $d->bind_param('i', $id);
        $d->execute();
        set_flash('success', 'Testimoni dihapus.');
    }

    header('Location: testimoni.php' . (isset($_GET['filter']) ? '?filter=' . urlencode($_GET['filter']) : ''));
    exit;
}

/* ================= daftar + filter ================= */
$filter = $_GET['filter'] ?? 'Pending';
if (!array_key_exists($filter, $warna_status)) {
    $filter = 'Pending';
}

$sql    = "SELECT t.*, p.email
           FROM testimoni t
           LEFT JOIN pelanggan p ON p.id_pelanggan = t.id_pelanggan";
$args   = [];

if ($filter !== 'Semua') {
    $sql   .= ' WHERE t.status = ?';
    $args[] = $filter;
}
$sql .= ' ORDER BY t.id_testimoni DESC';

$stmt = $koneksi->prepare($sql);
if ($args) {
    $bind = [];
    foreach ($args as $i => $v) {
        $bind[$i] = &$args[$i];
    }
    $stmt->bind_param('s', ...$bind);
}
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Jumlah per status buat tombol filter
$jumlah = ['Pending' => 0, 'Tampil' => 0, 'Ditolak' => 0, 'Semua' => 0];
foreach ($koneksi->query('SELECT status, COUNT(*) c FROM testimoni GROUP BY status')->fetch_all(MYSQLI_ASSOC) as $r) {
    $jumlah[$r['status']] = (int)$r['c'];
    $jumlah['Semua']     += (int)$r['c'];
}

include __DIR__ . '/layout/header.php';
?>

<div class="card-panel p-3 mb-3">
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($jumlah as $status => $n): ?>
            <a href="testimoni.php?filter=<?= urlencode($status) ?>"
               class="btn btn-sm <?= $filter === $status ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <?= e($status) ?>
                <span class="badge bg-light text-dark ms-1"><?= (int)$n ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="card-panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th style="min-width:280px">Testimoni</th>
                    <th class="text-center">Rating</th>
                    <th>Status</th>
                    <th>Dikirim</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$data): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">
                        Tidak ada testimoni dengan status ini.
                    </td></tr>
                <?php endif; ?>

                <?php foreach ($data as $t): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($t['nama']) ?></div>
                            <div class="text-muted small"><?= e($t['email'] ?? '-') ?></div>
                        </td>
                        <td class="small"><?= e($t['isi']) ?></td>
                        <td class="text-center" style="color:#f59e0b;white-space:nowrap">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi <?= $i <= (int)$t['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                            <?php endfor; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $warna_status[$t['status']] ?? 'secondary' ?>">
                                <?= e($t['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($t['dibuat_pada'])) ?></td>
                        <td class="text-center" style="white-space:nowrap">
                            <?php if ($t['status'] !== 'Tampil'): ?>
                                <a href="testimoni.php?aksi=tampil&id=<?= (int)$t['id_testimoni'] ?>&filter=<?= urlencode($filter) ?>"
                                   class="btn btn-sm btn-outline-success" title="Setujui & tampilkan">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($t['status'] !== 'Ditolak'): ?>
                                <a href="testimoni.php?aksi=tolak&id=<?= (int)$t['id_testimoni'] ?>&filter=<?= urlencode($filter) ?>"
                                   class="btn btn-sm btn-outline-warning" title="Tolak"
                                   onclick="return confirm('Tolak testimoni ini?')">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            <?php endif; ?>
                            <a href="testimoni.php?aksi=hapus&id=<?= (int)$t['id_testimoni'] ?>&filter=<?= urlencode($filter) ?>"
                               class="btn btn-sm btn-outline-danger" title="Hapus permanen"
                               onclick="return confirm('Hapus testimoni ini secara permanen?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>