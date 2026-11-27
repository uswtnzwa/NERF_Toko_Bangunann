<?php
/** Produk terlaris di beranda.php. Butuh $db_aktif dan $produk_terlaris. */
?>
<?php if ($db_aktif && $produk_terlaris): ?>
<section class="section-title">
    <div>
        <h2><i class="bi bi-fire text-primary"></i> Terlaris</h2>
        <p>Paling sering dibeli pelanggan kami.</p>
    </div>
    <a href="katalog.php?sort=terlaris" class="btn btn-sm btn-outline-primary">Lihat semua</a>
</section>

<section class="row g-3">
    <?php foreach ($produk_terlaris as $produk):
        $kolom          = 'col-6 col-lg-3';
        $tombol_label   = 'Beli';
        $tombol_url     = 'katalog.php?cari=' . urlencode($produk['kode_produk']);
        $tombol_class   = 'btn btn-primary btn-sm mt-auto';
        $tombol_disable = false;
        include __DIR__ . '/../../layout/kartu-produk.php';
    endforeach; ?>
</section>
<?php endif; ?>
