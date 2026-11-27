<?php
/**
 * KELOLA KATEGORI - daftar & form tambah/edit.
 *
 *   aksi/kategori.php       -> hapus & simpan
 *   config/kategori-query.php -> daftar kategori untuk tabel
 *   partial/kategori-form.php -> form tambah/edit
 *   partial/kategori-tabel.php -> tabel daftar kategori
 */
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
require_once __DIR__ . '/aksi/kategori.php';
require_once __DIR__ . '/../config/kategori-query.php';

butuh_role(['Super Admin', 'Admin']);

$judul = 'Kelola Kategori';
$edit  = null;

/* ================== HAPUS ================== */
if (isset($_GET['hapus'])) {
    [$tipe, $pesan] = kategori_hapus($koneksi, (int)$_GET['hapus']);
    set_flash($tipe, $pesan);
    header('Location: kategori.php');
    exit;
}

/* ================== SIMPAN ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$tipe, $pesan] = kategori_simpan($koneksi, [
        'id'         => (int)($_POST['id_kategori'] ?? 0),
        'nama'       => trim($_POST['nama_kategori'] ?? ''),
        'deskripsi'  => trim($_POST['deskripsi'] ?? ''),
        'status'     => $_POST['status'] ?? 'Aktif',
    ]);
    set_flash($tipe, $pesan);
    header('Location: kategori.php');
    exit;
}

/* ================== MODE FORM ================== */
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $s = $koneksi->prepare('SELECT * FROM kategori WHERE id_kategori = ?');
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
        <?php include __DIR__ . '/partial/kategori-form.php'; ?>
    </div>
    <div class="col-lg-8">
        <?php include __DIR__ . '/partial/kategori-tabel.php'; ?>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
