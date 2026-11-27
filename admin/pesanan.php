<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
butuh_role(['Super Admin', 'Admin', 'Kasir', 'Customer Service']);

$judul = 'Pesanan';

$status_valid = [
    'Menunggu Pembayaran', 'Dibayar', 'Diproses', 'Sedang Disiapkan',
    'Packing', 'Di Antar', 'Dikirim', 'Selesai', 'Dibatalkan', 'Retur',
];
$warna = [
    'Menunggu Pembayaran' => 'warning', 'Dibayar' => 'info', 'Diproses' => 'primary',
    'Sedang Disiapkan' => 'primary', 'Packing' => 'primary', 'Di Antar' => 'info',
    'Dikirim' => 'primary', 'Selesai' => 'success',
    'Dibatalkan' => 'danger', 'Retur' => 'dark',
];

/* ================== UBAH STATUS ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id_pesanan'] ?? 0);
    $status = $_POST['status_pesanan'] ?? '';

    if (!in_array($status, $status_valid, true)) {
        set_flash('danger', 'Status tidak valid.');
    } else {
        $koneksi->begin_transaction();
        try {
            $s = $koneksi->prepare('SELECT * FROM pesanan WHERE id_pesanan = ? FOR UPDATE');
            $s->bind_param('i', $id);
            $s->execute();
            $p = $s->get_result()->fetch_assoc();
            if (!$p) {
                throw new RuntimeException('Pesanan tidak ditemukan.');
            }

            $st = $koneksi->prepare('UPDATE pesanan SET status_pesanan = ? WHERE id_pesanan = ?');
            $st->bind_param('si', $status, $id);
            $st->execute();

            // Sinkronkan tabel pembayaran supaya badge di halaman pelanggan
            // tidak menggantung di "Menunggu" setelah status pesanan berubah.
            $status_bayar = [
                'Dibayar'    => 'Diverifikasi',
                'Dibatalkan' => 'Ditolak',
                'Retur'      => 'Ditolak',
            ][$status] ?? null;
            if ($status_bayar !== null) {
                $u = $koneksi->prepare('UPDATE pembayaran SET status = ? WHERE id_pesanan = ?');
                $u->bind_param('si', $status_bayar, $id);
                $u->execute();
            }

            // Saat dikirim, catat nomor resi bila diisi
            if (trim($_POST['nomor_resi'] ?? '') !== '') {
                $kurir = trim($_POST['kurir'] ?? 'Ekspedisi');
                $resi  = trim($_POST['nomor_resi']);
                $c = $koneksi->prepare(
                    'INSERT INTO pengiriman (id_pesanan, kurir, nomor_resi, ongkir, status) VALUES (?,?,?,?,?)'
                );
                $ongkir = (float) $p['ongkir'];
                $status_kirim = $status === 'Selesai' ? 'Sampai' : 'Dikirim';
                $c->bind_param('issds', $id, $kurir, $resi, $ongkir, $status_kirim);
                $c->execute();
            }

            // Catat perubahan status ke riwayat tracking.
            if ($status !== $p['status_pesanan']) {
                $keterangan = trim($_POST['keterangan'] ?? '');
                if ($keterangan === '') {
                    $keterangan = [
                        'Sedang Disiapkan' => 'Pesanan sedang disiapkan oleh toko.',
                        'Di Antar'         => 'Pesanan sedang diantar ke pelanggan.',
                    ][$status] ?? null;
                }
                $admin = admin_saat_ini();
                $petugas = $admin['nama_lengkap'] ?? 'Admin';
                $r = $koneksi->prepare(
                    'INSERT INTO riwayat_status_pesanan
                     (id_pesanan, status_lama, status_baru, keterangan, petugas)
                     VALUES (?,?,?,?,?)'
                );
                $r->bind_param('issss', $id, $p['status_pesanan'], $status, $keterangan, $petugas);
                $r->execute();
                $r->close();
            }

            $koneksi->commit();
            set_flash('success', "Status pesanan {$p['kode_pesanan']} diubah ke \"$status\".");
        } catch (RuntimeException $ex) {
            $koneksi->rollback();
            set_flash('danger', $ex->getMessage());
        }
    }
    header('Location: pesanan.php');
    exit;
}

$filter = $_GET['status'] ?? '';
$sql = 'SELECT p.*, pl.nama_lengkap, pl.nomor_hp,
               b.metode AS metode_bayar, b.status AS status_bayar
        FROM pesanan p
        LEFT JOIN pelanggan pl ON pl.id_pelanggan = p.id_pelanggan
        LEFT JOIN pembayaran b ON b.id_pesanan = p.id_pesanan';
$args = [];
if (in_array($filter, $status_valid, true)) {
    $sql .= ' WHERE p.status_pesanan = ?';
    $args[] = $filter;
}
$sql .= ' ORDER BY p.id_pesanan DESC LIMIT 100';

$stmt = $koneksi->prepare($sql);
if ($args) {
    // bind_param mewajibkan variabel, jadi bind lewat referensi
    $bind = [];
    foreach ($args as $i => $v) {
        $bind[$i] = &$args[$i];
    }
    $stmt->bind_param('s', ...$bind);
}
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* Riwayat tracking status untuk tiap pesanan yang tampil. */
$riwayat_per_pesanan = [];
if ($pesanan) {
    $ids = array_column($pesanan, 'id_pesanan');
    $placeholder = implode(',', array_fill(0, count($ids), '?'));
    $rh = $koneksi->prepare(
        'SELECT id_pesanan, status_lama, status_baru, keterangan, petugas, dibuat_pada
         FROM riwayat_status_pesanan
         WHERE id_pesanan IN (' . $placeholder . ')
         ORDER BY id_pesanan, id_riwayat'
    );
    $rh->bind_param(str_repeat('i', count($ids)), ...$ids);
    $rh->execute();
    foreach ($rh->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $riwayat_per_pesanan[$row['id_pesanan']][] = $row;
    }
    $rh->close();
}

