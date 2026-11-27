<?php
/**
 * CHECKOUT / PEMESANAN
 *
 * Alur pemesanan dalam tiga tahap (semua di satu halaman):
 *   1. Cara menerima barang   -> ambil di toko / diantar
 *   2. Alamat & biaya tambahan -> hanya untuk "diantar"
 *   3. Metode pembayaran        -> bayar di tempat / transfer
 *
 * Halaman ini wajib login sebagai pelanggan. Setelah pesanan tersimpan,
 * pelanggan diarahkan ke pesanan-saya.php untuk melihat statusnya.
 */
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/keranjang.php';
require_once __DIR__ . '/config/checkout-logic.php';

wajib_pelanggan();

$menu_aktif       = 'checkout';
$jumlah_keranjang = 0;
$footer_kategori  = $koneksi->query(
    "SELECT id_kategori, nama_kategori FROM kategori WHERE status='Aktif' ORDER BY nama_kategori"
)->fetch_all(MYSQLI_ASSOC);

$daftar_kirim = daftar_pengiriman();
$pengaturan    = pengaturan_toko($koneksi);
$ringkas       = keranjang_ringkas($koneksi);

/* ================= keranjang kosong ================= */
if (!$ringkas['barang']) {
    set_flash('info', 'Keranjang Anda masih kosong. Pilih produk dulu sebelum checkout.');
    header('Location: katalog.php');
    exit;
}

/* ================= nilai form yang sedang diisi ================= */
$kirim  = $_POST['metode_pengiriman'] ?? 'ambil_di_toko';
$bayar  = $_POST['metode_pembayaran'] ?? '';
if (!array_key_exists($kirim, $daftar_kirim)) {
    $kirim = 'ambil_di_toko';
}
$di_toko    = $kirim !== 'diantar';
$daftar_bayar = daftar_pembayaran($di_toko);
if (!array_key_exists($bayar, $daftar_bayar)) {
    // Default: COD untuk ambil di toko, transfer untuk diantar
    $bayar = $di_toko ? 'bayar_di_tempat' : 'transfer';
}

/* ================= ongkir ================= */
// Satu kecamatan, jadi biaya antar sama untuk semua alamat.
$ongkir = $kirim === 'diantar' ? ongkir_antar() : 0.0;

/* ================= alamat tersimpan ================= */
$id_pelanggan = (int)$_SESSION['pelanggan_id'];
$alamat_tersimpan = $koneksi->prepare(
    "SELECT a.*, p.nama_lengkap AS nama_pelanggan
     FROM alamat_pelanggan a
     JOIN pelanggan p ON p.id_pelanggan = a.id_pelanggan
     WHERE a.id_pelanggan = ?
     ORDER BY a.utama DESC, a.id_alamat DESC"
);
$alamat_tersimpan->bind_param('i', $id_pelanggan);
$alamat_tersimpan->execute();
$alamat_tersimpan = $alamat_tersimpan->get_result()->fetch_all(MYSQLI_ASSOC);

