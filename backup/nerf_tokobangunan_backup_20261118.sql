-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: nerf_tokobangunan
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `nerf_tokobangunan`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `nerf_tokobangunan` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `nerf_tokobangunan`;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Super Admin','Admin','Kasir','Warehouse','Customer Service') COLLATE utf8mb4_unicode_ci DEFAULT 'Admin',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'Aktif',
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'Administrator','admin','admin@tokobangunan.com','$2y$10$hqapKnSOeA/hQduzicR1YuFtoHrVmTorMuz/DPdDCbP6NseEZc7KK','Super Admin',NULL,'Aktif','2026-09-22 05:37:12'),(3,'fahri','Fahri','Fahri@gmail.com','$2y$10$NyUtg4kiRFrKBTGjzUjSV.A5meyeEEJvJAtJeC7lBxXcYUZrLHT4y','Admin',NULL,'Aktif','2026-10-06 13:28:01'),(4,'awa','awaa','awada@gmail.com','$2y$10$FaXpzm38NvaLPvIPwGb48.nLTfxhNYWF3VQUlhMCNv2mV4UwGHm0m','Admin','img_6ac5d48158549.png','Aktif','2026-10-07 05:11:29');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alamat_pelanggan`
--

DROP TABLE IF EXISTS `alamat_pelanggan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alamat_pelanggan` (
  `id_alamat` int NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Rumah',
  `nama_penerima` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provinsi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `utama` enum('Ya','Tidak') COLLATE utf8mb4_unicode_ci DEFAULT 'Tidak',
  PRIMARY KEY (`id_alamat`),
  KEY `id_pelanggan` (`id_pelanggan`),
  CONSTRAINT `alamat_pelanggan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alamat_pelanggan`
--

LOCK TABLES `alamat_pelanggan` WRITE;
/*!40000 ALTER TABLE `alamat_pelanggan` DISABLE KEYS */;
INSERT INTO `alamat_pelanggan` VALUES (1,1,'Rumah','Andi Pratama','081234567890','Jl. Merdeka No. 10, RT 02 RW 05','Bandung','Jawa Barat','40115','Ya'),(2,1,'Kantor','Andi Pratama','081234567890','Jl. Sudirman Kav. 5','Jakarta','DKI Jakarta','10220','Tidak'),(3,2,'Rumah','Budi Santoso','081298765432','Jl. Kenanga No. 22','Surabaya','Jawa Timur','60271','Ya'),(4,3,'Rumah','Citra Lestari','085611223344','Jl. Anggrek No. 7','Semarang','Jawa Tengah','50234','Ya'),(5,4,'Rumah','Dimas Nugroho','081377889900','Jl. Mawar No. 3','Yogyakarta','DI Yogyakarta','55223','Ya');
/*!40000 ALTER TABLE `alamat_pelanggan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `artikel`
--

