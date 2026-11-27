<?php
/** Daftar kategori di beranda.php. Butuh $db_aktif dan $kategori. */
?>
<section class="section-title" id="kategori">
    <div>
        <h2><i class="bi bi-tags text-primary"></i> Kategori Produk</h2>
        <p>Pilih kategori untuk menyaring kebutuhan proyek Anda.</p>
    </div>
    <a href="katalog.php" class="btn btn-sm btn-outline-primary">Semua Produk</a>
</section>

<section class="row g-3">
    <?php if (!$db_aktif): ?>
        <div class="col-12"><div class="cek-db small">Data kategori tidak dapat dimuat.</div></div>
    <?php elseif (!$kategori): ?>
        <div class="col-12"><div class="alert alert-info mb-0">Belum ada kategori aktif.</div></div>
    <?php else: ?>
        <?php foreach ($kategori as $k): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="katalog.php?kategori=<?= (int)$k['id_kategori'] ?>" class="kategori-card">
                    <div class="ikon">
                        <?php if ($k['gambar']): ?>
                            <img src="uploads/<?= e($k['gambar']) ?>" alt="" style="width:24px;height:24px;object-fit:contain"
                                 onerror="this.outerHTML='<i class=\'bi bi-tags\'></i>';">
                        <?php else: ?>
                            <i class="bi bi-tags"></i>
                        <?php endif; ?>
                    </div>
                    <div class="nama"><?= e($k['nama_kategori']) ?></div>
                    <div class="harga">
                        <?= (int)$k['jml'] ?> produk
                        <?php if ($k['harga_awal'] !== null): ?>
                            <br><span class="text-primary fw-semibold">mulai <?= rupiah($k['harga_awal']) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
