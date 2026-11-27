<?php
/** Langkah 2: buat password baru. Butuh $_SESSION[KUNCI_LANGKA]. */
?>
<div class="info-default mb-3">
    <i class="bi bi-check-circle text-success"></i>
    Email terdaftar. Halo, <strong><?= e($_SESSION[KUNCI_LANGKA]['nama']) ?></strong>,
    silakan buat password baru.
</div>

<form method="post" autocomplete="off">
    <input type="hidden" name="simpan_sandi" value="1">

    <div class="mb-3">
        <label class="label">Password Baru</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
            <input type="password" name="password_baru" class="form-control" required
                   minlength="6" placeholder="minimal 6 karakter">
        </div>
    </div>

    <div class="mb-3">
        <label class="label">Ulangi Password Baru</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="password_ulang" class="form-control" required
                   minlength="6" placeholder="ketik ulang password">
        </div>
    </div>

    <button class="btn btn-aksen w-100 text-white mb-3">
        <i class="bi bi-shield-lock"></i> Simpan Password Baru
    </button>
</form>

<div class="daftar-kecil">
    Salah email? <a href="lupa-sandi.php?ulang=1">Ganti email lain</a>
</div>
