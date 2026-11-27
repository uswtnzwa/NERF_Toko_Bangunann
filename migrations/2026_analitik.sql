-- ============================================================
--  MIGRASI: ANALITIK & STATISTIK
--  Menambah tabel kunjungan untuk laporan pengunjung.
--  Data penjualan & produk terlaris diambil dari pesanan dan
--  detail_pesanan yang sudah ada.
--
--  Jalankan lewat phpMyAdmin (import) ATAU CLI:
--    C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -uroot \
--      < migrations/2026_analitik.sql
--
--  Aman dijalankan berulang kali (CREATE IF NOT EXISTS + UPSERT).
-- ============================================================

USE NERF_TokoBangunan;

-- --------------------------------------------------------
-- 1. Tabel kunjungan
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kunjungan` (
  `id_kunjungan` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal`      datetime NOT NULL,
  `halaman`      varchar(150) NOT NULL,
  `sumber`       varchar(50) NOT NULL DEFAULT 'Langsung',
  `ip`           varchar(45) DEFAULT NULL,
  `session_id`   varchar(64) DEFAULT NULL,
  `dibuat_pada`  timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_kunjungan`),
  KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Data awal kunjungan
-- --------------------------------------------------------
INSERT INTO `kunjungan`
  (`id_kunjungan`, `tanggal`, `halaman`, `sumber`, `ip`, `session_id`)
VALUES
(1,  '2026-09-01 08:10:00', '/beranda.php',             'Langsung',  '127.0.0.1', 'sess_a1'),
(2,  '2026-09-01 08:12:00', '/katalog.php',             'Langsung',  '127.0.0.1', 'sess_a1'),
(3,  '2026-09-02 10:25:00', '/katalog.php',             'Google',    '127.0.0.2', 'sess_b2'),
(4,  '2026-09-03 13:40:00', '/blog.php',                'Instagram', '127.0.0.3', 'sess_c3'),
(5,  '2026-09-05 09:05:00', '/masuk.php',               'Langsung',  '127.0.0.1', 'sess_a1'),
(6,  '2026-09-07 15:30:00', '/customer-service.php',    'WhatsApp',  '127.0.0.4', 'sess_d4'),
(7,  '2026-09-10 11:15:00', '/beranda.php',             'Google',    '127.0.0.5', 'sess_e5'),
(8,  '2026-09-12 14:20:00', '/katalog.php?kategori=1',  'Google',    '127.0.0.5', 'sess_e5'),
(9,  '2026-09-15 16:45:00', '/artikel.php?slug=memilih-semen', 'Instagram', '127.0.0.6', 'sess_f6'),
(10, '2026-09-18 09:50:00', '/beranda.php',             'Referral',  '127.0.0.7', 'sess_g7'),
(11, '2026-09-20 19:10:00', '/katalog.php',             'Langsung',  '127.0.0.8', 'sess_h8'),
(12, '2026-09-20 19:14:00', '/daftar.php',              'Langsung',  '127.0.0.8', 'sess_h8'),
(13, '2026-10-02 08:35:00', '/beranda.php',             'Google',    '127.0.0.9', 'sess_i9'),
(14, '2026-10-04 10:00:00', '/katalog.php',             'Google',    '127.0.0.9', 'sess_i9'),
(15, '2026-10-06 12:20:00', '/blog.php',                'Instagram', '127.0.0.10','sess_j10'),
(16, '2026-10-09 11:38:00', '/masuk.php',               'Langsung',  '127.0.0.11','sess_k11'),
(17, '2026-10-11 20:55:00', '/beranda.php',             'WhatsApp',  '127.0.0.12','sess_l12'),
(18, '2026-10-14 09:40:00', '/katalog.php?kategori=6',  'Google',    '127.0.0.13','sess_m13'),
(19, '2026-10-18 15:05:00', '/customer-service.php',    'Langsung',  '127.0.0.14','sess_n14'),
(20, '2026-10-22 13:30:00', '/artikel.php?slug=pipa-pvc-vs-ppr', 'Instagram', '127.0.0.15','sess_o15'),
(21, '2026-11-02 08:45:00', '/beranda.php',             'Langsung',  '127.0.0.16','sess_p16'),
(22, '2026-11-04 10:10:00', '/katalog.php',             'Google',    '127.0.0.17','sess_q17'),
(23, '2026-11-08 14:25:00', '/blog.php',                'Instagram', '127.0.0.18','sess_r18'),
(24, '2026-11-10 09:15:00', '/pengaturan.php',          'Langsung',  '127.0.0.16','sess_p16'),
(25, '2026-11-14 16:40:00', '/katalog.php?kategori=3',  'Referral',  '127.0.0.19','sess_s19'),
(26, '2026-11-16 11:00:00', '/pesanan-saya.php',        'Langsung',  '127.0.0.20','sess_t20')
ON DUPLICATE KEY UPDATE
  `tanggal`    = VALUES(`tanggal`),
  `halaman`    = VALUES(`halaman`),
  `sumber`     = VALUES(`sumber`),
  `ip`         = VALUES(`ip`),
  `session_id` = VALUES(`session_id`);
