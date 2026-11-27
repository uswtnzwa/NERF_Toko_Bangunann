<?php
/** Tabel daftar kategori. Butuh $kategori. */
?>
<div class="card-panel p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="fw-bold mb-0">Daftar Kategori</h6>
        <a href="kategori.php?baru=1" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Tambah</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Produk</th>
                    <th>Status</th>
                    <th class="text-center" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$kategori): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                <?php endif; ?>

                <?php foreach ($kategori as $k): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($k['nama_kategori']) ?></td>
                        <td class="text-muted small"><?= e($k['deskripsi'] ?? '-') ?></td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><?= (int)$k['jml_produk'] ?></span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $k['status'] === 'Aktif' ? 'success' : 'secondary' ?>">
                                <?= e($k['status']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="kategori.php?edit=<?= (int)$k['id_kategori'] ?>"
                               class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <a href="kategori.php?hapus=<?= (int)$k['id_kategori'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Hapus kategori ini?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
