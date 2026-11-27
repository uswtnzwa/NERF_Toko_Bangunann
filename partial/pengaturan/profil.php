<?php
/** Form pengaturan profil. Butuh $pelanggan dari pengaturan.php. */
?>
<section id="profil" class="pengaturan-kotak">
    <div class="pengaturan-judul">
        <span class="pengaturan-ikon"><i class="bi bi-person"></i></span>
        <div>
            <h2>Profil Akun</h2>
            <p>Perbarui nama, email, nomor HP, dan foto profil Anda.</p>
        </div>
    </div>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="aksi" value="profil">

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Nama lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control form-control-sm"
                       value="<?= e($pelanggan['nama_lengkap'] ?? '') ?>"
                       maxlength="100" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Nomor HP</label>
                <input type="text" name="nomor_hp" class="form-control form-control-sm"
                       value="<?= e($pelanggan['nomor_hp'] ?? '') ?>"
                       maxlength="20" placeholder="08xxxxxxxxxx">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control form-control-sm"
                       value="<?= e($pelanggan['email'] ?? '') ?>"
                       maxlength="100" required>
                <div class="form-text">Email ini dipakai untuk masuk dan menerima notifikasi.</div>
            </div>

            <div class="col-12">
                <label class="form-label small fw-semibold">Foto profil</label>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="pratinjau-foto">
                        <?php if (!empty($pelanggan['foto'])): ?>
                            <img id="pratinjauFoto" src="<?= e(img_url('uploads/' . $pelanggan['foto'])) ?>" alt="Pratinjau foto">
                        <?php else: ?>
                            <span id="pratinjauFotoTeks"><?= e(initials($pelanggan['nama_lengkap'] ?? 'P')) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1" style="min-width:220px">
                        <input type="file" name="foto" id="inputFoto" accept="image/jpeg,image/png,image/webp"
                               class="form-control form-control-sm">
                        <div class="form-text">JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                        <?php if (!empty($pelanggan['foto'])): ?>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="hapus_foto"
                                       id="hapusFoto" value="1">
                                <label class="form-check-label small text-muted" for="hapusFoto">
                                    Hapus foto profil
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="pengaturan-aksi">
            <button class="btn btn-primary btn-sm"><i class="bi bi-check2-circle"></i> Simpan Profil</button>
        </div>
    </form>
</section>
