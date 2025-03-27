-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2025 at 09:10 AM
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
-- Database: `kpi2`
--

-- --------------------------------------------------------

--
-- Table structure for table `edit_date`
--

CREATE TABLE `edit_date` (
  `id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `edit_date`
--

INSERT INTO `edit_date` (`id`, `start_date`, `end_date`) VALUES
(1, '2025-03-11', '2025-03-18');

-- --------------------------------------------------------

--
-- Table structure for table `kpimaindata`
--

CREATE TABLE `kpimaindata` (
  `DATAID` int(11) NOT NULL,
  `DATANAME` varchar(255) NOT NULL,
  `DATADATE` year(4) NOT NULL,
  `DATATYPE` varchar(50) NOT NULL,
  `USERID` int(11) NOT NULL,
  `DATAYEAR` int(11) GENERATED ALWAYS AS (year(`DATADATE`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kpimaindata`
--

INSERT INTO `kpimaindata` (`DATAID`, `DATANAME`, `DATADATE`, `DATATYPE`, `USERID`) VALUES
(1, 'saq', '2020', 'KENAF', 1),
(3, 'Reduce Employee Turnover', '2025', 'KENAF', 3),
(6, 'Increase Sales', '2025', 'KENAF', 1),
(7, 'Improve Customer Satisfaction', '2025', 'KENAF', 2),
(11, 'saq', '2025', 'KENAF', 1),
(19, 'BIJI BENIH', '2025', 'KENAF', 1),
(20, 'Sayur', '2026', 'KENAF', 1),
(21, 'BIJI BENIH', '2026', 'KENAF', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kpisubdata`
--

CREATE TABLE `kpisubdata` (
  `SUBID` int(11) NOT NULL,
  `DATAID` int(11) NOT NULL,
  `SUBNAME` varchar(255) NOT NULL,
  `SASARAN` varchar(255) NOT NULL,
  `PENCAPAIAN` varchar(255) DEFAULT NULL,
  `CATATAN` text DEFAULT NULL,
  `JENISUNIT` varchar(50) NOT NULL,
  `USERID` int(11) NOT NULL,
  `DATAMONTH` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kpisubdata`
--

INSERT INTO `kpisubdata` (`SUBID`, `DATAID`, `SUBNAME`, `SASARAN`, `PENCAPAIAN`, `CATATAN`, `JENISUNIT`, `USERID`, `DATAMONTH`) VALUES
(1, 1, 'Achieve 10% Sales Growth', '10% Growth', '8% Growt', 'On track', 'BPP', 1, 1),
(2, 1, 'Expand to New Markets', '2 New Markets', '1 New Market', 'Delayed due to budget', 'BPP', 1, 2),
(4, 3, 'Conduct Employee Surveys', '2 Surveys', '1 Survey', 'Second survey planned', 'UUU', 3, 4),
(5, 1, 'nani', 'a', '', '', 'BPP', 1, 1),
(67, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 1),
(68, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 2),
(69, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 3),
(70, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 4),
(71, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 5),
(72, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 6),
(73, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 7),
(74, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 8),
(75, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 9),
(76, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 10),
(77, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 11),
(78, 19, 'Watermelon', '100', '1', '', 'BPP', 1, 12),
(79, 21, 'T', '1023001803300', '', '', 'BPP', 1, 1),
(80, 21, 'T', '1023001803300', '', '', 'BPP', 1, 2),
(81, 21, 'T', '1023001803300', '', '', 'BPP', 1, 3),
(82, 21, 'T', '1023001803300', '', '', 'BPP', 1, 4),
(83, 21, 'T', '1023001803300', '', '', 'BPP', 1, 5),
(84, 21, 'T', '1023001803300', '', '', 'BPP', 1, 6),
(85, 21, 'T', '1023001803300', '', '', 'BPP', 1, 7),
(86, 21, 'T', '1023001803300', '', '', 'BPP', 1, 8),
(87, 21, 'T', '1023001803300', '', '', 'BPP', 1, 9),
(88, 21, 'T', '1023001803300', '', '', 'BPP', 1, 10),
(89, 21, 'T', '1023001803300', '', '', 'BPP', 1, 11),
(90, 21, 'T', '1023001803300', '', '', 'BPP', 1, 12),
(91, 19, 'q', '1', '', '', 'BPP', 1, 1),
(92, 19, 'q', '1', '', '', 'BPP', 1, 2),
(93, 19, 'q', '1', '', '', 'BPP', 1, 3),
(94, 19, 'q', '1', '', '', 'BPP', 1, 4),
(95, 19, 'q', '1', '', '', 'BPP', 1, 5),
(96, 19, 'q', '1', '', '', 'BPP', 1, 6),
(97, 19, 'q', '1', '', '', 'BPP', 1, 7),
(98, 19, 'q', '1', '', '', 'BPP', 1, 8),
(99, 19, 'q', '1', '', '', 'BPP', 1, 9),
(100, 19, 'q', '1', '', '', 'BPP', 1, 10),
(101, 19, 'q', '1', '', '', 'BPP', 1, 11),
(102, 19, 'q', '1', '', '', 'BPP', 1, 12),
(103, 21, 'q', '1', '', '', 'BPP', 1, 0),
(104, 21, 'nani', '1', '', '', 'BPP', 1, 0),
(105, 21, 'nani', '1', '', '', 'BPP', 1, 1),
(106, 21, 'nani', '1', '', '', 'BPP', 1, 2),
(107, 21, 'nani', '1', '', '', 'BPP', 1, 3),
(108, 21, 'nani', '1', '', '', 'BPP', 1, 4),
(109, 21, 'nani', '1', '', '', 'BPP', 1, 5),
(110, 21, 'nani', '1', '', '', 'BPP', 1, 6),
(111, 21, 'nani', '1', '', '', 'BPP', 1, 7),
(112, 21, 'nani', '1', '', '', 'BPP', 1, 8),
(113, 21, 'nani', '1', '', '', 'BPP', 1, 9),
(114, 21, 'nani', '1', '', '', 'BPP', 1, 10),
(115, 21, 'nani', '1', '', '', 'BPP', 1, 11),
(116, 21, 'nani', '1', '', '', 'BPP', 1, 12);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `USERID` int(11) NOT NULL,
  `USERNAME` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `UNIT` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`USERID`, `USERNAME`, `PASSWORD`, `UNIT`) VALUES
(1, 'a', 'a', 'ADMIN'),
(2, '1', '1', 'BPP'),
(3, 'user2', 'user456', 'BKK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `edit_date`
--
ALTER TABLE `edit_date`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kpimaindata`
--
ALTER TABLE `kpimaindata`
  ADD PRIMARY KEY (`DATAID`),
  ADD UNIQUE KEY `unique_kpi_name_year` (`DATANAME`,`DATAYEAR`);

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
  ADD UNIQUE KEY `USERNAME` (`USERNAME`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `edit_date`
--
ALTER TABLE `edit_date`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kpimaindata`
--
ALTER TABLE `kpimaindata`
  MODIFY `DATAID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `kpisubdata`
--
ALTER TABLE `kpisubdata`
  MODIFY `SUBID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `USERID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kpisubdata`
--
ALTER TABLE `kpisubdata`
  ADD CONSTRAINT `kpisubdata_ibfk_1` FOREIGN KEY (`DATAID`) REFERENCES `kpimaindata` (`DATAID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
