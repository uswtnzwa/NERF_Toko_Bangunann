<?php
/**
 * Data untuk admin/analitik.php.
 *
 * Pengunjung : tabel kunjungan (dicatat per halaman yang dibuka).
 * Penjualan  : pesanan berstatus selain Menunggu Pembayaran/Dibatalkan/Retur.
 * Terlaris   : detail_pesanan dari pesanan tersebut, dikelompokkan per produk.
 */

$tahun = preg_match('/^\d{4}$/', $_GET['tahun'] ?? '') ? (string)$_GET['tahun'] : date('Y');
$bulan = preg_match('/^\d{1,2}$/', $_GET['bulan'] ?? '')
    ? str_pad((string)$_GET['bulan'], 2, '0', STR_PAD_LEFT)
    : '';

/** Klausa periode untuk sebuah kolom tanggal. */
function analitik_periode(string $kolom, string $tahun, string $bulan): string
{
    $sql = 'YEAR(' . $kolom . ') = ' . (int)$tahun;
    if ($bulan !== '') {
        $sql .= ' AND MONTH(' . $kolom . ') = ' . (int)$bulan;
    }
    return $sql;
}

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
];

/* ================= PENGUNJUNG ================= */
/* Total dihitung dari seluruh baris, tidak dibatasi LIMIT seperti daftar. */
$ringkasan_kunjungan = $koneksi->query(
    'SELECT COUNT(*) AS jml, COUNT(DISTINCT session_id) AS unik
     FROM kunjungan WHERE ' . analitik_periode('tanggal', $tahun, $bulan)
)->fetch_assoc();
$total_pengunjung    = (int)$ringkasan_kunjungan['jml'];
$jumlah_session_unik = (int)$ringkasan_kunjungan['unik'];

$pengunjung_per_bulan = [];
foreach ($koneksi->query(
    'SELECT DATE_FORMAT(tanggal, \'%m\') AS bl, COUNT(*) AS jml,
            COUNT(DISTINCT session_id) AS unik
     FROM kunjungan WHERE YEAR(tanggal) = ' . (int)$tahun . ' GROUP BY bl'
)->fetch_all(MYSQLI_ASSOC) as $r) {
    $pengunjung_per_bulan[$r['bl']] = $r;
}

/* Halaman dikelompokkan menjadi 6 label yang mudah dibaca. */
$grup_halaman = [
    ['label' => 'Beranda',            'awalan' => '/beranda.php'],
    ['label' => 'Katalog Produk',     'awalan' => '/katalog.php'],
    ['label' => 'Artikel & Berita',   'awalan' => '/blog.php'],
    ['label' => 'Artikel & Berita',   'awalan' => '/artikel.php'],
    ['label' => 'Halaman Login',      'awalan' => '/masuk.php'],
    ['label' => 'Layanan Pelanggan',  'awalan' => '/customer-service.php'],
    ['label' => 'Akun & Pendaftaran', 'awalan' => '/daftar.php'],
    ['label' => 'Akun & Pendaftaran', 'awalan' => '/lupa-sandi.php'],
    ['label' => 'Akun & Pendaftaran', 'awalan' => '/pengaturan.php'],
    ['label' => 'Pesanan Saya',       'awalan' => '/pesanan-saya.php'],
    ['label' => 'Checkout',           'awalan' => '/checkout.php'],
    ['label' => 'Testimoni',          'awalan' => '/testimoni.php'],
    ['label' => 'Syarat & Kebijakan', 'awalan' => '/syarat-ketentuan.php'],
    ['label' => 'Syarat & Kebijakan', 'awalan' => '/kebijakan-privasi.php'],
];

$mentah_halaman = $koneksi->query(
    'SELECT halaman, COUNT(*) AS jml FROM kunjungan
     WHERE ' . analitik_periode('tanggal', $tahun, $bulan) . '
     GROUP BY halaman'
)->fetch_all(MYSQLI_ASSOC);

/* Satu label hanya satu baris: beberapa awalan URL digabung jumlahnya. */
$halaman_teratas = [];
foreach ($grup_halaman as $grup) {
    $label = $grup['label'];
    if (!isset($halaman_teratas[$label])) {
        $halaman_teratas[$label] = ['label' => $label, 'jml' => 0];
    }
    foreach ($mentah_halaman as $r) {
        if (str_starts_with($r['halaman'], $grup['awalan'])) {
            $halaman_teratas[$label]['jml'] += (int)$r['jml'];
        }
    }
}
$halaman_teratas = array_values($halaman_teratas);
usort($halaman_teratas, static fn(array $a, array $b): int => $b['jml'] <=> $a['jml']);

