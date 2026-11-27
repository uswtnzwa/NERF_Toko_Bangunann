<?php
/**
 * BERANDA - halaman depan toko.
 *
 * Data untuk halaman ini diambil di config/beranda-data.php,
 * bagian markup dipisah ke folder partial/ agar file ini tetap ringkas.
 */
session_start();
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/beranda-data.php';

$menu_aktif = 'beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NERF Toko Bangunan - Material Konstruksi Lengkap</title>
    <meta name="description" content="NERF Toko Bangunan menyediakan Semen, besi, kayu, cat, keramik, pipa dan perlengkapan bangunan dengan harga terbaik.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/toko.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/hero.css') ?>" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/layout/navbar.php'; ?>

<div class="container">

    <?php include __DIR__ . '/partial/flash.php'; ?>
    <?php include __DIR__ . '/partial/beranda/hero.php'; ?>
    <?php include __DIR__ . '/partial/peringatan-db.php'; ?>
    <?php include __DIR__ . '/partial/beranda/statistik.php'; ?>
    <?php include __DIR__ . '/partial/beranda/banner.php'; ?>
    <?php include __DIR__ . '/partial/beranda/kategori.php'; ?>
    <?php include __DIR__ . '/partial/beranda/terlaris.php'; ?>
    <?php include __DIR__ . '/partial/beranda/terbaru.php'; ?>
    <?php include __DIR__ . '/partial/beranda/testimoni.php'; ?>
    <?php include __DIR__ . '/partial/beranda/faq.php'; ?>

</div>

<?php
$footer_kategori = $kategori;
include __DIR__ . '/layout/footer.php';
