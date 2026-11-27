<?php
/** Produk terbaru di beranda.php. Butuh $db_aktif dan $produk_terbaru. */
?>
<section class="section-title">
    <div>
        <h2><i class="bi bi-box-seam text-primary"></i> Produk Terbaru</h2>
        <p>Barang baru yang baru saja masuk katalog.</p>
    </div>
    <a href="katalog.php" class="btn btn-sm btn-outline-primary">Katalog lengkap</a>
</section>

<section class="row g-3">
    <?php if (!$db_aktif): ?>
        <div class="col-12"><div class="cek-db small">Data produk tidak dapat dimuat.</div></div>
    <?php elseif (!$produk_terbaru): ?>
        <div class="col-12"><div class="alert alert-info mb-0">Belum ada produk aktif.</div></div>
    <?php else: ?>
        <?php foreach ($produk_terbaru as $produk):
            $kolom          = 'col-6 col-md-4 col-lg-3';
            $tombol_label   = 'Lihat Detail';
            $tombol_url     = 'katalog.php?detail=' . (int)$produk['id_produk'];
            $tombol_class   = 'btn btn-outline-primary btn-sm mt-auto';
            $tombol_disable = (int)$produk['stok'] <= 0;
            include __DIR__ . '/../../layout/kartu-produk.php';
        endforeach; ?>
    <?php endif; ?>
</section>
