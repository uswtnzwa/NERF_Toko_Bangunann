<?php
/**
 * Logika backend untuk halaman checkout.php.
 *
 * Menangani satu hal: POST aksi=simpan -> menyimpan pesanan ke database.
 * Toko melayani satu kecamatan, jadi tidak ada pemilihan kota maupun
 * ongkir per kota; biaya antar sudah tetap (lihat ongkir_antar()).
 *
 * Semua nilai dari $_POST divalidasi ulang di sini; langkah di form
 * hanya untuk kenyamanan tampilan, bukan penentu keamanan.
 */

/** Nama-metode pengiriman yang sah. */
function kirim_valid(string $kunci): bool
{
    return array_key_exists($kunci, daftar_pengiriman());
}

/** Nama-metode pembayaran yang sah untuk jenis pengiriman tertentu. */
function bayar_valid(string $kunci, bool $di_toko): bool
{
    return array_key_exists($kunci, daftar_pembayaran($di_toko));
}

/**
 * Proses POST dari form checkout.
 * Mengembalikan array ['sukses' => bool, 'id_pesanan' => int, 'pesan' => string].
 */
function checkout_simpan(mysqli $db, int $id_pelanggan, array $ringkas): array
{
    /* ---------- 1. pilihan pengiriman ---------- */
    $kirim = $_POST['metode_pengiriman'] ?? 'ambil_di_toko';
    if (!kirim_valid($kirim)) {
        return ['sukses' => false, 'id_pesanan' => 0, 'pesan' => 'Metode pengiriman tidak valid.'];
    }
    $di_toko = $kirim !== 'diantar';

    /* ---------- 2. alamat (wajib kalau diantar) ---------- */
    $id_alamat_terpilih = (int)($_POST['id_alamat'] ?? 0);
    $alamat             = ['id_alamat' => null, 'nama_penerima' => '', 'nomor_hp' => '', 'alamat' => '', 'kota' => '', 'kode_pos' => '', 'catatan' => '', 'ongkir' => 0.0];

    if (!$di_toko) {
        // Satu kecamatan: tidak ada kolom kota, pelanggan cukup menulis alamat lengkap.
        $wajib = [
            'nama_penerima' => trim($_POST['nama_penerima'] ?? ''),
            'nomor_hp'      => preg_replace('/\s+/', '', $_POST['nomor_hp'] ?? ''),
            'alamat'        => trim($_POST['alamat'] ?? ''),
        ];
        foreach ($wajib as $isi) {
            if ($isi === '') {
                return ['sukses' => false, 'id_pesanan' => 0,
                        'pesan' => 'Alamat pengiriman belum lengkap. Mohon isi nama penerima, nomor HP, dan alamat lengkap.'];
            }
        }
        if (strlen($wajib['nomor_hp']) < 9) {
            return ['sukses' => false, 'id_pesanan' => 0, 'pesan' => 'Nomor HP penerima tidak valid.'];
        }

        $alamat = array_merge($alamat, $wajib, [
            'kota'     => '', // tidak dipakai lagi, tetap disimpan agar kolom pesanan tetap terisi
            'kode_pos' => trim($_POST['kode_pos'] ?? ''),
            'catatan'  => mb_substr(trim($_POST['catatan'] ?? ''), 0, 255),
            'ongkir'   => ongkir_antar(),
        ]);

        // Alamat tersimpan milik pelanggan ini? Kalau ya, pakai id-nya
        if ($id_alamat_terpilih > 0) {
            $s = $db->prepare("SELECT id_alamat FROM alamat_pelanggan WHERE id_alamat = ? AND id_pelanggan = ?");
            $s->bind_param('ii', $id_alamat_terpilih, $id_pelanggan);
            $s->execute();
            if ($row = $s->get_result()->fetch_assoc()) {
                $alamat['id_alamat'] = (int)$row['id_alamat'];
            }
        }
    } elseif ($kirim === 'ambil_di_toko') {
        // Alamat takeaway tetap dicatat supaya pesanan punya kontak
        $pel = pelanggan_saat_ini();
        $alamat['nama_penerima'] = $pel['nama_lengkap'] ?? ($_SESSION['pelanggan_nama'] ?? '');
        $alamat['nomor_hp']      = $pel['nomor_hp'] ?? '';
        $alamat['alamat']        = pengaturan_toko($db)['alamat_toko'] ?? 'Ambil di toko';
        $alamat['kota']          = '';
    }

    /* ---------- 3. metode pembayaran ---------- */
    $bayar = $_POST['metode_pembayaran'] ?? '';
    if (!bayar_valid($bayar, $di_toko)) {
        return ['sukses' => false, 'id_pesanan' => 0, 'pesan' => 'Metode pembayaran tidak valid untuk pilihan pengiriman ini.'];
    }

    /* ---------- 4. simpan ---------- */
    try {
        $id = simpan_pesanan(
            $db,
            $ringkas,
            $id_pelanggan,
            $kirim,
            $bayar,
            $alamat
        );
    } catch (RuntimeException $ex) {
        return ['sukses' => false, 'id_pesanan' => 0, 'pesan' => $ex->getMessage()];
    }

    return [
        'sukses'     => true,
        'id_pesanan' => $id,
        'pesan'      => 'Pesanan berhasil disimpan.',
    ];
}