include __DIR__ . '/layout/header.php';
?>

<div class="card-panel p-3 mb-3">
    <form method="get" class="d-flex gap-2 align-items-center flex-wrap">
        <label class="form-label small fw-semibold mb-0">Filter Status:</label>
        <select name="status" class="form-select form-select-sm" style="max-width:220px" onchange="this.form.submit()">
            <option value="">Semua</option>
            <?php foreach ($status_valid as $s): ?>
                <option value="<?= e($s) ?>" <?= $filter === $s ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
        </select>
        <noscript><button class="btn btn-sm btn-primary">Terapkan</button></noscript>
    </form>
</div>

<div class="card-panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode</th><th>Pelanggan</th><th>Tanggal</th>
                    <th>Pengiriman</th><th>Pembayaran</th>
                    <th class="text-end">Total</th><th>Status</th>
                    <th style="width:230px">Tracking</th>
                    <th style="width:300px">Ubah Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$pesanan): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                <?php endif; ?>
                <?php foreach ($pesanan as $p): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($p['kode_pesanan']) ?></td>
                        <td>
                            <?= e($p['nama_lengkap'] ?? '-') ?>
                            <br><small class="text-muted"><?= e($p['nomor_hp'] ?? '-') ?></small>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($p['tanggal_pesanan'])) ?></td>
                        <td>
                            <div class="small"><?= e($p['metode_pengiriman'] ?: '-') ?></div>
                            <?php if ($p['kota']): ?>
                                <small class="text-muted"><?= e($p['kota']) ?></small>
                            <?php endif; ?>
                            <?php if ((float)$p['ongkir'] > 0): ?>
                                <small class="text-muted d-block">Ongkir <?= rupiah($p['ongkir']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small"><?= e($p['metode_bayar'] ?: '-') ?></div>
                            <?php if ($p['status_bayar']): ?>
                                <span class="badge bg-<?= ['Menunggu' => 'warning', 'Diverifikasi' => 'success', 'Ditolak' => 'danger'][$p['status_bayar']] ?? 'secondary' ?>">
                                    <?= e($p['status_bayar']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end fw-semibold"><?= rupiah($p['total']) ?></td>
                        <td><span class="badge bg-<?= $warna[$p['status_pesanan']] ?? 'secondary' ?>"><?= e($p['status_pesanan']) ?></span></td>
                        <td>
                            <div class="tracking-list">
                                <?php $riwayat = $riwayat_per_pesanan[$p['id_pesanan']] ?? []; ?>
                                <?php if (!$riwayat): ?>
                                    <small class="text-muted">Belum ada riwayat.</small>
                                <?php else: ?>
                                    <?php foreach ($riwayat as $tr): ?>
                                        <div class="tracking-item">
                                            <span class="badge bg-<?= $warna[$tr['status_baru']] ?? 'secondary' ?>">
                                                <?= e($tr['status_baru']) ?>
                                            </span>
                                            <small class="text-muted d-block">
                                                <?= date('d/m H:i', strtotime($tr['dibuat_pada'])) ?>
                                                <?php if ($tr['keterangan']): ?>
                                                    · <?= e($tr['keterangan']) ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <form method="post" class="d-flex flex-column gap-1">
                                <input type="hidden" name="id_pesanan" value="<?= (int)$p['id_pesanan'] ?>">
                                <div class="d-flex gap-1">
                                    <select name="status_pesanan" class="form-select form-select-sm">
                                        <?php foreach ($status_valid as $s): ?>
                                            <option <?= $p['status_pesanan'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-primary"><i class="bi bi-check-lg"></i></button>
                                </div>
                                <input type="text" name="keterangan" class="form-control form-control-sm"
                                       placeholder="Keterangan tracking (opsional)">
                                <div class="d-flex gap-1">
                                    <input type="text" name="kurir" class="form-control form-control-sm"
                                           placeholder="Kurir">
                                    <input type="text" name="nomor_resi" class="form-control form-control-sm"
                                           placeholder="No. Resi">
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
