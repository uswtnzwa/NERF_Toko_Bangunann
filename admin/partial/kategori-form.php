<?php
/** Form tambah / edit kategori. Butuh $edit. */
$edit_id = (int)($edit['id_kategori'] ?? 0);
?>
<div class="card-panel p-4">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-pencil-square text-primary"></i>
        <?= $edit_id > 0 ? 'Edit Kategori' : 'Tambah Kategori' ?>
    </h6>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_kategori" value="<?= $edit_id ?>">

        <div class="mb-2">
            <label class="form-label small fw-semibold">Nama Kategori *</label>
            <input type="text" name="nama_kategori" class="form-control form-control-sm" required
                   value="<?= e($edit['nama_kategori'] ?? '') ?>" placeholder="Contoh: Semen">
        </div>

        <div class="mb-2">
            <label class="form-label small fw-semibold">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="form-control form-control-sm"><?= e($edit['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="mb-2">
            <label class="form-label small fw-semibold">Gambar</label>
            <input type="file" name="gambar" accept="image/*" class="form-control form-control-sm">
        </div>

        <div class="mb-3">
            <label class="form-label small fw-semibold">Status</label>
            <select name="status" class="form-select form-select-sm">
                <?php foreach (['Aktif', 'Nonaktif'] as $s): ?>
                    <option <?= ($edit['status'] ?? 'Aktif') === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-save"></i> Simpan</button>
            <a href="kategori.php" class="btn btn-outline-secondary btn-sm">Batal</a>
        </div>
    </form>
</div>