DROP TABLE IF EXISTS `artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artikel` (
  `id_artikel` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Produk',
  `ringkasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penulis` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Admin NERF',
  `dibaca` int NOT NULL DEFAULT '0',
  `status` enum('Terbit','Draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Terbit',
  `dibuat_pada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `diterbitkan_pada` datetime DEFAULT NULL,
  PRIMARY KEY (`id_artikel`),
  UNIQUE KEY `slug` (`slug`),
  KEY `kategori` (`kategori`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `artikel`
--

LOCK TABLES `artikel` WRITE;
/*!40000 ALTER TABLE `artikel` DISABLE KEYS */;
INSERT INTO `artikel` VALUES (1,'Memilih Semen yang Tepat untuk Proyek Rumah Anda','memilih-semen-yang-tepat-untuk-proyek-rumah','Produk','Tidak semua semen sama. Kenali perbedaan semen Portland, semen putih, dan semen cepat keras agar fondasi rumah Anda lebih kuat dan tetap hemat biaya.','<p>Semen adalah material paling dasar dalam sebuah konstruksi. Kesalahan memilih jenis semen bisa berdampak pada kekuatan struktur, biaya, dan umur bangunan. Berikut panduan singkat yang bisa Anda jadikan acuan.</p><h3>1. Semen Portland (PCC)</h3><p>Jenis ini paling umum dipakai untuk pekerjaan umum seperti pondasi, kolom, dan dinding. Daya ikatnya kuat dan harganya paling terjangkau, sehingga cocok untuk hampir seluruh kebutuhan rumah tinggal.</p><h3>2. Semen Putih</h3><p>Semen putih biasanya dipakai untuk finishing, nat keramik, dan pekerjaan dekoratif yang mengutamakan tampilan bersih. Harganya lebih tinggi, jadi gunakan hanya di bagian yang memang terlihat.</p><h3>3. Semen Cepat Keras</h3><p>Untuk proyek dengan waktu mepet, semen cepat keras bisa mempercepat proses pengecoran. Pastikan tenaga kerja siap karena waktu pengolahannya jauh lebih singkat dari semen biasa.</p><p>Tim NERF Toko Bangunan menyediakan semua jenis semen di atas dengan stok siap kirim. Konsultasikan kebutuhan proyek Anda kepada kami agar mendapat rekomendasi yang paling pas.</p>','uploads/artikel/cover-produk.svg','Tim NERF',343,'Terbit','2026-10-02 08:15:00','2026-10-03 09:00:00'),(2,'5 Tips Merawat Besi Beton agar Tidak Berkarat','5-tips-merawat-besi-beton-agar-tidak-berkarat','Tips','Karat bisa melemahkan struktur bangunan. Ikuti lima langkah praktis menyimpan dan merawat besi beton sebelum maupun sesudah pemasangan.','<p>Besi beton yang berkarat kehilangan daya ikat dengan beton dan bisa mengarah pada retakan struktur. Pencegahan jauh lebih murah daripada perbaikan. Ini lima tipsnya.</p><h3>1. Simpan di tempat kering</h3><p>Hindari penumpukan langsung di tanah. Gunakan alas kayu atau palet agar sirkulasi udara tetap baik.</p><h3>2. Tutup saat musim hujan</h3><p>Gunakan terpal kedap air, tetapi tetap beri celah ventilasi supaya lembap tidak terperangkap.</p><h3>3. Pisahkan ukuran dan batch</h3><p>Beri label pada setiap tumpukan besi agar mudah dipantau dan tidak tertukar saat pemakaian.</p><h3>4. Bersihkan sebelum dipakai</h3><p>Sikat permukaan yang berkarat ringan menggunakan sikat kawat sebelum besi dibengkokkan dan dipasang.</p><h3>5. Jaga selimut beton</h3><p>Pastikan ketebalan selimut beton sesuai spesifikasi agar besi tidak cepat terpapar air dan udara.</p><p>Butuh besi beton dengan kualitas terjamin? Lihat pilihan ukuran dan stok terbaru di katalog NERF.</p>','uploads/artikel/cover-tips.svg','Tim NERF',218,'Terbit','2026-10-09 10:20:00','2026-10-10 08:30:00'),(3,'Tren Industri Konstruksi 2026: Material Ramah Lingkungan','tren-industri-konstruksi-2026-material-ramah-lingkungan','Industri','Industri konstruksi bergerak ke arah material hijau. Simak tren beton rendah karbon, baja daur ulang, dan kayu bersertifikat yang mulai banyak dipakai.','<p>Kesadaran akan jejak karbon membuat banyak kontraktor beralih ke material yang lebih ramah lingkungan tanpa mengorbankan kekuatan struktur.</p><h3>Beton rendah karbon</h3><p>Penggunaan substitusi semen seperti fly ash dan slag dapat menurunkan emisi hingga sekitar 30 persen per meter kubik beton.</p><h3>Baja daur ulang</h3><p>Baja bekas yang diolah kembali kini banyak dipakai untuk konstruksi ringan hingga menengah dengan biaya yang lebih efisien.</p><h3>Kayu bersertifikat</h3><p>Sertifikasi hutan lestari memberi jaminan bahwa kayu berasal dari sumber yang dikelola secara bertanggung jawab.</p><p>NERF Toko Bangunan terus menambah pilihan material ramah lingkungan. Hubungi kami untuk informasi ketersediaan dan spesifikasinya.</p>','uploads/artikel/cover-industri.svg','Redaksi NERF',176,'Terbit','2026-10-18 07:45:00','2026-10-20 10:00:00'),(4,'Panduan Memasang Keramik Lantai Sendiri di Rumah','panduan-memasang-keramik-lantai-sendiri-di-rumah','Tips','Lem keset, nat rapi, hasil maksimal. Ikuti panduan lengkap memasang keramik lantai sendiri tanpa harus memanggil tukang profesional.','<p>Memasang keramik sendiri bukan hal mustahil asalkan persiapan dan urutan kerjanya benar. Berikut langkah yang biasa dipakai para profesional.</p><h3>Persiapan permukaan</h3><p>Bersihkan lantai dari debu dan minyak, lalu ratakan bagian yang bergelombang. Lantai yang tidak rata membuat keramik mudah retak.</p><h3>Penentuan patokan</h3><p>Pasang benang atau patokan dari tengah ruangan ke arah dinding supaya pola tetap simetris.</p><h3>Pemasangan dan nat</h3><p>Oleskan lem secara merata, tekan keramik dengan sendokan getar, lalu diamkan sebelum mengisi nat. Gunakan nat yang sesuai warna keramik.</p><p>Pastikan Anda memilih keramik dengan tingkat ketahanan gesek yang sesuai, terutama untuk area basah seperti kamar mandi.</p>','uploads/artikel/cover-tips.svg','Tim NERF',405,'Terbit','2026-10-27 13:10:00','2026-10-28 09:15:00'),(5,'Pipa PVC vs PPR: Mana yang Lebih Cocok untuk Rumah?','pipa-pvc-vs-ppr-mana-yang-lebih-cocok-untuk-rumah','Produk','Membandingkan pipa PVC dan PPR dari segi harga, ketahanan terhadap panas, dan kemudahan pemasangan untuk instalasi air bersih rumah tangga.','<p>Dua jenis pipa ini paling sering dipertimbangkan saat membangun atau merenovasi rumah. Masing-masing punya keunggulan tersendiri.</p><h3>Pipa PVC</h3><p>Harganya lebih ekonomis dan mudah ditemukan. Cocok untuk saluran air dingin dan pembuangan. Pemasangannya memakai lem khusus yang cepat mengeras.</p><h3>Pipa PPR</h3><p>Lebih tahan terhadap suhu tinggi dan tekanan, sehingga aman untuk air panas. Sambungannya dipanaskan dengan alat khusus sehingga hasilnya menyatu.</p><h3>Mana yang dipilih?</h3><p>Untuk instalasi air dingin dengan anggaran terbatas, PVC sudah cukup. Jika Anda membutuhkan jalur air panas atau menginginkan sambungan tanpa lem, PPR adalah pilihan yang lebih baik.</p><p>Keduanya tersedia di NERF Toko Bangunan dalam berbagai diameter. Silakan konsultasikan kebutuhan instalasi Anda.</p>','uploads/artikel/cover-produk.svg','Tim NERF',289,'Terbit','2026-11-03 09:30:00','2026-11-04 08:00:00'),(6,'Harga Material Bangunan Naik, Ini Cara Menghemat Anggaran Proyek','harga-material-bangunan-naik-cara-menghemat-anggaran-proyek','Industri','Biaya material terus meningkat. Simak strategi cermat mulai dari perencanaan, pembelian grosir, sampai memilih alternatif material yang tetap berkualitas.','<p>Kenaikan harga material sering membuat anggaran proyek membengkak. Dengan perencanaan yang matang, dampaknya bisa ditekan tanpa menurunkan kualitas bangunan.</p><h3>Rencanakan kebutuhan sejak awal</h3><p>Hitung volume material berdasarkan gambar kerja. Kelebihan pembelian sering menjadi penyebab utama pemborosan.</p><h3>Beli dalam jumlah besar</h3><p>Pembelian grosir umumnya mendapat harga lebih baik. Untuk proyek skala kecil, Anda bisa bergabung dengan tetangga atau kontraktor di sekitar.</p><h3>Pertimbangkan alternatif</h3><p>Beberapa material lokal kini memiliki kualitas setara dengan produk impor, dengan harga yang lebih bersahabat.</p><p>Pantau informasi harga terkini lewat katalog NERF yang terus diperbarui agar Anda mendapat penawaran terbaik.</p>','uploads/artikel/cover-industri.svg','Redaksi NERF',153,'Terbit','2026-11-06 11:05:00','2026-11-07 10:30:00'),(7,'Cat Eksterior Tahan Lama: Rekomendasi dan Cara Aplikasinya','cat-eksterior-tahan-lama-rekomendasi-dan-cara-aplikasinya','Produk','Cuaca panas dan hujan cepat merusak cat tembok. Pelajari jenis cat eksterior terbaik dan teknik pengecatan agar warna tetap awet bertahun-tahun.','<p>Cat bagian luar rumah menghadapi paparan sinar matahari dan hujan sepanjang tahun. Memilih produk yang tepat akan menghemat biaya pengecatan ulang.</p><h3>Jenis cat yang direkomendasikan</h3><p>Cat akrilik eksterior memiliki daya tahan baik terhadap cuaca dan mudah dibersihkan. Untuk hasil lebih premium, pilih cat dengan teknologi anti-jamur.</p><h3>Persiapan dinding</h3><p>Bersihkan lumut dan lapisan cat lama yang mengelupas. Gunakan plamir atau wall filler agar permukaan rata sebelum diwarnai.</p><h3>Teknik pengecatan</h3><p>Gunakan kuas atau roller dengan gerakan searah. Berikan dua lapis tipis daripada satu lapis tebal agar hasil lebih merata dan cepat kering.</p><p>Kami menyediakan berbagai pilihan cat eksterior dengan warna siap pakai. Cek stok dan harga di katalog produk.</p>','uploads/artikel/cover-produk.svg','Tim NERF',264,'Terbit','2026-11-10 14:40:00','2026-11-11 09:00:00'),(8,'Inovasi Kayu Olahan untuk Konstruksi Modern','inovasi-kayu-olahan-untuk-konstruksi-modern','Industri','Dari kayu laminasi hingga panel komposit, inovasi kayu olahan menawarkan kekuatan tinggi dengan tampilan estetik untuk hunian masa kini.','<p>Kayu tetap menjadi material favorit karena kehangatan tampilannya. Kini berbagai olahan kayu modern mampu mengatasi kelemahan kayu solid tradisional.</p><h3>Kayu laminasi</h3><p>Beberapa lapisan kayu direkatkan membentuk balok dengan kekuatan lebih tinggi dan ukuran lebih panjang dari kayu utuh.</p><h3>Panel komposit</h3><p>Campuran serat kayu dan polimer menghasilkan panel yang tahan lembap serta tidak mudah dimakan rayap.</p><h3>Penggunaan di hunian</h3><p>Material ini cocok untuk rangka atap, lantai, hingga furnitur built in yang membutuhkan tampilan rapi dan konsisten.</p><p>NERF Toko Bangunan menyediakan berbagai pilihan kayu olahan. Hubungi kami untuk informasi ukuran, spesifikasi, dan ketersediaan stok.</p>','uploads/artikel/cover-industri.svg','Redaksi NERF',131,'Terbit','2026-11-13 08:55:00','2026-11-15 11:00:00');
/*!40000 ALTER TABLE `artikel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner` (
  `id_banner` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subjudul` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tombol` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'Aktif',
  `urutan` int DEFAULT '0',
  PRIMARY KEY (`id_banner`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner`
--

LOCK TABLES `banner` WRITE;
/*!40000 ALTER TABLE `banner` DISABLE KEYS */;
INSERT INTO `banner` VALUES (1,'Diskon 20% untuk Semen','Berlaku sampai akhir bulan, stok terbatas.',NULL,'Lihat Produk','Katalog.php?cari=semen','Aktif',1),(2,'Besi Beton Harga Bersaing','Harga terbaik untuk proyek konstruksi Anda.',NULL,'Katalog Besi','Katalog.php?kategori=3','Aktif',2),(3,'Gratis Ongkir','Minimal pembelian Rp 500.000 ke seluruh Indonesia.',NULL,'Cek Katalog','Katalog.php','Aktif',3);
/*!40000 ALTER TABLE `banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_pesanan`
--

DROP TABLE IF EXISTS `detail_pesanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_pesanan` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `id_produk` int NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `jumlah` int NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_pesanan` (`id_pesanan`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_pesanan`
--

LOCK TABLES `detail_pesanan` WRITE;
/*!40000 ALTER TABLE `detail_pesanan` DISABLE KEYS */;
INSERT INTO `detail_pesanan` VALUES (1,1,1,60000.00,2,120000.00),(3,1,6,32000.00,3,96000.00),(4,1,5,95000.00,1,95000.00),(5,2,3,18500.00,10,185000.00),(6,3,1,55000.00,8,440000.00),(8,3,6,32000.00,1,32000.00),(9,4,5,95000.00,1,95000.00),(10,5,2,120000.00,1,120000.00),(11,5,3,18500.00,2,37000.00),(12,6,3,18500.00,1,18500.00),(13,7,2,125000.00,1,125000.00),(14,7,3,18500.00,4,74000.00),(30,9,6,32000.00,5,160000.00),(31,10,6,32000.00,10,320000.00),(32,11,3,18500.00,10,185000.00),(33,12,3,18500.00,1,18500.00),(34,12,2,125000.00,1,125000.00),(35,13,2,125000.00,1,125000.00),(36,14,2,125000.00,1,125000.00),(37,15,3,18500.00,2,37000.00),(38,16,6,32000.00,1,32000.00),(39,17,2,12500000.00,1,12500000.00),(40,18,6,3200000.00,1,3200000.00);
/*!40000 ALTER TABLE `detail_pesanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faq` (
  `id_faq` int NOT NULL AUTO_INCREMENT,
  `pertanyaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jawaban` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'Aktif',
  `urutan` int DEFAULT '0',
  PRIMARY KEY (`id_faq`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faq`
--

LOCK TABLES `faq` WRITE;
/*!40000 ALTER TABLE `faq` DISABLE KEYS */;
INSERT INTO `faq` VALUES (1,'Apakah bisa kirim ke luar Jawa?','Bisa. Kami melayani pengiriman ke seluruh Indonesia. Ongkos kirim dihitung otomatis berdasarkan kurir dan tujuan yang dipilih saat checkout.','Aktif',1),(2,'Berapa lama pesanan diproses?','Pesanan yang pembayarannya sudah diverifikasi kami proses dalam 1 x 24 jam kerja, lalu dikirim pada hari yang sama sebelum pukul 15.00 WIB.','Aktif',2),(3,'Apakah produk bergaransi?','Ya. Semua produk bergaransi resmi dari distributor. Bila barang bermasalah, kirimkan foto dan nomor pesanan ke cs@tokobangunan.com maksimal 7 hari setelah barang diterima.','Aktif',3),(4,'Bagaimana cara melacak pesanan?','Nomor resi muncul di halaman pesanan saya segera setelah paket diserahkan ke kurir. Anda juga bisa mengikuti tracking langsung di situs kurir terkait.','Aktif',4),(5,'Apakah tersedia cicilan?','Untuk pembelian di atas Rp 5.000.000 kami menyediakan cicilan 3x tanpa bunga. Hubungi customer service untuk pengajuan.','Aktif',5);
/*!40000 ALTER TABLE `faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interaksi`
--

DROP TABLE IF EXISTS `interaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interaksi` (
  `id_interaksi` int NOT NULL AUTO_INCREMENT,
  `subjek` enum('Lead','Pelanggan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_subjek` int NOT NULL,
  `jenis` enum('Telepon','WhatsApp','Email','Chat','Kunjungan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `petugas` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Admin',
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_interaksi`),
  KEY `subjek` (`subjek`,`id_subjek`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interaksi`
--

LOCK TABLES `interaksi` WRITE;
/*!40000 ALTER TABLE `interaksi` DISABLE KEYS */;
INSERT INTO `interaksi` VALUES (1,'Lead',2,'Telepon','Menjelaskan pilihan cat eksterior dan harga per kaleng.','Admin','2026-11-06 10:15:00'),(2,'Lead',2,'WhatsApp','Mengirim katalog warna cat Avian.','Admin','2026-11-07 13:20:00'),(3,'Lead',3,'Chat','Mendiskusikan kebutuhan keramik 60x60 untuk renovasi.','Admin','2026-11-09 09:40:00'),(4,'Lead',4,'Kunjungan','Pelanggan datang ke toko dan melakukan pembelian.','Admin','2026-11-10 15:30:00'),(5,'Pelanggan',1,'WhatsApp','Konfirmasi pengiriman pesanan INV-20261117-0B7A87.','Admin','2026-11-17 12:50:00'),(6,'Pelanggan',2,'Email','Mengirim faktur dan informasi garansi cat.','Admin','2026-11-16 11:00:00');
/*!40000 ALTER TABLE `interaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'Aktif',
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori`
--

LOCK TABLES `kategori` WRITE;
/*!40000 ALTER TABLE `kategori` DISABLE KEYS */;
INSERT INTO `kategori` VALUES (1,'Semen','Produk semen untuk kebutuhan konstruksi',NULL,'Aktif','2026-09-22 05:37:16'),(4,'Kayu','Berbagai jenis kayu bangunan',NULL,'Aktif','2026-09-22 05:37:16'),(5,'Cat','Cat tembok dan cat bangunan',NULL,'Aktif','2026-09-22 05:37:16'),(6,'Keramik','Keramik lantai dan dinding',NULL,'Aktif','2026-09-22 05:37:16'),(7,'Pipa & Plumbing','Pipa dan perlengkapan plumbing',NULL,'Aktif','2026-09-22 05:37:16'),(11,'Paku','Berbagai jenis paku untuk konstruksi dan pertukangan',NULL,'Aktif','2026-10-06 14:24:36'),(14,'siswa','dadada',NULL,'Aktif','2026-10-11 13:53:28');
/*!40000 ALTER TABLE `kategori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kunjungan`
--

DROP TABLE IF EXISTS `kunjungan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kunjungan` (
  `id_kunjungan` int NOT NULL AUTO_INCREMENT,
  `tanggal` datetime NOT NULL,
  `halaman` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sumber` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Langsung',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kunjungan`),
  KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kunjungan`
--

LOCK TABLES `kunjungan` WRITE;
/*!40000 ALTER TABLE `kunjungan` DISABLE KEYS */;
INSERT INTO `kunjungan` VALUES (1,'2026-09-01 08:10:00','/beranda.php','Langsung','127.0.0.1','sess_a1','2026-11-17 06:41:06'),(2,'2026-09-01 08:12:00','/katalog.php','Langsung','127.0.0.1','sess_a1','2026-11-17 06:41:06'),(3,'2026-09-02 10:25:00','/katalog.php','Google','127.0.0.2','sess_b2','2026-11-17 06:41:06'),(4,'2026-09-03 13:40:00','/blog.php','Instagram','127.0.0.3','sess_c3','2026-11-17 06:41:06'),(5,'2026-09-05 09:05:00','/masuk.php','Langsung','127.0.0.1','sess_a1','2026-11-17 06:41:06'),(6,'2026-09-07 15:30:00','/customer-service.php','WhatsApp','127.0.0.4','sess_d4','2026-11-17 06:41:06'),(7,'2026-09-10 11:15:00','/beranda.php','Google','127.0.0.5','sess_e5','2026-11-17 06:41:06'),(8,'2026-09-12 14:20:00','/katalog.php?kategori=1','Google','127.0.0.5','sess_e5','2026-11-17 06:41:06'),(9,'2026-09-15 16:45:00','/artikel.php?slug=memilih-semen','Instagram','127.0.0.6','sess_f6','2026-11-17 06:41:06'),(10,'2026-09-18 09:50:00','/beranda.php','Referral','127.0.0.7','sess_g7','2026-11-17 06:41:06'),(11,'2026-09-20 19:10:00','/katalog.php','Langsung','127.0.0.8','sess_h8','2026-11-17 06:41:06'),(12,'2026-09-20 19:14:00','/daftar.php','Langsung','127.0.0.8','sess_h8','2026-11-17 06:41:06'),(13,'2026-10-02 08:35:00','/beranda.php','Google','127.0.0.9','sess_i9','2026-11-17 06:41:06'),(14,'2026-10-04 10:00:00','/katalog.php','Google','127.0.0.9','sess_i9','2026-11-17 06:41:06'),(15,'2026-10-06 12:20:00','/blog.php','Instagram','127.0.0.10','sess_j10','2026-11-17 06:41:06'),(16,'2026-10-09 11:38:00','/masuk.php','Langsung','127.0.0.11','sess_k11','2026-11-17 06:41:06'),(17,'2026-10-11 20:55:00','/beranda.php','WhatsApp','127.0.0.12','sess_l12','2026-11-17 06:41:06'),(18,'2026-10-14 09:40:00','/katalog.php?kategori=6','Google','127.0.0.13','sess_m13','2026-11-17 06:41:06'),(19,'2026-10-18 15:05:00','/customer-service.php','Langsung','127.0.0.14','sess_n14','2026-11-17 06:41:06'),(20,'2026-10-22 13:30:00','/artikel.php?slug=pipa-pvc-vs-ppr','Instagram','127.0.0.15','sess_o15','2026-11-17 06:41:06'),(21,'2026-11-02 08:45:00','/beranda.php','Langsung','127.0.0.16','sess_p16','2026-11-17 06:41:06'),(22,'2026-11-04 10:10:00','/katalog.php','Google','127.0.0.17','sess_q17','2026-11-17 06:41:06'),(23,'2026-11-08 14:25:00','/blog.php','Instagram','127.0.0.18','sess_r18','2026-11-17 06:41:06'),(24,'2026-11-10 09:15:00','/pengaturan.php','Langsung','127.0.0.16','sess_p16','2026-11-17 06:41:06'),(25,'2026-11-14 16:40:00','/katalog.php?kategori=3','Referral','127.0.0.19','sess_s19','2026-11-17 06:41:06'),(26,'2026-11-16 11:00:00','/pesanan-saya.php','Langsung','127.0.0.20','sess_t20','2026-11-17 06:41:06'),(34,'2026-11-18 22:38:39','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:38:39'),(35,'2026-11-18 22:38:43','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:38:43'),(36,'2026-11-18 22:38:54','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:38:54'),(37,'2026-11-18 22:39:00','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:00'),(38,'2026-11-18 22:39:00','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:00'),(39,'2026-11-18 22:39:08','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:08'),(40,'2026-11-18 22:39:37','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:37'),(41,'2026-11-18 22:39:44','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:44'),(42,'2026-11-18 22:39:47','/katalog.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:47'),(43,'2026-11-18 22:39:49','/blog.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:49'),(44,'2026-11-18 22:39:51','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:51'),(45,'2026-11-18 22:39:54','/customer-service.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:54'),(46,'2026-11-18 22:39:55','/pesanan-saya.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:55'),(47,'2026-11-18 22:39:56','/testimoni.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:39:56'),(48,'2026-11-18 22:40:02','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:40:02'),(51,'2026-11-18 22:42:19','/masuk.php','Langsung','::1','sess_5608e6fa6f32351435c97356891ac8e1','2026-11-18 15:42:19'),(52,'2026-11-18 22:44:07','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:44:07'),(53,'2026-11-18 22:44:14','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:44:14'),(54,'2026-11-18 22:44:16','/katalog.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:44:16'),(55,'2026-11-18 22:44:17','/blog.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:44:17'),(56,'2026-11-18 22:44:18','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:44:18'),(57,'2026-11-18 22:44:21','/customer-service.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:44:21'),(58,'2026-11-18 22:44:22','/pesanan-saya.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:44:22'),(59,'2026-11-18 22:44:22','/testimoni.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:44:22'),(60,'2026-11-18 22:44:27','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:44:27'),(61,'2026-11-18 22:47:24','/masuk.php','Langsung','127.0.0.1','sess_34449b5131dffc978c2bb058f27ad174','2026-11-18 15:47:24'),(62,'2026-11-18 22:48:50','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:48:50'),(63,'2026-11-18 22:48:59','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:48:59'),(64,'2026-11-18 22:49:02','/katalog.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:02'),(65,'2026-11-18 22:49:03','/blog.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:03'),(66,'2026-11-18 22:49:03','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:03'),(67,'2026-11-18 22:49:08','/customer-service.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:08'),(68,'2026-11-18 22:49:10','/pesanan-saya.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:10'),(69,'2026-11-18 22:49:11','/testimoni.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:11'),(70,'2026-11-18 22:49:16','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:16'),(71,'2026-11-18 22:49:18','/katalog.php?cari=KAY001','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:18'),(72,'2026-11-18 22:49:21','/katalog.php?detail=6','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:21'),(73,'2026-11-18 22:49:23','/katalog.php?detail=6','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:23'),(74,'2026-11-18 22:49:24','/katalog.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:24'),(75,'2026-11-18 22:49:29','/katalog.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:29'),(76,'2026-11-18 22:49:41','/kebijakan-privasi.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:41'),(77,'2026-11-18 22:49:48','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 15:49:48'),(78,'2026-11-18 23:13:48','/masuk.php','Langsung','127.0.0.1','sess_7ac4e07abbdcd09c648ad46af7c0ca12','2026-11-18 16:13:48'),(79,'2026-11-18 23:21:57','/masuk.php','Langsung','127.0.0.1','sess_215085bf0c886f6f10e6fbda053dd354','2026-11-18 16:21:57'),(80,'2026-11-18 23:22:51','/masuk.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:51'),(81,'2026-11-18 23:22:52','/beranda.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:52'),(82,'2026-11-18 23:22:52','/katalog.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:52'),(83,'2026-11-18 23:22:52','/blog.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:52'),(84,'2026-11-18 23:22:52','/artikel.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:52'),(85,'2026-11-18 23:22:52','/masuk.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:52'),(86,'2026-11-18 23:22:53','/daftar.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:53'),(87,'2026-11-18 23:22:53','/customer-service.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:53'),(88,'2026-11-18 23:22:53','/checkout.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:53'),(89,'2026-11-18 23:22:53','/pesanan-saya.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:53'),(90,'2026-11-18 23:22:53','/testimoni.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:53'),(91,'2026-11-18 23:22:53','/kebijakan-privasi.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:53'),(92,'2026-11-18 23:22:53','/syarat-ketentuan.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:53'),(93,'2026-11-18 23:22:53','/pengaturan.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:53'),(94,'2026-11-18 23:22:53','/lupa-sandi.php','Langsung','127.0.0.1','sess_628e589acc30811d6ce187d3c74baea8','2026-11-18 16:22:53'),(95,'2026-11-18 23:24:55','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:24:55'),(96,'2026-11-18 23:25:03','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:25:03'),(97,'2026-11-18 23:25:07','/katalog.php?cari=KAY001','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:25:07'),(98,'2026-11-18 23:25:10','/katalog.php?detail=6','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:25:10'),(99,'2026-11-18 23:25:13','/checkout.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:25:13'),(100,'2026-11-18 23:26:21','/pesanan-saya.php?baru=18','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:26:21'),(101,'2026-11-18 23:26:24','/pesanan-saya.php?detail=18','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:26:24'),(102,'2026-11-18 23:26:31','/pesanan-saya.php?detail=18','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:26:31'),(103,'2026-11-18 23:26:45','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:26:45'),(104,'2026-11-18 23:27:08','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:27:08'),(105,'2026-11-18 23:27:17','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:27:17'),(106,'2026-11-18 23:27:20','/pesanan-saya.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:27:20'),(107,'2026-11-18 23:27:22','/pesanan-saya.php?detail=18','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:27:22'),(108,'2026-11-18 23:27:25','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:27:25'),(109,'2026-11-18 23:28:10','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:28:10'),(110,'2026-11-18 23:28:24','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:28:24'),(111,'2026-11-18 23:28:26','/pesanan-saya.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:28:26'),(112,'2026-11-18 23:28:29','/pesanan-saya.php?detail=18','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:28:29'),(113,'2026-11-18 23:28:40','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:28:40'),(114,'2026-11-18 23:29:24','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:29:24'),(115,'2026-11-18 23:29:32','/beranda.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:29:32'),(116,'2026-11-18 23:29:34','/pesanan-saya.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:29:34'),(117,'2026-11-18 23:29:36','/pesanan-saya.php?detail=18','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:29:36'),(118,'2026-11-18 23:29:50','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:29:50'),(119,'2026-11-18 23:33:26','/masuk.php','Langsung','127.0.0.1','sess_0212c7c5e386f9c8eaa09328955a20a7','2026-11-18 16:33:26'),(120,'2026-11-18 23:33:26','/lupa-sandi.php','Langsung','127.0.0.1','sess_7307d90296d74332592fa136042de626','2026-11-18 16:33:26'),(121,'2026-11-18 23:33:53','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:33:53'),(122,'2026-11-18 23:34:42','/lupa-sandi.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:34:42'),(123,'2026-11-18 23:34:44','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:34:44'),(124,'2026-11-18 23:34:46','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:34:46'),(125,'2026-11-18 23:34:47','/masuk.php','Langsung','::1','sess_b1955ff94a7cd9504ca549c61903c31b','2026-11-18 16:34:47');
/*!40000 ALTER TABLE `kunjungan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id_lead` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sumber` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Website',
  `status` enum('Baru','Dihubungi','Tertarik','Pelanggan','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Baru',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `dibuat_pada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `diubah_pada` datetime DEFAULT NULL,
  PRIMARY KEY (`id_lead`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,'Rizky Ananda','rizky@example.com','081211110001','Website','Baru','Bertanya harga semen Holcim 50 Kg.','2026-11-02 09:20:00',NULL),(2,'Nadia Putri','nadia@example.com','081211110002','Instagram','Dihubungi','Tertarik dengan cat eksterior.','2026-11-05 11:45:00',NULL),(3,'Farhan Maulana','farhan@example.com','081211110003','WhatsApp','Tertarik','Rencana renovasi rumah, butuh keramik 60x60.','2026-11-08 14:10:00',NULL),(4,'Siti Aminah','siti@example.com','081211110004','Referral','Pelanggan','Sudah melakukan pembelian paku baja.','2026-11-10 08:30:00',NULL),(5,'Dewi Sartika','dewi@example.com','081211110005','Walk-in','Baru','Datang ke toko menanyakan pipa PPR.','2026-11-12 16:05:00',NULL),(6,'Agus Setiawan','agus@example.com','081211110006','Iklan','Ditolak','Membutuhkan material di luar area layanan.','2026-11-14 10:00:00',NULL),(8,'Rio','rio@mail.com','','Website','Tertarik','rio tertarik','2026-11-17 13:38:00',NULL);
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ongkir`
--

DROP TABLE IF EXISTS `ongkir`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ongkir` (
  `id_ongkir` int NOT NULL AUTO_INCREMENT,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT '0.00',
  `estimasi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'Aktif',
  PRIMARY KEY (`id_ongkir`),
  UNIQUE KEY `kota` (`kota`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ongkir`
--

LOCK TABLES `ongkir` WRITE;
/*!40000 ALTER TABLE `ongkir` DISABLE KEYS */;
INSERT INTO `ongkir` VALUES (1,'Bandung',0.00,'Sampai hari yang sama','Aktif'),(2,'Bandung Raya',15000.00,'1 hari kerja','Aktif'),(3,'Cimahi',25000.00,'1 hari kerja','Aktif'),(4,'Sumedang',35000.00,'1-2 hari kerja','Aktif'),(5,'Garut',40000.00,'1-2 hari kerja','Aktif'),(6,'Tasikmalaya',35000.00,'1-2 hari kerja','Aktif'),(7,'Jakarta',60000.00,'2-3 hari kerja','Aktif'),(8,'Bandung Raya +',0.00,'Di luar daftar, hubungi admin','Nonaktif');
/*!40000 ALTER TABLE `ongkir` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opsi_produk`
--

DROP TABLE IF EXISTS `opsi_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `opsi_produk` (
  `id_opsi` int NOT NULL AUTO_INCREMENT,
  `id_produk` int NOT NULL,
  `kelompok` enum('Warna','Ukuran') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ukuran',
  `nilai` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_opsi` decimal(15,2) DEFAULT NULL,
  `stok_opsi` int DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_opsi`),
  KEY `fk_opsi_produk` (`id_produk`),
  CONSTRAINT `opsi_produk_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opsi_produk`
--

LOCK TABLES `opsi_produk` WRITE;
/*!40000 ALTER TABLE `opsi_produk` DISABLE KEYS */;
INSERT INTO `opsi_produk` VALUES (1,11,'Ukuran','3 cm',11000.00,40,1),(2,11,'Ukuran','5 cm',13000.00,30,2),(3,11,'Ukuran','7 cm',15000.00,20,3),(4,11,'Ukuran','10 cm',18000.00,10,4),(5,12,'Ukuran','3 cm',14000.00,30,1),(6,12,'Ukuran','5 cm',15500.00,25,2),(7,12,'Ukuran','7 cm',17500.00,15,3),(8,12,'Ukuran','10 cm',21000.00,10,4),(9,13,'Ukuran','3 cm',5000.00,25,1),(10,13,'Ukuran','5 cm',7000.00,20,2),(11,13,'Ukuran','7 cm',9000.00,10,3),(12,13,'Ukuran','10 cm',12000.00,5,4),(21,2,'Warna','Putih',NULL,NULL,1),(22,2,'Warna','Biru',NULL,NULL,2),(23,2,'Warna','Merah',NULL,NULL,3);
/*!40000 ALTER TABLE `opsi_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pelanggan`
--

DROP TABLE IF EXISTS `pelanggan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pelanggan` (
  `id_pelanggan` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'Aktif',
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pelanggan`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pelanggan`
--

LOCK TABLES `pelanggan` WRITE;
/*!40000 ALTER TABLE `pelanggan` DISABLE KEYS */;
INSERT INTO `pelanggan` VALUES (1,'Andi Pratama','andi@mail.com','$2y$10$HJymt97jlz8sxHWqF50esOGw8d5dnjTshm.Q8OKsVOYkp7Vzzf36q','081234567890',NULL,'Aktif','2026-10-06 13:11:07'),(2,'Budi Santoso','budi@mail.com','$2y$10$FvSbOP34gT2cB9Iu2nDsCOkQdOyVIAEdDtqoTyJwXg7AgTe7h.KA6','081298765432',NULL,'Aktif','2026-10-06 13:11:07'),(3,'Citra Lestari','citra@mail.com','$2y$10$Cd3LhEOuh7jWuTRu4drnL.oMBR6otBbBDwWVXSdqUEYqmNYfT2IUu','085611223344',NULL,'Aktif','2026-10-06 13:11:07'),(4,'Dimas Nugroho','dimas@mail.com','$2y$10$8WcOcuqIXoNLKsqyAjT1.eRmP9USOXaLqNd.UkGyGNyS.Dkl50shW','081377889900',NULL,'Aktif','2026-10-06 13:11:07'),(5,'Ahmad','ahmad@mail.com','$2y$10$kWFp.hiqT3KMkxiRrzsD7u1wl4W3JcXhPJ92yE15reLGk2rdg56ey','088121212121',NULL,'Aktif','2026-10-06 13:16:56');
/*!40000 ALTER TABLE `pelanggan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembayaran` (
  `id_pembayaran` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `metode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pembayaran` timestamp NULL DEFAULT NULL,
  `status` enum('Menunggu','Diverifikasi','Ditolak') COLLATE utf8mb4_unicode_ci DEFAULT 'Menunggu',
  PRIMARY KEY (`id_pembayaran`),
  KEY `id_pesanan` (`id_pesanan`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembayaran`
--

LOCK TABLES `pembayaran` WRITE;
/*!40000 ALTER TABLE `pembayaran` DISABLE KEYS */;
INSERT INTO `pembayaran` VALUES (1,1,'Transfer Bank',474000.00,NULL,'2026-09-01 03:45:00','Diverifikasi'),(2,2,'E-Wallet',203000.00,NULL,'2026-09-05 07:50:00','Diverifikasi'),(3,3,'Transfer Bank',452000.00,NULL,'2026-09-10 02:30:00','Diverifikasi'),(4,4,'COD',110000.00,NULL,'2026-09-15 09:40:00','Diverifikasi'),(5,5,'Transfer Bank',167000.00,NULL,'2026-09-20 05:00:00','Menunggu'),(6,6,'E-Wallet',27500.00,NULL,NULL,'Menunggu'),(7,7,'Transfer Bank',221000.00,NULL,'2026-09-23 06:30:00','Menunggu'),(8,8,'COD',76000.00,NULL,NULL,'Ditolak'),(21,9,'Transfer Bank',160000.00,NULL,'2026-10-09 04:40:29','Menunggu'),(22,10,'Transfer Bank',320000.00,NULL,NULL,'Diverifikasi'),(23,11,'Transfer Bank',185000.00,NULL,NULL,'Diverifikasi'),(24,12,'Bayar di Tempat (COD)',143500.00,NULL,NULL,'Diverifikasi'),(25,13,'Bayar di Tempat (COD)',125000.00,NULL,NULL,'Diverifikasi'),(26,14,'Transfer Bank',125000.00,NULL,NULL,'Diverifikasi'),(27,15,'Transfer Bank',37000.00,NULL,'2026-11-17 05:41:12','Diverifikasi'),(28,16,'Transfer Bank',32000.00,NULL,'2026-11-17 05:45:42','Diverifikasi'),(29,17,'Bayar di Tempat (COD)',12500000.00,NULL,NULL,'Diverifikasi'),(30,18,'Transfer Bank',3200000.00,NULL,'2026-11-18 16:26:31','Diverifikasi');
/*!40000 ALTER TABLE `pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengaturan`
--

DROP TABLE IF EXISTS `pengaturan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengaturan` (
  `id_pengaturan` int NOT NULL AUTO_INCREMENT,
  `kunci` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_pengaturan`),
  UNIQUE KEY `kunci` (`kunci`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaturan`
--

LOCK TABLES `pengaturan` WRITE;
/*!40000 ALTER TABLE `pengaturan` DISABLE KEYS */;
INSERT INTO `pengaturan` VALUES (1,'nama_toko','NERF Toko Bangunan'),(2,'alamat_toko','Jl. Merdeka No. 10, Bandung'),(3,'telepon_toko','0812-3456-7890'),(4,'nama_bank','Bank BCA'),(5,'nomor_rekening','8720-1188-9'),(6,'atas_nama_rekening','PT NERF Bangunan Perkasa'),(7,'qris_nama','NERF Toko Bangunan'),(8,'qris_gambar','qris.png');
/*!40000 ALTER TABLE `pengaturan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengeluaran`
--

DROP TABLE IF EXISTS `pengeluaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int NOT NULL AUTO_INCREMENT,
  `tanggal` datetime NOT NULL,
  `kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `metode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Tunai',
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengeluaran`),
  KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengeluaran`
--

LOCK TABLES `pengeluaran` WRITE;
/*!40000 ALTER TABLE `pengeluaran` DISABLE KEYS */;
INSERT INTO `pengeluaran` VALUES (1,'2026-09-02 09:00:00','Pembelian Stok','Pembelian semen dan cat dari supplier',300000.00,'Transfer','2026-11-17 06:22:37'),(2,'2026-09-10 08:30:00','Listrik','Tagihan listrik toko bulan September',85000.00,'Tunai','2026-11-17 06:22:37'),(3,'2026-09-15 17:00:00','Gaji Karyawan','Gaji karyawan toko September',250000.00,'Transfer','2026-11-17 06:22:37'),(4,'2026-10-03 10:00:00','Pembelian Stok','Pembelian kayu dan paku',320000.00,'Transfer','2026-11-17 06:22:37'),(5,'2026-10-10 08:30:00','Listrik','Tagihan listrik toko bulan Oktober',90000.00,'Tunai','2026-11-17 06:22:37'),(6,'2026-10-15 17:00:00','Gaji Karyawan','Gaji karyawan toko Oktober',250000.00,'Transfer','2026-11-17 06:22:37'),(7,'2026-10-20 14:00:00','Pengiriman','Biaya antar barang pelanggan',35000.00,'Tunai','2026-11-17 06:22:37'),(8,'2026-11-04 09:00:00','Pembelian Stok','Pembelian keras LP dan semen',15000.00,'Tunai','2026-11-17 06:22:37'),(9,'2026-11-10 08:30:00','Listrik','Tagihan listrik toko bulan November',18000.00,'Tunai','2026-11-17 06:22:37'),(10,'2026-11-15 17:00:00','Gaji Karyawan','Gaji karyawan toko November (setengah bulan)',25000.00,'Transfer','2026-11-17 06:22:37');
/*!40000 ALTER TABLE `pengeluaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengiriman`
--

DROP TABLE IF EXISTS `pengiriman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengiriman` (
  `id_pengiriman` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `kurir` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_resi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ongkir` decimal(15,2) DEFAULT '0.00',
  `status` enum('Menunggu','Diproses','Dikirim','Sampai') COLLATE utf8mb4_unicode_ci DEFAULT 'Menunggu',
  PRIMARY KEY (`id_pengiriman`),
  KEY `id_pesanan` (`id_pesanan`),
  CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengiriman`
--

LOCK TABLES `pengiriman` WRITE;
/*!40000 ALTER TABLE `pengiriman` DISABLE KEYS */;
INSERT INTO `pengiriman` VALUES (1,1,'JNE','JNE123456789',25000.00,'Sampai'),(2,2,'GoSend','GSN987654321',18000.00,'Sampai'),(3,3,'J&T Express','JNT555444333',30000.00,'Sampai'),(4,4,'SiCepat','SCP111222333',15000.00,'Dikirim'),(5,7,'JNE','JNE777888999',22000.00,'Diproses'),(15,16,NULL,NULL,0.00,'Menunggu'),(16,18,NULL,NULL,0.00,'Menunggu');
/*!40000 ALTER TABLE `pengiriman` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pesanan`
--

DROP TABLE IF EXISTS `pesanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL AUTO_INCREMENT,
  `kode_pesanan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pelanggan` int NOT NULL,
  `id_alamat` int DEFAULT NULL,
  `nama_penerima` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pesanan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subtotal` decimal(15,2) DEFAULT '0.00',
  `ongkir` decimal(15,2) DEFAULT '0.00',
  `diskon` decimal(15,2) DEFAULT '0.00',
  `total` decimal(15,2) DEFAULT '0.00',
  `metode_pembayaran` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metode_pengiriman` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pesanan` enum('Menunggu Pembayaran','Dibayar','Diproses','Sedang Disiapkan','Packing','Di Antar','Dikirim','Selesai','Dibatalkan','Retur') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Menunggu Pembayaran',
  PRIMARY KEY (`id_pesanan`),
  UNIQUE KEY `kode_pesanan` (`kode_pesanan`),
  KEY `id_pelanggan` (`id_pelanggan`),
  CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pesanan`
--

LOCK TABLES `pesanan` WRITE;
/*!40000 ALTER TABLE `pesanan` DISABLE KEYS */;
INSERT INTO `pesanan` VALUES (1,'INV-20260901-001',1,1,'Andi Pratama','081234567890','Jl. Merdeka No. 10, RT 02 RW 05','Bandung',NULL,'2026-09-01 03:15:00',461000.00,25000.00,12000.00,324000.00,'Transfer Bank','Ekspedisi','Selesai'),(2,'INV-20260905-002',2,3,'Budi Santoso','081298765432','Jl. Kenanga No. 22','Surabaya',NULL,'2026-09-05 07:30:00',185000.00,18000.00,0.00,203000.00,'E-Wallet','Sameday','Selesai'),(3,'INV-20260910-003',1,1,'Andi Pratama','081234567890','Jl. Merdeka No. 10, RT 02 RW 05','Bandung',NULL,'2026-09-10 02:05:00',622000.00,30000.00,200000.00,302000.00,'Transfer Bank','Ekspedisi','Selesai'),(4,'INV-20260915-004',3,4,'Citra Lestari','085611223344','Jl. Anggrek No. 7','Semarang',NULL,'2026-09-15 09:20:00',95000.00,15000.00,0.00,110000.00,'COD','Ekspedisi','Dikirim'),(5,'INV-20260920-005',2,3,'Budi Santoso','081298765432','Jl. Kenanga No. 22','Surabaya',NULL,'2026-09-20 04:40:00',157000.00,20000.00,10000.00,167000.00,'Transfer Bank','Ekspedisi','Diproses'),(6,'INV-20260922-006',1,1,'Andi Pratama','081234567890','Jl. Merdeka No. 10, RT 02 RW 05','Bandung',NULL,'2026-09-22 01:00:00',18500.00,9000.00,0.00,27500.00,'E-Wallet','Sameday','Menunggu Pembayaran'),(7,'INV-20260923-007',3,4,'Citra Lestari','085611223344','Jl. Anggrek No. 7','Semarang',NULL,'2026-09-23 06:10:00',199000.00,22000.00,0.00,221000.00,'Transfer Bank','Ekspedisi','Packing'),(8,'INV-20260924-008',2,3,'Budi Santoso','081298765432','Jl. Kenanga No. 22','Surabaya',NULL,'2026-09-24 03:00:00',64000.00,12000.00,0.00,76000.00,'COD','Ekspedisi','Dibatalkan'),(9,'INV-20261009-328259',5,0,'Ahmad','088121212121','Jl. Merdeka No. 10, Bandung','Bandung','','2026-10-09 04:38:26',160000.00,0.00,0.00,160000.00,'Transfer Bank','Ambil di Toko','Dibatalkan'),(10,'INV-20261009-B508D1',5,0,'Ahmad','088121212121','Jl. Merdeka No. 10, Bandung','Bandung','','2026-10-09 04:42:09',320000.00,0.00,0.00,320000.00,'Transfer Bank','Ambil di Toko','Dibayar'),(11,'INV-20261009-E466A3',5,0,'Ahmad','088121212121','Jl. Merdeka No. 10, Bandung','Bandung','','2026-10-09 04:49:57',185000.00,0.00,0.00,185000.00,'Transfer Bank','Ambil di Toko','Dibayar'),(12,'INV-20261009-4D01DC',5,0,'Ahmad','088121212121','Jl. Merdeka No. 10, Bandung','','','2026-10-09 14:51:05',143500.00,0.00,0.00,143500.00,'Bayar di Tempat (COD)','Ambil di Toko','Dibayar'),(13,'INV-20261009-BD53F5',5,0,'Ahmad','088121212121','Jl. Merdeka No. 10, Bandung','','','2026-10-09 14:55:53',125000.00,0.00,0.00,125000.00,'Bayar di Tempat (COD)','Ambil di Toko','Dibayar'),(14,'INV-20261011-1453D5',5,0,'Ahmad','088121212121','Jl. Merdeka No. 10, Bandung','','','2026-10-11 13:57:03',125000.00,0.00,0.00,125000.00,'Transfer Bank','Ambil di Toko','Di Antar'),(15,'INV-20261117-0B7A87',5,0,'Ahmad','088121212121','Jl. Merdeka No. 10, Bandung','','','2026-11-17 05:40:43',37000.00,0.00,0.00,37000.00,'Transfer Bank','Ambil di Toko','Dibayar'),(16,'INV-20261117-D1E800',5,0,'Elang','088211121910','Jl. Mayjend Sutoyo','','','2026-11-17 05:44:47',32000.00,0.00,0.00,32000.00,'Transfer Bank','Diantar ke Alamat','Dibayar'),(17,'INV-20261118-2A5C15',5,0,'Ahmad','088121212121','Jl. Merdeka No. 10, Bandung','','','2026-11-18 15:20:15',12500000.00,0.00,0.00,12500000.00,'Bayar di Tempat (COD)','Ambil di Toko','Dibayar'),(18,'INV-20261118-77B96A',5,0,'Elang','088211121910','hhooii','','','2026-11-18 16:26:21',3200000.00,0.00,0.00,3200000.00,'Transfer Bank','Diantar ke Alamat','Selesai');
/*!40000 ALTER TABLE `pesanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preferensi_notifikasi`
--

DROP TABLE IF EXISTS `preferensi_notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preferensi_notifikasi` (
  `id_pelanggan` int NOT NULL,
  `notif_email_pesanan` tinyint(1) NOT NULL DEFAULT '1',
  `notif_wa_pesanan` tinyint(1) NOT NULL DEFAULT '1',
  `notif_email_promo` tinyint(1) NOT NULL DEFAULT '0',
  `notif_email_pembayaran` tinyint(1) NOT NULL DEFAULT '1',
  `diperbarui_pada` datetime DEFAULT NULL,
  PRIMARY KEY (`id_pelanggan`),
  CONSTRAINT `fk_pref_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preferensi_notifikasi`
--

LOCK TABLES `preferensi_notifikasi` WRITE;
/*!40000 ALTER TABLE `preferensi_notifikasi` DISABLE KEYS */;
INSERT INTO `preferensi_notifikasi` VALUES (1,1,1,0,1,NULL),(2,1,1,0,1,NULL),(3,1,1,0,1,NULL),(4,1,1,0,1,NULL),(5,1,1,0,1,NULL);
/*!40000 ALTER TABLE `preferensi_notifikasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produk`
--

DROP TABLE IF EXISTS `produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk` (
  `id_produk` int NOT NULL AUTO_INCREMENT,
  `id_kategori` int NOT NULL,
  `kode_produk` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_produk` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `spesifikasi` text COLLATE utf8mb4_unicode_ci,
  `harga` decimal(15,2) DEFAULT '0.00',
  `harga_diskon` decimal(15,2) DEFAULT '0.00',
  `stok` int DEFAULT '0',
  `stok_minimum` int DEFAULT '5',
  `satuan` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'Pcs',
  `warna` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ukuran` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat` decimal(10,2) DEFAULT '0.00',
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'Aktif',
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produk`),
  UNIQUE KEY `kode_produk` (`kode_produk`),
  KEY `id_kategori` (`id_kategori`),
  CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produk`
--

LOCK TABLES `produk` WRITE;
/*!40000 ALTER TABLE `produk` DISABLE KEYS */;
INSERT INTO `produk` VALUES (1,1,'SMN001','Semen Tiga Roda 50 Kg','semen-tiga-roda-50-kg','Semen berkualitas untuk berbagai kebutuhan konstruksi.','',6200000.00,0.00,100,10,'Sak','','',0.00,'img_6afdc0ea7b93f.jpeg','Aktif','2026-09-22 05:37:16'),(2,5,'CAT001','Cat Avian 5 Kg','cat-avian-5-kg','Cat berkualitas untuk dinding dan bangunan.','',12500000.00,0.00,99,10,'Kaleng','Putih, Biru, Merah','',0.00,'img_6afdc09f905ab.jpeg','Aktif','2026-09-22 05:37:16'),(3,7,'PIP001','Pipa PVC 1/2 Inch','pipa-pvc-1-2-inch','Pipa PVC untuk kebutuhan saluran air.','',1850000.00,0.00,67,15,'Pcs','','',0.00,'img_6afdbe32a6a9a.jpeg','Aktif','2026-09-22 05:37:16'),(5,6,'KRM001','Keramik Roman 60x60','keramik-roman-60x60','Keramik lantai ukuran 60x60 cm.','',9500000.00,0.00,40,10,'Box','','',0.00,'img_6afdbeb26f965.jpeg','Aktif','2026-09-22 05:37:16'),(6,4,'KAY001','Kayu Balok 4x6','kayu-balok-4x6','Kayu balok untuk kebutuhan konstruksi.','',3200000.00,0.00,58,10,'Batang','','',0.00,'img_6afdbec8b6d16.jpeg','Aktif','2026-09-22 05:37:16'),(9,1,'SMN002','Semen Holcim 50 Kg','semen-holcim-50-kg','Semen Portland Holcim kualitas premium untuk beton struktural, pasangan dinding, dan plester.','Berat 50 Kg per sak\r\nWarna abu-abu muda\r\nKemasan sack',6100000.00,0.00,100,10,'Sak','','',50.00,'img_6afdc0dad93bc.jpeg','Aktif','2026-10-06 14:14:15'),(10,1,'SMN003','Semen Gresik 40 Kg','semen-gresik-40-kg','Semen umum untuk kebutuhan konstruksi harian dan pekerjaan non-struktural. Harga terjangkau dan mudah diaplikasi.','Berat 40 Kg per sak\r\nWarna abu-abu muda\r\nKemasan sack',5800000.00,0.00,80,10,'Sak','','',40.00,'img_6afdc0bf8ddec.jpeg','Aktif','2026-10-06 14:14:15'),(11,11,'PKU001','Paku Biasa','paku-biasa','Paku baja ringan untuk kayu.','Bahan: baja soft',130000000.00,0.00,100,10,'Kg','','3 cm, 5 cm, 7 cm, 10 cm',1.00,'img_6afdbda00c63f.jpeg','Aktif','2026-10-06 14:26:07'),(12,11,'PKU002','Paku Payung','paku-payung','Paku payung / roofing nail untuk pemasangan genteng beton dan seng. Ukuran dapat dipilih saat pemesanan.','Kepala lebar bentuk payung\r\nSatuan: Kg',900000.00,0.00,80,10,'Kg','','3 cm, 5 cm, 7 cm, 10 cm',1.00,'img_6afdbdd87ecf2.jpeg','Aktif','2026-10-06 14:26:07'),(13,11,'PKU003','Paku Baja','paku-baja','Paku baja keras (concrete nail) untuk menusuk beton, dinding bata, dan batu. Ukuran dapat dipilih saat pemesanan.','Bahan: baja keras\r\nSatuan: Kg',100000000.00,0.00,60,10,'Kg','','3 cm, 5 cm, 7 cm, 10 cm',1.00,'img_6afdbdef86bba.jpeg','Aktif','2026-10-06 14:26:07');
/*!40000 ALTER TABLE `produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `retur`
--

DROP TABLE IF EXISTS `retur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `retur` (
  `id_retur` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Menunggu','Diproses','Disetujui','Ditolak','Selesai') COLLATE utf8mb4_unicode_ci DEFAULT 'Menunggu',
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_retur`),
  KEY `id_pesanan` (`id_pesanan`),
  CONSTRAINT `retur_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retur`
--

LOCK TABLES `retur` WRITE;
/*!40000 ALTER TABLE `retur` DISABLE KEYS */;
INSERT INTO `retur` VALUES (1,5,'Ukuran sak semen yang dikirim tidak sesuai pesanan, pelanggan meminta ganti ukuran 40 Kg.',NULL,'Menunggu','2026-09-21 03:00:00'),(2,2,'Pipa PVC yang datang penyok dan bocor saat pemasangan.',NULL,'Diproses','2026-09-08 02:30:00');
/*!40000 ALTER TABLE `retur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `riwayat_status_pesanan`
--

DROP TABLE IF EXISTS `riwayat_status_pesanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `riwayat_status_pesanan` (
  `id_riwayat` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `status_lama` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_baru` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `petugas` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Admin',
  `dibuat_pada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_riwayat`),
  KEY `id_pesanan` (`id_pesanan`,`dibuat_pada`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_status_pesanan`
--

LOCK TABLES `riwayat_status_pesanan` WRITE;
/*!40000 ALTER TABLE `riwayat_status_pesanan` DISABLE KEYS */;
INSERT INTO `riwayat_status_pesanan` VALUES (1,1,NULL,'Selesai','Riwayat awal (migrasi)','Admin','2026-09-01 10:15:00'),(2,2,NULL,'Selesai','Riwayat awal (migrasi)','Admin','2026-09-05 14:30:00'),(3,3,NULL,'Selesai','Riwayat awal (migrasi)','Admin','2026-09-10 09:05:00'),(4,4,NULL,'Dikirim','Riwayat awal (migrasi)','Admin','2026-09-15 16:20:00'),(5,5,NULL,'Diproses','Riwayat awal (migrasi)','Admin','2026-09-20 11:40:00'),(6,6,NULL,'Menunggu Pembayaran','Riwayat awal (migrasi)','Admin','2026-09-22 08:00:00'),(7,7,NULL,'Packing','Riwayat awal (migrasi)','Admin','2026-09-23 13:10:00'),(8,8,NULL,'Dibatalkan','Riwayat awal (migrasi)','Admin','2026-09-24 10:00:00'),(9,9,NULL,'Dibatalkan','Riwayat awal (migrasi)','Admin','2026-10-09 11:38:26'),(10,10,NULL,'Dibayar','Riwayat awal (migrasi)','Admin','2026-10-09 11:42:09'),(11,11,NULL,'Dibayar','Riwayat awal (migrasi)','Admin','2026-10-09 11:49:57'),(12,12,NULL,'Dibayar','Riwayat awal (migrasi)','Admin','2026-10-09 21:51:05'),(13,13,NULL,'Dibayar','Riwayat awal (migrasi)','Admin','2026-10-09 21:55:53'),(14,14,NULL,'Dibayar','Riwayat awal (migrasi)','Admin','2026-10-11 20:57:03'),(15,15,NULL,'Dibayar','Riwayat awal (migrasi)','Admin','2026-11-17 12:40:43'),(16,16,NULL,'Dibayar','Riwayat awal (migrasi)','Admin','2026-11-17 12:44:47'),(17,17,NULL,'Dibayar','Riwayat awal (migrasi)','Admin','2026-11-18 22:20:15'),(34,14,'Dibayar','Di Antar','Pesanan sedang diantar ke pelanggan.','Administrator','2026-11-18 23:16:05'),(35,18,'Menunggu Pembayaran','Packing',NULL,'Administrator','2026-11-18 23:27:03'),(36,18,'Packing','Di Antar','Pesanan sedang diantar ke pelanggan.','Administrator','2026-11-18 23:27:44'),(37,18,'Di Antar','Dibayar',NULL,'Administrator','2026-11-18 23:29:02'),(38,18,'Dibayar','Selesai',NULL,'Administrator','2026-11-18 23:29:10');
/*!40000 ALTER TABLE `riwayat_status_pesanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stok`
--

DROP TABLE IF EXISTS `stok`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stok` (
  `id_stok` int NOT NULL AUTO_INCREMENT,
  `id_produk` int NOT NULL,
  `jenis` enum('Masuk','Keluar','Penyesuaian') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_stok`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `stok_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stok`
--

LOCK TABLES `stok` WRITE;
/*!40000 ALTER TABLE `stok` DISABLE KEYS */;
INSERT INTO `stok` VALUES (1,1,'Masuk',100,'Stok awal dari supplier PT Semen Indonesia','2026-10-06 13:11:07'),(2,2,'Masuk',50,'Stok awal dari supplier PT Avian','2026-10-06 13:11:07'),(3,3,'Masuk',80,'Stok awal dari supplier PT Rucika','2026-10-06 13:11:07'),(5,5,'Masuk',40,'Stok awal dari supplier PT Keramik Karya','2026-10-06 13:11:07'),(6,6,'Masuk',75,'Stok awal dari supplier PT Kayu Nusantara','2026-10-06 13:11:07'),(7,9,'Masuk',100,'Stok awal saat produk ditambahkan','2026-10-06 14:14:15'),(8,10,'Masuk',80,'Stok awal saat produk ditambahkan','2026-10-06 14:14:15'),(14,11,'Masuk',100,'Stok awal saat produk ditambahkan','2026-10-06 14:26:07'),(15,12,'Masuk',80,'Stok awal saat produk ditambahkan','2026-10-06 14:26:07'),(16,13,'Masuk',60,'Stok awal saat produk ditambahkan','2026-10-06 14:26:07'),(43,6,'Keluar',5,'Penjualan INV-20261009-328259','2026-10-09 04:38:26'),(44,6,'Keluar',10,'Penjualan INV-20261009-B508D1','2026-10-09 04:42:09'),(45,3,'Keluar',10,'Penjualan INV-20261009-E466A3','2026-10-09 04:49:57'),(46,3,'Keluar',1,'Penjualan INV-20261009-4D01DC','2026-10-09 14:51:05'),(47,2,'Keluar',1,'Penjualan INV-20261009-4D01DC','2026-10-09 14:51:05'),(48,2,'Keluar',1,'Penjualan INV-20261009-BD53F5','2026-10-09 14:55:53'),(49,2,'Keluar',1,'Penjualan INV-20261011-1453D5','2026-10-11 13:57:04'),(50,2,'Masuk',3,'Mutasi stok oleh admin','2026-10-11 14:02:51'),(51,3,'Keluar',2,'Penjualan INV-20261117-0B7A87','2026-11-17 05:40:43'),(52,6,'Keluar',1,'Penjualan INV-20261117-D1E800','2026-11-17 05:44:47'),(53,2,'Masuk',50,'Mutasi stok oleh admin','2026-11-17 05:53:41'),(57,2,'Keluar',1,'Penjualan INV-20261118-2A5C15','2026-11-18 15:20:15'),(58,6,'Keluar',1,'Penjualan INV-20261118-77B96A','2026-11-18 16:26:21');
/*!40000 ALTER TABLE `stok` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimoni`
--

DROP TABLE IF EXISTS `testimoni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimoni` (
  `id_testimoni` int NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int DEFAULT '5',
  `status` enum('Pending','Tampil','Ditolak') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_testimoni`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimoni`
--

LOCK TABLES `testimoni` WRITE;
/*!40000 ALTER TABLE `testimoni` DISABLE KEYS */;
INSERT INTO `testimoni` VALUES (1,1,'Andi Pratama','Semen datang cepat dan sesuai pesanan. Sudah dua kali order, selalu rapi. Recommended!',5,'Tampil','2026-10-06 13:11:07'),(2,2,'Budi Santoso','Harga paling bersaing di daerah saya. Packingnya juga rapi dan aman.',4,'Tampil','2026-10-06 13:11:07'),(3,3,'Citra Lestari','Respon customer service cepat, keramik sampai dengan utuh. Pasti belanja lagi di sini.',5,'Tampil','2026-10-06 13:11:07'),(4,NULL,'Rudi Hartono','Pipa PVC yang saya terima agak penyok, tapi admin sudah menindaklanjuti dengan cepat.',3,'Tampil','2026-10-06 13:11:07'),(5,NULL,'Sari Wijaya','Pelayanan baik, tapi ongkir telat sehari dari perkiraan.',4,'Pending','2026-10-06 13:11:07'),(6,NULL,'Pengunjung','Produk original dan sesuai deskripsi di katalog.',5,'Pending','2026-10-06 13:11:07'),(7,NULL,'Uji Coba','Testimoni contoh yang harus ditolak admin.',1,'Ditolak','2026-10-06 13:11:07'),(9,5,'Ahmad','bagus banget',5,'Tampil','2026-10-11 13:55:24');
/*!40000 ALTER TABLE `testimoni` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlist` (
  `id_wishlist` int NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int NOT NULL,
  `id_produk` int NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_wishlist`),
  UNIQUE KEY `unique_wishlist` (`id_pelanggan`,`id_produk`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
INSERT INTO `wishlist` VALUES (1,1,2,'2026-10-06 13:11:07'),(2,1,5,'2026-10-06 13:11:07'),(3,2,1,'2026-10-06 13:11:07');
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'nerf_tokobangunan'
--

--
-- Dumping routines for database 'nerf_tokobangunan'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-19  0:03:56
