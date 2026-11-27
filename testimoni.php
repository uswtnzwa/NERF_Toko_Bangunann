<?php
/**
 * TESTIMONI - halaman pelanggan untuk menulis testimoni.
 *
 * Pelanggan yang sudah login bisa mengirim satu testimoni (rating + cerita).
 * Testimoni masuk dengan status 'Pending' dan baru tampil di beranda
 * setelah disetujui admin lewat halaman admin/testimoni.php.
 */
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/testimoni-logic.php';

wajib_pelanggan();

$menu_aktif       = 'tulis-testimoni';
$jumlah_keranjang = 0;
$footer_kategori  = $koneksi->query(
    "SELECT id_kategori, nama_kategori FROM kategori WHERE status='Aktif' ORDER BY nama_kategori"
)->fetch_all(MYSQLI_ASSOC);

$pelanggan = pelanggan_saat_ini();
$id_pelanggan = (int)$_SESSION['pelanggan_id'];
$nama_pelanggan = $pelanggan['nama_lengkap'] ?? ($_SESSION['pelanggan_nama'] ?? 'Pelanggan');

/* ================= kirim testimoni ================= */
$error = '';
$isi_baru = '';
$rating_baru = 5;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan') {
    $hasil = testimoni_simpan($koneksi, $id_pelanggan, $nama_pelanggan, $_POST);

    if ($hasil['sukses']) {
        set_flash('success', $hasil['pesan']);
        header('Location: testimoni.php');
        exit;
    }

    // Validasi gagal -> tampilkan pesan + isian yang tadi di form
    $error      = $hasil['pesan'];
    $isi_baru   = trim($_POST['isi'] ?? '');
    $rating_baru = (int)($_POST['rating'] ?? 5);
}

/* ================= testimoni milik pelanggan ini ================= */
$riwayat = testimoni_saya($koneksi, $id_pelanggan);

$warna_status = ['Pending' => 'warning', 'Tampil' => 'success', 'Ditolak' => 'danger'];
$keterangan   = [
    'Pending' => 'Menunggu ditinjau admin.',
    'Tampil'  => 'Sudah tampil di beranda.',
    'Ditolak' => 'Tidak ditampilkan karena tidak sesuai.',
];

$flash = ambil_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni - NERF Toko Bangunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/toko.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/checkout.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/testimoni.css') ?>" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/layout/navbar.php'; ?>

<div class="container">

    <?php include __DIR__ . '/partial/flash.php'; ?>

    <nav class="breadcrumb-katalog mt-3">
        <a href="beranda.php">Beranda</a> / <a href="beranda.php#testimoni">Testimoni</a> / Tulis Testimoni
    </nav>

    <h1 class="h4 fw-bold mt-2 mb-1">Tulis Testimoni</h1>
    <p class="text-muted small mb-4">
        Ceritakan pengalaman belanja Anda di NERF Toko Bangunan. Testimoni Anda
        akan ditinjau admin sebelum tampil di halaman beranda.
    </p>

    <div class="row g-4">

        <!-- ============ FORM ============ -->
        <div class="col-lg-7">
            <form method="post" novalidate>
                <input type="hidden" name="aksi" value="simpan">

                <div class="langkah-kotak">
                    <div class="langkah-judul">
                        <span class="langkah-angka">1</span>
                        <div>
                            <div class="fw-bold">Penilaian</div>
                            <div class="text-muted small">Berapa bintang pengalaman Anda?</div>
                        </div>
                    </div>

                    <div class="pilih-bintang" role="radiogroup" aria-label="Rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label class="bintang-pilih" title="<?= $i ?> bintang">
                                <input type="radio" name="rating" value="<?= $i ?>"
                                       class="visually-hidden"
                                       <?= (int)$rating_baru === $i ? 'checked' : '' ?> required>
                                <i class="bi bi-star-fill"></i>
                                <span class="small text-muted ms-1"><?= $i ?></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="langkah-kotak">
                    <div class="langkah-judul">
                        <span class="langkah-angka">2</span>
                        <div>
                            <div class="fw-bold">Cerita Anda</div>
                            <div class="text-muted small">
                                Minimal <?= TESTIMONI_MIN ?> karakter, maksimal <?= TESTIMONI_MAX ?>.
                            </div>
                        </div>
                    </div>

                    <label class="form-label small fw-semibold">Testimoni <span class="text-danger">*</span></label>
                    <textarea name="isi" rows="6" class="form-control form-control-sm" required
                              minlength="<?= TESTIMONI_MIN ?>" maxlength="<?= TESTIMONI_MAX ?>"
                              placeholder="Contoh: Barang datang cepat, packing rapi, admin responsif."><?= e($isi_baru) ?></textarea>
                    <div class="form-text">Nama Anda diambil dari akun, jadi tidak bisa diubah di sini.</div>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger small py-2"><?= e($error) ?></div>
                <?php endif; ?>

                <div class="d-grid gap-2 mb-4">
                    <button class="btn btn-primary btn-lg">
                        <i class="bi bi-send"></i> Kirim Testimoni
                    </button>
                    <a href="beranda.php#testimoni" class="btn btn-link text-muted small">Lihat testimoni lainnya</a>
                </div>
            </form>
        </div>

        <!-- ============ RIWAYAT ============ -->
        <div class="col-lg-5">
            <div class="sticky-top" style="top:80px">
                <div class="ringkasan-kotak">
                    <h5 class="fw-bold mb-1">Testimoni Anda</h5>
                    <p class="small text-muted mb-3">Riwayat pengiriman yang sudah pernah Anda kirim.</p>

                    <?php if (!$riwayat): ?>
                        <div class="text-muted small py-3 text-center">
                            <i class="bi bi-chat-square-text d-block mb-2" style="font-size:26px"></i>
                            Belum ada testimoni dari akun ini.
                        </div>
                    <?php endif; ?>

                    <?php foreach ($riwayat as $t): ?>
                        <div class="ringkasan-item">
                            <div class="flex-grow-1">
                                <div class="bintang mb-1" style="font-size:13px">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi <?= $i <= (int)$t['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <div class="small mb-1"><?= e($t['isi']) ?></div>
                                <div class="text-muted" style="font-size:12px">
                                    <?= date('d/m/Y', strtotime($t['dibuat_pada'])) ?>
                                    &middot; <?= e($keterangan[$t['status']] ?? $t['status']) ?>
                                </div>
                            </div>
                            <span class="badge bg-<?= $warna_status[$t['status']] ?? 'secondary' ?>">
                                <?= e($t['status']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div style="height:40px"></div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>