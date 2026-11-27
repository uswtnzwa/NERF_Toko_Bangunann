<?php
/**
 * Aksi & helper untuk admin/stok.php.
 *
 * Stok tersimpan di dua tempat yang saling terkait:
 *   - produk.stok  -> jumlah stok saat ini (angka yang dipakai toko)
 *   - stok         -> riwayat mutasi (audit trail)
 *
 * Setiap perubahan riwayat juga menyesuaikan produk.stok supaya keduanya
 * tetap sinkron.
 */

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../config/fungsi.php';

/** Status stok untuk tampilan: Habis / Menipis / Aman. */
function stok_status(int $stok, int $minimum): array
{
    if ($stok <= 0) {
        return ['label' => 'Habis', 'warna' => 'danger', 'ikon' => 'bi-x-circle'];
    }
    if ($stok <= $minimum) {
        return ['label' => 'Menipis', 'warna' => 'warning', 'ikon' => 'bi-exclamation-triangle'];
    }
    return ['label' => 'Aman', 'warna' => 'success', 'ikon' => 'bi-check-circle'];
}

/** Efek suatu mutasi terhadap angka stok (Penyesuaian bersifat absolut). */
function stok_efek(string $jenis, int $jumlah): int
{
    return match ($jenis) {
        'Masuk'  => $jumlah,
        'Keluar' => -$jumlah,
        default  => 0,
    };
}

/** Validasi bersama untuk jumlah & jenis mutasi. */
function stok_validasi(string $jenis, int $jumlah): ?string
{
    if (!in_array($jenis, ['Masuk', 'Keluar', 'Penyesuaian'], true)) {
        return 'Jenis mutasi tidak valid.';
    }
    if ($jumlah <= 0) {
        return 'Jumlah harus lebih dari 0.';
    }
    return null;
}

/** CREATE: catat mutasi baru dan perbarui stok produk. */
function stok_mutasi_create(mysqli $db, int $id_produk, string $jenis, int $jumlah, string $keterangan): array
{
    if ($id_produk <= 0) {
        return ['danger', 'Produk harus dipilih.'];
    }
    if ($pesan = stok_validasi($jenis, $jumlah)) {
        return ['danger', $pesan];
    }

    $db->begin_transaction();
    try {
        $s = $db->prepare('SELECT nama_produk, stok, satuan FROM produk WHERE id_produk = ? FOR UPDATE');
        $s->bind_param('i', $id_produk);
        $s->execute();
        $p = $s->get_result()->fetch_assoc();
        if (!$p) {
            throw new RuntimeException('Produk tidak ditemukan.');
        }

        $stok_baru = $jenis === 'Penyesuaian' ? $jumlah : (int)$p['stok'] + stok_efek($jenis, $jumlah);
        if ($stok_baru < 0) {
            throw new RuntimeException('Stok tidak cukup. Tersedia: ' . (int)$p['stok'] . ' ' . $p['satuan'] . '.');
        }

        $up = $db->prepare('UPDATE produk SET stok = ? WHERE id_produk = ?');
        $up->bind_param('ii', $stok_baru, $id_produk);
        $up->execute();

        $ket = $keterangan !== '' ? $keterangan : 'Mutasi stok oleh admin';
        $in = $db->prepare('INSERT INTO stok (id_produk, jenis, jumlah, keterangan) VALUES (?,?,?,?)');
        $in->bind_param('isis', $id_produk, $jenis, $jumlah, $ket);
        $in->execute();

        $db->commit();
        return ['success', 'Stok "' . $p['nama_produk'] . '" diperbarui: '
            . (int)$p['stok'] . ' → ' . $stok_baru . ' ' . $p['satuan'] . '.'];
    } catch (Throwable $ex) {
        $db->rollback();
        return ['danger', $ex->getMessage()];
    }
}

