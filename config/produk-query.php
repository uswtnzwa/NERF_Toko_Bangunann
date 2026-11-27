<?php
/**
 * Data untuk halaman admin/produk.php:
 * daftar kategori, filter pencarian, dan daftar produk terpaginasikan.
 *
 * Menghasilkan: $daftar_kategori, $cari, $filter_k, $halaman, $offset, $total, $jml_hal, $produk
 */

$daftar_kategori = $koneksi->query(
    "SELECT id_kategori, nama_kategori FROM kategori WHERE status='Aktif' ORDER BY nama_kategori"
)->fetch_all(MYSQLI_ASSOC);

$cari     = trim($_GET['cari'] ?? '');
$filter_k = (int)($_GET['kategori'] ?? 0);
$halaman  = max(1, (int)($_GET['halaman'] ?? 1));
$per_hal  = 10;

/* ---------- klausa WHERE ---------- */
$where = ['1=1'];
$args  = [];
$types = '';

if ($cari !== '') {
    $where[] = '(p.nama_produk LIKE ? OR p.kode_produk LIKE ?)';
    $args[]  = "%$cari%";
    $args[]  = "%$cari%";
    $types  .= 'ss';
}
if ($filter_k > 0) {
    $where[] = 'p.id_kategori = ?';
    $args[]  = $filter_k;
    $types  .= 'i';
}
$sql_where = implode(' AND ', $where);

/* ---------- hitung total ---------- */
$stmt_count = $koneksi->prepare("SELECT COUNT(*) c FROM produk p WHERE $sql_where");
if ($args) {
    // bind_param mewajibkan variabel, jadi bind lewat referensi
    $bind_count = [];
    foreach ($args as $i => $v) {
        $bind_count[$i] = &$args[$i];
    }
    $stmt_count->bind_param($types, ...$bind_count);
}
$stmt_count->execute();
$total  = (int)$stmt_count->get_result()->fetch_assoc()['c'];
$jml_hal = max(1, (int)ceil($total / $per_hal));
$halaman = min($halaman, $jml_hal);
$offset  = ($halaman - 1) * $per_hal;

/* ---------- ambil daftar produk ---------- */
$stmt = $koneksi->prepare(
    "SELECT p.*, k.nama_kategori
     FROM produk p
     LEFT JOIN kategori k ON k.id_kategori = p.id_kategori
     WHERE $sql_where
     ORDER BY p.id_produk DESC
     LIMIT $per_hal OFFSET $offset"
);
if ($args) {
    $bind = [];
    foreach ($args as $i => $v) {
        $bind[$i] = &$args[$i];
    }
    $stmt->bind_param($types, ...$bind);
}
$stmt->execute();
$produk = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/** Link paginasi yang mempertahankan filter pencarian. */
function url_produk(array $p): string
{
    return 'produk.php?' . http_build_query([
        'cari'    => $p['cari'],
        'kategori' => $p['k'],
        'halaman' => $p['h'],
    ]);
}
