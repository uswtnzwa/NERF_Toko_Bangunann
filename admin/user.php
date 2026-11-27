<?php
/**
 * KELOLA ADMIN - daftar & form tambah/edit akun admin.
 *
 * Query & aksi ada di file terpisah supaya file ini ringkas:
 *   config/user-query.php    -> daftar akun untuk tabel
 *   aksi/user.php            -> hapus & simpar
 *   partial/user-form.php    -> form tambah/edit
 *   partial/user-tabel.php   -> tabel daftar akun
 */
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
require_once __DIR__ . '/aksi/user.php';
require_once __DIR__ . '/../config/user-query.php';

butuh_role(['Super Admin']);

$judul = 'Kelola Admin';
$edit  = null;

/* ================== HAPUS ================== */
if (isset($_GET['hapus'])) {
    [$tipe, $pesan] = user_hapus($koneksi, (int)$_GET['hapus'], (int)$_SESSION['admin_id']);
    set_flash($tipe, $pesan);
    header('Location: user.php');
    exit;
}

/* ================== SIMPAN ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$tipe, $pesan] = user_simpan($koneksi, [
        'id'     => (int)($_POST['id_admin'] ?? 0),
        'nama'   => trim($_POST['nama_lengkap'] ?? ''),
        'user'   => trim($_POST['username'] ?? ''),
        'email'  => trim($_POST['email'] ?? ''),
        'pass'   => $_POST['password'] ?? '',
        'role'   => $_POST['role'] ?? 'Admin',
        'status' => $_POST['status'] ?? 'Aktif',
    ]);
    set_flash($tipe, $pesan);
    header('Location: user.php');
    exit;
}

/* ================== MODE FORM ================== */
// ?edit=<id> -> edit, ?baru=1 -> tambah baru, tanpa keduanya -> hanya tabel
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $s = $koneksi->prepare('SELECT * FROM admin WHERE id_admin = ?');
    $s->bind_param('i', $id_edit);
    $s->execute();
    $edit = $s->get_result()->fetch_assoc() ?: null;
} elseif (isset($_GET['baru'])) {
    $edit = [];
}

include __DIR__ . '/layout/header.php';
?>

<div class="row g-3">
    <div class="col-lg-4">
        <?php include __DIR__ . '/partial/user-form.php'; ?>
    </div>
    <div class="col-lg-8">
        <?php include __DIR__ . '/partial/user-tabel.php'; ?>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
