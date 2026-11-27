<?php
/**
 * KEBIJAKAN PRIVASI - aturan pengelolaan data pribadi pengguna website.
 *
 * Halaman publik: bisa diakses pelanggan maupun pengunjung.
 * Menggunakan stylesheet yang sama dengan halaman syarat-ketentuan.php.
 */
session_start();
require_once __DIR__ . '/config/fungsi.php';

$menu_aktif       = 'kebijakan-privasi';
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
    <title>Kebijakan Privasi - NERF Toko Bangunan</title>
    <meta name="description" content="Kebijakan privasi NERF Toko Bangunan mengenai pengumpulan, penggunaan, penyimpanan, dan perlindungan data pribadi pengguna website.">
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
        <a href="beranda.php">Beranda</a> / Kebijakan Privasi
    </nav>

    <!-- ============ HEADER ============ -->
    <header class="sk-header">
        <div class="d-flex align-items-center gap-3">
            <div class="sk-ikon"><i class="bi bi-shield-lock"></i></div>
            <div>
                <h1>Kebijakan Privasi</h1>
                <p>Aturan penggunaan dan perlindungan data pribadi di Website NERF Toko Bangunan.</p>
            </div>
        </div>
        <span class="sk-tanggal"><i class="bi bi-calendar3"></i> Terakhir diperbarui: 17 November 2026</span>
    </header>

    <div class="row g-4 mt-1">

        <!-- ============ ISI ============ -->
        <div class="col-lg-9">
            <article class="sk-kartu">

                <section class="sk-pasal" id="pasal-1">
                    <h2><span class="sk-nomor">1</span> Pendahuluan</h2>
                    <p>Kami menghargai privasi Anda. Kebijakan ini menjelaskan informasi apa yang kami kumpulkan, bagaimana kami menggunakannya, dengan siapa kami membagikannya, dan bagaimana kami menjaganya, ketika Anda mengunjungi atau menggunakan Website NERF Toko Bangunan.</p>
                    <p>Dengan menggunakan Website, Anda menyetujui praktik yang dijelaskan dalam kebijakan ini beserta <a href="syarat-ketentuan.php">Syarat dan Ketentuan</a> yang berlaku.</p>
                </section>

                <section class="sk-pasal" id="pasal-2">
                    <h2><span class="sk-nomor">2</span> Informasi yang Kami Kumpulkan</h2>
                    <p>Kami hanya mengumpulkan informasi yang diperlukan untuk menjalankan Website dan melayani pesanan Anda, antara lain:</p>
                    <ul>
                        <li><strong>Data akun:</strong> nama lengkap, alamat email, nomor telepon, dan kata sandi (disimpan dalam bentuk hash, bukan teks asli).</li>
                        <li><strong>Data pesanan:</strong> produk yang dipesan, jumlah, alamat pengiriman, metode pengiriman, metode pembayaran, dan status pesanan.</li>
                        <li><strong>Data teknis:</strong> alamat IP, jenis perangkat/browser, dan halaman yang diakses, yang kami gunakan untuk menjaga keamanan dan memperbaiki layanan.</li>
                    </ul>
                    <p>Kami tidak meminta informasi yang tidak relevan dengan kebutuhan di atas.</p>
                </section>

                <section class="sk-pasal" id="pasal-3">
                    <h2><span class="sk-nomor">3</span> Cara Kami Menggunakan Informasi</h2>
                    <p>Informasi yang kami kumpulkan digunakan untuk:</p>
                    <ul>
                        <li>Membuat dan mengelola akun Anda serta mengatur proses masuk (<em>login</em>).</li>
                        <li>Memproses, mengelola, dan melacak pesanan Anda hingga selesai.</li>
                        <li>Menghubungi Anda terkait status pesanan, pengiriman, atau kendala transaksi.</li>
                        <li>Menampilkan riwayat pesanan dan mengelola keranjang belanja Anda.</li>
                        <li>Menanggapi pertanyaan, keluhan, dan permintaan dukungan pelanggan.</li>
                        <li>Menjaga keamanan Website, mencegah penipuan, dan memperbaiki pengalaman pengguna.</li>
                    </ul>
                    <p>Kami tidak menggunakan data pribadi Anda untuk dijual atau disewakan kepada pihak ketiga.</p>
                </section>

                <section class="sk-pasal" id="pasal-4">
                    <h2><span class="sk-nomor">4</span> Dasar Pengolahan Data</h2>
                    <p>Kami mengolah data pribadi Anda berdasarkan:</p>
                    <ul>
                        <li><strong>Persetujuan:</strong> saat Anda mendaftar, mengirim testimoni, atau menghubungi kami.</li>
                        <li><strong>Pelaksanaan transaksi:</strong> saat data diperlukan untuk memproses pesanan Anda.</li>
                        <li><strong>Kepentingan sah:</strong> untuk menjaga keamanan akun, mencegah penyalahgunaan, dan memperbaiki layanan.</li>
                        <li><strong>Kewajiban hukum:</strong> apabila diwajibkan oleh peraturan yang berlaku.</li>
                    </ul>
                </section>

                <section class="sk-pasal" id="pasal-5">
                    <h2><span class="sk-nomor">5</span> Penyimpanan dan Pengamanan Data</h2>
                    <p>Data Anda disimpan pada basis data Website dan dilindungi dengan beberapa langkah keamanan, antara lain:</p>
                    <ul>
                        <li>Kata sandi tidak disimpan apa adanya, melainkan dalam bentuk <em>hash</em> menggunakan algoritma <em>bcrypt</em>.</li>
                        <li>Akses ke data yang sensitif dibatasi hanya untuk pihak yang berwenang, seperti administrator toko.</li>
                        <li>Data dikirimkan melalui koneksi yang diamankan bila Website dijalankan dengan HTTPS.</li>
                        <li>Kami melakukan pembaruan dan perawatan sistem secara berkala untuk mengurangi risiko kebocoran data.</li>
                    </ul>
                    <p>Meskipun kami berusaha melindungi data Anda, tidak ada sistem yang sepenuhnya bebas risiko. Kami tidak dapat menjamin keamanan mutlak, namun kami akan segera menindaklanjuti setiap indikasi pelanggaran.</p>
                </section>

                <section class="sk-pasal" id="pasal-6">
                    <h2><span class="sk-nomor">6</span> Pihak Ketiga</h2>
                    <p>Kami dapat menggunakan layanan pihak ketiga untuk mendukung operasional Website, misalnya:</p>
                    <ul>
                        <li><strong>WhatsApp:</strong> bila Anda memilih menghubungi Customer Service, Anda akan diarahkan ke layanan WhatsApp yang memiliki kebijakan privasi tersendiri.</li>
                        <li><strong>Penyedia hosting dan infrastruktur:</strong> untuk menyimpan serta menjalankan Website dan basis data.</li>
                        <li><strong>Penyedia aset tampilan:</strong> seperti CDN untuk stylesheet dan ikon yang dimuat saat halaman ditampilkan.</li>
                    </ul>
                    <p>Pihak ketiga tersebut hanya menerima data yang diperlukan untuk layanan mereka. Kami tidak menjual data pribadi Anda kepada siapa pun.</p>
                </section>

                <section class="sk-pasal" id="pasal-7">
                    <h2><span class="sk-nomor">7</span> Sesi dan Pelacakan</h2>
                    <p>Website menggunakan sesi (<em>session</em>) untuk menyimpan status masuk, isi keranjang belanja, dan pesan singkat antarhalaman. Sesi ini bersifat sementara dan akan berakhir setelah Anda keluar atau masa berlakunya habis.</p>
                    <p>Kami tidak menggunakan cookie iklan atau alat pelacakan pihak ketiga untuk menampilkan iklan kepada Anda. Data teknis seperti alamat IP hanya digunakan untuk keperluan keamanan dan perbaikan layanan.</p>
                </section>

                <section class="sk-pasal" id="pasal-8">
                    <h2><span class="sk-nomor">8</span> Retensi Data</h2>
                    <p>Kami menyimpan data pribadi selama diperlukan untuk menjalankan layanan, memenuhi kewajiban hukum, dan menyelesaikan sengketa. Data akun dan riwayat pesanan dapat tetap disimpan selama akun Anda aktif.</p>
                    <p>Apabila Anda tidak lagi menggunakan Website, Anda dapat meminta penghapusan data dengan menghubungi Customer Service. Beberapa data mungkin tetap disimpan sepanjang diwajibkan oleh peraturan yang berlaku.</p>
                </section>

                <section class="sk-pasal" id="pasal-9">
                    <h2><span class="sk-nomor">9</span> Hak Anda</h2>
                    <p>Terkait data pribadi Anda, Anda memiliki hak untuk:</p>
                    <ul>
                        <li>Mengetahui informasi apa yang kami simpan tentang Anda.</li>
                        <li>Meminta pembetulan data yang tidak akurat atau tidak lengkap.</li>
                        <li>Meminta penghapusan data, selama tidak bertentangan dengan kewajiban hukum.</li>
                        <li>Mencabut persetujuan penggunaan data tertentu kapan saja.</li>
                        <li>Mengajukan pertanyaan atau keluhan terkait pengolahan data Anda.</li>
                    </ul>
                    <p>Untuk menggunakan hak tersebut, silakan hubungi kami melalui halaman Customer Service.</p>
                </section>

                <section class="sk-pasal" id="pasal-10">
                    <h2><span class="sk-nomor">10</span> Perubahan Kebijakan</h2>
                    <p>Kami dapat memperbarui kebijakan privasi ini sewaktu-waktu untuk menyesuaikan dengan perubahan layanan atau peraturan. Perubahan berlaku sejak tanggal pembaruan yang tercantum di bagian atas halaman ini.</p>
                    <p>Silakan periksa halaman ini secara berkala. Dengan terus menggunakan Website setelah perubahan diterbitkan, Anda dianggap menerima kebijakan yang diperbarui.</p>
                </section>

                <section class="sk-pasal" id="pasal-11">
                    <h2><span class="sk-nomor">11</span> Hubungi Kami</h2>
                    <p>Jika Anda memiliki pertanyaan, keluhan, atau permintaan terkait data pribadi Anda, hubungi Customer Service NERF Toko Bangunan melalui halaman <a href="customer-service.php">Customer Service</a> atau kontak yang tercantum di footer Website. Kami akan berusaha menanggapi permintaan Anda secepatnya.</p>
                </section>

            </article>
        </div>

        <!-- ============ SIDEBAR ============ -->
        <aside class="col-lg-3">
            <div class="sk-sisi">
                <div class="sk-daftar">
                    <h6>Daftar Isi</h6>
                    <a href="#pasal-1">1. Pendahuluan</a>
                    <a href="#pasal-2">2. Informasi yang Kami Kumpulkan</a>
                    <a href="#pasal-3">3. Cara Kami Menggunakan Informasi</a>
                    <a href="#pasal-4">4. Dasar Pengolahan Data</a>
                    <a href="#pasal-5">5. Penyimpanan dan Pengamanan</a>
                    <a href="#pasal-6">6. Pihak Ketiga</a>
                    <a href="#pasal-7">7. Sesi dan Pelacakan</a>
                    <a href="#pasal-8">8. Retensi Data</a>
                    <a href="#pasal-9">9. Hak Anda</a>
                    <a href="#pasal-10">10. Perubahan Kebijakan</a>
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
