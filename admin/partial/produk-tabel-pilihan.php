<?php
/** Badge warna/ukuran di kolom "Pilihan" tabel produk. Butuh $warna & $ukuran. */
?>
<?php if ($warna): ?>
    <?php foreach (array_slice($warna, 0, 3) as $w): ?>
        <span class="badge bg-light text-dark border"><?= e($w) ?></span>
    <?php endforeach; ?>
    <?php if (count($warna) > 3): ?>
        <span class="badge bg-secondary">+<?= count($warna) - 3 ?></span>
    <?php endif; ?>
<?php endif; ?>

<?php foreach (array_slice($ukuran, 0, 4) as $u): ?>
    <span class="badge bg-info text-dark"><?= e($u) ?></span>
<?php endforeach; ?>
<?php if (count($ukuran) > 4): ?>
    <span class="badge bg-secondary">+<?= count($ukuran) - 4 ?></span>
<?php endif; ?>

<?php if (!$warna && !$ukuran): ?>
    <span class="text-muted small">-</span>
<?php endif; ?>
