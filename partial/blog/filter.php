<?php
/**
 * Bilah filter: kategori artikel + pencarian.
 * Butuh $kategori, $cari, $daftar_kategori dari config/artikel-query.php.
 */
?>
<div class="panel-filter-blog">
    <div class="baris-chip">
        <a href="<?= e(artikel_url(['kategori' => ''])) ?>"
           class="chip<?= $kategori === '' ? ' aktif' : '' ?>">
            <i class="bi bi-grid"></i> Semua
        </a>
        <?php foreach ($daftar_kategori as $k): ?>
            <a href="<?= e(artikel_url(['kategori' => $k['kategori']])) ?>"
               class="chip<?= $kategori === $k['kategori'] ? ' aktif' : '' ?>">
                <?= e($k['kategori']) ?>
                <span class="chip-jml"><?= (int)$k['jml'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <form method="get" action="blog.php" class="blog-cari">
        <?php if ($kategori !== ''): ?>
            <input type="hidden" name="kategori" value="<?= e($kategori) ?>">
        <?php endif; ?>
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="cari" class="form-control"
                   placeholder="Cari judul atau topik artikel..."
                   value="<?= e($cari) ?>" aria-label="Cari artikel">
            <?php if ($cari !== ''): ?>
                <a href="<?= e(artikel_url(['cari' => ''])) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            <?php endif; ?>
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>
</div>
