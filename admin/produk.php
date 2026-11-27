<?php
/**
 * KELOLA PRODUK - daftar, tambah, edit, hapus.
 *
 * Query & aksi ada di file terpisah supaya file ini ringkas:
 *   config/produk-query.php -> filter, paginasi, data untuk tabel
 *   aksi/produk.php        -> hapus & simpan
 *   partial/produk-form.php -> form tambah/edit
 *   partial/produk-tabel.php -> tabel daftar produk
 */
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
require_once __DIR__ . '/aksi/produk.php';
require_once __DIR__ . '/../config/produk-query.php';

butuh_role(['Super Admin', 'Admin', 'Warehouse']);

$judul = 'Kelola Produk';
$error = '';
$edit  = null;

/* ================== HAPUS ================== */
if (isset($_GET['hapus'])) {
    [$tipe, $pesan] = produk_hapus($koneksi, (int)$_GET['hapus'], isset($_GET['aksa']));
    set_flash($tipe, $pesan);
    header('Location: produk.php');
    exit;
}

/* ================== MODE FORM ================== */
// ?edit=<id> -> edit, ?edit=0 -> tambah baru, tanpa ?edit -> hanya daftar
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];

    if ($id_edit > 0) {
        $s = $koneksi->prepare('SELECT * FROM produk WHERE id_produk = ?');
        $s->bind_param('i', $id_edit);
        $s->execute();
        $edit = $s->get_result()->fetch_assoc();
        if (!$edit) {
            set_flash('danger', 'Produk yang akan diedit tidak ditemukan.');
            header('Location: produk.php');
            exit;
        }
    } else {
        $edit = []; // mode tambah baru
    }
}

/* ================== SIMPAN (tambah / edit) ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = produk_ambil_input();

    if ($error = produk_validasi($in)) {
        // populate ulang form dengan nilai yang tadi dikirim
        $edit = [
            'id_produk' => $in['id'], 'id_kategori' => $in['kategori'], 'kode_produk' => $in['kode'],
            'nama_produk' => $in['nama'], 'deskripsi' => $in['deskripsi'], 'spesifikasi' => $in['spesifikasi'],
            'harga' => $in['harga'], 'harga_diskon' => $in['harga_diskon'], 'stok' => $in['stok'],
            'stok_minimum' => $in['stok_min'], 'satuan' => $in['satuan'],
            'warna' => $in['warna'], 'ukuran' => $in['ukuran'],
            'berat' => $in['berat'], 'status' => $in['status'],
            'gambar' => $edit['gambar'] ?? null,
        ];
    } else {
        try {
            [$error, $sukses] = produk_simpan($koneksi, $in, $edit['gambar'] ?? null);
            if (!$error) {
                set_flash('success', $sukses);
                header('Location: produk.php');
                exit;
            }
            // gagal (mis. kode duplikat) ->populate ulang form
            $edit = [
                'id_produk' => $in['id'], 'id_kategori' => $in['kategori'], 'kode_produk' => $in['kode'],
                'nama_produk' => $in['nama'], 'deskripsi' => $in['deskripsi'], 'spesifikasi' => $in['spesifikasi'],
                'harga' => $in['harga'], 'harga_diskon' => $in['harga_diskon'], 'stok' => $in['stok'],
                'stok_minimum' => $in['stok_min'], 'satuan' => $in['satuan'],
                'warna' => $in['warna'], 'ukuran' => $in['ukuran'],
                'berat' => $in['berat'], 'status' => $in['status'],
                'gambar' => $edit['gambar'] ?? null,
            ];
        } catch (Throwable $ex) {
            $error = $ex->getMessage();
        }
    }
}

include __DIR__ . '/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h6 class="fw-bold mb-0">Daftar Produk</h6>
        <small class="text-muted"><?= $total ?> produk ditemukan</small>
    </div>
    <a href="produk.php?edit=0" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if ($edit !== null): ?>
    <?php include __DIR__ . '/partial/produk-form.php'; ?>
<?php endif; ?>

<?php include __DIR__ . '/partial/produk-tabel.php'; ?>

<?php include __DIR__ . '/layout/footer.php'; ?>
