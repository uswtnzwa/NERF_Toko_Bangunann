<?php
/**
 * Koneksi database opsional untuk halaman publik.
 *
 * Halaman depan tidak boleh fatal jika database belum siap,
 * jadi mengembalikan null dan halaman bisa menampilkan fallback.
 */

function db_opsional()
{
    static $conn = null;
    static $sudah_coba = false;

    if ($sudah_coba) {
        return $conn;
    }
    $sudah_coba = true;

    $host = '127.0.0.1';
    $user = 'root';
    $pass = '';
    $name = 'NERF_TokoBangunan';

    mysqli_report(MYSQLI_REPORT_OFF);
    try {
        $c = new mysqli($host, $user, $pass, $name);
        $c->set_charset('utf8mb4');
        $conn = $c;
    } catch (Throwable $e) {
        $conn = null;
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    return $conn;
}
