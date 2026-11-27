<?php
/**
 * PESANAN SAYA
 *
 * Halaman pelanggan untuk melihat daftar pesanan, rincian barang,
 * status pengiriman, dan status pembayaran.
 *
 * URL:
 *   ?baru=<id_pesanan>  -> pesanan yang baru dibuat (dari checkout)
 *   ?detail=<id>       -> lihat rincian satu pesanan
 */
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/keranjang.php';   // keranjang_total_qty() untuk badge navbar

wajib_pelanggan();

$menu_aktif       = 'pesanan';
$jumlah_keranjang = keranjang_total_qty();
$footer_kategori  = $koneksi->query(
    "SELECT id_kategori, nama_kategori FROM kategori WHERE status='Aktif' ORDER BY nama_kategori"
)->fetch_all(MYSQLI_ASSOC);

$id_pelanggan = (int)$_SESSION['pelanggan_id'];
$warna_status = [
    'Menunggu Pembayaran' => 'warning',
    'Dibayar'             => 'info',
    'Diproses'            => 'primary',
    'Sedang Disiapkan'    => 'primary',
    'Packing'             => 'primary',
    'Di Antar'            => 'info',
    'Dikirim'             => 'primary',
    'Selesai'             => 'success',
    'Dibatalkan'          => 'danger',
    'Retur'               => 'dark',
];
$warna_bayar = ['Menunggu' => 'warning', 'Diverifikasi' => 'success', 'Ditolak' => 'danger'];

/* ---------- aksi: tandai sudah bayar ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = (int)($_POST['id_pesanan'] ?? 0);
    $aksi       = $_POST['aksi'] ?? '';

    // Pastikan pesanan itu milik pelanggan yang sedang login
    $s = $koneksi->prepare("SELECT id_pesanan FROM pesanan WHERE id_pesanan = ? AND id_pelanggan = ?");
    $s->bind_param('ii', $id_pesanan, $id_pelanggan);
    $s->execute();
    $milik = $s->get_result()->fetch_assoc();

    if (!$milik) {
        set_flash('danger', 'Pesanan tidak ditemukan.');
    } elseif ($aksi === 'bayar_di_tempat') {
        // Pelanggan sudah datang dan membayar di kasir: catat waktu bayar
        // lalu langsung proses. Jangan tulis ulang ke "Menunggu Pembayaran"
        // karena itu status awal dan sama sekali tidak ada artinya di sini.
        $u = $koneksi->prepare(
            "UPDATE pembayaran SET tanggal_pembayaran = NOW()
             WHERE id_pesanan = ? AND status = 'Menunggu'"
        );
        $u->bind_param('i', $id_pesanan);
        $u->execute();
        $u2 = $koneksi->prepare(
            "UPDATE pesanan SET status_pesanan = 'Diproses' WHERE id_pesanan = ?"
        );
        $u2->bind_param('i', $id_pesanan);
        $u2->execute();
        set_flash('success', 'Terima kasih. Kasir akan memproses pembayaran Anda.');
    } elseif ($aksi === 'bayar') {
        // Pesanan diantar: bayar dulu di awal supaya pesanan diproses
        $u = $koneksi->prepare(
            "UPDATE pembayaran SET tanggal_pembayaran = NOW()
             WHERE id_pesanan = ? AND status = 'Menunggu'"
        );
        $u->bind_param('i', $id_pesanan);
        $u->execute();
        set_flash('success', 'Pembayaran dicatat. Admin akan memverifikasi dalam 1x24 jam.');
    } else {
        set_flash('danger', 'Aksi tidak dikenal.');
    }
    header('Location: pesanan-saya.php?detail=' . $id_pesanan);
    exit;
}

/* ---------- data ---------- */
$sql = "SELECT p.*, b.status AS status_bayar, b.metode AS metode_bayar
        FROM pesanan p
        LEFT JOIN pembayaran b ON b.id_pesanan = p.id_pesanan
        WHERE p.id_pelanggan = ?
        ORDER BY p.tanggal_pesanan DESC, p.id_pesanan DESC";
$s = $koneksi->prepare($sql);
$s->bind_param('i', $id_pelanggan);
$s->execute();
$semua_pesanan = $s->get_result()->fetch_all(MYSQLI_ASSOC);

