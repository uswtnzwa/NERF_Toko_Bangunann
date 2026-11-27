<?php
/** Testimoni pelanggan di beranda.php. Butuh $db_aktif dan $testimoni. */
?>
<?php if ($db_aktif && $testimoni): ?>
<section class="section-title" id="testimoni">
    <div>
        <h2><i class="bi bi-chat-quote text-primary"></i> Testimoni Pelanggan</h2>
        <p>Cerita dari mereka yang sudah belanja.</p>
    </div>
    <?php if (pelanggan_terlogin()): ?>
        <a href="testimoni.php" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil-square"></i> Tulis Testimoni
        </a>
    <?php else: ?>
        <a href="masuk.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil-square"></i> Tulis Testimoni
        </a>
    <?php endif; ?>
</section>

<section class="row g-3">
    <?php foreach ($testimoni as $t): ?>
        <div class="col-md-4">
            <div class="testimoni-card">
                <div class="bintang mb-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi <?= $i <= (int)$t['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                    <?php endfor; ?>
                </div>
                <p class="small mb-3">&ldquo;<?= e($t['isi']) ?>&rdquo;</p>
                <div class="d-flex align-items-center gap-2">
                    <div class="stat-ikon" style="width:36px;height:36px;font-size:15px;background:#1473e6">
                        <?= strtoupper(substr($t['nama'], 0, 1)) ?>
                    </div>
                    <div class="small fw-semibold"><?= e($t['nama']) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>
