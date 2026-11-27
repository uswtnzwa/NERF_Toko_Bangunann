<?php
/** Paginasi daftar katalog. Butuh $halaman, $total_halaman, dan fungsi katalog_url(). */
?>
<?php if ($total_halaman > 1): ?>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination pagination-sm">
        <li class="page-item <?= $halaman <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(katalog_url(['halaman' => $halaman - 1])) ?>">&laquo;</a>
        </li>
        <?php
        $mulai  = max(1, $halaman - 2);
        $selesai = min($total_halaman, $halaman + 2);
        for ($i = $mulai; $i <= $selesai; $i++): ?>
            <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
                <a class="page-link" href="<?= e(katalog_url(['halaman' => $i])) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $halaman >= $total_halaman ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(katalog_url(['halaman' => $halaman + 1])) ?>">&raquo;</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
