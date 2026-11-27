<?php
/**
 * Halaman DAFTAR akun pelanggan (toko depan).
 */
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/fungsi.php';

if (pelanggan_terlogin()) {
    header('Location: ' . root_url('beranda.php'));
    exit;
}

$error = '';
$nama = $email = $hp = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $hp    = trim($_POST['nomor_hp'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($nama === '' || $email === '' || $pass === '') {
        $error = 'Nama lengkap, email, dan password wajib diisi.';
    } elseif (mb_strlen($nama) < 3) {
        $error = 'Nama lengkap minimal 3 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($pass !== $pass2) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $cek = $koneksi->prepare('SELECT id_pelanggan FROM pelanggan WHERE email = ?');
        $cek->bind_param('s', $email);
        $cek->execute();

        if ($cek->get_result()->num_rows > 0) {
            $error = 'Email ini sudah terdaftar. Silakan masuk.';
        } else {
            $status = 'Aktif';
            $ins = $koneksi->prepare(
                'INSERT INTO pelanggan (nama_lengkap, email, password, nomor_hp, status) VALUES (?,?,?,?,?)'
            );
            $ins->bind_param('sssss', $nama, $email, hash_password($pass), $hp, $status);

            if ($ins->execute()) {
                pelanggan_login([
                    'id_pelanggan' => $ins->insert_id,
                    'nama_lengkap' => $nama,
                    'email'        => $email,
                ]);
                set_flash('success', 'Selamat datang, ' . $nama . '! Akun Anda berhasil dibuat.');
                header('Location: ' . root_url('beranda.php'));
                exit;
            }
            $error = 'Pendaftaran gagal. Silakan coba lagi.';
        }
    }
}

$flash = ambil_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - NERF Toko Bangunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/auth.css') ?>" rel="stylesheet">
</head>
<body class="auth">
<div>

    <?php include __DIR__ . '/layout/auth-brand.php'; ?>

    <div class="card-otentikasi mx-auto" style="max-width:470px">
        <div class="kepala">
            <div class="logo-bulat">
                <img src="<?= img_url('TokoLogo.png') ?>" alt="Logo" onerror="this.remove()">
            </div>
            <h1>Daftar Akun Baru</h1>
            <p>NERF Toko Bangunan</p>
        </div>

        <div class="badan">
            <?php include __DIR__ . '/partial/auth-alert.php'; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                        <input type="text" name="nama_lengkap" class="form-control" required
                               value="<?= e($nama) ?>" placeholder="Andi Pratama">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" required
                               value="<?= e($email) ?>" placeholder="nama@email.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nomor HP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                        <input type="tel" name="nomor_hp" class="form-control"
                               value="<?= e($hp) ?>" placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Password *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" required
                                   minlength="6" placeholder="Min. 6 karakter">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Ulangi Password *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password2" class="form-control" required
                                   minlength="6" placeholder="Ketik ulang">
                        </div>
                    </div>
                </div>

                <div class="syarat my-3">
                    <i class="bi bi-info-circle"></i> Password minimal 6 karakter.
                    Email dipakai untuk masuk dan melacak pesanan Anda.
                </div>

                <button class="btn btn-aksen w-100 text-white mb-3">
                    <i class="bi bi-person-plus"></i> Daftar Sekarang
                </button>
            </form>

            <div class="daftar-kecil">
                Sudah punya akun? <a href="masuk.php">Masuk di sini</a>
            </div>

            <div class="kaki">
                <a href="beranda.php" class="kembali"><i class="bi bi-arrow-left"></i> Kembali ke toko</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= aset_url('js/toggle-sandi.js') ?>"></script>
</body>
</html>
