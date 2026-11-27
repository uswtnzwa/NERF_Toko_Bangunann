<?php
/**
 * Logika backend halaman pengaturan.php (akun pelanggan).
 *
 * Tiga bagian:
 *   1. profil     -> nama, email, nomor HP, foto
 *   2. password   -> password lama + password baru
 *   3. notifikasi -> preferensi kirim notifikasi
 *
 * Setiap fungsi mengembalikan array ['sukses' => bool, 'pesan' => string].
 */

/** Daftar preferensi notifikasi: kunci kolom => label. */
const PENGATURAN_NOTIF = [
    'notif_email_pesanan'    => 'Update status pesanan lewat email',
    'notif_wa_pesanan'       => 'Update status pesanan lewat WhatsApp',
    'notif_email_promo'      => 'Promo & penawaran khusus lewat email',
    'notif_email_pembayaran' => 'Pengingat pembayaran lewat email',
];

/** Nilai default bila baris preferensi belum ada. */
const PENGATURAN_NOTIF_DEFAULT = [
    'notif_email_pesanan'    => 1,
    'notif_wa_pesanan'       => 1,
    'notif_email_promo'      => 0,
    'notif_email_pembayaran' => 1,
];

/**
 * Ambil preferensi notifikasi satu pelanggan.
 * Kalau tabel belum ada / baris belum ada, pakai nilai default.
 */
