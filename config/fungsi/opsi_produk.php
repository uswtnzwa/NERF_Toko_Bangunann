<?php
/**
 * Opsi produk: daftar pilihan (warna / ukuran) beserta harga & stok sendiri.
 *
 * Tabel `opsi_produk` adalah sumber utama. Kolom teks `warna` / `ukuran` di
 * tabel produk hanya dipakai sebagai cadangan bila produk belum punya baris
 * di opsi_produk, supaya produk lama tetap bisa tampil.
 */

/** Pecah teks daftar dipisah koma menjadi array nilai bersih. */
function pecah_daftar(?string $teks): array
{
    if ($teks === null || trim($teks) === '') {
        return [];
    }
    $hasil = array_map('trim', explode(',', $teks));
    return array_values(array_filter($hasil, static fn($v) => $v !== ''));
}

/** Baca harga & stok yang dikirim form untuk satu kunci opsi. */
function opsi_nilai_kirim(array $harga_kirim, array $stok_kirim, string $kunci): array
{
    $h = trim((string)($harga_kirim[$kunci] ?? ''));
    $h = $h === '' ? null : (float)str_replace(['.', ' '], '', $h);

    $st = trim((string)($stok_kirim[$kunci] ?? ''));
    $st = $st === '' ? null : (int)$st;

    return [$h, $st];
}

/**
 * Bangun query UPDATE satu baris opsi.
 * mysqli tidak punya tipe NULL untuk bind, jadi kolom opsional
 * ditulis sebagai NULL lewat query yang dibangun dinamis.
 */
function opsi_query_update(int $id_opsi, int $urutan, ?float $harga, ?int $stok): array
{
    $sets  = ['urutan = ?'];
    $types = ['i'];
    $vals  = [$urutan];

    if ($harga !== null) {
        $sets[]  = 'harga_opsi = ?';
        $types[] = 'd';
        $vals[]  = $harga;
    } else {
        $sets[] = 'harga_opsi = NULL';
    }

    if ($stok !== null) {
        $sets[]  = 'stok_opsi = ?';
        $types[] = 'i';
        $vals[]  = $stok;
    } else {
        $sets[] = 'stok_opsi = NULL';
    }

    $types[] = 'i';                 // id_opsi untuk WHERE
    $vals[]  = $id_opsi;

    return [
        'sql'   => 'UPDATE opsi_produk SET ' . implode(', ', $sets) . ' WHERE id_opsi = ?',
        'types' => implode('', $types),
        'vals'  => $vals,
    ];
}

/** Bangun query INSERT satu baris opsi baru, kolom opsional disusun dinamis. */
function opsi_query_insert(int $id_produk, string $kelompok, string $nilai, int $urutan, ?float $harga, ?int $stok): array
{
    $kolom = ['id_produk', 'kelompok', 'nilai', 'urutan'];
    $tipe  = ['i', 's', 's', 'i'];
    $isi   = [$id_produk, $kelompok, $nilai, $urutan];

    if ($harga !== null) {
        $kolom[] = 'harga_opsi';
        $tipe[]  = 'd';
        $isi[]   = $harga;
    }
    if ($stok !== null) {
        $kolom[] = 'stok_opsi';
        $tipe[]  = 'i';
        $isi[]   = $stok;
    }

    return [
        'sql'   => 'INSERT INTO opsi_produk (' . implode(', ', $kolom) . ') VALUES ('
            . implode(', ', array_fill(0, count($kolom), '?')) . ')',
        'types' => implode('', $tipe),
        'vals'  => $isi,
    ];
}

/** Ambil peta "kelompok|nilai" => id_opsi dari database. */
function opsi_peta_yang_ada(mysqli $koneksi, int $id_produk): array
{
    $ada = [];
    $s = $koneksi->prepare('SELECT id_opsi, kelompok, nilai FROM opsi_produk WHERE id_produk = ?');
    $s->bind_param('i', $id_produk);
    $s->execute();
    foreach ($s->get_result()->fetch_all(MYSQLI_ASSOC) as $o) {
        $ada[$o['kelompok'] . '|' . $o['nilai']] = (int)$o['id_opsi'];
    }
    return $ada;
}

