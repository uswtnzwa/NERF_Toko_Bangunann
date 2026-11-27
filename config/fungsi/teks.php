<?php
/**
 * Fungsi bantu untuk teks & format angka.
 */

/** Escape output HTML. */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Format angka jadi Rupiah, mis. 150000 -> "Rp 150.000". */
function rupiah($angka): string
{
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/** Bersihkan teks menjadi slug URL, mis. "Semen 50 Kg" -> "semen-50-kg". */
function slugify(string $teks): string
{
    $teks = strtolower(trim($teks));
    $teks = preg_replace('/[^a-z0-9]+/', '-', $teks);
    return trim($teks, '-') ?: 'produk';
}

/** Inisial untuk avatar, mis. "Andi Pratama" -> "AP". */
function initials(string $nama): string
{
    $potongan = preg_split('/\s+/', trim($nama));
    $a = mb_substr($potongan[0] ?? '', 0, 1);
    $b = count($potongan) > 1 ? mb_substr(end($potongan), 0, 1) : '';
    return mb_strtoupper($a . $b);
}

/**
 * Harga yang benar-benar dibayar pelanggan.
 * Pakai harga_diskon bila ada, selain itu harga normal.
 */
function harga_efektif(array $p): float
{
    return (float)$p['harga_diskon'] > 0 ? (float)$p['harga_diskon'] : (float)$p['harga'];
}

/** Pecah kolomberisi daftar (dipisah koma) menjadi array pilihan yang rapi. */
function opsi_tersedia(?string $teks): array
{
    if ($teks === null || trim($teks) === '') {
        return [];
    }
    $hasil = array_map('trim', explode(',', $teks));
    return array_values(array_filter($hasil, static fn($v) => $v !== ''));
}
