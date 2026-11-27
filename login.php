<?php
/**
 * Login admin SUDAH digabung ke dalam masuk.php (pilih tab "Admin").
 * File ini dipertahankan supaya bookmark / tautan lama tetap bekerja.
 */
$qs = $_SERVER['QUERY_STRING'] ?? '';
require_once __DIR__ . '/config/fungsi.php';
header('Location: ' . (base_url() ?: '') . '/masuk.php?role=admin' . ($qs ? "&$qs" : ''));
exit;