/** UPDATE: ubah mutasi yang sudah tercatat lalu hitung ulang stok produk. */
function stok_mutasi_update(mysqli $db, int $id_stok, string $jenis, int $jumlah, string $keterangan): array
{
    if ($pesan = stok_validasi($jenis, $jumlah)) {
        return ['danger', $pesan];
    }

    $db->begin_transaction();
    try {
        $s = $db->prepare(
            'SELECT st.id_produk, st.jenis AS jenis_lama, st.jumlah AS jumlah_lama,
                    p.stok, p.satuan, p.nama_produk
             FROM stok st JOIN produk p ON p.id_produk = st.id_produk
             WHERE st.id_stok = ? FOR UPDATE'
        );
        $s->bind_param('i', $id_stok);
        $s->execute();
        $d = $s->get_result()->fetch_assoc();
        if (!$d) {
            throw new RuntimeException('Mutasi tidak ditemukan.');
        }

        /* Hilangkan efek lama, lalu terapkan efek baru. */
        $stok_baru = (int)$d['stok'] - stok_efek($d['jenis_lama'], (int)$d['jumlah_lama']);
        $stok_baru += stok_efek($jenis, $jumlah);
        if ($jenis === 'Penyesuaian') {
            $stok_baru = $jumlah;
        }
        if ($stok_baru < 0) {
            throw new RuntimeException('Perubahan ini membuat stok menjadi negatif.');
        }

        $ket = $keterangan !== '' ? $keterangan : 'Mutasi stok oleh admin';
        $u = $db->prepare('UPDATE stok SET jenis = ?, jumlah = ?, keterangan = ? WHERE id_stok = ?');
        $u->bind_param('sisi', $jenis, $jumlah, $ket, $id_stok);
        $u->execute();

        $id_produk = (int)$d['id_produk'];
        $p = $db->prepare('UPDATE produk SET stok = ? WHERE id_produk = ?');
        $p->bind_param('ii', $stok_baru, $id_produk);
        $p->execute();

        $db->commit();
        return ['success', 'Mutasi berhasil diubah. Stok "' . $d['nama_produk'] . '" sekarang '
            . $stok_baru . ' ' . $d['satuan'] . '.'];
    } catch (Throwable $ex) {
        $db->rollback();
        return ['danger', $ex->getMessage()];
    }
}

/** DELETE: hapus mutasi dan kembalikan efeknya ke stok produk. */
function stok_mutasi_delete(mysqli $db, int $id_stok): array
{
    $db->begin_transaction();
    try {
        $s = $db->prepare(
            'SELECT st.id_produk, st.jenis, st.jumlah, p.stok, p.satuan, p.nama_produk
             FROM stok st JOIN produk p ON p.id_produk = st.id_produk
             WHERE st.id_stok = ? FOR UPDATE'
        );
        $s->bind_param('i', $id_stok);
        $s->execute();
        $d = $s->get_result()->fetch_assoc();
        if (!$d) {
            throw new RuntimeException('Mutasi tidak ditemukan.');
        }

        $stok_baru = (int)$d['stok'] - stok_efek($d['jenis'], (int)$d['jumlah']);
        if ($stok_baru < 0) {
            throw new RuntimeException('Mutasi tidak bisa dihapus karena stok akan menjadi negatif.');
        }

        $h = $db->prepare('DELETE FROM stok WHERE id_stok = ?');
        $h->bind_param('i', $id_stok);
        $h->execute();

        $id_produk = (int)$d['id_produk'];
        $p = $db->prepare('UPDATE produk SET stok = ? WHERE id_produk = ?');
        $p->bind_param('ii', $stok_baru, $id_produk);
        $p->execute();

        $db->commit();
        return ['success', 'Mutasi dihapus. Stok "' . $d['nama_produk'] . '" sekarang '
            . $stok_baru . ' ' . $d['satuan'] . '.'];
    } catch (Throwable $ex) {
        $db->rollback();
        return ['danger', $ex->getMessage()];
    }
}

/** UPDATE: ubah ambang batas stok minimum suatu produk. */
function stok_set_minimum(mysqli $db, int $id_produk, int $minimum): array
{
    if ($id_produk <= 0 || $minimum < 0) {
        return ['danger', 'Produk dan batas minimum tidak valid.'];
    }
    $c = $db->prepare('SELECT id_produk FROM produk WHERE id_produk = ?');
    $c->bind_param('i', $id_produk);
    $c->execute();
    if (!$c->get_result()->fetch_assoc()) {
        return ['danger', 'Produk tidak ditemukan.'];
    }

    $u = $db->prepare('UPDATE produk SET stok_minimum = ? WHERE id_produk = ?');
    $u->bind_param('ii', $minimum, $id_produk);
    $u->execute();
    return ['success', 'Batas stok minimum berhasil diperbarui.'];
}

/** DELETE: hapus seluruh riwayat mutasi satu produk (stok saat ini tidak diubah). */
function stok_hapus_riwayat(mysqli $db, int $id_produk): array
{
    $h = $db->prepare('DELETE FROM stok WHERE id_produk = ?');
    $h->bind_param('i', $id_produk);
    $h->execute();
    return ['success', 'Riwayat mutasi produk ini dihapus. Stok saat ini tidak berubah.'];
}
