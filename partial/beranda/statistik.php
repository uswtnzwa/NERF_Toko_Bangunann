<?php
/** Angka ringkasan toko di beranda.php. Butuh $db_aktif dan $statistik. */
?>
<?php if ($db_aktif): ?>
<section class="row g-3">
    <?php
    $stat = [
        ['Produk Tersedia', $statistik['produk'],    'bi-box-seam',   '#1473e6'],
        ['Kategori',        $statistik['kategori'],  'bi-tags',       '#16a06a'],
        ['Pelanggan Aktif', $statistik['pelanggan'], 'bi-people',     '#8b5cf6'],
        ['Produk Terjual',  $statistik['terjual'],   'bi-cart-check', '#f59e0b'],
    ];
    foreach ($stat as [$label, $val, $ikon, $warna]): ?>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-ikon" style="background:<?= $warna ?>"><i class="bi <?= $ikon ?>"></i></div>
                <div>
                    <div class="fs-4 fw-bold lh-1 mb-1"><?= number_format($val, 0, ',', '.') ?></div>
                    <small class="text-muted"><?= $label ?></small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>
