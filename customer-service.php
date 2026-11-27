<?php
/**
 * CUSTOMER SERVICE - halaman pelanggan untuk menghubungi toko via WhatsApp.
 *
 * Link WA diambil dari config/customer-service.php supaya bisa diganti
 * di satu tempat saja (tanpa menyentuh file halaman).
 *
 * - Klik menu "Customer Service" -> halaman ini.
 * - Jika link sudah diisi, halaman otomatis mengalihkan ke WhatsApp.
 * - Tambahkan ?preview=1 untuk membuka halaman tanpa alihkan otomatis.
 */
session_start();
require_once __DIR__ . '/config/fungsi.php';
require_once __DIR__ . '/config/customer-service.php';

$menu_aktif       = 'customer-service';
$jumlah_keranjang = function_exists('keranjang_total_qty') ? keranjang_total_qty() : 0;

// Kategori untuk footer (tetap aman walau database belum siap).
$footer_kategori = [];
$db = db_opsional();
if ($db instanceof mysqli) {
    $footer_kategori = $db->query(
        "SELECT id_kategori, nama_kategori FROM kategori WHERE status='Aktif' ORDER BY nama_kategori"
    )->fetch_all(MYSQLI_ASSOC);
}

/* ================= susun URL WhatsApp ================= */

$cs_link_terisi = is_string($cs_wa_link) && preg_match('#^https?://#i', trim($cs_wa_link)) === 1;

// Pesan bisa dikirim lewat URL: ?pesan=... (misalnya dari link produk/pesanan)
$cs_pesan = trim((string)($_GET['pesan'] ?? ''));
$cs_pesan = $cs_pesan !== '' ? mb_substr($cs_pesan, 0, 500) : (string)$cs_wa_pesan;

$cs_url = '';
if ($cs_link_terisi) {
    $cs_url = trim($cs_wa_link);

    // Link undangan grup (chat.whatsapp.com) dipakai apa adanya.
    // Parameter text= hanya didukung link chat perorangan (wa.me / api.whatsapp.com).
    $cs_bisa_pesan = stripos($cs_url, 'wa.me/') !== false
                  || stripos($cs_url, 'api.whatsapp.com') !== false;

    if ($cs_bisa_pesan && $cs_pesan !== '' && stripos($cs_url, 'text=') === false) {
        $cs_url .= (str_contains($cs_url, '?') ? '&' : '?') . 'text=' . rawurlencode($cs_pesan);
    }
}

// Alihkan otomatis hanya bila link valid, fitur aktif, dan bukan mode preview.
$cs_bisa_alihkan = $cs_link_terisi && !empty($cs_auto_alihkan) && !isset($_GET['preview']);

$flash = ambil_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service - NERF Toko Bangunan</title>
    <meta name="description" content="Hubungi Customer Service NERF Toko Bangunan melalui WhatsApp untuk bantuan pesanan, produk, pembayaran, dan garansi.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/toko.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/customer-service.css') ?>" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/layout/navbar.php'; ?>

<div class="container">

    <?php include __DIR__ . '/partial/flash.php'; ?>

    <!-- ============ HERO ============ -->
    <section class="cs-hero">
        <div class="row align-items-center g-4 position-relative">
            <div class="col-lg-8">
                <span class="cs-eyebrow"><i class="bi bi-whatsapp"></i> Customer Service</span>
                <h1>Butuh bantuan? Chat tim kami lewat WhatsApp.</h1>
                <p class="cs-sub">
                    Ada pertanyaan seputar produk, harga, status pesanan, atau pengiriman?
                    Tim NERF Bangun siap membantu Anda dengan cepat melalui WhatsApp.
                </p>

                <?php if (!$cs_link_terisi): ?>
                    <div class="alert alert-warning py-2 px-3 mb-3" style="max-width:620px">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>https://chat.whatsapp.com/GqT81GcbH1eBzsdhKZTv9i?s=cl&p=a&iam=2</strong>
                        Silakan taruh link WA di
                        <code>config/customer-service.php</code> pada variabel
                        <code>$cs_wa_link</code>.
                    </div>
                    <button class="btn btn-secondary btn-lg" type="button" disabled>
                        <i class="bi bi-whatsapp"></i> Chat via WhatsApp
                    </button>
                <?php else: ?>
                    <a href="<?= e($cs_url) ?>" target="_blank" rel="noopener noreferrer"
                       class="btn btn-cs btn-lg" id="tombolWA">
                        <i class="bi bi-whatsapp"></i> Chat via WhatsApp
                    </a>
                    <?php if ($cs_bisa_alihkan): ?>
                        <p class="cs-alihkan">
                            <i class="bi bi-arrow-right-circle"></i>
                            Anda akan diarahkan ke WhatsApp dalam <?= (int)$cs_jeda_alihkan ?> detik.
                            <a href="<?= e($cs_url) ?>" target="_blank" rel="noopener noreferrer">Klik di sini</a>
                            jika tidak dialihkan otomatis.
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="col-lg-4 text-center position-relative">
                <div class="cs-ikon-besar"><i class="bi bi-whatsapp"></i></div>
                <div class="cs-jam">
                    <i class="bi bi-clock-history"></i>
                    Senin &ndash; Sabtu, 08.00&ndash;17.00 WIB
                </div>
            </div>
        </div>
    </section>

    <!-- ============ KONTAK LAIN ============ -->
    <div class="cs-kontak">
        <h5><i class="bi bi-info-circle"></i> Kontak lainnya</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="baris"><i class="bi bi-geo-alt"></i> Jl.Condet, Jakarta</div>
                <div class="baris"><i class="bi bi-telephone"></i> 0812-3456-7890</div>
            </div>
            <div class="col-md-6">
                <div class="baris"><i class="bi bi-envelope"></i> cs@tokobangunan.com</div>
                <div class="baris"><i class="bi bi-clock"></i> Senin &ndash; Sabtu, 08.00&ndash;17.00</div>
            </div>
        </div>
    </div>

</div>

<?php if ($cs_bisa_alihkan): ?>
<script>
    // Alihkan ke WhatsApp setelah jeda singkat. Tetap ada link cadangan di halaman.
    setTimeout(function () {
        window.location.href = <?= json_encode(
            $cs_url,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        ) ?>;
    }, <?= max(0, (int)$cs_jeda_alihkan) * 1000 ?>);
</script>
<?php endif; ?>

<?php include __DIR__ . '/layout/footer.php'; ?>
