<?php
/**
 * Pencatatan kunjungan otomatis untuk laporan analitik.
 *
 * Dipanggil sekali dari config/fungsi.php. Setiap halaman toko depan yang
 * dimuat akan menyimpan satu baris ke tabel `kunjungan`.
 *
 * Sumber ditentukan berurutan dari:
 *   1. parameter ?utm_source=... di URL
 *   2. HTTP_REFERER (google / instagram / facebook / whatsapp / tiktok)
 *   3. sisanya dianggap "Langsung"
 *
 * Pengunjung unik dikenali lewat cookie erp_kunjungan_id (30 hari), bukan IP
 * karena IP bisa bersama atau berubah.
 *
 * Kegagalan menulis ke database tidak boleh membuat halaman error.
 */

/** ID pengunjung yang stabil antar halaman dan sesi login. */
function kunjungan_id_pengunjung(): string
{
    $kunci = 'erp_kunjungan_id';
    $nilai = $_COOKIE[$kunci] ?? '';

    if (!preg_match('/^[a-f0-9]{32}$/', $nilai)) {
        $nilai = bin2hex(random_bytes(16));
    }
    if (!isset($_COOKIE[$kunci])) {
        setcookie($kunci, $nilai, [
            'expires'  => time() + 60 * 60 * 24 * 30,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[$kunci] = $nilai;
    }

    return 'sess_' . $nilai;
}

/** Halaman yang dibuka, tanpa parameter utm/PHPSESSID, maksimal 150 karakter. */
function kunjungan_halaman(): string
{
    $uri   = (string)($_SERVER['REQUEST_URI'] ?? '/');
    $path  = parse_url($uri, PHP_URL_PATH) ?: '/';
    $query = (string)(parse_url($uri, PHP_URL_QUERY) ?? '');

    // Akses bisa lewat vhost (/beranda.php) atau /ERP_Kel7/beranda.php.
    // Samakan keduanya supaya kelompok "Halaman Teratas" tetap cocok.
    $base = function_exists('base_url') ? base_url() : '';
    if ($base !== '') {
        if ($path === $base || $path === $base . '/') {
            $path = '/';
        } elseif (str_starts_with($path, $base . '/')) {
            $path = substr($path, strlen($base));
        }
    }
    if ($path === '') {
        $path = '/';
    }
    if ($path === '/') {
        $path = '/beranda.php';
    }

    if ($query !== '') {
        parse_str($query, $params);
        unset($params['PHPSESSID']);
        foreach (array_keys($params) as $k) {
            if (str_starts_with((string)$k, 'utm_')) {
                unset($params[$k]);
            }
        }
        $query = http_build_query($params);
    }

    $halaman = $path . ($query !== '' ? '?' . $query : '');
    if ($halaman === '' || $halaman[0] !== '/') {
        $halaman = '/' . $halaman;
    }

    return mb_substr($halaman, 0, 150);
}

/** Label sumber kunjungan: utm_source -> referer -> Langsung. */
function kunjungan_sumber(): string
{
    $peta = [
        'google'    => 'Google',
        'instagram' => 'Instagram',
        'facebook'  => 'Facebook',
        'whatsapp'  => 'WhatsApp',
        'wa'        => 'WhatsApp',
        'tiktok'    => 'TikTok',
        'referral'  => 'Referral',
    ];

    $utm = strtolower(trim((string)($_GET['utm_source'] ?? '')));
    if ($utm !== '') {
        $utm = preg_replace('/[^a-z0-9 ._-]+/', '', $utm);
        return $peta[$utm] ?? ucfirst(mb_substr($utm, 0, 50));
    }

    $referer = trim((string)($_SERVER['HTTP_REFERER'] ?? ''));
    if ($referer === '') {
        return 'Langsung';
    }

    $host = strtolower((string)parse_url($referer, PHP_URL_HOST));
    if ($host === '') {
        return 'Langsung';
    }

    $host_sendiri = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    if ($host === $host_sendiri
        || ($host_sendiri !== '' && str_ends_with($host, '.' . $host_sendiri))
        || ($host !== '' && str_ends_with($host_sendiri, '.' . $host))) {
        return 'Langsung'; // klik dari dalam situs sendiri
    }

    if (str_contains($host, 'wa.me')) {
        return 'WhatsApp';
    }
    foreach ($peta as $kata => $label) {
        if (str_contains($host, $kata)) {
            return $label;
        }
    }

    return 'Referral';
}

/** Simpan satu kunjungan. Aman dipanggil berulang dalam satu request. */
function catat_kunjungan(): void
{
    static $sudah = false;
    if ($sudah) {
        return;
    }
    $sudah = true;

    if (PHP_SAPI === 'cli' || ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return;
    }
    if (headers_sent()) {
        return;
    }

    // Pakai SCRIPT_NAME untuk filter: REQUEST_URI bisa berupa "/ERP_Kel7/"
    // sehingga basename()-nya bukan nama file yang sebenarnya dijalankan.
    $script = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    if (preg_match('#/admin(/|$)#', $script)) {
        return; // aktivitas panel admin bukan pengunjung toko
    }
    if (in_array(basename($script), ['index.php', 'login.php', 'keluar.php'], true)) {
        return; // halaman redirect murni
    }
    if (preg_match('/\.(css|js|mjs|map|png|jpe?g|gif|svg|ico|webp|json|xml)$/i', $script)) {
        return;
    }

    $db = db_opsional();
    if (!$db) {
        return;
    }

    $waktu   = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');
    $halaman = kunjungan_halaman();
    $sumber  = kunjungan_sumber();
    $ip      = (string)($_SERVER['REMOTE_ADDR'] ?? '');
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = '';
    }
    $sid = kunjungan_id_pengunjung();

    try {
        $stmt = $db->prepare(
            'INSERT INTO kunjungan (tanggal, halaman, sumber, ip, session_id)
             VALUES (?, ?, ?, ?, ?)'
        );
        if ($stmt === false) {
            return;
        }
        $stmt->bind_param('sssss', $waktu, $halaman, $sumber, $ip, $sid);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $e) {
        // Analitik boleh gagal tanpa membuat halaman error.
    }
}
