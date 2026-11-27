<?php
/**
 * Ringkasan pesanan (sidebar kanan checkout + pesanan-saya.php).
 * Butuh: $ringkas (hasil keranjang_ringkas()), $ongkir, $diskon, $catatan_admin.
 */
$total_bayar = $ringkas['subtotal'] + $ongkir - $diskon;
?>
<div class="ringkasan-kotak">
    <h5 class="fw-bold mb-3">Ringkasan Pesanan</h5>

    <?php foreach ($ringkas['barang'] as $b): ?>
        <div class="ringkasan-item">
            <div class="flex-grow-1">
                <div class="fw-semibold small"><?= e($b['nama_produk']) ?></div>
                <div class="text-muted" style="font-size:12px">
                    <?= e($b['jumlah']) ?> <?= e($b['satuan']) ?> &times; <?= rupiah($b['harga']) ?>
                    <?php if ($b['warna']): ?> &middot; <span class="badge bg-primary"><?= e($b['warna']) ?></span><?php endif; ?>
                    <?php if ($b['ukuran']): ?> &middot; <span class="badge bg-info text-dark"><?= e($b['ukuran']) ?></span><?php endif; ?>
                </div>
            </div>
            <div class="fw-semibold small"><?= rupiah($b['subtotal']) ?></div>
        </div>
        <?php if ($b['melebihi_stok']): ?>
            <div class="alert alert-danger py-1 px-2 small mb-2">
                Stok <strong><?= e($b['nama_produk']) ?></strong> tersisa <?= (int)$b['stok'] ?> <?= e($b['satuan']) ?>.
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <hr>

    <table class="table table-sm mb-0">
        <tr>
            <td class="text-muted small">Subtotal</td>
            <td class="text-end small fw-semibold"><?= rupiah($ringkas['subtotal']) ?></td>
        </tr>
        <?php if ($ongkir > 0): ?>
        <tr>
            <td class="text-muted small">Biaya antar</td>
            <td class="text-end small fw-semibold"><?= rupiah($ongkir) ?></td>
        </tr>
        <?php else: ?>
        <tr>
            <td class="text-muted small">Biaya antar</td>
            <td class="text-end small text-success">Rp 0</td>
        </tr>
        <?php endif; ?>
        <?php if ($diskon > 0): ?>
        <tr>
            <td class="text-muted small">Diskon</td>
            <td class="text-end small text-danger">- <?= rupiah($diskon) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th class="small">Total Bayar</th>
            <th class="text-end" style="font-size:17px"><?= rupiah($total_bayar) ?></th>
        </tr>
    </table>

    <?php if (!empty($catatan_admin)): ?>
        <div class="alert alert-warning small mt-3 mb-0"><?= e($catatan_admin) ?></div>
    <?php endif; ?>
</div>
