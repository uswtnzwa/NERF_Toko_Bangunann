<?php
/**
 * Fungsi bantu untuk alur pemesanan (checkout).
 *
 * Dipakai oleh checkout.php dan pesanan-saya.php:
 *   daftar_pengiriman()  -> pilihan cara terima barang
 *   daftar_pembayaran()  -> pilihan cara bayar
 *   ongkir_antar()       -> biaya antar (sama untuk semua alamat)
 *   keranjang_ringkas()  -> isi keranjang + subtotal, siap ditampilkan
 *   simpan_pesanan()     -> membuat pesanan + detail + pembayaran + pengiriman
 *   pengaturan_toko()    -> baca tabel pengaturan (rekening, dll)
 */

/* ============================================================
   PILIHAN PENGIRIMAN & PEMBAYARAN
   ============================================================ */

/** Label yang terbaca manusia untuk sebuah kunci pengiriman. */
function label_pengiriman(string $kunci): string
{
    return daftar_pengiriman()[$kunci]['Label'] ?? 'Ambil di Toko';
}

/**
 * Label yang terbaca manusia untuk sebuah kunci pembayaran.
 *
 * Cari di daftar LENGKAP, bukan daftar_pembayaran() polos: fungsi itu
 * membuang "bayar_di_tempat" karena hanya berlaku untuk ambil di toko,
 * sehingga label COD selalu jatuh ke fallback "Transfer Bank".
 */
function label_pembayaran(string $kunci): string
{
    $semua = daftar_pembayaran(true);
    return $semua[$kunci]['Label'] ?? 'Transfer Bank';
}

/**
 * Cara menerima barang.
 * Ambil di toko  -> ongkir 0, pelanggan datang ke toko.
 * Diantar         -> wajib isi alamat (satu kecamatan, tanpa pilih kota).
 */
function daftar_pengiriman(): array
{
    return [
        'ambil_di_toko' => [
            'Label'   => 'Ambil di Toko',
            'Ikon'    => 'bi-shop',
            'Ongkir'  => 0.0,
            'Keterangan' => 'Datang sendiri ke toko. Tanpa biaya tambahan.',
        ],
        'diantar' => [
            'Label'   => 'Diantar ke Alamat',
            'Ikon'    => 'bi-truck',
            'Ongkir'  => 0.0, // ongkir_antar()
            'Keterangan' => 'Barang diantar oleh kurir ke alamat Anda dalam satu kecamatan. Gratis ongkir.',
        ],
        
    ];
}

/**
 * Metode pembayaran yang Firmen menerima.
 *Bayar di tempat hanya masuk akal untuk ambil di toko.
 */
function daftar_pembayaran(bool $di_toko = false): array
{
    $semua = [
        'bayar_di_tempat' => [
            'Label'   => 'Bayar di Tempat (COD)',
            'Ikon'    => 'bi-cash-coin',
            'Keterangan' => 'Bayar langsung ke kasir saat mengambil barang di toko.',
        ],
        'transfer' => [
            'Label'   => 'Transfer Bank',
            'Ikon'    => 'bi-bank',
            'Keterangan' => 'Transfer ke rekening toko, lalu unggah bukti transfer.',
        ],
        
    ];

    // COD hanya untuk ambil di toko / ambil sendiri, bukan untuk barang diantar
    if (!$di_toko) {
        unset($semua['bayar_di_tempat']);
    }

    return $semua;
}

/* ============================================================
   ONGKIR
   ============================================================ */

/**
 * Biaya antar untuk mode "Diantar".
 *
 * Toko hanya melayani satu kecamatan, jadi pelanggan tidak memilih
 * kota dan biaya antar pun sama untuk semua alamat: Rp 0.
 * Kalau nanti ada tarif antar tetap, cukup ubah nilai baliknya di sini.
 */
function ongkir_antar(): float
{
    return 0.0;
}

/* ============================================================
   RINGKASAN KERANJANG
   ============================================================ */

/**
 * Susun isi keranjang lengkap dengan data produk.
 * Harga memakai harga opsi yang tersimpan saat item ditambahkan,
 * sama seperti tampilan di katalog.php.
 *
 * @return array{barang: array, subtotal: float}
 */
