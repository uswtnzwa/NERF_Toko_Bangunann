-- ============================================================
--  MIGRASI: NORMALISASI PATH KUNJUNGAN
--  Data lama yang tercatat saat mengakses lewat
--  http://localhost/ERP_Kel7/... menyimpan "/ERP_Kel7/beranda.php".
--  Laporan mengelompokkan halaman dengan awalan "/beranda.php",
--  jadi prefix folder perlu dibuang.
--
--  Aman dijalankan berulang kali.
-- ============================================================

USE NERF_TokoBangunan;

UPDATE `kunjungan`
   SET `halaman` = CONCAT('/', SUBSTRING(`halaman`, LENGTH('/ERP_Kel7/') + 1))
 WHERE `halaman` LIKE '/ERP_Kel7/%';

-- Root aplikasi dihitung sebagai kunjungan Beranda.
UPDATE `kunjungan` SET `halaman` = '/beranda.php'
 WHERE `halaman` IN ('/ERP_Kel7', '/ERP_Kel7/', '/', '');