/* ---------- rincian satu pesanan ---------- */
$detail_pesanan = null;
if (isset($_GET['detail'])) {
    $id = (int)$_GET['detail'];
    $s = $koneksi->prepare(
        "SELECT p.*, b.status AS status_bayar, b.metode AS metode_bayar,
                g.nomor_resi, g.kurir, g.status AS status_kirim
         FROM pesanan p
         LEFT JOIN pembayaran b ON b.id_pesanan = p.id_pesanan
         LEFT JOIN pengiriman g ON g.id_pesanan = p.id_pesanan
         WHERE p.id_pesanan = ? AND p.id_pelanggan = ? LIMIT 1"
    );
    $s->bind_param('ii', $id, $id_pelanggan);
    $s->execute();
    $detail_pesanan = $s->get_result()->fetch_assoc();

    if ($detail_pesanan) {
        $s = $koneksi->prepare(
            "SELECT d.*, pr.nama_produk, pr.kode_produk, pr.satuan
             FROM detail_pesanan d
             JOIN produk pr ON pr.id_produk = d.id_produk
             WHERE d.id_pesanan = ?
             ORDER BY d.id_detail"
        );
        $s->bind_param('i', $id);
        $s->execute();
        $detail_pesanan['barang'] = $s->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        set_flash('danger', 'Pesanan tidak ditemukan.');
        header('Location: pesanan-saya.php');
        exit;
    }
}

