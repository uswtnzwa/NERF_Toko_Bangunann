/* =========================================================
   Pengaturan akun: pratinjau foto, cek konfirmasi password,
   dan penanda menu sidebar aktif.
   ========================================================= */
document.addEventListener('DOMContentLoaded', function () {
    /* ----- pratinjau foto profil ----- */
    const inputFoto = document.getElementById('inputFoto');
    if (inputFoto) {
        inputFoto.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;

            const kotak = document.querySelector('.pratinjau-foto');
            if (!kotak) return;

            const img = document.createElement('img');
            img.alt = 'Pratinjau foto';
            img.src = URL.createObjectURL(file);
            kotak.innerHTML = '';
            kotak.appendChild(img);

            const hapus = document.getElementById('hapusFoto');
            if (hapus) hapus.checked = false;
        });
    }

    /* ----- cocokkan password ----- */
    const baru = document.getElementById('passwordBaru');
    const ulang = document.getElementById('passwordKonfirmasi');
    const pesan = document.getElementById('pesanCocok');
    if (baru && ulang && pesan) {
        const cek = function () {
            if (ulang.value === '') {
                pesan.textContent = 'Isi ulang password baru Anda.';
                pesan.className = 'form-text';
            } else if (baru.value === ulang.value) {
                pesan.textContent = 'Password cocok.';
                pesan.className = 'form-text text-success';
            } else {
                pesan.textContent = 'Password belum cocok.';
                pesan.className = 'form-text text-danger';
            }
        };
        baru.addEventListener('input', cek);
        ulang.addEventListener('input', cek);
    }

    /* ----- menu sidebar mengikuti bagian yang terlihat ----- */
    const tautan = Array.from(document.querySelectorAll('.akun-menu a[href^="#"]'));
    const bagian = tautan
        .map(function (a) { return document.querySelector(a.getAttribute('href')); })
        .filter(Boolean);

    if ('IntersectionObserver' in window && bagian.length) {
        const pengamat = new IntersectionObserver(function (entri) {
            entri.forEach(function (e) {
                if (!e.isIntersecting) return;
                tautan.forEach(function (a) {
                    a.classList.toggle('aktif', a.getAttribute('href') === '#' + e.target.id);
                });
            });
        }, { rootMargin: '-80px 0px -70% 0px' });

        bagian.forEach(function (b) { pengamat.observe(b); });
    }
});
