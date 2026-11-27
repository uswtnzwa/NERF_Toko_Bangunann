/**
 * Detail produk: harga & batas stok mengikuti pilihan (ukuran/warna) yang dipilih.
 * Dipakai katalog.php pada halaman detail.
 */
(function () {
    const form = document.querySelector('form[action*="detail="]');
    const elHarga  = document.getElementById('hargaTerpilih');
    const elJumlah = document.getElementById('jumlahBeli');
    const elStok   = document.getElementById('stokTersedia');
    if (!form) return;

    const rupiah = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');

    function terapkan() {
        // Pilihan harga: ukuran lebih spesifik, jadi dibaca terakhir
        const terpilih = form.querySelector('input[name="ukuran"]:checked')
                      || form.querySelector('input[name="warna"]:checked');
        if (!terpilih || !elHarga) return;

        const harga = parseFloat(terpilih.dataset.harga || '0');
        const stok  = terpilih.dataset.stok;

        elHarga.textContent = rupiah(harga);

        if (elJumlah && stok !== undefined && stok !== '') {
            const batas = parseInt(stok, 10);
            elJumlah.max = batas;
            if (parseInt(elJumlah.value, 10) > batas) elJumlah.value = batas;
            if (elStok) elStok.textContent = 'Stok ukuran ini: ' + batas;
        }
    }

    form.addEventListener('change', function (ev) {
        if (ev.target.matches('input[name="warna"], input[name="ukuran"]')) terapkan();
    });

    // Set harga awal sesuai opsi pertama yang tercentang
    terapkan();
})();
