<?php
/**
 * Kartu artikel unggulan (artikel terbaru).
 * Butuh $unggulan dari config/artikel-query.php.
 */
$gaya = artikel_gaya($unggulan['kategori']);
$link = 'artikel.php?slug=' . urlencode($unggulan['slug']);
?>
<section class="artikel-unggulan">
    <div class="unggulan-label"><i class="bi bi-stars"></i> Artikel Pilihan</div>
    <div class="row g-0 align-items-stretch">
        <div class="col-md-5">
            <a href="<?= e($link) ?>" class="unggulan-cover cover-<?= $gaya['warna'] ?>">
                <img src="<?= e(img_url($unggulan['gambar'])) ?>"
                     alt="Sampul artikel <?= e($unggulan['judul']) ?>">
            </a>
        </div>
        <div class="col-md-7">
            <div class="unggulan-isi">
                <div class="artikel-meta">
                    <span class="badak-kategori badge-<?= $gaya['warna'] ?>">
                        <i class="bi <?= $gaya['ikon'] ?>"></i> <?= e($unggulan['kategori']) ?>
                    </span>
                    <span><i class="bi bi-calendar3"></i> <?= e(artikel_tanggal($unggulan['diterbitkan_pada'])) ?></span>
                    <span><i class="bi bi-eye"></i> <?= (int)$unggulan['dibaca'] ?></span>
                </div>
                <h2><a href="<?= e($link) ?>"><?= e($unggulan['judul']) ?></a></h2>
                <p><?= e($unggulan['ringkasan']) ?></p>
                <div class="unggulan-bawah">
                    <span class="small text-muted"><i class="bi bi-pencil"></i> <?= e($unggulan['penulis']) ?></span>
                    <a href="<?= e($link) ?>" class="btn btn-primary btn-sm">
                        Baca artikel <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
