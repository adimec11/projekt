-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gostitelj: 127.0.0.1
-- Čas nastanka: 30. maj 2025 ob 12.24
-- Različica strežnika: 10.4.32-MariaDB
-- Različica PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Zbirka podatkov: `todolist`
--

-- --------------------------------------------------------

--
-- Struktura tabele `pravice`
--

CREATE TABLE `pravice` (
  `id` int(11) NOT NULL,
  `ime` char(20) NOT NULL,
  `opis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

--
-- Odloži podatke za tabelo `pravice`
--

INSERT INTO `pravice` (`id`, `ime`, `opis`) VALUES
(1, 'Administrator', 'dostop do vsega'),
(2, 'uporabnik', 'klasičen dostop do spletne strani');

-- --------------------------------------------------------

--
-- Struktura tabele `projekti`
--

CREATE TABLE `projekti` (
  `id` int(11) NOT NULL,
  `naslov` char(20) NOT NULL,
  `opis` text NOT NULL,
  `datum_začetka` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `datum_konca` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `skupina_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Struktura tabele `skupine`
--

CREATE TABLE `skupine` (
  `id` int(11) NOT NULL,
  `ime` char(20) NOT NULL,
  `vodja_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Struktura tabele `taski`
--

CREATE TABLE `taski` (
  `id` int(11) NOT NULL,
  `naslov` char(20) NOT NULL,
  `opis` text NOT NULL,
  `datum_konca` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) NOT NULL,
  `datum_začetka` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `projekt_id` int(11) DEFAULT NULL,
  `uporabnik_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Struktura tabele `uporabniki`
--

CREATE TABLE `uporabniki` (
  `id` int(11) NOT NULL,
  `ime` char(20) NOT NULL,
  `priimek` char(20) NOT NULL,
  `uporabnisko_ime` char(20) NOT NULL,
  `e-posta` char(30) NOT NULL,
  `geslo` char(20) NOT NULL,
  `tel_st` char(15) DEFAULT NULL,
  `pravica_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Struktura tabele `vodje_skupine`
--

CREATE TABLE `vodje_skupine` (
  `id` int(11) NOT NULL,
  `uporabnik_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

--
-- Indeksi zavrženih tabel
--

--
-- Indeksi tabele `pravice`
--
ALTER TABLE `pravice`
  ADD PRIMARY KEY (`id`);

--
-- Indeksi tabele `projekti`
--
ALTER TABLE `projekti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship6` (`skupina_id`);

--
-- Indeksi tabele `skupine`
--
ALTER TABLE `skupine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship5` (`vodja_id`);

--
-- Indeksi tabele `taski`
--
ALTER TABLE `taski`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship2` (`projekt_id`),
  ADD KEY `IX_Relationship3` (`uporabnik_id`);

--
-- Indeksi tabele `uporabniki`
--
ALTER TABLE `uporabniki`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship1` (`pravica_id`);

--
-- Indeksi tabele `vodje_skupine`
--
ALTER TABLE `vodje_skupine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship4` (`uporabnik_id`);

--
-- AUTO_INCREMENT zavrženih tabel
--

--
-- AUTO_INCREMENT tabele `pravice`
--
ALTER TABLE `pravice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT tabele `skupine`
--
ALTER TABLE `skupine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT tabele `uporabniki`
--
ALTER TABLE `uporabniki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT tabele `vodje_skupine`
--
ALTER TABLE `vodje_skupine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Omejitve tabel za povzetek stanja
--

--
-- Omejitve za tabelo `projekti`
--
ALTER TABLE `projekti`
  ADD CONSTRAINT `Relationship6` FOREIGN KEY (`skupina_id`) REFERENCES `skupine` (`id`);

--
-- Omejitve za tabelo `skupine`
--
ALTER TABLE `skupine`
  ADD CONSTRAINT `Relationship5` FOREIGN KEY (`vodja_id`) REFERENCES `vodje_skupine` (`id`);

--
-- Omejitve za tabelo `taski`
--
ALTER TABLE `taski`
  ADD CONSTRAINT `Relationship2` FOREIGN KEY (`projekt_id`) REFERENCES `projekti` (`id`),
  ADD CONSTRAINT `Relationship3` FOREIGN KEY (`uporabnik_id`) REFERENCES `uporabniki` (`id`);

--
-- Omejitve za tabelo `uporabniki`
--
ALTER TABLE `uporabniki`
  ADD CONSTRAINT `Relationship1` FOREIGN KEY (`pravica_id`) REFERENCES `pravice` (`id`);

--
-- Omejitve za tabelo `vodje_skupine`
--
ALTER TABLE `vodje_skupine`
  ADD CONSTRAINT `Relationship4` FOREIGN KEY (`uporabnik_id`) REFERENCES `uporabniki` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
