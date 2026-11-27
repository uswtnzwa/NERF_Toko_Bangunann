<?php
/**
 * KATALOG PRODUK
 *
 * Berisi dua tampilan:
 *   ?detail=<id_produk>  -> detail satu produk + form tambah ke keranjang
 *   (kosong)             -> daftar produk dengan filter, urutan, dan paginasi
 *
 * Parameter URL yang didukung:
 *   ?kategori=<id_kategori>   filter per kategori
 *   ?cari=<kata kunci>        cari kode / nama produk
 *   ?sort=terlaris|termurah|terbaru|namaindex   urutan tampilan
 *   ?halaman=<n>              nomor halaman
 *
 * Keranjang (session) ditangani oleh config/keranjang.php.
 */
session_start();
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/keranjang.php';
require_once __DIR__ . '/config/katalog-query.php';

$menu_aktif       = 'katalog';
$jumlah_keranjang = keranjang_total_qty();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - NERF Toko Bangunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/toko.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/katalog.css') ?>" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/layout/navbar.php'; ?>

<div class="container">

    <?php include __DIR__ . '/partial/flash.php'; ?>

    <?php if (!$db_aktif): ?>
        <div class="cek-db mt-3">
            <strong><i class="bi bi-database-exclamation text-warning"></i> Database belum terhubung.</strong>
            <div class="small text-muted mt-1">
                Nyalakan MySQL di XAMPP, lalu import file <code>NERF_TokoBangunan.sql</code> melalui phpMyAdmin.
            </div>
        </div>
    <?php endif; ?>

    <?php if ($detail): ?>
        <?php include __DIR__ . '/partial/katalog/detail-produk.php'; ?>
    <?php else: ?>
        <?php include __DIR__ . '/partial/katalog/daftar-produk.php'; ?>
    <?php endif; ?>

    <?php include __DIR__ . '/partial/katalog/keranjang.php'; ?>

    <div style="height:40px"></div>
</div>

<?php
$footer_kategori = $daftar_kategori;
include __DIR__ . '/layout/footer.php';
?>

<script src="<?= aset_url('js/katalog-detail.js') ?>"></script>
