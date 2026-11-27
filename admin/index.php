<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();

$judul = 'Dashboard';

// Ringkasan angka
$jumlah_produk   = (int) $koneksi->query("SELECT COUNT(*) c FROM produk WHERE status='Aktif'")->fetch_assoc()['c'];
$jumlah_kategori = (int) $koneksi->query("SELECT COUNT(*) c FROM kategori WHERE status='Aktif'")->fetch_assoc()['c'];
$jumlah_pesanan  = (int) $koneksi->query('SELECT COUNT(*) c FROM pesanan')->fetch_assoc()['c'];
$jumlah_pelanggan = (int) $koneksi->query("SELECT COUNT(*) c FROM pelanggan WHERE status='Aktif'")->fetch_assoc()['c'];

$omzet = (float) ($koneksi->query(
    "SELECT COALESCE(SUM(total),0) t FROM pesanan WHERE status_pesanan NOT IN ('Menunggu Pembayaran','Dibatalkan','Retur')"
)->fetch_assoc()['t']);

$stok_menipis = $koneksi->query(
    'SELECT id_produk, nama_produk, stok, stok_minimum, satuan
     FROM produk WHERE status="Aktif" AND stok <= stok_minimum
     ORDER BY stok ASC LIMIT 5'
)->fetch_all(MYSQLI_ASSOC);

$pesanan_terbaru = $koneksi->query(
    'SELECT p.kode_pesanan, p.total, p.status_pesanan, p.tanggal_pesanan, pl.nama_lengkap
     FROM pesanan p
     LEFT JOIN pelanggan pl ON pl.id_pelanggan = p.id_pelanggan
     ORDER BY p.id_pesanan DESC LIMIT 8'
)->fetch_all(MYSQLI_ASSOC);

$warna_status = [
    'Menunggu Pembayaran' => 'warning', 'Dibayar' => 'info', 'Diproses' => 'primary',
    'Sedang Disiapkan' => 'primary', 'Packing' => 'primary', 'Di Antar' => 'info',
    'Dikirim' => 'primary', 'Selesai' => 'success',
    'Dibatalkan' => 'danger', 'Retur' => 'dark',
];

include __DIR__ . '/layout/header.php';
?>

<div class="row g-3 mb-4">
    <?php
    $stat = [
        ['Produk Aktif', $jumlah_produk, 'bi-box-seam', '#1473e6'],
        ['Kategori', $jumlah_kategori, 'bi-tags', '#16a06a'],
        ['Pesanan', $jumlah_pesanan, 'bi-receipt', '#f59e0b'],
        ['Pelanggan', $jumlah_pelanggan, 'bi-people', '#8b5cf6'],
    ];
    foreach ($stat as [$label, $val, $ikon, $warna]): ?>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="ikon" style="background:<?= $warna ?>"><i class="bi <?= $ikon ?>"></i></div>
                <div>
                    <div class="fs-4 fw-bold lh-1"><?= $val ?></div>
                    <small class="text-muted"><?= $label ?></small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card-panel p-4 h-100">
            <h6 class="fw-bold mb-3">Omzet Tercatat</h6>
            <div class="fs-2 fw-bold text-primary"><?= rupiah($omzet) ?></div>
            <small class="text-muted">Total dari pesanan berstatus selain Menunggu Pembayaran, Dibatalkan, dan Retur.</small>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-panel p-4 h-100">
            <h6 class="fw-bold mb-3">Stok Menipis</h6>
            <?php if (!$stok_menipis): ?>
                <p class="text-muted small mb-0"><i class="bi bi-check-circle text-success"></i> Semua produk aman.</p>
            <?php else: ?>
                <ul class="list-unstyled mb-0 small">
                    <?php foreach ($stok_menipis as $s): ?>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span><?= e($s['nama_produk']) ?></span>
                            <span class="text-danger fw-semibold"><?= (int)$s['stok'] ?> / <?= (int)$s['stok_minimum'] ?> <?= e($s['satuan']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card-panel p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Pesanan Terbaru</h6>
        <a href="pesanan.php" class="btn btn-sm btn-outline-primary">Lihat semua</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode</th><th>Pelanggan</th><th>Tanggal</th>
                    <th class="text-end">Total</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$pesanan_terbaru): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                <?php endif; ?>
                <?php foreach ($pesanan_terbaru as $p): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($p['kode_pesanan']) ?></td>
                        <td><?= e($p['nama_lengkap'] ?? '-') ?></td>
                        <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($p['tanggal_pesanan'])) ?></td>
                        <td class="text-end fw-semibold"><?= rupiah($p['total']) ?></td>
                        <td>
                            <span class="badge bg-<?= $warna_status[$p['status_pesanan']] ?? 'secondary' ?>">
                                <?= e($p['status_pesanan']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
