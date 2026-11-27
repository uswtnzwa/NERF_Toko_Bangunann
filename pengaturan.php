<?php
/**
 * PENGATURAN AKUN PELANGGAN
 *
 * Tiga bagian dalam satu halaman:
 *   #profil      -> ubah nama, email, nomor HP, dan foto profil
 *   #password    -> ganti password dengan verifikasi password lama
 *   #notifikasi  -> atur preferensi notifikasi email / WhatsApp
 *
 * Hanya bisa diakses pelanggan yang sudah masuk.
 */
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/pengaturan-logic.php';
require_once __DIR__ . '/config/keranjang.php';

wajib_pelanggan();

$menu_aktif       = 'pengaturan';
$jumlah_keranjang = keranjang_total_qty();
$footer_kategori  = $koneksi->query(
    "SELECT id_kategori, nama_kategori FROM kategori WHERE status='Aktif' ORDER BY nama_kategori"
)->fetch_all(MYSQLI_ASSOC);

$id_pelanggan = (int)$_SESSION['pelanggan_id'];
$pelanggan    = pelanggan_saat_ini();

/* ------------------ proses aksi ------------------ */
$bagian = 'profil';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    switch ($aksi) {
        case 'profil':
            $hasil  = pengaturan_simpan_profil($koneksi, $id_pelanggan, $_POST);
            $bagian = 'profil';
            break;
        case 'password':
            $hasil  = pengaturan_simpan_password($koneksi, $id_pelanggan, $_POST);
            $bagian = 'password';
            break;
        case 'notifikasi':
            $hasil  = pengaturan_simpan_notifikasi($koneksi, $id_pelanggan, $_POST);
            $bagian = 'notifikasi';
            break;
        default:
            $hasil  = ['sukses' => false, 'pesan' => 'Aksi tidak dikenal.'];
            $bagian = 'profil';
    }

    set_flash($hasil['sukses'] ? 'success' : 'danger', $hasil['pesan']);
    header('Location: pengaturan.php#' . $bagian);
    exit;
}

/* ------------------ data untuk tampilan ------------------ */
$preferensi = pengaturan_preferensi($koneksi, $id_pelanggan);
$flash      = ambil_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - NERF Toko Bangunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/toko.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/pengaturan.css') ?>" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/layout/navbar.php'; ?>

<div class="container">

    <?php include __DIR__ . '/partial/flash.php'; ?>

    <nav class="breadcrumb-katalog mt-3">
        <a href="beranda.php">Beranda</a> / Pengaturan Akun
    </nav>

    <header class="kepala-pengaturan">
        <h1><i class="bi bi-gear"></i> Pengaturan Akun</h1>
        <p>Kelola profil, keamanan akun, dan preferensi notifikasi Anda.</p>
    </header>

    <div class="row g-4 align-items-start">
        <div class="col-lg-3">
            <?php include __DIR__ . '/partial/pengaturan/sidebar.php'; ?>
        </div>
        <div class="col-lg-9">
            <?php include __DIR__ . '/partial/pengaturan/profil.php'; ?>
            <?php include __DIR__ . '/partial/pengaturan/password.php'; ?>
            <?php include __DIR__ . '/partial/pengaturan/notifikasi.php'; ?>
        </div>
    </div>

    <div style="height:40px"></div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>

<script src="<?= aset_url('js/pengaturan.js') ?>"></script>
</body>
</html>
