<?php
/**
 * Navbar toko depan - dipakai beranda.php dan katalog.php.
 *
 * Variabel opsional sebelum include:
 *   $menu_aktif  -> nama menu yang sedang aktif ('beranda' | 'katalog')
 *   $jumlah_keranjang -> total barang di keranjang (hanya tampil bila > 0)
 */

$menu_aktif        = $menu_aktif ?? '';
$jumlah_keranjang  = $jumlah_keranjang ?? 0;

$menu_toko = [
    ['Kunci' => 'beranda', 'Label' => 'Beranda',   'URL' => 'beranda.php'],
    ['Kunci' => 'katalog', 'Label' => 'Katalog',   'URL' => 'katalog.php'],
    ['Kunci' => 'artikel', 'Label' => 'Artikel',   'URL' => 'blog.php'],
    ['Kunci' => 'kategori', 'Label' => 'Kategori', 'URL' => 'beranda.php#kategori'],
    ['Kunci' => 'testimoni', 'Label' => 'Testimoni', 'URL' => 'beranda.php#testimoni'],
    ['Kunci' => 'faq',     'Label' => 'FAQ',       'URL' => 'beranda.php#faq'],
    ['Kunci' => 'customer-service', 'Label' => 'Customer Service', 'URL' => 'customer-service.php'],
];

// Menu "Pesanan Saya" & "Tulis Testimoni" hanya untuk pelanggan yang sudah masuk.
// Kunci "tulis-testimoni" sengaja dibedakan dari "testimoni" (link ke beranda).
if (pelanggan_terlogin()) {
    $menu_toko = array_merge(
        $menu_toko,
        [
            ['Kunci' => 'pesanan',         'Label' => 'Pesanan Saya',    'URL' => 'pesanan-saya.php'],
            ['Kunci' => 'tulis-testimoni', 'Label' => 'Tulis Testimoni', 'URL' => 'testimoni.php'],
        ]
    );
}
?>
<nav class="navbar-kel7 sticky-top">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="beranda.php" class="brand">
            <div class="logo-box">
                <img src="<?= img_url('TokoLogo.png') ?>" alt="Logo Toko"
                     onerror="this.remove(); document.getElementById('logoText').style.display='flex';">
                <span id="logoText" style="display:none; width:100%; height:100%; align-items:center; justify-content:center;">NERF</span>
            </div>
            NERF
        </a>

        <div class="d-none d-md-flex gap-3 align-items-center">
            <?php foreach ($menu_toko as $m):
                $aktif = $menu_aktif === $m['Kunci']; ?>
                <a href="<?= $m['URL'] ?>" class="nav-link nav-link-kel7<?= $aktif ? ' active' : '' ?>"><?= e($m['Label']) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <?php if ($jumlah_keranjang > 0): ?>
                <a href="katalog.php#keranjang" class="btn btn-outline-light btn-sm position-relative">
                    <i class="bi bi-bag"></i> Keranjang
                    <span class="badge bg-danger rounded-pill"><?= (int)$jumlah_keranjang ?></span>
                </a>
            <?php endif; ?>

            <?php if (pelanggan_terlogin()): ?>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <span class="avatar-mini"><?= e(initials($_SESSION['pelanggan_nama'] ?? 'P')) ?></span>
                        <span class="d-none d-sm-inline"><?= e(explode(' ', $_SESSION['pelanggan_nama'] ?? 'Pelanggan')[0]) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted"><?= e($_SESSION['pelanggan_email'] ?? '') ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="katalog.php"><i class="bi bi-bag"></i> Katalog</a></li>
                        <li><a class="dropdown-item" href="pesanan-saya.php"><i class="bi bi-receipt"></i> Pesanan Saya</a></li>
                        <li><a class="dropdown-item" href="testimoni.php"><i class="bi bi-chat-quote"></i> Tulis Testimoni</a></li>
                        <li><a class="dropdown-item" href="pengaturan.php"><i class="bi bi-gear"></i> Pengaturan Akun</a></li>
                        <li><a class="dropdown-item" href="customer-service.php"><i class="bi bi-whatsapp"></i> Customer Service</a></li>
                        <li><a class="dropdown-item" href="syarat-ketentuan.php"><i class="bi bi-file-text"></i> Syarat &amp; Ketentuan</a></li>
                        <li><a class="dropdown-item" href="kebijakan-privasi.php"><i class="bi bi-shield-lock"></i> Kebijakan Privasi</a></li>
                        <li><a class="dropdown-item text-danger" href="keluar.php"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <!-- Tombol "Masuk" mengarah ke masuk.php, tidak perlu logika tambahan di sini -->
                <a href="masuk.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
