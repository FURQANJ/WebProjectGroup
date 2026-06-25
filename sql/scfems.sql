-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 25, 2026 at 01:19 PM
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
-- Database: `scfems`
--
CREATE DATABASE IF NOT EXISTS `scfems` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `scfems`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(50) NOT NULL,
  `admin_name` varchar(50) DEFAULT NULL,
  `maintenance_status` varchar(50) DEFAULT NULL,
  `venue_and_equipment_update` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `password`, `admin_name`, `maintenance_status`, `venue_and_equipment_update`) VALUES
(1, 'bagus', 'IBAD', NULL, ''),
(2, 'mirul123', 'EMMIRUL', NULL, NULL),
(3, 'hassan123', 'HASSAN', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

DROP TABLE IF EXISTS `booking`;
CREATE TABLE IF NOT EXISTS `booking` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `booking_details` varchar(255) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `rejection_reason` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `guest_id` (`guest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `booking_status`, `booking_details`, `guest_id`, `rejection_reason`) VALUES
(16, 'Pending', 'Furqan	01114496805	fur@student.utem.edu.my	Riadah	Tennis Court (1)	2026-06-26	18:30	21:30	Tennis Ball	5', 5, NULL),
(17, 'Rejected', 'Jazib	0135313685	jazib@student.utem.edu.my	Nak belajar tennis dgn kawan2	Tennis Court (2)	2026-06-26	20:30	22:30	Tennis Ball	5', 6, ''),
(18, 'Approved', 'Furqan	01114496805	fur@gmail.com	Nak main basketball lepas jumaat	Basketball Court (1)	2026-06-26	15:15	17:15	Basketball Ball	2', 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
CREATE TABLE IF NOT EXISTS `equipment` (
  `equipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `equipment_status` varchar(50) NOT NULL DEFAULT 'AVAILABLE',
  `equipment_quantity` int(11) DEFAULT 1,
  `equipment_details` varchar(255) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`equipment_id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=508 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `equipment_status`, `equipment_quantity`, `equipment_details`, `booking_id`) VALUES
(501, 'AVAILABLE', 15, 'Futsal Ball', NULL),
(502, 'NOT AVAILABLE', 1, 'Badminton Shuttlecock', NULL),
(503, 'AVAILABLE', 16, 'Hockey Stick', NULL),
(504, 'AVAILABLE', 20, 'Tennis Ball', NULL),
(505, 'AVAILABLE', 20, 'Basketball Ball', NULL),
(506, 'AVAILABLE', 10, 'Football Ball', NULL),
(507, 'AVAILABLE', 8, 'Rugby Ball', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `guest`
--

DROP TABLE IF EXISTS `guest`;
CREATE TABLE IF NOT EXISTS `guest` (
  `guest_id` int(11) NOT NULL AUTO_INCREMENT,
  `guest_name` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `matrik` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL,
  PRIMARY KEY (`guest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest`
--

INSERT INTO `guest` (`guest_id`, `guest_name`, `password`, `matrik`, `email`, `otp_code`, `otp_expiry`, `is_verified`) VALUES
(1, NULL, 'bijak', 'D032410377', 'D032410377@student.utem.edu.my', NULL, NULL, 0),
(4, 'Emmirul', '$2y$10$O2ztIskOsuCtPgZwDYjWt..HkOw99fiT2xB3vFsQh60gspwQ.LrXK', 'D032410370', 'emmiruliqwann@gmail.com', '413951', '2026-06-22 17:01:27', 0),
(5, 'Furqan', '$2y$10$b.sPTpFsj7BwewbusZ/n4.y4I5K7Ez5YAIA06q0wPUnCu7D3rwNrS', 'd032410185', 'fur@gmail.com', NULL, NULL, 1),
(6, 'Jazib', '$2y$10$7LzQE4Q5mQM7UPtXhz7KouFmhVcr8UihiVbRl173s8rpKcKQxh2ku', 'D032410108', 'jazib@gmail.com', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `guest_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `login_record` varchar(100) DEFAULT NULL,
  KEY `guest_id` (`guest_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`guest_id`, `admin_id`, `login_record`) VALUES
(1, 1, NULL),
(1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

DROP TABLE IF EXISTS `maintenance`;
CREATE TABLE IF NOT EXISTS `maintenance` (
  `maintenance_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `venue_id` int(11) DEFAULT NULL,
  `maintenance_report` varchar(255) DEFAULT NULL,
  `maintenance_status` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`maintenance_id`),
  KEY `admin_id` (`admin_id`),
  KEY `equipment_id` (`equipment_id`),
  KEY `venue_id` (`venue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

DROP TABLE IF EXISTS `venue`;
CREATE TABLE IF NOT EXISTS `venue` (
  `venue_id` int(11) NOT NULL AUTO_INCREMENT,
  `venue_status` varchar(50) NOT NULL DEFAULT 'AVAILABLE',
  `venue_details` varchar(255) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`venue_id`),
  KEY `guest_id` (`guest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`venue_id`, `venue_status`, `venue_details`, `guest_id`) VALUES
(101, 'AVAILABLE', 'Tennis Court (1)', NULL),
(102, 'AVAILABLE', 'Tennis Court (2)', NULL),
(103, 'AVAILABLE', 'Futsal Court (1)', NULL),
(104, 'AVAILABLE', 'Futsal Court (2)', NULL),
(105, 'AVAILABLE', 'Basketball Court (1)', NULL),
(106, 'AVAILABLE', 'Basketball Court (2)', NULL),
(107, 'AVAILABLE', 'Badminton Court (1)', NULL),
(108, 'AVAILABLE', 'Badminton Court (2)', NULL),
(109, 'AVAILABLE', 'Football Field', NULL),
(110, 'AVAILABLE', 'Rugby Field', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guest` (`guest_id`) ON DELETE SET NULL;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE SET NULL;

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guest` (`guest_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `login_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_ibfk_3` FOREIGN KEY (`venue_id`) REFERENCES `venue` (`venue_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
