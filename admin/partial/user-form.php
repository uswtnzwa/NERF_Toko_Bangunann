<?php
/**
 * Form tambah / edit akun admin.
 * Butuh $edit (data akun atau array kosong).
 */
$edit_id = (int)($edit['id_admin'] ?? 0);
?>
<div class="card-panel p-4">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-person-gear text-primary"></i>
        <?= $edit_id > 0 ? 'Edit Akun' : 'Tambah Akun' ?>
    </h6>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_admin" value="<?= $edit_id ?>">

        <div class="mb-2">
            <label class="form-label small fw-semibold">Nama Lengkap *</label>
            <input type="text" name="nama_lengkap" class="form-control form-control-sm" required
                   value="<?= e($edit['nama_lengkap'] ?? '') ?>">
        </div>

        <div class="mb-2">
            <label class="form-label small fw-semibold">Username *</label>
            <input type="text" name="username" class="form-control form-control-sm" required
                   value="<?= e($edit['username'] ?? '') ?>">
        </div>

        <div class="mb-2">
            <label class="form-label small fw-semibold">Email</label>
            <input type="email" name="email" class="form-control form-control-sm"
                   value="<?= e($edit['email'] ?? '') ?>">
        </div>

        <div class="mb-2">
            <label class="form-label small fw-semibold">
                Password
                <?= $edit_id > 0
                    ? '<span class="text-muted">(kosongkan bila tidak diubah)</span>'
                    : '*' ?>
            </label>
            <input type="password" name="password" class="form-control form-control-sm"
                   minlength="4" <?= $edit_id > 0 ? '' : 'required' ?>>
        </div>

        <div class="mb-2">
            <label class="form-label small fw-semibold">Role</label>
            <select name="role" class="form-select form-select-sm">
                <?php foreach (daftar_role_admin() as $r): ?>
                    <option <?= ($edit['role'] ?? 'Admin') === $r ? 'selected' : '' ?>><?= $r ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-2">
            <label class="form-label small fw-semibold">Status</label>
            <select name="status" class="form-select form-select-sm">
                <?php foreach (['Aktif', 'Nonaktif'] as $s): ?>
                    <option <?= ($edit['status'] ?? 'Aktif') === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-semibold">Foto</label>
            <input type="file" name="foto" accept="image/*" class="form-control form-control-sm">
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-save"></i> Simpan</button>
            <a href="user.php" class="btn btn-outline-secondary btn-sm">Batal</a>
        </div>
    </form>
</div>
