<?php
/**
 * Fungsi bantu aplikasi.
 *
 * File ini hanya pemuat (loader). Daftar file di bawah dipecah per	topik
 * supaya mudah dibaca dan tidak jadi satu file panjang:
 *
 *   analitik.php    -> pencatatan kunjungan otomatis untuk laporan
 *   auth.php        -> login admin & pelanggan, cek role
 *   checkout.php     -> alur pemesanan: pengiriman, ongkir, pembayaran
 *   db.php          -> koneksi opsional untuk halaman publik
 *   flash.php       -> pesan singkat antar halaman
 *   opsi_produk.php -> pilihan warna/ukuran beserta harga & stok
 *   password.php    -> hash & verifikasi password
 *   teks.php        -> escaping, format Rupiah, slug, inisial
 *   upload.php      -> upload & hapus gambar
 *   url.php         -> pembangun URL + cache busting gambar/aset
 */
require_once __DIR__ . '/fungsi/analitik.php';
require_once __DIR__ . '/fungsi/auth.php';
require_once __DIR__ . '/fungsi/checkout.php';
require_once __DIR__ . '/fungsi/db.php';
require_once __DIR__ . '/fungsi/flash.php';
require_once __DIR__ . '/fungsi/opsi_produk.php';
require_once __DIR__ . '/fungsi/password.php';
require_once __DIR__ . '/fungsi/teks.php';
require_once __DIR__ . '/fungsi/upload.php';
require_once __DIR__ . '/fungsi/url.php';

// Catat satu kunjungan toko depan (cek path/metode ada di dalam fungsi).
catat_kunjungan();
