-- phpMyAdmin SQL Dump
-- Database: `sidu`
-- Sistem Informasi Bank Sampah Sekolah

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Pastikan kamu sudah masuk/memilih database "banksampah_anes" di phpMyAdmin
-- SEBELUM meng-import file ini. Baris CREATE DATABASE/USE sengaja tidak dipakai
-- supaya tabel-tabel ini masuk ke database yang sudah ada, bukan bikin database baru.

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `saldo` decimal(12,2) NOT NULL DEFAULT 0,
  `role` enum('admin','siswa','guru','pengepul') NOT NULL DEFAULT 'siswa',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id_user`, `username`, `password`, `nama`, `saldo`, `role`) VALUES
(1, 'admin', 'admin123', 'Admin SiDU', 0, 'admin'),
(2, 'budi', 'budi123', 'Budi Santoso', 45000, 'siswa'),
(3, 'bu_ani', 'ani123', 'Bu Ani', 20000, 'guru'),
(4, 'pengepul1', 'pengepul123', 'Pak Joko (Pengepul)', 0, 'pengepul');

-- --------------------------------------------------------

--
-- Table structure for table `sampah`
--

CREATE TABLE `sampah` (
  `id_sampah` int(11) NOT NULL AUTO_INCREMENT,
  `jenis_sampah` varchar(50) NOT NULL,
  `harga_per_kg` decimal(10,2) NOT NULL COMMENT 'Harga beli dari nasabah (guru/siswa)',
  `harga_jual_kg` decimal(10,2) NOT NULL DEFAULT 0 COMMENT 'Harga jual ke pengepul',
  `stok_kg` decimal(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_sampah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sampah` (`id_sampah`, `jenis_sampah`, `harga_per_kg`, `harga_jual_kg`, `stok_kg`) VALUES
(1, 'Plastik', 2000, 2500, 500),
(2, 'Kardus', 1500, 1900, 300);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_sampah` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `berat_kg` decimal(10,2) NOT NULL,
  `total_rp` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_transaksi`),
  KEY `id_user` (`id_user`),
  KEY `id_sampah` (`id_sampah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `id_sampah`, `tanggal`, `berat_kg`, `total_rp`) VALUES
(1, 2, 1, '2026-09-01', 2, 4000),
(2, 2, 2, '2026-08-28', 6, 9000);

-- --------------------------------------------------------

--
-- Table structure for table `tarik_saldo`
--

CREATE TABLE `tarik_saldo` (
  `id_tarik` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `nominal` decimal(12,2) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id_tarik`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
-- Pengepul membeli sampah (stok) dari admin
--

CREATE TABLE `penjualan` (
  `id_penjualan` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengepul` int(11) NOT NULL,
  `id_sampah` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `berat_kg` decimal(10,2) NOT NULL,
  `harga_jual_kg` decimal(10,2) NOT NULL,
  `total_rp` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_penjualan`),
  KEY `id_pengepul` (`id_pengepul`),
  KEY `id_sampah` (`id_sampah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topup_saldo`
-- Admin mengisi saldo pengepul supaya bisa membeli sampah
--

CREATE TABLE `topup_saldo` (
  `id_topup` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `nominal` decimal(12,2) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_topup`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Constraints for dumped tables
--

ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_sampah`) REFERENCES `sampah` (`id_sampah`);

ALTER TABLE `tarik_saldo`
  ADD CONSTRAINT `tarik_saldo_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_pengepul`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`id_sampah`) REFERENCES `sampah` (`id_sampah`);

ALTER TABLE `topup_saldo`
  ADD CONSTRAINT `topup_saldo_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

COMMIT;