function pengaturan_preferensi(mysqli $db, int $id): array
{
    if (!$db->query("SHOW TABLES LIKE 'preferensi_notifikasi'")->num_rows) {
        return PENGATURAN_NOTIF_DEFAULT;
    }

    $hasil = PENGATURAN_NOTIF_DEFAULT;
    $stmt = $db->prepare('SELECT * FROM preferensi_notifikasi WHERE id_pelanggan = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $baris = $stmt->get_result()->fetch_assoc();

    if ($baris) {
        foreach (PENGATURAN_NOTIF as $kunci => $_) {
            $hasil[$kunci] = (int)$baris[$kunci];
        }
    }
    return $hasil;
}

/** Simpan perubahan profil. */
function pengaturan_simpan_profil(mysqli $db, int $id, array $post): array
{
    $nama = trim($post['nama_lengkap'] ?? '');
    $email = trim($post['email'] ?? '');
    $hp    = trim($post['nomor_hp'] ?? '');

    if ($nama === '' || mb_strlen($nama) > 100) {
        return ['sukses' => false, 'pesan' => 'Nama lengkap wajib diisi (maksimal 100 karakter).'];
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
        return ['sukses' => false, 'pesan' => 'Alamat email tidak valid.'];
    }
    if ($hp !== '' && mb_strlen($hp) > 20) {
        return ['sukses' => false, 'pesan' => 'Nomor HP maksimal 20 karakter.'];
    }

    /* Email tidak boleh dipakai akun lain. */
    $cek = $db->prepare('SELECT id_pelanggan FROM pelanggan WHERE email = ? AND id_pelanggan <> ? LIMIT 1');
    $cek->bind_param('si', $email, $id);
    $cek->execute();
    if ($cek->get_result()->fetch_assoc()) {
        return ['sukses' => false, 'pesan' => 'Email tersebut sudah digunakan akun lain.'];
    }

    /* Foto lama (dipakai kalau tidak ada upload / penghapusan). */
    $q = $db->prepare('SELECT foto FROM pelanggan WHERE id_pelanggan = ? LIMIT 1');
    $q->bind_param('i', $id);
    $q->execute();
    $foto_lama = $q->get_result()->fetch_assoc()['foto'] ?? null;

    $hapus = isset($post['hapus_foto']);
    $foto  = $foto_lama;

    try {
        if ($hapus) {
            hapus_gambar($foto_lama);
            $foto = null;
        } else {
            $foto = upload_gambar('foto', $foto_lama);
        }
    } catch (Throwable $ex) {
        return ['sukses' => false, 'pesan' => $ex->getMessage()];
    }

    $u = $db->prepare(
        'UPDATE pelanggan SET nama_lengkap = ?, email = ?, nomor_hp = ?, foto = ? WHERE id_pelanggan = ?'
    );
    $u->bind_param('ssssi', $nama, $email, $hp, $foto, $id);
    $u->execute();

    /* Session ikut diperbarui supaya navbar langsung menampilkan data baru. */
    $_SESSION['pelanggan_nama']  = $nama;
    $_SESSION['pelanggan_email'] = $email;

    return ['sukses' => true, 'pesan' => 'Profil berhasil disimpan.'];
}

/** Ganti password pelanggan. */
function pengaturan_simpan_password(mysqli $db, int $id, array $post): array
{
    $lama  = $post['password_lama'] ?? '';
    $baru  = $post['password_baru'] ?? '';
    $ulang = $post['password_konfirmasi'] ?? '';

    if ($lama === '' || $baru === '' || $ulang === '') {
        return ['sukses' => false, 'pesan' => 'Semua kolom password wajib diisi.'];
    }
    if ($baru !== $ulang) {
        return ['sukses' => false, 'pesan' => 'Konfirmasi password tidak cocok.'];
    }
    if (mb_strlen($baru) < 6) {
        return ['sukses' => false, 'pesan' => 'Password baru minimal 6 karakter.'];
    }
    if ($baru === $lama) {
        return ['sukses' => false, 'pesan' => 'Password baru harus berbeda dari password lama.'];
    }

    $q = $db->prepare('SELECT password FROM pelanggan WHERE id_pelanggan = ? LIMIT 1');
    $q->bind_param('i', $id);
    $q->execute();
    $tersimpan = $q->get_result()->fetch_assoc()['password'] ?? null;

    if (!$tersimpan || !cek_password($lama, $tersimpan)) {
        return ['sukses' => false, 'pesan' => 'Password lama tidak sesuai.'];
    }

    $hash = hash_password($baru);
    $u = $db->prepare('UPDATE pelanggan SET password = ? WHERE id_pelanggan = ?');
    $u->bind_param('si', $hash, $id);
    $u->execute();

    return ['sukses' => true, 'pesan' => 'Password berhasil diubah. Gunakan password baru saat masuk.'];
}

/** Simpan preferensi notifikasi. */
function pengaturan_simpan_notifikasi(mysqli $db, int $id, array $post): array
{
    if (!$db->query("SHOW TABLES LIKE 'preferensi_notifikasi'")->num_rows) {
        return ['sukses' => false, 'pesan' => 'Tabel preferensi belum tersedia. Import migrations/2026_pengaturan_akun.sql.'];
    }

    $nilai = [];
    $types = '';
    foreach (array_keys(PENGATURAN_NOTIF) as $kunci) {
        $nilai[$kunci] = isset($post[$kunci]) ? 1 : 0;
        $types .= 'i';
    }

    $u = $db->prepare(
        "INSERT INTO preferensi_notifikasi
            (id_pelanggan, notif_email_pesanan, notif_wa_pesanan, notif_email_promo,
             notif_email_pembayaran, diperbarui_pada)
         VALUES (?, ?, ?, ?, ?, NOW())
         ON DUPLICATE KEY UPDATE
            notif_email_pesanan = VALUES(notif_email_pesanan),
            notif_wa_pesanan = VALUES(notif_wa_pesanan),
            notif_email_promo = VALUES(notif_email_promo),
            notif_email_pembayaran = VALUES(notif_email_pembayaran),
            diperbarui_pada = NOW()"
    );
    $u->bind_param(
        'i' . $types,
        $id,
        $nilai['notif_email_pesanan'],
        $nilai['notif_wa_pesanan'],
        $nilai['notif_email_promo'],
        $nilai['notif_email_pembayaran']
    );
    $u->execute();

    return ['sukses' => true, 'pesan' => 'Preferensi notifikasi berhasil disimpan.'];
}
