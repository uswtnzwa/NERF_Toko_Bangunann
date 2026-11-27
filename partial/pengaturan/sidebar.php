<?php
/**
 * Sidebar akun: identitas pelanggan + tautan ke tiga bagian pengaturan.
 * Butuh $pelanggan dari pengaturan.php.
 */
$foto = $pelanggan['foto'] ?? '';
?>
<div class="akun-sidebar">
    <div class="akun-identitas">
        <div class="akun-foto">
            <?php if ($foto !== ''): ?>
                <img src="<?= e(img_url('uploads/' . $foto)) ?>" alt="Foto profil">
            <?php else: ?>
                <span><?= e(initials($pelanggan['nama_lengkap'] ?? 'P')) ?></span>
            <?php endif; ?>
        </div>
        <div class="fw-bold small mt-2"><?= e($pelanggan['nama_lengkap'] ?? 'Pelanggan') ?></div>
        <div class="text-muted" style="font-size:12px; word-break:break-all">
            <?= e($pelanggan['email'] ?? '') ?>
        </div>
    </div>

    <div class="akun-menu">
        <a href="#profil" class="aktif"><i class="bi bi-person"></i> Profil</a>
        <a href="#password"><i class="bi bi-key"></i> Ganti Password</a>
        <a href="#notifikasi"><i class="bi bi-bell"></i> Notifikasi</a>
        <hr>
        <a href="pesanan-saya.php"><i class="bi bi-receipt"></i> Pesanan Saya</a>
        <a href="beranda.php"><i class="bi bi-shop"></i> Kembali Belanja</a>
    </div>
</div>
