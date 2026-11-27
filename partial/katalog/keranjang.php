<?php
/**
 * Tabel keranjang di bagian bawah katalog.php.
 * Butuh $keranjang dan $db.
 */
$keranjang = $_SESSION['keranjang'] ?? [];
?>
<?php if ($keranjang): ?>
<div class="mt-5" id="keranjang">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h2 class="fw-bold mb-0 h5"><i class="bi bi-bag text-primary"></i> Keranjang Saya</h2>
        <form method="post" action="katalog.php">
            <input type="hidden" name="aksi" value="kosongkan">
            <button class="btn btn-outline-danger btn-sm">Kosongkan</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle bg-white mb-0" style="border:1px solid #e7ebf0;">
            <thead class="table-light">
                <tr>
                    <th>Produk</th><th>Pilihan</th><th class="text-center">Jumlah</th>
                    <th class="text-end">Subtotal</th><th class="text-center" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal_keranjang = 0;
                foreach ($keranjang as $kunci => $item):
                    $s = $db->prepare("SELECT nama_produk, kode_produk, harga, harga_diskon, satuan
                                       FROM produk WHERE id_produk = ? LIMIT 1");
                    $s->bind_param('i', $item['id_produk']);
                    $s->execute();
                    $ip = $s->get_result()->fetch_assoc();
                    if (!$ip) {
                        continue;
                    }

                    // Harga memakai harga opsi yang tersimpan saat item ditambahkan,
                    // supaya admin mengubah harga opsi tidak mengubah isi keranjang lama.
                    $harga_item = isset($item['harga']) && $item['harga'] > 0
                        ? (float)$item['harga']
                        : harga_efektif($ip);

                    $subtotal = $harga_item * $item['jumlah'];
                    $subtotal_keranjang += $subtotal;
                ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($ip['nama_produk']) ?></div>
                            <code class="small"><?= e($ip['kode_produk']) ?></code>
                        </td>
                        <td>
                            <?php if (!empty($item['warna'])): ?>
                                <span class="badge bg-primary"><?= e($item['warna']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($item['ukuran'])): ?>
                                <span class="badge bg-info text-dark"><?= e($item['ukuran']) ?></span>
                            <?php endif; ?>
                            <?php if (empty($item['warna']) && empty($item['ukuran'])): ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= (int)$item['jumlah'] ?> <?= e($ip['satuan']) ?></td>
                        <td class="text-end fw-semibold"><?= rupiah($subtotal) ?></td>
                        <td class="text-center">
                            <form method="post" action="katalog.php">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="kunci" value="<?= e($kunci) ?>">
                                <button class="btn btn-sm btn-outline-danger" title="Hapus dari keranjang">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th class="text-end"><?= rupiah($subtotal_keranjang) ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
        <div class="alert alert-info small mb-0" style="flex:1 1 280px">
            <i class="bi bi-info-circle"></i>
            Keranjang ini tersimpan di sesi browser Anda. Lanjut ke checkout untuk memilih
            cara terima barang dan metode pembayaran.
        </div>
        <?php if (pelanggan_terlogin()): ?>
            <a href="checkout.php" class="btn btn-primary btn-lg">
                <i class="bi bi-bag-check"></i> Lanjut ke Checkout
            </a>
        <?php else: ?>
            <a href="masuk.php" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right"></i> Masuk untuk Checkout
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
