<?php
/**
 * Isi halaman detail artikel.
 * Butuh $artikel (array) dan $terkait (array) dari artikel.php.
 */
$gaya = artikel_gaya($artikel['kategori']);
?>
<nav class="breadcrumb-katalog mt-3">
    <a href="beranda.php">Beranda</a> /
    <a href="blog.php">Artikel</a> /
    <span><?= e($artikel['kategori']) ?></span>
</nav>

<article class="detail-artikel">
    <header class="detail-kepala">
        <div class="artikel-meta mb-2">
            <span class="badak-kategori badge-<?= $gaya['warna'] ?>">
                <i class="bi <?= $gaya['ikon'] ?>"></i> <?= e($artikel['kategori']) ?>
            </span>
            <span><i class="bi bi-calendar3"></i> <?= e(artikel_tanggal($artikel['diterbitkan_pada'])) ?></span>
            <span><i class="bi bi-eye"></i> <?= (int)$artikel['dibaca'] ?> kali dibaca</span>
        </div>
        <h1><?= e($artikel['judul']) ?></h1>
        <p class="detail-ringkasan"><?= e($artikel['ringkasan']) ?></p>
        <div class="detail-penulis">
            <span class="avatar-penulis"><?= e(initials($artikel['penulis'])) ?></span>
            <div>
                <div class="fw-semibold small"><?= e($artikel['penulis']) ?></div>
                <div class="text-muted" style="font-size:12px">NERF Toko Bangunan</div>
            </div>
        </div>
    </header>

    <div class="detail-cover cover-<?= $gaya['warna'] ?>">
        <img src="<?= e(img_url($artikel['gambar'])) ?>" alt="Sampul artikel <?= e($artikel['judul']) ?>">
    </div>

    <div class="detail-isi">
        <?= $artikel['isi'] ?>

        <div class="detail-akhir">
            <a href="blog.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke daftar artikel
            </a>
            <div class="bagi-artikel">
                <span class="small text-muted me-2">Bagikan:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(root_url('artikel.php?slug=' . $artikel['slug'])) ?>"
                   target="_blank" rel="noopener" class="bagi-btn" aria-label="Bagikan ke Facebook">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="https://wa.me/?text=<?= urlencode($artikel['judul'] . ' - ' . root_url('artikel.php?slug=' . $artikel['slug'])) ?>"
                   target="_blank" rel="noopener" class="bagi-btn" aria-label="Bagikan ke WhatsApp">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>
</article>

<?php if ($terkait): ?>
    <section class="artikel-terkait">
        <div class="section-title">
            <div>
                <h2>Artikel Terkait</h2>
                <p>Bacaan lain seputar <?= e($artikel['kategori']) ?> untuk Anda.</p>
            </div>
            <a href="<?= e(artikel_url(['kategori' => $artikel['kategori']])) ?>" class="btn btn-sm btn-outline-primary">
                Lihat semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            <?php foreach ($terkait as $a): ?>
                <div class="col-md-6 col-lg-4">
                    <?php $kecil = true; include __DIR__ . '/../blog/kartu.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
