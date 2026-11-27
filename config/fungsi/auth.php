<?php
/**
 * Autentikasi admin (panel) & pelanggan (toko depan).
 */

/* ---------- Autentikasi admin ---------- */

function sudah_login(): bool
{
    return isset($_SESSION['admin_id']);
}

function wajib_login(): void
{
    if (!sudah_login()) {
        // absolut: dari /admin/ harus tetap ke /ERP_Kel7/masuk.php
        header('Location: ' . root_url('masuk.php'));
        exit;
    }
}

function admin_saat_ini(): ?array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    if (!sudah_login()) {
        return null;
    }
    global $koneksi;
    $stmt = $koneksi->prepare('SELECT * FROM admin WHERE id_admin = ? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['admin_id']);
    $stmt->execute();
    $cache = $stmt->get_result()->fetch_assoc() ?: null;
    return $cache;
}

/** Izinkan akses hanya untuk role admin tertentu. */
function butuh_role(array $roles): void
{
    wajib_login();
    $admin = admin_saat_ini();
    if (!$admin || !in_array($admin['role'], $roles, true)) {
        set_flash('danger', 'Anda tidak punya akses ke halaman tersebut.');
        header('Location: ' . root_url('admin/index.php'));
        exit;
    }
}

/* ---------- Autentikasi pelanggan (toko depan) ---------- */

function pelanggan_login(array $data): void
{
    session_regenerate_id(true);
    $_SESSION['pelanggan_id']   = (int)$data['id_pelanggan'];
    $_SESSION['pelanggan_nama'] = $data['nama_lengkap'];
    $_SESSION['pelanggan_email'] = $data['email'];
}

function pelanggan_terlogin(): bool
{
    return isset($_SESSION['pelanggan_id']);
}

function pelanggan_saat_ini(): ?array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    if (!pelanggan_terlogin()) {
        return null;
    }
    global $koneksi;
    $stmt = $koneksi->prepare('SELECT * FROM pelanggan WHERE id_pelanggan = ? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['pelanggan_id']);
    $stmt->execute();
    $cache = $stmt->get_result()->fetch_assoc() ?: null;
    return $cache;
}

function wajib_pelanggan(): void
{
    if (!pelanggan_terlogin()) {
        set_flash('danger', 'Silakan masuk terlebih dahulu.');
        header('Location: ' . root_url('masuk.php'));
        exit;
    }
}

function pelanggan_keluar(): void
{
    unset($_SESSION['pelanggan_id'], $_SESSION['pelanggan_nama'], $_SESSION['pelanggan_email']);
}
