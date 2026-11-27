<?php
/**
 * Aksi admin/kategori.php: hapus & simpan kategori.
 */

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../config/fungsi.php';

/**
 * Hapus kategori.
 * Kategori yang masih dipakai produk tidak boleh dihapus (foreign key),
 * cukup diminta untuk dinonaktifkan.
 * @return array{0:string,1:string} [tipe flash, pesan]
 */
function kategori_hapus(mysqli $koneksi, int $id): array
{
    $cek = $koneksi->prepare('SELECT COUNT(*) c FROM produk WHERE id_kategori = ?');
    $cek->bind_param('i', $id);
    $cek->execute();
    $jumlah_produk = (int)$cek->get_result()->fetch_assoc()['c'];

    if ($jumlah_produk > 0) {
        return ['danger', "Kategori masih dipakai oleh $jumlah_produk produk. Nonaktifkan saja, jangan hapus."];
    }

    $del = $koneksi->prepare('DELETE FROM kategori WHERE id_kategori = ?');
    $del->bind_param('i', $id);
    $del->execute();

    return ['success', 'Kategori dihapus.'];
}

/**
 * Simpan kategori baru atau perbarui yang ada.
 * @return array{0:string,1:string} [tipe flash, pesan]
 */
function kategori_simpan(mysqli $koneksi, array $in): array
{
    if ($in['nama'] === '') {
        return ['danger', 'Nama kategori wajib diisi.'];
    }

    try {
        $gambar = upload_gambar('gambar', null);
        $id     = $in['id'];
        $nama   = $in['nama'];

        if ($id > 0) {
            // Kolom gambar hanya ikut di-update kalau ada gambar baru
            $sql = 'UPDATE kategori SET nama_kategori=?, deskripsi=?'
                 . ($gambar ? ', gambar=?' : '') . ', status=? WHERE id_kategori=?';

            $st = $koneksi->prepare($sql);
            if ($gambar) {
                $st->bind_param('ssssi', $nama, $in['deskripsi'], $gambar, $in['status'], $id);
            } else {
                $st->bind_param('sssi', $nama, $in['deskripsi'], $in['status'], $id);
            }
            $st->execute();

            return ['success', 'Kategori "' . $nama . '" diperbarui.'];
        }

        $ins = $koneksi->prepare(
            'INSERT INTO kategori (nama_kategori, deskripsi, gambar, status) VALUES (?,?,?,?)'
        );
        $ins->bind_param('ssss', $nama, $in['deskripsi'], $gambar, $in['status']);
        $ins->execute();

        return ['success', 'Kategori "' . $nama . '" ditambahkan.'];
    } catch (RuntimeException $ex) {
        return ['danger', $ex->getMessage()];
    }
}
