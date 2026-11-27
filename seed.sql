-- ============================================================
--  DATA CONTOH  -  NERF Toko Bangunan  (seed)
--
--  Jalankan SETELAH meng-import NERF_TokoBangunan.sql
--  Password pelanggan contoh: andi / budi / citra / dimas
--  (hash dibuat dengan password_hash / bcrypt)
--
--  Aman dijalankan berulang kali: data lama dihapus dulu.
-- ============================================================

USE NERF_TokoBangunan;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE testimoni;
TRUNCATE TABLE banner;
TRUNCATE TABLE faq;
TRUNCATE TABLE retur;
TRUNCATE TABLE pembayaran;
TRUNCATE TABLE pengiriman;
TRUNCATE TABLE detail_pesanan;
TRUNCATE TABLE pesanan;
TRUNCATE TABLE alamat_pelanggan;
TRUNCATE TABLE wishlist;
TRUNCATE TABLE stok;
TRUNCATE TABLE pelanggan;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Foto produk (nama file placeholder di folder uploads/)
-- Ganti file gambarnya, nama file TIDAK perlu diubah.
-- --------------------------------------------------------
UPDATE produk SET gambar = 'smn001.png' WHERE kode_produk = 'SMN001';
UPDATE produk SET gambar = 'cat001.png' WHERE kode_produk = 'CAT001';
UPDATE produk SET gambar = 'pip001.png' WHERE kode_produk = 'PIP001';
UPDATE produk SET gambar = 'bsi001.png' WHERE kode_produk = 'BSI001';
UPDATE produk SET gambar = 'krm001.png' WHERE kode_produk = 'KRM001';
UPDATE produk SET gambar = 'kay001.png' WHERE kode_produk = 'KAY001';

-- --------------------------------------------------------
-- Pelanggan
-- --------------------------------------------------------
INSERT INTO pelanggan (nama_lengkap, email, password, nomor_hp, status) VALUES
('Andi Pratama',  'andi@mail.com',  '$2y$10$HJymt97jlz8sxHWqF50esOGw8d5dnjTshm.Q8OKsVOYkp7Vzzf36q', '081234567890', 'Aktif'),
('Budi Santoso',  'budi@mail.com',  '$2y$10$FvSbOP34gT2cB9Iu2nDsCOkQdOyVIAEdDtqoTyJwXg7AgTe7h.KA6', '081298765432', 'Aktif'),
('Citra Lestari', 'citra@mail.com', '$2y$10$Cd3LhEOuh7jWuTRu4drnL.oMBR6otBbBDwWVXSdqUEYqmNYfT2IUu', '085611223344', 'Aktif'),
('Dimas Nugroho', 'dimas@mail.com', '$2y$10$8WcOcuqIXoNLKsqyAjT1.eRmP9USOXaLqNd.UkGyGNyS.Dkl50shW', '081377889900', 'Nonaktif');

-- --------------------------------------------------------
-- Alamat pelanggan
-- --------------------------------------------------------
INSERT INTO alamat_pelanggan (id_pelanggan, label, nama_penerima, nomor_hp, alamat, kota, provinsi, kode_pos, utama) VALUES
(1, 'Rumah',  'Andi Pratama',  '081234567890', 'Jl. Merdeka No. 10, RT 02 RW 05', 'Bandung',    'Jawa Barat',   '40115', 'Ya'),
(1, 'Kantor', 'Andi Pratama',  '081234567890', 'Jl. Sudirman Kav. 5',             'Jakarta',    'DKI Jakarta',  '10220', 'Tidak'),
(2, 'Rumah',  'Budi Santoso',  '081298765432', 'Jl. Kenanga No. 22',              'Surabaya',   'Jawa Timur',   '60271', 'Ya'),
(3, 'Rumah',  'Citra Lestari', '085611223344', 'Jl. Anggrek No. 7',               'Semarang',   'Jawa Tengah', '50234', 'Ya'),
(4, 'Rumah',  'Dimas Nugroho', '081377889900', 'Jl. Mawar No. 3',                 'Yogyakarta', 'DI Yogyakarta','55223', 'Ya');

-- --------------------------------------------------------
-- Riwayat stok awal
-- --------------------------------------------------------
INSERT INTO stok (id_produk, jenis, jumlah, keterangan) VALUES
(1, 'Masuk', 100, 'Stok awal dari supplier PT Semen Indonesia'),
(2, 'Masuk',  50, 'Stok awal dari supplier PT Avian'),
(3, 'Masuk',  80, 'Stok awal dari supplier PT Rucika'),
(4, 'Masuk',  60, 'Stok awal dari supplier PT Baja Bharat'),
(5, 'Masuk',  40, 'Stok awal dari supplier PT Keramik Karya'),
(6, 'Masuk',  75, 'Stok awal dari supplier PT Kayu Nusantara');

