<?php
/**
 * Form tambah / edit produk.
 * Butuh $edit (data produk atau array kosong) dan $daftar_kategori.
 */

/*
 * Opsi yang akan diedit:
 *   - mode edit  -> ambil baris dari tabel opsi_produk
 *   - mode tambah -> pratinjau dari teks warna/ukuran di form
 */
$opsi_edit = [];
if ((int)($edit['id_produk'] ?? 0) > 0) {
    $opsi_edit = ambil_opsi_produk($koneksi, (int)$edit['id_produk']);
} else {
    foreach (['Warna' => ($edit['warna'] ?? ''), 'Ukuran' => ($edit['ukuran'] ?? '')] as $kel => $teks) {
        foreach (opsi_tersedia((string)$teks) as $nilai) {
            $opsi_edit[$kel][] = [
                'id_opsi'    => 0,
                'kelompok'   => $kel,
                'nilai'      => $nilai,
                'harga_opsi' => null,
                'stok_opsi'  => null,
            ];
        }
    }
}
?>
<div class="card-panel p-4 mb-4" id="formProduk">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-pencil-square text-primary"></i>
        <?= (int)($edit['id_produk'] ?? 0) > 0 ? 'Edit Produk' : 'Tambah Produk Baru' ?>
    </h6>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_produk" value="<?= (int)($edit['id_produk'] ?? 0) ?>">

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Kode Produk *</label>
                <input type="text" name="kode_produk" class="form-control form-control-sm" required
                       value="<?= e($edit['kode_produk'] ?? '') ?>" placeholder="SMN002">
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold">Nama Produk *</label>
                <input type="text" name="nama_produk" class="form-control form-control-sm" required
                       value="<?= e($edit['nama_produk'] ?? '') ?>" placeholder="Nama lengkap produk">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kategori *</label>
                <select name="id_kategori" class="form-select form-control-sm" required>
                    <option value="">-- Pilih kategori --</option>
                    <?php foreach ($daftar_kategori as $k): ?>
                        <option value="<?= (int)$k['id_kategori'] ?>"
                            <?= (int)($edit['id_kategori'] ?? 0) === (int)$k['id_kategori'] ? 'selected' : '' ?>>
                            <?= e($k['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Harga (Rp) *</label>
                <input type="number" name="harga" min="0" step="100" class="form-control form-control-sm" required
                       value="<?= e($edit['harga'] ?? 0) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Harga Diskon (Rp)</label>
                <input type="number" name="harga_diskon" min="0" step="100" class="form-control form-control-sm"
                       value="<?= e($edit['harga_diskon'] ?? 0) ?>">
                <small class="text-muted">Isi 0 jika tanpa diskon.</small>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Stok</label>
                <input type="number" name="stok" min="0" class="form-control form-control-sm"
                       value="<?= e($edit['stok'] ?? 0) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Stok Minimum</label>
                <input type="number" name="stok_minimum" min="0" class="form-control form-control-sm"
                       value="<?= e($edit['stok_minimum'] ?? 5) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Satuan</label>
                <input type="text" name="satuan" class="form-control form-control-sm"
                       value="<?= e($edit['satuan'] ?? 'Pcs') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Berat (kg)</label>
                <input type="number" name="berat" min="0" step="0.01" class="form-control form-control-sm"
                       value="<?= e($edit['berat'] ?? 0) ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold">
                    Pilihan Warna
                    <span class="text-muted fw-normal">(pisahkan dengan koma, kosongkan bila tidak ada)</span>
                </label>
                <input type="text" name="warna" class="form-control form-control-sm" list="pilihan-warna"
                       value="<?= e($edit['warna'] ?? '') ?>" placeholder="Putih, Biru, Merah, Hijau">
                <datalist id="pilihan-warna">
                    <option value="Putih"></option><option value="Biru"></option>
                    <option value="Merah"></option><option value="Hijau"></option>
                    <option value="Kuning"></option><option value="Hitam"></option>
                    <option value="Abu-abu"></option><option value="Coklat"></option>
                    <option value="Cream"></option><option value="Pink"></option>
                </datalist>
            </div>

            <?php include __DIR__ . '/produk-form-opsi.php'; ?>

            <div class="col-md-4">
                <label class="form-label small fw-semibold">
                    Pilihan Ukuran
                    <span class="text-muted fw-normal">(pisahkan dengan koma, kosongkan bila tidak ada)</span>
                </label>
                <input type="text" name="ukuran" class="form-control form-control-sm" list="pilihan-ukuran"
                       value="<?= e($edit['ukuran'] ?? '') ?>" placeholder="3 cm, 5 cm, 7 cm, 10 cm">
                <datalist id="pilihan-ukuran">
                    <option value="3 cm"></option><option value="5 cm"></option>
                    <option value="7 cm"></option><option value="10 cm"></option>
                    <option value="1 inch"></option><option value="2 inch"></option>
                    <option value="3 inch"></option><option value="4 inch"></option>
                    <option value="10 cm x 10 cm"></option><option value="20 cm x 20 cm"></option>
                </datalist>
                <div class="form-text">
                    Warna dan ukuran muncul sebagai pilihan saat pelanggan memesan
                    di halaman detail produk. Satu produk bisa punya keduanya.
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <?php foreach (['Aktif', 'Nonaktif'] as $s): ?>
                        <option <?= ($edit['status'] ?? 'Aktif') === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Gambar</label>
                <input type="file" name="gambar" accept="image/*" class="form-control form-control-sm">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="form-control form-control-sm"><?= e($edit['deskripsi'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Spesifikasi</label>
                <textarea name="spesifikasi" rows="3" class="form-control form-control-sm"><?= e($edit['spesifikasi'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary btn-sm">
                <i class="bi bi-save"></i>
                <?= (int)($edit['id_produk'] ?? 0) > 0 ? 'Simpan Perubahan' : 'Simpan Produk' ?>
            </button>
            <a href="produk.php" class="btn btn-outline-secondary btn-sm">Batal</a>
        </div>
    </form>
</div>