$sumber_teratas = $koneksi->query(
    'SELECT sumber, COUNT(*) AS jml FROM kunjungan
     WHERE ' . analitik_periode('tanggal', $tahun, $bulan) . '
     GROUP BY sumber ORDER BY jml DESC'
)->fetch_all(MYSQLI_ASSOC);

/* ================= PENJUALAN ================= */
$status_jual = "p.status_pesanan NOT IN ('Menunggu Pembayaran','Dibatalkan','Retur')";

$penjualan = $koneksi->query(
    "SELECT p.kode_pesanan, p.tanggal_pesanan, p.total, p.status_pesanan,
            pl.nama_lengkap
     FROM pesanan p LEFT JOIN pelanggan pl ON pl.id_pelanggan = p.id_pelanggan
     WHERE $status_jual AND " . analitik_periode('p.tanggal_pesanan', $tahun, $bulan) . "
     ORDER BY p.tanggal_pesanan DESC, p.id_pesanan DESC LIMIT 50"
)->fetch_all(MYSQLI_ASSOC);

$penjualan_per_bulan = [];
foreach ($koneksi->query(
    "SELECT DATE_FORMAT(p.tanggal_pesanan, '%m') AS bl, COUNT(*) AS jml,
            SUM(p.total) AS omzet
     FROM pesanan p WHERE $status_jual AND YEAR(p.tanggal_pesanan) = " . (int)$tahun . "
     GROUP BY bl"
)->fetch_all(MYSQLI_ASSOC) as $r) {
    $penjualan_per_bulan[$r['bl']] = $r;
}

/* ================= PRODUK TERLARIS ================= */
$terlaris = $koneksi->query(
    "SELECT pr.id_produk, pr.kode_produk, pr.nama_produk, pr.satuan,
            SUM(d.jumlah) AS qty, SUM(d.subtotal) AS omzet,
            COUNT(DISTINCT ps.id_pesanan) AS jml_pesanan
     FROM detail_pesanan d
     JOIN produk pr ON pr.id_produk = d.id_produk
     JOIN pesanan ps ON ps.id_pesanan = d.id_pesanan
     WHERE ps.status_pesanan NOT IN ('Menunggu Pembayaran','Dibatalkan','Retur')
       AND " . analitik_periode('ps.tanggal_pesanan', $tahun, $bulan) . "
     GROUP BY pr.id_produk
     ORDER BY qty DESC, omzet DESC
     LIMIT 10"
)->fetch_all(MYSQLI_ASSOC);

/* ================= REKAP 12 BULAN ================= */
$rekap = [];
for ($i = 1; $i <= 12; $i++) {
    $k = str_pad((string)$i, 2, '0', STR_PAD_LEFT);
    $kunj = $pengunjung_per_bulan[$k] ?? ['jml' => 0, 'unik' => 0];
    $jual = $penjualan_per_bulan[$k] ?? ['jml' => 0, 'omzet' => 0];
    $rekap[] = [
        'bulan'     => $k,
        'nama'      => $nama_bulan[$k],
        'pengunjung' => (int)$kunj['jml'],
        'unik'      => (int)$kunj['unik'],
        'pesanan'   => (int)$jual['jml'],
        'omzet'     => (float)$jual['omzet'],
    ];
}

/* ================= TOTAL FILTER AKTIF ================= */
$total_pesanan = 0;
$total_omzet = 0.0;
foreach ($penjualan as $p) {
    $total_pesanan++;
    $total_omzet += (float)$p['total'];
}
$total_qty_terlaris = 0;
foreach ($terlaris as $t) {
    $total_qty_terlaris += (int)$t['qty'];
}
$produk_teratas = $terlaris[0]['nama_produk'] ?? '-';

/* ================= TAHUN TERSEDIA ================= */
$tahun_list = [];
foreach ($koneksi->query(
    'SELECT YEAR(tanggal) AS y FROM kunjungan
     UNION SELECT YEAR(tanggal_pesanan) AS y FROM pesanan
     ORDER BY y DESC'
)->fetch_all(MYSQLI_ASSOC) as $r) {
    $tahun_list[] = (string)$r['y'];
}
if (!in_array($tahun, $tahun_list, true)) {
    $tahun_list[] = $tahun;
    rsort($tahun_list);
}
