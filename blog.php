<?php
/**
 * BLOG / BERITA
 *
 * Menampilkan daftar artikel seputar produk material bangunan,
 * tips pengerjaan, dan tren industri konstruksi.
 *
 * Parameter URL yang didukung:
 *   ?kategori=Produk|Tips|Industri   filter kategori artikel
 *   ?cari=<kata kunci>              cari judul, ringkasan, atau isi artikel
 *   ?halaman=<n>                    nomor halaman
 *
 * Detail artikel dibuka melalui artikel.php?slug=<slug>.
 */
session_start();
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/keranjang.php';
require_once __DIR__ . '/config/artikel-query.php';

$menu_aktif       = 'artikel';
$jumlah_keranjang = keranjang_total_qty();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog &amp; Berita - NERF Toko Bangunan</title>
    <meta name="description" content="Artikel seputar produk material bangunan, tips konstruksi, dan tren industri dari NERF Toko Bangunan.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/toko.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/blog.css') ?>" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/layout/navbar.php'; ?>

<div class="container">

    <?php include __DIR__ . '/partial/flash.php'; ?>
    <?php include __DIR__ . '/partial/peringatan-db.php'; ?>

    <?php if ($db_aktif && !$tabel_artikel): ?>
        <div class="cek-db mt-3">
            <strong><i class="bi bi-database-exclamation text-warning"></i> Tabel artikel belum tersedia.</strong>
            <div class="small text-muted mt-1">
                Import file <code>migrations/2026_artikel.sql</code> melalui phpMyAdmin atau MySQL CLI,
                lalu muat ulang halaman ini.
            </div>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/partial/blog/kepala.php'; ?>

    <?php if ($unggulan): ?>
        <?php include __DIR__ . '/partial/blog/unggulan.php'; ?>
    <?php endif; ?>

    <?php include __DIR__ . '/partial/blog/filter.php'; ?>

    <div class="section-title">
        <div>
            <h2><?= $kategori !== '' ? 'Artikel ' . e($kategori) : 'Semua Artikel' ?></h2>
            <p>
                <?php if ($cari !== ''): ?>
                    Hasil pencarian untuk <strong>&ldquo;<?= e($cari) ?>&rdquo;</strong> &middot; <?= $total ?> artikel
                <?php else: ?>
                    Informasi dan inspirasi untuk kebutuhan konstruksi Anda.
                <?php endif; ?>
            </p>
        </div>
    </div>

    <?php if (!$daftar_artikel): ?>
        <div class="blog-kosong">
            <i class="bi bi-file-earmark-x"></i>
            <h5>Belum ada artikel</h5>
            <p class="text-muted small mb-3">
                Tidak ditemukan artikel yang cocok. Coba kata kunci lain atau lihat semua artikel.
            </p>
            <a href="blog.php" class="btn btn-primary btn-sm">Lihat semua artikel</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($daftar_artikel as $a): ?>
                <div class="col-md-6 col-lg-4">
                    <?php include __DIR__ . '/partial/blog/kartu.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php include __DIR__ . '/partial/blog/paginasi.php'; ?>
    <?php endif; ?>

    <div style="height:40px"></div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
