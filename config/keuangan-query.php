<?php
/**
 * Data untuk admin/keuangan.php.
 *
 * Pendapatan : pembayaran berstatus Diverifikasi dari pesanan yang tidak
 *              Dibatalkan/Retur (tanggal pakai tanggal bayar, fallback tanggal pesanan).
 * Pengeluaran: tabel pengeluaran (biaya operasional yang dicatat admin).
 */

$tahun = preg_match('/^\d{4}$/', $_GET['tahun'] ?? '') ? (string)$_GET['tahun'] : date('Y');
$bulan = preg_match('/^\d{1,2}$/', $_GET['bulan'] ?? '')
    ? str_pad((string)$_GET['bulan'], 2, '0', STR_PAD_LEFT)
    : '';

/** Klausa periode untuk sebuah kolom tanggal. */
function keuangan_periode(string $kolom, string $tahun, string $bulan): string
{
    $sql = 'YEAR(' . $kolom . ') = ' . (int)$tahun;
    if ($bulan !== '') {
        $sql .= ' AND MONTH(' . $kolom . ') = ' . (int)$bulan;
    }
    return $sql;
}

$tgl_pendapatan = 'COALESCE(b.tanggal_pembayaran, p.tanggal_pesanan)';

/* ---------- daftar pendapatan ---------- */
$pendapatan = $koneksi->query(
    "SELECT p.kode_pesanan, p.status_pesanan, $tgl_pendapatan AS tanggal,
            pl.nama_lengkap, b.metode, b.jumlah
     FROM pembayaran b
     JOIN pesanan p ON p.id_pesanan = b.id_pesanan
     LEFT JOIN pelanggan pl ON pl.id_pelanggan = p.id_pelanggan
     WHERE b.status = 'Diverifikasi'
       AND p.status_pesanan NOT IN ('Dibatalkan','Retur')
       AND " . keuangan_periode($tgl_pendapatan, $tahun, $bulan) . "
     ORDER BY tanggal DESC, p.id_pesanan DESC"
)->fetch_all(MYSQLI_ASSOC);

/* ---------- daftar pengeluaran ---------- */
$pengeluaran = $koneksi->query(
    'SELECT * FROM pengeluaran
     WHERE ' . keuangan_periode('tanggal', $tahun, $bulan) . '
     ORDER BY tanggal DESC, id_pengeluaran DESC'
)->fetch_all(MYSQLI_ASSOC);

/* ---------- rekap per bulan (setahun penuh) ---------- */
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
];

$masuk_per_bulan = [];
foreach ($koneksi->query(
    "SELECT MONTH($tgl_pendapatan) AS bl, SUM(b.jumlah) AS total
     FROM pembayaran b JOIN pesanan p ON p.id_pesanan = b.id_pesanan
     WHERE b.status = 'Diverifikasi'
       AND p.status_pesanan NOT IN ('Dibatalkan','Retur')
       AND YEAR($tgl_pendapatan) = " . (int)$tahun . "
     GROUP BY bl"
)->fetch_all(MYSQLI_ASSOC) as $r) {
    $masuk_per_bulan[str_pad((string)$r['bl'], 2, '0', STR_PAD_LEFT)] = (float)$r['total'];
}

$keluar_per_bulan = [];
foreach ($koneksi->query(
    'SELECT DATE_FORMAT(tanggal, \'%m\') AS bl, SUM(jumlah) AS total
     FROM pengeluaran WHERE YEAR(tanggal) = ' . (int)$tahun . ' GROUP BY bl'
)->fetch_all(MYSQLI_ASSOC) as $r) {
    $keluar_per_bulan[$r['bl']] = (float)$r['total'];
}

$rekap = [];
for ($i = 1; $i <= 12; $i++) {
    $kunci = str_pad((string)$i, 2, '0', STR_PAD_LEFT);
    $masuk  = $masuk_per_bulan[$kunci] ?? 0.0;
    $keluar = $keluar_per_bulan[$kunci] ?? 0.0;
    $rekap[] = [
        'bulan'      => $kunci,
        'nama'       => $nama_bulan[$kunci],
        'pendapatan' => $masuk,
        'pengeluaran' => $keluar,
        'laba'       => $masuk - $keluar,
    ];
}

/* ---------- total sesuai filter aktif ---------- */
$total_pendapatan  = 0.0;
$total_pengeluaran = 0.0;
foreach ($pendapatan as $r) {
    $total_pendapatan += (float)$r['jumlah'];
}
foreach ($pengeluaran as $r) {
    $total_pengeluaran += (float)$r['jumlah'];
}
$laba = $total_pendapatan - $total_pengeluaran;

/* ---------- tahun yang tersedia untuk dropdown ---------- */
$tahun_list = [];
foreach ($koneksi->query(
    'SELECT YEAR(tanggal) AS y FROM pengeluaran
     UNION SELECT YEAR(tanggal_pesanan) AS y FROM pesanan
     ORDER BY y DESC'
)->fetch_all(MYSQLI_ASSOC) as $r) {
    $tahun_list[] = (string)$r['y'];
}
if (!in_array($tahun, $tahun_list, true)) {
    $tahun_list[] = $tahun;
    rsort($tahun_list);
}
