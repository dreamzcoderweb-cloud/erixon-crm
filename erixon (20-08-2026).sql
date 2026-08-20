-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2026 at 01:38 PM
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
-- Database: `erixon`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `check_in` time NOT NULL,
  `check_out` time DEFAULT NULL,
  `working_hours` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Present',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `user_id`, `date`, `check_in`, `check_out`, `working_hours`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, '2026-08-18', '09:00:00', '17:30:00', '8 hrs 30 mins', 'Present', '2026-08-18 04:38:11', '2026-08-18 04:38:11', NULL),
(2, 3, '2026-08-19', '10:06:05', NULL, NULL, 'Present', '2026-08-18 23:06:05', '2026-08-19 04:37:49', NULL),
(3, 2, '2026-08-19', '11:38:43', NULL, NULL, 'Late', '2026-08-19 06:08:43', '2026-08-19 06:08:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:72:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"dashboard.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"profile.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:16:\"profile.password\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:10:\"roles.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"roles.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:10:\"roles.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"roles.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:10:\"staff.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:12:\"staff.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:10:\"staff.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"staff.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:14:\"customers.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:21:\"general-settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:21:\"general-settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:22:\"referral-settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:22:\"referral-settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:16:\"customers.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:14:\"customers.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:16:\"customers.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:17:\"lead-sources.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:19:\"lead-sources.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:17:\"lead-sources.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:19:\"lead-sources.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:10:\"leads.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:12:\"leads.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:10:\"leads.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:12:\"leads.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:16:\"lead-stages.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:18:\"lead-stages.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:16:\"lead-stages.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:18:\"lead-stages.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:22:\"lead-requirements.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:24:\"lead-requirements.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:22:\"lead-requirements.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:24:\"lead-requirements.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:17:\"lost-reasons.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:19:\"lost-reasons.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:17:\"lost-reasons.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:19:\"lost-reasons.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:14:\"followups.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:16:\"followups.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:14:\"followups.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:16:\"followups.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:18:\"followups.reassign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:11:\"staff.leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:19:\"lead-documents.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:21:\"lead-documents.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:19:\"lead-documents.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:21:\"lead-documents.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:14:\"templates.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:16:\"templates.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:14:\"templates.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:16:\"templates.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:20:\"call-recordings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:22:\"call-recordings.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:20:\"call-recordings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:22:\"call-recordings.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:15:\"attendance.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:17:\"attendance.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:15:\"attendance.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:17:\"attendance.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:23:\"attendance-reports.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:11:\"leaves.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:13:\"leaves.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:14:\"leaves.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:13:\"leaves.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:11:\"salary.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:14:\"call-logs.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:16:\"call-logs.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:14:\"call-logs.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:16:\"call-logs.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:21:\"call-log-reports.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:2:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"manager\";s:1:\"c\";s:3:\"web\";}}}', 1787310690);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `call_logs`
--

