-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 08, 2026 at 11:22 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_eportal`
--
CREATE DATABASE IF NOT EXISTS `db_eportal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_eportal`;

-- --------------------------------------------------------

--
-- Table structure for table `client_satisfaction`
--

DROP TABLE IF EXISTS `client_satisfaction`;
CREATE TABLE IF NOT EXISTS `client_satisfaction` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `government_agency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_visited` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `sex` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_availed` text COLLATE utf8mb4_unicode_ci,
  `other_service` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_1` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_2` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_3` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_4` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_5` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_6` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_7` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_8` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complaint` text COLLATE utf8mb4_unicode_ci,
  `suggestions` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no_unique` (`reference_no`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_satisfaction`
--

INSERT INTO `client_satisfaction` (`id`, `reference_no`, `client_type`, `business_name`, `government_agency`, `name`, `contact_number`, `email_address`, `office_visited`, `date`, `sex`, `service_availed`, `other_service`, `rating_1`, `rating_2`, `rating_3`, `rating_4`, `rating_5`, `rating_6`, `rating_7`, `rating_8`, `complaint`, `suggestions`, `created_at`, `updated_at`) VALUES
(6, 'HR-20260825213017', 'General Public', '', '', 'Remart Maranan', '09266795288', 'remartm2@gmail.com', 'ORD', '2026-08-26', 'Male', 'Registration of Collective Bargaining Agreement, Registration of Contractors, Registration of Union', 'Performance Evaluation', 'Strongly agree', 'Agree', 'Neither', 'N/A', 'N/A', 'Disagree', 'Strongly disagree', 'Strongly disagree', 'ejfjnejenfjn', 'njnejfnejfnejfn', '2026-08-26 03:22:45', '2026-08-26 03:22:45'),
(7, 'HR-20260826040512', 'General Public', '', '', 'Remart Maranan', '09265414121', 'remartm2@gmail.com', 'ORD', '2000-10-16', 'Male', '', 'IPCR', 'Strongly agree', 'Agree', 'Neither', 'N/A', 'N/A', 'Disagree', 'Strongly disagree', 'Neither', '', '', '2026-08-26 04:10:34', '2026-08-26 04:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `hr_requests`
--

DROP TABLE IF EXISTS `hr_requests`;
CREATE TABLE IF NOT EXISTS `hr_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_availed` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contract_of_service_year` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fullname` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_address` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_category` enum('COS','PERMANENT') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `period_of_service` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_division` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certification` text COLLATE utf8mb4_unicode_ci,
  `ipcr_period` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `performance_evaluation_period` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documents_201` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `specify_201` text COLLATE utf8mb4_unicode_ci,
  `other_request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_requested` date NOT NULL,
  `status` enum('New Application','Processing','For Released','Completed','Rejected','Canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'New Application',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `date_received` date DEFAULT NULL,
  `received_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_released` date DEFAULT NULL,
  `released_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_processing` datetime DEFAULT NULL,
  `date_completed` datetime DEFAULT NULL,
  `date_rejected` datetime DEFAULT NULL,
  `date_canceled` datetime DEFAULT NULL,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=200 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hr_requests`
--

INSERT INTO `hr_requests` (`id`, `reference_no`, `service_availed`, `contract_of_service_year`, `service_purpose`, `fullname`, `contact_number`, `email_address`, `employee_category`, `period_of_service`, `position`, `office_division`, `certification`, `ipcr_period`, `performance_evaluation_period`, `documents_201`, `specify_201`, `other_request`, `date_requested`, `status`, `remarks`, `date_received`, `received_by`, `date_released`, `released_by`, `date_processing`, `date_completed`, `date_rejected`, `date_canceled`, `cancellation_reason`, `created_at`, `updated_at`) VALUES
(199, 'HR-20260907080912', 'Certificate of Employment', NULL, 'Personal', 'Remart Maranan', '09266795288', 'rmaranan02.dolemimaropa@gmail.com', 'COS', NULL, NULL, NULL, NULL, '', '', NULL, '', '', '2026-09-07', 'New Application', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-07 08:09:12', '2026-09-07 08:09:12'),
(198, 'HR-20260905132621', 'Certificate of Employment', NULL, 'dfdfdfdfd', 'vccvvcx', '09266795288', 'remart@gmail.com', 'PERMANENT', NULL, NULL, NULL, NULL, '', '', NULL, '', '', '2026-09-05', 'New Application', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-05 13:26:21', '2026-09-05 13:26:21'),
(197, 'HR-20260905131900', 'Service Record', NULL, 'cxcvcvc', 'vbfvdcf', '09266795288', 'remartm2@gmail.com', 'PERMANENT', NULL, NULL, NULL, NULL, '', '', NULL, '', '', '2026-09-05', 'For Released', '', NULL, NULL, '2026-09-05', NULL, '2026-09-05 13:20:53', NULL, NULL, NULL, NULL, '2026-09-05 13:19:00', '2026-09-05 13:21:06'),
(196, 'HR-20260905130159', 'Certificate of Employment', NULL, 'Personal', 'Remart Maranan', '09266792888', 'rmaranan02.dolemimaropa@gmail.com', 'COS', NULL, NULL, NULL, NULL, '', '', NULL, '', '', '2026-09-05', 'For Released', 'Pa Fillup nalang po ng Feedback form Thank you\r\n', NULL, NULL, '2026-09-05', NULL, '2026-09-05 13:06:15', NULL, NULL, NULL, NULL, '2026-09-05 13:02:00', '2026-09-05 13:23:04'),
(195, 'HR-20260905124721', 'Service Record', NULL, 'sdsdsdsds', 'Nash Rienzo Maranan', '09266795288', 'rmaranan02.dolemimaropa@gmail.com', 'PERMANENT', NULL, NULL, NULL, NULL, '', '', NULL, '', '', '2026-09-05', 'For Released', '', NULL, NULL, '2026-09-05', NULL, '2026-09-05 13:10:09', NULL, NULL, NULL, NULL, '2026-09-05 12:47:21', '2026-09-05 13:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-08-14-055811', 'App\\Database\\Migrations\\AddContractOfServiceYearToHrRequests', 'default', 'App', 1786687117, 1);

-- --------------------------------------------------------

--
-- Table structure for table `request_employment_history`
--

DROP TABLE IF EXISTS `request_employment_history`;
CREATE TABLE IF NOT EXISTS `request_employment_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_of_service` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `office_division` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_employment_history`
--

INSERT INTO `request_employment_history` (`id`, `reference_no`, `period_of_service`, `position`, `office_division`, `created_at`) VALUES
(31, 'HR-20260907080912', '2025', 'NSRP', 'ORD', '2026-09-07 08:09:12'),
(30, 'HR-20260907080912', '2026', 'TC', 'ORD', '2026-09-07 08:09:12'),
(29, 'HR-20260905130159', '2024', 'NSRP', 'TSSD', '2026-09-05 13:02:00'),
(28, 'HR-20260905130159', '2025', 'TC', 'ORD', '2026-09-05 13:02:00'),
(27, 'HR-20260905130159', '2026', 'TC', 'ORD', '2026-09-05 13:02:00'),
(26, 'HR-20260905124259', '2025', 'tc', 'ORD', '2026-09-05 12:42:59'),
(25, 'HR-20260905124259', '2026', 'tc', 'ORD', '2026-09-05 12:42:59'),
(24, 'HR-20260905124251', '2025', 'tc', 'ORD', '2026-09-05 12:42:51'),
(23, 'HR-20260905124251', '2026', 'tc', 'ORD', '2026-09-05 12:42:51'),
(22, 'HR-20260905110630', 'sdfsdfs', 'tc', 'ORD', '2026-09-05 11:06:30'),
(21, 'HR-20260905110623', 'sdfsdfs', 'tc', 'ORD', '2026-09-05 11:06:23'),
(20, 'HR-20260905105818', '2025', 'TC', 'ORD', '2026-09-05 10:58:18'),
(19, 'HR-20260905105818', '2026', 'TC', 'ORD', '2026-09-05 10:58:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('IT ADMINISTRATOR','HR ADMINISTRATOR') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `role`, `status`) VALUES
(23, 'Bunney Gupit', 'Bgupit_DOLE', 'rmaranan02.dolemimaropa@gmail.com', '$2y$12$QrH7Mf0ETfhWk.nxX4uRuOYjDQ5dE44zFwTpXOUGCdR705U4kJwxu', 'HR ADMINISTRATOR', 'ACTIVE'),
(22, 'Remart Maranan', 'Mat', 'rmaranan02.dolemimaropa@gmail.com', '$2y$12$EhLMy8vTtlHiwnlqdlgWwOccMarecUY2mn94FLDV5.yjOwapTkAJ6', 'IT ADMINISTRATOR', 'ACTIVE');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
