<?php
/**
 * Lupa Kata Sandi untuk akun PELANGGAN (toko depan).
 *
 * Alur 2 langkah:
 *   1) Masukkan email. Kalau email terdaftar, sistem lanjut ke langkah 2.
 *   2) Masukkan password baru + konfirmasi, langsung menggantikan password lama.
 *
 * Akun admin TIDAK bisa memakai halaman ini. Password admin hanya bisa
 * diubah oleh Super Admin lewat menu "Kelola Admin".
 */
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/lupa-sandi-logic.php';

$judul = 'Lupa Kata Sandi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - NERF Toko Bangunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/auth.css') ?>" rel="stylesheet">
</head>
<body class="auth">
<div>

    <?php include __DIR__ . '/layout/auth-brand.php'; ?>

    <div class="card-otentikasi mx-auto">
        <div class="kepala">
            <div class="logo-bulat">
                <img src="<?= img_url('TokoLogo.png') ?>" alt="Logo" onerror="this.remove()">
            </div>
            <h1>Lupa Kata Sandi</h1>
            <p>Pemulihan password akun pelanggan</p>
        </div>

        <div class="badan">
            <?php include __DIR__ . '/partial/auth-alert.php'; ?>

            <?php if ($langkah2): ?>
                <?php include __DIR__ . '/partial/lupa-sandi-langkah2.php'; ?>
            <?php else: ?>
                <?php include __DIR__ . '/partial/lupa-sandi-langkah1.php'; ?>
            <?php endif; ?>

            <div class="kaki">
                <a href="masuk.php" class="kembali"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
