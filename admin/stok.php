<?php
/**
 * STOK BARANG - daftar stok, CRUD mutasi, dan peringatan stok habis.
 *
 * Fitur:
 *   - Peringatan produk Habis / Menipis di bagian atas
 *   - Daftar stok semua produk (pencarian, filter status, paginasi)
 *   - CREATE   : catat mutasi stok (Masuk / Keluar / Penyesuaian)
 *   - READ     : daftar stok + riwayat mutasi (bisa difilter per produk)
 *   - UPDATE   : edit mutasi & atur batas stok minimum
 *   - DELETE   : hapus satu mutasi / seluruh riwayat suatu produk
 */
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
require_once __DIR__ . '/aksi/stok.php';

butuh_role(['Super Admin', 'Admin', 'Warehouse']);

$judul = 'Stok Barang';

/* ================== PROSES AKSI ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $anchor = 'mutasi';

    switch ($aksi) {
        case 'mutasi':
            [$tipe, $pesan] = stok_mutasi_create(
                $koneksi,
                (int)($_POST['id_produk'] ?? 0),
                $_POST['jenis'] ?? 'Masuk',
                (int)($_POST['jumlah'] ?? 0),
                trim($_POST['keterangan'] ?? '')
            );
            break;

        case 'edit':
            [$tipe, $pesan] = stok_mutasi_update(
                $koneksi,
                (int)($_POST['id_stok'] ?? 0),
                $_POST['jenis'] ?? 'Masuk',
                (int)($_POST['jumlah'] ?? 0),
                trim($_POST['keterangan'] ?? '')
            );
            $anchor = 'mutasi';
            break;

        case 'minimum':
            [$tipe, $pesan] = stok_set_minimum(
                $koneksi,
                (int)($_POST['id_produk'] ?? 0),
                (int)($_POST['stok_minimum'] ?? 0)
            );
            $anchor = 'daftar';
            break;

        case 'hapus':
            [$tipe, $pesan] = stok_mutasi_delete($koneksi, (int)($_POST['id_stok'] ?? 0));
            $anchor = 'riwayat';
            break;

        case 'hapus_riwayat':
            [$tipe, $pesan] = stok_hapus_riwayat($koneksi, (int)($_POST['id_produk'] ?? 0));
            $anchor = 'riwayat';
            break;

        default:
            [$tipe, $pesan] = ['danger', 'Aksi tidak dikenal.'];
    }

    set_flash($tipe, $pesan);
    header('Location: stok.php#' . $anchor);
    exit;
}

/* ================== DATA ================== */

/* Semua produk untuk pilihan form. */
$produk_list = $koneksi->query(
    'SELECT id_produk, kode_produk, nama_produk, stok, stok_minimum, satuan
     FROM produk ORDER BY nama_produk'
)->fetch_all(MYSQLI_ASSOC);

/* Peringatan stok. */
$habis = $koneksi->query(
    'SELECT id_produk, kode_produk, nama_produk, stok, satuan FROM produk
     WHERE stok <= 0 ORDER BY nama_produk'
)->fetch_all(MYSQLI_ASSOC);

$menipis = $koneksi->query(
    'SELECT id_produk, kode_produk, nama_produk, stok, stok_minimum, satuan FROM produk
     WHERE stok > 0 AND stok <= stok_minimum ORDER BY stok ASC, nama_produk ASC'
)->fetch_all(MYSQLI_ASSOC);

/* Filter daftar stok. */
$cari   = trim($_GET['cari'] ?? '');
$status = in_array($_GET['status'] ?? '', ['Habis', 'Menipis', 'Aman'], true) ? $_GET['status'] : '';
$halaman = max(1, (int)($_GET['halaman'] ?? 1));
$per_halaman = 10;

$where = [];
$types = '';
$nilai = [];
if ($cari !== '') {
    $where[] = '(kode_produk LIKE ? OR nama_produk LIKE ?)';
    $kunci = '%' . $cari . '%';
    $types .= 'ss';
    $nilai[] = $kunci;
    $nilai[] = $kunci;
}
if ($status === 'Habis') {
    $where[] = 'stok <= 0';
} elseif ($status === 'Menipis') {
    $where[] = 'stok > 0 AND stok <= stok_minimum';
} elseif ($status === 'Aman') {
    $where[] = 'stok > stok_minimum';
}
$sql_where = $where ? implode(' AND ', $where) : '1=1';

