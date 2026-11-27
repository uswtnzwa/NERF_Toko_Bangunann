<?php
/**
 * Keranjang belanja disimpan di session.
 *
 * Setiap baris keranjang menyimpan warna/ukuran pilihan bila produknya punya
 * daftar pilihan, sehingga pelanggan bisa memesan 2 warna atau 2 ukuran
 * berbeda dari produk yang sama.
 */

/** Tambah satu baris ke keranjang, atau tambah jumlahnya bila baris sama. */
function keranjang_tambah(int $id_produk, int $jumlah, string $warna, string $ukuran, float $harga): void
{
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }
    // Baris dibedakan oleh produk + warna + ukuran
    $kunci = $id_produk . '|' . strtolower($warna) . '|' . strtolower($ukuran);

    if (isset($_SESSION['keranjang'][$kunci])) {
        $_SESSION['keranjang'][$kunci]['jumlah'] += $jumlah;
        // Harga terbaru ikut diperbarui bila admin mengubah harga opsi
        $_SESSION['keranjang'][$kunci]['harga'] = $harga;
    } else {
        $_SESSION['keranjang'][$kunci] = [
            'id_produk' => $id_produk,
            'jumlah'    => $jumlah,
            'warna'     => $warna,
            'ukuran'    => $ukuran,
            'harga'     => $harga, // harga saat dipilih, ikut disimpan di keranjang
        ];
    }
}

/** Total jumlah barang di keranjang (untuk badge di navbar). */
function keranjang_total_qty(): int
{
    $keranjang = $_SESSION['keranjang'] ?? [];
    return (int)array_sum(array_column($keranjang, 'jumlah'));
}

/**
 * Daftar pilihan (warna/ukuran) untuk satu produk, lengkap dengan harga & stok.
 *
 * Tabel opsi_produk adalah sumber utama. Kolom teks `warna` / `ukuran` di
 * tabel produk hanya dipakai sebagai cadangan bila produk belum punya baris
 * di opsi_produk, supaya produk lama tetap bisa tampil.
 *
 * @return array{0: array<int,array>, 1: array<int,array>} daftar warna & ukuran
 */
function opsi_pilihan_produk(mysqli $db, int $id_produk, array $produk): array
{
    $kelompok = ['Warna' => [], 'Ukuran' => []];

    $s = $db->prepare(
        "SELECT kelompok, nilai, harga_opsi, stok_opsi
         FROM opsi_produk
         WHERE id_produk = ? AND kelompok IN ('Warna','Ukuran')
         ORDER BY kelompok, urutan, id_opsi"
    );
    $s->bind_param('i', $id_produk);
    $s->execute();

    $harga_produk = harga_efektif($produk);

    foreach ($s->get_result()->fetch_all(MYSQLI_ASSOC) as $o) {
        $kelompok[$o['kelompok']][] = [
            'nilai' => $o['nilai'],
            // Harga NULL berarti pakai harga produk, stok NULL berarti pakai stok produk
            'harga' => $o['harga_opsi'] !== null ? (float)$o['harga_opsi'] : $harga_produk,
            'stok'  => $o['stok_opsi'] !== null ? (int)$o['stok_opsi'] : null,
        ];
    }

    // Cadangan: bila tabel opsi kosong, pakai kolom teks produk
    foreach (['Warna' => 'warna', 'Ukuran' => 'ukuran'] as $kel => $kolom) {
        if ($kelompok[$kel]) {
            continue;
        }
        foreach (opsi_tersedia($produk[$kolom] ?? null) as $nilai) {
            $kelompok[$kel][] = ['nilai' => $nilai, 'harga' => $harga_produk, 'stok' => null];
        }
    }

    return [$kelompok['Warna'], $kelompok['Ukuran']];
}

/**
 * Stok & harga efektif untuk kombinasi warna + ukuran terpilih.
 * Stok = min(stok produk, stok opsi), harga = harga opsi terpilih.
 */
