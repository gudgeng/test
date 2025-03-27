-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2025 at 07:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kpi`
--

-- --------------------------------------------------------

--
-- Table structure for table `kpimaindata`
--

CREATE TABLE `kpimaindata` (
  `DATAID` int(11) NOT NULL,
  `DATADATE` date NOT NULL,
  `DATATYPE` enum('KENAF','TEMBAKAU','PENTADBIRAN') NOT NULL,
  `DATANAME` varchar(100) NOT NULL,
  `USERID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kpimaindata`
--

INSERT INTO `kpimaindata` (`DATAID`, `DATADATE`, `DATATYPE`, `DATANAME`, `USERID`) VALUES
(1, '2025-01-31', 'KENAF', 'Keluasan Penanaman Kenaf (Hektar)', 1),
(2, '2025-01-31', 'KENAF', 'Hasil Pengeluaran Kenaf (Ton)', 2),
(3, '2025-01-31', 'KENAF', 'Bil. Usahawan Kenaf', 3),
(4, '2025-01-31', 'TEMBAKAU', 'Pelesenan Tembakau dan Keluaran Tembakau', 1),
(5, '2025-01-31', 'TEMBAKAU', 'Kutipan Hasil FI Lesen', 2),
(6, '2025-01-31', 'PENTADBIRAN', 'Pengukuhan Struktur Organisasi', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kpisubdata`
--

CREATE TABLE `kpisubdata` (
  `SUBID` int(11) NOT NULL,
  `SASARAN` varchar(255) NOT NULL,
  `PENCAPAIAN` varchar(255) NOT NULL,
  `CATATAN` text DEFAULT NULL,
  `JENISUNIT` varchar(50) NOT NULL,
  `DATAID` int(11) DEFAULT NULL,
  `SUBDATANAME` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kpisubdata`
--

INSERT INTO `kpisubdata` (`SUBID`, `SASARAN`, `PENCAPAIAN`, `CATATAN`, `JENISUNIT`, `DATAID`, `SUBDATANAME`) VALUES
(1, '2,483.20', '2,000', 'Pekebun Kecil - 670 hektar', 'BPP', 1, 'Fiber'),
(2, '9,104', '8,000', 'Projek LKTN - 267 hektar', 'BPP', 2, 'Benih'),
(3, '200', '150', 'Pengusaha Output Based - 70 peserta', 'BKK', 3, 'Makanan ternakan'),
(4, '0', '0', 'Data belum dikemaskini', 'BPK', 4, NULL),
(5, '0', '0', 'Tiada kutipan baru', 'BPK', 5, NULL),
(6, '0', '0', 'Belum ada perubahan', 'BKP', 6, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `USERID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `UNIT` enum('BPP','BKK','UUU','BRD','BPM','BKP','TMK','ULT','UAD','UI','ADMIN') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`USERID`, `USERNAME`, `PASSWORD`, `UNIT`) VALUES
(1, 'jj', 'kk', 'ADMIN'),
(2, 'admin', 'admin123', 'ADMIN'),
(3, 'user1', 'password1', 'BPP'),
(4, 'user2', 'password2', 'BKK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kpimaindata`
--
ALTER TABLE `kpimaindata`
  ADD PRIMARY KEY (`DATAID`),
  ADD KEY `USERID` (`USERID`);

--
-- Indexes for table `kpisubdata`
--
ALTER TABLE `kpisubdata`
  ADD PRIMARY KEY (`SUBID`),
  ADD KEY `DATAID` (`DATAID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`USERID`),
  ADD UNIQUE KEY `USERID` (`USERID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kpimaindata`
--
ALTER TABLE `kpimaindata`
  MODIFY `DATAID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kpisubdata`
--
ALTER TABLE `kpisubdata`
  MODIFY `SUBID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `USERID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kpimaindata`
--
ALTER TABLE `kpimaindata`
  ADD CONSTRAINT `kpimaindata_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `user` (`USERID`) ON DELETE SET NULL;

--
-- Constraints for table `kpisubdata`
--
ALTER TABLE `kpisubdata`
  ADD CONSTRAINT `kpisubdata_ibfk_1` FOREIGN KEY (`DATAID`) REFERENCES `kpimaindata` (`DATAID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
