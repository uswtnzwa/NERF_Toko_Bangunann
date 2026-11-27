<?php
/**
 * Logika backend untuk halaman testimoni.php (toko depan).
 *
 * Testimoni pelanggan selalu masuk dengan status 'Pending' supaya admin
 * bisa menyaring dulu sebelum tampil di beranda. Nama diambil dari akun
 * pelanggan, bukan dari isian form, supaya tidak bisa dipalsukan.
 */

/** Panjang isi testimoni yang masih masuk akal. */
const TESTIMONI_MIN = 10;
const TESTIMONI_MAX = 500;

/**
 * Proses POST dari form testimoni.
 * Mengembalikan array ['sukses' => bool, 'pesan' => string].
 */
function testimoni_simpan(mysqli $db, int $id_pelanggan, string $nama, array $post): array
{
    $isi    = trim($post['isi'] ?? '');
    $rating = (int)($post['rating'] ?? 0);

    /* ---------- validasi ---------- */
    if ($isi === '') {
        return ['sukses' => false, 'pesan' => 'Isi testimoni belum diisi.'];
    }
    if (mb_strlen($isi) < TESTIMONI_MIN) {
        return ['sukses' => false,
                'pesan' => 'Testimoni minimal ' . TESTIMONI_MIN . ' karakter, sekarang ' . mb_strlen($isi) . '.'];
    }
    if (mb_strlen($isi) > TESTIMONI_MAX) {
        return ['sukses' => false, 'pesan' => 'Testimoni maksimal ' . TESTIMONI_MAX . ' karakter.'];
    }
    if ($rating < 1 || $rating > 5) {
        return ['sukses' => false, 'pesan' => 'Rating belum dipilih.'];
    }

    /* ---------- satu testimoni menunggu moderasi dalam satu waktu ---------- */
    $cek = $db->prepare(
        "SELECT id_testimoni FROM testimoni
         WHERE id_pelanggan = ? AND status IN ('Pending','Tampil')
         LIMIT 1"
    );
    $cek->bind_param('i', $id_pelanggan);
    $cek->execute();
    if ($cek->get_result()->fetch_assoc()) {
        return ['sukses' => false,
                'pesan' => 'Testimoni Anda yang sebelumnya masih menunggu persetujuan admin. '
                         . 'Tunggu sampai ditinjau dulu sebelum mengirim yang baru.'];
    }

    /* ---------- simpan ---------- */
    try {
        $s = $db->prepare(
            "INSERT INTO testimoni (id_pelanggan, nama, isi, rating, status)
             VALUES (?,?,?,?, 'Pending')"
        );
        $s->bind_param('isss', $id_pelanggan, $nama, $isi, $rating);
        $s->execute();
    } catch (Throwable $ex) {
        return ['sukses' => false, 'pesan' => 'Testimoni gagal disimpan: ' . $ex->getMessage()];
    }

    return [
        'sukses' => true,
        'pesan'  => 'Terima kasih! Testimoni Anda menunggu persetujuan admin sebelum tampil di beranda.',
    ];
}

/**
 * Daftar testimoni milik satu pelanggan, terbaru dulu.
 */
function testimoni_saya(mysqli $db, int $id_pelanggan): array
{
    $s = $db->prepare(
        "SELECT id_testimoni, isi, rating, status, dibuat_pada
         FROM testimoni
         WHERE id_pelanggan = ?
         ORDER BY id_testimoni DESC"
    );
    $s->bind_param('i', $id_pelanggan);
    $s->execute();
    return $s->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Daftar testimoni yang sudah disetujui untuk ditampilkan di beranda.
 * Parameter $jumlah dipakai untuk membatasi jumlah kartu.
 */
function testimoni_tampil(mysqli $db, int $jumlah = 3): array
{
    $jumlah = max(1, $jumlah);
    return $db->query(
        "SELECT nama, isi, rating FROM testimoni
         WHERE status = 'Tampil' AND rating >= 4
         ORDER BY id_testimoni DESC LIMIT $jumlah"
    )->fetch_all(MYSQLI_ASSOC);
}