function keranjang_ringkas(mysqli $db): array
{
    $keranjang = $_SESSION['keranjang'] ?? [];
    $barang    = [];
    $subtotal  = 0.0;

    foreach ($keranjang as $kunci => $item) {
        $s = $db->prepare(
            "SELECT id_produk, nama_produk, kode_produk, harga, harga_diskon, stok, satuan
             FROM produk WHERE id_produk = ? LIMIT 1"
        );
        $s->bind_param('i', $item['id_produk']);
        $s->execute();
        $p = $s->get_result()->fetch_assoc();

        // Produk dihapus admin -> lewati, jangan sampai subtotal ngawur
        if (!$p) {
            continue;
        }

        $harga = isset($item['harga']) && $item['harga'] > 0
            ? (float)$item['harga']
            : harga_efektif($p);

        $baris_subtotal = $harga * (int)$item['jumlah'];

        $barang[] = [
            'kunci'       => $kunci,
            'id_produk'   => (int)$item['id_produk'],
            'nama_produk' => $p['nama_produk'],
            'kode_produk' => $p['kode_produk'],
            'satuan'      => $p['satuan'],
            'stok'        => (int)$p['stok'],
            'warna'       => $item['warna'] ?? '',
            'ukuran'      => $item['ukuran'] ?? '',
            'jumlah'      => (int)$item['jumlah'],
            'harga'       => $harga,
            'subtotal'    => $baris_subtotal,
            'melebihi_stok' => (int)$item['jumlah'] > (int)$p['stok'],
        ];

        $subtotal += $baris_subtotal;
    }

    return ['barang' => $barang, 'subtotal' => $subtotal];
}

/* ============================================================
   PENYIMPANAN PESANAN
   ============================================================ */

