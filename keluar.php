<?php
/**
 * Keluar / Logout untuk Admin maupun Pelanggan.
 * Membersihkan seluruh session, lalu kembali ke halaman login (masuk.php).
 */
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/fungsi.php';

$base = base_url() ?: '';

// Simpan nama yang sedang masuk untuk pesan perpisahan
$who = 'admin';
if (sudah_login()) {
    $admin = admin_saat_ini();
    $who = $admin['nama_lengkap'] ?? 'admin';
} elseif (pelanggan_terlogin()) {
    $who = $_SESSION['pelanggan_nama'] ?? 'pelanggan';
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// Session baru khusus untuk menampilkan pesan perpisahan
session_start();
set_flash('success', 'Sampai jumpa, ' . $who . '. Anda berhasil keluar.');

header('Location: ' . $base . '/masuk.php');
exit;
