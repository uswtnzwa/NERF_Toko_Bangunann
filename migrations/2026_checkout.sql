-- ============================================================
--  MIGRASI: ALUR PEMESANAN (Checkout)
--  Berisi tambahan kolom pesanan + tabel ongkir & pengaturan toko.
--
--  Jalankan lewat phpMyAdmin (import) ATAU CLI:
--    C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -uroot \
--      < migrations/2026_checkout.sql
--
--  Aman dijalankan berulang kali (kolom dicek dulu).
-- ============================================================

USE NERF_TokoBangunan;

-- --------------------------------------------------------
-- 1. Kolom baru pada tabel pesanan
--    Alamat pengiriman ikut disimpan di pesanan (snapshot),
--    jadi riwayat pesanan tetap utuh walau pelanggan
--    mengganti atau menghapus alamatnya di kemudian hari.
-- --------------------------------------------------------
SET @ada = (SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'pesanan' AND COLUMN_NAME = 'nama_penerima');
SET @sql = IF(@ada = 0, 'ALTER TABLE pesanan ADD COLUMN nama_penerima varchar(100) DEFAULT NULL AFTER id_alamat', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ada = (SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'pesanan' AND COLUMN_NAME = 'nomor_hp');
SET @sql = IF(@ada = 0, 'ALTER TABLE pesanan ADD COLUMN nomor_hp varchar(20) DEFAULT NULL AFTER nama_penerima', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ada = (SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'pesanan' AND COLUMN_NAME = 'alamat');
SET @sql = IF(@ada = 0, 'ALTER TABLE pesanan ADD COLUMN alamat text DEFAULT NULL AFTER nomor_hp', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ada = (SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'pesanan' AND COLUMN_NAME = 'kota');
SET @sql = IF(@ada = 0, 'ALTER TABLE pesanan ADD COLUMN kota varchar(100) DEFAULT NULL AFTER alamat', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ada = (SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'pesanan' AND COLUMN_NAME = 'catatan');
SET @sql = IF(@ada = 0, 'ALTER TABLE pesanan ADD COLUMN catatan varchar(255) DEFAULT NULL AFTER kota', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --------------------------------------------------------
-- 2. Tabel ongkir (biaya tambahan untuk pesanan diantar)
--    Toko berada di Bandung, jadi baris kota = toko ongkir 0
--    (ambil di toko / antar sendiri).
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ongkir` (
  `id_ongkir` int(11) NOT NULL AUTO_INCREMENT,
  `kota` varchar(100) NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT 0.00,
  `estimasi` varchar(50) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  PRIMARY KEY (`id_ongkir`),
  UNIQUE KEY `kota` (`kota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ongkir` (`kota`, `biaya`, `estimasi`, `status`) VALUES
('Bandung',       0.00,     'Sampai hari yang sama', 'Aktif'),
('Bandung Raya',  15000.00, '1 hari kerja',          'Aktif'),
('Cimahi',        25000.00, '1 hari kerja',          'Aktif'),
('Sumedang',      35000.00, '1-2 hari kerja',        'Aktif'),
('Garut',         40000.00, '1-2 hari kerja',        'Aktif'),
('Tasikmalaya',   35000.00, '1-2 hari kerja',        'Aktif'),
('Jakarta',       60000.00, '2-3 hari kerja',        'Aktif'),
('Bandung Raya +',0.00,     'Di luar daftar, hubungi admin', 'Nonaktif')
ON DUPLICATE KEY UPDATE `estimasi` = VALUES(`estimasi`), `status` = VALUES(`status`);

-- --------------------------------------------------------
-- 3. Pengaturan toko untuk halaman pembayaran
--    (nomor rekening & QRIS statis, ditampilkan ke pelanggan)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT,
  `kunci` varchar(50) NOT NULL,
  `nilai` text DEFAULT NULL,
  PRIMARY KEY (`id_pengaturan`),
  UNIQUE KEY `kunci` (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pengaturan` (`kunci`, `nilai`) VALUES
('nama_toko',        'NERF Toko Bangunan'),
('alamat_toko',      'Jl. Merdeka No. 10, Bandung'),
('telepon_toko',     '0812-3456-7890'),
('nama_bank',        'Bank BCA'),
('nomor_rekening',   '8720-1188-9'),
('atas_nama_rekening','PT NERF Bangunan Perkasa'),
('qris_nama',        'NERF Toko Bangunan'),
('qris_gambar',      'qris.png')
ON DUPLICATE KEY UPDATE `nilai` = VALUES(`nilai`);

-- --------------------------------------------------------
-- 4. Pesanan lama: isi alamat dari tabel alamat_pelanggan
--    supaya kolom baru tidak kosong pada data seed.
-- --------------------------------------------------------
UPDATE pesanan p
JOIN alamat_pelanggan a ON a.id_alamat = p.id_alamat
SET p.nama_penerima = COALESCE(p.nama_penerima, a.nama_penerima),
    p.nomor_hp      = COALESCE(p.nomor_hp, a.nomor_hp),
    p.alamat        = COALESCE(p.alamat, a.alamat),
    p.kota          = COALESCE(p.kota, a.kota);