/**
 * Simpan daftar pilihan produk ke tabel `opsi_produk`, sekaligus menyimpan
 * harga dan stok per pilihan yang dikirim dari form.
 *
 * - Opsi baru (belum ada di DB) akan dibuat. Kalau form menyertakan harga/stok,
 *   langsung diisi; kalau tidak, dibiarkan NULL (= pakai harga/stok produk).
 * - Opsi lama yang dihapus dari form akan dihapus dari DB.
 * - Opsi lama yang tetap ada hanya diperbarui bila form benar-benar mengirim
 *   harga/stok untuk opsi tersebut (dikirim sebagai option_harga[] / option_stok[]
 *   dengan kunci "kelompok:nilai").
 *
 * @param mysqli  $koneksi
 * @param int     $id_produk
 * @param string  $warna   daftar warna dipisah koma (boleh kosong)
 * @param string  $ukuran  daftar ukuran dipisah koma (boleh kosong)
 * @param bool    $hanya_buat_baru  true = jangan ubah/hapus yang sudah ada
 */
function simpan_opsi_produk(mysqli $koneksi, int $id_produk, string $warna, string $ukuran, bool $hanya_buat_baru = false): void
{
    $daftar = [
        'Warna'  => pecah_daftar($warna),
        'Ukuran' => pecah_daftar($ukuran),
    ];

    // Harga & stok per opsi yang dikirim form
    $harga_kirim = $_POST['opsi_harga'] ?? [];
    $stok_kirim  = $_POST['opsi_stok'] ?? [];
    $harga_kirim = is_array($harga_kirim) ? $harga_kirim : [];
    $stok_kirim  = is_array($stok_kirim) ? $stok_kirim : [];

    // Opsi yang sudah ada di database
    $ada = opsi_peta_yang_ada($koneksi, $id_produk);

    foreach ($daftar as $kelompok => $nilai_list) {
        $urutan = 0;
        foreach ($nilai_list as $nilai) {
            $urutan++;
            $kunci = $kelompok . '|' . $nilai;

            if (isset($ada[$kunci])) {
                // Perbarui harga/stok hanya bila form mengirimnya
                if (!$hanya_buat_baru && array_key_exists($kunci, $harga_kirim)) {
                    [$h, $st] = opsi_nilai_kirim($harga_kirim, $stok_kirim, $kunci);
                    $q = opsi_query_update($ada[$kunci], $urutan, $h, $st);

                    $u = $koneksi->prepare($q['sql']);
                    $u->bind_param($q['types'], ...$q['vals']);
                    $u->execute();
                }
                continue;
            }

            // Opsi baru
            [$h, $st] = opsi_nilai_kirim($harga_kirim, $stok_kirim, $kunci);
            $q = opsi_query_insert($id_produk, $kelompok, $nilai, $urutan, $h, $st);

            $ins = $koneksi->prepare($q['sql']);
            $ins->bind_param($q['types'], ...$q['vals']);
            $ins->execute();
        }
    }

    // Hapus opsi yang tidak lagi ada di daftar (kecuali saat hanya membuat baru)
    if (!$hanya_buat_baru) {
        $boleh_dihapus = [];
        foreach ($daftar as $kelompok => $nilai_list) {
            foreach ($nilai_list as $nilai) {
                $boleh_dihapus[] = $kelompok . '|' . $nilai;
            }
        }

        foreach ($ada as $kunci => $id_opsi) {
            if (!in_array($kunci, $boleh_dihapus, true)) {
                $d = $koneksi->prepare('DELETE FROM opsi_produk WHERE id_opsi = ?');
                $d->bind_param('i', $id_opsi);
                $d->execute();
            }
        }
    }
}

/**
 * Ambil baris opsi_produk untuk satu produk, dikelompokkan per kelompok.
 * @return array<string, array<int, array<string,mixed>>>
 */
function ambil_opsi_produk(mysqli $koneksi, int $id_produk): array
{
    $kelompok = ['Warna' => [], 'Ukuran' => []];

    $s = $koneksi->prepare(
        'SELECT id_opsi, kelompok, nilai, harga_opsi, stok_opsi
         FROM opsi_produk WHERE id_produk = ?
         ORDER BY kelompok, urutan, id_opsi'
    );
    $s->bind_param('i', $id_produk);
    $s->execute();

    foreach ($s->get_result()->fetch_all(MYSQLI_ASSOC) as $o) {
        $kelompok[$o['kelompok']][] = $o;
    }

    return $kelompok;
}
