-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2017 at 10:47 PM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oblig5`
--

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `clubId` varchar(50) COLLATE utf8_danish_ci NOT NULL,
  `clubName` varchar(50) COLLATE utf8_danish_ci NOT NULL,
  `city` varchar(50) COLLATE utf8_danish_ci NOT NULL,
  `county` varchar(50) COLLATE utf8_danish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `season`
--

CREATE TABLE `season` (
  `userName` varchar(50) COLLATE utf8_danish_ci NOT NULL,
  `fallYear` int(4) UNSIGNED NOT NULL,
  `totalDistance` int(10) UNSIGNED DEFAULT NULL,
  `clubs` varchar(50) COLLATE utf8_danish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skiers`
--

CREATE TABLE `skiers` (
  `userName` varchar(50) COLLATE utf8_danish_ci NOT NULL,
  `firstName` varchar(50) COLLATE utf8_danish_ci NOT NULL,
  `lastName` varchar(50) COLLATE utf8_danish_ci NOT NULL,
  `yearOfBirth` int(4) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`clubId`);

--
-- Indexes for table `season`
--
ALTER TABLE `season`
  ADD PRIMARY KEY (`userName`,`fallYear`),
  ADD KEY `season_ibfk_2` (`clubs`);

--
-- Indexes for table `skiers`
--
ALTER TABLE `skiers`
  ADD PRIMARY KEY (`userName`),
  ADD UNIQUE KEY `userName` (`userName`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `season`
--
ALTER TABLE `season`
  ADD CONSTRAINT `season_ibfk_1` FOREIGN KEY (`userName`) REFERENCES `skiers` (`userName`),
  ADD CONSTRAINT `season_ibfk_3` FOREIGN KEY (`clubs`) REFERENCES `clubs` (`clubId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
