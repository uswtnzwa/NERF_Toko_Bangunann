<?php
/**
 * SYARAT & KETENTUAN - aturan penggunaan website NERF Toko Bangunan.
 *
 * Halaman publik: bisa diakses pelanggan maupun pengunjung.
 * Isi halaman ini adalah ketentuan penggunaan website, bukan daftar produk.
 */
session_start();
require_once __DIR__ . '/config/fungsi.php';

$menu_aktif       = 'syarat-ketentuan';
$jumlah_keranjang = function_exists('keranjang_total_qty') ? keranjang_total_qty() : 0;

// Kategori untuk footer (tetap aman walau database belum siap).
$footer_kategori = [];
$db = db_opsional();
if ($db instanceof mysqli) {
    $footer_kategori = $db->query(
        "SELECT id_kategori, nama_kategori FROM kategori WHERE status='Aktif' ORDER BY nama_kategori"
    )->fetch_all(MYSQLI_ASSOC);
}

$flash = ambil_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat dan Ketentuan - NERF Toko Bangunan</title>
    <meta name="description" content="Syarat dan ketentuan penggunaan website NERF Toko Bangunan, meliputi aturan akun, pemesanan, pembayaran, pengiriman, dan penggunaan konten.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= aset_url('css/toko.css') ?>" rel="stylesheet">
    <link href="<?= aset_url('css/syarat-ketentuan.css') ?>" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/layout/navbar.php'; ?>

