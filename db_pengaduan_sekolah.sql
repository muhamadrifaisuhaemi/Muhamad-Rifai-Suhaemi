-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 10, 2026 at 07:13 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pengaduan_sekolah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_petugas` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_petugas`) VALUES
(1, 'admin_galak', 'galak', 'rifai'),
(2, 'admin_salak', 'salak', 'wahyu'),
(3, 'laskar', '$2y$10$SEugdKEBaXbwFjKUr7hdrubZHGQ2gQIU/K4nYXxNs5iHGHV9WAGhm', 'rifai'),
(4, '123', '$2y$10$4XYdLzC4v0CHRUmZi1LjCetfRizhhuZKSMFkyZ/Onw.8S1ec0de6m', 'muhii');

-- --------------------------------------------------------

--
-- Table structure for table `aspirasi`
--

CREATE TABLE `aspirasi` (
  `id_aspirasi` int NOT NULL,
  `nis` char(10) NOT NULL,
  `id_kategori` int NOT NULL,
  `lokasi` varchar(50) NOT NULL,
  `keterangan` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Menunggu','Proses','Selesai') NOT NULL DEFAULT 'Menunggu',
  `feedback` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `aspirasi`
--

INSERT INTO `aspirasi` (`id_aspirasi`, `nis`, `id_kategori`, `lokasi`, `keterangan`, `foto`, `tanggal`, `status`, `feedback`) VALUES
(1, '1234', 3, 'lapangan', 'berantem', 'pngtree-car-top-view-vector-png-image_6890955.png', '2026-03-31', 'Selesai', 'jagalah kerukunan'),
(2, '444', 2, 'kelas', 'kurang bersih', '69cb2e7b841c6.png', '2026-03-31', 'Menunggu', ''),
(3, '1111', 5, 'perpus', 'kurang buku', '69cb3175dc52d_download.png', '2026-03-31', 'Menunggu', ''),
(4, '1111', 3, 'kantin', 'banyak copet', '69cb3aab5eb6a.png', '2026-03-31', 'Proses', 'sabaarrr sedang di tindak lanjuti'),
(5, '2222', 4, 'kelas', 'kotor bau', '69cb444dd2c43.png', '2026-03-31', 'Selesai', 'okey sudah di bersihkan'),
(6, '10010', 5, 'perpustakaan bi', 'bukunya ga komplit', '69cb4cd21a860.webp', '2026-03-31', 'Proses', 'siappp sedang dikordinasi'),
(7, '6666', 1, 'sekitar belakang ruang kelas', 'butuh kerja sama ', '69cb570188852.webp', '2026-03-31', 'Selesai', 'samamamamamammamamamamama'),
(8, '6666', 2, 'depan kelas', 'kondisi yang kacaw', '69cb57de7b216.webp', '2026-03-31', 'Selesai', 'kanjut'),
(9, '6666', 1, 'kelas XII rpl', 'tolong lebih rapih dan bersih', '69cb597796098.webp', '2025-12-04', 'Menunggu', ''),
(10, '1222', 3, 'kantin', 'buku banyak yang hilang', '69cb5ab58630a.jpg', '2026-06-03', 'Menunggu', ''),
(11, '34', 4, 'percuss', 'mati listrik', '69cb5d1caa841.jpg', '2026-03-28', 'Selesai', 'wegah ah sia mah bau'),
(12, '9999', 2, 'lab', 'kotor pisan', '69cb707bc621b.webp', '2026-08-06', 'Menunggu', ''),
(13, '2', 3, 'tangga ', 'licin ', '69cb7182cc60a.jpg', '2026-09-17', 'Menunggu', ''),
(14, '4', 4, 'lab mp', 'terjadi kerusakan pada pc', '69cb725983c11.webp', '2026-10-08', 'Menunggu', 'oke'),
(15, '0', 5, 'belakang perpus', 'ada kerusakaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaan', '69cb73f63baf7.webp', '2026-07-17', 'Menunggu', ''),
(16, '12', 4, 'depan lab', 'bauu', '69d752034b94b.png', '2026-11-12', 'Selesai', 'ukuk'),
(18, '1111', 1, 'lapangan', 'lairr lariii', '69cdc72cbe699.webp', '2026-04-02', 'Menunggu', ''),
(19, '1111', 4, 'lab mipa', 'bauuuuu', '69cdc80622346.webp', '2026-05-15', 'Selesai', 'okee'),
(20, '111', 3, 'Kelas X DKV', 'bauu', '69d72d9f99c66.png', '2026-04-09', 'Proses', 'okee'),
(21, '111', 1, 'KELAS XI TKJ', 'ga bersih', '69d732e60b3be.png', '2026-04-09', 'Proses', 'oke sedang di tindak lanjut'),
(22, '7777', 2, 'toilet', 'tolong buat nyaman', '69d89d34032e1.jpg', '2026-04-10', 'Selesai', 'siap');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'kelas'),
(2, 'kebersihan'),
(3, 'keamanan'),
(4, 'laboratorium'),
(5, 'perpustakaan');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `nis` char(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`nis`, `nama`, `kelas`, `password`) VALUES
('0', 'sarpin', 'XII DKV', '$2y$10$0Ef02eeE/dR1fq45IRT4oOiErXkr4WJY4d4Aai98n4M/bD8CV3FbW'),
('10', 'kodir', 'gokal', '$2y$10$NbSRiTuSA90xC/c8REc9TOL/zQdJoOIULb8XLTCj4YgMdglUZ6CNO'),
('10010', 'claR', 'XII TKJ 2', '$2y$10$k8OKTfMzrDCLTtWyUpra5.qJ8FqYwYXLdyJCVukniwHUe/js..J..'),
('111', 'yaya', 'XII RPL', '$2y$10$LUte9hvnC9p0JaOowto6MuwyDXCIdI2649HbQHhQ6mf1XcwbrETb.'),
('1111', 'zaed', 'XII TKJ', '123'),
('12', 'sarpin', 'XII DKV', '$2y$10$rIBQMGNmmmF/6gUGT7cebOxVPq5UHyu479CGQB5h22sgr/SNZhgNe'),
('1222', 'kohn', 'XII Mesin', '$2y$10$7AbA8RjfSDahJBnDQE/pMecRbhDQfiYylJC3qAnIKlzvdzJD8dEzy'),
('1234', 'Abidin', 'XII RPL', '478'),
('12345', 'abdul', NULL, '$2y$10$jfmBYHJb9K3ecxkAMgQqXuO7UXUMnsaxjAuX6X3mbffs5P1Oswl2S'),
('178', 'kakina', 'XII TKJ', '$2y$10$B.MUm.wdMpwLXVy7Ct3Hi.cfS5kwcJiCs4mmj2G/iD6UO3Q2PJbvq'),
('2', 'sartun', 'otomotif', '$2y$10$4o1MKO3mzxtyFilwojJauuNHG5uFDuKYVGT22/36QDCcLyMrEO99C'),
('2222', 'Ilham Yusuf', 'XII DKV', '$2y$10$4RTgWbH0vc9UPn6w5HcSVe0qNbC9CFHT8Yq6utZvAiRQ9qUyeV8eq'),
('333', 'agung', 'XII RPL', '$2y$10$NAeJv7T7VdY46EKruHEei.O5afgsIOQs5.ZmV4QApgl3YHIBOqMAW'),
('3333', 'samsu', 'XII MP', '$2y$10$BOXijKr8sl5LYsA1utzLC.CIPWNdbMiaEk.I3nQhwFNK4AcHWfevW'),
('34', 'emi', 'XII DKV', '$2y$10$x/xmkZ7rHZcjfD3mP098kuKjW5Hs.ENUHlukkC7Tyz960RcybmQd6'),
('4', 'petros', 'gudang 2', '$2y$10$aqYfpFlz6SsT93QygRflweNW4xGhbnmEMEF0qG70kV20HU64ePMzu'),
('444', 'Rake', NULL, '$2y$10$dUdyszW/d0KvXPhJ7VExo.5fthC0Zah7tcD6E03KPbVHBN6DNQ5k6'),
('4444', 'muhamad', 'Mekanik', '$2y$10$fYKcaEVLHxz8BlGIHkpgqeRIZmU3w8vecXH0/m0cTmEKuJg1NW7OW'),
('6666', 'samsu', 'XII MP', '$2y$10$BEDfPX2st6.vwW3KztjtyOsUnkFOdNRNWXQXsRe0QfEcq0BMO.N4O'),
('7777', 'RIZKI GUNAWAN', 'pembangunan', '$2y$10$vu5S7SAYmDca6IGI71.YGeqGQ0yLPWMBYPysuLk4DSEO88o6G4Fx.'),
('8', 'udin', 'pertenakan', '$2y$10$/UxD.zYgmVyPF2QqIgrz3utBwgxb./yWHxOEhQvL7g2aOAGngT06G'),
('80', 'maman', 'pembangunan', '$2y$10$0.rRpnlb06GW8cD2GQu0Uu/.je8TDorS1kWDHFZ5tyyqvJs0aT8YK'),
('9999', 'udin', 'pertenakan', '$2y$10$kJyOWHwlXzBo7ZriHdJqwuSB1M2LKoeVkxLIzlw95w4I9zTKGXTNm');