CREATE TABLE `call_logs` (
  `call_id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(30) NOT NULL,
  `call_type` varchar(50) NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `call_status` varchar(100) NOT NULL,
  `recording_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `call_logs`
--

INSERT INTO `call_logs` (`call_id`, `lead_id`, `user_id`, `phone`, `call_type`, `duration`, `call_status`, `recording_id`, `created_at`) VALUES
(1, 2, 2, '9489042085', 'Inbound', '5 min', 'Completed', 1, '2026-08-20 05:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `call_recordings`
--

CREATE TABLE `call_recordings` (
  `call_id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `recording_file` varchar(255) NOT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `call_recordings`
--

INSERT INTO `call_recordings` (`call_id`, `lead_id`, `recording_file`, `duration`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'uploads/call_recordings/1787046839_Jailer-2-Announcement-Bgm.mp3', '1 minute', 1, '2026-08-18 04:23:59', '2026-08-18 04:24:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `customer_type` enum('user','reseller') NOT NULL DEFAULT 'user',
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `alternate_mobile` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `password` varchar(255) DEFAULT NULL,
  `reference_code` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_type`, `name`, `company_name`, `mobile`, `email`, `alternate_mobile`, `address`, `city`, `state`, `country`, `pincode`, `created_by`, `status`, `password`, `reference_code`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'user', 'ajis', 'site studio', '9489042085', 'ajis@gmail.com', NULL, NULL, 'madurai', 'Taminadu', 'India', NULL, 1, 1, NULL, NULL, NULL, '2026-08-13 01:47:54', '2026-08-13 01:47:54', NULL),
(2, 'user', 'ajis', 'abc', '8521239632', NULL, NULL, NULL, NULL, NULL, 'India', NULL, 1, 1, NULL, NULL, NULL, '2026-08-18 05:21:07', '2026-08-18 05:21:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followups`
--

CREATE TABLE `followups` (
  `followups_id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `followup_type` varchar(255) NOT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `next_followup_date` datetime DEFAULT NULL,
  `followup_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `forward_to` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `followups`
--

INSERT INTO `followups` (`followups_id`, `lead_id`, `followup_type`, `duration`, `remarks`, `next_followup_date`, `followup_status`, `forward_to`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Call', '5 minutes', NULL, '2026-08-18 10:00:00', 'Pending', 2, 2, '2026-08-13 01:50:29', '2026-08-18 04:19:53', NULL),
(2, 1, 'Call', '10 minutes', 'Tomorrow followup meeting', '2026-08-19 11:00:00', 'Pending', 2, 2, '2026-08-18 05:33:34', '2026-08-18 05:33:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `followup_reassignments`
--

CREATE TABLE `followup_reassignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `followup_id` bigint(20) UNSIGNED NOT NULL,
  `previous_staff_id` bigint(20) UNSIGNED NOT NULL,
  `new_staff_id` bigint(20) UNSIGNED NOT NULL,
  `reassigned_by` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `followup_reassignments`
--

INSERT INTO `followup_reassignments` (`id`, `followup_id`, `previous_staff_id`, `new_staff_id`, `reassigned_by`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 3, 1, 'Reassigned due to staff leave', '2026-08-18 03:57:11', '2026-08-18 03:57:11'),
(2, 1, 3, 2, 1, 'Reassigned due to staff leave', '2026-08-18 04:19:53', '2026-08-18 04:19:53');

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE `general_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL DEFAULT 'PowerGYM',
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `whatsapp_no` varchar(255) DEFAULT NULL,
  `theme_color` varchar(255) NOT NULL DEFAULT '#00b2a9',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`id`, `company_name`, `logo`, `favicon`, `whatsapp_no`, `theme_color`, `created_at`, `updated_at`) VALUES
(1, 'Erixon CRM', 'uploads/settings/logo_1786084263.png', 'uploads/settings/favicon_1786599209.png', '8610747034', '#c66306', '2026-08-07 00:49:16', '2026-08-18 00:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `lead_title` varchar(255) NOT NULL,
  `lead_source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_requirement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `expected_amount` decimal(12,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `next_followup_date` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: New/Active, 0: Closed/Inactive',
  `lost_reason_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`lead_id`, `customer_id`, `lead_title`, `lead_source_id`, `lead_stage_id`, `lead_requirement_id`, `assigned_to`, `priority`, `expected_amount`, `description`, `next_followup_date`, `status`, `lost_reason_id`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'ecommerce website', 2, 1, 5, 2, 'medium', 100000.00, 'dynamic ecommerce website with payment gateway', '2026-08-18', 1, NULL, 1, '2026-08-13 01:49:42', '2026-08-18 05:25:32', NULL),
(2, 2, 'food app', 6, 1, 5, 2, 'medium', NULL, NULL, '2026-08-19', 1, NULL, 1, '2026-08-18 05:23:26', '2026-08-18 05:23:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_documents`
--

CREATE TABLE `lead_documents` (
  `lead_documents_id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_documents`
--

INSERT INTO `lead_documents` (`lead_documents_id`, `lead_id`, `document_type`, `file_name`, `file_path`, `uploaded_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'ID Proof', 'WhatsApp Image 2026-08-14 at 11.53.04 AM (2).jpeg', 'uploads/lead_documents/1787046386_WhatsApp_Image_2026-08-14_at_11.53.04_AM_(2).jpeg', 1, '2026-08-18 04:16:26', '2026-08-18 04:16:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_requirements`
--

CREATE TABLE `lead_requirements` (
  `lead_requirements_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_requirements`
--

INSERT INTO `lead_requirements` (`lead_requirements_id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SMS', 1, '2026-08-13 01:44:42', '2026-08-13 01:44:42', NULL),
(2, 'WhatsApp', 1, '2026-08-13 01:44:51', '2026-08-13 01:44:51', NULL),
(3, 'Voice Call', 1, '2026-08-13 01:45:00', '2026-08-13 01:45:00', NULL),
(4, 'Website', 1, '2026-08-13 01:45:08', '2026-08-13 01:45:08', NULL),
(5, 'Software', 1, '2026-08-13 01:45:16', '2026-08-13 01:45:16', NULL),
(6, 'Hardware', 1, '2026-08-13 01:45:24', '2026-08-17 05:26:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_sources`
--

CREATE TABLE `lead_sources` (
  `lead_sources_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_sources`
--

INSERT INTO `lead_sources` (`lead_sources_id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Referral', 1, '2026-08-13 01:41:29', '2026-08-13 01:41:29', NULL),
(2, 'Existing Client', 1, '2026-08-13 01:41:43', '2026-08-13 01:41:43', NULL),
(3, 'Campaign', 1, '2026-08-13 01:41:55', '2026-08-13 01:41:55', NULL),
(4, 'Website', 1, '2026-08-13 01:42:04', '2026-08-13 01:42:04', NULL),
(5, 'WhatsApp', 1, '2026-08-13 01:42:13', '2026-08-13 01:42:13', NULL),
(6, 'Facebook', 1, '2026-08-13 01:42:20', '2026-08-14 01:52:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_stages`
--

CREATE TABLE `lead_stages` (
  `lead_stage_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_stages`
--

INSERT INTO `lead_stages` (`lead_stage_id`, `name`, `sort_order`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'New', 1, 1, '2026-08-13 01:43:31', '2026-08-13 01:43:31', NULL),
(2, 'Follow-up', 2, 1, '2026-08-13 01:43:46', '2026-08-13 01:43:46', NULL),
(3, 'Shared Proposal', 3, 1, '2026-08-13 01:43:58', '2026-08-13 01:43:58', NULL),
(4, 'Negotiation', 0, 1, '2026-08-13 01:44:06', '2026-08-13 01:44:06', NULL),
(5, 'Won', 4, 1, '2026-08-13 01:44:19', '2026-08-13 01:44:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `number_of_days` decimal(5,2) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `user_id`, `from_date`, `to_date`, `number_of_days`, `leave_type`, `reason`, `status`, `approved_by`, `admin_remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, '2026-08-20', '2026-08-22', 3.00, 'Casual Leave', 'family function', 'Approved', 1, NULL, '2026-08-19 04:38:49', '2026-08-19 04:40:07', NULL),
(2, 2, '2026-08-20', '2026-08-20', 1.00, 'Casual Leave', 'personal reason', 'Approved', 1, NULL, '2026-08-19 06:07:31', '2026-08-19 06:07:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lost_reasons`
--

CREATE TABLE `lost_reasons` (
  `lost_reason_id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lost_reasons`
--

INSERT INTO `lost_reasons` (`lost_reason_id`, `reason`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Not Answering', 1, '2026-08-13 01:45:45', '2026-08-13 01:45:45', NULL),
(2, 'Not Interested', 1, '2026-08-13 01:45:54', '2026-08-13 01:45:54', NULL),
(3, 'Price High', 1, '2026-08-13 01:46:04', '2026-08-13 01:46:04', NULL),
(4, 'Purchased Elsewhere', 1, '2026-08-13 01:46:12', '2026-08-13 01:46:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_06_050937_create_personal_access_tokens_table', 1),
(5, '2026_05_06_051430_create_customers_table', 1),
(6, '2026_05_12_102631_create_permission_tables', 1),
(7, '2026_08_07_000001_create_general_settings_table', 1),
(8, '2026_08_07_000002_create_referral_settings_table', 1),
(9, '2026_08_13_000001_update_customers_table_schema', 2),
(10, '2026_08_13_000002_create_lead_sources_table', 2),
(11, '2026_08_13_000003_create_leads_table', 2),
(12, '2026_08_13_000004_add_favicon_to_general_settings_table', 3),
(13, '2026_08_13_000005_add_profile_image_to_users_table', 3),
(14, '2026_08_13_000006_add_soft_deletes_to_tables', 4),
(15, '2026_08_13_000007_create_lead_stages_table', 5),
(16, '2026_08_13_000008_create_lead_requirements_table', 5),
(17, '2026_08_13_000009_create_lost_reasons_table', 5),
(18, '2026_08_13_000010_create_followups_table', 5),
(19, '2026_08_13_000011_add_lead_foreign_key_constraints', 5),
(20, '2026_08_18_000001_add_duration_to_followups_table', 6),
(21, '2026_08_18_000002_add_is_on_leave_to_users_table', 6),
(22, '2026_08_18_000003_create_followup_reassignments_table', 6),
(23, '2026_08_18_000004_create_lead_documents_table', 7),
(24, '2026_08_18_000005_create_templates_table', 7),
(25, '2026_08_18_000006_create_call_recordings_table', 7),
(26, '2026_08_18_000007_create_attendance_table', 7),
(27, '2026_08_19_000001_add_staff_fields_to_users_table', 8),
(28, '2026_08_19_000002_create_leave_requests_table', 8),
(29, '2026_08_20_000001_create_call_logs_table', 9),
(30, '2026_08_20_000002_remove_deleted_at_from_call_logs_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard.view', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(2, 'profile.view', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(3, 'profile.password', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(4, 'roles.view', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(5, 'roles.create', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(6, 'roles.edit', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(7, 'roles.delete', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(8, 'staff.view', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(9, 'staff.create', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(10, 'staff.edit', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(11, 'staff.delete', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(12, 'customers.view', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(13, 'general-settings.view', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(14, 'general-settings.edit', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(15, 'referral-settings.view', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(16, 'referral-settings.edit', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(17, 'customers.create', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(18, 'customers.edit', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(19, 'customers.delete', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(20, 'lead-sources.view', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(21, 'lead-sources.create', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(22, 'lead-sources.edit', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(23, 'lead-sources.delete', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(24, 'leads.view', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(25, 'leads.create', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(26, 'leads.edit', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(27, 'leads.delete', 'web', '2026-08-12 23:21:46', '2026-08-12 23:21:46'),
(28, 'lead-stages.view', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(29, 'lead-stages.create', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(30, 'lead-stages.edit', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(31, 'lead-stages.delete', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(32, 'lead-requirements.view', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(33, 'lead-requirements.create', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(34, 'lead-requirements.edit', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(35, 'lead-requirements.delete', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(36, 'lost-reasons.view', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(37, 'lost-reasons.create', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(38, 'lost-reasons.edit', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(39, 'lost-reasons.delete', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(40, 'followups.view', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(41, 'followups.create', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(42, 'followups.edit', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(43, 'followups.delete', 'web', '2026-08-13 01:38:45', '2026-08-13 01:38:45'),
(44, 'followups.reassign', 'web', '2026-08-18 03:41:45', '2026-08-18 03:41:45'),
(45, 'staff.leave', 'web', '2026-08-18 03:41:45', '2026-08-18 03:41:45'),
(46, 'lead-documents.view', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(47, 'lead-documents.create', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(48, 'lead-documents.edit', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(49, 'lead-documents.delete', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(50, 'templates.view', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(51, 'templates.create', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(52, 'templates.edit', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(53, 'templates.delete', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(54, 'call-recordings.view', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(55, 'call-recordings.create', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(56, 'call-recordings.edit', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(57, 'call-recordings.delete', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(58, 'attendance.view', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(59, 'attendance.create', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(60, 'attendance.edit', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(61, 'attendance.delete', 'web', '2026-08-18 04:09:51', '2026-08-18 04:09:51'),
(62, 'attendance-reports.view', 'web', '2026-08-18 04:36:18', '2026-08-18 04:36:18'),
(63, 'leaves.view', 'web', '2026-08-18 22:56:02', '2026-08-18 22:56:02'),
(64, 'leaves.create', 'web', '2026-08-18 22:56:02', '2026-08-18 22:56:02'),
(65, 'leaves.approve', 'web', '2026-08-18 22:56:02', '2026-08-18 22:56:02'),
(66, 'leaves.delete', 'web', '2026-08-18 22:56:02', '2026-08-18 22:56:02'),
(67, 'salary.view', 'web', '2026-08-18 22:56:02', '2026-08-18 22:56:02'),
(68, 'call-logs.view', 'web', '2026-08-20 11:10:18', '2026-08-20 11:10:18'),
(69, 'call-logs.create', 'web', '2026-08-20 11:10:18', '2026-08-20 11:10:18'),
(70, 'call-logs.edit', 'web', '2026-08-20 11:10:19', '2026-08-20 11:10:19'),
(71, 'call-logs.delete', 'web', '2026-08-20 11:10:19', '2026-08-20 11:10:19'),
(72, 'call-log-reports.view', 'web', '2026-08-20 11:10:19', '2026-08-20 11:10:19');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_settings`
--

CREATE TABLE `referral_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `referral_points` int(11) NOT NULL DEFAULT 100,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referral_settings`
--

INSERT INTO `referral_settings` (`id`, `referral_points`, `created_at`, `updated_at`) VALUES
(1, 100, '2026-08-07 00:49:16', '2026-08-07 00:49:16');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Admin', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17', NULL),
(2, 'manager', 'web', '2026-08-07 02:06:08', '2026-08-07 02:06:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(20, 2),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(23, 1),
(23, 2),
(24, 1),
(24, 2),
(25, 1),
(25, 2),
(26, 1),
(26, 2),
(27, 1),
(27, 2),
(28, 1),
(28, 2),
(29, 1),
(29, 2),
(30, 1),
(30, 2),
(31, 1),
(31, 2),
(32, 1),
(32, 2),
(33, 1),
(33, 2),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(36, 1),
(36, 2),
(37, 1),
(37, 2),
(38, 1),
(38, 2),
(39, 1),
(39, 2),
(40, 1),
(40, 2),
(41, 1),
(41, 2),
(42, 1),
(42, 2),
(43, 1),
(43, 2),
(44, 1),
(44, 2),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(58, 2),
(59, 1),
(59, 2),
(60, 1),
(60, 2),
(61, 1),
(61, 2),
(62, 1),
(63, 1),
(63, 2),
(64, 1),
(64, 2),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('8qDW5DsgpERCLfAHyQKixZFTqiqgu7VXnPk5GvUI', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMGx5ZjNTVVhnd3l1SjIyT2Z0R0RnOTFNOWlmakRLcmc4NXp3amJOQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR0d2o4SVBLcFNRTE9WWjE2L2pGaW9lSzNPdmZnYjVKWUw3cUFReC96SG9OWG5zczIvYWdybSI7fQ==', 1787130049),
('kF2sDLSWzoKhqcnk0oohKfYvZYZTCL3VDY9LEh15', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidzRJNVlaalNUbkpMSmtRd1RVS3hpT0RPVzZ4R0VDc1BaOHk3dDZIaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1787125341),
('qTtrelweruUu7WUWzavwmB7JVb7mb1aV5yIlrOos', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicTU1QWxFUVo4WnV2NDF4aXY2SlJHM0dySDdRME9EeHhQRElEQ2FwRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9jYWxsLWxvZ3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkdHdqOElQS3BTUUxPVloxNi9qRmlvZUszT3ZmZ2I1SllMN3FBUXgvekhvTlhuc3MyL2Fncm0iO30=', 1787225782);

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `template_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `mobile_number` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `base_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `available_leave_count` decimal(5,2) NOT NULL DEFAULT 0.00,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `is_on_leave` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `profile_image`, `mobile_number`, `address`, `gender`, `date_of_birth`, `date_of_joining`, `designation`, `base_salary`, `available_leave_count`, `check_in_time`, `check_out_time`, `is_on_leave`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'super admin', 'admin@gmail.com', 'uploads/profile/profile_1_1786965689_9c2cd997.png', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, 0, NULL, '$2y$12$twj8IPKpSQLOVZ16/jFioeK3Ovfgb5JYL7qAQx/zHoNXnss2/agrm', NULL, NULL, '2026-08-17 05:51:29', NULL),
(2, 'Tharik', 'tharik@gmail.com', NULL, '9489042085', 'madurai,tamil nadu', NULL, '1993-05-09', '2026-01-19', 'manager', 32000.00, 1.00, '09:00:00', '18:30:00', 0, NULL, '$2y$12$eUdwxokNoIPWttFsek7eO.ZhMSHz5LjJ9POfUbnWviBEp5SaS5OzK', NULL, '2026-08-07 02:06:43', '2026-08-19 06:08:34', NULL),
(3, 'riyaz', 'riyaz@gmail.com', NULL, '9485042596', 'madurai,tamil nadu', NULL, '1996-06-03', '2026-03-02', 'sale executive', 20000.00, 1.00, '09:00:00', '18:00:00', 0, NULL, '$2y$12$wAOG0XmS9NXsMTXhNuz.EuraSyKW60ha6/y6flLrvx.3gOoHy6hLq', NULL, '2026-08-18 03:55:32', '2026-08-19 05:27:55', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `attendance_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `call_logs`
--
ALTER TABLE `call_logs`
  ADD PRIMARY KEY (`call_id`),
  ADD KEY `call_logs_lead_id_foreign` (`lead_id`),
  ADD KEY `call_logs_user_id_foreign` (`user_id`),
  ADD KEY `call_logs_recording_id_foreign` (`recording_id`);

--
-- Indexes for table `call_recordings`
--
ALTER TABLE `call_recordings`
  ADD PRIMARY KEY (`call_id`),
  ADD KEY `call_recordings_lead_id_foreign` (`lead_id`),
  ADD KEY `call_recordings_created_by_foreign` (`created_by`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customers_mobile_unique` (`mobile`),
  ADD KEY `customers_created_by_foreign` (`created_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `followups`
--
ALTER TABLE `followups`
  ADD PRIMARY KEY (`followups_id`),
  ADD KEY `followups_lead_id_foreign` (`lead_id`),
  ADD KEY `followups_forward_to_foreign` (`forward_to`),
  ADD KEY `followups_created_by_foreign` (`created_by`);

--
-- Indexes for table `followup_reassignments`
--
ALTER TABLE `followup_reassignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `followup_reassignments_followup_id_foreign` (`followup_id`),
  ADD KEY `followup_reassignments_previous_staff_id_foreign` (`previous_staff_id`),
  ADD KEY `followup_reassignments_new_staff_id_foreign` (`new_staff_id`),
  ADD KEY `followup_reassignments_reassigned_by_foreign` (`reassigned_by`);

--
-- Indexes for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`lead_id`),
  ADD KEY `leads_customer_id_foreign` (`customer_id`),
  ADD KEY `leads_lead_source_id_foreign` (`lead_source_id`),
  ADD KEY `leads_assigned_to_foreign` (`assigned_to`),
  ADD KEY `leads_created_by_foreign` (`created_by`),
  ADD KEY `leads_lead_stage_id_foreign` (`lead_stage_id`),
  ADD KEY `leads_lead_requirement_id_foreign` (`lead_requirement_id`),
  ADD KEY `leads_lost_reason_id_foreign` (`lost_reason_id`);

--
-- Indexes for table `lead_documents`
--
ALTER TABLE `lead_documents`
  ADD PRIMARY KEY (`lead_documents_id`),
  ADD KEY `lead_documents_lead_id_foreign` (`lead_id`),
  ADD KEY `lead_documents_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `lead_requirements`
--
ALTER TABLE `lead_requirements`
  ADD PRIMARY KEY (`lead_requirements_id`);

--
-- Indexes for table `lead_sources`
--
ALTER TABLE `lead_sources`
  ADD PRIMARY KEY (`lead_sources_id`);

--
-- Indexes for table `lead_stages`
--
ALTER TABLE `lead_stages`
  ADD PRIMARY KEY (`lead_stage_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_requests_user_id_foreign` (`user_id`),
  ADD KEY `leave_requests_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `lost_reasons`
--
ALTER TABLE `lost_reasons`
  ADD PRIMARY KEY (`lost_reason_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `referral_settings`
--
ALTER TABLE `referral_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `call_logs`
--
ALTER TABLE `call_logs`
  MODIFY `call_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `call_recordings`
--
ALTER TABLE `call_recordings`
  MODIFY `call_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followups`
--
ALTER TABLE `followups`
  MODIFY `followups_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `followup_reassignments`
--
ALTER TABLE `followup_reassignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `lead_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lead_documents`
--
ALTER TABLE `lead_documents`
  MODIFY `lead_documents_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lead_requirements`
--
ALTER TABLE `lead_requirements`
  MODIFY `lead_requirements_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `lead_sources_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lead_stages`
--
ALTER TABLE `lead_stages`
  MODIFY `lead_stage_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lost_reasons`
--
ALTER TABLE `lost_reasons`
  MODIFY `lost_reason_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `referral_settings`
--
ALTER TABLE `referral_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `template_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `call_logs`
--
ALTER TABLE `call_logs`
  ADD CONSTRAINT `call_logs_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`lead_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `call_logs_recording_id_foreign` FOREIGN KEY (`recording_id`) REFERENCES `call_recordings` (`call_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `call_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `call_recordings`
--
ALTER TABLE `call_recordings`
  ADD CONSTRAINT `call_recordings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `call_recordings_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`lead_id`) ON DELETE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `followups`
--
ALTER TABLE `followups`
  ADD CONSTRAINT `followups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `followups_forward_to_foreign` FOREIGN KEY (`forward_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `followups_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`lead_id`) ON DELETE CASCADE;

--
-- Constraints for table `followup_reassignments`
--
ALTER TABLE `followup_reassignments`
  ADD CONSTRAINT `followup_reassignments_followup_id_foreign` FOREIGN KEY (`followup_id`) REFERENCES `followups` (`followups_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followup_reassignments_new_staff_id_foreign` FOREIGN KEY (`new_staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followup_reassignments_previous_staff_id_foreign` FOREIGN KEY (`previous_staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followup_reassignments_reassigned_by_foreign` FOREIGN KEY (`reassigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leads_lead_requirement_id_foreign` FOREIGN KEY (`lead_requirement_id`) REFERENCES `lead_requirements` (`lead_requirements_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_lead_source_id_foreign` FOREIGN KEY (`lead_source_id`) REFERENCES `lead_sources` (`lead_sources_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_lead_stage_id_foreign` FOREIGN KEY (`lead_stage_id`) REFERENCES `lead_stages` (`lead_stage_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_lost_reason_id_foreign` FOREIGN KEY (`lost_reason_id`) REFERENCES `lost_reasons` (`lost_reason_id`) ON DELETE SET NULL;

--
-- Constraints for table `lead_documents`
--
ALTER TABLE `lead_documents`
  ADD CONSTRAINT `lead_documents_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`lead_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
