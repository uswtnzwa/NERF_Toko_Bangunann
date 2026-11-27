<?php
/**
 * Filter + tabel daftar produk.
 * Butuh $produk, $daftar_kategori, $cari, $filter_k, $total, $jml_hal, $halaman.
 */
?>
<div class="card-panel p-3 mb-3">
    <form method="get" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-semibold mb-1">Cari Produk</label>
            <input type="search" name="cari" class="form-control form-control-sm"
                   value="<?= e($cari) ?>" placeholder="Nama atau kode produk">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold mb-1">Kategori</label>
            <select name="kategori" class="form-select form-select-sm">
                <option value="0">Semua kategori</option>
                <?php foreach ($daftar_kategori as $k): ?>
                    <option value="<?= (int)$k['id_kategori'] ?>" <?= $filter_k === (int)$k['id_kategori'] ? 'selected' : '' ?>>
                        <?= e($k['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel"></i> Terapkan</button>
            <a href="produk.php" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="card-panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:70px">Gambar</th>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Pilihan</th>
                    <th class="text-end">Harga</th>
                    <th class="text-center">Stok</th>
                    <th>Status</th>
                    <th class="text-center" style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$produk): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada produk yang cocok.</td></tr>
                <?php endif; ?>

                <?php foreach ($produk as $p):
                    $diskon  = (float)$p['harga_diskon'] > 0;
                    $menipis = (int)$p['stok'] <= (int)$p['stok_minimum'];
                    $warna   = opsi_tersedia((string)($p['warna'] ?? ''));
                    $ukuran  = opsi_tersedia((string)($p['ukuran'] ?? ''));
                ?>
                    <tr>
                        <td>
                            <?php if ($p['gambar']): ?>
                                <img src="../uploads/<?= e($p['gambar']) ?>" class="thumb" alt="">
                            <?php else: ?>
                                <div class="thumb d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold"><?= e($p['kode_produk']) ?></td>
                        <td>
                            <?= e($p['nama_produk']) ?>
                            <?php if ($diskon): ?>
                                <span class="badge bg-danger ms-1">Diskon</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><?= e($p['nama_kategori'] ?? '-') ?></td>
                        <td><?php include __DIR__ . '/produk-tabel-pilihan.php'; ?></td>
                        <td class="text-end">
                            <?php if ($diskon): ?>
                                <div class="text-decoration-line-through text-muted small"><?= rupiah($p['harga']) ?></div>
                                <div class="fw-bold text-danger"><?= rupiah($p['harga_diskon']) ?></div>
                            <?php else: ?>
                                <span class="fw-semibold"><?= rupiah($p['harga']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-<?= $menipis ? 'danger' : 'success' ?>">
                                <?= (int)$p['stok'] ?> <?= e($p['satuan']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $p['status'] === 'Aktif' ? 'success' : 'secondary' ?>">
                                <?= e($p['status']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="produk.php?edit=<?= (int)$p['id_produk'] ?>"
                               class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <a href="produk.php?hapus=<?= (int)$p['id_produk'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Hapus produk <?= e(addslashes($p['nama_produk'])) ?>?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($jml_hal > 1): ?>
        <nav class="mt-3">
            <ul class="pagination pagination-sm justify-content-end mb-0">
                <?php for ($i = 1; $i <= $jml_hal; $i++): ?>
                    <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url_produk(['cari' => $cari, 'k' => $filter_k, 'h' => $i]) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
