<?php
/** Daftar produk katalog (bukan detail). */
?>
<div class="kepala-katalog">
    <nav class="breadcrumb-katalog">
        <a href="beranda.php">Beranda</a> /
        <?php if ($kategori_terpilih): ?>
            <a href="katalog.php">Katalog</a> / <?= e($kategori_terpilih['nama_kategori']) ?>
        <?php else: ?>
            Katalog
        <?php endif; ?>
    </nav>

    <h1><?= $kategori_terpilih ? e($kategori_terpilih['nama_kategori']) : 'Katalog Produk' ?></h1>

    <p>
        <?php if ($kategori_terpilih && $kategori_terpilih['deskripsi']): ?>
            <?= e($kategori_terpilih['deskripsi']) ?>
        <?php else: ?>
            Semua material konstruksi NERF dalam satu halaman. Filter, cari, dan pilih sesuai kebutuhan proyek Anda.
        <?php endif; ?>
    </p>

    <?php if ($db_aktif): ?>
        <div class="mt-3 small">
            <span class="badge bg-light text-dark me-2"><?= $total ?> produk ditemukan</span>
            <span class="badge bg-light text-dark"><?= $total_halaman ?> halaman</span>
        </div>
    <?php endif; ?>
</div>

<?php if (!$db_aktif): ?>

<div class="alert alert-warning">Katalog tidak dapat dimuat karena database belum terhubung.</div>

<?php else: ?>
<div class="row g-3">
    <?php include __DIR__ . '/filter.php'; ?>

    <div class="col-lg-9">
        <div class="panel-filter d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div class="small text-muted">
                Menampilkan <strong><?= $total === 0 ? 0 : ($offset + 1) ?>&ndash;<?= min($offset + $per_halaman, $total) ?></strong>
                dari <strong><?= $total ?></strong> produk
            </div>
            <div class="d-flex gap-1 flex-wrap">
                <?= tautan_sort('', 'Default', $sort) ?>
                <?= tautan_sort('terlaris', 'Terlaris', $sort) ?>
                <?= tautan_sort('termurah', 'Termurah', $sort) ?>
                <?= tautan_sort('terbaru', 'Terbaru', $sort) ?>
                <?= tautan_sort('namaindex', 'A-Z', $sort) ?>
            </div>
        </div>

        <?php if (!$produk): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Produk tidak ditemukan.
                <a href="katalog.php" class="alert-link">Tampilkan semua produk</a>.
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($produk as $produk):
                    $kolom          = 'col-6 col-md-4 col-xl-3';
                    $tombol_label   = 'Detail';
                    $tombol_url     = 'katalog.php?detail=' . (int)$produk['id_produk'];
                    $tombol_class   = 'btn btn-outline-primary btn-sm mt-auto';
                    $tombol_disable = false;
                    include __DIR__ . '/../../layout/kartu-produk.php';
                endforeach; ?>
            </div>

            <?php include __DIR__ . '/paginasi.php'; ?>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
