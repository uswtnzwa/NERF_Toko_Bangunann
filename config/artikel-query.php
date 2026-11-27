<?php
/**
 * Query & parameter untuk halaman blog / berita.
 *
 * Dipakai oleh:
 *   blog.php     -> daftar artikel (filter kategori, cari, paginasi)
 *   artikel.php  -> detail satu artikel + artikel terkait
 *
 * Variabel yang dihasilkan:
 *   $db, $db_aktif, $tabel_artikel
 *   $kategori, $cari, $halaman, $per_halaman
 *   $daftar_kategori, $daftar_artikel, $unggulan
 *   $total, $total_halaman, $offset, $footer_kategori, $flash
 */

$db       = db_opsional();
$db_aktif = $db instanceof mysqli;

/* Tabel artikel mungkin belum ada bila migrasi belum dijalankan. */
$tabel_artikel = false;
if ($db_aktif) {
    $tabel_artikel = $db->query("SHOW TABLES LIKE 'artikel'")->num_rows > 0;
}

/* ================= parameter ================= */

$kategori    = trim($_GET['kategori'] ?? '');
$cari        = trim($_GET['cari'] ?? '');
$halaman     = max(1, (int)($_GET['halaman'] ?? 1));
$per_halaman = 6;

/** Susun URL blog sambil mempertahankan filter yang sedang aktif. */
function artikel_url(array $ubah = []): string
{
    $param = $_GET;
    unset($param['halaman'], $param['slug']);
    $param = array_merge($param, $ubah);

    foreach ($param as $k => $v) {
        if ($v === '' || $v === null) {
            unset($param[$k]);
        }
    }
    return 'blog.php' . ($param ? '?' . http_build_query($param) : '');
}

/** Ringkas teks HTML menjadi cuplikan satu paragraf. */
function artikel_ringkas(string $teks, int $panjang = 165): string
{
    $teks = trim(preg_replace('/\s+/', ' ', strip_tags($teks)));
    return mb_strlen($teks) <= $panjang
        ? $teks
        : mb_substr($teks, 0, $panjang) . '...';
}

/** Format tanggal terbit yang ramah dibaca. */
function artikel_tanggal(?string $tanggal): string
{
    return $tanggal ? date('d M Y', strtotime($tanggal)) : '-';
}

/** Kelas warna & ikon Bootstrap per kategori artikel. */
function artikel_gaya(string $kategori): array
{
    switch ($kategori) {
        case 'Industri':
            return ['warna' => 'ungu', 'ikon' => 'bi-building'];
        case 'Tips':
            return ['warna' => 'hijau', 'ikon' => 'bi-lightbulb'];
        default:
            return ['warna' => 'biru', 'ikon' => 'bi-box-seam'];
    }
}

/** Ambil satu artikel berdasarkan slug (hanya yang sudah terbit). */
function artikel_ambil(mysqli $db, string $slug): ?array
{
    $stmt = $db->prepare(
        "SELECT * FROM artikel WHERE slug = ? AND status = 'Terbit' LIMIT 1"
    );
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

/** Tambah jumlah pembaca artikel. */
function artikel_tambah_dibaca(mysqli $db, int $id): void
{
    $stmt = $db->prepare("UPDATE artikel SET dibaca = dibaca + 1 WHERE id_artikel = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

/** Ambil beberapa artikel lain dalam kategori yang sama. */
function artikel_terkait(mysqli $db, int $id, string $kategori, int $batas = 3): array
{
    $batas = max(1, $batas);
    $stmt = $db->prepare(
        "SELECT id_artikel, judul, slug, kategori, ringkasan, gambar, penulis, dibaca, diterbitkan_pada
         FROM artikel
         WHERE status = 'Terbit' AND kategori = ? AND id_artikel <> ?
         ORDER BY diterbitkan_pada DESC, id_artikel DESC LIMIT $batas"
    );
    $stmt->bind_param('si', $kategori, $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/* ================= daftar artikel ================= */

$daftar_kategori = [];
$daftar_artikel  = [];
$unggulan        = null;
$total           = 0;
$total_halaman   = 1;
$offset          = 0;

if ($tabel_artikel) {
    $daftar_kategori = $db->query(
        "SELECT kategori, COUNT(*) AS jml FROM artikel
         WHERE status = 'Terbit'
         GROUP BY kategori
         ORDER BY FIELD(kategori, 'Produk', 'Tips', 'Industri'), kategori"
    )->fetch_all(MYSQLI_ASSOC);

    $where = ["status = 'Terbit'"];
    $types = '';
    $nilai = [];

    /* Artikel terbaru ditampilkan sebagai unggulan (hanya di tampilan awal). */
    if ($kategori === '' && $cari === '') {
        $unggulan = $db->query(
            "SELECT * FROM artikel WHERE status = 'Terbit'
             ORDER BY diterbitkan_pada DESC, id_artikel DESC LIMIT 1"
        )->fetch_assoc() ?: null;

        if ($unggulan) {
            $where[] = 'id_artikel <> ?';
            $types  .= 'i';
            $nilai[] = (int)$unggulan['id_artikel'];
        }
    }

    if ($kategori !== '') {
        $where[] = 'kategori = ?';
        $types  .= 's';
        $nilai[] = $kategori;
    }
    if ($cari !== '') {
        $where[] = '(judul LIKE ? OR ringkasan LIKE ? OR isi LIKE ?)';
        $kunci   = '%' . $cari . '%';
        $types  .= 'sss';
        $nilai[] = $kunci;
        $nilai[] = $kunci;
        $nilai[] = $kunci;
    }

    $sql_where = implode(' AND ', $where);

    /* Hitung total untuk paginasi. */
    $stmt = $db->prepare("SELECT COUNT(*) AS c FROM artikel WHERE $sql_where");
    if ($types !== '') {
        $stmt->bind_param($types, ...$nilai);
    }
    $stmt->execute();
    $total = (int)$stmt->get_result()->fetch_assoc()['c'];

    $total_halaman = max(1, (int)ceil($total / $per_halaman));
    $halaman       = min($halaman, $total_halaman);
    $offset        = ($halaman - 1) * $per_halaman;

    /* Ambil artikel untuk halaman ini. */
    $stmt = $db->prepare(
        "SELECT id_artikel, judul, slug, kategori, ringkasan, gambar, penulis, dibaca, diterbitkan_pada
         FROM artikel WHERE $sql_where
         ORDER BY diterbitkan_pada DESC, id_artikel DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->bind_param($types . 'ii', ...array_merge($nilai, [$per_halaman, $offset]));
    $stmt->execute();
    $daftar_artikel = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/* Daftar kategori produk untuk kolom footer. */
$footer_kategori = [];
if ($db_aktif) {
    $footer_kategori = $db->query(
        "SELECT id_kategori, nama_kategori FROM kategori
         WHERE status = 'Aktif' ORDER BY nama_kategori"
    )->fetch_all(MYSQLI_ASSOC);
}

$flash = ambil_flash();
