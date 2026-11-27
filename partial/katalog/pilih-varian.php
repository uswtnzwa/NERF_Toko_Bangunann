<?php
/** Tombol pilihan warna / ukuran pada form detail produk. */
?>
<?php if ($opsi_warna): ?>
    <label class="label mb-1">Pilih Warna</label>
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?php foreach ($opsi_warna as $i => $o): ?>
            <input type="radio" class="btn-check" name="warna" id="warna<?= (int)$i ?>"
                   value="<?= e($o['nilai']) ?>"
                   data-harga="<?= (float)$o['harga'] ?>"
                   data-stok="<?= $o['stok'] !== null ? (int)$o['stok'] : '' ?>"
                   <?= $i === 0 ? 'checked' : '' ?>>
            <label class="btn btn-outline-secondary btn-sm" for="warna<?= (int)$i ?>">
                <?= e($o['nilai']) ?>
                <span class="opacity-75">(<?= rupiah($o['harga']) ?>)</span>
            </label>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($opsi_ukuran): ?>
    <label class="label mb-1">Pilih Ukuran</label>
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?php foreach ($opsi_ukuran as $i => $o): ?>
            <input type="radio" class="btn-check" name="ukuran" id="ukuran<?= (int)$i ?>"
                   value="<?= e($o['nilai']) ?>"
                   data-harga="<?= (float)$o['harga'] ?>"
                   data-stok="<?= $o['stok'] !== null ? (int)$o['stok'] : '' ?>"
                   <?= $i === 0 ? 'checked' : '' ?>>
            <label class="btn btn-outline-primary btn-sm" for="ukuran<?= (int)$i ?>">
                <?= e($o['nilai']) ?>
                <span class="opacity-75">(<?= rupiah($o['harga']) ?>)</span>
            </label>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($opsi_warna || $opsi_ukuran): ?>
    <div class="form-text mb-3">
        Harga dan stok mengikuti pilihan di atas.
    </div>
<?php endif; ?>
