<?php
/**
 * Aksi produk.php di panel admin: hapus & simpan (tambah/edit).
 * Dipanggil dari admin/produk.php sebelum query tampilannya dieksekusi.
 */

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../config/fungsi.php';

/* ================== HAPUS ================== */

/**
 * Produk yang sudah pernah masuk pesanan tidak boleh dihapus permanen,
 * karena tabel detail_pesanan memakai foreign key ke produk.
 * Karena itu produk hanya dinonaktifkan, dan penghapusan paksa
 * harus diminta sengaja lewat ?aksa=1.
 */
function produk_hapus(mysqli $koneksi, int $id, bool $aksa): array
{
    $g = $koneksi->prepare('SELECT gambar, nama_produk, stok, satuan, status FROM produk WHERE id_produk = ?');
    $g->bind_param('i', $id);
    $g->execute();
    $p = $g->get_result()->fetch_assoc();

    if (!$p) {
        return ['danger', 'Produk tidak ditemukan.'];
    }

    $cek = $koneksi->prepare(
        'SELECT COUNT(*) AS jml, COUNT(DISTINCT id_pesanan) AS jml_pesanan
         FROM detail_pesanan WHERE id_produk = ?'
    );
    $cek->bind_param('i', $id);
    $cek->execute();
    $dipakai = $cek->get_result()->fetch_assoc();
    $jml_item    = (int)$dipakai['jml'];
    $jml_pesanan = (int)$dipakai['jml_pesanan'];

    /* Sudah pernah terjual */
    if ($jml_item > 0) {
        if ($aksa) {
            $k = $koneksi->prepare('DELETE FROM detail_pesanan WHERE id_produk = ?');
            $k->bind_param('i', $id);
            $k->execute();
            $del = $koneksi->prepare('DELETE FROM produk WHERE id_produk = ?');
            $del->bind_param('i', $id);
            $del->execute();
            hapus_gambar($p['gambar']);

            return ['success', 'Produk "' . $p['nama_produk'] . '" beserta ' . $jml_item
                . ' baris riwayat detail_pesanan dihapus permanen.'];
        }

        // Default: jangan dihapus, cukup dinonaktifkan
        if ($p['status'] !== 'Nonaktif') {
            $u = $koneksi->prepare('UPDATE produk SET status = ? WHERE id_produk = ?');
            $baru = 'Nonaktif';
            $u->bind_param('si', $baru, $id);
            $u->execute();
        }

        return ['warning',
            'Produk "' . $p['nama_produk'] . '" sudah dipakai di ' . $jml_pesanan
            . ' pesanan, jadi tidak bisa dihapus permanen karena riwayat transaksi harus disimpan. '
            . 'Statusnya diubah menjadi Nonaktif sehingga tidak muncul di katalog. '
            . 'Kalau memang harus dihapus paksa, klik Hapus dengan ?aksa=1.'];
    }

    /* Belum pernah terjual: aman dihapus permanen, stok dikembalikan dulu */
    $koneksi->begin_transaction();
    try {
        $sisa = (int)$p['stok'];
        if ($sisa > 0) {
            $k = $koneksi->prepare('INSERT INTO stok (id_produk, jenis, jumlah, keterangan) VALUES (?,?,?,?)');
            $jenis  = 'Penyesuaian';
            $jumlah = $sisa;
            $ket    = 'Stok dikembalikan sebelum produk "' . $p['nama_produk'] . '" dihapus';
            $k->bind_param('isis', $id, $jenis, $jumlah, $ket);
            $k->execute();
        }

        $del = $koneksi->prepare('DELETE FROM produk WHERE id_produk = ?');
        $del->bind_param('i', $id);
        $del->execute();

        $koneksi->commit();
        hapus_gambar($p['gambar']);
        return ['success', 'Produk "' . $p['nama_produk'] . '" dihapus permanen.'];
    } catch (Throwable $ex) {
        $koneksi->rollback();
        return ['danger', 'Gagal menghapus produk: ' . $ex->getMessage()];
    }
}

/* ================== SIMPAN (tambah / edit) ================== */

/** Baca nilai form produk dari POST, sudah dibersihkan dari format ribuan. */
function produk_ambil_input(): array
{
    return [
        'id'           => (int)($_POST['id_produk'] ?? 0),
        'kode'         => trim($_POST['kode_produk'] ?? ''),
        'nama'         => trim($_POST['nama_produk'] ?? ''),
        'kategori'     => (int)($_POST['id_kategori'] ?? 0),
        'harga'        => (float)str_replace(['.', ' '], '', $_POST['harga'] ?? '0'),
        'harga_diskon' => (float)str_replace(['.', ' '], '', $_POST['harga_diskon'] ?? '0'),
        'stok'         => (int)($_POST['stok'] ?? 0),
        'stok_min'     => (int)($_POST['stok_minimum'] ?? 5),
        'satuan'       => trim($_POST['satuan'] ?? 'Pcs'),
        'warna'        => trim($_POST['warna'] ?? ''),
        'ukuran'       => trim($_POST['ukuran'] ?? ''),
        'berat'        => (float)str_replace(',', '.', $_POST['berat'] ?? '0'),
        'status'       => $_POST['status'] ?? 'Aktif',
        'deskripsi'    => trim($_POST['deskripsi'] ?? ''),
        'spesifikasi'  => trim($_POST['spesifikasi'] ?? ''),
    ];
}

