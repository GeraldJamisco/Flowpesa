-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2025 at 01:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flowpesa`
--

-- --------------------------------------------------------

--
-- Table structure for table `registration_flows`
--

CREATE TABLE `registration_flows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_code` varchar(8) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `msisdn` varchar(32) NOT NULL,
  `phone_otp_hash` varchar(255) DEFAULT NULL,
  `phone_otp_expires_at` datetime DEFAULT NULL,
  `phone_verified` tinyint(1) NOT NULL DEFAULT 0,
  `email` varchar(190) DEFAULT NULL,
  `email_otp_hash` varchar(255) DEFAULT NULL,
  `email_otp_expires_at` datetime DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `temp_passcode_hash` varchar(255) DEFAULT NULL,
  `passcode_hash` varchar(255) DEFAULT NULL,
  `step` varchar(50) NOT NULL DEFAULT 'phone',
  `attempts_phone` int(11) NOT NULL DEFAULT 0,
  `attempts_email` int(11) NOT NULL DEFAULT 0,
  `citizenship_country` varchar(250) NOT NULL,
  `citizenship_is_citizen` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_doc_type` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration_flows`
--

INSERT INTO `registration_flows` (`id`, `country_code`, `phone`, `msisdn`, `phone_otp_hash`, `phone_otp_expires_at`, `phone_verified`, `email`, `email_otp_hash`, `email_otp_expires_at`, `email_verified`, `temp_passcode_hash`, `passcode_hash`, `step`, `attempts_phone`, `attempts_email`, `citizenship_country`, `citizenship_is_citizen`, `created_at`, `updated_at`, `id_doc_type`) VALUES
(8, '+256', '+256759603080', '+256256759603080', NULL, NULL, 1, 'flowpesa@example.com', NULL, NULL, 1, NULL, '$2y$10$tn5Gxd5X.Rji9YamDk9lMuYbL5w2WVtWTBUd6VoKnV.mqRXktOf2u', 'id_type', 0, 0, '', '', '2025-11-17 23:51:42', '2025-11-18 00:50:58', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `registration_flows`
--
ALTER TABLE `registration_flows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_msisdn_step` (`msisdn`,`step`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `registration_flows`
--
ALTER TABLE `registration_flows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
