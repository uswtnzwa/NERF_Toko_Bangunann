-- ============================================================
--  MIGRASI: PENGATURAN AKUN PELANGGAN
--  Menyimpan preferensi notifikasi per akun pelanggan.
--  Data profil & password sudah tersedia di tabel pelanggan.
--
--  Jalankan lewat phpMyAdmin (import) ATAU CLI:
--    C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -uroot \
--      < migrations/2026_pengaturan_akun.sql
--
--  Aman dijalankan berulang kali.
-- ============================================================

USE NERF_TokoBangunan;

-- --------------------------------------------------------
-- 1. Tabel preferensi notifikasi
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `preferensi_notifikasi` (
  `id_pelanggan`          int(11) NOT NULL,
  `notif_email_pesanan`   tinyint(1) NOT NULL DEFAULT 1,
  `notif_wa_pesanan`      tinyint(1) NOT NULL DEFAULT 1,
  `notif_email_promo`     tinyint(1) NOT NULL DEFAULT 0,
  `notif_email_pembayaran` tinyint(1) NOT NULL DEFAULT 1,
  `diperbarui_pada`       datetime DEFAULT NULL,
  PRIMARY KEY (`id_pelanggan`),
  CONSTRAINT `fk_pref_pelanggan` FOREIGN KEY (`id_pelanggan`)
    REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Beri preferensi default untuk pelanggan yang sudah ada
-- --------------------------------------------------------
INSERT INTO `preferensi_notifikasi` (`id_pelanggan`)
SELECT p.`id_pelanggan`
FROM `pelanggan` p
LEFT JOIN `preferensi_notifikasi` n ON n.`id_pelanggan` = p.`id_pelanggan`
WHERE n.`id_pelanggan` IS NULL;
