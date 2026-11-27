/* =========================================================
   ALUR CHECKOUT - form pemesanan (checkout.php)
   Mengatur panel alamat dan panel pembayaran
   supaya halaman tetap ringkas saat pelanggan memilih opsi.
   ========================================================= */

/* ---------- panel alamat: muncul hanya untuk "diantar" ---------- */
function sinkronPanelAlamat() {
    const kirim = document.querySelector('input[name="metode_pengiriman"]:checked');
    const panel = document.getElementById('panelAlamat');
    if (!kirim || !panel) return;

    const perluAlamat = kirim.value === 'diantar';
    panel.classList.toggle('d-none', !perluAlamat);

    // Kolom alamat hanya wajib saat diantar
    panel.querySelectorAll('input[name="nama_penerima"], input[name="nomor_hp"], textarea[name="alamat"]')
        .forEach(el => el.required = perluAlamat);

    // "Bayar di tempat" hanya berlaku untuk ambil di toko.
    // Saat pelanggan pilih diantar, kartu COD disembunyikan dan
    // pilihan pembayaran dipindahkan ke transfer supaya tidak terkirim tidak valid.
    const cod = document.querySelector('input[name="metode_pembayaran"][value="bayar_di_tempat"]');
    if (cod) {
        const kartuCod = cod.closest('.pilih-kartu');
        if (kartuCod) kartuCod.classList.toggle('d-none', perluAlamat);

        if (perluAlamat && cod.checked) {
            cod.checked = false;
            const transfer = document.querySelector('input[name="metode_pembayaran"][value="transfer"]');
            if (transfer) transfer.checked = true;
        }
    }
    sinkronPanelPembayaran();
}

/* ---------- panel pembayaran: tampilkan instruksi yang sesuai ---------- */
function sinkronPanelPembayaran() {
    const pilih = document.querySelector('input[name="metode_pembayaran"]:checked');
    const kunci = pilih ? pilih.value : '';

    document.querySelectorAll('#panelPembayaran [data-panel]').forEach(panel => {
        panel.classList.toggle('d-none', panel.dataset.panel !== kunci);
    });

    // Bukti transfer hanya untuk metode transfer
    const bukti = document.querySelector('input[name="bukti_transfer"]');
    if (bukti) bukti.required = kunci === 'transfer';
}

/* ---------- isi form alamat dari alamat tersimpan yang dipilih ---------- */
function isiDariAlamatTersimpan(radio) {
    if (!radio || radio.dataset.baru === '1') return;

    const set = (name, value) => {
        const el = document.querySelector('[name="' + name + '"]');
        if (el && value) el.value = value;
    };

    set('nama_penerima', radio.dataset.nama);
    set('nomor_hp', radio.dataset.hp);
    set('alamat', radio.dataset.alamat);
}

/* ---------- pasang semua penangan ---------- */
function pasangCheckout() {
    document.querySelectorAll('input[name="metode_pengiriman"]').forEach(el => {
        el.addEventListener('change', sinkronPanelAlamat);
    });

    document.querySelectorAll('input[name="metode_pembayaran"]').forEach(el => {
        el.addEventListener('change', sinkronPanelPembayaran);
    });

    document.querySelectorAll('input[name="id_alamat"]').forEach(el => {
        el.addEventListener('change', () => isiDariAlamatTersimpan(el));
    });

    // Cegah klik ganda saat pesanan sedang disimpan
    const form = document.getElementById('formCheckout');
    const tombol = document.getElementById('btnSimpan');
    if (form && tombol) {
        form.addEventListener('submit', () => {
            tombol.disabled = true;
            tombol.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
        });
    }

    // Kondisi awal setelah halaman selesai dimuat
    sinkronPanelAlamat();
}

// Script ini diletakkan setelah footer.php, jadi DOM bisa sudah siap
// sebelum listener didaftarkan. Cek dulu, baru daftarkan.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', pasangCheckout);
} else {
    pasangCheckout();
}
