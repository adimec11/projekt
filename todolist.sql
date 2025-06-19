-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql209.infinityfree.com
-- Generation Time: Jun 19, 2025 at 10:23 AM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39102746_todolist`
--

-- --------------------------------------------------------

--
-- Table structure for table `pravice`
--

CREATE TABLE `pravice` (
  `id` int(11) NOT NULL,
  `ime` char(20) NOT NULL,
  `opis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovenian_ci;

--
-- Dumping data for table `pravice`
--

INSERT INTO `pravice` (`id`, `ime`, `opis`) VALUES
(1, 'Administrator', 'dostop do vsega'),
(2, 'uporabnik', 'klasičen dostop do spletne strani'),
(3, 'administrator_strani', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `projekti`
--

CREATE TABLE `projekti` (
  `id` int(11) NOT NULL,
  `naslov` char(20) NOT NULL,
  `opis` text NOT NULL,
  `datum_začetka` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `datum_konca` timestamp NULL DEFAULT NULL,
  `skupina_id` int(11) DEFAULT NULL,
  `lastnik_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovenian_ci;

--
-- Dumping data for table `projekti`
--

INSERT INTO `projekti` (`id`, `naslov`, `opis`, `datum_začetka`, `datum_konca`, `skupina_id`, `lastnik_id`, `status`) VALUES
(34, 'blazko', '', '2025-06-19 01:31:42', '2025-07-08 22:00:00', NULL, 3, 0),
(35, 'test', '', '2025-06-19 06:24:26', '2025-06-27 07:00:00', NULL, NULL, 0),
(36, 'zevnik', '', '2025-06-19 06:34:51', '2025-06-27 07:00:00', NULL, NULL, 0),
(37, 'Spletna stran', '', '2025-06-19 11:45:57', '2025-06-27 07:00:00', 48, NULL, 1),
(38, 'SPletna stran', '', '2025-06-19 09:34:51', '2025-07-03 07:00:00', NULL, 3, 0),
(39, 'Spletna stran', '', '2025-06-19 10:11:37', '2025-06-27 07:00:00', 49, NULL, 0),
(40, 'SPletna stran', '', '2025-06-19 10:16:00', '2025-06-26 07:00:00', NULL, 16, 0),
(41, 'Spletna stran', '', '2025-06-19 11:48:16', '2025-06-27 22:00:00', 50, NULL, 0),
(42, 'Spletna stran', '', '2025-06-19 12:04:14', '2025-06-26 22:00:00', 51, NULL, 0),
(44, 'spletišče', '', '2025-06-19 12:11:54', '2025-06-19 22:00:00', NULL, 14, 0),
(45, 'spletne strani', '', '2025-06-19 12:26:26', '2025-06-19 22:00:00', NULL, 14, 0);

-- --------------------------------------------------------

--
-- Table structure for table `skupine`
--

CREATE TABLE `skupine` (
  `id` int(11) NOT NULL,
  `ime` char(20) NOT NULL,
  `vodja_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovenian_ci;

--
-- Dumping data for table `skupine`
--

INSERT INTO `skupine` (`id`, `ime`, `vodja_id`) VALUES
(48, 'računalničari', 5),
(49, 'programerji', 6),
(50, 'koderji', 7),
(51, 'spletniki', 5);

-- --------------------------------------------------------

--
-- Table structure for table `taski`
--

CREATE TABLE `taski` (
  `id` int(11) NOT NULL,
  `naslov` char(20) NOT NULL,
  `opis` text NOT NULL,
  `datum_konca` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `datum_začetka` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `projekt_id` int(11) DEFAULT NULL,
  `uporabnik_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovenian_ci;

--
-- Dumping data for table `taski`
--

INSERT INTO `taski` (`id`, `naslov`, `opis`, `datum_konca`, `status`, `datum_začetka`, `projekt_id`, `uporabnik_id`) VALUES
(21, 'logincic', '', '2025-06-27 07:00:00', 0, '2025-06-19 06:33:52', 35, 3),
(23, 'login', '', '2025-06-28 07:00:00', 0, '2025-06-19 06:35:02', 36, NULL),
(24, 'login', '', '2025-06-27 07:00:00', 1, '2025-06-19 11:46:00', 37, 3),
(25, 'logout', '', '2025-06-20 07:00:00', 1, '2025-06-19 12:26:49', 37, 3),
(26, 'login', '', '2025-06-20 07:00:00', 0, '2025-06-19 09:35:01', 38, NULL),
(27, 'registracija', '', '2025-07-11 07:00:00', 0, '2025-06-19 09:40:26', 37, 3),
(28, 'login', '', '2025-06-20 07:00:00', 1, '2025-06-19 11:32:13', 39, 14),
(29, 'login', '', '2025-06-20 07:00:00', 1, '2025-06-19 11:56:32', 39, 16),
(30, 'login', '', '2025-06-20 07:00:00', 1, '2025-06-19 10:16:48', 40, NULL),
(31, 'login', '', '2025-06-26 22:00:00', 0, '2025-06-19 11:49:29', 41, 3),
(32, 'logout', '', '2025-06-25 22:00:00', 0, '2025-06-19 11:51:24', 41, 3),
(33, 'sign', '', '2025-06-19 22:00:00', 0, '2025-06-19 11:48:56', 41, NULL),
(34, 'login', '', '2025-06-26 22:00:00', 1, '2025-06-19 12:02:14', 34, NULL),
(35, 'logout', '', '2025-06-26 22:00:00', 0, '2025-06-19 12:02:23', 34, NULL),
(36, 'glavna stran', '', '2025-06-29 22:00:00', 0, '2025-06-19 12:02:41', 34, NULL),
(37, 'login', '', '2025-06-27 22:00:00', 0, '2025-06-19 12:04:22', 42, NULL),
(38, 'login', '', '2025-06-27 22:00:00', 0, '2025-06-19 12:04:57', 42, NULL),
(40, 'login', '', '2025-06-20 22:00:00', 1, '2025-06-19 12:12:10', 44, NULL),
(41, 'logout', '', '2025-06-25 22:00:00', 1, '2025-06-19 12:26:09', 44, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `uporabniki`
--

CREATE TABLE `uporabniki` (
  `id` int(11) NOT NULL,
  `ime` char(20) NOT NULL,
  `priimek` char(20) NOT NULL,
  `uporabnisko_ime` char(20) NOT NULL,
  `e-posta` char(30) NOT NULL,
  `geslo` varchar(255) NOT NULL,
  `tel_st` char(15) DEFAULT NULL,
  `pravica_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovenian_ci;

--
-- Dumping data for table `uporabniki`
--

INSERT INTO `uporabniki` (`id`, `ime`, `priimek`, `uporabnisko_ime`, `e-posta`, `geslo`, `tel_st`, `pravica_id`) VALUES
(3, 'blaž', 'kristan', 'kristan', 'blaz.kristan@scv.si', '$2y$10$DDe1niabGE/5ASTQrVe84umbOV.Or9XNrVcb1ESvLmoly7qRq37lm', '', 1),
(12, 'Tilen', 'Zavolovšek', 'tilcika', 'tilen.zavolovsek@gmail.com', '$2y$10$pi/8YF5AhP9RqHFmBu6m9OUbEVRqnXQbCzMxsOKk5nKySOY9mn1bC', '', 1),
(13, 'jan', 'meh', 'jan', 'jan.meh@scv.si', '$2y$10$Zy7hasochjR4aqpqjPOgPuiyet9Op5QqqyIoy.P9VN.jO1UQ3MgQm', '', 1),
(14, 'Andraž', 'Dimec', 'admin', 'adimec78910@gmail.com', '$2y$10$tOG0XYeaM7i/D3NaNV.aSOdDCCbII0QVKmzsZkCcNVQGkj6h2r1ai', '', 3),
(16, 'Nik', 'kristan', 'kiki', 'nik.blagus@scv.si', '$2y$10$ebvP0RXbY/XS3QZkwpqnJu6GGMFL4ktpw/ulGpc9YbXa8VmvF/h4W', '', 1),
(17, 'Aleša', 'puklič', 'alesa', 'alesa.puklic@gmail.com', '$2y$10$l87L4Kn3UZsFBi9xErhn4e9cqBi0g0nfZBme6f.j0k.o3dFTucffO', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `uporabniki_skupine`
--

CREATE TABLE `uporabniki_skupine` (
  `uporabnik_id` int(11) NOT NULL,
  `skupina_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovenian_ci;

--
-- Dumping data for table `uporabniki_skupine`
--

INSERT INTO `uporabniki_skupine` (`uporabnik_id`, `skupina_id`) VALUES
(3, 48),
(3, 50),
(3, 51),
(14, 49),
(14, 50),
(16, 49),
(16, 51);

-- --------------------------------------------------------

--
-- Table structure for table `vodje_skupine`
--

CREATE TABLE `vodje_skupine` (
  `id` int(11) NOT NULL,
  `uporabnik_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovenian_ci;

--
-- Dumping data for table `vodje_skupine`
--

INSERT INTO `vodje_skupine` (`id`, `uporabnik_id`) VALUES
(6, 3),
(1, 11),
(3, 12),
(4, 13),
(5, 14),
(7, 16);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pravice`
--
ALTER TABLE `pravice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projekti`
--
ALTER TABLE `projekti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship6` (`skupina_id`),
  ADD KEY `IX_RelationshipLastnik` (`lastnik_id`);

--
-- Indexes for table `skupine`
--
ALTER TABLE `skupine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_RelationshipVodja` (`vodja_id`);

--
-- Indexes for table `taski`
--
ALTER TABLE `taski`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_RelationshipProjekt` (`projekt_id`),
  ADD KEY `IX_RelationshipUporabnik` (`uporabnik_id`);

--
-- Indexes for table `uporabniki`
--
ALTER TABLE `uporabniki`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uporabnisko_ime` (`uporabnisko_ime`),
  ADD UNIQUE KEY `e-posta` (`e-posta`);

--
-- Indexes for table `uporabniki_skupine`
--
ALTER TABLE `uporabniki_skupine`
  ADD PRIMARY KEY (`uporabnik_id`,`skupina_id`);

--
-- Indexes for table `vodje_skupine`
--
ALTER TABLE `vodje_skupine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_RelationshipVodja` (`uporabnik_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pravice`
--
ALTER TABLE `pravice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projekti`
--
ALTER TABLE `projekti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `skupine`
--
ALTER TABLE `skupine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `taski`
--
ALTER TABLE `taski`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `uporabniki`
--
ALTER TABLE `uporabniki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `vodje_skupine`
--
ALTER TABLE `vodje_skupine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projekti`
--
ALTER TABLE `projekti`
  ADD CONSTRAINT `IX_Relationship6` FOREIGN KEY (`skupina_id`) REFERENCES `skupine` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_projekti_lastnik` FOREIGN KEY (`lastnik_id`) REFERENCES `uporabniki` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `skupine`
--
ALTER TABLE `skupine`
  ADD CONSTRAINT `IX_RelationshipVodja` FOREIGN KEY (`vodja_id`) REFERENCES `vodje_skupine` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `taski`
--
ALTER TABLE `taski`
  ADD CONSTRAINT `IX_RelationshipProjekt` FOREIGN KEY (`projekt_id`) REFERENCES `projekti` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `IX_RelationshipUporabnik` FOREIGN KEY (`uporabnik_id`) REFERENCES `uporabniki` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
