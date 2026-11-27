<?php
/**
 * Brand di atas kartu otentikasi (masuk.php, daftar.php, lupa-sandi.php).
 * Set $tampilkan_tag = false sebelum include untuk menyembunyikan tag kecil.
 */
$tampilkan_tag = $tampilkan_tag ?? true;
?>
<div class="brand">
    <div class="nama">NERF<span>Bangun</span></div>
    <?php if ($tampilkan_tag): ?>
        <div class="tag">Toko Bangunan &mdash; ERP_Kel7</div>
    <?php endif; ?>
</div>
