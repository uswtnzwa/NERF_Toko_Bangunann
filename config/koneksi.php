<?php
/**
 * Koneksi database ERP_Kel7
 * Ganti kredensial di sini bila berbeda dengan setting XAMPP Anda.
 */
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$name = 'NERF_TokoBangunan';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = new mysqli($host, $user, $pass, $name);
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('<div style="font-family:sans-serif;padding:40px">
        <h2>Koneksi database gagal</h2>
        <p>Tidak bisa terhubung ke database <code>' . $name . '</code>.</p>
        <p>Pastikan MySQL/MariaDB sudah jalan di XAMPP, lalu import file
           <code>NERF_TokoBangunan.sql</code> lewat phpMyAdmin.</p>
        <pre style="background:#f1f4f8;padding:12px">' . htmlspecialchars($e->getMessage()) . '</pre>
    </div>');
}

// Zona waktu agar tanggal tampil sesuai Waktu Indonesia
date_default_timezone_set('Asia/Jakarta');
