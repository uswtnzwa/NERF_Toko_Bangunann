<?php
/**
 * Halaman MASUK (satu form untuk semua).
 *
 * Sistem otomatis membaca apakah akun yang masuk berasal dari
 * tabel `admin` (panel) atau tabel `pelanggan` (toko depan),
 * jadi tidak perlu memilih role.
 */
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/masuk-logic.php';

$judul = 'Masuk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - NERF Toko Bangunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/auth.css') ?>" rel="stylesheet">
</head>
<body class="auth">
<div>

    <?php $tampilkan_tag = false; include __DIR__ . '/layout/auth-brand.php'; ?>

    <div class="card-otentikasi mx-auto">
        <div class="kepala">
            <div class="logo-bulat">
                <img src="<?= img_url('TokoLogo.png') ?>" alt="Logo" onerror="this.remove()">
            </div>
            <h1>Masuk</h1>
            <p>Gunakan akun admin atau pelanggan Anda</p>
        </div>

        <div class="badan">
            <?php include __DIR__ . '/partial/auth-alert.php'; ?>

            <form method="post" autocomplete="off">
                <input type="hidden" name="redirect" value="<?= e($redirect_dari_form ?? '') ?>">

                <div class="mb-3">
                    <label class="label">Email atau Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                        <input type="text" name="identitas" class="form-control" required autofocus
                               value="<?= e($identitas) ?>" placeholder="admin atau andi@mail.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="pw" class="form-control" required
                               placeholder="••••••">
                        <button type="button" class="btn btn-outline-secondary" id="togglePw" title="Lihat password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button class="btn btn-aksen w-100 text-white mb-3">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <div class="daftar-kecil">
                <a href="lupa-sandi.php"><i class="bi bi-key"></i> Lupa kata sandi?</a>
            </div>

            <div class="daftar-kecil">
                Belum punya akun? <a href="daftar.php">Daftar di sini</a>
            </div>

            <div class="kaki">
                <a href="index.php" class="kembali"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= aset_url('js/toggle-sandi.js') ?>"></script>
</body>
</html>
