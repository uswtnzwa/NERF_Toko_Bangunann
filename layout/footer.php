<?php
/**
 * Footer toko depan - dipakai beranda.php dan katalog.php.
 *
 * Variabel opsional sebelum include:
 *   $footer_kategori -> array kategori untuk kolom "Kategori"
 */

$footer_kategori = $footer_kategori ?? [];
?>
<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="logo-box">
                        <img src="<?= img_url('TokoLogo.png') ?>" alt="" onerror="this.remove()">
                    </div>
                    <strong class="text-white">NERF Toko Bangunan</strong>
                </div>
                <p class="small mb-0" style="max-width:320px">
                    Toko material bangunan lengkap untuk kebutuhan rumah, renovasi,
                    dan proyek konstruksi Anda.
                </p>
            </div>

            <div class="col-6 col-md-2">
                <h6>Menu</h6>
                <a href="beranda.php">Beranda</a>
                <a href="katalog.php">Katalog</a>
                <a href="blog.php">Artikel</a>
                <a href="beranda.php#kategori">Kategori</a>
                <a href="beranda.php#faq">FAQ</a>
                <a href="customer-service.php">Customer Service</a>
                <a href="syarat-ketentuan.php">Syarat &amp; Ketentuan</a>
                <a href="kebijakan-privasi.php">Kebijakan Privasi</a>
            </div>

            <div class="col-6 col-md-3">
                <h6>Kategori</h6>
                <?php foreach (array_slice($footer_kategori, 0, 5) as $k): ?>
                    <a href="katalog.php?kategori=<?= (int)$k['id_kategori'] ?>"><?= e($k['nama_kategori']) ?></a>
                <?php endforeach; ?>
            </div>

            <div class="col-md-3">
                <h6>Kontak</h6>
                <div class="small">
                    <div class="mb-2"><i class="bi bi-geo-alt"></i> Jl.Condet, Jakarta</div>
                    <div class="mb-2"><i class="bi bi-telephone"></i> 0812-3456-7890</div>
                    <div class="mb-2"><i class="bi bi-envelope"></i> cs@tokobangunan.com</div>
                    <div><i class="bi bi-clock"></i> Senin &ndash; Sabtu, 08.00&ndash;17.00</div>
                </div>
            </div>
        </div>

        <div class="bawah d-flex justify-content-between flex-wrap gap-2">
            <small>&copy; <?= date('Y') ?> NERF Toko Bangunan &mdash; ERP_Kel5</small>
            <small>Semua produk bergaransi resmi</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
