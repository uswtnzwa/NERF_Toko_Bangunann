<?php
/**
 * ANALITIK & STATISTIK
 *
 * Berisi tiga laporan:
 *   1. Laporan pengunjung       -> kunjungan per bulan, halaman & sumber teratas
 *   2. Laporan penjualan        -> pesanan & omzet per bulan + transaksi terbaru
 *   3. Laporan produk terlaris  -> ranking produk berdasarkan jumlah terjual
 */
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
require_once __DIR__ . '/../config/analitik-query.php';

butuh_role(['Super Admin', 'Admin']);

$judul = 'Analitik & Statistik';
$periode_label = $nama_bulan[$bulan] ?? 'Sepanjang Tahun';

include __DIR__ . '/layout/header.php';
?>

<!-- ===================== FILTER ===================== -->
<div class="card-panel p-3 mb-3">
    <form method="get" class="row g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label small fw-semibold mb-1">Tahun</label>
            <select name="tahun" class="form-select form-select-sm">
                <?php foreach ($tahun_list as $t): ?>
                    <option value="<?= e($t) ?>" <?= $t === $tahun ? 'selected' : '' ?>><?= e($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label small fw-semibold mb-1">Bulan</label>
            <select name="bulan" class="form-select form-select-sm">
                <option value="">Sepanjang tahun</option>
                <?php foreach ($nama_bulan as $k => $n): ?>
                    <option value="<?= $k ?>" <?= $bulan === $k ? 'selected' : '' ?>><?= $n ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
        </div>
        <div class="col-auto ms-auto align-self-center">
            <small class="text-muted">Periode: <strong><?= e($periode_label) ?> <?= e($tahun) ?></strong></small>
        </div>
    </form>
</div>

<!-- ===================== RINGKASAN ===================== -->
<div class="row g-3 mb-3">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="ikon" style="background:#1473e6"><i class="bi bi-eye"></i></div>
            <div>
                <div class="fs-4 fw-bold lh-1"><?= $total_pengunjung ?></div>
                <small class="text-muted">Pengunjung (<?= $jumlah_session_unik ?> unik)</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="ikon" style="background:#f59e0b"><i class="bi bi-receipt"></i></div>
            <div><div class="fs-4 fw-bold lh-1"><?= $total_pesanan ?></div><small class="text-muted">Pesanan</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="ikon" style="background:#16a06a"><i class="bi bi-cash-stack"></i></div>
            <div><div class="fs-4 fw-bold lh-1"><?= rupiah($total_omzet) ?></div><small class="text-muted">Omzet</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="ikon" style="background:#8b5cf6"><i class="bi bi-trophy"></i></div>
            <div>
                <div class="fw-bold" style="font-size:15px"><?= e($produk_teratas) ?></div>
                <small class="text-muted">Produk terlaris (<?= $total_qty_terlaris ?> terjual)</small>
            </div>
        </div>
    </div>
</div>

<!-- ===================== REKAP BULANAN ===================== -->
<div class="card-panel p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="fw-bold mb-0">Rekap Bulanan — Pengunjung &amp; Penjualan</h6>
        <small class="text-muted">Tahun <?= e($tahun) ?></small>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th class="text-center">Pengunjung</th>
                    <th class="text-center">Unik</th>
                    <th class="text-center">Pesanan</th>
                    <th class="text-end">Omzet</th>
                    <th style="width:160px">Grafik Omzet</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $omzet_max = 0;
                foreach ($rekap as $r) {
                    $omzet_max = max($omzet_max, $r['omzet']);
                }
                foreach ($rekap as $r): ?>
                    <tr class="<?= $bulan === $r['bulan'] ? 'table-primary' : '' ?>">
                        <td class="fw-semibold"><?= e($r['nama']) ?></td>
                        <td class="text-center"><?= $r['pengunjung'] ?></td>
                        <td class="text-center text-muted"><?= $r['unik'] ?></td>
                        <td class="text-center"><span class="badge bg-secondary"><?= $r['pesanan'] ?></span></td>
                        <td class="text-end fw-semibold text-success"><?= rupiah($r['omzet']) ?></td>
                        <td>
                            <div class="progress" style="height:8px">
                                <div class="progress-bar bg-success"
                                     style="width:<?= $omzet_max ? round($r['omzet'] / $omzet_max * 100) : 0 ?>%"></div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3">
    <!-- ===================== 1. LAPORAN PENGUNJUNG ===================== -->
    <div class="col-lg-5">
        <div class="card-panel p-3 h-100" id="pengunjung">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-eye text-primary"></i> Laporan Pengunjung</h6>
                <span class="badge bg-primary"><?= $total_pengunjung ?> kunjungan</span>
            </div>

            <h6 class="small fw-bold text-muted text-uppercase mt-3">Halaman Teratas</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-3">
                    <tbody>
                        <?php if (!$halaman_teratas): ?>
                            <tr><td class="text-center text-muted py-3">Belum ada data.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($halaman_teratas as $h): ?>
                            <tr>
                                <td class="small"><?= e($h['label']) ?></td>
                                <td class="text-end"><span class="badge bg-light text-dark"><?= (int)$h['jml'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h6 class="small fw-bold text-muted text-uppercase">Sumber Kunjungan</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <tbody>
                        <?php foreach ($sumber_teratas as $s): ?>
                            <tr>
                                <td class="small"><?= e($s['sumber']) ?></td>
                                <td class="text-end fw-semibold"><?= (int)$s['jml'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================== 2. LAPORAN PENJUALAN ===================== -->
    <div class="col-lg-7">
        <div class="card-panel p-3 h-100" id="penjualan">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-graph-up text-success"></i> Laporan Penjualan</h6>
                <span class="badge bg-success"><?= rupiah($total_omzet) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode Pesanan</th><th>Tanggal</th><th>Pelanggan</th>
                            <th>Status</th><th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$penjualan): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada penjualan pada periode ini.</td></tr>
                        <?php endif; ?>
                        <?php
                        $warna_status = [
                            'Dibayar' => 'info', 'Diproses' => 'primary',
                            'Sedang Disiapkan' => 'primary', 'Packing' => 'primary',
                            'Di Antar' => 'info', 'Dikirim' => 'primary', 'Selesai' => 'success',
                        ];
                        foreach (array_slice($penjualan, 0, 15) as $p): ?>
                            <tr>
                                <td class="fw-semibold small"><?= e($p['kode_pesanan']) ?></td>
                                <td class="small text-muted"><?= date('d/m/Y', strtotime($p['tanggal_pesanan'])) ?></td>
                                <td class="small"><?= e($p['nama_lengkap'] ?? '-') ?></td>
                                <td><span class="badge bg-<?= $warna_status[$p['status_pesanan']] ?? 'secondary' ?>"><?= e($p['status_pesanan']) ?></span></td>
                                <td class="text-end fw-semibold"><?= rupiah($p['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <small class="text-muted d-block mt-2">
                Pesanan dengan status Menunggu Pembayaran, Dibatalkan, dan Retur tidak dihitung.
            </small>
        </div>
    </div>
</div>

<!-- ===================== 3. PRODUK TERLARIS ===================== -->
<div class="card-panel p-3 mt-3" id="terlaris">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="fw-bold mb-0"><i class="bi bi-trophy text-warning"></i> Laporan Produk Terlaris</h6>
            <small class="text-muted">Diurutkan dari jumlah barang paling banyak terjual.</small>
        </div>
        <span class="badge bg-warning text-dark"><?= count($terlaris) ?> produk</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:50px">#</th><th>Produk</th>
                    <th class="text-center">Pesanan</th>
                    <th class="text-center">Terjual</th>
                    <th class="text-end">Omzet</th>
                    <th style="width:180px">Daya Jual</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$terlaris): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada produk terjual pada periode ini.</td></tr>
                <?php endif; ?>
                <?php
                $qty_max = 0;
                foreach ($terlaris as $t) {
                    $qty_max = max($qty_max, (int)$t['qty']);
                }
                foreach ($terlaris as $i => $t): ?>
                    <tr>
                        <td>
                            <?php if ($i < 3): ?>
                                <span class="badge bg-<?= ['warning', 'secondary', 'dark'][$i] ?>"><?= $i + 1 ?></span>
                            <?php else: ?>
                                <span class="text-muted"><?= $i + 1 ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-semibold small"><?= e($t['nama_produk']) ?></div>
                            <small class="text-muted"><?= e($t['kode_produk']) ?></small>
                        </td>
                        <td class="text-center"><span class="badge bg-light text-dark"><?= (int)$t['jml_pesanan'] ?></span></td>
                        <td class="text-center fw-bold"><?= (int)$t['qty'] ?> <?= e($t['satuan']) ?></td>
                        <td class="text-end fw-semibold text-success"><?= rupiah($t['omzet']) ?></td>
                        <td>
                            <div class="progress" style="height:8px">
                                <div class="progress-bar bg-primary"
                                     style="width:<?= $qty_max ? round((int)$t['qty'] / $qty_max * 100) : 0 ?>%"></div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