$stmt = $koneksi->prepare("SELECT COUNT(*) c FROM produk WHERE $sql_where");
if ($types !== '') {
    $stmt->bind_param($types, ...$nilai);
}
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()['c'];
$total_halaman = max(1, (int)ceil($total / $per_halaman));
$halaman = min($halaman, $total_halaman);
$offset = ($halaman - 1) * $per_halaman;

$stmt = $koneksi->prepare(
    "SELECT id_produk, kode_produk, nama_produk, stok, stok_minimum, satuan
     FROM produk WHERE $sql_where
     ORDER BY (stok <= 0) DESC, (stok > 0 AND stok <= stok_minimum) DESC, nama_produk ASC
     LIMIT ? OFFSET ?"
);
$stmt->bind_param($types . 'ii', ...array_merge($nilai, [$per_halaman, $offset]));
$stmt->execute();
$daftar = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* Riwayat mutasi (bisa difilter per produk). */
$produk_filter = (int)($_GET['produk'] ?? 0);
if ($produk_filter > 0) {
    $r = $koneksi->prepare(
        'SELECT s.*, p.nama_produk, p.kode_produk, p.satuan
         FROM stok s JOIN produk p ON p.id_produk = s.id_produk
         WHERE s.id_produk = ? ORDER BY s.id_stok DESC LIMIT 30'
    );
    $r->bind_param('i', $produk_filter);
    $r->execute();
    $riwayat = $r->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $riwayat = $koneksi->query(
        'SELECT s.*, p.nama_produk, p.kode_produk, p.satuan
         FROM stok s JOIN produk p ON p.id_produk = s.id_produk
         ORDER BY s.id_stok DESC LIMIT 30'
    )->fetch_all(MYSQLI_ASSOC);
}

/* Mode edit mutasi. */
$edit = null;
if (isset($_GET['edit'])) {
    $e = $koneksi->prepare('SELECT * FROM stok WHERE id_stok = ?');
    $id_edit = (int)$_GET['edit'];
    $e->bind_param('i', $id_edit);
    $e->execute();
    $edit = $e->get_result()->fetch_assoc() ?: null;
    if (!$edit) {
        set_flash('danger', 'Mutasi yang akan diedit tidak ditemukan.');
        header('Location: stok.php#riwayat');
        exit;
    }
}

$produk_terpilih = (int)($_GET['produk'] ?? ($edit['id_produk'] ?? 0));
$nama_produk_filter = 'Produk';
foreach ($produk_list as $p) {
    if ((int)$p['id_produk'] === $produk_filter) {
        $nama_produk_filter = $p['nama_produk'];
        break;
    }
}
$warna_jenis = ['Masuk' => 'success', 'Keluar' => 'danger', 'Penyesuaian' => 'warning'];

include __DIR__ . '/layout/header.php';
?>