-- --------------------------------------------------------

--
-- Table structure for table `tanggapan`
--

CREATE TABLE `tanggapan` (
  `id_tanggapan` int NOT NULL,
  `id_aspirasi` int NOT NULL,
  `tgl_tanggapan` date DEFAULT NULL,
  `id_admin` int NOT NULL,
  `foto_tanggapan` varchar(255) DEFAULT NULL,
  `tanggapan` text NOT NULL,
  `tanggal_tanggapan` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tanggapan`
--

INSERT INTO `tanggapan` (`id_tanggapan`, `id_aspirasi`, `tgl_tanggapan`, `id_admin`, `foto_tanggapan`, `tanggapan`, `tanggal_tanggapan`) VALUES
(1, 20, '2026-04-09', 4, NULL, 'okee', '2026-04-09 04:45:23'),
(2, 16, '2026-04-09', 4, NULL, 'ukuk', '2026-04-09 05:03:14'),
(3, 14, '2026-04-09', 4, '69d75c1260ea9.png', 'oke', '2026-04-09 07:58:10'),
(4, 21, NULL, 4, '69d7c0e9525c9.jpg', 'oke sedang di tindak lanjut', '2026-04-09 15:08:25'),
(5, 22, '2026-04-10', 4, '69d89d77ed211.jpg', 'siap', '2026-04-10 06:49:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`);

--
-- Indexes for table `tanggapan`
--
ALTER TABLE `tanggapan`
  ADD PRIMARY KEY (`id_tanggapan`),
  ADD KEY `id_aspirasi` (`id_aspirasi`),
  ADD KEY `id_admin` (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `aspirasi`
--
ALTER TABLE `aspirasi`
  MODIFY `id_aspirasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tanggapan`
--
ALTER TABLE `tanggapan`
  MODIFY `id_tanggapan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tanggapan`
--
ALTER TABLE `tanggapan`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `fk_aspirasi` FOREIGN KEY (`id_aspirasi`) REFERENCES `aspirasi` (`id_aspirasi`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
