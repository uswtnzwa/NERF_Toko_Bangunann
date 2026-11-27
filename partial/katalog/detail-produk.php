<?php
/**
 * Detail satu produk.
 *
 * Butuh: $detail, $detail_gambar_lain, $db, dan fungsi katalog_url(),
 * opsi_pilihan_produk(). Harga dihitung di sini lalu dipakai form.
 */
$harga  = harga_efektif($detail);
$diskon = (float)$detail['harga_diskon'] > 0;
$habis  = (int)$detail['stok'] <= 0;

[$opsi_warna, $opsi_ukuran] = opsi_pilihan_produk($db, (int)$detail['id_produk'], $detail);

// Harga awal: harga opsi pertama bila ada, selain itu harga produk
$harga_awal = $opsi_ukuran ? $opsi_ukuran[0]['harga']
             : ($opsi_warna ? $opsi_warna[0]['harga'] : $harga);
?>
<nav class="breadcrumb-katalog mt-3">
    <a href="beranda.php">Beranda</a> / <a href="katalog.php">Katalog</a> /
    <a href="<?= e(katalog_url(['kategori' => (int)$detail['id_kategori'], 'halaman' => 1])) ?>">
        <?= e($detail['nama_kategori'] ?? 'Umum') ?>
    </a> / <?= e($detail['nama_produk']) ?>
</nav>

<div class="row g-4 mt-1">
    <div class="col-lg-5">
        <div class="galeri">
            <?php if ($detail['gambar']): ?>
                <img src="uploads/<?= e($detail['gambar']) ?>" alt="<?= e($detail['nama_produk']) ?>" onerror="this.remove()">
            <?php else: ?>
                <i class="bi bi-box text-muted" style="font-size:70px"></i>
            <?php endif; ?>
            <?php if ($diskon): ?>
                <span class="badge bg-danger position-absolute top-0 start-0 m-3">Diskon</span>
            <?php elseif ($habis): ?>
                <span class="badge bg-secondary position-absolute top-0 start-0 m-3">Habis</span>
            <?php endif; ?>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2 small text-muted">
            <span>Kode: <code><?= e($detail['kode_produk']) ?></code></span>
            <a href="katalog.php" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Kembali ke katalog
            </a>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="info-kotak">
            <div class="product-cat mb-1"><?= e($detail['nama_kategori'] ?? 'Umum') ?></div>
            <h2 class="fw-bold mb-3"><?= e($detail['nama_produk']) ?></h2>

            <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
                <span class="harga-besar" id="hargaTerpilih"><?= rupiah($harga_awal) ?></span>
                <?php if ($diskon): ?>
                    <del class="text-muted"><?= rupiah($detail['harga']) ?></del>
                    <span class="badge bg-danger">
                        Hemat <?= rupiah((float)$detail['harga'] - $harga) ?>
                    </span>
                <?php endif; ?>
            </div>

            <dl class="row mb-0">
                <div class="col-6">
                    <dt>Stok tersedia</dt>
                    <dd class="<?= $habis ? 'text-danger' : '' ?>">
                        <?= $habis ? 'Stok habis' : (int)$detail['stok'] . ' ' . e($detail['satuan']) ?>
                    </dd>
                </div>
                <div class="col-6">
                    <dt>Berat</dt>
                    <dd><?= (float)$detail['berat'] > 0 ? (float)$detail['berat'] . ' kg' : '-' ?></dd>
                </div>
                <?php if ($detail['spesifikasi']): ?>
                <div class="col-12">
                    <dt>Spesifikasi</dt>
                    <dd class="fw-normal small text-secondary" style="white-space:pre-line"><?= e($detail['spesifikasi']) ?></dd>
                </div>
                <?php endif; ?>
                <?php if ($detail['deskripsi']): ?>
                <div class="col-12">
                    <dt>Deskripsi</dt>
                    <dd class="fw-normal small text-secondary" style="white-space:pre-line"><?= e($detail['deskripsi']) ?></dd>
                </div>
                <?php endif; ?>
            </dl>

            <?php /*
                Satu form dipakai dua tombol:
                  - "Tambah ke Keranjang" -> kembali ke katalog
                  - "Beli Sekarang"       -> langsung ke halaman checkout
                Varian dan jumlah yang dipilih ikut terpakai untuk keduanya,
                jadi tidak perlu duplikat pilihan warna/ukuran.
            */ ?>
            <form method="post" action="katalog.php?detail=<?= (int)$detail['id_produk'] ?>" class="mt-3">
                <input type="hidden" name="id_produk" value="<?= (int)$detail['id_produk'] ?>">

                <?php include __DIR__ . '/pilih-varian.php'; ?>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <input type="number" name="jumlah" value="1" min="1" id="jumlahBeli"
                           max="<?= max(1, (int)$detail['stok']) ?>"
                           class="form-control jumlah-kotak" <?= $habis ? 'disabled' : '' ?>>
                    <span class="text-muted small" id="stokTersedia">
                        Stok: <?= (int)$detail['stok'] ?> <?= e($detail['satuan']) ?>
                    </span>
                    <button type="submit" name="aksi" value="tambah"
                            class="btn btn-primary btn-lg" <?= $habis ? 'disabled' : '' ?>>
                        <i class="bi bi-bag-plus"></i> <?= $habis ? 'Stok Habis' : 'Tambah ke Keranjang' ?>
                    </button>
                    <?php if (pelanggan_terlogin()): ?>
                        <button type="submit" name="aksi" value="beli_sekarang"
                                class="btn btn-outline-primary btn-lg" <?= $habis ? 'disabled' : '' ?>>
                            <i class="bi bi-lightning-charge"></i> <?= $habis ? 'Stok Habis' : 'Beli Sekarang' ?>
                        </button>
                    <?php endif; ?>
                </div>
            </form>

            <div class="d-flex gap-2 align-items-center flex-wrap mt-3">
                <?php if (!pelanggan_terlogin()): ?>
                    <a href="masuk.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk untuk Memesan
                    </a>
                <?php endif; ?>
                <a href="<?= e(katalog_url(['kategori' => (int)$detail['id_kategori'], 'halaman' => 1])) ?>"
                   class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-tags"></i> Kategori ini
                </a>
            </div>

            <?php if (!pelanggan_terlogin()): ?>
                <div class="alert alert-info small mt-3 mb-0">
                    <i class="bi bi-info-circle"></i>
                    Silakan <a href="masuk.php" class="alert-link">masuk</a> agar pesanan Anda
                    tercatat dengan nama dan alamat pengiriman.
                </div>
            <?php else: ?>
                <div class="alert alert-info small mt-3 mb-0">
                    <i class="bi bi-info-circle"></i>
                    Checkout cepat: pilih cara terima barang (ambil di toko atau diantar),
                    lalu pilih pembayaran (bayar di tempat atau transfer).
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($detail_gambar_lain): ?>
<div class="mt-5">
    <h5 class="fw-bold mb-3">Produk lain di kategori ini</h5>
    <div class="row g-3">
        <?php foreach ($detail_gambar_lain as $produk):
            $kolom          = 'col-6 col-md-3';
            $tombol_url     = 'katalog.php?detail=' . (int)$produk['id_produk'];
            $bungkus_link   = true;
            include __DIR__ . '/../../layout/kartu-produk.php';
        endforeach; ?>
    </div>
</div>
<?php endif; ?>
