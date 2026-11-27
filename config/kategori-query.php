<?php
/** Data untuk halaman admin/kategori.php. */

$kategori = $koneksi->query(
    'SELECT k.*, (SELECT COUNT(*) FROM produk p WHERE p.id_kategori = k.id_kategori) AS jml_produk
     FROM kategori k ORDER BY k.nama_kategori'
)->fetch_all(MYSQLI_ASSOC);
