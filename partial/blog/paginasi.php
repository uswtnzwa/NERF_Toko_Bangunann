<?php
/**
 * Paginasi daftar artikel.
 * Butuh $halaman, $total_halaman, $total dari config/artikel-query.php.
 */
if ($total_halaman <= 1) {
    return;
}

$awal  = max(1, $halaman - 2);
$akhir = min($total_halaman, $halaman + 2);
?>
<nav class="blog-paginasi mt-4" aria-label="Paginasi artikel">
    <ul class="pagination pagination-sm justify-content-center flex-wrap">
        <?php if ($halaman > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= e(artikel_url(['halaman' => $halaman - 1])) ?>">&laquo; Sebelumnya</a>
            </li>
        <?php endif; ?>

        <?php if ($awal > 1): ?>
            <li class="page-item"><a class="page-link" href="<?= e(artikel_url(['halaman' => 1])) ?>">1</a></li>
            <?php if ($awal > 2): ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li><?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $awal; $i <= $akhir; $i++): ?>
            <li class="page-item<?= $i === $halaman ? ' active' : '' ?>">
                <a class="page-link" href="<?= e(artikel_url(['halaman' => $i])) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($akhir < $total_halaman): ?>
            <?php if ($akhir < $total_halaman - 1): ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li><?php endif; ?>
            <li class="page-item"><a class="page-link" href="<?= e(artikel_url(['halaman' => $total_halaman])) ?>"><?= $total_halaman ?></a></li>
        <?php endif; ?>

        <?php if ($halaman < $total_halaman): ?>
            <li class="page-item">
                <a class="page-link" href="<?= e(artikel_url(['halaman' => $halaman + 1])) ?>">Berikutnya &raquo;</a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="text-center text-muted small">
        Menampilkan <?= $total ? $offset + 1 : 0 ?>&ndash;<?= min($offset + count($daftar_artikel), $total) ?>
        dari <?= $total ?> artikel
    </div>
</nav>
