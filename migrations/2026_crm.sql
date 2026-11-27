-- ============================================================
--  MIGRASI: CRM (CUSTOMER RELATIONSHIP MANAGEMENT)
--  Menambah tabel leads untuk mengelola calon pelanggan.
--  Data pelanggan & produk terlaris diambil dari tabel yang ada.
--
--  Jalankan lewat phpMyAdmin (import) ATAU CLI:
--    C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -uroot \
--      < migrations/2026_crm.sql
--
--  Aman dijalankan berulang kali (CREATE IF NOT EXISTS + UPSERT).
-- ============================================================

USE NERF_TokoBangunan;

-- --------------------------------------------------------
-- 1. Tabel leads / calon pelanggan
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `leads` (
  `id_lead`      int(11) NOT NULL AUTO_INCREMENT,
  `nama`         varchar(100) NOT NULL,
  `email`        varchar(100) DEFAULT NULL,
  `nomor_hp`     varchar(20) DEFAULT NULL,
  `sumber`       varchar(50) NOT NULL DEFAULT 'Website',
  `status`       enum('Baru','Dihubungi','Tertarik','Pelanggan','Ditolak') NOT NULL DEFAULT 'Baru',
  `keterangan`   text DEFAULT NULL,
  `dibuat_pada`  datetime NOT NULL DEFAULT current_timestamp(),
  `diubah_pada`  datetime DEFAULT NULL,
  PRIMARY KEY (`id_lead`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Data awal leads
-- --------------------------------------------------------
INSERT INTO `leads`
  (`id_lead`, `nama`, `email`, `nomor_hp`, `sumber`, `status`, `keterangan`, `dibuat_pada`)
VALUES
(1, 'Rizky Ananda',   'rizky@example.com',   '081211110001', 'Website',    'Baru',      'Bertanya harga semen Holcim 50 Kg.', '2026-11-02 09:20:00'),
(2, 'Nadia Putri',    'nadia@example.com',   '081211110002', 'Instagram',  'Dihubungi', 'Tertarik dengan cat eksterior.', '2026-11-05 11:45:00'),
(3, 'Farhan Maulana', 'farhan@example.com',  '081211110003', 'WhatsApp',   'Tertarik',  'Rencana renovasi rumah, butuh keramik 60x60.', '2026-11-08 14:10:00'),
(4, 'Siti Aminah',    'siti@example.com',    '081211110004', 'Referral',   'Pelanggan', 'Sudah melakukan pembelian paku baja.', '2026-11-10 08:30:00'),
(5, 'Dewi Sartika',   'dewi@example.com',    '081211110005', 'Walk-in',    'Baru',      'Datang ke toko menanyakan pipa PPR.', '2026-11-12 16:05:00'),
(6, 'Agus Setiawan',  'agus@example.com',    '081211110006', 'Iklan',      'Ditolak',   'Membutuhkan material di luar area layanan.', '2026-11-14 10:00:00')
ON DUPLICATE KEY UPDATE
  `nama`       = VALUES(`nama`),
  `email`      = VALUES(`email`),
  `nomor_hp`   = VALUES(`nomor_hp`),
  `sumber`     = VALUES(`sumber`),
  `status`     = VALUES(`status`),
  `keterangan` = VALUES(`keterangan`),
  `dibuat_pada` = VALUES(`dibuat_pada`);

-- --------------------------------------------------------
-- 3. Tabel riwayat interaksi (bisa untuk lead maupun pelanggan)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `interaksi` (
  `id_interaksi` int(11) NOT NULL AUTO_INCREMENT,
  `subjek`       enum('Lead','Pelanggan') NOT NULL,
  `id_subjek`    int(11) NOT NULL,
  `jenis`        enum('Telepon','WhatsApp','Email','Chat','Kunjungan') NOT NULL,
  `catatan`      text NOT NULL,
  `petugas`      varchar(100) NOT NULL DEFAULT 'Admin',
  `tanggal`      datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_interaksi`),
  KEY `subjek` (`subjek`, `id_subjek`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Data awal interaksi
-- --------------------------------------------------------
INSERT INTO `interaksi`
  (`id_interaksi`, `subjek`, `id_subjek`, `jenis`, `catatan`, `petugas`, `tanggal`)
VALUES
(1, 'Lead', 2, 'Telepon',     'Menjelaskan pilihan cat eksterior dan harga per kaleng.', 'Admin', '2026-11-06 10:15:00'),
(2, 'Lead', 2, 'WhatsApp',    'Mengirim katalog warna cat Avian.',                        'Admin', '2026-11-07 13:20:00'),
(3, 'Lead', 3, 'Chat',        'Mendiskusikan kebutuhan keramik 60x60 untuk renovasi.',    'Admin', '2026-11-09 09:40:00'),
(4, 'Lead', 4, 'Kunjungan',   'Pelanggan datang ke toko dan melakukan pembelian.',        'Admin', '2026-11-10 15:30:00'),
(5, 'Pelanggan', 1, 'WhatsApp', 'Konfirmasi pengiriman pesanan INV-20261117-0B7A87.',     'Admin', '2026-11-17 12:50:00'),
(6, 'Pelanggan', 2, 'Email',    'Mengirim faktur dan informasi garansi cat.',             'Admin', '2026-11-16 11:00:00')
ON DUPLICATE KEY UPDATE
  `subjek`   = VALUES(`subjek`),
  `id_subjek` = VALUES(`id_subjek`),
  `jenis`    = VALUES(`jenis`),
  `catatan`  = VALUES(`catatan`),
  `petugas`  = VALUES(`petugas`),
  `tanggal`  = VALUES(`tanggal`);