/** Validasi input produk. Balikin pesan error, atau null bila valid. */
function produk_validasi(array $in): ?string
{
    if ($in['kode'] === '' || $in['nama'] === '' || $in['kategori'] === 0) {
        return 'Kode produk, nama produk, dan kategori wajib diisi.';
    }
    if ($in['harga'] <= 0) {
        return 'Harga harus lebih besar dari 0.';
    }
    if ($in['harga_diskon'] > 0 && $in['harga_diskon'] >= $in['harga']) {
        return 'Harga diskon harus lebih kecil dari harga normal.';
    }
    if ($in['stok'] < 0 || $in['stok_min'] < 0) {
        return 'Stok tidak boleh bernilai negatif.';
    }
    return null;
}

/** Catat mutasi stok di tabel `stok`. */
function produk_catat_stok(mysqli $koneksi, int $id_produk, string $jenis, int $jumlah, string $ket): void
{
    $c = $koneksi->prepare('INSERT INTO stok (id_produk, jenis, jumlah, keterangan) VALUES (?,?,?,?)');
    $c->bind_param('isis', $id_produk, $jenis, $jumlah, $ket);
    $c->execute();
}

/**
 * Simpan produk baru atau perbarui produk yang ada.
 * @return array{0:?string, 1:?string} [pesan error, pesan sukses]
 */
function produk_simpan(mysqli $koneksi, array $in, ?string $gambar_lama): array
{
    $gambar_baru = upload_gambar('gambar', $gambar_lama);
    $slug        = slugify($in['nama']);

    /* ---------- Mode edit ---------- */
    if ($in['id'] > 0) {
        $id = $in['id'];

        $st = $koneksi->prepare(
            'UPDATE produk SET id_kategori=?, kode_produk=?, nama_produk=?, slug=?, deskripsi=?,
             spesifikasi=?, harga=?, harga_diskon=?, stok=?, stok_minimum=?, satuan=?, warna=?, ukuran=?,
             berat=?, gambar=?, status=? WHERE id_produk=?'
        );
        $st->bind_param('isssssddiisssdssi', $in['kategori'], $in['kode'], $in['nama'], $slug, $in['deskripsi'],
            $in['spesifikasi'], $in['harga'], $in['harga_diskon'], $in['stok'], $in['stok_min'], $in['satuan'],
            $in['warna'], $in['ukuran'], $in['berat'], $gambar_baru, $in['status'], $id);

        // Baca stok lama dari database SEBELUM update, supaya nilai pembanding
        // tidak ikut berubah. Diambil langsung dari DB (bukan dari $edit) agar
        // tetap benar walau form disubmit tanpa parameter ?edit=id.
        $ambil_lama = $koneksi->prepare('SELECT stok FROM produk WHERE id_produk = ?');
        $ambil_lama->bind_param('i', $id);
        $ambil_lama->execute();
        $stok_lama = (int)($ambil_lama->get_result()->fetch_assoc()['stok'] ?? 0);

        $st->execute();

        // Simpan harga & stok per pilihan (warna/ukuran)
        simpan_opsi_produk($koneksi, $id, $in['warna'], $in['ukuran']);

        if ($in['stok'] !== $stok_lama) {
            $selisih = $in['stok'] - $stok_lama;
            produk_catat_stok(
                $koneksi, $id,
                $selisih > 0 ? 'Masuk' : 'Keluar',
                abs($selisih),
                'Penyesuaian dari halaman edit produk'
            );
        }

        return [null, 'Produk "' . $in['nama'] . '" berhasil diperbarui.'];
    }

    /* ---------- Mode tambah baru ---------- */
    $c = $koneksi->prepare('SELECT id_produk FROM produk WHERE kode_produk = ?');
    $c->bind_param('s', $in['kode']);
    $c->execute();
    if ($c->get_result()->num_rows > 0) {
        return ['Kode produk "' . $in['kode'] . '" sudah dipakai.', null];
    }

    $ins = $koneksi->prepare(
        'INSERT INTO produk (id_kategori, kode_produk, nama_produk, slug, deskripsi, spesifikasi,
         harga, harga_diskon, stok, stok_minimum, satuan, warna, ukuran, berat, gambar, status)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    $ins->bind_param('isssssddiisssdss', $in['kategori'], $in['kode'], $in['nama'], $slug, $in['deskripsi'],
        $in['spesifikasi'], $in['harga'], $in['harga_diskon'], $in['stok'], $in['stok_min'], $in['satuan'],
        $in['warna'], $in['ukuran'], $in['berat'], $gambar_baru, $in['status']);
    $ins->execute();
    $id_baru = $ins->insert_id;

    // Buat baris opsi (warna/ukuran) supaya bisa diisi harga & stok per pilihan
    simpan_opsi_produk($koneksi, $id_baru, $in['warna'], $in['ukuran'], true);

    if ($in['stok'] > 0) {
        produk_catat_stok($koneksi, $id_baru, 'Masuk', $in['stok'], 'Stok awal saat produk ditambahkan');
    }

    return [null, 'Produk "' . $in['nama'] . '" berhasil ditambahkan.'];
}
