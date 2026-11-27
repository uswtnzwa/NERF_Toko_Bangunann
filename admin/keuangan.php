<?php
/**
 * LAPORAN KEUANGAN
 *
 * Berisi tiga bagian:
 *   1. Daftar laporan keuangan -> rekap pendapatan & pengeluaran per bulan
 *   2. Laporan pendapatan      -> pembayaran terverifikasi
 *   3. Laporan pengeluaran     -> catatan biaya operasional (+ tambah/hapus)
 */
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
require_once __DIR__ . '/../config/keuangan-query.php';

butuh_role(['Super Admin', 'Admin']);

$judul = 'Laporan Keuangan';

/* ================== AKSI PENGELUARAN ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $anchor = 'pengeluaran';

    if ($aksi === 'tambah') {
        $tanggal = trim($_POST['tanggal'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $jumlah = (float)str_replace(['.', ' '], '', $_POST['jumlah'] ?? '0');
        $metode = trim($_POST['metode'] ?? 'Tunai');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) || !checkdate(
            (int)substr($tanggal, 5, 2), (int)substr($tanggal, 8, 2), (int)substr($tanggal, 0, 4)
        )) {
            [$tipe, $pesan] = ['danger', 'Tanggal tidak valid.'];
        } elseif ($kategori === '' || $keterangan === '') {
            [$tipe, $pesan] = ['danger', 'Kategori dan keterangan wajib diisi.'];
        } elseif ($jumlah <= 0) {
            [$tipe, $pesan] = ['danger', 'Jumlah harus lebih dari 0.'];
        } else {
            $s = $koneksi->prepare(
                'INSERT INTO pengeluaran (tanggal, kategori, keterangan, jumlah, metode)
                 VALUES (?,?,?,?,?)'
            );
            $tgl_lengkap = $tanggal . ' ' . date('H:i:s');
            $s->bind_param('sssds', $tgl_lengkap, $kategori, $keterangan, $jumlah, $metode);
            $s->execute();
            [$tipe, $pesan] = ['success', 'Pengeluaran berhasil dicatat.'];
        }
    } elseif ($aksi === 'hapus') {
        $s = $koneksi->prepare('DELETE FROM pengeluaran WHERE id_pengeluaran = ?');
        $id = (int)($_POST['id_pengeluaran'] ?? 0);
        $s->bind_param('i', $id);
        $s->execute();
        [$tipe, $pesan] = ['success', 'Catatan pengeluaran dihapus.'];
    } else {
        [$tipe, $pesan] = ['danger', 'Aksi tidak dikenal.'];
    }

    set_flash($tipe, $pesan);
    $query = http_build_query(array_filter(['tahun' => $tahun, 'bulan' => $bulan]));
    header('Location: keuangan.php' . ($query ? '?' . $query : '') . '#' . $anchor);
    exit;
}

$kategori_list = [
    'Pembelian Stok', 'Listrik', 'Gaji Karyawan', 'Pengiriman',
    'Sewa Tempat', 'Internet', 'Perbaikan', 'Lainnya',
];
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
];
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
        <div class="col-auto">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
        <div class="col-auto ms-auto align-self-center">
            <small class="text-muted">Periode: <strong><?= e($periode_label) ?> <?= e($tahun) ?></strong></small>
        </div>
    </form>
</div>

<!-- ===================== RINGKASAN ===================== -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card-panel p-3">
            <div class="small text-muted"><i class="bi bi-arrow-down-left-circle text-success"></i> Pendapatan</div>
            <div class="fs-4 fw-bold text-success"><?= rupiah($total_pendapatan) ?></div>
            <small class="text-muted"><?= count($pendapatan) ?> transaksi terverifikasi</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-panel p-3">
            <div class="small text-muted"><i class="bi bi-arrow-up-right-circle text-danger"></i> Pengeluaran</div>
            <div class="fs-4 fw-bold text-danger"><?= rupiah($total_pengeluaran) ?></div>
            <small class="text-muted"><?= count($pengeluaran) ?> catatan biaya</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-panel p-3">
            <div class="small text-muted"><i class="bi bi-graph-up text-primary"></i> Laba / Rugi</div>
            <div class="fs-4 fw-bold <?= $laba >= 0 ? 'text-primary' : 'text-danger' ?>">
                <?= rupiah($laba) ?>
            </div>
            <small class="text-muted">Pendapatan dikurangi pengeluaran</small>
        </div>
    </div>
</div>

<!-- ===================== DAFTAR LAPORAN ===================== -->
<div class="card-panel p-3 mb-3" id="daftar">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="fw-bold mb-0">Daftar Laporan Keuangan</h6>
            <small class="text-muted">Rekap per bulan tahun <?= e($tahun) ?>. Klik bulan untuk melihat rincian.</small>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th class="text-end">Pendapatan</th>
                    <th class="text-end">Pengeluaran</th>
                    <th class="text-end">Laba / Rugi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rekap as $r): ?>
                    <tr class="<?= $bulan === $r['bulan'] ? 'table-primary' : '' ?>">
                        <td class="fw-semibold"><?= e($r['nama']) ?> <?= e($tahun) ?></td>
                        <td class="text-end text-success"><?= rupiah($r['pendapatan']) ?></td>
                        <td class="text-end text-danger"><?= rupiah($r['pengeluaran']) ?></td>
                        <td class="text-end fw-semibold <?= $r['laba'] >= 0 ? 'text-primary' : 'text-danger' ?>">
                            <?= rupiah($r['laba']) ?>
                        </td>
                        <td class="text-center">
                            <a href="keuangan.php?tahun=<?= e($tahun) ?>&bulan=<?= $r['bulan'] ?>#daftar"
                               class="btn btn-sm btn-outline-primary">Lihat</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3">
    <!-- ===================== PENDAPATAN ===================== -->
    <div class="col-lg-6">
        <div class="card-panel p-3 h-100" id="pendapatan">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-cash-coin text-success"></i> Laporan Pendapatan</h6>
                <span class="badge bg-success"><?= count($pendapatan) ?> transaksi</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode / Tanggal</th><th>Pelanggan</th>
                            <th>Metode</th><th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$pendapatan): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada pendapatan pada periode ini.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($pendapatan as $p): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold small"><?= e($p['kode_pesanan']) ?></div>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($p['tanggal'])) ?></small>
                                </td>
                                <td class="small"><?= e($p['nama_lengkap'] ?? '-') ?></td>
                                <td class="small text-muted"><?= e($p['metode']) ?></td>
                                <td class="text-end fw-semibold text-success"><?= rupiah($p['jumlah']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if ($pendapatan): ?>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="3" class="text-end">Total Pendapatan</th>
                                <th class="text-end text-success"><?= rupiah($total_pendapatan) ?></th>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
            <small class="text-muted d-block mt-2">
                Pendapatan dihitung dari pembayaran berstatus Diverifikasi pada pesanan yang tidak dibatalkan/retur.
            </small>
        </div>
    </div>

    <!-- ===================== PENGELUARAN ===================== -->
    <div class="col-lg-6">
        <div class="card-panel p-3 h-100" id="pengeluaran">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff text-danger"></i> Laporan Pengeluaran</h6>
                <span class="badge bg-danger"><?= count($pengeluaran) ?> catatan</span>
            </div>

            <form method="post" class="row g-2 mb-3">
                <input type="hidden" name="aksi" value="tambah">
                <div class="col-6">
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-6">
                    <select name="kategori" class="form-select form-select-sm" required>
                        <option value="">-- Kategori --</option>
                        <?php foreach ($kategori_list as $k): ?>
                            <option value="<?= e($k) ?>"><?= e($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-7">
                    <input type="text" name="keterangan" class="form-control form-control-sm"
                           placeholder="Keterangan biaya" required>
                </div>
                <div class="col-5">
                    <input type="number" name="jumlah" min="1" step="1" class="form-control form-control-sm"
                           placeholder="Jumlah (Rp)" required>
                </div>
                <div class="col-7">
                    <select name="metode" class="form-select form-select-sm">
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer">Transfer</option>
                    </select>
                </div>
                <div class="col-5">
                    <button class="btn btn-sm btn-danger w-100">
                        <i class="bi bi-plus-lg"></i> Catat Pengeluaran
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th><th>Kategori</th><th>Keterangan</th>
                            <th class="text-end">Jumlah</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$pengeluaran): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengeluaran pada periode ini.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($pengeluaran as $x): ?>
                            <tr>
                                <td class="text-muted small"><?= date('d/m/Y', strtotime($x['tanggal'])) ?></td>
                                <td><span class="badge bg-secondary"><?= e($x['kategori']) ?></span></td>
                                <td class="small"><?= e($x['keterangan']) ?></td>
                                <td class="text-end fw-semibold text-danger"><?= rupiah($x['jumlah']) ?></td>
                                <td class="text-center">
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('Hapus catatan pengeluaran ini?')">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="id_pengeluaran" value="<?= (int)$x['id_pengeluaran'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if ($pengeluaran): ?>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="3" class="text-end">Total Pengeluaran</th>
                                <th class="text-end text-danger"><?= rupiah($total_pengeluaran) ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
