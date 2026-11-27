<?php
/**
 * Logika halaman masuk.php.
 *
 * Menghasilkan $error, $identitas, $redirect_dari_form, dan $flash.
 * Alur: coba tabel admin dulu, kalau tidak ada akunnya coba tabel pelanggan.
 */

/** Pesan khusus yang berarti "akun tidak ada di tabel ini", bukan error. */
const COBA_LANJUT = '__coba_pelanggan__';

/* ---------- Sudah punya session? ---------- */

if (sudah_login()) {
    header('Location: ' . root_url('admin/index.php'));
    exit;
}
if (pelanggan_terlogin()) {
    header('Location: ' . root_url('beranda.php'));
    exit;
}

/* ---------- Proses POST ---------- */

$error             = '';
$identitas         = '';
$redirect_dari_form = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $identitas = trim($_POST['identitas'] ?? '');
    $password  = $_POST['password'] ?? '';

    // Buang path aplikasi dari nilai redirect, sisakan path relatif saja
    $redirect_dari_form = trim($_POST['redirect'] ?? '');
    if ($redirect_dari_form === '' || str_contains($redirect_dari_form, '//')
        || str_starts_with($redirect_dari_form, base_url())) {
        $redirect_dari_form = '';
    }

    if ($identitas === '' || $password === '') {
        $error = 'Kolom akun dan password wajib diisi.';
    } else {
        $hasil = cobakan_login_admin($koneksi, $identitas, $password, $redirect_dari_form);

        // Akun tidak ada di tabel admin -> coba sebagai pelanggan
        if (($hasil['error'] ?? '') === COBA_LANJUT) {
            $hasil = cobakan_login_pelanggan($koneksi, $identitas, $password, $redirect_dari_form);
        }

        if (isset($hasil['error'])) {
            $error = $hasil['error'];
        } else {
            header('Location: ' . $hasil['url']);
            exit;
        }
    }
}

/* ---------- Pesan ---------- */

$flash = ambil_flash();

// Pesan perpisahan dikirim lewat URL karena session sudah dihancurkan saat logout
if (!$flash && isset($_GET['keluar'])) {
    $flash = $_GET['keluar'] === 'admin'
        ? ['tipe' => 'success', 'pesan' => 'Anda berhasil keluar dari panel admin.']
        : ['tipe' => 'success', 'pesan' => 'Anda sudah keluar. Terima kasih sudah berbelanja!'];
}

/**
 * Coba login ke tabel admin.
 * @return array{error:string}|array{url:string}
 */
function cobakan_login_admin(mysqli $koneksi, string $identitas, string $password, string $redirect = '')
{
    $stmt = $koneksi->prepare('SELECT * FROM admin WHERE username = ? OR email = ? LIMIT 1');
    $stmt->bind_param('ss', $identitas, $identitas);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if (!$admin) {
        return ['error' => COBA_LANJUT];
    }
    if ($admin['status'] !== 'Aktif') {
        return ['error' => 'Akun ini berstatus Nonaktif. Hubungi Super Admin.'];
    }
    if (!cek_password($password, $admin['password'])) {
        return ['error' => 'Password salah.'];
    }

    session_regenerate_id(true);
    $_SESSION['admin_id']   = (int)$admin['id_admin'];
    $_SESSION['admin_nama'] = $admin['nama_lengkap'];
    $_SESSION['admin_role'] = $admin['role'];

    // Naikkan hash MD5 lama ke bcrypt saat login berhasil
    if (strlen($admin['password']) === 32) {
        $baru = hash_password($password);
        $up = $koneksi->prepare('UPDATE admin SET password = ? WHERE id_admin = ?');
        $up->bind_param('si', $baru, $admin['id_admin']);
        $up->execute();
    }

    return ['url' => $redirect ?: root_url('admin/index.php')];
}

/**
 * Coba login ke tabel pelanggan.
 * @return array{error:string}|array{url:string}
 */
function cobakan_login_pelanggan(mysqli $koneksi, string $identitas, string $password, string $redirect = '')
{
    $stmt = $koneksi->prepare('SELECT * FROM pelanggan WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $identitas);
    $stmt->execute();
    $pelanggan = $stmt->get_result()->fetch_assoc();

    if (!$pelanggan) {
        return ['error' => 'Akun tidak ditemukan. Periksa kembali, atau daftar dulu.'];
    }
    if ($pelanggan['status'] !== 'Aktif') {
        return ['error' => 'Akun Anda berstatus Nonaktif. Hubungi customer service.'];
    }
    if (!cek_password($password, $pelanggan['password'])) {
        return ['error' => 'Password salah.'];
    }

    pelanggan_login($pelanggan);
    set_flash('success', 'Selamat datang kembali, ' . $pelanggan['nama_lengkap'] . '!');

    return ['url' => $redirect ?: root_url('beranda.php')];
}
