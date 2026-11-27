<?php
/**
 * Halaman depan (entry point).
 * Aplikasi dibuka langsung ke halaman login.
 */
session_start();
require_once __DIR__ . '/config/fungsi.php';

// Sudah punya session? Arahkan ke dashboard masing-masing.
if (sudah_login()) {
    header('Location: ' . root_url('admin/index.php'));
    exit;
}
if (pelanggan_terlogin()) {
    header('Location: ' . root_url('beranda.php'));
    exit;
}

header('Location: ' . root_url('masuk.php'));
exit;
