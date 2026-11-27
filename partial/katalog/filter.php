<?php
/** Sidebar filter katalog: cari + daftar kategori. */
?>
<div class="col-lg-3">
    <div class="panel-filter mb-3">
        <form method="get" action="katalog.php">
            <label class="form-label small fw-semibold mb-1">
                <i class="bi bi-search"></i> Cari Produk
            </label>
            <div class="input-group input-group-sm mb-2">
                <input type="text" name="cari" class="form-control" value="<?= e($cari) ?>"
                       placeholder="Kode atau nama produk">
                <?php if ($kategori_id): ?>
                    <input type="hidden" name="kategori" value="<?= (int)$kategori_id ?>">
                <?php endif; ?>
            </div>
            <button class="btn btn-primary btn-sm w-100">Cari</button>
        </form>
        <?php if ($cari !== ''): ?>
            <a href="<?= e(katalog_url(['cari' => '', 'halaman' => 1])) ?>"
               class="btn btn-link btn-sm w-100 text-decoration-none mt-1">
                <i class="bi bi-x-circle"></i> Hapus pencarian
            </a>
        <?php endif; ?>
    </div>

    <div class="panel-filter">
        <div class="small fw-semibold mb-2"><i class="bi bi-tags"></i> Kategori</div>
        <div class="d-flex flex-column gap-1">
            <a href="<?= e(katalog_url(['kategori' => 0, 'halaman' => 1])) ?>"
               class="chip <?= $kategori_id === 0 ? 'aktif' : '' ?>">
                Semua kategori
            </a>
            <?php foreach ($daftar_kategori as $k): ?>
                <a href="<?= e(katalog_url(['kategori' => (int)$k['id_kategori'], 'halaman' => 1])) ?>"
                   class="chip <?= $kategori_id === (int)$k['id_kategori'] ? 'aktif' : '' ?>">
                    <?= e($k['nama_kategori']) ?>
                    <span class="opacity-75">(<?= (int)$k['jml'] ?>)</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