-- --------------------------------------------------------
-- Pesanan
-- --------------------------------------------------------
INSERT INTO pesanan (kode_pesanan, id_pelanggan, id_alamat, tanggal_pesanan, subtotal, ongkir, diskon, total, metode_pembayaran, metode_pengiriman, status_pesanan) VALUES
('INV-20260901-001', 1, 1, '2026-09-01 10:15:00', 461000.00, 25000.00,  12000.00, 474000.00, 'Transfer Bank', 'Ekspedisi', 'Selesai'),
('INV-20260905-002', 2, 3, '2026-09-05 14:30:00', 185000.00, 18000.00,      0.00, 203000.00, 'E-Wallet',     'Sameday',   'Selesai'),
('INV-20260910-003', 1, 1, '2026-09-10 09:05:00', 622000.00, 30000.00, 200000.00, 452000.00, 'Transfer Bank', 'Ekspedisi', 'Selesai'),
('INV-20260915-004', 3, 4, '2026-09-15 16:20:00',  95000.00, 15000.00,      0.00, 110000.00, 'COD',          'Ekspedisi', 'Dikirim'),
('INV-20260920-005', 2, 3, '2026-09-20 11:40:00', 157000.00, 20000.00,  10000.00, 167000.00, 'Transfer Bank', 'Ekspedisi', 'Diproses'),
('INV-20260922-006', 1, 1, '2026-09-22 08:00:00',  18500.00,  9000.00,      0.00,  27500.00, 'E-Wallet',     'Sameday',   'Menunggu Pembayaran'),
('INV-20260923-007', 3, 4, '2026-09-23 13:10:00', 199000.00, 22000.00,      0.00, 221000.00, 'Transfer Bank', 'Ekspedisi', 'Packing'),
('INV-20260924-008', 2, 3, '2026-09-24 10:00:00',  64000.00, 12000.00,      0.00,  76000.00, 'COD',          'Ekspedisi', 'Dibatalkan');

-- --------------------------------------------------------
-- Detail pesanan
-- --------------------------------------------------------
INSERT INTO detail_pesanan (id_pesanan, id_produk, harga, jumlah, subtotal) VALUES
(1, 1,  60000.00, 2, 120000.00),
(1, 4,  75000.00, 2, 150000.00),
(1, 6,  32000.00, 3,  96000.00),
(1, 5,  95000.00, 1,  95000.00),
(2, 3,  18500.00,10, 185000.00),
(3, 1,  55000.00, 8, 440000.00),
(3, 4,  75000.00, 2, 150000.00),
(3, 6,  32000.00, 1,  32000.00),
(4, 5,  95000.00, 1,  95000.00),
(5, 2, 120000.00, 1, 120000.00),
(5, 3,  18500.00, 2,  37000.00),
(6, 3,  18500.00, 1,  18500.00),
(7, 2, 125000.00, 1, 125000.00),
(7, 3,  18500.00, 4,  74000.00);

-- --------------------------------------------------------
-- Pembayaran
-- --------------------------------------------------------
INSERT INTO pembayaran (id_pesanan, metode, jumlah, tanggal_pembayaran, status) VALUES
(1, 'Transfer Bank', 474000.00, '2026-09-01 10:45:00', 'Diverifikasi'),
(2, 'E-Wallet',      203000.00, '2026-09-05 14:50:00', 'Diverifikasi'),
(3, 'Transfer Bank', 452000.00, '2026-09-10 09:30:00', 'Diverifikasi'),
(4, 'COD',           110000.00, '2026-09-15 16:40:00', 'Diverifikasi'),
(5, 'Transfer Bank', 167000.00, '2026-09-20 12:00:00', 'Menunggu'),
(6, 'E-Wallet',       27500.00, NULL,                  'Menunggu'),
(7, 'Transfer Bank', 221000.00, '2026-09-23 13:30:00', 'Menunggu'),
(8, 'COD',            76000.00, NULL,                  'Ditolak');

