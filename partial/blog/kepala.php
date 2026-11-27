<?php
/**
 * Kepala halaman blog: judul, deskripsi, dan ringkasan jumlah artikel.
 * Butuh $daftar_kategori dari config/artikel-query.php.
 */
$jumlah_artikel = 0;
foreach ($daftar_kategori as $k) {
    $jumlah_artikel += (int)$k['jml'];
}
?>
<nav class="breadcrumb-katalog mt-3">
    <a href="beranda.php">Beranda</a> / <span>Artikel</span>
</nav>

<header class="kepala-blog">
    <span class="label-atas"><i class="bi bi-newspaper"></i> Blog &amp; Berita</span>
    <h1>Wawasan Terbaru Dunia Konstruksi</h1>
    <p>
        Artikel seputar produk material bangunan, tips pengerjaan, dan tren industri
        yang membantu Anda merencanakan proyek dengan lebih cerdas.
    </p>
    <div class="kepala-stat">
        <span><i class="bi bi-file-earmark-text"></i> <?= $jumlah_artikel ?> artikel</span>
        <span><i class="bi bi-tags"></i> <?= count($daftar_kategori) ?> topik</span>
        <span><i class="bi bi-shop"></i> NERF Toko Bangunan</span>
    </div>
</header>
