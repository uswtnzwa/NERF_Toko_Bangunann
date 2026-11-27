<?php
/**
 * Satu kartu artikel. Dipakai di blog.php (daftar) dan artikel.php (terkait).
 * Variabel yang dibutuhkan: $a (array artikel), opsional $kecil = true.
 */
$gaya  = artikel_gaya($a['kategori']);
$link  = 'artikel.php?slug=' . urlencode($a['slug']);
$kecil = $kecil ?? false;
?>
<article class="artikel-card<?= $kecil ? ' kartu-kecil' : '' ?>">
    <a href="<?= e($link) ?>" class="artikel-cover cover-<?= $gaya['warna'] ?>">
        <img src="<?= e(img_url($a['gambar'])) ?>" alt="Sampul artikel <?= e($a['judul']) ?>">
        <span class="cover-kategori badge-<?= $gaya['warna'] ?>">
            <i class="bi <?= $gaya['ikon'] ?>"></i> <?= e($a['kategori']) ?>
        </span>
    </a>
    <div class="artikel-isi">
        <div class="artikel-meta">
            <span><i class="bi bi-calendar3"></i> <?= e(artikel_tanggal($a['diterbitkan_pada'])) ?></span>
            <span><i class="bi bi-eye"></i> <?= (int)$a['dibaca'] ?></span>
        </div>
        <h3><a href="<?= e($link) ?>"><?= e($a['judul']) ?></a></h3>
        <p><?= e($a['ringkasan']) ?></p>
        <div class="artikel-bawah">
            <span class="small text-muted"><i class="bi bi-pencil"></i> <?= e($a['penulis']) ?></span>
            <a href="<?= e($link) ?>">Baca <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</article>
