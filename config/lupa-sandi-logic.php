<?php
/**
 * Logika halaman lupa-sandi.php.
 *
 * Menghasilkan $langkah2, $flash, dan $email_terakhir.
 *
 * Langkah 2 hanya bisa diakses bila session sudah "dilewati" langkah 1,
 * jadi tidak bisa dipakai untuk mengganti password tanpa tahu email akun.
 */

const KUNCI_LANGKA = 'reset_sandi';
const BATAS_WAKTU  = 900; // 15 menit

// Minta mulai ulang dari langkah 1
if (isset($_GET['ulang'])) {
    unset($_SESSION[KUNCI_LANGKA]);
}

/* ================= LANGKAH 1: cek email ================= */
if (isset($_POST['cek_email'])) {
    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'Format email tidak valid.');
    } else {
        // Hanya mencari di tabel pelanggan
        $stmt = $koneksi->prepare('SELECT id_pelanggan, nama_lengkap, status FROM pelanggan WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $akun = $stmt->get_result()->fetch_assoc();

        if (!$akun) {
            set_flash('danger', 'Email tersebut tidak terdaftar pada akun pelanggan mana pun.');
        } elseif ($akun['status'] !== 'Aktif') {
            set_flash('danger', 'Akun Anda berstatus Nonaktif. Hubungi customer service.');
        } else {
            // Tandai session sudah boleh ganti password untuk akun ini
            $_SESSION[KUNCI_LANGKA] = [
                'id'    => (int)$akun['id_pelanggan'],
                'nama'  => $akun['nama_lengkap'],
                'waktu' => time(),
                'token' => bin2hex(random_bytes(16)),
            ];
            // Cegah form langkah 2 di-refresh ulang berkali-kali
            header('Location: lupa-sandi.php');
            exit;
        }
    }
    header('Location: lupa-sandi.php');
    exit;
}

/* ================= LANGKAH 2: ganti password ================= */
if (isset($_POST['simpan_sandi'])) {
    $sandi   = $_POST['password_baru'] ?? '';
    $ulangi = $_POST['password_ulang'] ?? '';
    $sesi    = $_SESSION[KUNCI_LANGKA] ?? null;

    if (!$sesi) {
        set_flash('danger', 'Sesi kedaluwarsa. Silakan mulai lagi dari input email.');
        header('Location: lupa-sandi.php');
        exit;
    }
    if (time() - $sesi['waktu'] > BATAS_WAKTU) {
        unset($_SESSION[KUNCI_LANGKA]);
        set_flash('danger', 'Waktu habis. Silakan ulangi dari awal.');
        header('Location: lupa-sandi.php');
        exit;
    }
    if (strlen($sandi) < 6) {
        set_flash('danger', 'Password baru minimal 6 karakter.');
    } elseif ($sandi !== $ulangi) {
        set_flash('danger', 'Konfirmasi password tidak sama.');
    } else {
        $up = $koneksi->prepare('UPDATE pelanggan SET password = ? WHERE id_pelanggan = ?');
        $up->bind_param('si', hash_password($sandi), $sesi['id']);
        $up->execute();

        unset($_SESSION[KUNCI_LANGKA]);
        set_flash('success', 'Password berhasil diubah. Silakan masuk dengan password baru.');
        header('Location: masuk.php');
        exit;
    }
    header('Location: lupa-sandi.php');
    exit;
}

/* ================= status tampilan ================= */

// Kalau masih ada proses langkah 1 yang belum selesai, tampilkan form password
$langkah2      = isset($_SESSION[KUNCI_LANGKA])
                && (time() - $_SESSION[KUNCI_LANGKA]['waktu'] <= BATAS_WAKTU);
$flash         = ambil_flash();
$email_terakhir = $_POST['email'] ?? '';
