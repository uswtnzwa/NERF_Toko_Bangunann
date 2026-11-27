-- ============================================================
--  MIGRASI: LAPORAN KEUANGAN
--  Menambah tabel pengeluaran untuk mencatat biaya operasional.
--  Pendapatan diambil dari pembayaran terverifikasi di tabel
--  pembayaran + pesanan, jadi tidak perlu tabel tambahan.
--
--  Jalankan lewat phpMyAdmin (import) ATAU CLI:
--    C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -uroot \
--      < migrations/2026_keuangan.sql
--
--  Aman dijalankan berulang kali (CREATE IF NOT EXISTS + UPSERT).
-- ============================================================

USE NERF_TokoBangunan;

-- --------------------------------------------------------
-- 1. Tabel pengeluaran
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pengeluaran` (
  `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` datetime NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `metode` varchar(50) DEFAULT 'Tunai',
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pengeluaran`),
  KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Data awal pengeluaran
-- --------------------------------------------------------
INSERT INTO `pengeluaran`
  (`id_pengeluaran`, `tanggal`, `kategori`, `keterangan`, `jumlah`, `metode`)
VALUES
(1, '2026-09-02 09:00:00', 'Pembelian Stok', 'Pembelian semen dan cat dari supplier', 300000.00, 'Transfer'),
(2, '2026-09-10 08:30:00', 'Listrik',        'Tagihan listrik toko bulan September',   85000.00, 'Tunai'),
(3, '2026-09-15 17:00:00', 'Gaji Karyawan',  'Gaji karyawan toko September',           250000.00, 'Transfer'),
(4, '2026-10-03 10:00:00', 'Pembelian Stok', 'Pembelian kayu dan paku',                320000.00, 'Transfer'),
(5, '2026-10-10 08:30:00', 'Listrik',        'Tagihan listrik toko bulan Oktober',      90000.00, 'Tunai'),
(6, '2026-10-15 17:00:00', 'Gaji Karyawan',  'Gaji karyawan toko Oktober',             250000.00, 'Transfer'),
(7, '2026-10-20 14:00:00', 'Pengiriman',     'Biaya antar barang pelanggan',            35000.00, 'Tunai'),
(8, '2026-11-04 09:00:00', 'Pembelian Stok', 'Pembelian keras LP dan semen',           15000.00, 'Tunai'),
(9, '2026-11-10 08:30:00', 'Listrik',        'Tagihan listrik toko bulan November',     18000.00, 'Tunai'),
(10,'2026-11-15 17:00:00', 'Gaji Karyawan',  'Gaji karyawan toko November (setengah bulan)', 25000.00, 'Transfer')
ON DUPLICATE KEY UPDATE
  `tanggal`    = VALUES(`tanggal`),
  `kategori`   = VALUES(`kategori`),
  `keterangan` = VALUES(`keterangan`),
  `jumlah`     = VALUES(`jumlah`),
  `metode`     = VALUES(`metode`);
