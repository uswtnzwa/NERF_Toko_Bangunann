<?php
// header admin - dipanggil setelah session_start() & config
$admin = admin_saat_ini();
$flash = ambil_flash();

$menu = [
    ['Berkas' => 'index.php',    'Ikon' => 'bi-speedometer2',     'Label' => 'Dashboard'],
    ['Berkas' => 'produk.php',   'Ikon' => 'bi-box-seam',         'Label' => 'Produk'],
    ['Berkas' => 'kategori.php', 'Ikon' => 'bi-tags',             'Label' => 'Kategori'],
    ['Berkas' => 'stok.php',     'Ikon' => 'bi-arrow-left-right', 'Label' => 'Stok Barang'],
    ['Berkas' => 'pesanan.php',  'Ikon' => 'bi-receipt',          'Label' => 'Pesanan'],
    ['Berkas' => 'analitik.php', 'Ikon' => 'bi-bar-chart',        'Label' => 'Analitik'],
    ['Berkas' => 'keuangan.php', 'Ikon' => 'bi-cash-coin',        'Label' => 'Keuangan'],
    ['Berkas' => 'pelanggan.php','Ikon' => 'bi-people',           'Label' => 'Pelanggan'],
    ['Berkas' => 'crm.php',      'Ikon' => 'bi-person-badge',     'Label' => 'CRM'],
    ['Berkas' => 'testimoni.php','Ikon' => 'bi-chat-quote',       'Label' => 'Testimoni'],
    ['Berkas' => 'user.php',     'Ikon' => 'bi-person-gear',      'Label' => 'Kelola Admin'],
];
$halaman = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin NERF - Toko Bangunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/admin.css') ?>" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <aside class="sidebar d-none d-lg-block">
        <a href="../beranda.php" class="brand">
            <div class="logo-box"><img src="<?= img_url('TokoLogo.png') ?>" alt="Logo" onerror="this.remove()"></div>
            Admin <span>NERF</span>
        </a>
        <div class="nav-section">Menu</div>
        <nav class="nav flex-column">
            <?php foreach ($menu as $m): ?>
                <a class="nav-link <?= $halaman === $m['Berkas'] ? 'active' : '' ?>" href="<?= $m['Berkas'] ?>">
                    <i class="bi <?= $m['Ikon'] ?>"></i> <?= $m['Label'] ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="nav-section">Lainnya</div>
        <nav class="nav flex-column">
            <a class="nav-link" href="../keluar.php" style="color:#ff8080">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </nav>
    </aside>

    <div class="flex-grow-1">
        <header class="topbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold"><?= e($judul ?? 'Dashboard') ?></h5>
                <small class="text-muted"><?= date('d F Y, H:i') ?> WIB</small>
            </div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                    <span><?= e($admin['nama_lengkap'] ?? 'Admin') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small text-muted"><?= e($admin['role'] ?? '') ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../keluar.php">Keluar</a></li>
                </ul>
            </div>
        </header>

        <main class="content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['tipe']) ?> alert-dismissible fade show">
                    <?= e($flash['pesan']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