<div class="container">

    <?php include __DIR__ . '/partial/flash.php'; ?>

    <nav class="breadcrumb-katalog mt-3">
        <a href="beranda.php">Beranda</a> / Syarat dan Ketentuan
    </nav>

    <!-- ============ HEADER ============ -->
    <header class="sk-header">
        <div class="d-flex align-items-center gap-3">
            <div class="sk-ikon"><i class="bi bi-file-text"></i></div>
            <div>
                <h1>Syarat dan Ketentuan</h1>
                <p>Aturan dan ketentuan penggunaan website NERF Toko Bangunan.</p>
            </div>
        </div>
        <span class="sk-tanggal"><i class="bi bi-calendar3"></i> Terakhir diperbarui: 17 November 2026</span>
    </header>

    <div class="row g-4 mt-1">

        <!-- ============ ISI ============ -->
        <div class="col-lg-9">
            <article class="sk-kartu">

                <section class="sk-pasal" id="pasal-1">
                    <h2><span class="sk-nomor">1</span> Penerimaan Syarat</h2>
                    <p>Dengan mengakses, menjelajahi, atau menggunakan website NERF Toko Bangunan (selanjutnya disebut <strong>"Website"</strong>), Anda dinyatakan telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan ini. Jika Anda tidak menyetujui salah satu bagian, mohon untuk tidak menggunakan Website.</p>
                    <p>Syarat ini berlaku bagi semua pengunjung, pelanggan, maupun pihak lain yang menggunakan Website, termasuk layanan pemesanan dan fitur terkait di dalamnya.</p>
                </section>

                <section class="sk-pasal" id="pasal-2">
                    <h2><span class="sk-nomor">2</span> Penggunaan Website</h2>
                    <p>Website disediakan untuk membantu Anda memperoleh informasi produk dan melakukan transaksi pembelian material bangunan. Anda wajib menggunakan Website dengan cara yang sah dan tidak merugikan pihak lain.</p>
                    <ul>
                        <li>Dilarang menggunakan Website untuk tujuan penipuan, pengecohan, atau aktivitas melanggar hukum.</li>
                        <li>Dilarang mengambil, menyalin, atau menyebarkan data dalam jumlah besar tanpa izin tertulis (termasuk praktik <em>scraping</em> otomatis).</li>
                        <li>Dilarang mengganggu kinerja server, jaringan, atau keamanan Website, termasuk melalui virus, malware, atau serangan lainnya.</li>
                        <li>Dilarang mengirimkan komentar, ulasan, testimoni, atau konten yang mengandung ujaran kebencian, pornografi, SARA, atau informasi palsu.</li>
                        <li>Setiap testimoni atau ulasan yang dikirimkan akan ditinjau terlebih dahulu oleh admin sebelum ditampilkan.</li>
                    </ul>
                </section>

                <section class="sk-pasal" id="pasal-3">
                    <h2><span class="sk-nomor">3</span> Akun dan Keamanan</h2>
                    <p>Untuk menggunakan beberapa fitur, seperti melakukan pemesanan, menyimpan riwayat pesanan, dan menulis testimoni, Anda perlu masuk menggunakan akun pelanggan. Anda juga dapat mendaftar melalui halaman pendaftaran yang tersedia.</p>
                    <ul>
                        <li>Anda bertanggung jawab atas keakuratan data yang diberikan saat mendaftar, termasuk nama, email, dan nomor telepon yang masih aktif.</li>
                        <li>Anda wajib menjaga kerahasiaan kata sandi. Segala aktivitas yang terjadi menggunakan akun Anda menjadi tanggung jawab Anda sendiri.</li>
                        <li>Harap keluar (<em>logout</em>) dari akun bila menggunakan perangkat bersama atau publik.</li>
                        <li>Jika terjadi penggunaan akun tanpa izin, segera hubungi Customer Service melalui tautan di bawah halaman ini.</li>
                        <li>Kami berhak menonaktifkan akun yang terbukti menyalahgunakan Website atau melanggar syarat ini.</li>
                    </ul>
                </section>

                <section class="sk-pasal" id="pasal-4">
                    <h2><span class="sk-nomor">4</span> Informasi Produk dan Harga</h2>
                    <p>Kami berusaha menampilkan informasi produk, gambar, spesifikasi, stok, dan harga seakurat mungkin. Namun, gambar produk bersifat ilustrasi dan dapat sedikit berbeda dari barang yang dikirimkan.</p>
                    <ul>
                        <li>Harga dan stok dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya.</li>
                        <li>Harga yang berlaku adalah harga yang ditampilkan pada saat Anda melakukan pemesanan.</li>
                        <li>Jumlah stok yang ditampilkan bukan jaminan ketersediaan barang hingga saat pembayaran dikonfirmasi.</li>
                        <li>Informasi produk dapat diperbarui atau dihapus kapan saja sesuai kondisi persediaan toko.</li>
                    </ul>
                </section>

                <section class="sk-pasal" id="pasal-5">
                    <h2><span class="sk-nomor">5</span> Pemesanan dan Pembayaran</h2>
                    <p>Pemesanan dibuat melalui keranjang belanja dan halaman <em>checkout</em>. Dengan mengirimkan pesanan, Anda menyatakan bahwa data yang dimasukkan sudah benar dan Anda menyetujui total tagihan yang ditampilkan.</p>
                    <ul>
                        <li>Pesanan hanya diproses setelah metode pengiriman, alamat (bila diantar), dan metode pembayaran diisi dengan valid.</li>
                        <li>Anda wajib membayar sesuai jumlah dan metode yang dipilih pada halaman checkout.</li>
                        <li>Kami berhak menolak atau membatalkan pesanan apabila terdapat indikasi kesalahan data, duplikasi pesanan, atau dugaan penyalahgunaan.</li>
                        <li>Status pesanan dapat Anda pantau melalui menu <strong>Pesanan Saya</strong> setelah masuk ke akun Anda.</li>
                    </ul>
                </section>

                <section class="sk-pasal" id="pasal-6">
                    <h2><span class="sk-nomor">6</span> Pengiriman dan Pengambilan</h2>
                    <p>Kami melayani dua pilihan cara menerima pesanan, yaitu pengambilan langsung di toko (<em>ambil di toko</em>) dan pengantaran ke alamat yang Anda berikan.</p>
                    <ul>
                        <li>Untuk pengambilan di toko, Anda akan dihubungi saat pesanan siap diambil.</li>
                        <li>Untuk pengantaran, alamat dan nomor telepon penerima wajib diisi dengan lengkap dan benar.</li>
                        <li>Biaya antar mengikuti ketentuan yang ditampilkan saat checkout.</li>
                        <li>Keterlambatan akibat kondisi di luar kendali kami, seperti cuaca buruk, kendala transportasi, atau data alamat yang tidak lengkap, tidak menjadi tanggung jawab kami sepenuhnya.</li>
                    </ul>
                </section>

                <section class="sk-pasal" id="pasal-7">
                    <h2><span class="sk-nomor">7</span> Pembatalan, Retur, dan Garansi</h2>
                    <p>Anda dapat mengajukan pembatalan, retur, atau klaim garansi dengan menghubungi Customer Service sesegera mungkin setelah menerima pesanan.</p>
                    <ul>
                        <li>Barang yang dikembalikan harus dalam kondisi sesuai ketentuan dan disertai bukti pembelian.</li>
                        <li>Klaim garansi mengikuti ketentuan garansi dari masing-masing produk atau produsen.</li>
                        <li>Keputusan akhir atas permintaan pembatalan, retur, atau garansi ditetapkan setelah peninjauan oleh pihak toko.</li>
                    </ul>
                </section>

                <section class="sk-pasal" id="pasal-8">
                    <h2><span class="sk-nomor">8</span> Konten dan Kekayaan Intelektual</h2>
                    <p>Seluruh konten di Website, termasuk nama dan logo NERF Bangun, gambar produk, teks, desain, dan kode program, merupakan hak milik NERF Toko Bangunan atau pihak yang berhak dan dilindungi oleh peraturan perundang-undangan.</p>
                    <ul>
                        <li>Anda tidak diperkenankan menyalin, mengubah, mendistribusikan, atau menggunakan kembali konten tersebut untuk kepentingan komersial tanpa izin tertulis.</li>
                        <li>Penggunaan gambar atau deskripsi produk untuk keperluan pribadi dan tidak komersial diperbolehkan sepanjang tidak menyesatkan pihak lain.</li>
                    </ul>
                </section>

                <section class="sk-pasal" id="pasal-9">
                    <h2><span class="sk-nomor">9</span> Batasan Tanggung Jawab</h2>
                    <p>Website disediakan "sebagaimana adanya". Kami berusaha menjaga Website tetap tersedia dan aman, namun tidak menjamin akses tanpa gangguan setiap saat.</p>
                    <ul>
                        <li>Kami tidak bertanggung jawab atas kerugian yang timbul akibat gangguan jaringan, pemadaman listrik, atau perangkat pengguna yang tidak mendukung.</li>
                        <li>Kami tidak bertanggung jawab atas kesalahan yang timbul karena data yang Anda masukkan sendiri, seperti alamat atau nomor telepon yang keliru.</li>
                        <li>Tanggung jawab kami atas suatu pesanan dibatasi pada nilai pesanan yang bersangkutan.</li>
                    </ul>
                </section>

                <section class="sk-pasal" id="pasal-10">
                    <h2><span class="sk-nomor">10</span> Perubahan Syarat</h2>
                    <p>Kami berhak mengubah, menambah, atau memperbarui syarat dan ketentuan ini sewaktu-waktu. Perubahan akan berlaku sejak tanggal pembaruan yang tercantum di bagian atas halaman ini.</p>
                    <p>Dengan tetap menggunakan Website setelah perubahan diterbitkan, Anda dianggap menerima syarat yang telah diperbarui. Silakan periksa halaman ini secara berkala untuk mengetahui versi terbaru.</p>
                </section>

                <section class="sk-pasal" id="pasal-11">
                    <h2><span class="sk-nomor">11</span> Hubungi Kami</h2>
                    <p>Jika Anda memiliki pertanyaan, keluhan, atau memerlukan penjelasan lebih lanjut mengenai syarat dan ketentuan ini, silakan hubungi Customer Service NERF Toko Bangunan melalui halaman <a href="customer-service.php">Customer Service</a> atau kontak yang tercantum di footer Website.</p>
                </section>

            </article>
        </div>

        <!-- ============ SIDEBAR ============ -->
        <aside class="col-lg-3">
            <div class="sk-sisi">
                <div class="sk-daftar">
                    <h6>Daftar Isi</h6>
                    <a href="#pasal-1">1. Penerimaan Syarat</a>
                    <a href="#pasal-2">2. Penggunaan Website</a>
                    <a href="#pasal-3">3. Akun dan Keamanan</a>
                    <a href="#pasal-4">4. Informasi Produk dan Harga</a>
                    <a href="#pasal-5">5. Pemesanan dan Pembayaran</a>
                    <a href="#pasal-6">6. Pengiriman dan Pengambilan</a>
                    <a href="#pasal-7">7. Pembatalan, Retur, dan Garansi</a>
                    <a href="#pasal-8">8. Konten dan Kekayaan Intelektual</a>
                    <a href="#pasal-9">9. Batasan Tanggung Jawab</a>
                    <a href="#pasal-10">10. Perubahan Syarat</a>
                    <a href="#pasal-11">11. Hubungi Kami</a>
                </div>
                <div class="sk-bantuan">
                    <h6><i class="bi bi-question-circle"></i> Butuh bantuan?</h6>
                    <p>Tim kami siap membantu Anda lewat WhatsApp.</p>
                    <a href="customer-service.php">Hubungi Customer Service &rarr;</a>
                </div>
            </div>
        </aside>

    </div>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