-- --------------------------------------------------------
-- Pengiriman
-- --------------------------------------------------------
INSERT INTO pengiriman (id_pesanan, kurir, nomor_resi, ongkir, status) VALUES
(1, 'JNE',         'JNE123456789', 25000.00, 'Sampai'),
(2, 'GoSend',      'GSN987654321', 18000.00, 'Sampai'),
(3, 'J&T Express', 'JNT555444333', 30000.00, 'Sampai'),
(4, 'SiCepat',     'SCP111222333', 15000.00, 'Dikirim'),
(7, 'JNE',         'JNE777888999', 22000.00, 'Diproses');

-- --------------------------------------------------------
-- Retur
-- --------------------------------------------------------
INSERT INTO retur (id_pesanan, alasan, status, tanggal) VALUES
(5, 'Ukuran sak semen yang dikirim tidak sesuai pesanan, pelanggan meminta ganti ukuran 40 Kg.', 'Menunggu', '2026-09-21 10:00:00'),
(2, 'Pipa PVC yang datang penyok dan bocor saat pemasangan.', 'Diproses',  '2026-09-08 09:30:00');

-- --------------------------------------------------------
-- Wishlist
-- --------------------------------------------------------
INSERT INTO wishlist (id_pelanggan, id_produk) VALUES
(1, 2), (1, 5), (2, 1), (3, 4);

-- --------------------------------------------------------
-- Banner promosi
-- --------------------------------------------------------
INSERT INTO banner (judul, subjudul, tombol, link, status, urutan) VALUES
('Diskon 20% untuk Semen', 'Berlaku sampai akhir bulan, stok terbatas.', 'Lihat Produk', 'Katalog.php?cari=semen', 'Aktif', 1),
('Besi Beton Harga Bersaing', 'Harga terbaik untuk proyek konstruksi Anda.', 'Katalog Besi', 'Katalog.php?kategori=3', 'Aktif', 2),
('Gratis Ongkir', 'Minimal pembelian Rp 500.000 ke seluruh Indonesia.', 'Cek Katalog', 'Katalog.php', 'Aktif', 3);

-- --------------------------------------------------------
-- FAQ
-- --------------------------------------------------------
INSERT INTO faq (pertanyaan, jawaban, status, urutan) VALUES
('Apakah bisa kirim ke luar Jawa?', 'Bisa. Kami melayani pengiriman ke seluruh Indonesia. Ongkos kirim dihitung otomatis berdasarkan kurir dan tujuan yang dipilih saat checkout.', 'Aktif', 1),
('Berapa lama pesanan diproses?', 'Pesanan yang pembayarannya sudah diverifikasi kami proses dalam 1 x 24 jam kerja, lalu dikirim pada hari yang sama sebelum pukul 15.00 WIB.', 'Aktif', 2),
('Apakah produk bergaransi?', 'Ya. Semua produk bergaransi resmi dari distributor. Bila barang bermasalah, kirimkan foto dan nomor pesanan ke cs@tokobangunan.com maksimal 7 hari setelah barang diterima.', 'Aktif', 3),
('Bagaimana cara melacak pesanan?', 'Nomor resi muncul di halaman pesanan saya segera setelah paket diserahkan ke kurir. Anda juga bisa mengikuti tracking langsung di situs kurir terkait.', 'Aktif', 4),
('Apakah tersedia cicilan?', 'Untuk pembelian di atas Rp 5.000.000 kami menyediakan cicilan 3x tanpa bunga. Hubungi customer service untuk pengajuan.', 'Aktif', 5);

-- --------------------------------------------------------
-- Testimoni
-- --------------------------------------------------------
INSERT INTO testimoni (id_pelanggan, nama, isi, rating, status) VALUES
(1, 'Andi Pratama',  'Semen datang cepat dan sesuai pesanan. Sudah dua kali order, selalu rapi. Recommended!', 5, 'Tampil'),
(2, 'Budi Santoso',  'Harga paling bersaing di daerah saya. Packingnya juga rapi dan aman.', 4, 'Tampil'),
(3, 'Citra Lestari', 'Respon customer service cepat, keramik sampai dengan utuh. Pasti belanja lagi di sini.', 5, 'Tampil'),
(NULL, 'Rudi Hartono', 'Pipa PVC yang saya terima agak penyok, tapi admin sudah menindaklanjuti dengan cepat.', 3, 'Tampil'),
(NULL, 'Sari Wijaya',  'Pelayanan baik, tapi ongkir telat sehari dari perkiraan.', 4, 'Pending'),
(NULL, 'Pengunjung',   'Produk original dan sesuai deskripsi di katalog.', 5, 'Pending'),
(NULL, 'Uji Coba',     'Testimoni contoh yang harus ditolak admin.', 1, 'Ditolak');
