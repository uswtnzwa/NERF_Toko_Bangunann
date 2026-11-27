-- ============================================================
--  MIGRASI: BLOG / BERITA (ARTIKEL)
--  Menyimpan artikel seputar produk material bangunan,
--  tips pengerjaan, dan tren industri konstruksi.
--
--  Jalankan lewat phpMyAdmin (import) ATAU CLI:
--    C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -uroot \
--      < migrations/2026_artikel.sql
--
--  Aman dijalankan berulang kali (CREATE IF NOT EXISTS + UPSERT).
-- ============================================================

USE NERF_TokoBangunan;

-- --------------------------------------------------------
-- 1. Tabel artikel
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `artikel` (
  `id_artikel`        int(11) NOT NULL AUTO_INCREMENT,
  `judul`             varchar(200) NOT NULL,
  `slug`              varchar(220) NOT NULL,
  `kategori`          varchar(40) NOT NULL DEFAULT 'Produk',
  `ringkasan`         text NOT NULL,
  `isi`               longtext NOT NULL,
  `gambar`            varchar(255) DEFAULT NULL,
  `penulis`           varchar(100) NOT NULL DEFAULT 'Admin NERF',
  `dibaca`            int(11) NOT NULL DEFAULT 0,
  `status`            enum('Terbit','Draft') NOT NULL DEFAULT 'Terbit',
  `dibuat_pada`       datetime NOT NULL DEFAULT current_timestamp(),
  `diterbitkan_pada`  datetime DEFAULT NULL,
  PRIMARY KEY (`id_artikel`),
  UNIQUE KEY `slug` (`slug`),
  KEY `kategori` (`kategori`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Data awal artikel (topik produk & industri)
-- --------------------------------------------------------
INSERT INTO `artikel`
  (`id_artikel`, `judul`, `slug`, `kategori`, `ringkasan`, `isi`, `gambar`, `penulis`, `dibaca`, `status`, `dibuat_pada`, `diterbitkan_pada`)
VALUES

(1, 'Memilih Semen yang Tepat untuk Proyek Rumah Anda',
    'memilih-semen-yang-tepat-untuk-proyek-rumah',
    'Produk',
    'Tidak semua semen sama. Kenali perbedaan semen Portland, semen putih, dan semen cepat keras agar fondasi rumah Anda lebih kuat dan tetap hemat biaya.',
    '<p>Semen adalah material paling dasar dalam sebuah konstruksi. Kesalahan memilih jenis semen bisa berdampak pada kekuatan struktur, biaya, dan umur bangunan. Berikut panduan singkat yang bisa Anda jadikan acuan.</p><h3>1. Semen Portland (PCC)</h3><p>Jenis ini paling umum dipakai untuk pekerjaan umum seperti pondasi, kolom, dan dinding. Daya ikatnya kuat dan harganya paling terjangkau, sehingga cocok untuk hampir seluruh kebutuhan rumah tinggal.</p><h3>2. Semen Putih</h3><p>Semen putih biasanya dipakai untuk finishing, nat keramik, dan pekerjaan dekoratif yang mengutamakan tampilan bersih. Harganya lebih tinggi, jadi gunakan hanya di bagian yang memang terlihat.</p><h3>3. Semen Cepat Keras</h3><p>Untuk proyek dengan waktu mepet, semen cepat keras bisa mempercepat proses pengecoran. Pastikan tenaga kerja siap karena waktu pengolahannya jauh lebih singkat dari semen biasa.</p><p>Tim NERF Toko Bangunan menyediakan semua jenis semen di atas dengan stok siap kirim. Konsultasikan kebutuhan proyek Anda kepada kami agar mendapat rekomendasi yang paling pas.</p>',
    'uploads/artikel/cover-produk.svg', 'Tim NERF', 342, 'Terbit', '2026-10-02 08:15:00', '2026-10-03 09:00:00'),

(2, '5 Tips Merawat Besi Beton agar Tidak Berkarat',
    '5-tips-merawat-besi-beton-agar-tidak-berkarat',
    'Tips',
    'Karat bisa melemahkan struktur bangunan. Ikuti lima langkah praktis menyimpan dan merawat besi beton sebelum maupun sesudah pemasangan.',
    '<p>Besi beton yang berkarat kehilangan daya ikat dengan beton dan bisa mengarah pada retakan struktur. Pencegahan jauh lebih murah daripada perbaikan. Ini lima tipsnya.</p><h3>1. Simpan di tempat kering</h3><p>Hindari penumpukan langsung di tanah. Gunakan alas kayu atau palet agar sirkulasi udara tetap baik.</p><h3>2. Tutup saat musim hujan</h3><p>Gunakan terpal kedap air, tetapi tetap beri celah ventilasi supaya lembap tidak terperangkap.</p><h3>3. Pisahkan ukuran dan batch</h3><p>Beri label pada setiap tumpukan besi agar mudah dipantau dan tidak tertukar saat pemakaian.</p><h3>4. Bersihkan sebelum dipakai</h3><p>Sikat permukaan yang berkarat ringan menggunakan sikat kawat sebelum besi dibengkokkan dan dipasang.</p><h3>5. Jaga selimut beton</h3><p>Pastikan ketebalan selimut beton sesuai spesifikasi agar besi tidak cepat terpapar air dan udara.</p><p>Butuh besi beton dengan kualitas terjamin? Lihat pilihan ukuran dan stok terbaru di katalog NERF.</p>',
    'uploads/artikel/cover-tips.svg', 'Tim NERF', 218, 'Terbit', '2026-10-09 10:20:00', '2026-10-10 08:30:00'),

(3, 'Tren Industri Konstruksi 2026: Material Ramah Lingkungan',
    'tren-industri-konstruksi-2026-material-ramah-lingkungan',
    'Industri',
    'Industri konstruksi bergerak ke arah material hijau. Simak tren beton rendah karbon, baja daur ulang, dan kayu bersertifikat yang mulai banyak dipakai.',
    '<p>Kesadaran akan jejak karbon membuat banyak kontraktor beralih ke material yang lebih ramah lingkungan tanpa mengorbankan kekuatan struktur.</p><h3>Beton rendah karbon</h3><p>Penggunaan substitusi semen seperti fly ash dan slag dapat menurunkan emisi hingga sekitar 30 persen per meter kubik beton.</p><h3>Baja daur ulang</h3><p>Baja bekas yang diolah kembali kini banyak dipakai untuk konstruksi ringan hingga menengah dengan biaya yang lebih efisien.</p><h3>Kayu bersertifikat</h3><p>Sertifikasi hutan lestari memberi jaminan bahwa kayu berasal dari sumber yang dikelola secara bertanggung jawab.</p><p>NERF Toko Bangunan terus menambah pilihan material ramah lingkungan. Hubungi kami untuk informasi ketersediaan dan spesifikasinya.</p>',
    'uploads/artikel/cover-industri.svg', 'Redaksi NERF', 176, 'Terbit', '2026-10-18 07:45:00', '2026-10-20 10:00:00'),

(4, 'Panduan Memasang Keramik Lantai Sendiri di Rumah',
    'panduan-memasang-keramik-lantai-sendiri-di-rumah',
    'Tips',
    'Lem keset, nat rapi, hasil maksimal. Ikuti panduan lengkap memasang keramik lantai sendiri tanpa harus memanggil tukang profesional.',
    '<p>Memasang keramik sendiri bukan hal mustahil asalkan persiapan dan urutan kerjanya benar. Berikut langkah yang biasa dipakai para profesional.</p><h3>Persiapan permukaan</h3><p>Bersihkan lantai dari debu dan minyak, lalu ratakan bagian yang bergelombang. Lantai yang tidak rata membuat keramik mudah retak.</p><h3>Penentuan patokan</h3><p>Pasang benang atau patokan dari tengah ruangan ke arah dinding supaya pola tetap simetris.</p><h3>Pemasangan dan nat</h3><p>Oleskan lem secara merata, tekan keramik dengan sendokan getar, lalu diamkan sebelum mengisi nat. Gunakan nat yang sesuai warna keramik.</p><p>Pastikan Anda memilih keramik dengan tingkat ketahanan gesek yang sesuai, terutama untuk area basah seperti kamar mandi.</p>',
    'uploads/artikel/cover-tips.svg', 'Tim NERF', 405, 'Terbit', '2026-10-27 13:10:00', '2026-10-28 09:15:00'),

(5, 'Pipa PVC vs PPR: Mana yang Lebih Cocok untuk Rumah?',
    'pipa-pvc-vs-ppr-mana-yang-lebih-cocok-untuk-rumah',
    'Produk',
    'Membandingkan pipa PVC dan PPR dari segi harga, ketahanan terhadap panas, dan kemudahan pemasangan untuk instalasi air bersih rumah tangga.',
    '<p>Dua jenis pipa ini paling sering dipertimbangkan saat membangun atau merenovasi rumah. Masing-masing punya keunggulan tersendiri.</p><h3>Pipa PVC</h3><p>Harganya lebih ekonomis dan mudah ditemukan. Cocok untuk saluran air dingin dan pembuangan. Pemasangannya memakai lem khusus yang cepat mengeras.</p><h3>Pipa PPR</h3><p>Lebih tahan terhadap suhu tinggi dan tekanan, sehingga aman untuk air panas. Sambungannya dipanaskan dengan alat khusus sehingga hasilnya menyatu.</p><h3>Mana yang dipilih?</h3><p>Untuk instalasi air dingin dengan anggaran terbatas, PVC sudah cukup. Jika Anda membutuhkan jalur air panas atau menginginkan sambungan tanpa lem, PPR adalah pilihan yang lebih baik.</p><p>Keduanya tersedia di NERF Toko Bangunan dalam berbagai diameter. Silakan konsultasikan kebutuhan instalasi Anda.</p>',
    'uploads/artikel/cover-produk.svg', 'Tim NERF', 289, 'Terbit', '2026-11-03 09:30:00', '2026-11-04 08:00:00'),

(6, 'Harga Material Bangunan Naik, Ini Cara Menghemat Anggaran Proyek',
    'harga-material-bangunan-naik-cara-menghemat-anggaran-proyek',
    'Industri',
    'Biaya material terus meningkat. Simak strategi cermat mulai dari perencanaan, pembelian grosir, sampai memilih alternatif material yang tetap berkualitas.',
    '<p>Kenaikan harga material sering membuat anggaran proyek membengkak. Dengan perencanaan yang matang, dampaknya bisa ditekan tanpa menurunkan kualitas bangunan.</p><h3>Rencanakan kebutuhan sejak awal</h3><p>Hitung volume material berdasarkan gambar kerja. Kelebihan pembelian sering menjadi penyebab utama pemborosan.</p><h3>Beli dalam jumlah besar</h3><p>Pembelian grosir umumnya mendapat harga lebih baik. Untuk proyek skala kecil, Anda bisa bergabung dengan tetangga atau kontraktor di sekitar.</p><h3>Pertimbangkan alternatif</h3><p>Beberapa material lokal kini memiliki kualitas setara dengan produk impor, dengan harga yang lebih bersahabat.</p><p>Pantau informasi harga terkini lewat katalog NERF yang terus diperbarui agar Anda mendapat penawaran terbaik.</p>',
    'uploads/artikel/cover-industri.svg', 'Redaksi NERF', 153, 'Terbit', '2026-11-06 11:05:00', '2026-11-07 10:30:00'),

(7, 'Cat Eksterior Tahan Lama: Rekomendasi dan Cara Aplikasinya',
    'cat-eksterior-tahan-lama-rekomendasi-dan-cara-aplikasinya',
    'Produk',
    'Cuaca panas dan hujan cepat merusak cat tembok. Pelajari jenis cat eksterior terbaik dan teknik pengecatan agar warna tetap awet bertahun-tahun.',
    '<p>Cat bagian luar rumah menghadapi paparan sinar matahari dan hujan sepanjang tahun. Memilih produk yang tepat akan menghemat biaya pengecatan ulang.</p><h3>Jenis cat yang direkomendasikan</h3><p>Cat akrilik eksterior memiliki daya tahan baik terhadap cuaca dan mudah dibersihkan. Untuk hasil lebih premium, pilih cat dengan teknologi anti-jamur.</p><h3>Persiapan dinding</h3><p>Bersihkan lumut dan lapisan cat lama yang mengelupas. Gunakan plamir atau wall filler agar permukaan rata sebelum diwarnai.</p><h3>Teknik pengecatan</h3><p>Gunakan kuas atau roller dengan gerakan searah. Berikan dua lapis tipis daripada satu lapis tebal agar hasil lebih merata dan cepat kering.</p><p>Kami menyediakan berbagai pilihan cat eksterior dengan warna siap pakai. Cek stok dan harga di katalog produk.</p>',
    'uploads/artikel/cover-produk.svg', 'Tim NERF', 264, 'Terbit', '2026-11-10 14:40:00', '2026-11-11 09:00:00'),

(8, 'Inovasi Kayu Olahan untuk Konstruksi Modern',
    'inovasi-kayu-olahan-untuk-konstruksi-modern',
    'Industri',
    'Dari kayu laminasi hingga panel komposit, inovasi kayu olahan menawarkan kekuatan tinggi dengan tampilan estetik untuk hunian masa kini.',
    '<p>Kayu tetap menjadi material favorit karena kehangatan tampilannya. Kini berbagai olahan kayu modern mampu mengatasi kelemahan kayu solid tradisional.</p><h3>Kayu laminasi</h3><p>Beberapa lapisan kayu direkatkan membentuk balok dengan kekuatan lebih tinggi dan ukuran lebih panjang dari kayu utuh.</p><h3>Panel komposit</h3><p>Campuran serat kayu dan polimer menghasilkan panel yang tahan lembap serta tidak mudah dimakan rayap.</p><h3>Penggunaan di hunian</h3><p>Material ini cocok untuk rangka atap, lantai, hingga furnitur built in yang membutuhkan tampilan rapi dan konsisten.</p><p>NERF Toko Bangunan menyediakan berbagai pilihan kayu olahan. Hubungi kami untuk informasi ukuran, spesifikasi, dan ketersediaan stok.</p>',
    'uploads/artikel/cover-industri.svg', 'Redaksi NERF', 131, 'Terbit', '2026-11-13 08:55:00', '2026-11-15 11:00:00')

ON DUPLICATE KEY UPDATE
  `judul`            = VALUES(`judul`),
  `kategori`         = VALUES(`kategori`),
  `ringkasan`        = VALUES(`ringkasan`),
  `isi`              = VALUES(`isi`),
  `gambar`           = VALUES(`gambar`),
  `penulis`          = VALUES(`penulis`),
  `status`           = VALUES(`status`),
  `diterbitkan_pada` = VALUES(`diterbitkan_pada`);
