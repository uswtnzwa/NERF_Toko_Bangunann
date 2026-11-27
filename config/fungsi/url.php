<?php
/**
 * Fungsi bantu untuk membangun URL (path relatif ke folder aplikasi).
 */

/**
 * Path URL folder APLIKASI (root), bukan folder script.
 * Contoh: "/ERP_Kel7".
 *
 * Penting: file di dalam /admin/ tidak boleh redirect ke "login.php" relatif,
 * karena browser akan mencari /admin/login.php yang tidak ada -> 404.
 * Semua redirect harus pakai root_url().
 */
function base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    // dirname() di Windows memakai "\", jadi normalisasi setelah tiap pemanggilan
    $dir = str_replace('\\', '/', dirname(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/')));
    $dir = ($dir === '/' || $dir === '.') ? '' : rtrim($dir, '/');

    // Kalau script berada di folder admin, naik satu level ke root aplikasi
    if (basename($dir) === 'admin') {
        $dir = str_replace('\\', '/', dirname($dir));
        $dir = ($dir === '/' || $dir === '.') ? '' : rtrim($dir, '/');
    }

    $base = $dir;
    return $base;
}

/** URL ke file di root aplikasi, mis. root_url('login.php') -> "/ERP_Kel7/login.php" */
function root_url(string $file = ''): string
{
    return (base_url() ?: '') . '/' . ltrim($file, '/');
}

/**
 * URL gambar di root aplikasi dengan anti-cache (cache busting).
 * Setiap kali file gambarnya diganti, URL-nya ikut berubah
 * sehingga browser langsung menampilkan gambar baru tanpa hard refresh.
 *
 * Contoh: img_url('TokoLogo.png') -> "/ERP_Kel7/TokoLogo.png?v=1762000000"
 */
function img_url(string $file): string
{
    $path = __DIR__ . '/../../' . ltrim($file, '/');
    $v    = is_file($path) ? filemtime($path) : 0;

    return root_url($file) . '?v=' . $v;
}

/**
 * URL file aset (CSS/JS di folder /assets) dengan anti-cache.
 *
 * Contoh: aset_url('css/admin.css') -> "/ERP_Kel7/assets/css/admin.css?v=1762000000"
 */
function aset_url(string $file): string
{
    $path = __DIR__ . '/../../assets/' . ltrim($file, '/');
    $v    = is_file($path) ? filemtime($path) : 0;

    return root_url('assets/' . ltrim($file, '/')) . '?v=' . $v;
}
