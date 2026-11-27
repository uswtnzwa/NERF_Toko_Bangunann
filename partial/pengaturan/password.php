<?php /** Form ganti password. */ ?>
<section id="password" class="pengaturan-kotak">
    <div class="pengaturan-judul">
        <span class="pengaturan-ikon"><i class="bi bi-key"></i></span>
        <div>
            <h2>Ganti Password</h2>
            <p>Gunakan password yang kuat dan jangan bagikan kepada siapa pun.</p>
        </div>
    </div>

    <form method="post">
        <input type="hidden" name="aksi" value="password">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Password lama <span class="text-danger">*</span></label>
                <input type="password" name="password_lama" class="form-control form-control-sm"
                       autocomplete="current-password" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Password baru <span class="text-danger">*</span></label>
                <input type="password" name="password_baru" id="passwordBaru"
                       class="form-control form-control-sm" autocomplete="new-password"
                       minlength="6" required>
                <div class="form-text">Minimal 6 karakter.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Konfirmasi password baru <span class="text-danger">*</span></label>
                <input type="password" name="password_konfirmasi" id="passwordKonfirmasi"
                       class="form-control form-control-sm" autocomplete="new-password" required>
                <div class="form-text" id="pesanCocok">Isi ulang password baru Anda.</div>
            </div>
        </div>

        <div class="pengaturan-aksi">
            <button class="btn btn-primary btn-sm"><i class="bi bi-shield-lock"></i> Ubah Password</button>
            <span class="small text-muted">Anda akan tetap masuk setelah password diubah.</span>
        </div>
    </form>
</section>