function harga_stok_terpilih(array $opsi, int $stok_produk, string $warna, string $ukuran, float $harga_default): array
{
    $stok_efektif  = $stok_produk;
    $harga_efektif = $harga_default;

    foreach ($opsi as $daftar) {
        foreach ($daftar as $o) {
            if ($o['nilai'] !== $warna && $o['nilai'] !== $ukuran) {
                continue;
            }
            if ($o['stok'] !== null) {
                $stok_efektif = min($stok_efektif, $o['stok']);
            }
            $harga_efektif = $o['harga'];
        }
    }

    return [$harga_efektif, $stok_efektif];
}

/** Jalankan aksi keranjang dari POST, lalu redirect. */
function keranjang_aksi(mysqli $db): void
{
    $aksi = $_POST['aksi'] ?? '';

    // "tambah" = masukkan ke keranjang, "beli_sekarang" = masukkan lalu langsung checkout
    if ($aksi === 'tambah' || $aksi === 'beli_sekarang') {
        $berhasilDitambah = false;
        // Checkout butuh pelanggan yang sudah masuk
        if ($aksi === 'beli_sekarang' && !pelanggan_terlogin()) {
            set_flash('info', 'Silakan masuk terlebih dahulu untuk melanjutkan pemesanan.');
            header('Location: ' . root_url('masuk.php'));
            exit;
        }

        $id           = (int)($_POST['id_produk'] ?? 0);
        $jumlah       = max(1, (int)($_POST['jumlah'] ?? 1));
        $pilih_warna  = trim($_POST['warna'] ?? '');
        $pilih_ukuran = trim($_POST['ukuran'] ?? '');

        $s = $db->prepare("SELECT * FROM produk WHERE id_produk = ? AND status='Aktif'");
        $s->bind_param('i', $id);
        $s->execute();
        $produk = $s->get_result()->fetch_assoc();

        if (!$produk) {
            set_flash('danger', 'Produk tidak ditemukan.');
        } else {
            [$opsi_warna, $opsi_ukuran] = opsi_pilihan_produk($db, $id, $produk);

            // Pastikan pilihan yang dikirim memang ada di daftar
            $warna  = in_array($pilih_warna, array_column($opsi_warna, 'nilai'), true)  ? $pilih_warna  : '';
            $ukuran = in_array($pilih_ukuran, array_column($opsi_ukuran, 'nilai'), true) ? $pilih_ukuran : '';

            if ($opsi_warna && $warna === '') {
                set_flash('danger', 'Silakan pilih warna yang tersedia.');
            } elseif ($opsi_ukuran && $ukuran === '') {
                set_flash('danger', 'Silakan pilih ukuran yang tersedia.');
            } else {
                [$harga, $stok] = harga_stok_terpilih(
                    [$opsi_warna, $opsi_ukuran],
                    (int)$produk['stok'],
                    $warna,
                    $ukuran,
                    harga_efektif($produk)
                );

                if ($jumlah > $stok) {
                    set_flash('danger', 'Jumlah melebihi stok tersedia (' . $stok . ').');
                } else {
                    keranjang_tambah($id, $jumlah, $warna, $ukuran, $harga);
                    $catatan = array_filter([$warna, $ukuran]);
                    set_flash('success', 'Produk "' . $produk['nama_produk']
                        . ($catatan ? ' (' . implode(', ', $catatan) . ')' : '')
                        . '" ditambahkan ke keranjang.');
                    $berhasilDitambah = true;
                }
            }
        }

        // "Beli Sekarang": masukkan ke keranjang lalu langsung ke halaman checkout.
        // Kalau gagal (produk tidak ada / warna tidak dipilih / stok kurang),
        // tetap di halaman produk supaya pesan errornya kelihatan.
        if ($aksi === 'beli_sekarang' && $berhasilDitambah) {
            header('Location: ' . root_url('checkout.php'));
        } else {
            header('Location: katalog.php?detail=' . $id);
        }
        exit;
    }

    if ($aksi === 'hapus') {
        unset($_SESSION['keranjang'][$_POST['kunci'] ?? '']);
        set_flash('success', 'Item keranjang dihapus.');
        header('Location: katalog.php');
        exit;
    }

    if ($aksi === 'kosongkan') {
        $_SESSION['keranjang'] = [];
        set_flash('success', 'Keranjang dikosongkan.');
        header('Location: katalog.php');
        exit;
    }
}
