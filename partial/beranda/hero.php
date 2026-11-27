<?php
/** Blok HERO di beranda.php. Butuh $db_aktif. */
?>
<section class="hero">
    <div class="row align-items-center g-4 position-relative">
        <div class="col-lg-7">
            <span class="badge badge-erp mb-3"><i class="bi bi-shop"></i> NERF Toko Bangunan</span>
            <h1>Bangun Masa Depan<br>Lebih Kuat</h1>
            <p>
                Semua kebutuhan konstruksi Anda dalam satu tempat &mdash; Semen, besi &amp; baja, kayu,
                cat, keramik, hingga perlengkapan pipa dan listrik. Harga jujur, stok terjamin,
                kirim ke seluruh Indonesia.
            </p>
            <div class="d-flex gap-2 flex-wrap mt-4">
                <a href="katalog.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-bag"></i> Belanja Sekarang
                </a>
                <a href="#kategori" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-grid"></i> Lihat Kategori
                </a>
            </div>
            <div class="d-flex gap-4 mt-4 small flex-wrap">
                <span><i class="bi bi-check-circle-fill text-warning"></i> Produk Original</span>
                <span><i class="bi bi-truck text-warning"></i> Kirim Se-Indonesia</span>
                <span><i class="bi bi-shield-check text-warning"></i> Bergaransi</span>
            </div>
        </div>
        <div class="col-lg-5 text-center">
            <div class="bg-white rounded-4 p-3 shadow">
                <div class="ratio ratio-1x1">
                    <img src="<?= img_url('TokoLogo.png') ?>" alt="Logo NERF Toko Bangunan" class="rounded-3"
                         onerror="this.outerHTML='<div class=\"d-flex h-100 align-items-center justify-content-center text-muted\"><div><i class=\"bi bi-hammer\" style=\"font-size:70px\"></i><div class=\"fw-bold mt-2\">NERF</div><div class=\"small\">Toko Bangunan</div></div></div>';">
                </div>
            </div>
        </div>
    </div>
</section>