<!-- ===================== PERINGATAN STOK ===================== -->
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="stok-alert stok-alert-merah">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-x-octagon"></i> Stok Habis</h6>
                <span class="badge bg-danger"><?= count($habis) ?> produk</span>
            </div>
            <?php if (!$habis): ?>
                <div class="small text-muted mt-2">Tidak ada produk yang stoknya habis.</div>
            <?php else: ?>
                <div class="stok-alert-list">
                    <?php foreach (array_slice($habis, 0, 5) as $p): ?>
                        <a href="stok.php?produk=<?= (int)$p['id_produk'] ?>#riwayat">
                            <?= e($p['nama_produk']) ?>
                            <span class="badge bg-light text-danger"><?= e($p['kode_produk']) ?></span>
                        </a>
                    <?php endforeach; ?>
                    <?php if (count($habis) > 5): ?>
                        <span class="text-muted small">+<?= count($habis) - 5 ?> produk lainnya</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stok-alert stok-alert-kuning">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle"></i> Stok Menipis</h6>
                <span class="badge bg-warning text-dark"><?= count($menipis) ?> produk</span>
            </div>
            <?php if (!$menipis): ?>
                <div class="small text-muted mt-2">Semua stok berada di atas batas minimum.</div>
            <?php else: ?>
                <div class="stok-alert-list">
                    <?php foreach (array_slice($menipis, 0, 5) as $p): ?>
                        <a href="stok.php?produk=<?= (int)$p['id_produk'] ?>#riwayat">
                            <?= e($p['nama_produk']) ?>
                            <span class="badge bg-light text-dark">
                                <?= (int)$p['stok'] ?> / <?= (int)$p['stok_minimum'] ?> <?= e($p['satuan']) ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                    <?php if (count($menipis) > 5): ?>
                        <span class="text-muted small">+<?= count($menipis) - 5 ?> produk lainnya</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- ===================== FORM CREATE / UPDATE ===================== -->
    <div class="col-lg-4">
        <div class="card-panel p-4" id="mutasi">
            <h6 class="fw-bold mb-3">
                <i class="bi <?= $edit ? 'bi-pencil-square' : 'bi-plus-circle' ?> text-primary"></i>
                <?= $edit ? 'Edit Mutasi Stok' : 'Catat Mutasi Stok' ?>
            </h6>

            <form method="post">
                <?php if ($edit): ?>
                    <input type="hidden" name="aksi" value="edit">
                    <input type="hidden" name="id_stok" value="<?= (int)$edit['id_stok'] ?>">
                    <div class="alert alert-warning py-2 small">
                        Mengubah mutasi akan menyesuaikan stok produk secara otomatis.
                    </div>
                <?php else: ?>
                    <input type="hidden" name="aksi" value="mutasi">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Produk *</label>
                        <select name="id_produk" class="form-select form-select-sm" required>
                            <option value="">-- Pilih produk --</option>
                            <?php foreach ($produk_list as $p): ?>
                                <option value="<?= (int)$p['id_produk'] ?>"
                                    <?= $produk_terpilih === (int)$p['id_produk'] ? 'selected' : '' ?>>
                                    <?= e($p['kode_produk']) ?> - <?= e($p['nama_produk']) ?>
                                    (<?= (int)$p['stok'] ?> <?= e($p['satuan']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="mb-2">
                    <label class="form-label small fw-semibold">Jenis *</label>
                    <select name="jenis" class="form-select form-select-sm" required>
                        <?php foreach (['Masuk', 'Keluar', 'Penyesuaian'] as $j): ?>
                            <option value="<?= $j ?>"
                                <?= ($edit['jenis'] ?? '') === $j ? 'selected' : '' ?>><?= $j ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Jumlah *</label>
                    <input type="number" name="jumlah" min="1" class="form-control form-control-sm"
                           value="<?= (int)($edit['jumlah'] ?? 0) ?: '' ?>" required>
                    <small class="text-muted">
                        Untuk Penyesuaian, isi stok akhir yang diinginkan.
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="form-control form-control-sm"
                              placeholder="Contoh: Pembelian dari supplier"><?= e($edit['keterangan'] ?? '') ?></textarea>
                </div>

                <button class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-save"></i> <?= $edit ? 'Simpan Perubahan' : 'Simpan Mutasi' ?>
                </button>
                <?php if ($edit): ?>
                    <a href="stok.php#riwayat" class="btn btn-link btn-sm w-100 text-muted">Batal</a>
                <?php endif; ?>
            </form>

            <hr>
            <h6 class="fw-bold small mb-2"><i class="bi bi-sliders"></i> Atur Stok Minimum</h6>
            <form method="post" class="row g-2">
                <input type="hidden" name="aksi" value="minimum">
                <div class="col-8">
                    <select name="id_produk" class="form-select form-select-sm" required>
                        <option value="">-- Produk --</option>
                        <?php foreach ($produk_list as $p): ?>
                            <option value="<?= (int)$p['id_produk'] ?>"
                                <?= $produk_terpilih === (int)$p['id_produk'] ? 'selected' : '' ?>>
                                <?= e($p['nama_produk']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-4">
                    <input type="number" name="stok_minimum" min="0" class="form-control form-control-sm"
                           placeholder="Min" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-check2"></i> Simpan Minimum
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===================== DAFTAR STOK ===================== -->
    <div class="col-lg-8">
        <div class="card-panel p-3 mb-3" id="daftar">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-0">Daftar Stok</h6>
                    <small class="text-muted"><?= $total ?> produk ditemukan</small>
                </div>
                <form method="get" class="d-flex gap-1">
                    <input type="text" name="cari" value="<?= e($cari) ?>"
                           class="form-control form-control-sm" placeholder="Cari produk..." style="width:170px">
                    <select name="status" class="form-select form-select-sm" style="width:120px">
                        <option value="">Semua</option>
                        <?php foreach (['Habis', 'Menipis', 'Aman'] as $s): ?>
                            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Min</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$daftar): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada produk yang cocok.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($daftar as $p):
                            $st = stok_status((int)$p['stok'], (int)$p['stok_minimum']); ?>
                            <tr class="<?= $st['label'] === 'Habis' ? 'table-danger' : ($st['label'] === 'Menipis' ? 'table-warning' : '') ?>">
                                <td>
                                    <div class="fw-semibold"><?= e($p['nama_produk']) ?></div>
                                    <small class="text-muted"><?= e($p['kode_produk']) ?></small>
                                </td>
                                <td class="text-center fw-bold">
                                    <?= (int)$p['stok'] ?> <small class="text-muted"><?= e($p['satuan']) ?></small>
                                </td>
                                <td class="text-center text-muted"><?= (int)$p['stok_minimum'] ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $st['warna'] ?>">
                                        <i class="bi <?= $st['ikon'] ?>"></i> <?= $st['label'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="stok.php?produk=<?= (int)$p['id_produk'] ?>#riwayat"
                                       class="btn btn-sm btn-outline-secondary" title="Lihat riwayat">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('Hapus seluruh riwayat mutasi <?= e($p['nama_produk']) ?>? Stok saat ini tidak berubah.')">
                                        <input type="hidden" name="aksi" value="hapus_riwayat">
                                        <input type="hidden" name="id_produk" value="<?= (int)$p['id_produk'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus riwayat">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_halaman > 1): ?>
                <nav class="mt-3">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                            <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
                                <a class="page-link" href="<?= e('stok.php?' . http_build_query(array_merge($_GET, ['halaman' => $i]))) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <!-- ===================== RIWAYAT MUTASI ===================== -->
        <div class="card-panel p-3" id="riwayat">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <h6 class="fw-bold mb-0">
                    Riwayat Mutasi
                    <?php if ($produk_filter > 0): ?>
                        <small class="text-muted fw-normal">(<?= e($nama_produk_filter) ?>)</small>
                    <?php endif; ?>
                </h6>
                <?php if ($produk_filter > 0): ?>
                    <a href="stok.php#riwayat" class="btn btn-sm btn-link text-muted">Tampilkan semua</a>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th><th>Produk</th><th>Jenis</th>
                            <th class="text-center">Jumlah</th><th>Keterangan</th><th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$riwayat): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat mutasi.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($riwayat as $r): ?>
                            <tr>
                                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($r['tanggal'])) ?></td>
                                <td class="fw-semibold small"><?= e($r['nama_produk']) ?></td>
                                <td><span class="badge bg-<?= $warna_jenis[$r['jenis']] ?? 'secondary' ?>"><?= e($r['jenis']) ?></span></td>
                                <td class="text-center"><?= (int)$r['jumlah'] ?> <?= e($r['satuan']) ?></td>
                                <td class="text-muted small"><?= e($r['keterangan'] ?? '-') ?></td>
                                <td class="text-center">
                                    <a href="stok.php?edit=<?= (int)$r['id_stok'] ?>#mutasi"
                                       class="btn btn-sm btn-outline-primary" title="Edit mutasi">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('Hapus mutasi ini? Stok produk akan disesuaikan.')">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="id_stok" value="<?= (int)$r['id_stok'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus mutasi">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
