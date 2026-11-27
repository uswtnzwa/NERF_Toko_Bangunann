<?php
/** Banner promo di beranda.php. Butuh $db_aktif dan $banner. */
?>
<?php if ($db_aktif && $banner): ?>
<section class="section-title"><h2><i class="bi bi-megaphone text-primary"></i> Promo Saat Ini</h2></section>
<section class="row g-3">
    <?php
    $gradien = [
        'linear-gradient(120deg,#1473e6,#0b4a9e)',
        'linear-gradient(120deg,#16a06a,#0c5c3d)',
        'linear-gradient(120deg,#f59e0b,#a45c05)',
        'linear-gradient(120deg,#8b5cf6,#4c1d95)',
    ];
    foreach (array_slice($banner, 0, 3) as $i => $b): ?>
        <div class="col-md-4">
            <div class="banner-card" style="background:<?= $gradien[$i % 4] ?>">
                <h5><?= e($b['judul']) ?></h5>
                <p><?= e($b['subjudul'] ?? '') ?></p>
                <?php if ($b['link']): ?>
                    <div>
                        <a href="<?= e($b['link']) ?>" class="btn btn-light btn-sm fw-semibold">
                            <?= e($b['tombol'] ?: 'Lihat Detail') ?> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>
