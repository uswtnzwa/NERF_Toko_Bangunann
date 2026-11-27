-- ============================================================
--  MIGRASI: TRACKING PENGIRIMAN
--  Menambah status "Sedang Disiapkan" dan "Di Antar" pada pesanan,
--  serta tabel riwayat perubahan status (tracking).
--
--  Jalankan lewat phpMyAdmin (import) ATAU CLI:
--    Get-Content migrations/2026_tracking_pengiriman.sql |
--      mysql.exe -uroot
--
--  Aman dijalankan berulang kali.
-- ============================================================

USE NERF_TokoBangunan;

-- --------------------------------------------------------
-- 1. Status baru pada tabel pesanan
-- --------------------------------------------------------
ALTER TABLE `pesanan`
  MODIFY `status_pesanan`
    ENUM('Menunggu Pembayaran','Dibayar','Diproses','Sedang Disiapkan',
         'Packing','Di Antar','Dikirim','Selesai','Dibatalkan','Retur')
    COLLATE utf8mb4_unicode_ci DEFAULT 'Menunggu Pembayaran';

-- --------------------------------------------------------
-- 2. Riwayat perubahan status
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `riwayat_status_pesanan` (
  `id_riwayat`  int(11) NOT NULL AUTO_INCREMENT,
  `id_pesanan`  int(11) NOT NULL,
  `status_lama` varchar(40) DEFAULT NULL,
  `status_baru` varchar(40) NOT NULL,
  `keterangan`  varchar(255) DEFAULT NULL,
  `petugas`     varchar(100) NOT NULL DEFAULT 'Admin',
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_riwayat`),
  KEY `id_pesanan` (`id_pesanan`, `dibuat_pada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Riwayat awal untuk pesanan yang sudah ada
-- --------------------------------------------------------
INSERT INTO `riwayat_status_pesanan`
  (`id_pesanan`, `status_lama`, `status_baru`, `keterangan`, `petugas`, `dibuat_pada`)
SELECT p.id_pesanan, NULL, p.status_pesanan, 'Riwayat awal (migrasi)', 'Admin', p.tanggal_pesanan
FROM `pesanan` p
WHERE NOT EXISTS (
    SELECT 1 FROM `riwayat_status_pesanan` r WHERE r.id_pesanan = p.id_pesanan
);
