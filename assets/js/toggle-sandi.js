/**
 * Tampilkan/sembunyikan password pada field password.
 * Dipakai masuk.php & daftar.php lewat tombol mata (#togglePw + #pw).
 * Kalau tombolnya tidak ada, file ini diam-diam dilewati.
 */
document.addEventListener('DOMContentLoaded', function () {
    const tombol = document.getElementById('togglePw');
    const pw = document.getElementById('pw');
    if (!tombol || !pw) return;

    tombol.addEventListener('click', function () {
        pw.type = pw.type === 'password' ? 'text' : 'password';
        this.querySelector('i').className = pw.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
});
