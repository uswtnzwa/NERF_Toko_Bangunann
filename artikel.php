<?php
/**
 * DETAIL ARTIKEL
 *
 * Membuka satu artikel berdasarkan slug:
 *   artikel.php?slug=<slug-artikel>
 */
session_start();
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/keranjang.php';
require_once __DIR__ . '/config/artikel-query.php';

$menu_aktif       = 'artikel';
$jumlah_keranjang = keranjang_total_qty();

$slug     = trim($_GET['slug'] ?? '');
$artikel  = null;
$terkait  = [];

if ($slug !== '' && $tabel_artikel) {
    $artikel = artikel_ambil($db, $slug);

    if ($artikel) {
        artikel_tambah_dibaca($db, (int)$artikel['id_artikel']);
        $artikel['dibaca'] = (int)$artikel['dibaca'] + 1;
        $terkait = artikel_terkait($db, (int)$artikel['id_artikel'], $artikel['kategori']);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $artikel ? e($artikel['judul']) . ' - ' : '' ?>NERF Toko Bangunan</title>
    <?php if ($artikel): ?>
        <meta name="description" content="<?= e($artikel['ringkasan']) ?>">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/toko.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/blog.css') ?>" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/layout/navbar.php'; ?>

<div class="container">

    <?php include __DIR__ . '/partial/flash.php'; ?>

    <?php if ($artikel): ?>
        <?php include __DIR__ . '/partial/artikel/detail.php'; ?>
    <?php else: ?>
        <nav class="breadcrumb-katalog mt-3">
            <a href="beranda.php">Beranda</a> / <a href="blog.php">Artikel</a> / Tidak ditemukan
        </nav>
        <div class="blog-kosong">
            <i class="bi bi-file-earmark-x"></i>
            <h5>Artikel tidak ditemukan</h5>
            <p class="text-muted small mb-3">
                Artikel yang Anda cari mungkin sudah dihapus atau tautannya tidak tepat.
            </p>
            <a href="blog.php" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke daftar artikel
            </a>
        </div>
    <?php endif; ?>

    <div style="height:40px"></div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