/* ================= simpan pesanan ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan') {
    $hasil = checkout_simpan($koneksi, $id_pelanggan, $ringkas);

    if ($hasil['sukses']) {
        header('Location: pesanan-saya.php?baru=' . $hasil['id_pesanan']);
        exit;
    }

    // Validasi gagal -> kembali ke form dengan pesan + isian yang tadi.
    set_flash('danger', $hasil['pesan']);
}

// Pesan dari halaman sebelumnya (mis. keranjang kosong) atau dari validasi
// di atas. Dipanggil setelah blok simpan supaya set_flash() ikut terbaca.
$flash = ambil_flash();

$diskon        = 0.0;
$total_bayar   = $ringkas['subtotal'] + $ongkir - $diskon;
$catatan_admin = '';

/* Nilai isian form yang tadi, supaya tidak hilang saat validasi gagal */
$data_alamat = [
    'nama_penerima' => trim($_POST['nama_penerima'] ?? ''),
    'nomor_hp'      => trim($_POST['nomor_hp'] ?? ''),
    'alamat'        => trim($_POST['alamat'] ?? ''),
    'kode_pos'      => trim($_POST['kode_pos'] ?? ''),
    'catatan'       => trim($_POST['catatan'] ?? ''),
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - NERF Toko Bangunan</title>
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
        <a href="beranda.php">Beranda</a> / <a href="katalog.php">Katalog</a> / Checkout
    </nav>

    <h1 class="h4 fw-bold mt-2 mb-1">Checkout</h1>
    <p class="text-muted small mb-4">
        Tiga langkah: pilih cara terima barang, isi alamat bila diantar, lalu pilih pembayaran.
    </p>

    <div class="row g-4">

        <!-- ============ FORM ============ -->
        <div class="col-lg-7">
            <form method="post" id="formCheckout" novalidate>
                <input type="hidden" name="aksi" value="simpan">

                <!-- ---------- langkah 1: pengiriman ---------- -->
                <div class="langkah-kotak">
                    <div class="langkah-judul">
                        <span class="langkah-angka">1</span>
                        <div>
                            <div class="fw-bold">Cara Menerima Barang</div>
                            <div class="text-muted small">Pilih salah satu</div>
                        </div>
                    </div>

                    <div class="pilih-metode">
                        <?php foreach ($daftar_kirim as $kunci => $m): ?>
                            <label class="pilih-kartu">
                                <input type="radio" name="metode_pengiriman" value="<?= e($kunci) ?>"
                                       class="form-check-input mt-1" data-kirim="1"
                                       <?= $kirim === $kunci ? 'checked' : '' ?> required>
                                <div>
                                    <div class="fw-semibold"><i class="bi <?= e($m['Ikon']) ?>"></i> <?= e($m['Label']) ?></div>
                                    <div class="text-muted small"><?= e($m['Keterangan']) ?></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ---------- langkah 2: alamat ---------- -->
                <div class="langkah-kotak">
                    <div class="langkah-judul">
                        <span class="langkah-angka">2</span>
                        <div>
                            <div class="fw-bold">Alamat &amp; Biaya Tambahan</div>
                            <div class="text-muted small">Wajib diisi bila barang diantar. Satu kecamatan, gratis ongkir.</div>
                        </div>
                    </div>

                    <?php
                    include __DIR__ . '/partial/checkout/form-alamat.php';
                    ?>
                </div>

                <!-- ---------- langkah 3: pembayaran ---------- -->
                <div class="langkah-kotak">
                    <div class="langkah-judul">
                        <span class="langkah-angka">3</span>
                        <div>
                            <div class="fw-bold">Metode Pembayaran</div>
                            <div class="text-muted small">Pilih cara yang paling nyaman</div>
                        </div>
                    </div>

                    <?php
                    $metode_pembayaran_terpilih = $bayar;
                    include __DIR__ . '/partial/checkout/metode-pembayaran.php';
                    ?>
                </div>

                <div class="d-grid gap-2 mb-4">
                    <button class="btn btn-primary btn-lg" id="btnSimpan">
                        <i class="bi bi-check2-circle"></i> Buat Pesanan
                    </button>
                    <a href="katalog.php#keranjang" class="btn btn-link text-muted small">Kembali ke keranjang</a>
                </div>
            </form>
        </div>

        <!-- ============ RINGKASAN ============ -->
        <div class="col-lg-5">
            <div class="sticky-top" style="top:80px">
                <?php include __DIR__ . '/partial/checkout/ringkasan.php'; ?>
            </div>
        </div>
    </div>

    <div style="height:40px"></div>
</div>

<!-- Script checkout: diletakkan sebelum footer.php karena footer sudah menutup dokumen -->
<script src="<?= aset_url('js/checkout.js') ?>"></script>

<?php include __DIR__ . '/layout/footer.php'; ?>
