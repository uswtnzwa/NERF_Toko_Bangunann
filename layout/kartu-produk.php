<?php
/**
 * Kartu produk yang dipakai ulang di beranda.php dan katalog.php.
 *
 * Parameter (set sebelum include):
 *   $produk          (wajib) satu baris produk
 *   $kolom           kelas kolom bootstrap, mis. "col-6 col-lg-3"
 *   $tombol_label    teks tombol
 *   $tombol_url      tujuan tombol
 *   $tombol_class    kelas tombol, mis. "btn btn-primary btn-sm mt-auto"
 *   $tombol_disable  true = tombol nonaktif
 *   $bungkus_link    true = seluruh kartu jadi tautan, tombol disembunyikan
 *
 * Semua parameter punya nilai default, jadi cukup include lalu set $produk.
 */

$produk         = $produk ?? [];
$kolom          = $kolom          ?? 'col-6 col-md-4 col-lg-3';
$tombol_label   = $tombol_label   ?? 'Lihat Detail';
$tombol_url     = $tombol_url     ?? 'katalog.php';
$tombol_class   = $tombol_class   ?? 'btn btn-outline-primary btn-sm mt-auto';
$tombol_disable = $tombol_disable ?? false;
$bungkus_link   = $bungkus_link   ?? false;

$diskon = (float)($produk['harga_diskon'] ?? 0) > 0;
$harga  = harga_efektif($produk);
$habis  = (int)($produk['stok'] ?? 0) <= 0;
?>
<div class="<?= e($kolom) ?>">
    <?php if ($bungkus_link): ?>
        <a href="<?= e($tombol_url) ?>" class="text-decoration-none text-reset">
    <?php endif; ?>

    <div class="product-card">
        <div class="product-img">
            <?php if (!empty($produk['gambar'])): ?>
                <img src="uploads/<?= e($produk['gambar']) ?>" alt="<?= e($produk['nama_produk']) ?>" onerror="this.remove()">
            <?php else: ?>
                <i class="bi bi-box text-muted" style="font-size:38px"></i>
            <?php endif; ?>

            <?php if ($diskon): ?>
                <span class="badge bg-danger position-absolute top-0 start-0 m-2">Diskon</span>
            <?php elseif ($habis): ?>
                <span class="badge bg-secondary position-absolute top-0 start-0 m-2">Habis</span>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <div class="product-cat"><?= e($produk['nama_kategori'] ?? 'Umum') ?></div>
            <div class="product-name"><?= e($produk['nama_produk']) ?></div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="product-price"><?= rupiah($harga) ?></span>
                <?php if ($diskon): ?>
                    <del class="text-muted small"><?= rupiah($produk['harga']) ?></del>
                <?php endif; ?>
            </div>

            <div class="product-stock <?= $habis ? 'text-danger' : '' ?>">
                <?= $habis ? 'Stok habis' : 'Sisa ' . (int)$produk['stok'] . ' ' . e($produk['satuan']) ?>
            </div>

            <?php if (!$bungkus_link): ?>
                <a href="<?= e($tombol_url) ?>" class="<?= e($tombol_class) ?> <?= $tombol_disable ? 'disabled' : '' ?>">
                    <?= $tombol_disable ? 'Tidak Tersedia' : e($tombol_label) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($bungkus_link): ?>
        </a>
    <?php endif; ?>
</div>
