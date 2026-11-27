<?php
/** Langkah 1: input email akun. Butuh $email_terakhir. */
?>
<div class="info-default mb-3">
    <i class="bi bi-info-circle"></i>
    Masukkan email akun pelanggan Anda. Jika email terdaftar,
    Anda bisa langsung membuat password baru.
</div>

<form method="post" autocomplete="off">
    <input type="hidden" name="cek_email" value="1">

    <div class="mb-3">
        <label class="label">Email Terdaftar</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" required autofocus
                   value="<?= e($email_terakhir) ?>" placeholder="contoh@mail.com">
        </div>
    </div>

    <button class="btn btn-aksen w-100 text-white mb-3">
        <i class="bi bi-arrow-right"></i> Lanjutkan
    </button>
</form>

<div class="daftar-kecil">
    Ingat passwordnya? <a href="masuk.php">Kembali ke halaman masuk</a>
</div>
