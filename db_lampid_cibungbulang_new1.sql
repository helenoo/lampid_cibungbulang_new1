-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 21, 2025 at 01:16 AM
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
-- Database: `db_lampid_cibungbulang_new1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$cvxNQoEhN4c7lhX9ND75beyeszph2tqG9DeadPVgih.YryPm.kBNO');

-- --------------------------------------------------------

--
-- Table structure for table `data_lampid`
--

CREATE TABLE `data_lampid` (
  `id` int NOT NULL,
  `desa_id` int NOT NULL,
  `kategori` enum('Lahir','Mati','Pindah','Datang') NOT NULL,
  `jumlah` int NOT NULL,
  `tanggal_data` date NOT NULL,
  `diinput_oleh` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `data_lampid`
--

INSERT INTO `data_lampid` (`id`, `desa_id`, `kategori`, `jumlah`, `tanggal_data`, `diinput_oleh`) VALUES
(1, 1, 'Lahir', 22, '2025-01-15', NULL),
(2, 2, 'Mati', 8, '2025-02-20', NULL),
(3, 3, 'Pindah', 15, '2025-03-10', NULL),
(4, 4, 'Datang', 18, '2025-04-05', NULL),
(5, 1, 'Lahir', 35, '2024-05-10', NULL),
(6, 2, 'Lahir', 40, '2024-06-15', NULL),
(7, 3, 'Lahir', 2, '2025-01-01', 1),
(8, 3, 'Lahir', 2, '2025-01-01', 1),
(9, 3, 'Lahir', 3, '2024-01-01', 1),
(10, 1, 'Mati', 2, '2024-01-01', 1),
(11, 5, 'Datang', 5, '2024-01-01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `desa`
--

CREATE TABLE `desa` (
  `id` int NOT NULL,
  `nama_desa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `desa`
--

INSERT INTO `desa` (`id`, `nama_desa`) VALUES
(1, 'Cemplang'),
(2, 'Situ Ilir'),
(3, 'Cibatok 1'),
(4, 'Cibatok 2'),
(5, 'Galuga');

-- --------------------------------------------------------

--
-- Table structure for table `info_kecamatan`
--

CREATE TABLE `info_kecamatan` (
  `id` int NOT NULL,
  `deskripsi_lampid` text,
  `tentang_kecamatan` text,
  `jumlah_penduduk` int DEFAULT '0',
  `luas_wilayah` varchar(50) DEFAULT NULL,
  `luas_tanah` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `info_kecamatan`
--

INSERT INTO `info_kecamatan` (`id`, `deskripsi_lampid`, `tentang_kecamatan`, `jumlah_penduduk`, `luas_wilayah`, `luas_tanah`) VALUES
(1, 'LAMPID adalah singkatan dari Lahir, Mati, Pindah, dan Datang. Sistem ini bertujuan untuk mendata dan memvisualisasikan data kependudukan di Kecamatan Cibungbulang secara digital.', 'Kecamatan Cibungbulang adalah sebuah kecamatan di Kabupaten Bogor, Provinsi Jawa Barat, Indonesia.', 145800, '37.5', '3750');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_lampid`
--
ALTER TABLE `data_lampid`
  ADD PRIMARY KEY (`id`),
  ADD KEY `desa_id` (`desa_id`);

--
-- Indexes for table `desa`
--
ALTER TABLE `desa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `info_kecamatan`
--
ALTER TABLE `info_kecamatan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `data_lampid`
--
ALTER TABLE `data_lampid`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `desa`
--
ALTER TABLE `desa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `info_kecamatan`
--
ALTER TABLE `info_kecamatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_lampid`
--
ALTER TABLE `data_lampid`
  ADD CONSTRAINT `data_lampid_ibfk_1` FOREIGN KEY (`desa_id`) REFERENCES `desa` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
