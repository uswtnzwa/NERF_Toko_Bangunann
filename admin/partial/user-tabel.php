<?php
/** Tabel daftar akun admin. Butuh $users. */
$warna_role = warna_role_admin();
?>
<div class="card-panel p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="fw-bold mb-0">Daftar Akun Admin</h6>
        <a href="user.php?baru=1" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Tambah</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Akun</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th class="text-center" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($u['foto']): ?>
                                    <img src="../uploads/<?= e($u['foto']) ?>" class="thumb" alt="">
                                <?php else: ?>
                                    <div class="thumb d-flex align-items-center justify-content-center bg-primary text-white fw-bold">
                                        <?= strtoupper(substr($u['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-semibold"><?= e($u['nama_lengkap']) ?></div>
                                    <small class="text-muted"><?= e($u['email'] ?? '-') ?></small>
                                </div>
                            </div>
                        </td>
                        <td><code><?= e($u['username']) ?></code></td>
                        <td>
                            <span class="badge bg-<?= $warna_role[$u['role']] ?? 'secondary' ?>">
                                <?= e($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $u['status'] === 'Aktif' ? 'success' : 'secondary' ?>">
                                <?= e($u['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($u['dibuat_pada'])) ?></td>
                        <td class="text-center">
                            <a href="user.php?edit=<?= (int)$u['id_admin'] ?>"
                               class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <a href="user.php?hapus=<?= (int)$u['id_admin'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Hapus akun <?= e(addslashes($u['nama_lengkap'])) ?>?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