/* Pesanan yang baru dibuat -> pesan selamat datang */
if (isset($_GET['baru'])) {
    $kode = '';
    foreach ($semua_pesanan as $p) {
        if ((int)$p['id_pesanan'] === (int)$_GET['baru']) {
            $kode = $p['kode_pesanan'];
            break;
        }
    }
    set_flash('success', 'Pesanan ' . ($kode ?: '') . ' berhasil dibuat. Lihat detail dan instruksi pembayarannya di bawah.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - NERF Toko Bangunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/toko.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/checkout.css') ?>" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/layout/navbar.php'; ?>

<div class="container">

    <?php include __DIR__ . '/partial/flash.php'; ?>

    <nav class="breadcrumb-katalog mt-3">
        <a href="beranda.php">Beranda</a> / Pesanan Saya
    </nav>

    <h1 class="h4 fw-bold mt-2 mb-4">Pesanan Saya</h1>

    <?php if ($detail_pesanan): ?>
        <!-- ============ RINCIAN PESANAN ============ -->
        <a href="pesanan-saya.php" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Semua pesanan
        </a>

        <div class="kartu-pesanan">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <div class="kode"><?= e($detail_pesanan['kode_pesanan']) ?></div>
                    <div class="text-muted small">
                        <?= date('d F Y, H:i', strtotime($detail_pesanan['tanggal_pesanan'])) ?> WIB
                    </div>
                </div>
                <span class="badge bg-<?= $warna_status[$detail_pesanan['status_pesanan']] ?? 'secondary' ?> fs-6">
                    <?= e($detail_pesanan['status_pesanan']) ?>
                </span>
            </div>

            <!-- bahasa pengiriman -->
            <hr>
            <div class="row g-3 small">
                <div class="col-md-6">
                    <div class="text-muted">Penerima</div>
                    <div class="fw-semibold"><?= e($detail_pesanan['nama_penerima'] ?: '-') ?></div>
                    <div><?= e($detail_pesanan['nomor_hp'] ?: '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted">Cara Terima Barang</div>
                    <div class="fw-semibold"><?= e($detail_pesanan['metode_pengiriman'] ?: '-') ?></div>
                    <div class="text-muted" style="white-space:pre-line"><?= e($detail_pesanan['alamat'] ?: '-') ?></div>
                    <?php if ($detail_pesanan['catatan']): ?>
                        <div class="fst-italic text-muted mt-1">Catatan: <?= e($detail_pesanan['catatan']) ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($detail_pesanan['nomor_resi']): ?>
                <div class="col-md-6">
                    <div class="text-muted">Nomor Resi</div>
                    <div class="fw-semibold">
                        <?= e($detail_pesanan['kurir']) ?> &mdash; <?= e($detail_pesanan['nomor_resi']) ?>
                    </div>
                    <span class="badge bg-<?= $detail_pesanan['status_kirim'] === 'Sampai' ? 'success' : 'primary' ?>">
                        <?= e($detail_pesanan['status_kirim']) ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <!-- bahasa pembayaran -->
            <hr>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <div class="text-muted small">Metode Pembayaran</div>
                    <div class="fw-semibold"><?= e($detail_pesanan['metode_bayar'] ?: '-') ?></div>
                </div>
                <span class="badge bg-<?= $warna_bayar[$detail_pesanan['status_bayar'] ?? 'Menunggu'] ?? 'secondary' ?>">
                    <?= e($detail_pesanan['status_bayar'] ?: 'Menunggu') ?>
                </span>
            </div>

            <!-- tombol aksi pembayaran -->
            <?php if (($detail_pesanan['status_bayar'] ?? '') === 'Menunggu'
                      && $detail_pesanan['status_pesanan'] !== 'Dibatalkan'): ?>
                <div class="alert alert-light border small mt-3 mb-0">
                    <?php if ($detail_pesanan['metode_pengiriman'] === 'Ambil di Toko'): ?>
                        <form method="post" class="d-flex gap-2 align-items-center flex-wrap">
                            <input type="hidden" name="aksi" value="bayar_di_tempat">
                            <input type="hidden" name="id_pesanan" value="<?= (int)$detail_pesanan['id_pesanan'] ?>">
                            <span class="text-muted mb-0">
                                Sudah datang dan ingin bayar di kasir?
                            </span>
                            <button class="btn btn-sm btn-primary">Saya Sudah Bayar di Toko</button>
                        </form>
                    <?php else: ?>
                        <div class="mb-2">
                            Pesanan diantar. Lakukan pembayaran sesuai metode di atas, lalu tekan tombol
                            agar admin bisa memverifikasi.
                        </div>
                        <form method="post">
                            <input type="hidden" name="aksi" value="bayar">
                            <input type="hidden" name="id_pesanan" value="<?= (int)$detail_pesanan['id_pesanan'] ?>">
                            <button class="btn btn-sm btn-primary">
                                <i class="bi bi-check2-circle"></i> Saya Sudah Bayar
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ============ RINGKASAN BIAYA ============ -->
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="kartu-pesanan h-100">
                    <h6 class="fw-bold mb-3">Barang Dipesan</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead><tr><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr></thead>
                            <tbody>
                                <?php foreach ($detail_pesanan['barang'] as $b): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= e($b['nama_produk']) ?></div>
                                            <code class="small"><?= e($b['kode_produk']) ?></code>
                                        </td>
                                        <td class="text-center"><?= (int)$b['jumlah'] ?> <?= e($b['satuan']) ?></td>
                                        <td class="text-end"><?= rupiah($b['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="ringkasan-kotak">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted small">Subtotal</td><td class="text-end small"><?= rupiah($detail_pesanan['subtotal']) ?></td></tr>
                        <tr>
                            <td class="text-muted small">Biaya antar</td>
                            <td class="text-end small"><?= (float)$detail_pesanan['ongkir'] > 0 ? rupiah($detail_pesanan['ongkir']) : '<span class="text-success">Rp 0</span>' ?></td>
                        </tr>
                        <?php if ((float)$detail_pesanan['diskon'] > 0): ?>
                        <tr><td class="text-muted small">Diskon</td><td class="text-end small text-danger">- <?= rupiah($detail_pesanan['diskon']) ?></td></tr>
                        <?php endif; ?>
                        <tr><th>Total</th><th class="text-end fs-6"><?= rupiah($detail_pesanan['total']) ?></th></tr>
                    </table>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- ============ DAFTAR PESANAN ============ -->
        <?php if (!$semua_pesanan): ?>
            <div class="kartu-pesanan text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size:48px"></i>
                <h6 class="fw-bold mt-3">Belum ada pesanan</h6>
                <p class="text-muted small mb-3">Semua pesanan Anda akan muncul di halaman ini.</p>
                <a href="katalog.php" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <?php foreach ($semua_pesanan as $p): ?>
                <div class="kartu-pesanan">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <div class="kode"><?= e($p['kode_pesanan']) ?></div>
                            <div class="text-muted small">
                                <?= date('d/m/Y H:i', strtotime($p['tanggal_pesanan'])) ?> &middot;
                                <?= e($p['metode_pengiriman'] ?: '-') ?> &middot;
                                <?= e($p['metode_bayar'] ?: '-') ?>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold"><?= rupiah($p['total']) ?></div>
                            <span class="badge bg-<?= $warna_status[$p['status_pesanan']] ?? 'secondary' ?>">
                                <?= e($p['status_pesanan']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            Pembayaran: <span class="badge bg-<?= $warna_bayar[$p['status_bayar'] ?? 'Menunggu'] ?? 'secondary' ?>"><?= e($p['status_bayar'] ?: 'Menunggu') ?></span>
                        </small>
                        <a href="pesanan-saya.php?detail=<?= (int)$p['id_pesanan'] ?>" class="btn btn-sm btn-outline-primary">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    <div style="height:40px"></div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
</body>
</html>
