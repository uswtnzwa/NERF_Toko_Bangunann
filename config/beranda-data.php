<?php
/**
 * Data untuk halaman beranda.php.
 *
 * Semua query dikumpulkan di satu tempat supaya beranda.php
 * hanya berisi markup. Halaman publik memakai koneksi opsional:
 * kalau database belum siap, halaman tetap tampil dengan fallback.
 */

$db       = db_opsional();
$db_aktif = $db instanceof mysqli;

$banner = $kategori = $produk_terlaris = $produk_terbaru = $testimoni = $faq = $statistik = [];

if ($db_aktif) {
    $banner = $db->query(
        "SELECT * FROM banner WHERE status = 'Aktif' ORDER BY urutan ASC, id_banner ASC"
    )->fetch_all(MYSQLI_ASSOC);

    $kategori = $db->query(
        "SELECT k.id_kategori, k.nama_kategori, k.deskripsi, k.gambar,
                (SELECT MIN(p.harga) FROM produk p
                  WHERE p.id_kategori = k.id_kategori AND p.status = 'Aktif') AS harga_awal,
                (SELECT COUNT(*) FROM produk p
                  WHERE p.id_kategori = k.id_kategori AND p.status = 'Aktif') AS jml
         FROM kategori k
         WHERE k.status = 'Aktif' AND k.id_kategori <= 6
         ORDER BY k.id_kategori ASC"
    )->fetch_all(MYSQLI_ASSOC);

    $produk_terlaris = $db->query(
        "SELECT p.id_produk, p.kode_produk, p.nama_produk, p.harga, p.harga_diskon,
                p.stok, p.satuan, p.gambar, k.nama_kategori, IFNULL(SUM(d.jumlah), 0) AS terjual
         FROM produk p
         LEFT JOIN kategori k ON k.id_kategori = p.id_kategori
         LEFT JOIN detail_pesanan d ON d.id_produk = p.id_produk
         LEFT JOIN pesanan ps ON ps.id_pesanan = d.id_pesanan
                            AND ps.status_pesanan NOT IN ('Menunggu Pembayaran','Dibatalkan','Retur')
         WHERE p.status = 'Aktif'
         GROUP BY p.id_produk
         ORDER BY terjual DESC, p.id_produk DESC
         LIMIT 4"
    )->fetch_all(MYSQLI_ASSOC);

    $produk_terbaru = $db->query(
        "SELECT p.id_produk, p.kode_produk, p.nama_produk, p.harga, p.harga_diskon,
                p.stok, p.satuan, p.gambar, k.nama_kategori
         FROM produk p
         LEFT JOIN kategori k ON k.id_kategori = p.id_kategori
         WHERE p.status = 'Aktif'
         ORDER BY p.dibuat_pada DESC, p.id_produk DESC
         LIMIT 8"
    )->fetch_all(MYSQLI_ASSOC);

    $testimoni = $db->query(
        "SELECT nama, isi, rating FROM testimoni
         WHERE status = 'Tampil' AND rating >= 4
         ORDER BY id_testimoni DESC LIMIT 3"
    )->fetch_all(MYSQLI_ASSOC);

    $faq = $db->query(
        "SELECT pertanyaan, jawaban FROM faq
         WHERE status = 'Aktif' ORDER BY urutan ASC LIMIT 5"
    )->fetch_all(MYSQLI_ASSOC);

    $statistik = [
        'produk'    => (int)$db->query("SELECT COUNT(*) c FROM produk WHERE status='Aktif'")->fetch_assoc()['c'],
        'kategori'  => (int)$db->query("SELECT COUNT(*) c FROM kategori WHERE status='Aktif'")->fetch_assoc()['c'],
        'pelanggan' => (int)$db->query("SELECT COUNT(*) c FROM pelanggan WHERE status='Aktif'")->fetch_assoc()['c'],
        'terjual'   => (int)$db->query("SELECT COALESCE(SUM(d.jumlah),0) c FROM detail_pesanan d
                                         JOIN pesanan p ON p.id_pesanan = d.id_pesanan
                                         WHERE p.status_pesanan NOT IN ('Menunggu Pembayaran','Dibatalkan','Retur')")
                       ->fetch_assoc()['c'],
    ];
}

$flash = ambil_flash();
