<?php
/**
 * Aksi admin/user.php: hapus & simpan akun admin.
 */

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../config/fungsi.php';

/** Role yang boleh dipilih, dipakai juga untuk validasi. */
function daftar_role_admin(): array
{
    return ['Super Admin', 'Admin', 'Kasir', 'Warehouse', 'Customer Service'];
}

/** Warna badge untuk setiap role. */
function warna_role_admin(): array
{
    return [
        'Super Admin' => 'danger', 'Admin' => 'primary', 'Kasir' => 'success',
        'Warehouse' => 'warning', 'Customer Service' => 'info',
    ];
}

/**
 * Hapus akun admin.
 * @return array{0:string,1:string} [tipe flash, pesan]
 */
function user_hapus(mysqli $koneksi, int $id, int $id_sendiri): array
{
    if ($id === $id_sendiri) {
        return ['danger', 'Anda tidak bisa menghapus akun yang sedang dipakai.'];
    }

    // Sistem harus selalu menyisakan minimal satu Super Admin
    $cek = $koneksi->prepare('SELECT role FROM admin WHERE id_admin = ?');
    $cek->bind_param('i', $id);
    $cek->execute();
    $row = $cek->get_result()->fetch_assoc();

    if ($row && $row['role'] === 'Super Admin') {
        $jml = (int)$koneksi->query("SELECT COUNT(*) c FROM admin WHERE role='Super Admin'")->fetch_assoc()['c'];
        if ($jml <= 1) {
            return ['danger', 'Minimal harus ada satu Super Admin.'];
        }
    }

    $g = $koneksi->prepare('SELECT foto FROM admin WHERE id_admin = ?');
    $g->bind_param('i', $id);
    $g->execute();
    hapus_gambar($g->get_result()->fetch_assoc()['foto'] ?? null);

    $del = $koneksi->prepare('DELETE FROM admin WHERE id_admin = ?');
    $del->bind_param('i', $id);
    $del->execute();

    return ['success', 'Akun admin dihapus.'];
}

/**
 * Simpan akun admin baru atau perbarui yang ada.
 * @return array{0:string,1:string} [tipe flash, pesan]
 */
function user_simpan(mysqli $koneksi, array $in): array
{
    $daftar_role = daftar_role_admin();

    if ($in['nama'] === '' || $in['user'] === '') {
        return ['danger', 'Nama lengkap dan username wajib diisi.'];
    }
    if ($in['id'] === 0 && strlen($in['pass']) < 4) {
        return ['danger', 'Password minimal 4 karakter.'];
    }
    if (!in_array($in['role'], $daftar_role, true)) {
        return ['danger', 'Role tidak valid.'];
    }

    try {
        // Username harus unik
        $cek = $koneksi->prepare('SELECT id_admin FROM admin WHERE username = ? AND id_admin <> ?');
        $cek->bind_param('si', $in['user'], $in['id']);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            throw new RuntimeException('Username "' . $in['user'] . '" sudah dipakai.');
        }

        $foto = upload_gambar('foto', null);
        $id   = $in['id'];

        /* ---------- Mode edit ---------- */
        if ($id > 0) {
            // Kolom foto hanya ikut di-update kalau ada gambar baru
            $sql = 'UPDATE admin SET nama_lengkap=?, username=?, email=?, role=?, status=?'
                 . ($foto ? ', foto=?' : '') . ' WHERE id_admin=?';

            $st = $koneksi->prepare($sql);
            if ($foto) {
                $st->bind_param('ssssssi', $in['nama'], $in['user'], $in['email'], $in['role'], $in['status'], $foto, $id);
            } else {
                $st->bind_param('sssssi', $in['nama'], $in['user'], $in['email'], $in['role'], $in['status'], $id);
            }

            if ($in['pass'] !== '') {
                $st2 = $koneksi->prepare('UPDATE admin SET password = ? WHERE id_admin = ?');
                $hash = hash_password($in['pass']);
                $st2->bind_param('si', $hash, $id);
                $st2->execute();
            }

            $st->execute();
            return ['success', 'Akun "' . $in['nama'] . '" diperbarui.'];
        }

        /* ---------- Mode tambah baru ---------- */
        $ins = $koneksi->prepare(
            'INSERT INTO admin (nama_lengkap, username, email, password, role, foto, status)
             VALUES (?,?,?,?,?,?,?)'
        );
        $hash = hash_password($in['pass']);
        $ins->bind_param('sssssss', $in['nama'], $in['user'], $in['email'], $hash, $in['role'], $foto, $in['status']);
        $ins->execute();

        return ['success', 'Akun "' . $in['nama'] . '" ditambahkan.'];
    } catch (RuntimeException $ex) {
        return ['danger', $ex->getMessage()];
    }
}