/** Nomor pesanan unik, contoh: INV-20261009-7F3A1C */
function kode_pesanan_baru(): string
{
    return 'INV-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

/**
 * Simpan pesanan beserta detail, pembayaran, dan pengirimannya.
 * Semua dalam satu transaksi supaya tidak ada pesanan setengah jadi.
 * Stok produk ikut berkurang.
 *
 * @throws RuntimeException bila keranjang kosong / stok kurang
 */
function simpan_pesanan(
    mysqli $db,
    array $ringkas,
    int $id_pelanggan,
    string $metode_pengiriman,
    string $metode_pembayaran,
    array $pengiriman   // nama_penerima, nomor_hp, alamat, kota, catatan
): int {
    if (!$ringkas['barang']) {
        throw new RuntimeException('Keranjang belanja masih kosong.');
    }

    foreach ($ringkas['barang'] as $b) {
        if ($b['melebihi_stok']) {
            throw new RuntimeException(
                'Stok "' . $b['nama_produk'] . '" tidak lagi cukup. '
                . 'Sisa stok ' . $b['stok'] . ' ' . $b['satuan'] . '.'
            );
        }
    }

    $ongkir = (float)($pengiriman['ongkir'] ?? 0);
    $total  = $ringkas['subtotal'] + $ongkir;

    $db->begin_transaction();
    try {
        /* ---------- header pesanan ---------- */
        $s = $db->prepare(
            "INSERT INTO pesanan
                (kode_pesanan, id_pelanggan, id_alamat, nama_penerima, nomor_hp,
                 alamat, kota, catatan, subtotal, ongkir, diskon, total,
                 metode_pembayaran, metode_pengiriman, status_pesanan)
             VALUES (?,?,?,?,?,?,?,?,?,?,0,?,?,?,'Menunggu Pembayaran')"
        );
        $kode    = kode_pesanan_baru();
        $catatan = (string)($pengiriman['catatan'] ?? '');

        // Kolom id_alamat nullable, tapi bind_param tipe 'i' tidak menerima NULL.
        // Pakai 0 sebagai penanda "tidak ada alamat" (tidak ada baris id 0 di tabel).
        $id_alamat = (int)($pengiriman['id_alamat'] ?? 0);

        // Salin ke variabel biasa: bind_param butuh referensi, bukan indeks array.
        // Simpan juga label yang terbaca manusia, bukan kunci internal.
        $id_pelanggan_v = (int)$id_pelanggan;
        $nama_penerima  = (string)($pengiriman['nama_penerima'] ?? '');
        $nomor_hp       = (string)($pengiriman['nomor_hp'] ?? '');
        $alamat_teks    = (string)($pengiriman['alamat'] ?? '');
        $kota_teks      = (string)($pengiriman['kota'] ?? '');
        $subtotal       = (float)$ringkas['subtotal'];
        $label_bayar    = label_pembayaran($metode_pembayaran);
        $label_kirim    = label_pengiriman($metode_pengiriman);

        // Urutan: kode(s), id_pelanggan(i), id_alamat(i), nama(s), hp(s), alamat(s),
        //         kota(s), catatan(s), subtotal(d), ongkir(d), total(d), bayar(s), kirim(s)
        $s->bind_param(
            'siisssssddsss',
            $kode,
            $id_pelanggan_v,
            $id_alamat,
            $nama_penerima,
            $nomor_hp,
            $alamat_teks,
            $kota_teks,
            $catatan,
            $subtotal,
            $ongkir,
            $total,
            $label_bayar,
            $label_kirim
        );
        $s->execute();
        $id_pesanan = (int)$db->insert_id;

        /* ---------- detail barang ---------- */
        $d = $db->prepare(
            "INSERT INTO detail_pesanan (id_pesanan, id_produk, harga, jumlah, subtotal)
             VALUES (?,?,?,?,?)"
        );
        $stok = $db->prepare(
            "INSERT INTO stok (id_produk, jenis, jumlah, keterangan) VALUES (?,'Keluar',?,?)"
        );
        $kurang = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");

        foreach ($ringkas['barang'] as $b) {
            $jumlah      = (int)$b['jumlah'];
            $id_produk   = (int)$b['id_produk'];
            $harga_barang = (float)$b['harga'];
            $subtotal_barang = (float)$b['subtotal'];

            $d->bind_param('iiddi', $id_pesanan, $id_produk, $harga_barang, $jumlah, $subtotal_barang);
            $d->execute();

            // 's' untuk keterangan, bukan 'i' -> kalau 'i' teksnya jadi angka 0
            $keterangan = 'Penjualan ' . $kode;
            $stok->bind_param('iis', $id_produk, $jumlah, $keterangan);
            $stok->execute();

            $kurang->bind_param('ii', $jumlah, $id_produk);
            $kurang->execute();
        }

        /* ---------- pembayaran ---------- */
        $p = $db->prepare(
            "INSERT INTO pembayaran (id_pesanan, metode, jumlah, status) VALUES (?,?,?,'Menunggu')"
        );
        $p->bind_param('isd', $id_pesanan, $label_bayar, $total);
        $p->execute();

        /* ---------- pengiriman (hanya untuk barang diantar) ---------- */
        if ($metode_pengiriman === 'diantar') {
            $k = $db->prepare(
                "INSERT INTO pengiriman (id_pesanan, ongkir, status) VALUES (?,?, 'Menunggu')"
            );
            $k->bind_param('id', $id_pesanan, $ongkir);
            $k->execute();
        }

        $db->commit();

        // Keranjang dikosongkan setelah pesanan tersimpan
        $_SESSION['keranjang'] = [];

        return $id_pesanan;
    } catch (Throwable $ex) {
        $db->rollback();
        throw new RuntimeException('Pesanan gagal disimpan: ' . $ex->getMessage());
    }
}

/* ============================================================
   PENGATURAN TOKO
   ============================================================ */

/** Ambil semua isi tabel pengaturan jadi array asosiatif ['kunci' => 'nilai']. */
function pengaturan_toko(mysqli $db): array
{
    $hasil = [];
    foreach ($db->query("SELECT kunci, nilai FROM pengaturan")->fetch_all(MYSQLI_ASSOC) as $row) {
        $hasil[$row['kunci']] = $row['nilai'];
    }
    return $hasil;
}
