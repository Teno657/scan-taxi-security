-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2026 at 11:28 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taxi_yaounde`
--

-- --------------------------------------------------------

--
-- Table structure for table `taxis`
--

CREATE TABLE `taxis` (
  `id` int(11) NOT NULL,
  `code_unique` varchar(20) NOT NULL,
  `immatriculation` varchar(20) DEFAULT NULL,
  `statut` enum('actif','inactif','suspendu') DEFAULT 'actif',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_affectation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `taxis`
--

INSERT INTO `taxis` (`id`, `code_unique`, `immatriculation`, `statut`, `date_creation`, `date_affectation`) VALUES
(80, 'TAXI-0E449AC5', 'CE601VW', 'actif', '2026-11-08 09:22:47', NULL),
(81, 'TAXI-72838B88', 'CE194AJ', 'actif', '2026-11-08 09:49:33', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `taxis`
--
ALTER TABLE `taxis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_unique` (`code_unique`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `taxis`
--
ALTER TABLE `taxis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
