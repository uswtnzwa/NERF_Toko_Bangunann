<?php
/**
 * Query & parameter untuk katalog.php.
 *
 * Menghasilkan variabel berikut untuk dipakai oleh halaman:
 *   $kategori_id, $cari, $sort, $halaman, $offset, $per_halaman
 *   $produk, $kategori_terpilih, $total, $total_halaman, $daftar_kategori, $statistik
 *   $detail, $detail_gambar_lain
 *   $flash
 */

$db       = db_opsional();
$db_aktif = $db instanceof mysqli;

/* ================= parameter ================= */

$kategori_id = (int)($_GET['kategori'] ?? 0);
$cari        = trim($_GET['cari'] ?? '');
$sort        = in_array($_GET['sort'] ?? '', ['terlaris', 'termurah', 'terbaru', 'namaindex'], true)
                 ? $_GET['sort'] : '';
$halaman     = max(1, (int)($_GET['halaman'] ?? 1));
$per_halaman = 12;

/** Susun query URL sambil mempertahankan filter yang sedang aktif. */
function katalog_url(array $ubah = []): string
{
    $param = array_merge($_GET, $ubah);
    foreach ($param as $k => $v) {
        if ($v === '' || $v === null) {
            unset($param[$k]);
        }
    }
    // "detail" tidak boleh ikut dibawa ke link lain
    unset($param['halaman'], $param['detail']);
    return 'katalog.php' . ($param ? '?' . http_build_query($param) : '');
}

/** Pembantu ringkas untuk membangun link sort di toolbar. */
function tautan_sort(string $kunci, string $label, string $sort_aktif): string
{
    $aktif = $sort_aktif === $kunci;
    return '<a href="' . e(katalog_url(['sort' => $aktif ? '' : $kunci, 'halaman' => 1]))
         . '" class="btn btn-sm ' . ($aktif ? 'btn-primary' : 'btn-outline-secondary') . '">'
         . e($label) . '</a>';
}

/** Susun klausa ORDER BY dari nilai $sort. */
function katalog_order(string $sort): string
{
    switch ($sort) {
        case 'terlaris':
            return 'terjual DESC, p.id_produk DESC';
        case 'termurah':
            return 'CASE WHEN p.harga_diskon > 0 THEN p.harga_diskon ELSE p.harga END ASC, p.id_produk DESC';
        case 'terbaru':
            return 'p.dibuat_pada DESC, p.id_produk DESC';
        case 'namaindex':
            return 'p.nama_produk ASC';
        default:
            return 'k.nama_kategori ASC, p.nama_produk ASC';
    }
}

/* ================= daftar produk ================= */

$produk            = [];
$kategori_terpilih = null;
$total             = 0;
$total_halaman     = 1;
$daftar_kategori   = [];
$statistik         = ['produk' => 0, 'kategori' => 0, 'halaman' => 0];
$offset            = 0;

if ($db_aktif) {
    $daftar_kategori = $db->query(
        "SELECT k.id_kategori, k.nama_kategori,
                (SELECT COUNT(*) FROM produk p
                  WHERE p.id_kategori = k.id_kategori AND p.status = 'Aktif') AS jml
         FROM kategori k
         WHERE k.status = 'Aktif'
         ORDER BY k.nama_kategori ASC"
    )->fetch_all(MYSQLI_ASSOC);

    // Nama kategori yang sedang dipilih (untuk judul)
    foreach ($daftar_kategori as $k) {
        if ((int)$k['id_kategori'] === $kategori_id) {
            $kategori_terpilih = $k;
            break;
        }
    }

    /* ---------- klausa WHERE ---------- */
    $where = ["p.status = 'Aktif'"];
    $types = '';
    $nilai = [];

    if ($kategori_id > 0) {
        $where[] = 'p.id_kategori = ?';
        $types  .= 'i';
        $nilai[] = $kategori_id;
    }
    if ($cari !== '') {
        $where[] = '(p.nama_produk LIKE ? OR p.kode_produk LIKE ? OR p.deskripsi LIKE ?)';
        $kunci   = '%' . $cari . '%';
        $types  .= 'sss';
        $nilai[] = $kunci;
        $nilai[] = $kunci;
        $nilai[] = $kunci;
    }
    $sql_where = implode(' AND ', $where);

    /* ---------- hitung total & paginasi ---------- */
    $stmt = $db->prepare("SELECT COUNT(*) c FROM produk p WHERE $sql_where");
    if ($types !== '') {
        $stmt->bind_param($types, ...$nilai);
    }
    $stmt->execute();
    $total = (int)$stmt->get_result()->fetch_assoc()['c'];

    $total_halaman = max(1, (int)ceil($total / $per_halaman));
    $halaman       = min($halaman, $total_halaman);
    $offset         = ($halaman - 1) * $per_halaman;

    /* ---------- ambil data ---------- */
    $stmt = $db->prepare(
        "SELECT p.id_produk, p.kode_produk, p.nama_produk, p.deskripsi,
                p.harga, p.harga_diskon, p.stok, p.satuan, p.gambar, p.warna, p.ukuran,
                k.nama_kategori,
                IFNULL(SUM(d.jumlah), 0) AS terjual
         FROM produk p
         LEFT JOIN kategori k ON k.id_kategori = p.id_kategori
         LEFT JOIN detail_pesanan d ON d.id_produk = p.id_produk
         LEFT JOIN pesanan ps ON ps.id_pesanan = d.id_pesanan
                            AND ps.status_pesanan NOT IN ('Menunggu Pembayaran','Dibatalkan','Retur')
         WHERE $sql_where
         GROUP BY p.id_produk, k.nama_kategori
         ORDER BY " . katalog_order($sort) . "
         LIMIT ? OFFSET ?"
    );
    $stmt->bind_param($types . 'ii', ...array_merge($nilai, [$per_halaman, $offset]));
    $stmt->execute();
    $produk = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $statistik = [
        'produk'   => (int)$db->query("SELECT COUNT(*) c FROM produk WHERE status='Aktif'")->fetch_assoc()['c'],
        'kategori' => count($daftar_kategori),
        'halaman'  => $total_halaman,
    ];
}

/* ================= detail satu produk ================= */

$detail            = null;
$detail_gambar_lain = [];

if ($db_aktif && isset($_GET['detail'])) {
    $id = (int)$_GET['detail'];
    $s = $db->prepare("SELECT p.*, k.nama_kategori FROM produk p
                       LEFT JOIN kategori k ON k.id_kategori = p.id_kategori
                       WHERE p.id_produk = ? AND p.status = 'Aktif' LIMIT 1");
    $s->bind_param('i', $id);
    $s->execute();
    $detail = $s->get_result()->fetch_assoc();

    // Produk lain dari kategori yang sama
    if ($detail) {
        $l = $db->prepare("SELECT id_produk, kode_produk, nama_produk, harga, harga_diskon, stok, satuan, gambar
                           FROM produk
                           WHERE id_kategori = ? AND id_produk <> ? AND status = 'Aktif'
                           ORDER BY RAND() LIMIT 4");
        $l->bind_param('ii', $detail['id_kategori'], $id);
        $l->execute();
        $detail_gambar_lain = $l->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

/* ================= aksi keranjang (POST) ================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $db_aktif) {
    keranjang_aksi($db);
}

$flash = ambil_flash();
