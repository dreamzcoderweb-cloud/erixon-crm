-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2026 at 06:48 AM
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
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `second_check_in_latitude` decimal(10,8) DEFAULT NULL,
  `second_check_in_longitude` decimal(11,8) DEFAULT NULL,
  `permission_start` time DEFAULT NULL,
  `permission_end` time DEFAULT NULL,
  `second_check_in` time DEFAULT NULL,
  `second_check_out` time DEFAULT NULL,
  `sessions` longtext DEFAULT NULL,
  `permission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `user_id`, `date`, `check_in`, `check_out`, `working_hours`, `status`, `latitude`, `longitude`, `second_check_in_latitude`, `second_check_in_longitude`, `permission_start`, `permission_end`, `second_check_in`, `second_check_out`, `sessions`, `permission_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, '2026-08-18', '09:00:00', '17:30:00', '8 hrs 30 mins', 'Present', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-18 04:38:11', '2026-08-18 04:38:11', NULL),
(2, 3, '2026-08-19', '10:06:05', NULL, NULL, 'Present', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-18 23:06:05', '2026-08-19 04:37:49', NULL),
(3, 2, '2026-08-19', '11:38:43', NULL, NULL, 'Late', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-19 06:08:43', '2026-08-19 06:08:43', NULL),
(4, 4, '2026-08-21', '09:00:00', '18:00:00', '9 hrs', 'Present', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-21 06:14:58', '2026-08-21 06:14:58', NULL),
(5, 6, '2026-08-21', '09:00:00', '06:26:00', '21 hrs 26 mins', 'Present', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-21 06:26:08', '2026-08-21 06:33:00', NULL),
(6, 2, '2026-08-25', '09:48:04', NULL, NULL, 'Late', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-25 05:48:04', '2026-08-25 09:33:06', NULL),
(7, 3, '2026-08-25', '09:33:39', NULL, '0 hrs', 'Late', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-25 09:33:39', '2026-08-25 09:34:04', NULL),
(8, 2, '2026-08-24', '09:15:00', NULL, NULL, 'Late', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-24 04:06:28', '2026-09-24 04:06:28', NULL),
(9, 2, '2026-08-26', '09:40:10', '12:14:27', '2 hrs 34 mins', 'Late', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-24 04:06:28', '2026-08-26 12:14:27', NULL),
(13, 3, '2026-08-27', '09:52:06', NULL, NULL, 'Late', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-27 09:52:06', '2026-08-27 09:52:06', NULL),
(14, 2, '2026-08-28', '11:12:21', '11:12:32', '0 hrs', 'Late', NULL, NULL, NULL, NULL, NULL, NULL, '11:12:43', '11:12:46', NULL, NULL, '2026-08-28 11:12:21', '2026-08-28 11:12:46', NULL),
(15, 1, '2026-08-31', '11:22:55', NULL, NULL, 'Present', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-31 11:22:55', '2026-08-31 11:22:55', NULL),
(17, 2, '2026-08-31', '15:09:47', NULL, NULL, 'Late', 10.03290000, 78.15000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-31 15:09:47', '2026-08-31 15:09:47', NULL),
(18, 1, '2026-09-01', '13:26:34', NULL, NULL, 'Present', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-01 13:26:34', '2026-09-01 13:26:34', NULL),
(19, 1, '2026-09-05', '11:38:09', NULL, NULL, 'Present', 8.87775728, 78.13276328, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-05 11:38:09', '2026-09-05 11:38:09', NULL),
(20, 2, '2026-09-10', '12:26:49', '12:27:10', '0 hrs', 'Late', 10.03290000, 78.15000000, 10.03290000, 78.15000000, NULL, NULL, '12:27:00', '12:27:02', '[{\"session\":1,\"check_in\":\"12:26:49\",\"check_out\":\"12:26:56\",\"latitude\":\"10.0329\",\"longitude\":\"78.15\"},{\"session\":2,\"check_in\":\"12:27:00\",\"check_out\":\"12:27:02\",\"latitude\":\"10.0329\",\"longitude\":\"78.15\"},{\"session\":3,\"check_in\":\"12:27:05\",\"check_out\":\"12:27:10\",\"latitude\":\"10.0329\",\"longitude\":\"78.15\"}]', NULL, '2026-09-10 12:26:49', '2026-09-10 12:27:10', NULL),
(21, 1, '2026-09-10', '12:39:24', '12:39:32', '0 hrs', 'Present', 10.98940000, 76.95980000, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"session\":1,\"check_in\":\"12:39:24\",\"check_out\":\"12:39:32\",\"latitude\":\"10.9894\",\"longitude\":\"76.9598\"}]', NULL, '2026-09-10 12:39:24', '2026-09-10 12:39:32', NULL),
(22, 2, '2026-09-16', '10:28:54', '17:09:57', '3 hrs 11 mins', 'Present', 13.08270000, 80.27070000, 13.08270000, 80.27070000, NULL, NULL, '10:49:05', '10:50:07', '[{\"session\":1,\"check_in\":\"10:28:54\",\"check_out\":\"10:30:44\",\"latitude\":13.0827,\"longitude\":80.2707},{\"session\":2,\"check_in\":\"10:49:05\",\"check_out\":\"10:50:07\",\"latitude\":13.0827,\"longitude\":80.2707},{\"session\":3,\"check_in\":\"11:00:03\",\"check_out\":\"11:09:29\",\"latitude\":13.0827,\"longitude\":80.2707},{\"session\":4,\"check_in\":\"11:12:18\",\"check_out\":\"13:10:05\",\"latitude\":10.3687396,\"longitude\":77.9820143},{\"session\":5,\"check_in\":\"16:08:17\",\"check_out\":\"17:09:57\",\"latitude\":10.3687379,\"longitude\":77.9820154}]', NULL, '2026-09-16 10:28:54', '2026-09-16 17:09:57', NULL),
(23, 1, '2026-09-16', '11:09:48', NULL, NULL, 'Present', 8.87776000, 78.13276900, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"session\":1,\"check_in\":\"11:09:48\",\"check_out\":null,\"latitude\":\"8.87776\",\"longitude\":\"78.132769\"}]', NULL, '2026-09-16 11:09:48', '2026-09-16 11:09:48', NULL),
(24, 12, '2026-09-16', '12:53:26', NULL, NULL, 'Late', 8.69555100, 77.73335800, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"session\":1,\"check_in\":\"12:53:26\",\"check_out\":null,\"latitude\":\"8.695551\",\"longitude\":\"77.733358\"}]', NULL, '2026-09-16 12:53:26', '2026-09-16 12:53:26', NULL),
(25, 2, '2026-09-17', '09:00:35', '12:22:05', '1 hrs 45 mins', 'Late', 10.36873760, 77.98202010, 10.36873780, 77.98201420, NULL, NULL, '15:02:03', NULL, '[{\"session\":1,\"check_in\":\"10:36:35\",\"check_out\":\"12:22:05\",\"latitude\":10.3687376,\"longitude\":77.9820201},{\"session\":2,\"check_in\":\"15:02:03\",\"check_out\":null,\"latitude\":10.3687378,\"longitude\":77.9820142}]', NULL, '2026-09-17 10:36:35', '2026-09-17 15:02:03', NULL),
(26, 1, '2026-09-18', '09:48:38', NULL, NULL, 'Present', 8.87776000, 78.13277000, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"session\":1,\"check_in\":\"09:48:38\",\"check_out\":null,\"latitude\":\"8.87776\",\"longitude\":\"78.13277\"}]', NULL, '2026-09-18 09:48:38', '2026-09-18 09:48:38', NULL),
(27, 2, '2026-09-21', '11:47:15', '11:47:49', '0 hrs', 'Late', 8.87592240, 78.12831180, 8.87592400, 78.12831460, NULL, NULL, '11:48:00', '11:48:16', '[{\"session\":1,\"check_in\":\"11:47:15\",\"check_out\":\"11:47:49\",\"latitude\":8.8759224,\"longitude\":78.1283118},{\"session\":2,\"check_in\":\"11:48:00\",\"check_out\":\"11:48:16\",\"latitude\":8.875924,\"longitude\":78.1283146},{\"session\":3,\"check_in\":\"11:48:21\",\"check_out\":null,\"latitude\":8.8759249,\"longitude\":78.1283092}]', NULL, '2026-09-21 11:47:15', '2026-09-21 11:48:21', NULL),
(28, 2, '2026-09-22', '12:19:02', NULL, NULL, 'Late', 8.47667350, 76.94823060, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"session\":1,\"check_in\":\"12:19:02\",\"check_out\":null,\"latitude\":8.4766735,\"longitude\":76.9482306}]', NULL, '2026-09-22 12:19:02', '2026-09-22 12:19:02', NULL),
(29, 6, '2026-09-22', '14:46:29', NULL, NULL, 'Late', 8.69566360, 77.73328070, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"session\":1,\"check_in\":\"14:46:29\",\"check_out\":null,\"latitude\":8.6956636,\"longitude\":77.7332807}]', NULL, '2026-09-22 14:46:29', '2026-09-22 14:46:29', NULL),
(30, 1, '2026-09-23', '10:45:14', '17:39:42', '6 hrs 54 mins', 'Present', 8.87776000, 78.13277000, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"session\":1,\"check_in\":\"10:45:14\",\"check_out\":\"17:39:42\",\"latitude\":\"8.87776\",\"longitude\":\"78.13277\"}]', NULL, '2026-09-23 10:45:14', '2026-09-23 17:39:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `auditable_type` varchar(255) DEFAULT NULL,
  `auditable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `module` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `event`, `auditable_type`, `auditable_id`, `module`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `created_at`, `updated_at`) VALUES
(1, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-17 11:41:20', '2026-09-17 11:41:20'),
(2, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-17 11:41:33', '2026-09-17 11:41:33'),
(3, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-17 11:41:36', '2026-09-17 11:41:36'),
(4, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-17 11:42:54', '2026-09-17 11:42:54'),
(5, 2, 'updated', 'App\\Models\\Attendance', 25, 'Attendances', 'Updated Attendance \'#25\' (check_out, working_hours, sessions)', '{\"check_out\":null,\"working_hours\":null,\"sessions\":[{\"session\":1,\"check_in\":\"10:36:35\",\"check_out\":null,\"latitude\":10.3687376,\"longitude\":77.9820201}]}', '{\"check_out\":\"12:22:05\",\"working_hours\":\"1 hrs 45 mins\",\"sessions\":\"[{\\\"session\\\":1,\\\"check_in\\\":\\\"10:36:35\\\",\\\"check_out\\\":\\\"12:22:05\\\",\\\"latitude\\\":10.3687376,\\\"longitude\\\":77.9820201}]\"}', '223.185.25.62', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/attendance/check-out', '2026-09-17 12:22:05', '2026-09-17 12:22:05'),
(6, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-17 12:27:44', '2026-09-17 12:27:44'),
(7, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-17 12:30:25', '2026-09-17 12:30:25'),
(8, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-17 13:07:16', '2026-09-17 13:07:16'),
(9, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-17 13:07:55', '2026-09-17 13:07:55'),
(10, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-17 13:11:13', '2026-09-17 13:11:13'),
(11, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-17 13:13:48', '2026-09-17 13:13:48'),
(12, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-17 13:27:51', '2026-09-17 13:27:51'),
(13, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-17 13:27:55', '2026-09-17 13:27:55'),
(14, 2, 'login', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-17 13:27:59', '2026-09-17 13:27:59'),
(15, 2, 'logout', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-17 13:35:22', '2026-09-17 13:35:22'),
(16, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-17 14:17:49', '2026-09-17 14:17:49'),
(17, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-17 14:18:33', '2026-09-17 14:18:33'),
(18, 2, 'updated', 'App\\Models\\Attendance', 25, 'Attendances', 'Updated Attendance \'#25\' (second_check_in_latitude, second_check_in_longitude, second_check_in, sessions)', '{\"second_check_in_latitude\":null,\"second_check_in_longitude\":null,\"second_check_in\":null,\"sessions\":[{\"session\":1,\"check_in\":\"10:36:35\",\"check_out\":\"12:22:05\",\"latitude\":10.3687376,\"longitude\":77.9820201}]}', '{\"second_check_in_latitude\":10.3687378,\"second_check_in_longitude\":77.9820142,\"second_check_in\":\"15:02:03\",\"sessions\":\"[{\\\"session\\\":1,\\\"check_in\\\":\\\"10:36:35\\\",\\\"check_out\\\":\\\"12:22:05\\\",\\\"latitude\\\":10.3687376,\\\"longitude\\\":77.9820201},{\\\"session\\\":2,\\\"check_in\\\":\\\"15:02:03\\\",\\\"check_out\\\":null,\\\"latitude\\\":10.3687378,\\\"longitude\\\":77.9820142}]\"}', '223.185.25.62', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/attendance/check-in', '2026-09-17 15:02:03', '2026-09-17 15:02:03'),
(19, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-17 15:04:01', '2026-09-17 15:04:01'),
(20, 2, 'created', 'App\\Models\\Followup', 1, 'Followups', 'Created Followup \'#1\'', NULL, '{\"lead_id\":10,\"followup_type\":\"Call\",\"duration\":\"00:01\",\"remarks\":null,\"next_followup_date\":\"2026-09-18 10:30:00\",\"followup_status\":\"Pending\",\"forward_to\":3,\"created_by\":2,\"created_at\":\"2026-09-17T09:41:29.000000Z\",\"followups_id\":1}', '223.185.25.62', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/call-recordings', '2026-09-17 15:11:29', '2026-09-17 15:11:29'),
(21, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-17 15:40:20', '2026-09-17 15:40:20'),
(22, 2, 'updated', 'App\\Models\\Followup', 1, 'Followups', 'Updated Followup \'#1\' (duration, custom_fields, followup_status)', '{\"duration\":\"00:01\",\"custom_fields\":null,\"followup_status\":\"Pending\"}', '{\"duration\":\"5 minutes\",\"custom_fields\":\"{\\\"whatsapp\\\":\\\"6622\\\"}\",\"followup_status\":\"Completed\"}', '223.185.25.62', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/followups/update/1', '2026-09-17 17:08:46', '2026-09-17 17:08:46'),
(23, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '152.57.87.157', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-18 09:48:33', '2026-09-18 09:48:33'),
(24, 1, 'created', 'App\\Models\\Attendance', 26, 'Attendances', 'Created Attendance \'#26\'', NULL, '{\"user_id\":1,\"date\":\"2026-09-18\",\"check_in\":\"09:48:38\",\"check_out\":null,\"working_hours\":null,\"status\":\"Present\",\"latitude\":\"8.87776\",\"longitude\":\"78.13277\",\"sessions\":[{\"session\":1,\"check_in\":\"09:48:38\",\"check_out\":null,\"latitude\":\"8.87776\",\"longitude\":\"78.13277\"}],\"created_at\":\"2026-09-18T04:18:38.000000Z\",\"attendance_id\":26,\"sessions_list\":[{\"session\":1,\"check_in\":\"09:48:38\",\"check_out\":null,\"latitude\":\"8.87776\",\"longitude\":\"78.13277\"}],\"is_currently_checked_in\":true,\"current_session_number\":1,\"active_check_in_time\":\"09:48:38\"}', '152.57.87.157', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/attendance/mark-self', '2026-09-18 09:48:38', '2026-09-18 09:48:38'),
(25, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 10:25:22', '2026-09-18 10:25:22'),
(26, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 10:26:15', '2026-09-18 10:26:15'),
(27, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '223.185.25.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 10:32:52', '2026-09-18 10:32:52'),
(28, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 11:59:13', '2026-09-18 11:59:13'),
(29, 1, 'created', 'App\\Models\\CreditRequest', 7, 'Credit Requests', 'Created CreditRequest \'#7\'', NULL, '{\"customer_id\":\"791\",\"lead_source_id\":\"2\",\"lead_requirement_id\":\"4\",\"credit_amount\":\"20000.00\",\"is_estimate\":false,\"username\":\"10003\",\"phone\":\"2707\",\"email\":\"anand@sparkalerts.in\",\"status\":\"Pending Admin Approval\",\"requested_by\":1,\"custom_fields\":{\"username\":\"test\"},\"created_at\":\"2026-09-18T06:29:48.000000Z\",\"credit_request_id\":7}', '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/credit-requests/store', '2026-09-18 11:59:48', '2026-09-18 11:59:48'),
(30, 1, 'updated', 'App\\Models\\CreditRequest', 7, 'Credit Requests', 'Updated CreditRequest \'#7\' (status, admin_approved_by, admin_approved_at, admin_remarks)', '{\"status\":\"Pending Admin Approval\",\"admin_approved_by\":null,\"admin_approved_at\":null,\"admin_remarks\":null}', '{\"status\":\"Forwarded to Product Manager\",\"admin_approved_by\":1,\"admin_approved_at\":\"2026-09-18 12:00:14\",\"admin_remarks\":\"testing\"}', '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/credit-requests/approve-admin/7', '2026-09-18 12:00:14', '2026-09-18 12:00:14'),
(31, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 12:00:54', '2026-09-18 12:00:54'),
(32, NULL, 'updated', 'App\\Models\\User', 2, 'Staff', 'Updated User \'Tharik\' (fcmtoken)', '{\"fcmtoken\":null}', '{\"fcmtoken\":\"c2nWXOcCRBS0n3E09ECZHm:APA91bFgRTCwMLNldQJvcGgPRtXgIkSF-1dJTcGJpgfZj6gh7Y58qgAw-ZC5ph-TIUUfmMVoJXlkpVt6Z4GRZY_xbtoe-9tfQ3nhvsfK7qeafg182g4F1nY\"}', '106.51.24.147', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/staff/login', '2026-09-18 12:30:14', '2026-09-18 12:30:14'),
(33, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 12:30:53', '2026-09-18 12:30:53'),
(34, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 12:31:13', '2026-09-18 12:31:13'),
(35, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 12:40:44', '2026-09-18 12:40:44'),
(36, 1, 'created', 'App\\Models\\Coordination', 4, 'Coordinations', 'Created Coordination \'demo Notification\'', NULL, '{\"title\":\"demo Notification\",\"staff_id\":\"1\",\"link\":\"https:\\/\\/meet.google.com\\/ksj-tvbv-pyq\",\"created_by\":1,\"created_at\":\"2026-09-18T07:11:30.000000Z\",\"coordination_id\":4}', '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/coordinations/store', '2026-09-18 12:41:30', '2026-09-18 12:41:30'),
(37, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 12:41:50', '2026-09-18 12:41:50'),
(38, 2, 'login', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 12:41:54', '2026-09-18 12:41:54'),
(39, 2, 'logout', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 12:42:00', '2026-09-18 12:42:00'),
(40, 3, 'login', 'App\\Models\\User', 3, 'Authentication', 'User \'riyaz\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 12:42:12', '2026-09-18 12:42:12'),
(41, 3, 'logout', 'App\\Models\\User', 3, 'Authentication', 'User \'riyaz\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 12:42:23', '2026-09-18 12:42:23'),
(42, 2, 'login', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 12:45:16', '2026-09-18 12:45:16'),
(43, 2, 'logout', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 12:48:30', '2026-09-18 12:48:30'),
(44, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 12:57:31', '2026-09-18 12:57:31'),
(45, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 12:57:35', '2026-09-18 12:57:35'),
(46, 2, 'login', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 13:15:35', '2026-09-18 13:15:35'),
(47, 2, 'login', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 13:15:35', '2026-09-18 13:15:35'),
(48, 2, 'logout', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 13:16:07', '2026-09-18 13:16:07'),
(49, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 13:16:10', '2026-09-18 13:16:10'),
(50, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 13:16:41', '2026-09-18 13:16:41'),
(51, 2, 'login', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 13:16:45', '2026-09-18 13:16:45'),
(52, 2, 'logout', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 13:16:51', '2026-09-18 13:16:51'),
(53, 2, 'login', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 13:34:30', '2026-09-18 13:34:30'),
(54, 2, 'logout', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 13:34:37', '2026-09-18 13:34:37'),
(55, 1, 'updated', 'App\\Models\\Followup', 1, 'Followups', 'Updated Followup \'#1\' (forward_to)', '{\"forward_to\":3}', '{\"forward_to\":2}', '223.185.25.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/followups/reassign/1', '2026-09-18 14:52:22', '2026-09-18 14:52:22'),
(56, NULL, 'updated', 'App\\Models\\User', 2, 'Staff', 'Updated User \'Tharik\' (fcmtoken)', '{\"fcmtoken\":\"c2nWXOcCRBS0n3E09ECZHm:APA91bFgRTCwMLNldQJvcGgPRtXgIkSF-1dJTcGJpgfZj6gh7Y58qgAw-ZC5ph-TIUUfmMVoJXlkpVt6Z4GRZY_xbtoe-9tfQ3nhvsfK7qeafg182g4F1nY\"}', '{\"fcmtoken\":\"ddwB7IXOT9S-yYBXpqeFZH:APA91bGDlP84zwMBFky3QgKefGhHyP67nZFpFccklNu3bMiJkLsLyU_GamI-yf9JDFbLsXep9ispqOAeVTUJQ51RFdlJ-TVDo4Q6Gxm2hKcPeRpH1PfH89c\"}', '223.185.25.62', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/staff/login', '2026-09-18 14:52:38', '2026-09-18 14:52:38'),
(57, 2, 'updated', 'App\\Models\\Followup', 1, 'Followups', 'Updated Followup \'#1\' (followup_status)', '{\"followup_status\":\"Pending\"}', '{\"followup_status\":\"Completed\"}', '106.51.24.147', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/call-recordings', '2026-09-18 15:09:26', '2026-09-18 15:09:26'),
(58, 2, 'created', 'App\\Models\\Followup', 2, 'Followups', 'Created Followup \'#2\'', NULL, '{\"lead_id\":10,\"followup_type\":\"Call\",\"duration\":\"00:44\",\"remarks\":null,\"next_followup_date\":\"2026-09-26 11:00:00\",\"followup_status\":\"Pending\",\"forward_to\":3,\"created_by\":2,\"created_at\":\"2026-09-18T09:39:26.000000Z\",\"followups_id\":2}', '106.51.24.147', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/call-recordings', '2026-09-18 15:09:26', '2026-09-18 15:09:26'),
(59, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 15:09:41', '2026-09-18 15:09:41'),
(60, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 15:10:01', '2026-09-18 15:10:01'),
(61, NULL, 'updated', 'App\\Models\\User', 2, 'Staff', 'Updated User \'Tharik\' (fcmtoken)', '{\"fcmtoken\":\"ddwB7IXOT9S-yYBXpqeFZH:APA91bGDlP84zwMBFky3QgKefGhHyP67nZFpFccklNu3bMiJkLsLyU_GamI-yf9JDFbLsXep9ispqOAeVTUJQ51RFdlJ-TVDo4Q6Gxm2hKcPeRpH1PfH89c\"}', '{\"fcmtoken\":\"c2nWXOcCRBS0n3E09ECZHm:APA91bFgRTCwMLNldQJvcGgPRtXgIkSF-1dJTcGJpgfZj6gh7Y58qgAw-ZC5ph-TIUUfmMVoJXlkpVt6Z4GRZY_xbtoe-9tfQ3nhvsfK7qeafg182g4F1nY\"}', '106.51.24.147', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/staff/login', '2026-09-18 15:36:38', '2026-09-18 15:36:38'),
(62, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 15:38:44', '2026-09-18 15:38:44'),
(63, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 15:46:40', '2026-09-18 15:46:40'),
(64, 2, 'login', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 16:23:16', '2026-09-18 16:23:16'),
(65, 2, 'logout', 'App\\Models\\User', 2, 'Authentication', 'User \'Tharik\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 16:23:25', '2026-09-18 16:23:25'),
(66, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-18 16:23:28', '2026-09-18 16:23:28'),
(67, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-18 16:24:38', '2026-09-18 16:24:38'),
(68, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-19 15:29:00', '2026-09-19 15:29:00'),
(69, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-19 15:29:31', '2026-09-19 15:29:31'),
(70, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-19 15:38:39', '2026-09-19 15:38:39'),
(71, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-19 15:51:35', '2026-09-19 15:51:35'),
(72, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-21 08:48:43', '2026-09-21 08:48:43'),
(73, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.25.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-21 09:32:48', '2026-09-21 09:32:48'),
(74, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.25.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-21 09:32:57', '2026-09-21 09:32:57'),
(75, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '223.185.25.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-21 10:38:20', '2026-09-21 10:38:20'),
(76, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.25.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-21 11:17:48', '2026-09-21 11:17:48'),
(77, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.25.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-21 11:17:58', '2026-09-21 11:17:58'),
(78, NULL, 'updated', 'App\\Models\\User', 2, 'Staff', 'Updated User \'Tharik\' (fcmtoken)', '{\"fcmtoken\":\"c2nWXOcCRBS0n3E09ECZHm:APA91bFgRTCwMLNldQJvcGgPRtXgIkSF-1dJTcGJpgfZj6gh7Y58qgAw-ZC5ph-TIUUfmMVoJXlkpVt6Z4GRZY_xbtoe-9tfQ3nhvsfK7qeafg182g4F1nY\"}', '{\"fcmtoken\":\"eksaY8dhQpi8MMumUFJHcK:APA91bENgf7iIMyt5ms6QuQCwGJb7q-JVGJSgAGl2fopc6kT75UMaAORjdJihJTdY9J2Ra_JF5PEIl-MFmK-orJ2qE5VbeoIQdNyA9DFo8mb3oOMqqU1-14\"}', '157.51.84.251', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/staff/login', '2026-09-21 11:41:15', '2026-09-21 11:41:15'),
(79, 2, 'created', 'App\\Models\\Attendance', 27, 'Attendances', 'Created Attendance \'#27\'', NULL, '{\"user_id\":2,\"date\":\"2026-09-21\",\"check_in\":\"11:47:15\",\"check_out\":null,\"working_hours\":null,\"status\":\"Late\",\"latitude\":8.8759224,\"longitude\":78.1283118,\"sessions\":[{\"session\":1,\"check_in\":\"11:47:15\",\"check_out\":null,\"latitude\":8.8759224,\"longitude\":78.1283118}],\"created_at\":\"2026-09-21T06:17:15.000000Z\",\"attendance_id\":27,\"sessions_list\":[{\"session\":1,\"check_in\":\"11:47:15\",\"check_out\":null,\"latitude\":8.8759224,\"longitude\":78.1283118}],\"is_currently_checked_in\":true,\"current_session_number\":1,\"active_check_in_time\":\"11:47:15\"}', '157.51.84.251', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/attendance/check-in', '2026-09-21 11:47:15', '2026-09-21 11:47:15'),
(80, 2, 'updated', 'App\\Models\\Attendance', 27, 'Attendances', 'Updated Attendance \'#27\' (check_out, working_hours, sessions)', '{\"check_out\":null,\"working_hours\":null,\"sessions\":[{\"session\":1,\"check_in\":\"11:47:15\",\"check_out\":null,\"latitude\":8.8759224,\"longitude\":78.1283118}]}', '{\"check_out\":\"11:47:49\",\"working_hours\":\"0 hrs\",\"sessions\":\"[{\\\"session\\\":1,\\\"check_in\\\":\\\"11:47:15\\\",\\\"check_out\\\":\\\"11:47:49\\\",\\\"latitude\\\":8.8759224,\\\"longitude\\\":78.1283118}]\"}', '157.51.84.251', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/attendance/check-out', '2026-09-21 11:47:49', '2026-09-21 11:47:49'),
(81, 2, 'updated', 'App\\Models\\Attendance', 27, 'Attendances', 'Updated Attendance \'#27\' (second_check_in_latitude, second_check_in_longitude, second_check_in, sessions)', '{\"second_check_in_latitude\":null,\"second_check_in_longitude\":null,\"second_check_in\":null,\"sessions\":[{\"session\":1,\"check_in\":\"11:47:15\",\"check_out\":\"11:47:49\",\"latitude\":8.8759224,\"longitude\":78.1283118}]}', '{\"second_check_in_latitude\":8.875924,\"second_check_in_longitude\":78.1283146,\"second_check_in\":\"11:48:00\",\"sessions\":\"[{\\\"session\\\":1,\\\"check_in\\\":\\\"11:47:15\\\",\\\"check_out\\\":\\\"11:47:49\\\",\\\"latitude\\\":8.8759224,\\\"longitude\\\":78.1283118},{\\\"session\\\":2,\\\"check_in\\\":\\\"11:48:00\\\",\\\"check_out\\\":null,\\\"latitude\\\":8.875924,\\\"longitude\\\":78.1283146}]\"}', '157.51.84.251', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/attendance/check-in', '2026-09-21 11:48:00', '2026-09-21 11:48:00'),
(82, 2, 'updated', 'App\\Models\\Attendance', 27, 'Attendances', 'Updated Attendance \'#27\' (second_check_out, sessions)', '{\"second_check_out\":null,\"sessions\":[{\"session\":1,\"check_in\":\"11:47:15\",\"check_out\":\"11:47:49\",\"latitude\":8.8759224,\"longitude\":78.1283118},{\"session\":2,\"check_in\":\"11:48:00\",\"check_out\":null,\"latitude\":8.875924,\"longitude\":78.1283146}]}', '{\"second_check_out\":\"11:48:16\",\"sessions\":\"[{\\\"session\\\":1,\\\"check_in\\\":\\\"11:47:15\\\",\\\"check_out\\\":\\\"11:47:49\\\",\\\"latitude\\\":8.8759224,\\\"longitude\\\":78.1283118},{\\\"session\\\":2,\\\"check_in\\\":\\\"11:48:00\\\",\\\"check_out\\\":\\\"11:48:16\\\",\\\"latitude\\\":8.875924,\\\"longitude\\\":78.1283146}]\"}', '157.51.84.251', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/attendance/check-out', '2026-09-21 11:48:16', '2026-09-21 11:48:16'),
(83, 2, 'updated', 'App\\Models\\Attendance', 27, 'Attendances', 'Updated Attendance \'#27\' (sessions)', '{\"sessions\":[{\"session\":1,\"check_in\":\"11:47:15\",\"check_out\":\"11:47:49\",\"latitude\":8.8759224,\"longitude\":78.1283118},{\"session\":2,\"check_in\":\"11:48:00\",\"check_out\":\"11:48:16\",\"latitude\":8.875924,\"longitude\":78.1283146}]}', '{\"sessions\":\"[{\\\"session\\\":1,\\\"check_in\\\":\\\"11:47:15\\\",\\\"check_out\\\":\\\"11:47:49\\\",\\\"latitude\\\":8.8759224,\\\"longitude\\\":78.1283118},{\\\"session\\\":2,\\\"check_in\\\":\\\"11:48:00\\\",\\\"check_out\\\":\\\"11:48:16\\\",\\\"latitude\\\":8.875924,\\\"longitude\\\":78.1283146},{\\\"session\\\":3,\\\"check_in\\\":\\\"11:48:21\\\",\\\"check_out\\\":null,\\\"latitude\\\":8.8759249,\\\"longitude\\\":78.1283092}]\"}', '157.51.84.251', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/attendance/check-in', '2026-09-21 11:48:21', '2026-09-21 11:48:21'),
(84, 2, 'created', 'App\\Models\\Customer', 1132, 'Customers', 'Created Customer \'maha\'', NULL, '{\"customer_type\":\"user\",\"name\":\"maha\",\"company_name\":null,\"mobile\":\"8089028830\",\"email\":null,\"alternate_mobile\":null,\"address\":null,\"city\":null,\"state\":\"Tamil Nadu\",\"country\":\"India\",\"pincode\":null,\"owner_by\":2,\"assign_by\":2,\"created_by\":2,\"status\":1,\"custom_fields\":{\"aa\":\"1\",\"category\":\"electronics\",\"number_field\":\"8089028830\",\"text_filed\":null,\"text_area\":null,\"date\":null,\"remarks\":\"\"},\"created_at\":\"2026-09-21T06:49:25.000000Z\",\"customer_id\":1132,\"owner_by_name\":\"Tharik\",\"assign_by_name\":\"Tharik\",\"created_by_name\":\"Tharik\",\"owner_name\":\"Tharik\",\"assign_name\":\"Tharik\",\"assigned_by_name\":\"Tharik\",\"creator_name\":\"Tharik\"}', '157.51.84.251', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/customers', '2026-09-21 12:19:25', '2026-09-21 12:19:25'),
(85, NULL, 'updated', 'App\\Models\\User', 2, 'Staff', 'Updated User \'Tharik\' (fcmtoken)', '{\"fcmtoken\":\"eksaY8dhQpi8MMumUFJHcK:APA91bENgf7iIMyt5ms6QuQCwGJb7q-JVGJSgAGl2fopc6kT75UMaAORjdJihJTdY9J2Ra_JF5PEIl-MFmK-orJ2qE5VbeoIQdNyA9DFo8mb3oOMqqU1-14\"}', '{\"fcmtoken\":\"c2nWXOcCRBS0n3E09ECZHm:APA91bFgRTCwMLNldQJvcGgPRtXgIkSF-1dJTcGJpgfZj6gh7Y58qgAw-ZC5ph-TIUUfmMVoJXlkpVt6Z4GRZY_xbtoe-9tfQ3nhvsfK7qeafg182g4F1nY\"}', '106.51.25.85', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/staff/login', '2026-09-21 12:47:45', '2026-09-21 12:47:45'),
(86, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.25.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-21 16:18:19', '2026-09-21 16:18:19'),
(87, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.25.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-21 16:19:46', '2026-09-21 16:19:46'),
(88, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.25.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-21 16:20:23', '2026-09-21 16:20:23'),
(89, 2, 'created', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Created DemoProcess \'Ravi Kumar\'', NULL, '{\"customer_name\":\"Ravi Kumar\",\"customer_phone\":\"9876543210\",\"lead_requirement_id\":1,\"lead_source_id\":1,\"demo_date\":\"2026-09-21T18:30:00.000000Z\",\"demo_time\":\"11:30 AM\",\"customer_type\":\"New Customer\",\"created_by\":2,\"assigned_by\":2,\"sub_assigned_by\":3,\"status\":\"Pending\",\"remarks\":\"Product demo scheduled for CRM software via Google Meet\",\"custom_fields\":[],\"created_at\":\"2026-09-21T10:53:55.000000Z\",\"demo_process_id\":4}', '106.51.25.85', 'bruno-runtime/3.5.2', 'https://erixon.in/crm/api/v1/demo-processes', '2026-09-21 16:23:55', '2026-09-21 16:23:55'),
(90, 2, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (customer_name)', '{\"customer_name\":\"Ravi Kumar\"}', '{\"customer_name\":\"ajis Kumar\"}', '106.51.25.85', 'bruno-runtime/3.5.2', 'https://erixon.in/crm/api/v1/demo-processes/update/4', '2026-09-21 16:26:31', '2026-09-21 16:26:31'),
(91, 2, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (status)', '{\"status\":\"Pending\"}', '{\"status\":\"Finished\"}', '106.51.25.85', 'bruno-runtime/3.5.2', 'https://erixon.in/crm/api/v1/demo-processes/change-status/4', '2026-09-21 16:27:54', '2026-09-21 16:27:54'),
(92, 2, 'deleted', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Deleted DemoProcess \'ajis Kumar\'', '{\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"customer_phone\":\"9876543210\",\"lead_source_id\":1,\"lead_requirement_id\":1,\"demo_date\":\"2026-09-21T18:30:00.000000Z\",\"demo_time\":\"11:30 AM\",\"customer_type\":\"New Customer\",\"created_by\":2,\"assigned_by\":2,\"sub_assigned_by\":3,\"status\":\"Finished\",\"remarks\":\"Product demo scheduled for CRM software via Google Meet\",\"custom_fields\":[],\"created_at\":\"2026-09-21T10:53:55.000000Z\",\"deleted_at\":\"2026-09-21T10:58:58.000000Z\"}', NULL, '106.51.25.85', 'bruno-runtime/3.5.2', 'https://erixon.in/crm/api/v1/demo-processes/delete/4', '2026-09-21 16:28:58', '2026-09-21 16:28:58'),
(93, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.25.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-21 16:33:15', '2026-09-21 16:33:15'),
(94, NULL, 'updated', 'App\\Models\\User', 2, 'Staff', 'Updated User \'Tharik\' (fcmtoken)', '{\"fcmtoken\":\"c2nWXOcCRBS0n3E09ECZHm:APA91bFgRTCwMLNldQJvcGgPRtXgIkSF-1dJTcGJpgfZj6gh7Y58qgAw-ZC5ph-TIUUfmMVoJXlkpVt6Z4GRZY_xbtoe-9tfQ3nhvsfK7qeafg182g4F1nY\"}', '{\"fcmtoken\":\"cyEG1ItcSbeRfzQifeGLJ4:APA91bHE1YaDhuUfyWzK7kp0GRwGow9IY-D74PLTiqGTPcR0TTC0RTrUf6G9OlfqWgsXw2jr7SXr2lutVrBSnmuhO8A6hjcM_0s15L5Wsk2_2tAyv37OKh8\"}', '106.51.25.85', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/staff/login', '2026-09-21 16:36:33', '2026-09-21 16:36:33'),
(95, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-21 17:06:00', '2026-09-21 17:06:00'),
(96, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 11:03:07', '2026-09-22 11:03:07'),
(97, NULL, 'updated', 'App\\Models\\User', 2, 'Staff', 'Updated User \'Tharik\' (fcmtoken)', '{\"fcmtoken\":\"cyEG1ItcSbeRfzQifeGLJ4:APA91bHE1YaDhuUfyWzK7kp0GRwGow9IY-D74PLTiqGTPcR0TTC0RTrUf6G9OlfqWgsXw2jr7SXr2lutVrBSnmuhO8A6hjcM_0s15L5Wsk2_2tAyv37OKh8\"}', '{\"fcmtoken\":\"d4CnIBsASribX25g2N6faS:APA91bEwaWPmXbwwGxf5Q_wrtg0RRW6IXBE6thiDnY_iXpzc6pQv0cGDx832Sxu-CWK4xYc-AwspfFZ6wfthR-mpIp7hmVfGJmuLTOhQxI0tJMZ06Qh_IxM\"}', '111.92.30.51', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/staff/login', '2026-09-22 12:18:47', '2026-09-22 12:18:47'),
(98, 2, 'created', 'App\\Models\\Attendance', 28, 'Attendances', 'Created Attendance \'#28\'', NULL, '{\"user_id\":2,\"date\":\"2026-09-22\",\"check_in\":\"12:19:02\",\"check_out\":null,\"working_hours\":null,\"status\":\"Late\",\"latitude\":8.4766735,\"longitude\":76.9482306,\"sessions\":[{\"session\":1,\"check_in\":\"12:19:02\",\"check_out\":null,\"latitude\":8.4766735,\"longitude\":76.9482306}],\"created_at\":\"2026-09-22T06:49:02.000000Z\",\"attendance_id\":28,\"sessions_list\":[{\"session\":1,\"check_in\":\"12:19:02\",\"check_out\":null,\"latitude\":8.4766735,\"longitude\":76.9482306}],\"is_currently_checked_in\":true,\"current_session_number\":1,\"active_check_in_time\":\"12:19:02\"}', '111.92.30.51', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/attendance/check-in', '2026-09-22 12:19:02', '2026-09-22 12:19:02'),
(99, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 12:54:39', '2026-09-22 12:54:39'),
(100, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 12:56:40', '2026-09-22 12:56:40'),
(101, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 13:03:58', '2026-09-22 13:03:58'),
(102, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 13:04:02', '2026-09-22 13:04:02'),
(103, 1, 'created', 'App\\Models\\Payment', 3, 'Payments', 'Created Payment \'#3\'', NULL, '{\"customer_id\":\"6\",\"lead_source_id\":null,\"lead_requirement_id\":\"3\",\"amount\":\"2500.00\",\"tax_percentage\":\"18.00\",\"tax_amount\":\"450.00\",\"total_amount\":\"2950.00\",\"payment_method\":\"UPI \\/ QR\",\"payment_date\":\"2026-09-21T18:30:00.000000Z\",\"payment_screenshot\":\"uploads\\/payments\\/1790062507_WhatsApp_Image_2026-09-17_at_2.05.48_PM_(1).jpeg\",\"tax_number\":\"sdsadsadsadsadsad\",\"remarks\":null,\"created_by\":1,\"created_at\":\"2026-09-22T07:35:07.000000Z\",\"payment_id\":3}', '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/payments/store', '2026-09-22 13:05:07', '2026-09-22 13:05:07'),
(104, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 13:09:18', '2026-09-22 13:09:18'),
(105, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 13:09:20', '2026-09-22 13:09:20'),
(106, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 13:10:08', '2026-09-22 13:10:08'),
(107, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 13:10:09', '2026-09-22 13:10:09'),
(108, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 13:10:10', '2026-09-22 13:10:10'),
(109, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 13:10:15', '2026-09-22 13:10:15'),
(110, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 13:32:33', '2026-09-22 13:32:33'),
(111, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 13:34:22', '2026-09-22 13:34:22'),
(112, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 14:33:20', '2026-09-22 14:33:20'),
(113, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 14:33:54', '2026-09-22 14:33:54'),
(114, NULL, 'updated', 'App\\Models\\User', 6, 'Staff', 'Updated User \'Shanmuga Sundari\' (fcmtoken)', '{\"fcmtoken\":null}', '{\"fcmtoken\":\"dRW7fn7XSIypczZ9hwbTPw:APA91bFoy-Sa9WXFz1qdQkUsxYJLebyf9b9ZFVCS6X-AJmf4As5fda6fRl19ftRN1hgizZbkvg06A3OMi6mRNLQi1KHxH-lB3VojQjdj-u5ckJDDDh3B9Ic\"}', '49.37.200.88', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/staff/login', '2026-09-22 14:46:02', '2026-09-22 14:46:02'),
(115, 6, 'created', 'App\\Models\\Attendance', 29, 'Attendances', 'Created Attendance \'#29\'', NULL, '{\"user_id\":6,\"date\":\"2026-09-22\",\"check_in\":\"14:46:29\",\"check_out\":null,\"working_hours\":null,\"status\":\"Late\",\"latitude\":8.6956636,\"longitude\":77.7332807,\"sessions\":[{\"session\":1,\"check_in\":\"14:46:29\",\"check_out\":null,\"latitude\":8.6956636,\"longitude\":77.7332807}],\"created_at\":\"2026-09-22T09:16:29.000000Z\",\"attendance_id\":29,\"sessions_list\":[{\"session\":1,\"check_in\":\"14:46:29\",\"check_out\":null,\"latitude\":8.6956636,\"longitude\":77.7332807}],\"is_currently_checked_in\":true,\"current_session_number\":1,\"active_check_in_time\":\"14:46:29\"}', '49.37.200.88', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/attendance/check-in', '2026-09-22 14:46:29', '2026-09-22 14:46:29'),
(116, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 15:20:12', '2026-09-22 15:20:12'),
(117, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 15:21:02', '2026-09-22 15:21:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `event`, `auditable_type`, `auditable_id`, `module`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `created_at`, `updated_at`) VALUES
(118, 6, 'created', 'App\\Models\\Customer', 1133, 'Customers', 'Created Customer \'vishnu\'', NULL, '{\"customer_type\":\"user\",\"name\":\"vishnu\",\"company_name\":\"Erixon Network\",\"mobile\":\"8778188245\",\"email\":\"erixonsales@gmail.com\",\"alternate_mobile\":null,\"address\":null,\"city\":null,\"state\":\"Tamil Nadu\",\"country\":\"India\",\"pincode\":null,\"owner_by\":6,\"assign_by\":10,\"created_by\":6,\"status\":1,\"custom_fields\":{\"aa\":\"1\",\"category\":\"electronics\",\"text_filed\":\"Mahendra\",\"date\":\"2026-09-24\",\"number_field\":\"8778188245\",\"text_area\":null,\"remarks\":\"\"},\"created_at\":\"2026-09-22T10:10:28.000000Z\",\"customer_id\":1133,\"owner_by_name\":\"Shanmuga Sundari\",\"assign_by_name\":\"Baiju\",\"created_by_name\":\"Shanmuga Sundari\",\"owner_name\":\"Shanmuga Sundari\",\"assign_name\":\"Baiju\",\"assigned_by_name\":\"Baiju\",\"creator_name\":\"Shanmuga Sundari\"}', '49.37.200.88', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/customers', '2026-09-22 15:40:28', '2026-09-22 15:40:28'),
(119, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 16:08:46', '2026-09-22 16:08:46'),
(120, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 16:08:53', '2026-09-22 16:08:53'),
(121, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-22 16:38:34', '2026-09-22 16:38:34'),
(122, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-22 16:38:37', '2026-09-22 16:38:37'),
(123, NULL, 'updated', 'App\\Models\\User', 2, 'Staff', 'Updated User \'Tharik\' (fcmtoken)', '{\"fcmtoken\":\"d4CnIBsASribX25g2N6faS:APA91bEwaWPmXbwwGxf5Q_wrtg0RRW6IXBE6thiDnY_iXpzc6pQv0cGDx832Sxu-CWK4xYc-AwspfFZ6wfthR-mpIp7hmVfGJmuLTOhQxI0tJMZ06Qh_IxM\"}', '{\"fcmtoken\":\"cyEG1ItcSbeRfzQifeGLJ4:APA91bHE1YaDhuUfyWzK7kp0GRwGow9IY-D74PLTiqGTPcR0TTC0RTrUf6G9OlfqWgsXw2jr7SXr2lutVrBSnmuhO8A6hjcM_0s15L5Wsk2_2tAyv37OKh8\"}', '106.51.24.192', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/staff/login', '2026-09-22 16:58:58', '2026-09-22 16:58:58'),
(124, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-22 17:13:45', '2026-09-22 17:13:45'),
(125, 2, 'created', 'App\\Models\\DemoProcess', 5, 'Demo Processes', 'Created DemoProcess \'Ravi Kumar\'', NULL, '{\"customer_name\":\"Ravi Kumar\",\"customer_phone\":\"9876543210\",\"lead_requirement_id\":1,\"lead_source_id\":1,\"demo_date\":\"2026-09-21T18:30:00.000000Z\",\"demo_time\":\"11:30 AM\",\"customer_type\":\"New Customer\",\"created_by\":2,\"assigned_by\":2,\"sub_assigned_by\":3,\"status\":\"Pending\",\"remarks\":\"Product demo scheduled for CRM software via Google Meet\",\"custom_fields\":[],\"created_at\":\"2026-09-23T04:45:01.000000Z\",\"demo_process_id\":5}', '223.185.21.142', 'bruno-runtime/3.1.4', 'https://erixon.in/crm/api/v1/demo-processes', '2026-09-23 10:15:01', '2026-09-23 10:15:01'),
(126, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-23 10:28:26', '2026-09-23 10:28:26'),
(127, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-23 10:36:21', '2026-09-23 10:36:21'),
(128, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-23 10:40:14', '2026-09-23 10:40:14'),
(129, 1, 'created', 'App\\Models\\Attendance', 30, 'Attendances', 'Created Attendance \'#30\'', NULL, '{\"user_id\":1,\"date\":\"2026-09-23\",\"check_in\":\"10:45:14\",\"check_out\":null,\"working_hours\":null,\"status\":\"Present\",\"latitude\":\"8.87776\",\"longitude\":\"78.13277\",\"sessions\":[{\"session\":1,\"check_in\":\"10:45:14\",\"check_out\":null,\"latitude\":\"8.87776\",\"longitude\":\"78.13277\"}],\"created_at\":\"2026-09-23T05:15:14.000000Z\",\"attendance_id\":30,\"sessions_list\":[{\"session\":1,\"check_in\":\"10:45:14\",\"check_out\":null,\"latitude\":\"8.87776\",\"longitude\":\"78.13277\"}],\"is_currently_checked_in\":true,\"current_session_number\":1,\"active_check_in_time\":\"10:45:14\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/attendance/mark-self', '2026-09-23 10:45:14', '2026-09-23 10:45:14'),
(130, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-23 10:49:07', '2026-09-23 10:49:07'),
(131, 2, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (status)', '{\"status\":\"Finished\"}', '{\"status\":\"Pending\"}', '223.185.21.142', 'bruno-runtime/3.1.4', 'https://erixon.in/crm/api/v1/demo-processes/change-status/4', '2026-09-23 10:54:07', '2026-09-23 10:54:07'),
(132, 2, 'updated', 'App\\Models\\DemoProcess', 5, 'Demo Processes', 'Updated DemoProcess \'Ravi Kumar\' (status)', '{\"status\":\"Pending\"}', '{\"status\":\"Finished\"}', '223.185.21.142', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/demo-processes/change-status/5', '2026-09-23 11:01:22', '2026-09-23 11:01:22'),
(133, 1, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (status)', '{\"status\":\"Pending\"}', '{\"status\":\"Finished\"}', '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/demo-processes/update-status/4', '2026-09-23 11:08:45', '2026-09-23 11:08:45'),
(134, 1, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (status)', '{\"status\":\"Finished\"}', '{\"status\":\"Pending\"}', '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/demo-processes/update-status/4', '2026-09-23 11:08:46', '2026-09-23 11:08:46'),
(135, 1, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (status)', '{\"status\":\"Pending\"}', '{\"status\":\"Finished\"}', '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/demo-processes/update-status/4', '2026-09-23 11:08:47', '2026-09-23 11:08:47'),
(136, 1, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (status)', '{\"status\":\"Finished\"}', '{\"status\":\"Pending\"}', '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/demo-processes/update-status/4', '2026-09-23 11:08:47', '2026-09-23 11:08:47'),
(137, 1, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (status)', '{\"status\":\"Pending\"}', '{\"status\":\"Finished\"}', '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/demo-processes/update-status/4', '2026-09-23 11:08:54', '2026-09-23 11:08:54'),
(138, 1, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (status)', '{\"status\":\"Finished\"}', '{\"status\":\"Pending\"}', '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/demo-processes/update-status/4', '2026-09-23 11:08:55', '2026-09-23 11:08:55'),
(139, 1, 'updated', 'App\\Models\\DemoProcess', 4, 'Demo Processes', 'Updated DemoProcess \'ajis Kumar\' (status)', '{\"status\":\"Pending\"}', '{\"status\":\"Finished\"}', '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/demo-processes/update-status/4', '2026-09-23 11:10:26', '2026-09-23 11:10:26'),
(140, 1, 'updated', 'App\\Models\\CreditRequest', 4, 'Credit Requests', 'Updated CreditRequest \'#4\' (status, admin_approved_by, admin_approved_at)', '{\"status\":\"Pending Admin Approval\",\"admin_approved_by\":null,\"admin_approved_at\":null}', '{\"status\":\"Forwarded to Product Manager\",\"admin_approved_by\":1,\"admin_approved_at\":\"2026-09-23 11:14:31\"}', '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/credit-requests/approve-admin/4', '2026-09-23 11:14:31', '2026-09-23 11:14:31'),
(141, 2, 'created', 'App\\Models\\CreditRequest', 8, 'Credit Requests', 'Created CreditRequest \'#8\'', NULL, '{\"customer_id\":1,\"lead_source_id\":2,\"lead_requirement_id\":3,\"credit_amount\":\"5000.00\",\"is_estimate\":false,\"phone\":\"9876543210\",\"email\":\"john@example.com\",\"status\":\"Pending Admin Approval\",\"requested_by\":2,\"custom_fields\":{\"username\":\"john_doe\"},\"created_at\":\"2026-09-23T05:50:01.000000Z\",\"credit_request_id\":8}', '223.185.21.142', 'bruno-runtime/3.1.4', 'https://erixon.in/crm/api/v1/credit-requests', '2026-09-23 11:20:01', '2026-09-23 11:20:01'),
(142, 2, 'updated', 'App\\Models\\CreditRequest', 8, 'Credit Requests', 'Updated CreditRequest \'#8\' (status, admin_remarks)', '{\"status\":\"Pending Admin Approval\",\"admin_remarks\":null}', '{\"status\":\"Rejected\",\"admin_remarks\":\"reject\"}', '223.185.21.142', 'bruno-runtime/3.1.4', 'https://erixon.in/crm/api/v1/credit-requests/reject/8', '2026-09-23 11:50:57', '2026-09-23 11:50:57'),
(143, 1, 'updated', 'App\\Models\\CreditRequest', 3, 'Credit Requests', 'Updated CreditRequest \'#3\' (status, admin_approved_by, admin_approved_at)', '{\"status\":\"Pending Admin Approval\",\"admin_approved_by\":null,\"admin_approved_at\":null}', '{\"status\":\"Forwarded to Product Manager\",\"admin_approved_by\":1,\"admin_approved_at\":\"2026-09-23 11:52:13\"}', '223.185.21.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/credit-requests/approve-admin/3', '2026-09-23 11:52:13', '2026-09-23 11:52:13'),
(144, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-23 11:52:23', '2026-09-23 11:52:23'),
(145, 2, 'created', 'App\\Models\\CreditRequest', 9, 'Credit Requests', 'Created CreditRequest \'#9\'', NULL, '{\"customer_id\":1124,\"lead_source_id\":6,\"lead_requirement_id\":4,\"credit_amount\":\"5000.00\",\"is_estimate\":true,\"phone\":\"8523694591\",\"email\":\"rohit@gmail.com\",\"status\":\"Pending Admin Approval\",\"requested_by\":2,\"custom_fields\":{\"username\":\"mmmm\"},\"created_at\":\"2026-09-23T06:35:25.000000Z\",\"credit_request_id\":9}', '223.185.21.142', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/credit-requests', '2026-09-23 12:05:25', '2026-09-23 12:05:25'),
(146, 2, 'updated', 'App\\Models\\CreditRequest', 9, 'Credit Requests', 'Updated CreditRequest \'#9\' (status, admin_remarks)', '{\"status\":\"Pending Admin Approval\",\"admin_remarks\":null}', '{\"status\":\"Rejected\",\"admin_remarks\":\"reject\"}', '223.185.21.142', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/credit-requests/reject/9', '2026-09-23 12:06:06', '2026-09-23 12:06:06'),
(147, 2, 'updated', 'App\\Models\\CreditRequest', 3, 'Credit Requests', 'Updated CreditRequest \'#3\' (status, support_approved_by, support_approved_at, support_remarks)', '{\"status\":\"Forwarded to Product Manager\",\"support_approved_by\":null,\"support_approved_at\":null,\"support_remarks\":null}', '{\"status\":\"Credit Added\",\"support_approved_by\":2,\"support_approved_at\":\"2026-09-23 12:23:13\",\"support_remarks\":\"ok\"}', '223.185.21.142', 'Dart/3.11 (dart:io)', 'https://erixon.in/crm/api/v1/credit-requests/approve-support/3', '2026-09-23 12:23:13', '2026-09-23 12:23:13'),
(148, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-23 12:38:26', '2026-09-23 12:38:26'),
(149, 1, 'created', 'App\\Models\\Customer', 1134, 'Customers', 'Created Customer \'ajisajis\'', NULL, '{\"name\":\"ajisajis\",\"customer_type\":\"user\",\"mobile\":\"9631234123\",\"email\":\"ajisajis@gmail.com\",\"status\":1,\"created_by\":1,\"created_at\":\"2026-09-23T07:29:02.000000Z\",\"customer_id\":1134,\"owner_by_name\":null,\"assign_by_name\":null,\"created_by_name\":\"Ezecrm - Sundari (Admin)\",\"owner_name\":null,\"assign_name\":null,\"assigned_by_name\":null,\"creator_name\":\"Ezecrm - Sundari (Admin)\"}', '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/leads/store', '2026-09-23 12:59:02', '2026-09-23 12:59:02'),
(150, 1, 'created', 'App\\Models\\Lead', 13, 'Leads', 'Created Lead \'Fashion design\'', NULL, '{\"customer_name\":\"ajisajis\",\"customer_type\":\"user\",\"mobile\":\"9631234123\",\"email\":\"ajisajis@gmail.com\",\"lead_title\":\"Fashion design\",\"lead_source_id\":\"4\",\"lead_stage_id\":\"7\",\"lead_requirement_id\":\"4\",\"assigned_to\":\"2\",\"priority\":\"medium\",\"expected_amount\":\"2500\",\"description\":\"dsadsadsad\",\"next_followup_date\":\"2026-09-24\",\"status\":\"1\",\"lost_reason_id\":null,\"custom_fields\":[],\"customer_id\":1134,\"created_by\":1,\"created_at\":\"2026-09-23T07:29:02.000000Z\",\"lead_id\":13}', '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/leads/store', '2026-09-23 12:59:02', '2026-09-23 12:59:02'),
(151, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-23 12:59:27', '2026-09-23 12:59:27'),
(152, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-23 13:05:09', '2026-09-23 13:05:09'),
(153, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-23 13:08:47', '2026-09-23 13:08:47'),
(154, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-23 13:09:02', '2026-09-23 13:09:02'),
(155, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-23 13:12:38', '2026-09-23 13:12:38'),
(156, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-23 13:12:54', '2026-09-23 13:12:54'),
(157, 1, 'created', 'App\\Models\\Customer', 1135, 'Customers', 'Created Customer \'Santha Jewellery\'', NULL, '{\"customer_type\":\"user\",\"name\":\"Santha Jewellery\",\"company_name\":null,\"mobile\":\"9008005656\",\"email\":null,\"alternate_mobile\":null,\"address\":null,\"city\":\"Tirunelveli\",\"state\":null,\"country\":\"India\",\"pincode\":null,\"owner_by\":\"1\",\"assign_by\":\"1\",\"status\":\"1\",\"created_by\":1,\"custom_fields\":[],\"created_at\":\"2026-09-23T07:57:32.000000Z\",\"customer_id\":1135,\"owner_by_name\":\"Ezecrm - Sundari (Admin)\",\"assign_by_name\":\"Ezecrm - Sundari (Admin)\",\"created_by_name\":\"Ezecrm - Sundari (Admin)\",\"owner_name\":\"Ezecrm - Sundari (Admin)\",\"assign_name\":\"Ezecrm - Sundari (Admin)\",\"assigned_by_name\":\"Ezecrm - Sundari (Admin)\",\"creator_name\":\"Ezecrm - Sundari (Admin)\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/customers/store', '2026-09-23 13:27:32', '2026-09-23 13:27:32'),
(158, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-23 13:28:13', '2026-09-23 13:28:13'),
(159, 1, 'updated', 'App\\Models\\Customer', 4, 'Customers', 'Updated Customer \'John Doe\' (company_name)', '{\"company_name\":\"Acme Corp\"}', '{\"company_name\":\"ABC Enterprises\"}', '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/customers/import', '2026-09-23 13:30:20', '2026-09-23 13:30:20'),
(160, 1, 'updated', 'App\\Models\\Customer', 4, 'Customers', 'Updated Customer \'John Doe\' (company_name)', '{\"company_name\":\"Acme Corp\"}', '{\"company_name\":\"ABC Enterprises\"}', '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/customers/import', '2026-09-23 13:30:20', '2026-09-23 13:30:20'),
(161, 1, 'created', 'App\\Models\\Customer', 1136, 'Customers', 'Created Customer \'dhoni Smith\'', NULL, '{\"name\":\"dhoni Smith\",\"customer_type\":\"reseller\",\"company_name\":\"XYZ Solutions\",\"mobile\":\"9123456780\",\"email\":\"jane@example.com\",\"alternate_mobile\":null,\"address\":\"456 Cross Rd\",\"city\":\"Madurai\",\"state\":\"Tamil Nadu\",\"country\":\"India\",\"pincode\":\"625001\",\"status\":1,\"created_by\":1,\"created_at\":\"2026-09-23T08:00:20.000000Z\",\"customer_id\":1136,\"owner_by_name\":null,\"assign_by_name\":null,\"created_by_name\":\"Ezecrm - Sundari (Admin)\",\"owner_name\":null,\"assign_name\":null,\"assigned_by_name\":null,\"creator_name\":\"Ezecrm - Sundari (Admin)\"}', '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/customers/import', '2026-09-23 13:30:20', '2026-09-23 13:30:20'),
(162, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-23 13:33:08', '2026-09-23 13:33:08'),
(163, 1, 'updated', 'App\\Models\\Customer', 1135, 'Customers', 'Updated Customer \'Harikaran\' (9 fields modified)', '{\"name\":\"Santha Jewellery\",\"company_name\":null,\"mobile\":\"9008005656\",\"email\":null,\"alternate_mobile\":null,\"address\":null,\"state\":null,\"pincode\":null,\"custom_fields\":[]}', '{\"name\":\"Harikaran\",\"company_name\":\"Santha Jewellery\",\"mobile\":\"900800556\",\"email\":\"santhajewellery2018@gmail.com\",\"alternate_mobile\":\"9550482345\",\"address\":\"5\\/91,\\r\\n3rd floor, \\r\\nNew Bus stand near,\",\"state\":\"Tamil nadu\",\"pincode\":\"627007\",\"custom_fields\":\"{\\\"followup_date\\\":\\\"2026-09-24\\\"}\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/customers/update/1135', '2026-09-23 13:33:44', '2026-09-23 13:33:44'),
(164, 1, 'updated', 'App\\Models\\Customer', 1117, 'Customers', 'Updated Customer \'suriya\' (status, custom_fields)', '{\"status\":1,\"custom_fields\":{\"aadhar_number\":\"fdsfdsfdsfdsfds\",\"category\":\"electronics\"}}', '{\"status\":\"0\",\"custom_fields\":\"{\\\"followup_date\\\":null}\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/customers/update/1117', '2026-09-23 13:39:19', '2026-09-23 13:39:19'),
(165, 1, 'created', 'App\\Models\\Customer', 1138, 'Customers', 'Created Customer \'manimala\'', NULL, '{\"name\":\"manimala\",\"customer_type\":\"reseller\",\"company_name\":null,\"mobile\":\"9087542578\",\"email\":null,\"alternate_mobile\":null,\"address\":null,\"city\":null,\"state\":null,\"country\":\"India\",\"pincode\":null,\"status\":1,\"created_by\":1,\"created_at\":\"2026-09-23T08:20:31.000000Z\",\"customer_id\":1138,\"owner_by_name\":null,\"assign_by_name\":null,\"created_by_name\":\"Ezecrm - Sundari (Admin)\",\"owner_name\":null,\"assign_name\":null,\"assigned_by_name\":null,\"creator_name\":\"Ezecrm - Sundari (Admin)\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/customers/import', '2026-09-23 13:50:31', '2026-09-23 13:50:31'),
(166, 1, 'created', 'App\\Models\\Customer', 1139, 'Customers', 'Created Customer \'harini\'', NULL, '{\"name\":\"harini\",\"customer_type\":\"user\",\"company_name\":null,\"mobile\":\"9876532356\",\"email\":null,\"alternate_mobile\":null,\"address\":null,\"city\":null,\"state\":null,\"country\":\"India\",\"pincode\":null,\"status\":1,\"created_by\":1,\"created_at\":\"2026-09-23T08:20:31.000000Z\",\"customer_id\":1139,\"owner_by_name\":null,\"assign_by_name\":null,\"created_by_name\":\"Ezecrm - Sundari (Admin)\",\"owner_name\":null,\"assign_name\":null,\"assigned_by_name\":null,\"creator_name\":\"Ezecrm - Sundari (Admin)\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/customers/import', '2026-09-23 13:50:31', '2026-09-23 13:50:31'),
(167, 1, 'created', 'App\\Models\\Customer', 1140, 'Customers', 'Created Customer \'vishal\'', NULL, '{\"name\":\"vishal\",\"customer_type\":\"reseller\",\"company_name\":null,\"mobile\":\"8895623456\",\"email\":null,\"alternate_mobile\":null,\"address\":null,\"city\":null,\"state\":null,\"country\":\"India\",\"pincode\":null,\"status\":1,\"created_by\":1,\"created_at\":\"2026-09-23T08:20:31.000000Z\",\"customer_id\":1140,\"owner_by_name\":null,\"assign_by_name\":null,\"created_by_name\":\"Ezecrm - Sundari (Admin)\",\"owner_name\":null,\"assign_name\":null,\"assigned_by_name\":null,\"creator_name\":\"Ezecrm - Sundari (Admin)\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/customers/import', '2026-09-23 13:50:31', '2026-09-23 13:50:31'),
(168, 1, 'updated', 'App\\Models\\Customer', 1140, 'Customers', 'Updated Customer \'vishal\' (7 fields modified)', '{\"company_name\":null,\"email\":null,\"alternate_mobile\":null,\"city\":null,\"owner_by\":null,\"assign_by\":null,\"custom_fields\":null}', '{\"company_name\":\"RK Automobiles\",\"email\":\"rkautomobiles@gmail.com\",\"alternate_mobile\":\"876543239\",\"city\":\"Trichy\",\"owner_by\":\"6\",\"assign_by\":\"6\",\"custom_fields\":\"{\\\"followup_date\\\":\\\"2026-09-25\\\"}\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/customers/update/1140', '2026-09-23 13:52:05', '2026-09-23 13:52:05'),
(169, 1, 'updated', 'App\\Models\\Customer', 1140, 'Customers', 'Updated Customer \'vishal\' (custom_fields)', '{\"custom_fields\":{\"followup_date\":\"2026-09-25\"}}', '{\"custom_fields\":\"{\\\"followup_date\\\":\\\"2026-09-25\\\",\\\"sale_closed_date\\\":\\\"2026-09-22\\\",\\\"customer_created_date\\\":\\\"2026-09-23\\\"}\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/customers/update/1140', '2026-09-23 13:56:03', '2026-09-23 13:56:03'),
(170, 1, 'updated', 'App\\Models\\Customer', 1139, 'Customers', 'Updated Customer \'harini\' (custom_fields)', '{\"custom_fields\":null}', '{\"custom_fields\":\"{\\\"followup_date\\\":\\\"2026-09-26\\\",\\\"sale_closed_date\\\":\\\"2026-09-16\\\",\\\"customer_created_date\\\":\\\"2026-09-16\\\"}\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/customers/update/1139', '2026-09-23 13:56:30', '2026-09-23 13:56:30'),
(171, 1, 'created', 'App\\Models\\CreditRequest', 10, 'Credit Requests', 'Created CreditRequest \'#10\'', NULL, '{\"customer_id\":\"791\",\"lead_source_id\":\"6\",\"lead_requirement_id\":\"6\",\"credit_amount\":\"5900.00\",\"is_estimate\":false,\"phone\":\"2707\",\"email\":\"anand@sparkalerts.in\",\"status\":\"Pending Admin Approval\",\"requested_by\":1,\"custom_fields\":[],\"created_at\":\"2026-09-23T08:56:10.000000Z\",\"credit_request_id\":10}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/credit-requests/store', '2026-09-23 14:26:10', '2026-09-23 14:26:10'),
(172, 1, 'updated', 'App\\Models\\CreditRequest', 10, 'Credit Requests', 'Updated CreditRequest \'#10\' (status, admin_approved_by, admin_approved_at)', '{\"status\":\"Pending Admin Approval\",\"admin_approved_by\":null,\"admin_approved_at\":null}', '{\"status\":\"Forwarded to Product Manager\",\"admin_approved_by\":1,\"admin_approved_at\":\"2026-09-23 14:26:27\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/credit-requests/approve-admin/10', '2026-09-23 14:26:27', '2026-09-23 14:26:27'),
(173, 5, 'login', 'App\\Models\\User', 5, 'Authentication', 'User \'Maharajan\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-23 14:26:52', '2026-09-23 14:26:52'),
(174, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-23 14:29:02', '2026-09-23 14:29:02'),
(175, 1, 'updated', 'App\\Models\\User', 5, 'Staff', 'Updated User \'Maharajan\' (email, check_in_time, allow_check_in_time, check_out_time)', '{\"email\":\"tecsupport@erixon.in\",\"check_in_time\":\"09:00:00\",\"allow_check_in_time\":\"09:10:00\",\"check_out_time\":\"18:00:00\"}', '{\"email\":\"techsupport@erixon.in\",\"check_in_time\":\"09:00\",\"allow_check_in_time\":\"09:10\",\"check_out_time\":\"18:00\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/edit_staff/5', '2026-09-23 14:29:39', '2026-09-23 14:29:39'),
(176, 5, 'login', 'App\\Models\\User', 5, 'Authentication', 'User \'Maharajan\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-23 14:29:56', '2026-09-23 14:29:56'),
(177, 5, 'login', 'App\\Models\\User', 5, 'Authentication', 'User \'Maharajan\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-23 14:30:01', '2026-09-23 14:30:01'),
(178, 5, 'updated', 'App\\Models\\CreditRequest', 10, 'Credit Requests', 'Updated CreditRequest \'#10\' (status, support_approved_by, support_approved_at, support_remarks)', '{\"status\":\"Forwarded to Product Manager\",\"support_approved_by\":null,\"support_approved_at\":null,\"support_remarks\":null}', '{\"status\":\"Credit Added\",\"support_approved_by\":5,\"support_approved_at\":\"2026-09-23 14:31:17\",\"support_remarks\":\"25000 credit added\"}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/credit-requests/approve-support/10', '2026-09-23 14:31:17', '2026-09-23 14:31:17'),
(179, 5, 'updated', 'App\\Models\\Customer', 791, 'Customers', 'Updated Customer \'10003\' (credit_balance)', '{\"credit_balance\":\"0.00\"}', '{\"credit_balance\":5900}', '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/credit-requests/approve-support/10', '2026-09-23 14:31:17', '2026-09-23 14:31:17'),
(180, 5, 'logout', 'App\\Models\\User', 5, 'Authentication', 'User \'Maharajan\' logged out.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/logout', '2026-09-23 14:31:40', '2026-09-23 14:31:40'),
(181, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '157.51.84.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-23 14:32:03', '2026-09-23 14:32:03'),
(182, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-23 14:52:41', '2026-09-23 14:52:41'),
(183, 1, 'logout', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged out.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/logout', '2026-09-23 15:30:55', '2026-09-23 15:30:55'),
(184, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-23 16:04:40', '2026-09-23 16:04:40'),
(185, 1, 'updated', 'App\\Models\\Customer', 1136, 'Customers', 'Updated Customer \'Jane Smith\' (name)', '{\"name\":\"dhoni Smith\"}', '{\"name\":\"Jane Smith\"}', '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/customers/import', '2026-09-23 16:06:16', '2026-09-23 16:06:16'),
(186, 1, 'updated', 'App\\Models\\Customer', 1136, 'Customers', 'Updated Customer \'Jane Smith\' (name)', '{\"name\":\"dhoni Smith\"}', '{\"name\":\"Jane Smith\"}', '106.51.24.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/customers/import', '2026-09-23 16:06:16', '2026-09-23 16:06:16'),
(187, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '157.51.19.114', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/login', '2026-09-23 17:39:40', '2026-09-23 17:39:40'),
(188, 1, 'updated', 'App\\Models\\Attendance', 30, 'Attendances', 'Updated Attendance \'#30\' (check_out, working_hours, sessions)', '{\"check_out\":null,\"working_hours\":null,\"sessions\":[{\"session\":1,\"check_in\":\"10:45:14\",\"check_out\":null,\"latitude\":\"8.87776\",\"longitude\":\"78.13277\"}]}', '{\"check_out\":\"17:39:42\",\"working_hours\":\"6 hrs 54 mins\",\"sessions\":\"[{\\\"session\\\":1,\\\"check_in\\\":\\\"10:45:14\\\",\\\"check_out\\\":\\\"17:39:42\\\",\\\"latitude\\\":\\\"8.87776\\\",\\\"longitude\\\":\\\"78.13277\\\"}]\"}', '157.51.19.114', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'https://erixon.in/crm/admin/attendance/mark-self', '2026-09-23 17:39:42', '2026-09-23 17:39:42'),
(189, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '106.51.24.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'https://erixon.in/crm/admin/login', '2026-09-24 09:18:56', '2026-09-24 09:18:56'),
(190, 1, 'login', 'App\\Models\\User', 1, 'Authentication', 'User \'Ezecrm - Sundari (Admin)\' logged in successfully.', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/admin/login', '2026-09-24 03:53:33', '2026-09-24 03:53:33'),
(191, NULL, 'created', 'App\\Models\\Customer', 1141, 'Customers', 'Created Customer \'Test Unit Customer\'', NULL, '{\"customer_type\":\"user\",\"name\":\"Test Unit Customer\",\"mobile\":\"9999900001\",\"status\":1,\"lead_requirement_id\":1,\"lead_stage_id\":1,\"created_at\":\"2026-09-24T04:11:16.000000Z\",\"customer_id\":1141,\"owner_by_name\":null,\"assign_by_name\":null,\"created_by_name\":null,\"owner_name\":null,\"assign_name\":null,\"assigned_by_name\":null,\"creator_name\":null,\"lead_requirement_name\":\"Bulk Whatsapp ( Dp)\",\"lead_stage_name\":\"New\"}', '127.0.0.1', 'Symfony', 'http://localhost', '2026-09-24 04:11:16', '2026-09-24 04:11:16'),
(192, NULL, 'deleted', 'App\\Models\\Customer', 1141, 'Customers', 'Deleted Customer \'Test Unit Customer\'', '{\"customer_type\":\"user\",\"name\":\"Test Unit Customer\",\"mobile\":\"9999900001\",\"status\":1,\"lead_requirement_id\":1,\"lead_stage_id\":1,\"created_at\":\"2026-09-24T04:11:16.000000Z\",\"customer_id\":1141,\"owner_by_name\":null,\"assign_by_name\":null,\"created_by_name\":null,\"owner_name\":null,\"assign_name\":null,\"assigned_by_name\":null,\"creator_name\":null,\"lead_requirement_name\":\"Bulk Whatsapp ( Dp)\",\"lead_stage_name\":\"New\"}', NULL, '127.0.0.1', 'Symfony', 'http://localhost', '2026-09-24 04:11:16', '2026-09-24 04:11:16'),
(193, 1, 'created', 'App\\Models\\Customer', 1142, 'Customers', 'Created Customer \'dhoni\'', NULL, '{\"customer_type\":\"user\",\"name\":\"dhoni\",\"company_name\":null,\"mobile\":\"8418523621\",\"email\":null,\"alternate_mobile\":null,\"address\":null,\"city\":null,\"state\":null,\"country\":\"India\",\"pincode\":null,\"owner_by\":null,\"assign_by\":null,\"lead_requirement_id\":\"7\",\"lead_stage_id\":\"7\",\"status\":\"1\",\"custom_fields\":{\"followup_date\":null,\"sale_closed_date\":\"2026-09-24\",\"customer_created_date\":\"2026-09-17\"},\"created_by\":1,\"created_at\":\"2026-09-24T04:13:50.000000Z\",\"customer_id\":1142,\"owner_by_name\":null,\"assign_by_name\":null,\"created_by_name\":\"Ezecrm - Sundari (Admin)\",\"owner_name\":null,\"assign_name\":null,\"assigned_by_name\":null,\"creator_name\":\"Ezecrm - Sundari (Admin)\",\"lead_requirement_name\":\"Bulk Mail\",\"lead_stage_name\":\"Interested\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/admin/customers/store', '2026-09-24 04:13:50', '2026-09-24 04:13:50');

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
('spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:110:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"dashboard.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"profile.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:16:\"profile.password\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:10:\"roles.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"roles.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:10:\"roles.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"roles.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:10:\"staff.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:12:\"staff.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:10:\"staff.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"staff.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:14:\"customers.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:21:\"general-settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:21:\"general-settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:18:\"lead-settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:18:\"lead-settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:16:\"customers.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:14:\"customers.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:16:\"customers.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:17:\"lead-sources.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:19:\"lead-sources.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:17:\"lead-sources.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:19:\"lead-sources.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:10:\"leads.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:12:\"leads.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:10:\"leads.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:12:\"leads.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:16:\"lead-stages.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:18:\"lead-stages.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:16:\"lead-stages.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:18:\"lead-stages.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:22:\"lead-requirements.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:24:\"lead-requirements.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:22:\"lead-requirements.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:24:\"lead-requirements.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:17:\"lost-reasons.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:19:\"lost-reasons.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:17:\"lost-reasons.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:19:\"lost-reasons.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:14:\"followups.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:16:\"followups.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:14:\"followups.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:16:\"followups.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:18:\"followups.reassign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:11:\"staff.leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:19:\"lead-documents.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:21:\"lead-documents.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:19:\"lead-documents.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:21:\"lead-documents.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:14:\"templates.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:16:\"templates.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:14:\"templates.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:16:\"templates.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:20:\"call-recordings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:22:\"call-recordings.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:20:\"call-recordings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:22:\"call-recordings.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:15:\"attendance.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:17:\"attendance.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:15:\"attendance.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:17:\"attendance.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:23:\"attendance-reports.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:11:\"leaves.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:13:\"leaves.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:14:\"leaves.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:13:\"leaves.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:11:\"salary.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:14:\"call-logs.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:16:\"call-logs.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:14:\"call-logs.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:16:\"call-logs.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:21:\"call-log-reports.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:20:\"credit-requests.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:22:\"credit-requests.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:29:\"credit-requests.approve_admin\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:31:\"credit-requests.approve_support\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:22:\"credit-requests.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:13:\"payments.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:78;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:15:\"payments.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:79;a:4:{s:1:\"a\";i:80;s:1:\"b\";s:13:\"payments.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:80;a:4:{s:1:\"a\";i:81;s:1:\"b\";s:15:\"payments.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:81;a:4:{s:1:\"a\";i:82;s:1:\"b\";s:16:\"permissions.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:82;a:4:{s:1:\"a\";i:83;s:1:\"b\";s:18:\"permissions.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:83;a:4:{s:1:\"a\";i:84;s:1:\"b\";s:19:\"permissions.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:84;a:4:{s:1:\"a\";i:85;s:1:\"b\";s:18:\"permissions.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:85;a:4:{s:1:\"a\";i:86;s:1:\"b\";s:15:\"incentives.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:86;a:4:{s:1:\"a\";i:87;s:1:\"b\";s:17:\"incentives.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:87;a:4:{s:1:\"a\";i:88;s:1:\"b\";s:15:\"incentives.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:88;a:4:{s:1:\"a\";i:89;s:1:\"b\";s:17:\"incentives.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:89;a:4:{s:1:\"a\";i:90;s:1:\"b\";s:22:\"customer-settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:90;a:4:{s:1:\"a\";i:91;s:1:\"b\";s:22:\"customer-settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:91;a:4:{s:1:\"a\";i:92;s:1:\"b\";s:22:\"followup-settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:92;a:4:{s:1:\"a\";i:93;s:1:\"b\";s:22:\"followup-settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:93;a:4:{s:1:\"a\";i:94;s:1:\"b\";s:28:\"credit-request-settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:94;a:4:{s:1:\"a\";i:95;s:1:\"b\";s:28:\"credit-request-settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:95;a:4:{s:1:\"a\";i:96;s:1:\"b\";s:18:\"coordinations.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:96;a:4:{s:1:\"a\";i:97;s:1:\"b\";s:20:\"coordinations.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:97;a:4:{s:1:\"a\";i:98;s:1:\"b\";s:18:\"coordinations.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:98;a:4:{s:1:\"a\";i:99;s:1:\"b\";s:20:\"coordinations.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:99;a:4:{s:1:\"a\";i:100;s:1:\"b\";s:19:\"demo-processes.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:100;a:4:{s:1:\"a\";i:101;s:1:\"b\";s:21:\"demo-processes.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:101;a:4:{s:1:\"a\";i:102;s:1:\"b\";s:19:\"demo-processes.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:102;a:4:{s:1:\"a\";i:103;s:1:\"b\";s:21:\"demo-processes.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:103;a:4:{s:1:\"a\";i:104;s:1:\"b\";s:21:\"demo-processes.assign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:104;a:4:{s:1:\"a\";i:105;s:1:\"b\";s:26:\"demo-process-settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:105;a:4:{s:1:\"a\";i:106;s:1:\"b\";s:26:\"demo-process-settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:106;a:4:{s:1:\"a\";i:107;s:1:\"b\";s:13:\"salary.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:107;a:4:{s:1:\"a\";i:108;s:1:\"b\";s:11:\"salary.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:108;a:4:{s:1:\"a\";i:109;s:1:\"b\";s:13:\"salary.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:109;a:4:{s:1:\"a\";i:110;s:1:\"b\";s:15:\"audit-logs.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:5:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"manager\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"support\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:10:\"sales team\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"Product Manager\";s:1:\"c\";s:3:\"web\";}}}', 1790309539);

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
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `followup_id` bigint(20) UNSIGNED DEFAULT NULL,
  `followup_code` varchar(50) DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(30) NOT NULL,
  `call_type` varchar(50) DEFAULT 'Outbound',
  `call_source` varchar(50) DEFAULT 'direct',
  `duration` varchar(100) DEFAULT NULL,
  `call_start_time` datetime DEFAULT NULL,
  `call_end_time` datetime DEFAULT NULL,
  `call_status` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `recording_id` bigint(20) UNSIGNED DEFAULT NULL,
  `recording_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `call_logs`
--

INSERT INTO `call_logs` (`call_id`, `lead_id`, `customer_id`, `customer_code`, `customer_name`, `followup_id`, `followup_code`, `followup_date`, `user_id`, `phone`, `call_type`, `call_source`, `duration`, `call_start_time`, `call_end_time`, `call_status`, `notes`, `recording_id`, `recording_file`, `created_at`) VALUES
(1, 10, 1126, 'CUST_1126', 'Muthu', NULL, NULL, NULL, 2, '7604931311', 'Outbound', 'Lead', '00:05', '2026-09-17 15:05:12', '2026-09-17 15:05:24', 'Answered', NULL, 1, 'uploads/call_recordings/1789637737_sys_call_7604931311_20260917_150526.m4a', '2026-09-17 15:05:12'),
(2, 10, 1126, 'CUST_1126', 'Muthu', NULL, NULL, NULL, 2, '7604931311', 'Outbound', 'Customer', '00:56', '2026-09-17 15:06:02', '2026-09-17 15:06:59', 'Answered', 'test call', 2, 'uploads/call_recordings/1789637856_call_20260917_150602.m4a', '2026-09-17 15:06:02'),
(3, 10, 1126, 'CUST_1126', 'Muthu', NULL, NULL, NULL, 2, '7604931311', 'Outbound', 'Lead', '00:05', '2026-09-17 15:05:46', '2026-09-17 15:06:31', 'Answered', NULL, 3, 'uploads/call_recordings/1789637892_sys_call_7604931311_20260917_150632.m4a', '2026-09-17 15:05:46'),
(4, 10, 1126, 'CUST_1126', 'Muthu', 1, 'FOL_1', '2026-09-18', 2, '7604931311', 'Outbound', 'Customer', '00:01', '2026-09-17 15:11:04', '2026-09-17 15:11:19', 'Answered', NULL, 4, 'uploads/call_recordings/1789638089_sys_call_7604931311_20260917_151120.m4a', '2026-09-17 15:11:04'),
(5, 10, 1126, 'CUST_1126', 'Muthu', 1, 'FOL_1', '2026-09-18', 2, '7604931311', 'Outbound', 'Customer', '00:03', '2026-09-17 15:14:08', '2026-09-17 15:14:15', 'Answered', NULL, 5, 'uploads/call_recordings/1789638258_sys_call_7604931311_20260917_151416.m4a', '2026-09-17 15:14:08'),
(7, 10, 1126, 'CUST_1126', 'Muthu', 2, 'FOL_2', '2026-09-26', 2, '7604931311', 'Outbound', 'Followup', '00:44', '2026-09-18 15:08:08', '2026-09-18 15:08:52', 'Answered', NULL, 7, 'uploads/call_recordings/1789724366_call_20260918_150808.wav', '2026-09-18 15:08:08');

-- --------------------------------------------------------

--
-- Table structure for table `call_recordings`
--

CREATE TABLE `call_recordings` (
  `call_id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
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

INSERT INTO `call_recordings` (`call_id`, `lead_id`, `customer_id`, `recording_file`, `duration`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 10, 1126, 'uploads/call_recordings/1789637737_sys_call_7604931311_20260917_150526.m4a', '00:05', 2, '2026-09-17 15:05:37', '2026-09-17 15:05:37', NULL),
(2, 10, 1126, 'uploads/call_recordings/1789637856_call_20260917_150602.m4a', '00:56', 2, '2026-09-17 15:07:36', '2026-09-17 15:07:36', NULL),
(3, 10, 1126, 'uploads/call_recordings/1789637892_sys_call_7604931311_20260917_150632.m4a', '00:05', 2, '2026-09-17 15:08:12', '2026-09-17 15:08:12', NULL),
(4, 10, 1126, 'uploads/call_recordings/1789638089_sys_call_7604931311_20260917_151120.m4a', '00:01', 2, '2026-09-17 15:11:29', '2026-09-17 15:11:29', NULL),
(5, 10, 1126, 'uploads/call_recordings/1789638258_sys_call_7604931311_20260917_151416.m4a', '00:03', 2, '2026-09-17 15:14:18', '2026-09-17 15:14:18', NULL),
(6, NULL, 1, 'uploads/call_recordings/1789638787_sys_call_9489042085_20260917_152256.m4a', '01:08', 2, '2026-09-17 15:23:07', '2026-09-17 15:23:07', NULL),
(7, 10, 1126, 'uploads/call_recordings/1789724366_call_20260918_150808.wav', '00:44', 2, '2026-09-18 15:09:26', '2026-09-18 15:09:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coordinations`
--

CREATE TABLE `coordinations` (
  `coordination_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `link` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coordinations`
--

INSERT INTO `coordinations` (`coordination_id`, `title`, `staff_id`, `link`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Sprint Planning Sync', 1, 'https://meet.google.com/ksj-tvbv-pyq', 1, '2026-09-03 12:35:52', '2026-09-15 16:39:04', NULL),
(2, 'test', 1, 'https://meet.google.com/ksj-tvbv-pyq', 1, '2026-09-15 16:44:00', '2026-09-15 16:44:21', '2026-09-15 16:44:21'),
(3, 'bulk sms demo', 12, 'https://meet.google.com/ame-ffzq-qct', 12, '2026-09-16 13:20:40', '2026-09-16 13:20:40', NULL),
(4, 'demo Notification', 1, 'https://meet.google.com/ksj-tvbv-pyq', 1, '2026-09-18 12:41:30', '2026-09-18 12:41:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coordination_joining_staff`
--

CREATE TABLE `coordination_joining_staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `coordination_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('Pending','Joined') NOT NULL DEFAULT 'Pending',
  `joined_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coordination_joining_staff`
--

INSERT INTO `coordination_joining_staff` (`id`, `coordination_id`, `user_id`, `status`, `joined_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Joined', '2026-09-04 17:40:12', '2026-09-03 12:35:52', '2026-09-15 16:39:04'),
(2, 1, 2, 'Joined', '2026-09-03 12:36:41', '2026-09-03 12:35:52', '2026-09-15 16:39:04'),
(3, 2, 1, 'Pending', NULL, '2026-09-15 16:44:00', '2026-09-15 16:44:13'),
(4, 2, 3, 'Pending', NULL, '2026-09-15 16:44:00', '2026-09-15 16:44:13'),
(5, 3, 5, 'Pending', NULL, '2026-09-16 13:20:40', '2026-09-16 13:20:40'),
(6, 3, 12, 'Joined', '2026-09-16 13:21:23', '2026-09-16 13:20:40', '2026-09-16 13:21:23'),
(7, 3, 6, 'Pending', NULL, '2026-09-16 13:20:40', '2026-09-16 13:20:40'),
(8, 4, 1, 'Pending', NULL, '2026-09-18 12:41:30', '2026-09-18 12:41:30'),
(9, 4, 3, 'Pending', NULL, '2026-09-18 12:41:30', '2026-09-18 12:41:30'),
(10, 4, 2, 'Pending', NULL, '2026-09-18 12:41:30', '2026-09-18 12:41:30');

-- --------------------------------------------------------

--
-- Table structure for table `credit_requests`
--

CREATE TABLE `credit_requests` (
  `credit_request_id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_source_id` bigint(20) DEFAULT NULL,
  `lead_requirement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `credit_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_estimate` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(100) NOT NULL DEFAULT 'Pending Admin Approval',
  `admin_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_approved_at` timestamp NULL DEFAULT NULL,
  `admin_remarks` text DEFAULT NULL,
  `support_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `support_approved_at` timestamp NULL DEFAULT NULL,
  `support_remarks` text DEFAULT NULL,
  `requested_by` bigint(20) UNSIGNED DEFAULT NULL,
  `custom_fields` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `credit_requests`
--

INSERT INTO `credit_requests` (`credit_request_id`, `lead_id`, `lead_source_id`, `lead_requirement_id`, `customer_id`, `phone`, `email`, `credit_amount`, `is_estimate`, `status`, `admin_approved_by`, `admin_approved_at`, `admin_remarks`, `support_approved_by`, `support_approved_at`, `support_remarks`, `requested_by`, `custom_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 1, NULL, 791, '2707', 'anand@sparkalerts.in', 15000.00, 0, 'Pending Admin Approval', NULL, NULL, NULL, NULL, NULL, NULL, 1, '[]', '2026-09-10 15:36:44', '2026-09-10 16:06:32', '2026-09-10 16:06:32'),
(2, NULL, 3, 7, 90, '348', 'loomsnweaves@gmail.com', 65000.00, 1, 'Pending Admin Approval', NULL, NULL, NULL, NULL, NULL, NULL, 1, '[]', '2026-09-10 16:06:50', '2026-09-10 16:07:01', NULL),
(3, NULL, NULL, NULL, 1130, '8523694528', 'ajith@example.com', 0.00, 0, 'Credit Added', 1, '2026-09-23 11:52:13', NULL, 2, '2026-09-23 12:23:13', 'ok', 2, NULL, '2026-09-11 12:29:02', '2026-09-23 12:23:13', NULL),
(4, NULL, NULL, NULL, 1130, '8523694528', 'ajith@example.com', 0.00, 0, 'Forwarded to Product Manager', 1, '2026-09-23 11:14:31', NULL, NULL, NULL, NULL, 2, NULL, '2026-09-11 12:29:15', '2026-09-23 11:14:31', NULL),
(5, NULL, NULL, NULL, 1127, '9879543258', 'rohit@gmail.com', 20000.00, 0, 'Forwarded to Support', 1, '2026-09-15 12:01:57', NULL, NULL, NULL, NULL, 2, NULL, '2026-09-12 11:28:59', '2026-09-15 12:01:57', NULL),
(6, NULL, 1, 6, 1130, '8523694528', 'ajith@example.com', 5900.00, 0, 'Credit Added', 1, '2026-09-16 13:28:56', 'payment received', 5, '2026-09-16 13:29:38', '25k sms added', 6, '[]', '2026-09-16 13:25:32', '2026-09-16 13:29:38', NULL),
(7, NULL, 2, 4, 791, '2707', 'anand@sparkalerts.in', 20000.00, 0, 'Forwarded to Product Manager', 1, '2026-09-18 12:00:14', 'testing', NULL, NULL, NULL, 1, '{\"username\":\"test\"}', '2026-09-18 11:59:48', '2026-09-18 12:00:14', NULL),
(8, NULL, 2, 3, 1, '9876543210', 'john@example.com', 5000.00, 0, 'Rejected', NULL, NULL, 'reject', NULL, NULL, NULL, 2, '{\"username\":\"john_doe\"}', '2026-09-23 11:20:01', '2026-09-23 11:50:57', NULL),
(9, NULL, 6, 4, 1124, '8523694591', 'rohit@gmail.com', 5000.00, 1, 'Rejected', NULL, NULL, 'reject', NULL, NULL, NULL, 2, '{\"username\":\"mmmm\"}', '2026-09-23 12:05:25', '2026-09-23 12:06:06', NULL),
(10, NULL, 6, 6, 791, '2707', 'anand@sparkalerts.in', 5900.00, 0, 'Credit Added', 1, '2026-09-23 14:26:27', NULL, 5, '2026-09-23 14:31:17', '25000 credit added', 1, '[]', '2026-09-23 14:26:10', '2026-09-23 14:31:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `credit_request_custom_fields`
--

CREATE TABLE `credit_request_custom_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(255) NOT NULL,
  `field_options` text DEFAULT NULL,
  `is_required` varchar(255) NOT NULL DEFAULT 'No',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `owner_by` bigint(20) DEFAULT NULL,
  `assign_by` bigint(20) DEFAULT NULL,
  `lead_requirement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `credit_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `password` varchar(255) DEFAULT NULL,
  `reference_code` varchar(255) DEFAULT NULL,
  `custom_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_fields`)),
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_type`, `name`, `company_name`, `mobile`, `email`, `alternate_mobile`, `address`, `city`, `state`, `country`, `pincode`, `owner_by`, `assign_by`, `lead_requirement_id`, `lead_stage_id`, `created_by`, `status`, `credit_balance`, `password`, `reference_code`, `custom_fields`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'user', 'ajis', 'site studio', '9489042085', 'ajis@gmail.com', NULL, NULL, 'madurai', 'Taminadu', 'India', NULL, NULL, NULL, NULL, NULL, 2, 0, 1500.00, NULL, NULL, NULL, NULL, '2026-08-13 01:47:54', '2026-09-10 15:52:03', NULL),
(2, 'user', 'ajis', 'abc', '8521239632', NULL, NULL, NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, 2, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-18 05:21:07', '2026-09-10 15:51:48', '2026-09-10 15:51:48'),
(3, 'user', 'deepika', 'abc', '9653247890', NULL, NULL, NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, 4, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-21 06:46:17', '2026-08-21 06:46:17', NULL),
(4, 'user', 'John Doe', 'ABC Enterprises', '9876543210', 'john@example.com', '9876543211', '123 Main St', 'Chennai', 'Tamil Nadu', 'India', '600001', NULL, NULL, NULL, NULL, 1, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-21 10:59:47', '2026-09-23 13:30:20', NULL),
(5, 'reseller', 'Jane Smith', 'Global Resellers', '9123456789', 'jane@example.com', NULL, '456 Tech Park', 'Bangalore', 'Karnataka', 'India', '560001', NULL, NULL, NULL, NULL, 1, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-21 10:59:47', '2026-08-21 10:59:47', NULL),
(6, 'user', '1', '8086888830', '1', 'info@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:14', '2026-08-24 09:01:14', NULL),
(8, 'user', '5', '9895555215', '6', 'info@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:14', '2026-08-24 09:01:14', NULL),
(9, 'user', '12', '9447770700', '142', 'mookambikatravelstvm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(10, 'user', '31', '9847156536', '145', 'jayantvm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(11, 'user', '40', '9895567788', '117', 'srimulamclub@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(12, 'user', '45', '9847042520', '121', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(13, 'user', '72', '9746426954', '166', 'informharris@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(14, 'user', '101', '9176040346', '178', 'anand@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(15, 'user', '119', '9847042520', '102', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(16, 'user', '137', '8089018830', '175', 'info@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(17, 'user', '146', '7736000601', '187', 'bijusnair1331@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(18, 'user', '155', '9846992220', '111', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(19, 'user', '158', '919810775881', '71', 'rajukumar83@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(20, 'user', '221', '9745967040', '135', 'jeringeogygeorge@amaljyothi.ac.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(21, 'user', '311', '9500129885', '125', 'smartbees.raja@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(22, 'user', '337', '9404990330', '144', 'shivain380@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(23, 'user', '358', '9847042520', '133', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(24, 'user', '359', '9745647400', '77', 'sangeethk7@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(25, 'user', '401', '9447470783', '219', 'sundarmelayil@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(26, 'user', '453', '9846433335', '295', 'tvmglobers6699@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(27, 'user', '469', '9847042520', '270', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(28, 'user', '490', '9526038788', '288', 'aqsakerala@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(29, 'user', '492', '9745196958', '92', 'nisha_arafa@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(30, 'user', '523', '9847042520', '245', 'info@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(31, 'user', '524', '9995601040', '243', 'vysaka.mailme@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(32, 'user', '525', '9847143202', '15', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(33, 'user', '527', '9633807974', '47', 'anju@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(34, 'user', '532', '9847042520', '224', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(35, 'user', '533', '9847042520', '83', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(36, 'user', '537', '9633799944', '56', 'iamt.adv@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(37, 'user', '544', '9995349361', '189', 'adeeb2358@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(38, 'user', '547', '8590208141', '240', 'hr@sundry.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(39, 'user', '548', '9539948441', '237', 'hr@sundry.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(40, 'user', '549', '8590208141', '239', 'hr@sundry.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(41, 'user', '550', '9995218441', '238', 'hr@sundry.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(42, 'user', '551', '9956478990', '225', 'asjda.asda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(43, 'user', '553', '9847042520', '216', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(44, 'user', '555', '9847042520', '218', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(45, 'user', '557', '9605060414', '106', 'hotelcitypalace@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(46, 'user', '559', '9567266644', '97', 'pioneerbajaj@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(47, 'user', '564', '9994333000', '236', 'jmanoj@suxusmobiles.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(48, 'user', '573', '9847042520', '194', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(49, 'user', '602', '9847042520', '203', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(50, 'user', '611', '9847042520', '271', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(51, 'user', '614', '9847042520', '303', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(52, 'user', '626', '9847042520', '322', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(53, 'user', '651', '9847042520', '317', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(54, 'user', '654', '9847042520', '305', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(55, 'user', '670', '9443404030', '351', 'kingmobiles567@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(56, 'user', '682', '9847042520', '337', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(57, 'user', '693', '9847042520', '207', 'support@alvo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(58, 'user', '696', '9995551398', '152', 'mrapalayam@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(59, 'user', '714', '7094475605', '304', 'sgjbikes@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(60, 'user', '722', '8089048830', '371', 'sales@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(61, 'user', '736', '9846992220', '193', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(62, 'user', '761', '9846992220', '113', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(63, 'user', '769', '9446804372', '385', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(64, 'user', '771', '9597484748', '392', 'er.sethupathy@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(65, 'user', '774', '9447541741', '139', 'chithran.karikkineth@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(66, 'user', '786', '9544550760', '390', 'admin@foresightgroup.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(67, 'user', '794', '9447066565', '402', 'ravii2000@yahoo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(68, 'user', '801', '9946130462', '346', 'rajeshsaraswathy7@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(69, 'user', '835', '9605443891', '64', 'vipin@gias.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(70, 'user', '845', '9003944611', '431', 'info@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(71, 'user', '867', '9447048248', '427', 'info@pearlxp.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(72, 'user', '868', '8089553808', '435', 'info@pearlxp.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(73, 'user', '873', '9633665599', '420', 'rkutty1729@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(74, 'user', '885', '9003485888', '434', 'bharathson@tvsts.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(75, 'user', '896', '9846992220', '383', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(76, 'user', '902', '9847161261', '404', 'binoj@foresightgroup.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(77, 'user', '912', '9846992220', '448', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(78, 'user', '916', '9846992220', '381', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(79, 'user', '923', '9447062148', '445', 'utekatl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(80, 'user', '926', '9446904697', '415', 'lajeeshk@sarobal.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(81, 'user', '933', '9447135050', '441', 'drkannan123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(82, 'user', '934', '9847041060', '396', 'jm@kuruvithadam.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(83, 'user', '937', '9446550504', '182', 'ugitvm@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(84, 'user', '947', '9215056089', '469', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(85, 'user', '958', '9846992220', '382', 'meera@techonz.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(86, 'user', '961', '9846992220', '424', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(87, 'user', '975', '9846992220', '478', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(88, 'user', '982', '9626612339', '433', 'ArunKumar.k@tvsts.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(89, 'user', '990', '9446920517', '278', 'royalktym@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(90, 'user', '992', '9946807500', '348', 'loomsnweaves@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(91, 'user', '999', '9846992220', '467', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(92, 'user', '1002', '9215056089', '470', 'dynamicinfoservices@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(93, 'user', '1008', '9600248421', '141', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(94, 'user', '1039', '9496050055', '464', 'rajendran.agg@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(95, 'user', '1059', '9895985911', '89', 'vighnesh.b@jobsnta.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(96, 'user', '1109', '8089018830', '181', 'techsupport@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(97, 'user', '1114', '9842684405', '472', 'balamuruga.sales@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(98, 'user', '1124', '9600248421', '524', 'manikandan@buson.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(99, 'user', '1144', '9633147432', '545', 'radhakrishnanpura@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(100, 'user', '1197', '9349924919', '504', 'apollo_tvm@yahoo.co.uk', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(101, 'user', '1220', '9894668278', '577', 'aalwarscreen@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(102, 'user', '1241', '9846992220', '561', 'prashanth@techonz.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(103, 'user', '1247', '9894505005', '597', 'dungarchand@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(104, 'user', '1256', '9600248421', '509', 'honda.dalton@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(105, 'user', '1312', '9994042032', '457', 'info@vsitsindia.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(106, 'user', '1338', '9400339696', '230', 'balu@magbtunes.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(107, 'user', '1345', '9447182101', '632', 'shajisena@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(108, 'user', '1358', '9500129885', '131', 'essakkieha.87@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(109, 'user', '1362', '9944021428', '430', 'routes.raja@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(110, 'user', '1406', '9645553466', '253', 'sreeramsudhan@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(111, 'user', '1423', '9842514966', '318', 'mariomotorsrjpm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(112, 'user', '1434', '9846992220', '627', 'prashanth@techonz.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(113, 'user', '1453', '9447135050', '428', 'drkannan123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(114, 'user', '1459', '9791341963', '626', 'manojin1991@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(115, 'user', '1460', '9488323667', '675', 'ashif.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(116, 'user', '1472', '9446705551', '72', 'sopanamitmission@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(117, 'user', '1487', '9746686869', '685', 'akhil@foresightgroup.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(118, 'user', '1512', '9846992220', '592', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(119, 'user', '1531', '9597568363', '650', 'ajithkumaran072@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(120, 'user', '1558', '9894926531', '713', 'stantonytvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(121, 'user', '1561', '9944472081', '692', 'deepaminnovates@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(122, 'user', '1569', '9894205274', '500', 'vivekamatricschool@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(123, 'user', '1572', '9500525868', '502', 'twillstuticorin@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(124, 'user', '1576', '7373732619', '641', 'saralamcoo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(125, 'user', '1583', '9842520338', '644', 'tajjewels@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(126, 'user', '1609', '8893535300', '541', 'davidindia204@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(127, 'user', '1613', '9846992220', '737', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(128, 'user', '1638', '7598557755', '688', 'butterfliesapm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(129, 'user', '1658', '9894499502', '501', 'sgopinath@consolidatedgroup.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(130, 'user', '1680', '9846992220', '665', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(131, 'user', '1694', '9842774520', '765', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(132, 'user', '1697', '9446034201', '484', 'director@igmt.org', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(133, 'user', '1704', '9600248421', '543', 'muthiah.sanjay@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(134, 'user', '1722', '9942388099', '604', 'kknanban@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(135, 'user', '1746', '9750603406', '609', 'routes.nkl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(136, 'user', '1759', '9846992220', '647', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(137, 'user', '1763', '8589031888', '773', 'diyamobikes@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(138, 'user', '1776', '9215056089', '471', 'dynamicinfoservices@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(139, 'user', '1783', '9600324075', '788', 'antsolution046@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(140, 'user', '1789', '9626612339', '633', 'smartbeesdata@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(141, 'user', '1795', '9600486762', '610', 'routeseduins@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(142, 'user', '1846', '9994724449', '809', 'chellakuttymdu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(143, 'user', '1851', '9600248421', '515', 'kingmobiles567@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(144, 'user', '1862', '7667222777', '719', 'paramasivachandra@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(145, 'user', '1876', '9942852100', '815', 'stgroupvasu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(146, 'user', '1883', '9244308231', '821', 't.jothimaniyan@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(147, 'user', '1896', '9842391647', '757', 'manojin1991@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(148, 'user', '1897', '8903939390', '746', 'ashif.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(149, 'user', '1898', '9566642666', '782', 'ashif.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(150, 'user', '1899', '9715525743', '716', 'shaju@picpaisa.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(151, 'user', '1901', '9787633048', '673', 'mubarakhotels@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(152, 'user', '1902', '8012528000', '666', 'ashif.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(153, 'user', '1903', '7598557755', '687', 'butterfliesapm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(154, 'user', '1904', '9443554210', '761', 'prabhuvs111@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(155, 'user', '1906', '9443500992', '779', 'basith.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(156, 'user', '1908', '9443149191', '749', 'alikhan.tos@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(157, 'user', '1910', '9846992220', '321', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(158, 'user', '1911', '9600248421', '764', 'rajanandco@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(159, 'user', '1913', '9842181878', '780', 'anbutravels2010@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(160, 'user', '1925', '9626612339', '820', 'smartbeesdata@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(161, 'user', '1932', '9746686869', '615', 'akhil@foresightgroup.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(162, 'user', '1933', '9961632905', '403', 'foresight.foresight@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(163, 'user', '1949', '8903170298', '846', 'basith.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(164, 'user', '1956', '8907950862', '841', 'alvinodkumar@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(165, 'user', '1966', '9446197209', '582', 'aviesed@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(166, 'user', '1970', '9894615546', '851', 'rcnr2016@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(167, 'user', '1993', '9994236985', '861', 'kaboor123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(168, 'user', '2000', '9047033408', '655', 'essensuals.madurai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(169, 'user', '2018', '9600248421', '812', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(170, 'user', '2019', '9846992220', '845', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(171, 'user', '2047', '9600324075', '796', 'antsolution046@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(172, 'user', '2074', '9042351735', '774', 'v.msolutions@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(173, 'user', '2078', '9842391647', '882', 'manojin1991@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(174, 'user', '2079', '9842391647', '884', 'basith.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(175, 'user', '2081', '9715399922', '803', 'svavarsonsyamaha@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(176, 'user', '2101', '9789308131', '881', 'info@sangvish.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(177, 'user', '2112', '9965595956', '896', 'ssbsys@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(178, 'user', '2123', '9387817747', '606', 'technoworlditstore@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(179, 'user', '2131', '9865199621', '888', 'muthiahspark@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(180, 'user', '2140', '9790950111', '714', 'contact@broadmindgroup.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(181, 'user', '2182', '8220005614', '554', 'sgjbikes@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(182, 'user', '2202', '9947042131', '517', 'sripadmamgroup@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(183, 'user', '2227', '7402424810', '935', 'merlinselva93@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(184, 'user', '2245', '9215056089', '920', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(185, 'user', '2248', '9567549999', '941', 'raj_itiin@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(186, 'user', '2253', '9215056089', '889', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(187, 'user', '2256', '9443450506', '949', 'sheejassaleem@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(188, 'user', '2259', '9215056089', '691', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(189, 'user', '2260', '9215056089', '898', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(190, 'user', '2264', '9215056089', '944', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(191, 'user', '2278', '9789566684', '516', 'muthiah.sanjay@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(192, 'user', '2315', '9894304652', '954', 'sriganeshstorengl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(193, 'user', '2324', '9791885020', '601', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(194, 'user', '2327', '9094777723', '726', 'admin@z3infotech.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(195, 'user', '2339', '9843155338', '974', 'suchhboutique@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(196, 'user', '2356', '9751933933', '978', 'sriselvalakshmichitfunds@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(197, 'user', '2357', '9846992220', '696', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(198, 'user', '2395', '9446100131', '1000', 'kssunillic@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(199, 'user', '2412', '9788125125', '1008', 'itechacademy@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(200, 'user', '2419', '8903170929', '1009', 'manojinanxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(201, 'user', '2424', '8525881570', '981', 'arunabc181@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(202, 'user', '2441', '9600248421', '754', 'manikandan@buson.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(203, 'user', '2445', '9698585000', '579', 'mmxtvl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(204, 'user', '2452', '9215056089', '897', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(205, 'user', '2466', '9944434456', '877', 'smfh.tvl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(206, 'user', '2483', '9215056089', '945', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(207, 'user', '2496', '9215056089', '948', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(208, 'user', '2508', '9843155338', '1001', 'suchhboutique@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(209, 'user', '2549', '9790504042', '972', 'pkashwin93@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(210, 'user', '2561', '9447781984', '1052', 'its@uds.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(211, 'user', '2570', '9489788801', '998', 'artenterprises8801@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(212, 'user', '2581', '9443234752', '895', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(213, 'user', '2594', '9562411899', '1053', 'sarath.gs@herculessuperbazar.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(214, 'user', '2647', '9894615546', '905', 'smartbeesdata@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(215, 'user', '2652', '9842774520', '1011', 'royalsysytems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(216, 'user', '2656', '9443808849', '1077', 'silversuresh925@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(217, 'user', '2658', '9094777723', '1044', 'sales@z3infotech.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(218, 'user', '2673', '9996399399', '1071', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(219, 'user', '2700', '9092515553', '915', 'msaraglc@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(220, 'user', '2762', '9215056089', '1039', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(221, 'user', '2788', '9846992220', '690', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(222, 'user', '2796', '9994333000', '533', 'jmanoj@suxusmobiles.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(223, 'user', '2800', '9843651979', '990', 'vlhosting.in@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(224, 'user', '2814', '9715113333', '1134', 'kiscol@zthree.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(225, 'user', '2848', '9349773663', '1126', 'cyibersoft@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(226, 'user', '2856', '9442244966', '950', 'mgsiva66@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(227, 'user', '2913', '9842514966', '827', 'mariohondaservice@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(228, 'user', '2918', '9996399399', '1079', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(229, 'user', '2919', '9996399399', '1080', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(230, 'user', '2920', '9996399399', '1082', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(231, 'user', '2921', '9996399399', '1084', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(232, 'user', '2922', '9996399399', '1085', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL);
INSERT INTO `customers` (`customer_id`, `customer_type`, `name`, `company_name`, `mobile`, `email`, `alternate_mobile`, `address`, `city`, `state`, `country`, `pincode`, `owner_by`, `assign_by`, `lead_requirement_id`, `lead_stage_id`, `created_by`, `status`, `credit_balance`, `password`, `reference_code`, `custom_fields`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(233, 'user', '2923', '9996399399', '1086', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(234, 'user', '2924', '9996399399', '1087', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(235, 'user', '2925', '9996399399', '1088', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(236, 'user', '2926', '9996399399', '1089', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(237, 'user', '2927', '9996399399', '1090', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(238, 'user', '2928', '9996399399', '1091', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(239, 'user', '2929', '9996399399', '1095', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(240, 'user', '2930', '9996399399', '1096', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(241, 'user', '2931', '9996399399', '1097', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(242, 'user', '2932', '9996399399', '1109', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(243, 'user', '2933', '9996399399', '1099', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(244, 'user', '2934', '9996399399', '1107', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(245, 'user', '2935', '9996399399', '1101', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(246, 'user', '2936', '9996399399', '1100', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(247, 'user', '2937', '9996399399', '1078', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(248, 'user', '2938', '9996399399', '1083', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(249, 'user', '2949', '9842376111', '1010', 'bookings@hotelchenthurpark.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(250, 'user', '2996', '7373044520', '1074', 'nellaisystems@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(251, 'user', '2999', '8220099023', '1147', 'mariviswanath.m@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(252, 'user', '3000', '9600248421', '711', 'ananthameerajewellers@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(253, 'user', '3018', '9942276669', '967', 'srl.kannanjewel@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(254, 'user', '3037', '9566929298', '1029', 'rafeekplanetn@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(255, 'user', '3045', '8606934215', '1129', 'vipinraj@visionhonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(256, 'user', '3054', '7550324552', '899', 'routes.raja@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(257, 'user', '3069', '9030513404', '625', 'mummydaddyrjy@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(258, 'user', '3070', '9994333000', '495', 'jmanoj@suxusmobiles.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(259, 'user', '3086', '9626605566', '1178', 'vsgarun@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(260, 'user', '3087', '9965416595', '1221', 'apsganapathy15@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(261, 'user', '3091', '8903312233', '705', 'idealbharathi@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(262, 'user', '3095', '9205523554', '1219', 'avoncoders@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(263, 'user', '3105', '9996399399', '1217', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(264, 'user', '3150', '9996399399', '1092', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(265, 'user', '3157', '9996399399', '1103', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:15', '2026-08-24 09:01:15', NULL),
(266, 'user', '3205', '9750672720', '1238', 'athi@sansoftsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(267, 'user', '3216', '9996399399', '1237', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(268, 'user', '3225', '9578218187', '1235', 'support@paarijaatham.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(269, 'user', '3227', '9996399399', '1229', 'arora_pankaj@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(270, 'user', '3255', '9788700459', '982', 'bhservice@bharathgroup.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(271, 'user', '3269', '9443167405', '1241', 'velsoft.tuty@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(272, 'user', '3286', '9443110299', '1263', 'kamalahasanjewellers@yahoo.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(273, 'user', '3288', '9600248421', '1244', 'balasankacars@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(274, 'user', '3291', '7373062958', '1255', 'service@ishanahonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(275, 'user', '3305', '8754000495', '1233', 'rrm@rajinimandram.org', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(276, 'user', '3315', '9600248421', '1248', 'coollifemm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(277, 'user', '3322', '9994444342', '973', 'designer@kmchospital.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(278, 'user', '3326', '9961868847', '1273', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(279, 'user', '3329', '9215056089', '1272', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(280, 'user', '3341', '9597414404', '1245', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(281, 'user', '3361', '9842812131', '1251', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(282, 'user', '3363', '9842774520', '1006', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(283, 'user', '3367', '9865037640', '1280', 'Smjvishal@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(284, 'user', '3402', '9215056089', '1064', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(285, 'user', '3408', '8129380555', '1261', 'sm@cbchonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(286, 'user', '3462', '9442642563', '1265', 'rbalajibangaru@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(287, 'user', '3464', '9600248421', '1309', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(288, 'user', '3486', '8089553808', '1296', 'info@pearlxp.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(289, 'user', '3491', '7502837000', '778', 'harithahondamdu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(290, 'user', '3502', '9526364415', '1318', 'nestofresh@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(291, 'user', '3550', '9994143110', '1283', 'makstimber@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(292, 'user', '3558', '9003361159', '1308', 'dev231115@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(293, 'user', '3568', '9842774520', '1266', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(294, 'user', '3573', '9847000003', '177', 'suresh@zeeqmobiles.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(295, 'user', '3577', '8593001118', '1345', 'techsupport@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(296, 'user', '3598', '9349947721', '951', 'nadirshanraahath@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(297, 'user', '3618', '9047066780', '894', 'vms.sfashion@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(298, 'user', '3630', '9871987636', '1288', 'avoncoders@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(299, 'user', '3653', '9003679996', '519', 'vignesh.murugiah@svccollege.ac.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(300, 'user', '3680', '9215056089', '1305', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(301, 'user', '3693', '8148866870', '635', 'kanda@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(302, 'user', '3695', '9842372661', '652', 'srishunmugamjewellers@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(303, 'user', '3699', '8610845730', '1350', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(304, 'user', '3700', '9600248421', '1327', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(305, 'user', '3702', '9600248421', '1291', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(306, 'user', '3724', '9566808951', '1373', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(307, 'user', '3769', '9842514966', '1388', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(308, 'user', '3782', '9567899499', '1060', 'pioneerbajajservice@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(309, 'user', '3790', '9205523554', '1223', 'avoncoders@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(310, 'user', '3792', '8848577707', '1277', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(311, 'user', '3802', '9746686869', '1020', 'akhil@foresightgroup.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(312, 'user', '3804', '9961632905', '686', 'foresight.foresight@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(313, 'user', '3805', '9999999999', '689', 'kiran@g.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(314, 'user', '3820', '9751697101', '1025', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(315, 'user', '3839', '9842156157', '1399', 'vetri@vetrimatrimony.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(316, 'user', '3853', '9072332121', '1355', 'INFO@SMARTIPS.IN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(317, 'user', '3854', '9446920318', '1384', 'manathraboby@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(318, 'user', '3862', '9443348865', '639', 'amutha916@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(319, 'user', '3874', '9841104030', '1410', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(320, 'user', '3901', '9994677344', '833', 'btqten@titan.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(321, 'user', '3903', '9585544400', '867', 'essensuals.thanjavur@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(322, 'user', '3907', '9994333000', '494', 'jmanoj@suxusmobiles.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(323, 'user', '3917', '9745000495', '1411', 'info@paradisegroups.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(324, 'user', '3919', '9629947488', '1419', 'karthick12636@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(325, 'user', '3928', '9500373061', '1385', 'hbbthamil@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(326, 'user', '3943', '9715613486', '1403', 'nstsilks@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(327, 'user', '3988', '9751010248', '1228', 'srikabimarketing@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(328, 'user', '3994', '9788860150', '843', 'asacollege366@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(329, 'user', '3996', '7708232321', '588', 'mahesh@nammamobiles.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(330, 'user', '4005', '9600248421', '1258', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(331, 'user', '4074', '9750925800', '1448', 'ram.avmgrps@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(332, 'user', '4118', '9600248421', '1451', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(333, 'user', '4130', '9745886747', '1454', 'jayeshjayachandran@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(334, 'user', '4140', '9961490536', '1396', 'letterlandschool03@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(335, 'user', '4141', '9943042544', '453', 'principal@ssvcollege.ac.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(336, 'user', '4142', '9495261456', '398', 'brightlanddiscoveryschool@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(337, 'user', '4163', '9600248421', '1452', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(338, 'user', '4168', '9486171329', '1460', 'albenjoel_mano@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(339, 'user', '4180', '9842513377', '1161', 'balasankamotor@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(340, 'user', '4255', '8592988830', '1310', 'techsupport@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(341, 'user', '4259', '9843111922', '1058', 'info@k4tourworld.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(342, 'user', '4274', '9884048282', '1478', 'arunulaga85@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(343, 'user', '4277', '9626605566', '1447', 'vsgarun@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(344, 'user', '4279', '9600248421', '875', 'vsr.inter.school@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(345, 'user', '4353', '99420026800', '1496', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(346, 'user', '4356', '7811940809', '1475', 'lifestyle7788@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(347, 'user', '4412', '9656412221', '1070', 'smholidaysalp@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(348, 'user', '4413', '9447811618', '767', 'vaisakhvam@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(349, 'user', '4422', '9215056089', '1470', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(350, 'user', '4430', '9072581811', '1259', 'edpekm@visionhonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(351, 'user', '4441', '9215056089', '893', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(352, 'user', '4470', '8608870134', '1521', 'ashif.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(353, 'user', '4495', '9215056089', '1468', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(354, 'user', '4497', '9215056089', '1477', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(355, 'user', '4534', '9746906475', '492', 'rafeekazeez@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(356, 'user', '4540', '9600248421', '1490', 'kingsvinothbabu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(357, 'user', '4541', '9597755441', '1540', 'nellaiautomobiles@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(358, 'user', '4618', '9600248421', '617', 'skjoy69@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(359, 'user', '4623', '8438189879', '1564', 'westmountaincorporation@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(360, 'user', '4636', '8903170929', '1289', 'ashif.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(361, 'user', '4656', '9976004100', '887', 'asrjewellery@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(362, 'user', '4668', '9842787027', '1580', 'vishri28@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(363, 'user', '4694', '9842787027', '1581', 'vishri28@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(364, 'user', '4699', '9566808951', '1467', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(365, 'user', '4710', '9884493971', '1458', 'avinashambeth@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(366, 'user', '4721', '9789555283', '913', 'avkschools@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(367, 'user', '4772', '8754833367', '1348', 'uventerprises76@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(368, 'user', '4774', '9600248421', '1597', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(369, 'user', '4806', '9791402655', '1337', 'mechservicereports@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(370, 'user', '4807', '8300041188', '608', 'routeskanthipuram@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(371, 'user', '4808', '8124547565', '1068', 'mrkumar60310@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(372, 'user', '4809', '8608540540', '983', 'krishnan_5400@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(373, 'user', '4846', '9442585981', '1506', 'rkbajaj.service.mdu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(374, 'user', '4885', '8903170929', '995', 'ashif.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(375, 'user', '4922', '8848540029', '1623', 'vgvijivg@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(376, 'user', '4927', '8148716925', '1204', 'ceo@w2bweb.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(377, 'user', '4932', '9489484240', '1609', 'justclicksms@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(378, 'user', '4955', '9952737318', '1594', 'rilwan_ril2000@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(379, 'user', '4956', '9597755441', '1556', 'kingsvinothbabu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(380, 'user', '4986', '919840210185', '1639', 'jayeshjchndrn@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(381, 'user', '4997', '917708450633', '1647', 'visiontechs2017@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(382, 'user', '5025', '8593001118', '1519', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(383, 'user', '5026', '9995572200', '1507', 'INFO@SMARTIPZ.IN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(384, 'user', '5027', '8593001118', '1505', 'INFO@SMARTIPZ.IN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(385, 'user', '5028', '9895123218', '1446', 'smartipzbs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(386, 'user', '5029', '8593001118', '1424', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(387, 'user', '5030', '8593001118', '1420', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(388, 'user', '5031', '9946501167', '1479', 'kumaradhas@noratel.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(389, 'user', '5032', '8593001118', '1415', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(390, 'user', '5033', '8593001118', '1413', 'smartipzbs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(391, 'user', '5034', '8593001118', '1412', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(392, 'user', '5035', '8593001118', '1406', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(393, 'user', '5036', '9745844491', '1404', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(394, 'user', '5042', '9047033408', '654', 'essensuals.madurai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(395, 'user', '5059', '9843287152', '1252', 'yamaha1@alagendran.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(396, 'user', '5069', '9215056089', '1472', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(397, 'user', '5100', '8848577707', '1276', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(398, 'user', '5107', '9095278525', '1652', 'jeffry4jesus@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(399, 'user', '5127', '9600248421', '1596', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(400, 'user', '5152', '9994849499', '1702', 'dhina@dhinatechnologies.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(401, 'user', '5180', '9176565757', '1678', 'hitechcomputerscctv@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(402, 'user', '5193', '9539019861', '1614', 'dotsedu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(403, 'user', '5198', '9842151444', '1684', 'trigger.tenkasi@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(404, 'user', '5204', '8714169875', '1532', 'principalssdc2013@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(405, 'user', '5205', '8714169875', '1538', 'principalssdc2013@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(406, 'user', '5240', '9865027978', '1325', 'abbas@zealinfonetworks.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(407, 'user', '5243', '8848578847', '1374', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(408, 'user', '5248', '9751311000', '1589', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(409, 'user', '5262', '9400761952', '1579', 'salahudeentvm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(410, 'user', '5310', '9215056089', '1317', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(411, 'user', '5320', '9585070124', '1643', 'winvinothbabu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(412, 'user', '5356', '9486170302', '1606', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(413, 'user', '5359', '9092915290', '1554', '4dinteriors.nellai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(414, 'user', '5369', '9025807083', '710', 'icdsramnad@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(415, 'user', '5377', '7305327003', '1779', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(416, 'user', '5381', '9600248421', '1721', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(417, 'user', '5393', '9677900677', '1780', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(418, 'user', '5440', '8848577707', '1578', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(419, 'user', '5490', '9789308131', '886', 't.h.vishnukumar@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(420, 'user', '5496', '9751230003', '1595', 'g.ramanathan90@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(421, 'user', '5506', '9600248421', '1771', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(422, 'user', '5518', '9387319200', '1680', 'sitetechnologies11@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(423, 'user', '5521', '9943073210', '1809', 'karunaiillam1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(424, 'user', '5523', '9656731460', '1732', 'zephyrtvm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(425, 'user', '5534', '9961868847', '1745', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(426, 'user', '5565', '9847392939', '1307', 'mailbiindia@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(427, 'user', '5569', '9842563790', '1592', 'anannd@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(428, 'user', '5574', '9487494997', '1637', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(429, 'user', '5583', '9600248421', '1705', 'dpschennai1213@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(430, 'user', '5592', '9566666985', '1583', 'lakshmanan1987@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(431, 'user', '5603', '9176040346', '1336', 'bigbazarsm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(432, 'user', '5604', '9600248421', '1453', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(433, 'user', '5613', '8848577707', '1498', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(434, 'user', '5649', '9944423248', '1575', 'starcreativity10@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(435, 'user', '5699', '9655206161', '1808', 'astrosramesh@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(436, 'user', '5717', '9961632905', '514', 'foresight.foresight@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(437, 'user', '5755', '9655167310', '1850', 'ashifanxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(438, 'user', '5758', '9361761802', '1494', 'mgbuildersvms@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(439, 'user', '5764', '9884847742', '1776', 'azik.india@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(440, 'user', '5774', '9215056089', '1247', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(441, 'user', '5778', '9443323860', '1271', 'prasath.swifterz@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(442, 'user', '5780', '9600248421', '1856', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(443, 'user', '5808', '8870116699', '1681', 'mail@hiltek.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(444, 'user', '5812', '9791468016', '1160', 'sreeram.3g@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(445, 'user', '5829', '9500001555', '1455', 'jayeshjayachandran@hptmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(446, 'user', '5834', '8144026222', '1768', 'deepakgerry@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(447, 'user', '5860', '9215056089', '1361', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(448, 'user', '5868', '9871987636', '1354', 'avoncoders@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(449, 'user', '5893', '8248846895', '1824', 'getcashindiacom@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(450, 'user', '5903', '8593001118', '1421', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(451, 'user', '5905', '8593001118', '1416', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(452, 'user', '5906', '8593001118', '1414', 'SMARTIPZBS@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(453, 'user', '5912', '919611824441', '1722', 'mail@radicaltechnologies.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(454, 'user', '5917', '9500105555', '1612', 'jayeshjayachandran@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(455, 'user', '5918', '918056282727', '1774', 'nationalacademy2008@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(456, 'user', '5921', '9349438643', '1848', 'info@cubixsystems.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(457, 'user', '5922', '9500126654', '1836', 'murali@reachacademy.net', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(458, 'user', '5923', '9566157457', '1694', 'hemish31@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(459, 'user', '5949', '7620576931', '1531', 'jayeshjchndrn@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(460, 'user', '5967', '9047272277', '1878', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL);
INSERT INTO `customers` (`customer_id`, `customer_type`, `name`, `company_name`, `mobile`, `email`, `alternate_mobile`, `address`, `city`, `state`, `country`, `pincode`, `owner_by`, `assign_by`, `lead_requirement_id`, `lead_stage_id`, `created_by`, `status`, `credit_balance`, `password`, `reference_code`, `custom_fields`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(461, 'user', '6077', '9944418716', '1900', 'hrsbusinesssolutions@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(462, 'user', '6107', '9072581876', '1851', 'corp.edp@visionhonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(463, 'user', '6115', '9072581876', '1870', 'corp.edp@visionhonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(464, 'user', '6122', '9791341963', '1921', 'manojin.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(465, 'user', '6123', '9994339731', '1922', 'manojin.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(466, 'user', '6126', '9865229141', '717', 'rashid@picpaisa.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(467, 'user', '6130', '9894537429', '1644', 'aljannathschool@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(468, 'user', '6139', '9072581876', '1867', 'corp.edp@visionhonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(469, 'user', '6180', '9894532899', '603', 'stmaryschool.mullikulam@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(470, 'user', '6219', '9446092721', '1877', 'dmadhavan7@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(471, 'user', '6237', '9629831848', '806', 'suxuz.erode@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(472, 'user', '6255', '9600922390', '1627', 'krbharatgas@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(473, 'user', '6267', '9841855129', '522', 'raiyanclothing@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(474, 'user', '6268', '9489906144', '1655', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(475, 'user', '6297', '9995592278', '662', 'binnysharon@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(476, 'user', '6339', '9746686869', '1831', 'dental@igmt.org', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(477, 'user', '6340', '9999999999', '758', 'admin@adm.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(478, 'user', '6358', '9600248421', '1998', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(479, 'user', '6395', '9072581876', '2007', 'corp.edp@visionhonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(480, 'user', '6399', '9092008013', '2003', 'info@sigytal.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(481, 'user', '6400', '9072581876', '2010', 'corp.edp@visionhonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(482, 'user', '6429', '9656107735', '2026', 'mirashnooriya@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(483, 'user', '6463', '9842985447', '1502', 'aksmani1983@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(484, 'user', '6470', '9600248421', '1997', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(485, 'user', '6483', '9047736611', '1999', 'bala2332@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(486, 'user', '6493', '9442277764', '2049', 'vinayagamobileseswar@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(487, 'user', '6507', '9443234752', '1242', 'svjewellerymart@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(488, 'user', '6511', '9787944490', '2042', 'samsoneventmangement@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(489, 'user', '6522', '9894044445', '2043', 'info@delmanexpert.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(490, 'user', '6532', '9600248421', '1926', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(491, 'user', '6535', '9215056089', '1668', 'subhashhooda@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(492, 'user', '6537', '9600248421', '2008', 'ANAND@SPARKALERTS.IN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(493, 'user', '6542', '8973239042', '2061', 'googreensolution@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(494, 'user', '6564', '9215056089', '1873', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(495, 'user', '6571', '9215056089', '1302', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(496, 'user', '6617', '9633300023', '1504', 'iconseducation@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(497, 'user', '6618', '9746477227', '2073', 'itmicons@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(498, 'user', '6621', '7200101718', '2058', 'shrisankermobile@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(499, 'user', '6638', '9746477227', '2079', 'itmicons@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(500, 'user', '6640', '7012020410', '1803', 'cubitacademy@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(501, 'user', '6663', '8129917765', '2085', 'alphafurnituretvm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(502, 'user', '6684', '9072581876', '2084', 'corp.edp@visionhonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(503, 'user', '6686', '9215056089', '1557', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(504, 'user', '6726', '9600248421', '2088', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(505, 'user', '6752', '9442330750', '1898', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(506, 'user', '6758', '9944778463', '1476', 'jsstudiovgl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(507, 'user', '6759', '9884979937', '2063', 'sfsnltn@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:16', '2026-08-24 09:01:16', NULL),
(508, 'user', '6790', '9994023233', '2118', 'benat.charles@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(509, 'user', '6791', '9249400800', '1943', 'jcksajikumar@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(510, 'user', '6809', '9600248421', '2018', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(511, 'user', '6834', '9600324075', '909', 'antsolution046@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(512, 'user', '6840', '9865229141', '988', 'rashid@picpaisa.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(513, 'user', '6841', '9677372713', '700', 'sapbasis2@vgn.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(514, 'user', '6843', '7811831222', '1787', 'ismailredif93@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(515, 'user', '6844', '9443350960', '1541', 'selvinsiddha@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(516, 'user', '6845', '8015591618', '989', 'sales@kumarideal.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(517, 'user', '6846', '9865229141', '987', 'hi2rashid@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(518, 'user', '6847', '8903170927', '885', 'ashif.anxo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(519, 'user', '6848', '9677536615', '783', 'dclfindia@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(520, 'user', '6850', '8220022233', '672', 'balasriram1989@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(521, 'user', '6851', '8220022233', '671', 'balasriram1989@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(522, 'user', '6871', '7094481581', '1106', 'kpventhan5@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(523, 'user', '6919', '9789555283', '1007', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(524, 'user', '6939', '7448717777', '2056', 'tallentgroups1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(525, 'user', '6942', '9659022122', '2166', 'sandysakthi50@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(526, 'user', '6944', '8714187087', '302', 'mobilegallerytvm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(527, 'user', '6955', '9600248421', '1683', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(528, 'user', '6961', '9171111551', '2117', 'contactfilmaddicts@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(529, 'user', '6975', '9585070124', '2135', 'winvinothbabu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(530, 'user', '6978', '9072581876', '2013', 'corp.edp@visionhonda.Com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(531, 'user', '6996', '9842112383', '1881', 'sukraraju@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(532, 'user', '7001', '9072581876', '2179', 'corp.edp@visionhonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(533, 'user', '7034', '9566000200', '1601', 'info@narayanapearls.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(534, 'user', '7054', '9003770990', '1582', 'ceo@pkdesignzz.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(535, 'user', '7056', '9791820044', '2197', 'bsubbu652@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(536, 'user', '7059', '9442277764', '2050', 'vinayagamobileseswar@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(537, 'user', '7070', '9750672720', '1269', 'sansoftsolutions@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(538, 'user', '7082', '9526413777', '2124', 'adhimotorsservice777@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(539, 'user', '7098', '9894755582', '2036', 'director.placement@scad.ac.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(540, 'user', '7099', '9894114421', '1860', 'psxelectronics@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(541, 'user', '7121', '9746040913', '2212', 'jineshmpjmkochi@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(542, 'user', '7135', '9215056089', '1920', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(543, 'user', '7139', '9894894135', '733', 'saravanakmu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(544, 'user', '7145', '9080085345', '1723', 'prabhakar@buson.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(545, 'user', '7153', '9626605566', '1879', 'VSGARUN@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(546, 'user', '7163', '9809885294', '2214', 'muhammedriyaz08@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(547, 'user', '7164', '9894411110', '1352', 'scoofi@aparajith.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(548, 'user', '7172', '9944944022', '2206', 'karthickarun@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(549, 'user', '7179', '8891305179', '2103', 'woodpeckertvm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(550, 'user', '7191', '9894395556', '2220', 'karthiganesq@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(551, 'user', '7203', '9746686869', '1829', 'akhil@foresightgroup.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(552, 'user', '7207', '9629988887', '506', 'service@kaninitechnology.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(553, 'user', '7235', '9965673005', '1656', 'antsolution046@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(554, 'user', '7264', '9188872782', '2247', 'adwaithmedia@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(555, 'user', '7271', '9633988741', '2250', 'amarinfolab@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(556, 'user', '7311', '9995905555', '132', 'kpmalimtr@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(557, 'user', '7318', '9524663251', '2260', 'karthikgofficial@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(558, 'user', '7319', '9524663251', '2261', 'karthikg1296@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(559, 'user', '7327', '9789555283', '2144', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(560, 'user', '7333', '7373078989', '930', 'ed@snsgroups.org', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(561, 'user', '7351', '9629234788', '2263', 'mani1network@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(562, 'user', '7368', '9629234788', '2272', 'mk49994@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(563, 'user', '7381', '9791877777', '2186', 'info@sigytal.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(564, 'user', '7390', '9443453568', '2227', 'musalinen2017@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(565, 'user', '7392', '9447605775', '2257', 'elixirbis.mail@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(566, 'user', '7394', '8848577707', '1720', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(567, 'user', '7407', '9215056089', '1558', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(568, 'user', '7413', '9655177777', '2092', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(569, 'user', '7418', '8848577707', '1368', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(570, 'user', '7421', '8438189879', '1456', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(571, 'user', '7423', '9790599888', '2032', 'praveen999797@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(572, 'user', '7425', '9629198551', '2284', 'ELAMANJLIC@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(573, 'user', '7426', '9787353433', '2019', 'thambimobilesavl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(574, 'user', '7429', '9994573451', '1278', 'info@nilaseafoods.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(575, 'user', '7430', '9597555488', '1349', 'p_samps@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(576, 'user', '7441', '8086700010', '1679', 'whitedammar@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(577, 'user', '7452', '9600324075', '1561', 'antsolution046@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(578, 'user', '7458', '9944946621', '1704', 'karthiyainivaidhiyasalai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(579, 'user', '7467', '9840044038', '2255', 'kpmsvm@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(580, 'user', '7469', '9865619420', '2016', 'cbe.bioblooms@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(581, 'user', '7470', '8870291191', '2017', 'rmdmobiles01@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(582, 'user', '7472', '9787322210', '2057', 'abcpoorna123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(583, 'user', '7485', '9092780007', '2285', 'hellomarriedly@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(584, 'user', '7509', '9790014768', '1801', 'selvanayakibharatgas@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(585, 'user', '7512', '9215056089', '1517', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(586, 'user', '7516', '7502885350', '2303', 'azeesnisha@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(587, 'user', '7520', '9789222202', '2232', 'admin@m.sigytal.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(588, 'user', '7522', '9842951177', '1750', 'mukeshhonda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(589, 'user', '7524', '9871987636', '1854', 'avoncoders@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(590, 'user', '7527', '9976725555', '878', 'rkpmotors@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(591, 'user', '7539', '9416657758', '1316', 'jbnkkila@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(592, 'user', '7544', '9581573919', '1796', 'caphalguna@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(593, 'user', '7555', '8015073303', '546', 'manikandan@buson.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(594, 'user', '7590', '8129486555', '2193', 'edp@cbchonda.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(595, 'user', '7595', '7305901176', '2319', 'quickpickpro@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(596, 'user', '7597', '9943168284', '2311', 'bbshiju@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(597, 'user', '7598', '9080085345', '1994', 'prabhakar@buson.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(598, 'user', '7616', '7598557755', '1766', 'butterfliesapm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(599, 'user', '7623', '8848577707', '1402', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(600, 'user', '7624', '9597568363', '1371', 'ajithkumar072@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(601, 'user', '7643', '9215056089', '1303', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(602, 'user', '7683', '9942779598', '1022', 'ramaravind916@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(603, 'user', '7716', '7373123453', '1605', 'bastinpc@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(604, 'user', '7741', '7373712312', '1730', 'shrivinayakabharatgas@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(605, 'user', '7749', '9871987636', '1286', 'pstanmay@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(606, 'user', '7764', '9597568363', '1125', 'ajithkumar072@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(607, 'user', '7765', '9488251888', '745', 'muthukumar1888@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(608, 'user', '7792', '7010365398', '1918', 'itsfarook@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(609, 'user', '7813', '8940353536', '1733', 'sonamotors@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(610, 'user', '7853', '9894346627', '2379', 'rsoftmadurai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(611, 'user', '7859', '9626787878', '1300', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(612, 'user', '7866', '8807000904', '658', 'karpagaagenciesmdu@rediffmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(613, 'user', '7893', '8675373311', '2136', 'ssartscollege@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(614, 'user', '7894', '7708291909', '2245', 'jaisaitemple@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(615, 'user', '7899', '8848577707', '1638', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(616, 'user', '7900', '8848577707', '1662', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(617, 'user', '7902', '8848577707', '1461', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(618, 'user', '7920', '8129210141', '605', 'jsmchathannoor@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(619, 'user', '7924', '9025848800', '2385', 'slvntamil10@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(620, 'user', '7932', '9446903873', '1422', 'cybotech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(621, 'user', '7935', '9961691699', '2384', 'admin@b2businesssolution.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(622, 'user', '7948', '9894989359', '2354', 'royal299@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(623, 'user', '7951', '9842774520', '718', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(624, 'user', '7953', '9842774520', '992', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(625, 'user', '7954', '8220016215', '756', 'samtallytuty@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(626, 'user', '7955', '9842449215', '996', 'srmfautomobiles@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(627, 'user', '7956', '9787772044', '1488', 'sarathytvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(628, 'user', '7957', '9443550212', '1982', 'mahe@suninfosys.org.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(629, 'user', '7960', '9600248421', '1466', 'kingsvinothbabu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(630, 'user', '7969', '9994221309', '797', 'rajguru25@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(631, 'user', '7996', '8848577707', '1397', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(632, 'user', '7997', '9961868847', '2337', 'demo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(633, 'user', '7998', '9961868847', '2336', 'demo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(634, 'user', '8005', '8489144966', '852', 'chellamhonda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(635, 'user', '8037', '9698989878', '542', 'welcomeholidaysnellai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(636, 'user', '8062', '9894807608', '2424', 'drvijaynmv@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(637, 'user', '8066', '9994333000', '2033', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(638, 'user', '8069', '9600248421', '1769', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(639, 'user', '8073', '7373119333', '769', 'samtallytuty@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(640, 'user', '8074', '9842449215', '876', 'ziondataproducts@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(641, 'user', '8079', '8644888777', '1492', 'arasanganiyappa@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(642, 'user', '8092', '9500035843', '1312', 'rameshgasagencies@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(643, 'user', '8100', '7373767682', '2174', 'apj.bharatgas@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(644, 'user', '8101', '9600248421', '2129', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(645, 'user', '8132', '9941508055', '2432', 'muza28@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(646, 'user', '8136', '8870156564', '1551', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(647, 'user', '8137', '9789555283', '1677', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(648, 'user', '8147', '9842774520', '738', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(649, 'user', '8160', '9442631607', '1880', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(650, 'user', '8204', '8848577707', '1845', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(651, 'user', '8206', '8848577707', '1473', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(652, 'user', '8207', '8848577707', '1474', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(653, 'user', '8221', '9789555283', '2438', 'anand@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(654, 'user', '8222', '9894194980', '638', 'srikrishnagold916@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(655, 'user', '8224', '9843257685', '1671', 'dayanadsingh@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(656, 'user', '8229', '9842774520', '1162', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(657, 'user', '8230', '9842774520', '1149', 'royalsysytems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(658, 'user', '8231', '9600416151', '1166', 'tmhs.tuty@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(659, 'user', '8240', '9842774520', '697', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(660, 'user', '8241', '9842774520', '674', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(661, 'user', '8243', '9344106384', '648', 'spbalu9@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(662, 'user', '8246', '9843199999', '591', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(663, 'user', '8247', '9443102610', '573', 'janagiramans@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(664, 'user', '8249', '9894497101', '1790', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(665, 'user', '8251', '7373767682', '1713', 'apj.bharatgas@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(666, 'user', '8253', '9597174280', '1543', 'sdtradersnellai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(667, 'user', '8254', '9442159716', '1542', 'skantharegency@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(668, 'user', '8255', '9345935451', '1545', 'heerasiddiq@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(669, 'user', '8258', '9591079450', '1430', 'manickshine@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(670, 'user', '8259', '9566095160', '1429', 'saurabh@aliciasouza.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(671, 'user', '8277', '9940103571', '2451', 'govind.pgr2011@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(672, 'user', '8292', '9361607799', '2431', 'acc.murugappasstores@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(673, 'user', '8294', '9842774520', '904', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(674, 'user', '8320', '9600248421', '1837', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(675, 'user', '8322', '7373155899', '2408', 'support@cloudstracker.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(676, 'user', '8327', '8754722833', '2025', 'infohams2014@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(677, 'user', '8330', '9791010020', '2470', 'vishnu@agaramsolutions.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(678, 'user', '8377', '9842774520', '817', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(679, 'user', '8380', '9842774520', '760', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(680, 'user', '8382', '9940525779', '1889', 'jimmylonan@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(681, 'user', '8445', '9791010020', '2506', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(682, 'user', '8447', '9865422268', '712', 'shunmuganjewelleryapk@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(683, 'user', '8464', '9791010020', '2493', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(684, 'user', '8468', '8695952599', '1636', 'parveentraders.tup@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(685, 'user', '8485', '9495919194', '2519', 'foxwid.com@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(686, 'user', '8489', '7373773232', '2500', 'ashok3232vijay@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(687, 'user', '8496', '9791010020', '2510', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(688, 'user', '8504', '9486470577', '752', 'srikrishnapvt.ltd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL);
INSERT INTO `customers` (`customer_id`, `customer_type`, `name`, `company_name`, `mobile`, `email`, `alternate_mobile`, `address`, `city`, `state`, `country`, `pincode`, `owner_by`, `assign_by`, `lead_requirement_id`, `lead_stage_id`, `created_by`, `status`, `credit_balance`, `password`, `reference_code`, `custom_fields`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(689, 'user', '8513', '9791010020', '2504', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(690, 'user', '8542', '9215056089', '2402', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(691, 'user', '8559', '9629234788', '2539', 'mani1network@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(692, 'user', '8613', '9791010020', '2549', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(693, 'user', '8638', '9655339359', '2416', 'kadalads9@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(694, 'user', '8653', '9215056089', '2577', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(695, 'user', '8655', '7015671117', '2494', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(696, 'user', '8674', '9600364443', '2580', 'creativeinfotecherd@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(697, 'user', '8694', '9791010020', '2548', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(698, 'user', '8696', '9791010020', '2514', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(699, 'user', '8703', '9215056089', '2478', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(700, 'user', '8717', '9791010020', '2582', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(701, 'user', '8769', '9791010020', '2525', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(702, 'user', '8772', '9791010020', '2567', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(703, 'user', '8778', '7598160099', '2569', 'tidovinus@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(704, 'user', '8782', '9443636935', '2573', 'sales@jts.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(705, 'user', '8783', '9962466700', '2615', 'kanna@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(706, 'user', '8786', '9994656862', '2419', 'onemediaads3@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(707, 'user', '8787', '8015080950', '2537', 'hkshrihariharan@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(708, 'user', '8797', '9585070124', '2371', 'ceo@myauditoroffice.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(709, 'user', '8806', '7418406045', '2560', 'clinksolutionsudi@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(710, 'user', '8814', '8012626111', '2613', 'subramanian.gtech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(711, 'user', '8870', '7092150100', '2597', 'jellysoftindia@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(712, 'user', '8886', '9092008013', '2671', 'info@sigytal.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(713, 'user', '8893', '9488444595', '2262', 'bookings@zentravel.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(714, 'user', '8911', '9600248421', '2547', 'pearlagency@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(715, 'user', '8924', '9500925802', '2472', 'udhayakumar.swaminathan@agaramsolutions.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(716, 'user', '8935', '9865401494', '2641', 'muthusai29@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(717, 'user', '8969', '9626244772', '2508', 'gm.service@aadhithyamotors.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(718, 'user', '8986', '9962177556', '2699', 'krajesh@infinitusdata.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(719, 'user', '8994', '9791010020', '2532', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(720, 'user', '8995', '9791010020', '2638', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(721, 'user', '8996', '9791010020', '2527', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(722, 'user', '8997', '9791010020', '2587', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(723, 'user', '9001', '9940725999', '2656', 'lifesafesolutions@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(724, 'user', '9023', '7356525319', '2602', 'navictech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(725, 'user', '9024', '9443127970', '2701', 'ramalingamsahana@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(726, 'user', '9056', '9791010020', '2714', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(727, 'user', '9067', '7867039087', '2722', 'uhcikanyakumari@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(728, 'user', '9070', '9842635906', '2724', 'ganesan.yes@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(729, 'user', '9116', '9791010020', '2733', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(730, 'user', '9127', '9597188885', '2594', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(731, 'user', '9135', '8012627000', '2742', 'nmgopinath@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(732, 'user', '9142', '9791010020', '2745', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(733, 'user', '9177', '9944455040', '2315', 'maccom1980@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(734, 'user', '9181', '9600248421', '2735', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(735, 'user', '9187', '9894416562', '2632', 'subramanian.gtech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(736, 'user', '9188', '9843284044', '2748', 'mksmuthu11@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(737, 'user', '9191', '9600372296', '2635', 'harikeshthiruppathi@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(738, 'user', '9193', '9894416562', '2740', 'subramanian.gtech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(739, 'user', '9198', '9965053534', '2676', 'info@gtechnology.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(740, 'user', '9206', '9791010020', '2746', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(741, 'user', '9209', '9791010020', '2664', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(742, 'user', '9222', '9442622046', '2695', 'chella1976@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(743, 'user', '9247', '9843307916', '2743', 'sriparvathisjewels85@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(744, 'user', '9279', '9791010020', '2778', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(745, 'user', '9280', '9791010020', '2680', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(746, 'user', '9311', '9750940603', '2757', 'rainbowtvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(747, 'user', '9317', '9943563220', '2693', 'sales@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(748, 'user', '9334', '7259809019', '2784', 'pssaranya1995@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(749, 'user', '9335', '9842754452', '2789', 'titaanicprinters@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(750, 'user', '9339', '9215056089', '2406', 'subahshhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(751, 'user', '9348', '9940508833', '2563', 'shankarvj@metrograndhotel.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(752, 'user', '9361', '9205523554', '2290', 'avoncoders@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(753, 'user', '9384', '9578104488', '2756', 'vanaviltvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(754, 'user', '9408', '9791010020', '2773', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(755, 'user', '9419', '9884847742', '2803', 'alike.india@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(756, 'user', '9420', '9894232667', '2814', 'ceoaljannath@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(757, 'user', '9421', '9500745829', '2666', 'harisakthi@neophrontech.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(758, 'user', '9426', '9894655079', '2643', 'rightlearningsys@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(759, 'user', '9447', '7259809019', '2817', 'srividyamanthirmatricschoolbvn@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(760, 'user', '9468', '9789060029', '2753', 'truefishbng@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(761, 'user', '9497', '9994656862', '2616', 'onemediaads3@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(762, 'user', '9509', '9626244772', '2507', 'gm.service@aadhithyamotors.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(763, 'user', '9530', '9486493203', '2815', 'info@neophrontech.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(764, 'user', '9538', '9791010020', '2720', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(765, 'user', '9637', '9443167405', '2481', 'info@elshaddai.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(766, 'user', '9669', '9791010020', '2495', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(767, 'user', '9672', '9445970174', '1661', 'onestopsmpl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(768, 'user', '9684', '9443167405', '2486', 'info@anifur.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(769, 'user', '9685', '9489048348', '1279', 'royston@vvmarineproducts.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(770, 'user', '9697', '9791010020', '2512', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(771, 'user', '9701', '9894506600', '2509', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(772, 'user', '9705', '9791010020', '2708', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(773, 'user', '9711', '9791010020', '2591', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(774, 'user', '9748', '9791010020', '2562', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(775, 'user', '9839', '9205523554', '2237', 'avoncoders@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(776, 'user', '9848', '9894886556', '2952', 'karti14be@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(777, 'user', '9857', '8148997585', '2837', 'gopikumaran@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(778, 'user', '9864', '9205523554', '1608', 'avoncoders@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(779, 'user', '9888', '9215056089', '2941', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(780, 'user', '9920', '8012055999', '2891', 'sales.krkhonda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(781, 'user', '9922', '918344258474', '2964', 'govardhann337@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(782, 'user', '9927', '9995401476', '2322', 'adv.sanjaymohan@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(783, 'user', '9936', '9094447770', '2781', 'mazglobalservices@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(784, 'user', '9956', '9488565656', '2351', 'ladislic@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(785, 'user', '9966', '9787585801', '2758', 'vallivasu.tvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(786, 'user', '9979', '9842635906', '2725', 'ganesan.yes@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(787, 'user', '9982', '7373734407', '2989', 'ajasranjith007@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(788, 'user', '9984', '9080517101', '2541', 'arafafashionboutique@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(789, 'user', '9988', '8438189879', '2182', 'mail.absolutions@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(790, 'user', '9993', '918667414985', '2872', 'sparrowmenswear.19@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(791, 'user', '10003', '9600248421', '2707', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 5900.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-09-23 14:31:17', NULL),
(792, 'user', '10008', '9750024777', '2912', 'amutha916@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(793, 'user', '10011', '9759202241', '2916', 'arsal_software@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(794, 'user', '10015', '9688455522', '2972', 'vallivasu.tvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(795, 'user', '10025', '9524412067', '3001', 'itadminmdu@peeyesyemhyundai.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(796, 'user', '10031', '8220430385', '2955', 'ramnadraymond@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(797, 'user', '10074', '9962177556', '2766', 'krajesh@infinitusdata.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(798, 'user', '10081', '9500745829', '2848', 'sales@gasvalley.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(799, 'user', '10092', '9600364443', '3019', 'creativeinfotecherd@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(800, 'user', '10096', '9791010020', '2956', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(801, 'user', '10120', '8220225555', '3026', 'zionfurniturenellai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(802, 'user', '10127', '8489932604', '2946', 'mechhondatnj@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(803, 'user', '10162', '9388588588', '1907', 'info@toddlersinternational.org', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(804, 'user', '10163', '9847392939', '3039', 'mailbiindia@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(805, 'user', '10164', '9037260098', '2163', 'mailbiindia@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(806, 'user', '10166', '9526355552', '2334', 'mrasignature@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(807, 'user', '10213', '9600248421', '1830', 'gm.hotelaarathy@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:17', '2026-08-24 09:01:17', NULL),
(808, 'user', '10229', '9182055870', '3063', 'muraliwings@rediffmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(809, 'user', '10232', '9791010020', '2556', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(810, 'user', '10234', '9791010020', '2496', 'support@onhandsms.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(811, 'user', '10254', '8589031888', '2770', 'servicediyatvm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(812, 'user', '10262', '9042410368', '2776', 'svmghonda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(813, 'user', '10267', '9629236600', '1465', 'raja@velsoft.org', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(814, 'user', '10275', '9715511333', '3030', 'srirajtvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(815, 'user', '10282', '8940066011', '3084', 'info@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(816, 'user', '10335', '9566580039', '2705', 'viswamtvsservice@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(817, 'user', '10339', '9597831038', '2924', 'itsdurai10@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(818, 'user', '10353', '9442628960', '3102', 'anbuparamedical@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(819, 'user', '10381', '916380201962', '3043', 'gokul9442656@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(820, 'user', '10382', '8524873934', '2709', 'ttntsm1950@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(821, 'user', '10490', '9495919194', '2603', 'densingdanielm@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(822, 'user', '10492', '8289919000', '2604', '919autospa@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(823, 'user', '10494', '9495919194', '2520', 'densingdaniel2@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(824, 'user', '10507', '7406939191', '2691', 'hsr.raksha@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(825, 'user', '10547', '7010075551', '3087', 'sowdeswaritvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(826, 'user', '10558', '9786302141', '1066', 'michaeleee@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(827, 'user', '10585', '9952794003', '2950', 'thangamagaljewellery@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(828, 'user', '10587', '9894491343', '3000', 'oscarhdflex@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(829, 'user', '10589', '9962917550', '2268', 'Whiletechnology@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(830, 'user', '10595', '9947321736', '3157', 'arunraj4010@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(831, 'user', '10598', '9947321736', '3155', 'arunraj4010@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(832, 'user', '10599', '9842774520', '1148', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(833, 'user', '10600', '8220016215', '1046', 'jeyasakthiplywoods@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(834, 'user', '10601', '9842774520', '1019', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(835, 'user', '10608', '9443415957', '1146', 't.selvamathan@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(836, 'user', '10610', '7373780555', '3020', 'sakthivinoth3@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(837, 'user', '10708', '9360989789', '3065', 'info@neophrontech.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(838, 'user', '10713', '9360989789', '3031', 'neophronyovan@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(839, 'user', '10735', '9843357685', '975', 'thangampalay@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(840, 'user', '10744', '9842449215', '1030', 'samtallytuty@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(841, 'user', '10747', '9961868847', '2446', 'admin@kgoasc.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(842, 'user', '10755', '9080276600', '2786', 'innoce2018@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(843, 'user', '10757', '9842774520', '842', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(844, 'user', '10760', '9188870183', '2352', 'servicetopgears@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(845, 'user', '10762', '9865360410', '2489', 'info@share-gold.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(846, 'user', '10768', '9207136911', '3201', 'mazstudios4k@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(847, 'user', '10781', '8220178274', '2749', 'ansaribilal79@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(848, 'user', '10783', '9626787878', '3221', 'royal299@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(849, 'user', '10788', '8848578847', '3214', 'ccbemployeessociety123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(850, 'user', '10791', '7373719779', '2744', 'bharathtvspalladam@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(851, 'user', '10810', '9842774520', '943', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(852, 'user', '10816', '9788860704', '2571', 'jothybroilers@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(853, 'user', '10821', '9746576518', '3238', 'akdhatech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(854, 'user', '10824', '9790041301', '3209', 'saravinosnacks2000@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(855, 'user', '10838', '9994223778', '3236', 'manisankar@ethoughtz.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(856, 'user', '10848', '9843348464', '2906', 'merlincse@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(857, 'user', '10851', '9994017738', '3216', 'tatamotors@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(858, 'user', '10852', '918667081323', '3140', 'krishnamaternity@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(859, 'user', '10866', '9994222983', '3080', 'atselectropower@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(860, 'user', '10869', '8976886881', '3187', 'sumant.jha@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(861, 'user', '10875', '9840179017', '914', 'qualitagro@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(862, 'user', '10881', '9865702153', '3183', 'aranthangitvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(863, 'user', '10882', '9976216669', '3148', 'subramanian.gtech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(864, 'user', '10883', '9489810733', '2852', 'copy.shoppy2020@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(865, 'user', '10887', '9965543335', '2721', 'kcskathir@rediffmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(866, 'user', '10888', '9942311618', '2921', 'sbi.12750@sbi.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(867, 'user', '10892', '9846992220', '3258', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(868, 'user', '10894', '9894346627', '2380', 'rsoftmadurai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(869, 'user', '10896', '9894346627', '2600', 'rsoftmadurai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(870, 'user', '10908', '9600364443', '3267', 'creativeinfotecherd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(871, 'user', '10917', '9846992220', '3260', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(872, 'user', '10925', '9846992220', '3269', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(873, 'user', '10935', '9176449746', '3273', 'Neutralin@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(874, 'user', '10938', '9840153013', '3222', 'santhosh.nts@sbi.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(875, 'user', '10949', '9746156588', '155', 'cocktailgentshub@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(876, 'user', '10962', '953873835', '715', 'nagarajjannu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(877, 'user', '10970', '7402740244', '1230', 'arunram.ra@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(878, 'user', '10979', '9846992220', '3287', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(879, 'user', '10982', '9846992220', '3271', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(880, 'user', '10983', '9745936073', '3224', 'info@suffixesolutions.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(881, 'user', '10995', '9443255158', '3292', 'bawaamedical.rmd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(882, 'user', '11006', '9089087654', '3302', 'anas@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(883, 'user', '11008', '8754322218', '2490', 'tutytech@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(884, 'user', '11009', '8754345454', '2578', 'jojep@amail3.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(885, 'user', '11022', '9842635906', '3143', 'ganesan.yes@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(886, 'user', '11023', '9842635906', '3079', 'ganesan.yes@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(887, 'user', '11024', '9842635906', '3186', 'ganesan.yes@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(888, 'user', '11036', '9894346627', '2467', 'rsoftmadurai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(889, 'user', '11048', '8129382438', '3288', 'equipz.in@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(890, 'user', '11081', '9944990900', '2883', 'vaikundadurai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(891, 'user', '11094', '919961868847', '3213', 'admin@b2businesssolution.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(892, 'user', '11101', '9846992220', '3279', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(893, 'user', '11123', '8098990088', '3332', 'bawaameds@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(894, 'user', '11127', '9842103750', '3064', 'muraliwings@rediffmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(895, 'user', '11139', '8760827291', '2610', 'sabareeswaranv@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(896, 'user', '11140', '7736805077', '2957', 'pranajewellery@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(897, 'user', '11141', '8590928830', '3339', 'maharajanrraajjaa@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(898, 'user', '11145', '9497195500', '3318', 'sirashkhan@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(899, 'user', '11146', '9846992220', '3335', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(900, 'user', '11151', '7598449026', '3340', 'anbu.oswald@einnel.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(901, 'user', '11153', '8344368464', '3344', 'niveda@einnel.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(902, 'user', '11154', '8590928830', '3343', 'maharajanrraajjaa@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(903, 'user', '11155', '8590928830', '3346', 'techsupport@erixon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(904, 'user', '11172', '9003338853', '3348', 'aalayam@einnel.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(905, 'user', '11197', '9865830261', '3350', 'royal299@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(906, 'user', '11209', '9629256411', '3358', 'japravin33@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(907, 'user', '11214', '8848578847', '3308', 'info@b2businesssolution.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(908, 'user', '11255', '9655339359', '3368', 'kadalads9@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(909, 'user', '11292', '8668112392', '2945', 'karuppasamyaero7@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(910, 'user', '11293', '8754504797', '3146', 'kalamnanbargal@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(911, 'user', '11294', '9947278874', '3121', 'manager@renotechnology.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(912, 'user', '11312', '9447154644', '1515', 'dptechworld@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(913, 'user', '11327', '8754687555', '2636', 'pondyrajvijaytvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(914, 'user', '11333', '8848577707', '1432', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(915, 'user', '11366', '8848577707', '1294', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL);
INSERT INTO `customers` (`customer_id`, `customer_type`, `name`, `company_name`, `mobile`, `email`, `alternate_mobile`, `address`, `city`, `state`, `country`, `pincode`, `owner_by`, `assign_by`, `lead_requirement_id`, `lead_stage_id`, `created_by`, `status`, `credit_balance`, `password`, `reference_code`, `custom_fields`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(916, 'user', '11367', '8848577707', '1299', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(917, 'user', '11368', '8848577707', '1586', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(918, 'user', '11369', '8848577707', '1503', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(919, 'user', '11371', '8848577707', '1689', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(920, 'user', '11374', '8848578847', '3396', 'admin@b2businesssolution.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(921, 'user', '11376', '9944526478', '3051', 'vsb.try@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(922, 'user', '11408', '8015668033', '3127', 'arunrrr18@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(923, 'user', '11418', '8220178274', '3211', 'arunrrr18@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(924, 'user', '11419', '8220178274', '2483', 'arunrrr18@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(925, 'user', '11426', '9994821177', '3417', 'nagasmsindia@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(926, 'user', '11454', '9994707691', '2694', 'info@doozyinfo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(927, 'user', '11455', '9894989359', '3391', 'royal299@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(928, 'user', '11519', '9842399935', '1712', 'dhakshinabharat@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(929, 'user', '11525', '9842516214', '3450', 'ssuthandiralakshmi@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(930, 'user', '11540', '8754345463', '3440', 'dev@tutytech.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(931, 'user', '11558', '9750228800', '3323', 'guberanidhilimitedmd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(932, 'user', '11563', '9488477702', '834', 'gstjewels@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(933, 'user', '11564', '9600248421', '2145', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(934, 'user', '11568', '9884201891', '3128', 'bprakash@infinitusdata.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(935, 'user', '11578', '7639975888', '3162', 'bestymotor@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(936, 'user', '11579', '9677614614', '3124', 'edp@ampl.ind.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(937, 'user', '11581', '7022049623', '3094', 'servicemanager@acceleratemotors.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(938, 'user', '11582', '9379555592', '3093', 'manjunatha_garage@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(939, 'user', '11585', '9894387777', '3038', 'resmioptical@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(940, 'user', '11587', '7708442195', '3021', 'magalakshme.tvs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(941, 'user', '11589', '9965543336', '2982', 'kcskathir@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(942, 'user', '11591', '9087423415', '2951', 'info@abmitsupport.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(943, 'user', '11593', '9786484548', '2907', 'info@abmitsupport.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(944, 'user', '11594', '9965744360', '2884', 'arrahmanatg@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(945, 'user', '11595', '7200076254', '2869', 'sales@rockgmicrotech.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(946, 'user', '11597', '9443490800', '2811', 'runishmotorshonda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(947, 'user', '11598', '8870412264', '2787', 'mdfarook.jmc15@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(948, 'user', '11599', '8754759262', '2761', 'andavartvs.nam@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(949, 'user', '11600', '7373727125', '2760', 'sriramhondaskl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(950, 'user', '11602', '7373778601', '2755', 'selecthonda786@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(951, 'user', '11606', '9965521600', '2726', 'gnanathiral@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(952, 'user', '11607', '9842156021', '2703', 'shuryamotors@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(953, 'user', '11608', '7010153530', '2696', 'chonaa2021dg3000@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(954, 'user', '11611', '9442408000', '2654', 'ameentravels73@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(955, 'user', '11620', '918220044301', '3474', 'drvasanth82@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(956, 'user', '11622', '919842188335', '3476', 'vinothcs07@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(957, 'user', '11671', '8778217056', '2550', 'ssfinance56@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(958, 'user', '11681', '9842055636', '800', 'seemattireadymade@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(959, 'user', '11684', '9600364443', '2629', 'creativeinfotecherd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(960, 'user', '11705', '9600364443', '3390', 'creativeinfotecherd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(961, 'user', '11710', '9500900243', '3506', 'mis@thillaismasala.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(962, 'user', '11720', '9846992220', '3326', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(963, 'user', '11723', '9846992220', '3509', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(964, 'user', '11727', '9600364443', '2977', 'creativeinfotecherd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(965, 'user', '11747', '9400000756', '1366', 'itmgr@hycinthhotels.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(966, 'user', '11769', '9600364443', '3333', 'creativeinfotecherd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(967, 'user', '11770', '9600364443', '3312', 'creativeinfotecherd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(968, 'user', '11793', '9495447265', '3316', 'rsnassociates2015@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(969, 'user', '11809', '8848578847', '3230', 'admin@b2businesssolution.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(970, 'user', '11815', '9361527510', '3365', 'kousalya52750@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(971, 'user', '11826', '919894010207', '3473', 'suryjay@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(972, 'user', '11833', '916382683032', '3559', 'ashmithacabs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(973, 'user', '11859', '9846992220', '3455', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(974, 'user', '11882', '8883888819', '3527', 'genewtech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(975, 'user', '11924', '9600364443', '3054', 'creativeinfotecherd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(976, 'user', '11926', '9600364443', '3172', 'creativeinfotecherd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(977, 'user', '11942', '9787015446', '3608', 'djs777djs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(978, 'user', '11950', '8489928791', '2850', 'ramkpbrs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(979, 'user', '11957', '8940555543', '3598', 'relysoftware@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(980, 'user', '11959', '9715915226', '3617', 'universecomputer2008@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(981, 'user', '11962', '7598088557', '3521', 'hannieltech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(982, 'user', '11968', '9600248421', '3614', 'sun@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(983, 'user', '11969', '9600248421', '3607', 'jaiandco@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(984, 'user', '11992', '7904482799', '3624', 'velsoft.tuty@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(985, 'user', '12015', '9600248421', '2440', 'aca@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(986, 'user', '12017', '9360020083', '1861', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(987, 'user', '12035', '9551133150', '3647', 'mgmebuild@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(988, 'user', '12054', '7598088557', '3622', 'hannieltech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(989, 'user', '12061', '8606387830', '3536', 'erebsindia@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(990, 'user', '12089', '8144444467', '2491', 'oviyajeweller@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(991, 'user', '12095', '9629270466', '2968', 'suryarps23@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(992, 'user', '12108', '9094777723', '3147', 'sanju@rcet.org.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(993, 'user', '12110', '9750952613', '3618', 'btqten@titan.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(994, 'user', '12130', '9677546655', '3290', 'ajees90@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(995, 'user', '12136', '9442671010', '3615', 'goldengtns@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(996, 'user', '12142', '9846992220', '3305', 'radikkals@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(997, 'user', '12149', '9788806063', '3702', 'naveen@crystalcodelabs.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(998, 'user', '12160', '7598088557', '3707', 'hannieltech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(999, 'user', '12173', '9942315316', '3709', 'Theammansupermart@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1000, 'user', '12180', '7639900033', '3714', 'reachus@rasiinfotech.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1001, 'user', '12196', '9500900243', '3514', 'mis@thillaismasala.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1002, 'user', '12220', '8973879089', '3668', 'lp.alexmuthubala@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1003, 'user', '12227', '9600248421', '3739', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1004, 'user', '12248', '9443122672', '3367', 'muraliwings@rediffmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1005, 'user', '12288', '9447135050', '3765', 'drkannan123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1006, 'user', '12313', '6382640110', '3743', 'greensafemanagement@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1007, 'user', '12337', '9150827665', '3246', 'rapigro001@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1008, 'user', '12347', '9600248421', '3804', 'xpress@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1009, 'user', '12355', '7550363173', '2990', 'mail.absolutions@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1010, 'user', '12359', '9585070124', '2301', 'winvinothbabu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1011, 'user', '12360', '8940066011', '2273', 'zealinfonetworks@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1012, 'user', '12362', '9952737318', '2045', 'winvinothbabu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1013, 'user', '12363', '9585070124', '1887', 'winvinothbabu@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1014, 'user', '12364', '9585070124', '1886', 'WINVINOTHBABU@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1015, 'user', '12365', '9629744125', '1885', 'WINVINOTHBABU@GMAIL.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1016, 'user', '12406', '9894989359', '3445', 'royal299@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1017, 'user', '12408', '9843831888', '3785', 'devarshandco@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1018, 'user', '12425', '9790931182', '3400', 'care@superlyfe.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1019, 'user', '12438', '8344081111', '3777', 'rioadsworld@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1020, 'user', '12453', '9080906835', '3790', 'Kovilpattifriedchicken@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1021, 'user', '12463', '9789744966', '3778', 'angarakka@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1022, 'user', '12466', '9626451945', '3793', 'surya.asp2@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1023, 'user', '12482', '9384031017', '3870', 'info@skyhomeenterprises.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1024, 'user', '12484', '9092077399', '3304', 'kgubu80@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1025, 'user', '12497', '9944819353', '3863', 'Vbjpollachi@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1026, 'user', '12505', '9677069009', '3796', 'kmniches@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1027, 'user', '12507', '6360310582', '3880', 'aditya.pb@webbazaar.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1028, 'user', '12514', '9600248421', '3812', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1029, 'user', '12531', '9443452898', '3854', 'sivatvs.snk@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1030, 'user', '12553', '9600248421', '3910', 'suxusmobiles@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1031, 'user', '12575', '8667755156', '3916', 'momzmart@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1032, 'user', '12577', '8754322218', '3917', 'info@tutytech.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1033, 'user', '12594', '9003030920', '3612', 'gloriouswebtech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1034, 'user', '12600', '9843089973', '3826', 'rpathlabtvl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1035, 'user', '12622', '9884201891', '3620', 'accounts@infinitusdata.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1036, 'user', '12624', '9842856048', '3922', 'jaissoftware@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1037, 'user', '12647', '9488477702', '3877', 'prabhuhariharan1520@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1038, 'user', '12649', '9600861277', '3946', 'hariprasad.s@techforge.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1039, 'user', '12652', '918608112416', '3436', 'mohangrid@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1040, 'user', '12656', '8667457762', '3957', 'admin@dreamzcoder.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1041, 'user', '12694', '7639977333', '3715', 'pawesome@rasiinfotech.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1042, 'user', '12713', '9345433334', '2274', 'info@velsoft.org', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1043, 'user', '12733', '9715659515', '3952', 'sridharan.a@techforge.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1044, 'user', '12735', '9600248421', '3963', 'gasska@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1045, 'user', '12736', '9600248421', '3889', 'merit@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1046, 'user', '12741', '8056282839', '3984', 'kkishore056@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1047, 'user', '12752', '9003821040', '3175', 'selvarathnamps@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1048, 'user', '12772', '9215056089', '3424', 'subhashhooda@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1049, 'user', '12790', '9846953153', '3548', 'capzon21@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1050, 'user', '12794', '7094070607', '2503', 'groskiiniz@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1051, 'user', '12814', '9884201891', '3531', 'accounts@infinitusdata.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1052, 'user', '12815', '9884459260', '3403', 'ppselvan@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1053, 'user', '12816', '9884201891', '3229', 'accounts@infinitusdata.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1054, 'user', '12817', '9962177556', '2767', 'krajesh@infinitusdata.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1055, 'user', '12818', '9884201891', '2867', 'accounts@infinitusdata.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1056, 'user', '12840', '8838410926', '4008', 'support@heofon.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1057, 'user', '12865', '9080245154', '3853', 'sivatvs.snk@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1058, 'user', '12880', '9487310673', '3362', 'psree75@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1059, 'user', '12925', '9600364443', '2627', 'creativeinfotecherd@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1060, 'user', '12935', '9600248421', '3652', 'vvimage@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1061, 'user', '12945', '9600248421', '1913', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1062, 'user', '12947', '9600248421', '1876', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1063, 'user', '12950', '9597390580', '1849', 'madhankumars@carotechs.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1064, 'user', '12951', '9345215281', '1840', 'gopal@laxvel.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1065, 'user', '12953', '9943374466', '1822', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1066, 'user', '12961', '9442330750', '1602', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1067, 'user', '12965', '9884310354', '1489', 'anand@sparkalerts.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1068, 'user', '12971', '8122924934', '1250', 'annaimeenakshischool@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1069, 'user', '12972', '9655506033', '1213', 'mksbatteries@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1070, 'user', '12991', '9842774520', '751', 'royalsystems@yahoo.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1071, 'user', '13000', '9626345321', '3483', 'surgitexaf@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1072, 'user', '13001', '9894989359', '3435', 'royal299@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1073, 'user', '13002', '9894989359', '3498', 'royal299@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1074, 'user', '13015', '7598088557', '3597', 'hannieltech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1075, 'user', '13047', '9894909089', '3662', 'michaelraja82@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1076, 'user', '13075', '9488873007', '3748', 'ghmjude@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1077, 'user', '13077', '7598088557', '3578', 'hannieltech@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1078, 'user', '13085', '9043752530', '3341', 'testorsfile@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1079, 'user', '13126', '9487822218', '3485', 'tutytech@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1080, 'user', '13127', '8300101011', '1710', 'achusenthil001@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1081, 'user', '13128', '9940527561', '2376', 'mailtokairos@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1082, 'user', '13129', '8428521393', '2392', 'selvad.1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1083, 'user', '13145', '9791950080', '3808', 'mannusalwarestaurant@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1084, 'user', '13151', '9790053903', '3907', 'suncomputerstnvl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1085, 'user', '13196', '7603977784', '3904', 'deetechnologies@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1086, 'user', '13200', '9965865904', '4089', 'suresh@binaryclouds.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1087, 'user', '13207', '9842169287', '4098', 'suria@landmarksms.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1088, 'user', '13211', '9842169287', '4099', 'suria@landmarksms.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1089, 'user', '13222', '9940040075', '3967', 'rameshkumartextileshr@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1090, 'user', '13255', '6382579303', '4109', 'attralinfo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1091, 'user', '13261', '9566677733', '4083', 'mescopes@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1092, 'user', '13267', '9092623266', '4125', 'srsenterprises3266@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1093, 'user', '13291', '9535113141', '4116', 'Vinay.j@houseofkarnataka.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1094, 'user', '13304', '8754810234', '3387', 'sbi.71252@sbi.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1095, 'user', '13338', '9884299971', '4119', 'info@spiderindia.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1096, 'user', '13343', '9789451665', '3814', 'arunvitto@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:18', '2026-08-24 09:01:18', NULL),
(1097, 'user', '13361', '9940103571', '2452', 'govind.pgr2011@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1098, 'user', '13381', '9894346627', '3979', 'rsoftmadurai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1099, 'user', '13382', '9894346627', '3639', 'rsoftmadurai@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1100, 'user', '13394', '7810956576', '4150', 'test@123.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1101, 'user', '13395', '7200752556', '4163', 'test@123.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1102, 'user', '13396', '9884454228', '4170', 'info@test.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1103, 'user', '13412', '9842856048', '3935', 'jaissoftware@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1104, 'user', '13422', '7200827222', '3703', 'dev@crystalcodelabs.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1105, 'user', '13441', '8508902131', '4160', 'tanfediasection@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1106, 'user', '13465', '9961691699', '3204', 'reshmasivan2737@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1107, 'user', '13466', '9961691699', '3203', 'admin@b2businesssolution.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1108, 'user', '13468', '8848578847', '3384', 'admin@b2businesssolution.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1109, 'user', '13471', '8848577707', '1356', 'sales@pingme.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1110, 'user', '13473', '8848578847', '3241', 'southshoreent@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1111, 'user', '13475', '9443427000', '3664', 'info@viosmart.co.in', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1112, 'user', '13477', '8606387830', '4027', 'jissanto@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1113, 'user', '13479', '9443167405', '2148', 'velsoft@onspassh.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:19', '2026-08-24 09:01:19', NULL),
(1114, 'user', '128334', '7072656146', '103434', '8037043231', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:01:51', '2026-08-24 09:01:51', NULL),
(1116, 'user', 'shanmuga sundari', 'Erixon', '7092741147', 'dataanalyst.erixon@gmail.com', '7845131147', '55,masi street, Alwarthirunagari,Thoothukudi', 'Alwarthirunagari', 'Tamil Nadu', 'India', '628612', NULL, NULL, NULL, NULL, 8, 1, 0.00, NULL, NULL, NULL, NULL, '2026-08-24 09:04:54', '2026-08-25 05:24:51', NULL),
(1117, 'reseller', 'suriya', 'ss', '9852365211', 'surya@gmail.com', NULL, 'gghfh', 'ghgfh', 'gfhgfh', 'ghgfhg', 'gfhgfh', 1, 2, NULL, NULL, 1, 0, 0.00, NULL, NULL, '{\"followup_date\":null}', NULL, '2026-08-25 11:33:08', '2026-09-23 13:39:19', NULL),
(1119, 'user', 'dfdsfds', NULL, '8412352136', NULL, NULL, NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, 1, 1, 0.00, NULL, NULL, '{\"aadhar_number\":null,\"category\":null,\"terms_and_condition\":\"1\",\"assign_to\":\"gfgfdgfdgf\"}', NULL, '2026-09-07 15:29:39', '2026-09-07 15:29:39', NULL),
(1120, 'user', 'Harish', 'Textile', '9845672354', 'harish123@gmail.com', NULL, '7/9934/8 , vinagar street', 'Trichy', 'Tamil nadu', 'India', NULL, 1, 6, NULL, NULL, 1, 1, 0.00, NULL, NULL, '{\"aadhar_number\":null,\"category\":null,\"terms_and_condition\":\"1\",\"assign_to\":\"shanmuga sundari\"}', NULL, '2026-09-07 15:44:57', '2026-09-07 15:46:15', NULL),
(1121, 'user', 'ajith', NULL, '8523694521', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 0, 0.00, NULL, NULL, '{\"category\":\"electronics\"}', NULL, '2026-09-10 13:48:21', '2026-09-10 13:53:35', '2026-09-10 13:53:35'),
(1124, 'user', 'ajiths', NULL, '8523694591', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, 0.00, NULL, NULL, '{\"category\":\"fashion\",\"aa\":\"0\"}', NULL, '2026-09-10 14:52:49', '2026-09-10 14:52:49', NULL),
(1126, 'user', 'Muthu', NULL, '7604931311', NULL, NULL, NULL, NULL, 'Tamil Nadu', 'India', NULL, NULL, NULL, NULL, NULL, 2, 1, 0.00, NULL, NULL, '{\"remarks\":\"\",\"aa\":\"0\",\"number_field\":\"987654310\",\"category\":null,\"text_filed\":null,\"text_area\":null,\"date\":null}', NULL, '2026-09-10 15:19:00', '2026-09-15 17:08:45', NULL),
(1127, 'reseller', 'raj', 'Lap', '9879543258', 'rohit@gmail.com', '8098765432', 'Dindigul', 'mdu', 'Tamil Nadu', 'India', '624059', 2, 6, NULL, NULL, 2, 1, 0.00, NULL, NULL, '{\"category\":\"fashion\",\"aa\":\"1\",\"date\":\"2026-09-10\",\"text_filed\":\"muth\",\"number_field\":\"58888\",\"text_area\":\"test\",\"remarks\":\"hhh\"}', NULL, '2026-09-10 15:31:29', '2026-09-10 15:51:11', NULL),
(1128, 'user', 'muthu', 'textile', '9876543258', 'text@gmail.com', '8098765445', 'chn', 'mdu', 'Tamil Nadu', 'India', '90852', 2, 6, NULL, NULL, 2, 1, 0.00, NULL, NULL, '{\"category\":\"fashion\",\"number_field\":\"6098765432\",\"text_filed\":\"no\",\"text_area\":\"bye bye\",\"date\":\"2026-09-10\",\"aa\":\"0\",\"remarks\":\"kkkkkk\"}', NULL, '2026-09-10 15:56:10', '2026-09-10 16:28:13', NULL),
(1130, 'user', 'Ajith', 'ABC Pvt Ltd', '9655209482', 'ajith@example.com', '9876543210', 'No 10, Anna Nagar', 'Chennai', 'Tamil Nadu', 'India', '600040', 3, 5, NULL, NULL, 2, 1, 5900.00, NULL, NULL, '{\"category\":\"electronics\",\"number_field\":\"8529631235\",\"remarks\":\"Test Customer\",\"aa\":\"0\",\"text_filed\":null,\"text_area\":null,\"date\":null}', NULL, '2026-09-10 16:25:09', '2026-09-16 16:15:17', NULL),
(1131, 'user', 'tes', NULL, '9489042089', NULL, NULL, NULL, NULL, NULL, 'India', NULL, 7, 12, NULL, NULL, 1, 1, 0.00, NULL, NULL, '{\"category\":null,\"text_filed\":null,\"number_field\":\"8523698521\",\"text_area\":null,\"date\":null,\"aa\":\"1\"}', NULL, '2026-09-15 10:49:34', '2026-09-15 10:49:34', NULL),
(1132, 'user', 'maha', NULL, '8089028830', NULL, NULL, NULL, NULL, 'Tamil Nadu', 'India', NULL, 2, 2, NULL, NULL, 2, 1, 0.00, NULL, NULL, '{\"aa\":\"1\",\"category\":\"electronics\",\"number_field\":\"8089028830\",\"text_filed\":null,\"text_area\":null,\"date\":null,\"remarks\":\"\"}', NULL, '2026-09-21 12:19:25', '2026-09-21 12:19:25', NULL),
(1133, 'user', 'vishnu', 'Erixon Network', '8778188245', 'erixonsales@gmail.com', NULL, NULL, NULL, 'Tamil Nadu', 'India', NULL, 6, 10, NULL, NULL, 6, 1, 0.00, NULL, NULL, '{\"aa\":\"1\",\"category\":\"electronics\",\"text_filed\":\"Mahendra\",\"date\":\"2026-09-24\",\"number_field\":\"8778188245\",\"text_area\":null,\"remarks\":\"\"}', NULL, '2026-09-22 15:40:28', '2026-09-22 15:40:28', NULL),
(1134, 'user', 'ajisajis', NULL, '9631234123', 'ajisajis@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 0.00, NULL, NULL, NULL, NULL, '2026-09-23 12:59:02', '2026-09-23 12:59:02', NULL),
(1135, 'user', 'Harikaran', 'Santha Jewellery', '900800556', 'santhajewellery2018@gmail.com', '9550482345', '5/91,\r\n3rd floor, \r\nNew Bus stand near,', 'Tirunelveli', 'Tamil nadu', 'India', '627007', 1, 1, NULL, NULL, 1, 1, 0.00, NULL, NULL, '{\"followup_date\":\"2026-09-24\"}', NULL, '2026-09-23 13:27:32', '2026-09-23 13:33:44', NULL),
(1136, 'reseller', 'Jane Smith', 'XYZ Solutions', '9123456780', 'jane@example.com', NULL, '456 Cross Rd', 'Madurai', 'Tamil Nadu', 'India', '625001', NULL, NULL, NULL, NULL, 1, 1, 0.00, NULL, NULL, NULL, NULL, '2026-09-23 13:30:20', '2026-09-23 16:06:16', NULL),
(1138, 'reseller', 'manimala', NULL, '9087542578', NULL, NULL, NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, 1, 1, 0.00, NULL, NULL, NULL, NULL, '2026-09-23 13:50:31', '2026-09-23 13:50:31', NULL),
(1139, 'user', 'harini', NULL, '9876532356', NULL, NULL, NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, 1, 1, 0.00, NULL, NULL, '{\"followup_date\":\"2026-09-26\",\"sale_closed_date\":\"2026-09-16\",\"customer_created_date\":\"2026-09-16\"}', NULL, '2026-09-23 13:50:31', '2026-09-23 13:56:30', NULL),
(1140, 'reseller', 'vishal', 'RK Automobiles', '8895623456', 'rkautomobiles@gmail.com', '876543239', NULL, 'Trichy', NULL, 'India', NULL, 6, 6, NULL, NULL, 1, 1, 0.00, NULL, NULL, '{\"followup_date\":\"2026-09-25\",\"sale_closed_date\":\"2026-09-22\",\"customer_created_date\":\"2026-09-23\"}', NULL, '2026-09-23 13:50:31', '2026-09-23 13:56:03', NULL);
INSERT INTO `customers` (`customer_id`, `customer_type`, `name`, `company_name`, `mobile`, `email`, `alternate_mobile`, `address`, `city`, `state`, `country`, `pincode`, `owner_by`, `assign_by`, `lead_requirement_id`, `lead_stage_id`, `created_by`, `status`, `credit_balance`, `password`, `reference_code`, `custom_fields`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1142, 'user', 'dhoni', NULL, '8418523621', NULL, NULL, NULL, NULL, NULL, 'India', NULL, NULL, NULL, 7, 7, 1, 1, 0.00, NULL, NULL, '{\"followup_date\":null,\"sale_closed_date\":\"2026-09-24\",\"customer_created_date\":\"2026-09-17\"}', NULL, '2026-09-24 04:13:50', '2026-09-24 04:13:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_custom_fields`
--

CREATE TABLE `customer_custom_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(255) NOT NULL,
  `field_options` text DEFAULT NULL,
  `is_required` varchar(255) NOT NULL DEFAULT 'No',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_custom_fields`
--

INSERT INTO `customer_custom_fields` (`id`, `field_label`, `field_name`, `field_type`, `field_options`, `is_required`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Followup Date', 'followup_date', 'Date', NULL, 'No', 1, 0, '2026-09-23 13:28:23', '2026-09-23 13:28:23'),
(2, 'Sale Closed Date', 'sale_closed_date', 'Date', NULL, 'Yes', 1, 0, '2026-09-23 13:53:15', '2026-09-23 13:53:15'),
(3, 'Customer Created Date', 'customer_created_date', 'Date', NULL, 'Yes', 1, 0, '2026-09-23 13:53:46', '2026-09-23 13:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `demo_processes`
--

CREATE TABLE `demo_processes` (
  `demo_process_id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(30) DEFAULT NULL,
  `lead_source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_requirement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `demo_date` date DEFAULT NULL,
  `demo_time` varchar(20) DEFAULT NULL,
  `customer_type` varchar(100) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `sub_assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Pending','Finished') NOT NULL DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `custom_fields` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `demo_processes`
--

INSERT INTO `demo_processes` (`demo_process_id`, `customer_name`, `customer_phone`, `lead_source_id`, `lead_requirement_id`, `demo_date`, `demo_time`, `customer_type`, `created_by`, `assigned_by`, `sub_assigned_by`, `status`, `remarks`, `custom_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '10003', '2707', NULL, 3, '2026-09-16', '23:00', 'user', 1, 5, 12, 'Finished', NULL, NULL, '2026-09-10 09:56:21', '2026-09-16 12:56:12', NULL),
(2, '10008', '2912', NULL, 5, '2026-09-16', '14:00', 'user', 1, 5, NULL, 'Pending', NULL, NULL, '2026-09-16 13:00:06', '2026-09-16 13:07:11', '2026-09-16 13:07:11'),
(3, '10011', '2916', NULL, 1, '2026-09-16', '14:09', 'user', 6, 5, 12, 'Finished', NULL, NULL, '2026-09-16 13:10:32', '2026-09-16 13:13:52', NULL),
(4, 'ajis Kumar', '9876543210', 1, 1, '2026-09-22', '11:30 AM', 'New Customer', 2, 2, 3, 'Finished', 'Product demo scheduled for CRM software via Google Meet', '[]', '2026-09-21 16:23:55', '2026-09-23 11:10:26', NULL),
(5, 'Ravi Kumar', '9876543210', 1, 1, '2026-09-22', '11:30 AM', 'New Customer', 2, 2, 3, 'Finished', 'Product demo scheduled for CRM software via Google Meet', '[]', '2026-09-23 10:15:01', '2026-09-23 11:01:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `demo_process_custom_fields`
--

CREATE TABLE `demo_process_custom_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(255) NOT NULL,
  `field_options` text DEFAULT NULL,
  `is_required` varchar(255) NOT NULL DEFAULT 'No',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `custom_fields` longtext DEFAULT NULL,
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

INSERT INTO `followups` (`followups_id`, `lead_id`, `followup_type`, `duration`, `remarks`, `next_followup_date`, `custom_fields`, `followup_status`, `forward_to`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 10, 'Call', '5 minutes', NULL, '2026-09-18 10:30:00', '{\"whatsapp\":\"6622\"}', 'Completed', 2, 2, '2026-09-17 15:11:29', '2026-09-18 15:09:26', NULL),
(2, 10, 'Call', '00:44', NULL, '2026-09-26 11:00:00', NULL, 'Pending', 3, 2, '2026-09-18 15:09:26', '2026-09-18 15:09:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `followup_custom_fields`
--

CREATE TABLE `followup_custom_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(255) NOT NULL,
  `field_options` text DEFAULT NULL,
  `is_required` varchar(255) NOT NULL DEFAULT 'No',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(2, 1, 3, 2, 1, 'Reassigned due to staff leave', '2026-08-18 04:19:53', '2026-08-18 04:19:53'),
(3, 1, 3, 2, 1, 'Reassigned due to staff leave', '2026-09-18 14:52:22', '2026-09-18 14:52:22');

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
(1, 'EzeCRM', 'uploads/settings/logo_1788768052_3ec440b9.png', 'uploads/settings/favicon_1788768086_4ed16e80.png', '8610747034', '#df6b0c', '2026-08-07 00:49:16', '2026-09-07 13:31:26');

-- --------------------------------------------------------

--
-- Table structure for table `incentives`
--

CREATE TABLE `incentives` (
  `incentive_id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `month` varchar(20) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `incentives`
--

INSERT INTO `incentives` (`incentive_id`, `staff_id`, `month`, `amount`, `remarks`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, '2026-08', 5000.00, 'Performance Bonus', 1, '2026-08-26 07:16:37', '2026-08-26 07:16:37', '2026-08-26 07:16:37'),
(2, 2, '2026-08', 2000.00, 'employee of the month', 1, '2026-08-26 07:20:23', '2026-08-26 07:20:23', NULL);

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
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_type` varchar(255) NOT NULL DEFAULT 'user',
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `lead_title` varchar(255) NOT NULL,
  `lead_source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_requirement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `expected_amount` decimal(12,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `custom_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_fields`)),
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

INSERT INTO `leads` (`lead_id`, `customer_id`, `customer_name`, `customer_type`, `mobile`, `email`, `lead_title`, `lead_source_id`, `lead_stage_id`, `lead_requirement_id`, `assigned_to`, `priority`, `expected_amount`, `description`, `custom_fields`, `next_followup_date`, `status`, `lost_reason_id`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1130, 'Ajith', 'user', '9655209482', 'ajith@example.com', 'Website Development Project', 2, 1, 3, 6, 'high', 50000.00, 'Customer requires a Laravel-based CRM application.', '{\"created_by\":\"2\",\"mobile_number\":8523218523,\"customer_type\":\"user\"}', '2026-08-18', 1, 1, 1, '2026-08-13 01:49:42', '2026-09-11 16:09:13', NULL),
(2, 2, 'ajis', 'user', '8521239632', NULL, 'food app', 6, 1, 5, 2, 'medium', NULL, NULL, NULL, '2026-08-19', 1, NULL, 1, '2026-08-18 05:23:26', '2026-09-11 11:56:10', '2026-09-11 11:56:10'),
(3, 3, 'deepika', 'user', '9653247890', NULL, 'textile', 3, 3, 1, 6, 'urgent', 5000.00, NULL, NULL, '2026-08-26', 1, NULL, 4, '2026-08-21 06:51:16', '2026-08-25 05:29:31', NULL),
(4, 1116, 'shanmuga sundari', 'user', '7092741147', 'dataanalyst.erixon@gmail.com', 'Fashion design', 5, 2, 2, 8, 'medium', 500000.00, 'shared proposal', '{\"gst_number\":\"76985566ttggggg\",\"category\":\"electronics\",\"detailed_address\":\"FGDGFDGFDGFDG\",\"agreement_date\":\"2026-08-29\",\"interested_in_product\":\"0\"}', '2026-08-25', 1, 3, 8, '2026-08-24 09:08:41', '2026-08-29 05:19:11', NULL),
(7, 139, '1783', 'user', '788', 'antsolution046@gmail.com', 'jewellery', 6, 2, 6, 12, 'high', NULL, NULL, '{\"created_by\":\"sundari (hr)\",\"mobile_number\":\"9563423412\",\"customer_type\":\"User\"}', '2026-09-09', 1, NULL, 1, '2026-09-08 16:45:50', '2026-09-08 16:50:12', NULL),
(8, 1, 'ajis', 'user', '9489042085', 'ajis@gmail.com', 'Website Development Project', 2, 1, 3, 6, 'high', 50000.00, 'Customer requires a Laravel-based CRM application.', '{\"created_by\":\"2\",\"mobile_number\":8523218523,\"customer_type\":\"user\"}', '2026-09-20', 0, NULL, 2, '2026-09-11 11:13:32', '2026-09-11 11:17:52', '2026-09-11 11:17:52'),
(9, 1130, 'Ajith', 'user', '9655209482', 'ajith@example.com', 'Website Development Project', 2, 1, 3, 5, 'high', 50000.00, 'Customer requires a Laravel-based CRM application.', '{\"created_by\":\"2\",\"mobile_number\":8523218523,\"customer_type\":\"user\"}', '2026-09-20', 1, 1, 2, '2026-09-11 11:22:29', '2026-09-11 12:10:01', NULL),
(10, 1126, 'Muthu', 'user', '7604931311', NULL, 'App development', 5, 1, 12, 3, 'high', 50000.00, 'Mobile app', '{\"customer_type\":\"reseller\",\"mobile_number\":9787698254,\"created_by\":\"muthu\",\"expext_date\":\"2026-09-11\"}', '2026-09-26', 1, 3, 2, '2026-09-11 11:54:39', '2026-09-18 15:09:26', NULL),
(11, 1130, 'Ajith', 'user', '9655209482', 'ajith@example.com', 'kkk', 1, 6, 1, 2, 'medium', 0.00, NULL, '{\"customer_type\":\"Reseller\",\"expext_date\":\"2026-09-11\",\"mobile_number\":258}', NULL, 1, 1, 2, '2026-09-11 12:29:02', '2026-09-11 12:31:06', '2026-09-11 12:31:06'),
(12, 1127, 'raj', 'reseller', '9879543258', 'rohit@gmail.com', 'SmsBulk', 7, 6, 1, 3, 'medium', 20000.00, NULL, '{\"customer_type\":\"Reseller\",\"mobile_number\":9879543258,\"expext_date\":\"2026-09-12\"}', '2026-09-23', 1, 4, 2, '2026-09-12 11:28:59', '2026-09-12 11:28:59', NULL),
(13, 1134, 'ajisajis', 'user', '9631234123', 'ajisajis@gmail.com', 'Fashion design', 4, 7, 4, 2, 'medium', 2500.00, 'dsadsadsad', '[]', '2026-09-24', 1, NULL, 1, '2026-09-23 12:59:02', '2026-09-23 12:59:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_custom_fields`
--

CREATE TABLE `lead_custom_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(255) NOT NULL,
  `field_options` text DEFAULT NULL,
  `is_required` varchar(255) NOT NULL DEFAULT 'No',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 'ID Proof', 'WhatsApp Image 2026-08-14 at 11.53.04 AM (2).jpeg', 'uploads/lead_documents/1787046386_WhatsApp_Image_2026-08-14_at_11.53.04_AM_(2).jpeg', 1, '2026-08-18 04:16:26', '2026-08-18 04:16:26', NULL),
(2, 3, 'Proposal / Quote', 'Professional_Data_Analyst_Project_Template.xlsx', 'uploads/lead_documents/1787295377_Professional_Data_Analyst_Project_Template.xlsx', 6, '2026-08-21 06:56:17', '2026-08-21 06:56:17', NULL);

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
(1, 'Bulk Whatsapp ( Dp)', 1, '2026-08-13 01:44:42', '2026-09-05 11:51:52', NULL),
(2, 'Bulk Whatsapp ( Button)', 1, '2026-08-13 01:44:51', '2026-09-05 11:51:28', NULL),
(3, 'Bulk SMS ( Transcational)', 1, '2026-08-13 01:45:00', '2026-09-05 11:51:06', NULL),
(4, 'Website', 1, '2026-08-13 01:45:08', '2026-08-13 01:45:08', NULL),
(5, 'Bulk Voicecall', 1, '2026-08-13 01:45:16', '2026-09-05 11:50:11', NULL),
(6, 'Bulk SMS (Promotional)', 1, '2026-08-13 01:45:24', '2026-09-05 11:50:33', NULL),
(7, 'Bulk Mail', 1, '2026-09-05 11:52:08', '2026-09-05 11:52:08', NULL),
(8, 'Missed Call', 1, '2026-09-05 11:52:16', '2026-09-05 11:52:16', NULL),
(9, 'Verified Whatsapp', 1, '2026-09-05 11:52:31', '2026-09-05 11:52:31', NULL),
(10, 'IVR', 1, '2026-09-05 11:52:39', '2026-09-05 11:52:39', NULL),
(11, 'Digital marketing ( fb / instragram)', 1, '2026-09-05 11:53:19', '2026-09-05 11:53:19', NULL),
(12, 'crm', 1, '2026-09-05 11:53:26', '2026-09-05 11:53:26', NULL),
(13, 'None', 1, '2026-09-12 13:48:09', '2026-09-12 16:55:12', '2026-09-12 16:55:12');

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
(3, 'Product Campaign', 1, '2026-08-13 01:41:55', '2026-09-05 11:47:29', NULL),
(4, 'Website', 1, '2026-08-13 01:42:04', '2026-08-13 01:42:04', NULL),
(5, 'Verified WhatsApp enquiry', 1, '2026-08-13 01:42:13', '2026-09-05 11:47:12', NULL),
(6, 'Meta', 1, '2026-08-13 01:42:20', '2026-09-05 11:46:45', NULL),
(7, 'New Data', 1, '2026-09-05 11:47:48', '2026-09-05 11:47:48', NULL);

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
(4, 'Converted', 0, 1, '2026-08-13 01:44:06', '2026-09-05 11:49:19', '2026-09-05 11:49:19'),
(5, 'Customer Call Back', 4, 1, '2026-08-13 01:44:19', '2026-09-05 11:48:57', NULL),
(6, 'Sale closed', 0, 1, '2026-09-05 11:49:06', '2026-09-05 11:49:06', NULL),
(7, 'Interested', 0, 1, '2026-09-07 10:47:45', '2026-09-07 10:47:45', NULL);

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
(2, 2, '2026-08-20', '2026-08-20', 1.00, 'Casual Leave', 'personal reason', 'Approved', 1, NULL, '2026-08-19 06:07:31', '2026-08-19 06:07:53', NULL),
(3, 6, '2026-08-22', '2026-08-23', 2.00, 'Sick Leave', 'fever', 'Rejected', 4, 'this month you take so many leaves', '2026-08-21 07:02:02', '2026-08-21 07:03:54', NULL),
(4, 6, '2026-08-21', '2026-08-22', 2.00, 'Sick Leave', 'fever', 'Approved', 4, 'okay carry on', '2026-08-21 07:05:45', '2026-08-21 07:07:04', NULL),
(5, 3, '2026-08-28', '2026-08-31', 3.00, 'Casual Leave', 'going to kerala family function', 'Pending', NULL, NULL, '2026-08-27 11:07:02', '2026-08-27 11:07:02', NULL);

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
(4, 'Purchased Elsewhere', 1, '2026-08-13 01:46:12', '2026-08-13 01:46:12', NULL),
(5, 'Ringing Not Response', 1, '2026-09-05 11:55:14', '2026-09-05 11:55:14', NULL),
(6, 'None', 1, '2026-09-12 16:55:05', '2026-09-12 16:55:05', NULL);

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
(30, '2026_08_20_000002_remove_deleted_at_from_call_logs_table', 10),
(31, '2026_08_21_000001_create_credit_requests_table', 11),
(32, '2026_08_21_000002_create_payments_table', 11),
(33, '2026_08_21_000003_add_credit_balance_to_customers_table', 11),
(34, '2026_08_21_000004_create_permission_requests_table', 11),
(35, '2026_08_26_000001_add_new_staff_fields_to_users_table', 12),
(36, '2026_08_29_000001_create_lead_custom_fields_table', 13),
(37, '2026_08_29_000002_add_lead_list_columns_to_referral_settings_table', 14),
(38, '2026_08_29_000003_create_customer_custom_fields_table', 15),
(39, '2026_08_29_000004_add_custom_fields_to_customers_table', 16),
(40, '2026_08_29_000005_add_customer_list_columns_to_referral_settings_table', 17),
(41, '2026_08_26_000002_create_incentives_table', 18),
(42, '2026_08_26_000003_add_owner_by_and_assign_by_to_customers_table', 18),
(43, '2026_08_26_000004_add_session_fields_to_attendance_table', 18),
(44, '2026_08_27_000001_create_notifications_table', 18),
(45, '2026_08_31_000001_create_followup_custom_fields_table', 18),
(46, '2026_08_31_000002_add_location_fields_to_attendance_table', 18),
(47, '2026_09_02_000001_create_credit_request_custom_fields_table', 18),
(48, '2026_09_02_000002_add_lead_id_to_credit_requests_table', 18),
(49, '2026_09_02_000003_create_coordinations_table', 18),
(50, '2026_09_03_000001_fix_credit_requests_foreign_keys', 18),
(51, '2026_09_03_000002_create_coordination_joining_staff_table', 18),
(52, '2026_09_03_000003_create_demo_processes_table', 18),
(53, '2026_09_03_000004_create_demo_process_custom_fields_table', 18),
(54, '2026_09_08_000001_add_lead_requirement_id_to_demo_processes_table', 18),
(55, '2026_09_10_112218_add_sessions_to_attendance_table', 18),
(56, '2026_09_10_153500_add_status_to_users_table', 18),
(57, '2026_09_10_160000_add_lead_requirement_id_to_credit_requests_table', 18),
(58, '2026_09_15_000001_create_salary_adjustments_table', 18),
(59, '2026_09_15_000002_add_title_to_coordinations_table', 18),
(60, '2026_09_16_000003_add_call_recording_mobile_fields', 18),
(61, '2026_09_16_000004_add_call_source_to_call_logs_table', 18),
(62, '2026_09_17_000001_create_audit_logs_table', 18),
(63, '2026_09_18_000001_add_fcmtoken_to_users_table', 18),
(64, '2026_09_18_000002_update_credit_requests_status_column', 18),
(65, '2026_09_22_000001_add_lead_requirement_id_to_payments_table', 18),
(66, '2026_09_23_113125_add_customer_fields_to_leads_table', 18),
(67, '2026_09_24_000001_add_lead_requirement_and_stage_to_customers_table', 18);

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
(1, 'App\\Models\\User', 7),
(1, 'App\\Models\\User', 10),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 11),
(3, 'App\\Models\\User', 12),
(4, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 8),
(5, 'App\\Models\\User', 5);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('0670a6a5-e38d-4322-8d85-554a0c33fd28', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for Ravi Kumar is pending. Demo Date: 22\\/09\\/2026, Timing: 11:30 AM.\",\"demo_process_id\":5,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-23 10:38:36', '2026-09-23 10:15:01', '2026-09-23 10:38:36'),
('077101bd-8d76-44d0-9612-10f3f58be815', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for 10011 is pending. Demo Date: 16\\/09\\/2026, Timing: 14:09.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Pending\"}', '2026-09-17 12:28:10', '2026-09-16 13:10:32', '2026-09-17 12:28:10'),
('08e017db-7cd0-4df7-913e-74801ef00007', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for 10008 (2912). Product: Bulk Voicecall. Date: 16\\/09\\/2026, Timing: 14:00. Created By: Ezecrm - Sundari (Admin).\",\"demo_process_id\":2,\"customer_name\":\"10008\",\"status\":\"Pending\"}', '2026-09-17 12:28:20', '2026-09-16 13:00:06', '2026-09-17 12:28:20'),
('0d06fa30-efb9-4ff6-a0e5-d479ef145a26', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for Ravi Kumar (9876543210). Product: Bulk Whatsapp ( Dp). Date: 22\\/09\\/2026, Timing: 11:30 AM. Created By: Tharik.\",\"demo_process_id\":4,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-21 16:32:16', '2026-09-21 16:23:55', '2026-09-21 16:32:16'),
('100383fc-6812-4585-b4ca-40f5b1aa6c8a', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for 10003 has been completed successfully.\",\"demo_process_id\":1,\"customer_name\":\"10003\",\"status\":\"Finished\"}', '2026-09-17 12:28:26', '2026-09-16 12:56:12', '2026-09-17 12:28:26'),
('13b2ff2f-7951-46bb-86f7-20b7e9c22d4c', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-21 16:32:16', '2026-09-21 16:27:54', '2026-09-21 16:32:16'),
('15527990-8926-496d-aed4-468a222cb3f3', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 6, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for 10011 has been completed successfully.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Finished\"}', '2026-09-17 12:28:04', '2026-09-16 13:13:52', '2026-09-17 12:28:04'),
('17006b11-9e60-4058-ad70-77fa7151d4f8', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for Ravi Kumar (9876543210). Product: Bulk Whatsapp ( Dp). Date: 22\\/09\\/2026, Timing: 11:30 AM. Created By: Tharik.\",\"demo_process_id\":5,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-23 10:38:36', '2026-09-23 10:15:01', '2026-09-23 10:38:36'),
('20b68b97-8dc2-4070-9bbe-f17a029113fa', 'App\\Notifications\\CreditRequestApprovedByProductManager', 'App\\Models\\User', 5, '{\"title\":\"Credit Request Approved by Product Manager\",\"message\":\"Product Manager has approved the credit request of \\u20b95,900.00 for 10003. Credit has been added to customer balance.\",\"credit_request_id\":10,\"status\":\"Credit Added\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 15:00:43', '2026-09-23 14:31:17', '2026-09-23 15:00:43'),
('224e3cfc-6545-4f55-9b3c-b3365eadb999', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:08:54', '2026-09-23 11:17:17'),
('23ec4114-424e-429b-96af-1d66db478d9f', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 2, '{\"title\":\"Credit Request Approved by Admin\",\"message\":\"Your credit request of \\u20b90.00 for Ajith has been approved by Super Admin and forwarded to Product Manager.\",\"credit_request_id\":3,\"status\":\"Forwarded to Product Manager\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 11:52:13', '2026-09-23 13:05:38'),
('2498a06f-778b-439a-a986-13d097e18f14', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for 10011 (2916). Product: Bulk Whatsapp ( Dp). Date: 16\\/09\\/2026, Timing: 14:09. Created By: Shanmuga Sundari.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Pending\"}', '2026-09-17 12:28:11', '2026-09-16 13:10:32', '2026-09-17 12:28:11'),
('250b594f-2466-41e1-bf32-ea97779f9d7d', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 1, '{\"title\":\"New Credit Request Pending Approval\",\"message\":\"Tharik submitted a credit request of \\u20b95,000.00 for ajiths (Pending Admin Approval).\",\"credit_request_id\":9,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 12:05:25', '2026-09-23 13:05:38'),
('2512f499-a0cb-4d68-a958-3a89d1c9a6a6', 'App\\Notifications\\CreditRequestApprovedByProductManager', 'App\\Models\\User', 5, '{\"title\":\"Credit Request Approved by Product Manager\",\"message\":\"Product Manager has approved the Credit Request. The approval process is completed.\",\"credit_request_id\":6,\"status\":\"Credit Added\"}', '2026-09-17 12:27:56', '2026-09-16 13:29:38', '2026-09-17 12:27:56'),
('2ba892da-1501-42e6-8bc0-316123afce28', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for Ravi Kumar has been completed successfully.\",\"demo_process_id\":5,\"customer_name\":\"Ravi Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:01:22', '2026-09-23 11:17:17'),
('30d523e1-34ba-4b70-a92c-93decad7afbc', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 2, '{\"title\":\"Credit Request Submitted\",\"message\":\"Your credit request of \\u20b95,000.00 for ajis has been submitted (Pending Admin Approval).\",\"credit_request_id\":8,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 11:20:01', '2026-09-23 13:05:38'),
('33b3dbc7-46dc-442b-8d9f-f30241e39012', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 5, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for 10003 has been completed successfully.\",\"demo_process_id\":1,\"customer_name\":\"10003\",\"status\":\"Finished\"}', '2026-09-17 12:28:25', '2026-09-16 12:56:12', '2026-09-17 12:28:25'),
('414c9a17-167f-4151-bc7b-4d578876c7da', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:08:45', '2026-09-23 11:17:17'),
('43c9e133-77f6-49d3-871d-a10cef610355', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:08:54', '2026-09-23 11:17:17'),
('469801a6-7b8e-45f5-b0e6-6a994d0fb7c3', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:08:47', '2026-09-23 11:17:17'),
('48c6d64b-ff87-4407-bb22-e0ca153f9508', 'App\\Notifications\\CreditRequestApprovedByProductManager', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Completed\",\"message\":\"Your credit request of \\u20b95,900.00 for 10003 has been approved by Product Manager and added to customer credit balance.\",\"credit_request_id\":10,\"status\":\"Credit Added\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 15:00:43', '2026-09-23 14:31:17', '2026-09-23 15:00:43'),
('4df1a85a-693f-486b-8e6b-8df8803a385e', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for Ravi Kumar (9876543210). Product: Bulk Whatsapp ( Dp). Date: 22\\/09\\/2026, Timing: 11:30 AM. Created By: Tharik.\",\"demo_process_id\":5,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-23 10:38:36', '2026-09-23 10:15:01', '2026-09-23 10:38:36'),
('5adade50-94fd-433e-9c3d-b8cd5444fa0e', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:08:54', '2026-09-23 11:17:17'),
('5bb80745-30a3-4b54-86cb-b9f47fcd593a', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for Ravi Kumar is pending. Demo Date: 22\\/09\\/2026, Timing: 11:30 AM.\",\"demo_process_id\":4,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-21 16:32:16', '2026-09-21 16:23:55', '2026-09-21 16:32:16'),
('5ca6a696-798d-45f8-894d-4a9808605d97', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 6, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for 10011 (2916). Product: Bulk Whatsapp ( Dp). Date: 16\\/09\\/2026, Timing: 14:09. Created By: Shanmuga Sundari.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Pending\"}', '2026-09-17 12:28:15', '2026-09-16 13:10:32', '2026-09-17 12:28:15'),
('5ce5310a-cb89-4b8f-b931-27cf21af581a', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for Ravi Kumar has been completed successfully.\",\"demo_process_id\":5,\"customer_name\":\"Ravi Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:01:22', '2026-09-23 11:17:17'),
('6082dfdc-8995-4137-9fe2-98039a4ce3ae', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 10, '{\"title\":\"New Credit Request Pending Approval\",\"message\":\"Tharik submitted a credit request of \\u20b95,000.00 for ajis (Pending Admin Approval).\",\"credit_request_id\":8,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 11:20:02', '2026-09-23 13:05:38'),
('62871db8-a5fd-447a-9346-6c3ed94c3c39', 'App\\Notifications\\CoordinationCreatedNotification', 'App\\Models\\User', 1, '{\"title\":\"Coordination Created\",\"message\":\"You have created coordination: \\\"demo Notification\\\".\",\"coordination_id\":4,\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/coordinations\",\"type\":\"coordination_created\"}', '2026-09-18 15:40:00', '2026-09-18 12:41:30', '2026-09-18 15:40:00'),
('64092832-cd67-448f-8503-e9e6edf98abd', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 5, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for 10011 has been completed successfully.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Finished\"}', '2026-09-17 12:28:05', '2026-09-16 13:13:52', '2026-09-17 12:28:05'),
('6707b44d-5fd7-4108-9e03-68f1f11fe8fe', 'App\\Notifications\\CoordinationAssignedNotification', 'App\\Models\\User', 2, '{\"title\":\"New Coordination Assigned\",\"message\":\"You have been added to coordination: \\\"demo Notification\\\" by Ezecrm - Sundari (Admin).\",\"coordination_id\":4,\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/coordinations\",\"type\":\"coordination_assigned\"}', '2026-09-18 12:48:27', '2026-09-18 12:41:30', '2026-09-18 12:48:27'),
('681edccb-98c5-4e25-824f-b1c2c1e7da9e', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 12, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for 10008 (2912). Product: Bulk Voicecall. Date: 16\\/09\\/2026, Timing: 14:00. Created By: Ezecrm - Sundari (Admin).\",\"demo_process_id\":2,\"customer_name\":\"10008\",\"status\":\"Pending\"}', '2026-09-17 12:28:21', '2026-09-16 13:00:06', '2026-09-17 12:28:21'),
('68f5bd35-868b-4a12-a994-2a9beef06980', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Approved by Admin\",\"message\":\"Your credit request of \\u20b95,900.00 for 10003 has been approved by Super Admin and forwarded to Product Manager.\",\"credit_request_id\":10,\"status\":\"Forwarded to Product Manager\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 15:00:43', '2026-09-23 14:26:27', '2026-09-23 15:00:43'),
('6a267233-27fe-4e98-a2b8-58b292c0520d', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:08:45', '2026-09-23 11:17:17'),
('6b4e507b-47e1-49a3-8965-e4baec1d2f16', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 5, '{\"title\":\"Credit Request Approved by Admin\",\"message\":\"Super Admin approved credit request of \\u20b90.00 for Ajith. Next, Product Manager approval is required.\",\"credit_request_id\":4,\"status\":\"Forwarded to Product Manager\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 11:17:17', '2026-09-23 11:14:31', '2026-09-23 11:17:17'),
('70dc6c57-c5d6-4138-9d36-c557fc0e00c7', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Approved\",\"message\":\"Super Admin has approved the Credit Request. Next, Product Manager approval is required.\",\"credit_request_id\":7,\"status\":\"Forwarded to Product Manager\"}', '2026-09-18 12:00:49', '2026-09-18 12:00:14', '2026-09-18 12:00:49'),
('765d2731-9280-4dec-89f1-62eb749f375d', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 1, '{\"title\":\"New Credit Request Pending Approval\",\"message\":\"Tharik submitted a credit request of \\u20b95,000.00 for ajis (Pending Admin Approval).\",\"credit_request_id\":8,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 11:20:02', '2026-09-23 13:05:38'),
('78b8b563-6f6b-49cf-bd9f-357dc90acc70', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 5, '{\"title\":\"Credit Request Approved\",\"message\":\"Super Admin has approved the Credit Request. Next, Product Manager approval is required.\",\"credit_request_id\":5,\"status\":\"Forwarded to Support\"}', '2026-09-15 12:02:05', '2026-09-15 12:01:57', '2026-09-15 12:02:05'),
('7a8c7660-5b84-4e7b-bc27-4552caa372d2', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 7, '{\"title\":\"New Credit Request Pending Approval\",\"message\":\"Tharik submitted a credit request of \\u20b95,000.00 for ajis (Pending Admin Approval).\",\"credit_request_id\":8,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 11:20:02', '2026-09-23 13:05:38'),
('7c331478-478c-4335-8b26-4635e298164e', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:10:26', '2026-09-23 11:17:17'),
('893a50d8-171a-4828-90ca-8e1b15ffaec0', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for 10011 has been completed successfully.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Finished\"}', '2026-09-17 12:28:06', '2026-09-16 13:13:52', '2026-09-17 12:28:06'),
('89f68f15-0d30-411c-b1e3-6bc65132475f', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 5, '{\"title\":\"Credit Request Approved by Admin\",\"message\":\"Super Admin approved credit request of \\u20b90.00 for Ajith. Next, Product Manager approval is required.\",\"credit_request_id\":3,\"status\":\"Forwarded to Product Manager\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 11:52:13', '2026-09-23 13:05:38'),
('8b0699d6-e0d0-460e-b78b-924f7db57989', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for 10008 is pending. Demo Date: 16\\/09\\/2026, Timing: 14:00.\",\"demo_process_id\":2,\"customer_name\":\"10008\",\"status\":\"Pending\"}', '2026-09-17 12:28:23', '2026-09-16 13:00:06', '2026-09-17 12:28:23'),
('8c56e934-fdd6-4a6b-a185-e77fb529de94', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 7, '{\"title\":\"New Credit Request Pending Approval\",\"message\":\"Ezecrm - Sundari (Admin) submitted a credit request of \\u20b95,900.00 for 10003 (Pending Admin Approval).\",\"credit_request_id\":10,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 15:00:43', '2026-09-23 14:26:10', '2026-09-23 15:00:43'),
('8cc33a20-7493-4d21-8943-a19455adc992', 'App\\Notifications\\CreditRequestApprovedByProductManager', 'App\\Models\\User', 2, '{\"title\":\"Credit Request Completed\",\"message\":\"Your credit request of \\u20b90.00 for Ajith has been approved by Product Manager and added to customer credit balance.\",\"credit_request_id\":3,\"status\":\"Credit Added\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 12:23:13', '2026-09-23 13:05:38'),
('8f52f343-2ccd-4a50-9c2e-438d055ea9f4', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for Ravi Kumar is pending. Demo Date: 22\\/09\\/2026, Timing: 11:30 AM.\",\"demo_process_id\":5,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-23 10:38:36', '2026-09-23 10:15:01', '2026-09-23 10:38:36'),
('8f6f3aeb-802d-496f-b3ef-76e7fd26f712', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for Ravi Kumar (9876543210). Product: Bulk Whatsapp ( Dp). Date: 22\\/09\\/2026, Timing: 11:30 AM. Created By: Tharik.\",\"demo_process_id\":5,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-23 10:38:36', '2026-09-23 10:15:01', '2026-09-23 10:38:36'),
('925a6956-ebed-4630-9ea2-166a1b669f45', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 12, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for 10003 has been completed successfully.\",\"demo_process_id\":1,\"customer_name\":\"10003\",\"status\":\"Finished\"}', '2026-09-17 15:38:03', '2026-09-16 12:56:12', '2026-09-17 15:38:03'),
('98653f8d-abc8-476d-bc9e-964521661c82', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-21 16:32:16', '2026-09-21 16:27:54', '2026-09-21 16:32:16'),
('9c48223b-6d3b-46b7-9a8b-ceb73af129ec', 'App\\Notifications\\CreditRequestApprovedByProductManager', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Approved by Product Manager\",\"message\":\"Product Manager has approved the Credit Request. The approval process is completed.\",\"credit_request_id\":6,\"status\":\"Credit Added\"}', '2026-09-17 12:27:57', '2026-09-16 13:29:38', '2026-09-17 12:27:57'),
('a080d954-9558-4aa2-a3c8-8016bb34176d', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 10, '{\"title\":\"New Credit Request Pending Approval\",\"message\":\"Ezecrm - Sundari (Admin) submitted a credit request of \\u20b95,900.00 for 10003 (Pending Admin Approval).\",\"credit_request_id\":10,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 15:00:43', '2026-09-23 14:26:10', '2026-09-23 15:00:43'),
('a1450c3d-2092-4fcb-b7ef-927091a9ac19', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:08:45', '2026-09-23 11:17:17'),
('a24b088b-f92f-4fee-976a-15cf826ddde4', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Approved\",\"message\":\"Super Admin has approved the Credit Request. Next, Product Manager approval is required.\",\"credit_request_id\":5,\"status\":\"Forwarded to Support\"}', '2026-09-15 12:02:06', '2026-09-15 12:01:57', '2026-09-15 12:02:06'),
('a39dc1fa-9c16-4545-915a-dd4abaaacf72', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for Ravi Kumar is pending. Demo Date: 22\\/09\\/2026, Timing: 11:30 AM.\",\"demo_process_id\":5,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-23 10:38:36', '2026-09-23 10:15:01', '2026-09-23 10:38:36'),
('a4b12114-37a9-453e-b8c1-463baf81114d', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:10:26', '2026-09-23 11:17:17'),
('a888f90c-337a-4a12-9a6a-c36cc7ad2458', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 12, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for 10008 is pending. Demo Date: 16\\/09\\/2026, Timing: 14:00.\",\"demo_process_id\":2,\"customer_name\":\"10008\",\"status\":\"Pending\"}', '2026-09-17 12:28:24', '2026-09-16 13:00:06', '2026-09-17 12:28:24'),
('aa8024ae-2ab1-45fe-b41f-3912e1404f12', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 5, '{\"title\":\"Credit Request Approved by Admin\",\"message\":\"Super Admin approved credit request of \\u20b95,900.00 for 10003. Next, Product Manager approval is required.\",\"credit_request_id\":10,\"status\":\"Forwarded to Product Manager\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 15:00:43', '2026-09-23 14:26:27', '2026-09-23 15:00:43'),
('ae73f58d-2cb9-48d5-af4c-b79742c4a5c7', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 5, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for 10011 (2916). Product: Bulk Whatsapp ( Dp). Date: 16\\/09\\/2026, Timing: 14:09. Created By: Shanmuga Sundari.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Pending\"}', '2026-09-17 12:28:18', '2026-09-16 13:10:32', '2026-09-17 12:28:18'),
('b3e79c3b-df9e-40cd-b92a-66c372778fb4', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 5, '{\"title\":\"Credit Request Approved\",\"message\":\"Super Admin has approved the Credit Request. Next, Product Manager approval is required.\",\"credit_request_id\":6,\"status\":\"Forwarded to Support\"}', '2026-09-17 12:28:01', '2026-09-16 13:28:56', '2026-09-17 12:28:01'),
('b43da4bd-0aad-4f8b-a3da-6d8533b3593a', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Approved\",\"message\":\"Super Admin has approved the Credit Request. Next, Product Manager approval is required.\",\"credit_request_id\":6,\"status\":\"Forwarded to Support\"}', '2026-09-17 12:28:03', '2026-09-16 13:28:56', '2026-09-17 12:28:03'),
('b8972a08-f137-45b6-912a-dda889f1280f', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:10:26', '2026-09-23 11:17:17'),
('bd340dc0-16f5-49ca-a15f-10b9134840f4', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Approved by Admin\",\"message\":\"Super Admin approved credit request of \\u20b90.00 for Ajith. Next, Product Manager approval is required.\",\"credit_request_id\":3,\"status\":\"Forwarded to Product Manager\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 11:52:13', '2026-09-23 13:05:38'),
('c1d630ac-0729-48e9-a054-59fa14f328f2', 'App\\Notifications\\CreditRequestApprovedByProductManager', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Approved by Product Manager\",\"message\":\"Product Manager has approved the credit request of \\u20b90.00 for Ajith. Credit has been added to customer balance.\",\"credit_request_id\":3,\"status\":\"Credit Added\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 12:23:13', '2026-09-23 13:05:38'),
('c35ad724-e55f-4831-b266-5046f1b35d1a', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:08:47', '2026-09-23 11:17:17'),
('c48fafa3-e5e0-4c0a-aecd-6ffb2d14a834', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for Ravi Kumar (9876543210). Product: Bulk Whatsapp ( Dp). Date: 22\\/09\\/2026, Timing: 11:30 AM. Created By: Tharik.\",\"demo_process_id\":4,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-21 16:32:16', '2026-09-21 16:23:55', '2026-09-21 16:32:16'),
('c5e4bedc-1c9b-4941-a71a-5b05edb12a6d', 'App\\Notifications\\CoordinationAssignedNotification', 'App\\Models\\User', 3, '{\"title\":\"New Coordination Assigned\",\"message\":\"You have been added to coordination: \\\"demo Notification\\\" by Ezecrm - Sundari (Admin).\",\"coordination_id\":4,\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/coordinations\",\"type\":\"coordination_assigned\"}', '2026-09-18 15:40:03', '2026-09-18 12:41:30', '2026-09-18 15:40:03'),
('c7c68845-8e69-42a1-9545-93f59e181fb0', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:08:47', '2026-09-23 11:17:17'),
('c8c13ffa-5ade-40b5-a267-f472085f1ebc', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Submitted\",\"message\":\"Your credit request of \\u20b95,900.00 for 10003 has been submitted (Pending Admin Approval).\",\"credit_request_id\":10,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 15:00:43', '2026-09-23 14:26:10', '2026-09-23 15:00:43'),
('c8c8a8b1-59c3-471a-9875-e4669506a1e9', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 1, '{\"title\":\"Credit Request Approved by Admin\",\"message\":\"Super Admin approved credit request of \\u20b90.00 for Ajith. Next, Product Manager approval is required.\",\"credit_request_id\":4,\"status\":\"Forwarded to Product Manager\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 11:17:17', '2026-09-23 11:14:31', '2026-09-23 11:17:17'),
('ceb72bab-a244-4f33-b9a6-4e130307b7db', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for 10003 (2707). Product: N\\/A. Date: 10\\/09\\/2026, Timing: 23:00. Created By: Ezecrm - Sundari (Admin).\",\"demo_process_id\":1,\"customer_name\":\"10003\",\"status\":\"Pending\"}', '2026-09-10 09:56:38', '2026-09-10 09:56:21', '2026-09-10 09:56:38'),
('d17acc43-971c-4ac8-a8d7-017a402e5799', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for Ravi Kumar is pending. Demo Date: 22\\/09\\/2026, Timing: 11:30 AM.\",\"demo_process_id\":4,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-21 16:32:16', '2026-09-21 16:23:55', '2026-09-21 16:32:16'),
('d7090f79-8824-4048-9c1c-d0b569921dd5', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 5, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for 10011 is pending. Demo Date: 16\\/09\\/2026, Timing: 14:09.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Pending\"}', '2026-09-17 12:28:17', '2026-09-16 13:10:32', '2026-09-17 12:28:17'),
('dc789c7b-3a90-4440-8459-bd5af6c46a50', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 6, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for 10011 is pending. Demo Date: 16\\/09\\/2026, Timing: 14:09.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Pending\"}', '2026-09-17 12:28:19', '2026-09-16 13:10:32', '2026-09-17 12:28:19'),
('e540c1cf-e174-4e7a-8e84-9d6c581deb5e', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 7, '{\"title\":\"New Credit Request Pending Approval\",\"message\":\"Tharik submitted a credit request of \\u20b95,000.00 for ajiths (Pending Admin Approval).\",\"credit_request_id\":9,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 12:05:25', '2026-09-23 13:05:38'),
('e59ef782-540a-4aa6-a987-7e4283bcc0f3', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 5, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for 10003 (2707). Product: N\\/A. Date: 10\\/09\\/2026, Timing: 23:00. Created By: Ezecrm - Sundari (Admin).\",\"demo_process_id\":1,\"customer_name\":\"10003\",\"status\":\"Pending\"}', '2026-09-10 09:56:41', '2026-09-10 09:56:21', '2026-09-10 09:56:41'),
('e8f2d819-4bd4-4421-954e-42be2efa8122', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 5, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for 10003 is pending. Demo Date: 10\\/09\\/2026, Timing: 23:00.\",\"demo_process_id\":1,\"customer_name\":\"10003\",\"status\":\"Pending\"}', '2026-09-10 09:56:42', '2026-09-10 09:56:21', '2026-09-10 09:56:42'),
('ec5cb415-790b-4fcb-970d-2d5dc99e51c6', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for Ravi Kumar is pending. Demo Date: 22\\/09\\/2026, Timing: 11:30 AM.\",\"demo_process_id\":4,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-21 16:32:16', '2026-09-21 16:23:55', '2026-09-21 16:32:16'),
('eea090e9-a344-4c2e-9b82-ff386165bd6c', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 12, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for 10011 has been completed successfully.\",\"demo_process_id\":3,\"customer_name\":\"10011\",\"status\":\"Finished\"}', '2026-09-17 12:28:13', '2026-09-16 13:13:52', '2026-09-17 12:28:13'),
('eed39aeb-486a-4b40-82d2-43feeec55a68', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 10, '{\"title\":\"New Credit Request Pending Approval\",\"message\":\"Tharik submitted a credit request of \\u20b95,000.00 for ajiths (Pending Admin Approval).\",\"credit_request_id\":9,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 12:05:25', '2026-09-23 13:05:38'),
('ef62566d-8ee7-45d9-93eb-5a6de8108e2a', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 2, '{\"title\":\"Credit Request Approved by Admin\",\"message\":\"Your credit request of \\u20b90.00 for Ajith has been approved by Super Admin and forwarded to Product Manager.\",\"credit_request_id\":4,\"status\":\"Forwarded to Product Manager\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 11:17:17', '2026-09-23 11:14:31', '2026-09-23 11:17:17'),
('f13b3a65-09e9-40fd-927f-695680c294d2', 'App\\Notifications\\DemoProcessPending', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Pending\",\"message\":\"Demo for 10003 is pending. Demo Date: 10\\/09\\/2026, Timing: 23:00.\",\"demo_process_id\":1,\"customer_name\":\"10003\",\"status\":\"Pending\"}', '2026-09-10 09:56:43', '2026-09-10 09:56:21', '2026-09-10 09:56:43'),
('f4281893-84bb-437b-bcfb-0c0127932094', 'App\\Notifications\\CreditRequestCreatedNotification', 'App\\Models\\User', 2, '{\"title\":\"Credit Request Submitted\",\"message\":\"Your credit request of \\u20b95,000.00 for ajiths has been submitted (Pending Admin Approval).\",\"credit_request_id\":9,\"status\":\"Pending Admin Approval\",\"url\":\"https:\\/\\/erixon.in\\/crm\\/admin\\/credit-requests\"}', '2026-09-23 13:05:38', '2026-09-23 12:05:25', '2026-09-23 13:05:38'),
('f6833f59-9d66-40a7-960a-c6de6a9109e5', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 3, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for ajis Kumar has been completed successfully.\",\"demo_process_id\":4,\"customer_name\":\"ajis Kumar\",\"status\":\"Finished\"}', '2026-09-21 16:32:16', '2026-09-21 16:27:54', '2026-09-21 16:32:16'),
('f979bb60-d402-42e5-8f3f-9282124534fe', 'App\\Notifications\\DemoProcessFinished', 'App\\Models\\User', 1, '{\"title\":\"Demo Process Finished\",\"message\":\"Demo for Ravi Kumar has been completed successfully.\",\"demo_process_id\":5,\"customer_name\":\"Ravi Kumar\",\"status\":\"Finished\"}', '2026-09-23 11:17:17', '2026-09-23 11:01:22', '2026-09-23 11:17:17'),
('f9de5675-725b-428b-a742-758c34790351', 'App\\Notifications\\CreditRequestApprovedByAdmin', 'App\\Models\\User', 5, '{\"title\":\"Credit Request Approved\",\"message\":\"Super Admin has approved the Credit Request. Next, Product Manager approval is required.\",\"credit_request_id\":7,\"status\":\"Forwarded to Product Manager\"}', '2026-09-18 12:40:52', '2026-09-18 12:00:14', '2026-09-18 12:40:52'),
('ff8c4151-d37f-4af5-a469-a9169404721c', 'App\\Notifications\\DemoProcessCreated', 'App\\Models\\User', 2, '{\"title\":\"Demo Process Created\",\"message\":\"New Demo Process created for Ravi Kumar (9876543210). Product: Bulk Whatsapp ( Dp). Date: 22\\/09\\/2026, Timing: 11:30 AM. Created By: Tharik.\",\"demo_process_id\":4,\"customer_name\":\"Ravi Kumar\",\"status\":\"Pending\"}', '2026-09-21 16:32:16', '2026-09-21 16:23:55', '2026-09-21 16:32:16');

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_source_id` bigint(20) DEFAULT NULL,
  `lead_requirement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Bank Transfer',
  `payment_date` date DEFAULT NULL,
  `payment_screenshot` varchar(255) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `customer_id`, `lead_id`, `lead_source_id`, `lead_requirement_id`, `amount`, `tax_percentage`, `tax_amount`, `total_amount`, `payment_method`, `payment_date`, `payment_screenshot`, `tax_number`, `remarks`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, NULL, 50000.00, 9.00, 4500.00, 54500.00, 'Cash', '2026-08-21', 'uploads/payments/1787309452_WhatsApp_Image_2026-08-14_at_11.53.04_AM_(2)_(1).jpeg', NULL, NULL, 1, '2026-08-21 10:50:52', '2026-09-02 11:47:36', '2026-09-02 11:47:36'),
(2, 791, NULL, 1, NULL, 35000.00, 18.00, 6300.00, 41300.00, 'Cash', '2026-09-02', 'uploads/payments/1788329881_WhatsApp_Image_2026-08-31_at_11.11.36_AM.jpeg', NULL, NULL, 1, '2026-09-02 11:48:01', '2026-09-02 11:48:01', NULL),
(3, 6, NULL, NULL, 3, 2500.00, 18.00, 450.00, 2950.00, 'UPI / QR', '2026-09-22', 'uploads/payments/1790062507_WhatsApp_Image_2026-09-17_at_2.05.48_PM_(1).jpeg', 'sdsadsadsadsadsad', NULL, 1, '2026-09-22 13:05:07', '2026-09-22 13:05:07', NULL);

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
(15, 'lead-settings.view', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
(16, 'lead-settings.edit', 'web', '2026-08-07 00:49:17', '2026-08-07 00:49:17'),
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
(72, 'call-log-reports.view', 'web', '2026-08-20 11:10:19', '2026-08-20 11:10:19'),
(73, 'credit-requests.view', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(74, 'credit-requests.create', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(75, 'credit-requests.approve_admin', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(76, 'credit-requests.approve_support', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(77, 'credit-requests.delete', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(78, 'payments.view', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(79, 'payments.create', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(80, 'payments.edit', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(81, 'payments.delete', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(82, 'permissions.view', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(83, 'permissions.create', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(84, 'permissions.approve', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(85, 'permissions.delete', 'web', '2026-08-21 10:00:04', '2026-08-21 10:00:04'),
(86, 'incentives.view', 'web', '2026-08-26 13:09:22', '2026-08-26 13:09:22'),
(87, 'incentives.create', 'web', '2026-08-26 13:09:22', '2026-08-26 13:09:22'),
(88, 'incentives.edit', 'web', '2026-08-26 13:10:35', '2026-08-26 13:10:35'),
(89, 'incentives.delete', 'web', '2026-08-26 13:10:35', '2026-08-26 13:10:35'),
(90, 'customer-settings.view', 'web', '2026-08-29 07:20:38', '2026-08-29 07:20:38'),
(91, 'customer-settings.edit', 'web', '2026-08-29 07:20:38', '2026-08-29 07:20:38'),
(92, 'followup-settings.view', 'web', '2026-08-31 10:06:07', '2026-08-31 10:06:07'),
(93, 'followup-settings.edit', 'web', '2026-08-31 10:06:07', '2026-08-31 10:06:07'),
(94, 'credit-request-settings.view', 'web', '2026-09-03 11:09:27', '2026-09-03 11:09:27'),
(95, 'credit-request-settings.edit', 'web', '2026-09-03 11:09:27', '2026-09-03 11:09:27'),
(96, 'coordinations.view', 'web', '2026-09-03 11:09:27', '2026-09-03 11:09:27'),
(97, 'coordinations.create', 'web', '2026-09-03 11:09:27', '2026-09-03 11:09:27'),
(98, 'coordinations.edit', 'web', '2026-09-03 11:09:27', '2026-09-03 11:09:27'),
(99, 'coordinations.delete', 'web', '2026-09-03 11:09:27', '2026-09-03 11:09:27'),
(100, 'demo-processes.view', 'web', '2026-09-03 15:01:21', '2026-09-03 15:01:21'),
(101, 'demo-processes.create', 'web', '2026-09-03 15:01:21', '2026-09-03 15:01:21'),
(102, 'demo-processes.edit', 'web', '2026-09-03 15:01:21', '2026-09-03 15:01:21'),
(103, 'demo-processes.delete', 'web', '2026-09-03 15:01:21', '2026-09-03 15:01:21'),
(104, 'demo-processes.assign', 'web', '2026-09-03 15:01:21', '2026-09-03 15:01:21'),
(105, 'demo-process-settings.view', 'web', '2026-09-03 16:25:32', '2026-09-03 16:25:32'),
(106, 'demo-process-settings.edit', 'web', '2026-09-03 16:25:32', '2026-09-03 16:25:32'),
(107, 'salary.create', 'web', '2026-09-15 15:45:37', '2026-09-15 15:45:37'),
(108, 'salary.edit', 'web', '2026-09-15 15:45:37', '2026-09-15 15:45:37'),
(109, 'salary.delete', 'web', '2026-09-15 15:46:21', '2026-09-15 15:46:21'),
(110, 'audit-logs.view', 'web', '2026-09-17 11:33:07', '2026-09-17 11:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `permission_requests`
--

CREATE TABLE `permission_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `permission_type` varchar(100) NOT NULL DEFAULT 'Short Permission',
  `reason` text DEFAULT NULL,
  `admin_remarks` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(5, 'App\\Models\\User', 2, 'staff-api', '1eb19546e40b7d61dc88c6981908f42e165e0d20265e9a0bddf091c407ed8bc3', '[\"*\"]', NULL, NULL, '2026-09-10 12:28:16', '2026-09-10 12:28:16'),
(7, 'App\\Models\\User', 2, 'staff-api', 'c3dce4eabf3637d26e574815c8089edc608f1bd5796da9f9f59458a9150fd90f', '[\"*\"]', '2026-09-11 10:53:30', NULL, '2026-09-10 13:44:23', '2026-09-11 10:53:30'),
(8, 'App\\Models\\User', 2, 'staff-api', '544b54d6e86a13fbef8c7f2b1db0c9e35e403c172e34f9119d99090fb5055e61', '[\"*\"]', '2026-09-16 11:33:16', NULL, '2026-09-10 15:11:26', '2026-09-16 11:33:16'),
(10, 'App\\Models\\User', 2, 'staff-api', 'e3e8701a452c63e61f9dc54a13dfe15ff83075b38250093c4844fbe058e5ed79', '[\"*\"]', '2026-09-11 13:20:42', NULL, '2026-09-10 16:46:56', '2026-09-11 13:20:42'),
(12, 'App\\Models\\User', 2, 'staff-api', '38e6f56be3258ff15f8a5ecb3a8d8a03591f078c8040a14b498fec1c8a17d024', '[\"*\"]', '2026-09-11 14:06:07', NULL, '2026-09-11 11:11:09', '2026-09-11 14:06:07'),
(13, 'App\\Models\\User', 2, 'staff-api', 'fb30ae30d6dadb5c8e9677b030335a73e6c75a76d5accf2c16674eb2e96c5528', '[\"*\"]', '2026-09-11 12:30:30', NULL, '2026-09-11 11:26:45', '2026-09-11 12:30:30'),
(14, 'App\\Models\\User', 2, 'staff-api', '0282b9d33fb03fe3d87d5a210ddcb5316a84b67aacb150878fa8628ba0ac798d', '[\"*\"]', '2026-09-11 15:40:47', NULL, '2026-09-11 13:21:54', '2026-09-11 15:40:47'),
(16, 'App\\Models\\User', 2, 'staff-api', '021fb10f4e00a26bc42596230fc2b223bad91409d5e01f69048753991b449cb5', '[\"*\"]', '2026-09-11 13:44:10', NULL, '2026-09-11 13:39:46', '2026-09-11 13:44:10'),
(17, 'App\\Models\\User', 2, 'staff-api', '862f12b2e3f23d6ea61aa6d87765a46ae4e205bf5371013790d444730a7a51f5', '[\"*\"]', '2026-09-11 16:10:08', NULL, '2026-09-11 13:45:35', '2026-09-11 16:10:08'),
(19, 'App\\Models\\User', 2, 'staff-api', '3c5fe15c20834ebf6733f126cf111f91bb8b456f126704d6fcbc5de3e90d2828', '[\"*\"]', '2026-09-16 11:59:20', NULL, '2026-09-12 11:55:58', '2026-09-16 11:59:20'),
(21, 'App\\Models\\User', 2, 'staff-api', '3df35c77e559577692447b31f85abc259051ce5c41550374c482f8f4da1cb290', '[\"*\"]', '2026-09-16 11:38:36', NULL, '2026-09-16 09:54:25', '2026-09-16 11:38:36'),
(23, 'App\\Models\\User', 2, 'staff-api', 'b20a7e05c5360603b91196e0a4ef172fe279672c7cb48614fc169e9ae9b09aed', '[\"*\"]', '2026-09-17 13:40:30', NULL, '2026-09-16 12:03:26', '2026-09-17 13:40:30'),
(25, 'App\\Models\\User', 2, 'staff-api', '02e1dd9c13e2c42312036f1680cf12c0c70c87742561040296e6af304e4b1a57', '[\"*\"]', NULL, NULL, '2026-09-16 13:16:34', '2026-09-16 13:16:34'),
(27, 'App\\Models\\User', 2, 'staff-api', '071b02863b3b3a14a5509d32cbafbe5bb8e321d38d4eeeb8048cc1d55d70e9e4', '[\"*\"]', '2026-09-16 15:49:58', NULL, '2026-09-16 13:52:07', '2026-09-16 15:49:58'),
(28, 'App\\Models\\User', 2, 'staff-api', '36e3d4776ed4e6b271500e1582256c260a55a880dcb232c0e8e5f595923b00fb', '[\"*\"]', '2026-09-16 15:45:05', NULL, '2026-09-16 15:44:23', '2026-09-16 15:45:05'),
(29, 'App\\Models\\User', 2, 'staff-api', '8aa375274aa7e2c78df12356d782ab99acf3d1cd57d7cdef77515e0d93f340e4', '[\"*\"]', '2026-09-16 16:26:40', NULL, '2026-09-16 16:00:29', '2026-09-16 16:26:40'),
(31, 'App\\Models\\User', 2, 'staff-api', 'b5daaf75e7b89c2caaa54a78af09943546b0363a8006f4c4e6883277fe7b54ec', '[\"*\"]', '2026-09-17 13:57:05', NULL, '2026-09-17 12:54:06', '2026-09-17 13:57:05'),
(32, 'App\\Models\\User', 2, 'staff-api', '799ffe8283843d2188e2b28a5d82fd873db233095fcba0c5c99a58a59bd68bb4', '[\"*\"]', '2026-09-17 12:56:49', NULL, '2026-09-17 12:56:32', '2026-09-17 12:56:49'),
(35, 'App\\Models\\User', 2, 'staff-api', '9126f749ce67dd615a23f92537b2d6f78d66e0c900ddf9863473edb0714d6a89', '[\"*\"]', '2026-09-17 15:38:20', NULL, '2026-09-17 15:35:52', '2026-09-17 15:38:20'),
(37, 'App\\Models\\User', 2, 'staff-api', 'de663aae0e40099951bc5d9abbb7cb731cca723f050f9397a11aeca7aea78c7c', '[\"*\"]', '2026-09-23 12:24:05', NULL, '2026-09-17 16:34:04', '2026-09-23 12:24:05'),
(38, 'App\\Models\\User', 2, 'staff-api', 'dba76347201f3f322c100bcbd151300558a265c514e83a975f210c93ada1d054', '[\"*\"]', NULL, NULL, '2026-09-18 11:04:34', '2026-09-18 11:04:34'),
(42, 'App\\Models\\User', 2, 'staff-api', 'aa8032144c78cc25f51c55f30e0cc11510be2ecf9ebeeecfbbf1489a26458ad8', '[\"*\"]', '2026-09-18 13:05:10', NULL, '2026-09-18 13:02:33', '2026-09-18 13:05:10'),
(47, 'App\\Models\\User', 2, 'staff-api', '54a66ceed73b066a31274ea310bd6dc3590166c4f315caf33bba571f94326fdd', '[\"*\"]', '2026-09-21 16:23:08', NULL, '2026-09-18 14:52:38', '2026-09-21 16:23:08'),
(51, 'App\\Models\\User', 2, 'staff-api', '62c968c58b4a5cfe8b9966eef0cf670b91e1f1885fd16762a3cb07dbb34c5d55', '[\"*\"]', '2026-09-23 14:40:53', NULL, '2026-09-21 11:41:15', '2026-09-23 14:40:53'),
(52, 'App\\Models\\User', 2, 'staff-api', 'c1656e3faa371f10ac3a00b8763c2ddd071a430bdaa746c4efa892bc7a99fc04', '[\"*\"]', '2026-09-21 13:08:14', NULL, '2026-09-21 12:47:45', '2026-09-21 13:08:14'),
(54, 'App\\Models\\User', 2, 'staff-api', 'f93a8c76b73eb54c5c4052aa3028b443bf1ccb5a4623810cff6d8c7bb310bb00', '[\"*\"]', '2026-09-23 10:54:07', NULL, '2026-09-21 16:17:06', '2026-09-23 10:54:07'),
(56, 'App\\Models\\User', 2, 'staff-api', 'b9833739a3def1447c820790b7f9ac4d184b872b3ff2c012c6c3939cbe4bff08', '[\"*\"]', '2026-09-22 13:28:14', NULL, '2026-09-22 12:18:47', '2026-09-22 13:28:14'),
(57, 'App\\Models\\User', 6, 'staff-api', '7de2c97244dae8e2ceed51ba6bc855e4413636e79fbc31d2c7128bebc339d400', '[\"*\"]', '2026-09-22 15:40:46', NULL, '2026-09-22 14:46:02', '2026-09-22 15:40:46'),
(58, 'App\\Models\\User', 2, 'staff-api', 'f4ad0117b7e1feafd801081582e548ef798b5a75064543625cc67772edc7eaa6', '[\"*\"]', '2026-09-23 10:33:53', NULL, '2026-09-22 16:58:58', '2026-09-23 10:33:53');

-- --------------------------------------------------------

--
-- Table structure for table `referral_settings`
--

CREATE TABLE `referral_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `referral_points` int(11) NOT NULL DEFAULT 100,
  `lead_list_columns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`lead_list_columns`)),
  `customer_list_columns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_list_columns`)),
  `followup_list_columns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`followup_list_columns`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `credit_request_list_columns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`credit_request_list_columns`)),
  `demo_process_list_columns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`demo_process_list_columns`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referral_settings`
--

INSERT INTO `referral_settings` (`id`, `referral_points`, `lead_list_columns`, `customer_list_columns`, `followup_list_columns`, `created_at`, `updated_at`, `credit_request_list_columns`, `demo_process_list_columns`) VALUES
(1, 150, '[\"created_by\",\"created_at\",\"customer_type\",\"customer\",\"lead_requirement\",\"lead_source\",\"lead_action\",\"lead_stage\",\"lost_reason\",\"next_followup_date\",\"status\"]', '[\"customer_created_date\",\"sale_closed_date\",\"name\",\"company_name\",\"mobile\",\"customer_type\",\"lead_stage\",\"lead_requirement\",\"city\",\"created_by\",\"followup_date\",\"lead_source\",\"status\"]', '[\"lead_info\",\"followup_type\",\"duration\",\"next_followup_date\",\"created_by\",\"created_at\",\"remarks\",\"lead_requirement\",\"lead_stage\",\"lead_source\"]', '2026-08-07 00:49:16', '2026-09-23 13:55:08', '[\"customer_info\",\"contact_info\",\"created_at\",\"credit_amount\",\"is_estimate\",\"status\",\"lead_requirement\",\"requested_by\"]', '[\"customer_name\",\"customer_phone\",\"demo_date\",\"demo_time\",\"customer_type\",\"created_by\",\"assigned_by\",\"sub_assigned_by\",\"status\",\"created_at\",\"remarks\",\"lead_requirement\"]');

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
(2, 'manager', 'web', '2026-08-07 02:06:08', '2026-09-05 11:37:25', NULL),
(3, 'support', 'web', '2026-08-21 05:30:09', '2026-08-21 05:30:09', NULL),
(4, 'sales team', 'web', '2026-08-21 05:39:17', '2026-08-21 05:39:17', NULL),
(5, 'Product Manager', 'web', '2026-09-05 11:31:44', '2026-09-05 11:31:44', NULL);

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
(1, 3),
(1, 4),
(1, 5),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(12, 4),
(13, 1),
(13, 2),
(13, 3),
(13, 4),
(13, 5),
(14, 1),
(14, 2),
(14, 3),
(14, 4),
(14, 5),
(15, 1),
(15, 2),
(15, 4),
(16, 1),
(16, 2),
(16, 4),
(17, 1),
(17, 2),
(17, 4),
(18, 1),
(18, 2),
(18, 4),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(20, 4),
(21, 1),
(21, 2),
(21, 4),
(22, 1),
(22, 2),
(22, 4),
(23, 1),
(23, 2),
(24, 1),
(24, 2),
(24, 4),
(25, 1),
(25, 2),
(25, 4),
(26, 1),
(26, 2),
(26, 4),
(27, 1),
(27, 2),
(28, 1),
(28, 2),
(28, 4),
(29, 1),
(29, 2),
(29, 4),
(30, 1),
(30, 2),
(30, 4),
(31, 1),
(31, 2),
(32, 1),
(32, 2),
(32, 4),
(33, 1),
(33, 2),
(33, 4),
(34, 1),
(34, 2),
(34, 4),
(35, 1),
(35, 2),
(36, 1),
(36, 2),
(36, 4),
(37, 1),
(37, 2),
(37, 4),
(38, 1),
(38, 2),
(38, 4),
(39, 1),
(39, 2),
(40, 1),
(40, 2),
(40, 4),
(41, 1),
(41, 2),
(41, 4),
(42, 1),
(42, 2),
(42, 4),
(43, 1),
(43, 2),
(44, 1),
(44, 2),
(44, 4),
(45, 1),
(45, 2),
(46, 1),
(46, 2),
(46, 4),
(47, 1),
(47, 2),
(47, 4),
(48, 1),
(48, 2),
(48, 4),
(49, 1),
(49, 2),
(50, 1),
(50, 2),
(50, 3),
(50, 4),
(50, 5),
(51, 1),
(51, 2),
(51, 3),
(51, 4),
(51, 5),
(52, 1),
(52, 2),
(52, 3),
(52, 4),
(52, 5),
(53, 1),
(53, 2),
(53, 3),
(53, 5),
(54, 1),
(54, 2),
(54, 4),
(55, 1),
(55, 2),
(55, 4),
(56, 1),
(56, 2),
(57, 1),
(57, 2),
(58, 1),
(58, 2),
(58, 3),
(58, 4),
(58, 5),
(59, 1),
(59, 2),
(59, 3),
(59, 4),
(59, 5),
(60, 1),
(60, 2),
(61, 1),
(61, 2),
(62, 1),
(62, 2),
(62, 3),
(62, 4),
(62, 5),
(63, 1),
(63, 2),
(63, 3),
(63, 4),
(63, 5),
(64, 1),
(64, 2),
(64, 3),
(64, 4),
(64, 5),
(65, 1),
(65, 2),
(66, 1),
(66, 2),
(67, 1),
(67, 2),
(67, 3),
(67, 4),
(67, 5),
(68, 1),
(68, 2),
(68, 4),
(69, 1),
(69, 2),
(69, 4),
(70, 1),
(70, 2),
(70, 4),
(71, 1),
(71, 2),
(72, 1),
(72, 2),
(72, 4),
(73, 1),
(73, 2),
(73, 3),
(73, 4),
(73, 5),
(74, 1),
(74, 2),
(74, 4),
(75, 1),
(75, 2),
(76, 1),
(76, 2),
(76, 5),
(77, 1),
(77, 2),
(78, 1),
(78, 2),
(78, 4),
(78, 5),
(79, 1),
(79, 2),
(80, 1),
(80, 2),
(81, 1),
(81, 2),
(82, 1),
(82, 2),
(82, 3),
(82, 4),
(82, 5),
(83, 1),
(83, 2),
(83, 3),
(83, 4),
(83, 5),
(84, 1),
(84, 2),
(85, 1),
(85, 2),
(86, 1),
(86, 2),
(86, 3),
(86, 4),
(86, 5),
(87, 1),
(87, 2),
(88, 1),
(88, 2),
(89, 1),
(89, 2),
(90, 1),
(90, 2),
(90, 4),
(91, 1),
(91, 2),
(91, 4),
(92, 1),
(92, 2),
(92, 4),
(93, 1),
(93, 2),
(93, 4),
(94, 1),
(94, 2),
(94, 3),
(94, 4),
(94, 5),
(95, 1),
(95, 2),
(95, 4),
(95, 5),
(96, 1),
(96, 2),
(96, 3),
(96, 4),
(96, 5),
(97, 1),
(97, 2),
(97, 3),
(97, 4),
(97, 5),
(98, 1),
(98, 2),
(98, 3),
(99, 1),
(99, 2),
(100, 1),
(100, 2),
(100, 3),
(100, 4),
(100, 5),
(101, 1),
(101, 2),
(101, 4),
(102, 1),
(102, 2),
(102, 3),
(102, 4),
(102, 5),
(103, 1),
(103, 2),
(104, 1),
(104, 2),
(104, 4),
(104, 5),
(105, 1),
(105, 2),
(105, 3),
(105, 4),
(105, 5),
(106, 1),
(106, 2),
(106, 4),
(107, 1),
(107, 2),
(108, 1),
(108, 2),
(109, 1),
(109, 2),
(110, 1),
(110, 2);

-- --------------------------------------------------------

--
-- Table structure for table `salary_adjustments`
--

CREATE TABLE `salary_adjustments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `month` varchar(20) NOT NULL,
  `ot_income` decimal(12,2) DEFAULT NULL,
  `leave_deduction` decimal(12,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('48o6edbeZ5yhkEog2PlJUmb4QSK5WgDnsUzXEMTW', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMUhnREh5SDUyeTB3VkRhaEJiOG9RY0I5TG9qdmcyWFZFRnBnVFpGQiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2N1c3RvbWVycyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vbGVhZC1zdGFnZXMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkNjNPMFY3SU05ZGdhazNwTVpocGZvT0wxcXRENFByWVVydlI2d1R6dDV3SnpVNGlnZkd3ZWEiO30=', 1790225273),
('4CJRR9MvksquJZaGO69DroHFu25oQ8ZNIXSkWmcT', 1, '106.51.24.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQkkwT2dzamlGa0NENzZvQ0cwWVdHZzhIYmRBVWdodmhLd0Fia2UxOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vZXJpeG9uLmluL2NybS9hZG1pbi9jdXN0b21lcnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkNjNPMFY3SU05ZGdhazNwTVpocGZvT0wxcXRENFByWVVydlI2d1R6dDV3SnpVNGlnZkd3ZWEiO30=', 1790221889);

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

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`template_id`, `type`, `title`, `content`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'WhatsApp', 'sales person', 'hai', 'Active', '2026-09-01 17:51:56', '2026-09-01 17:52:55', NULL);

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
  `staff_type` varchar(50) NOT NULL DEFAULT 'Temporary',
  `base_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `available_leave_count` decimal(5,2) NOT NULL DEFAULT 0.00,
  `check_in_time` time DEFAULT NULL,
  `late_attendance_count` int(11) NOT NULL DEFAULT 0,
  `increment_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `increment_date` date DEFAULT NULL,
  `allow_check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `is_on_leave` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `fcmtoken` text DEFAULT NULL,
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

INSERT INTO `users` (`id`, `name`, `email`, `profile_image`, `mobile_number`, `address`, `gender`, `date_of_birth`, `date_of_joining`, `designation`, `staff_type`, `base_salary`, `available_leave_count`, `check_in_time`, `late_attendance_count`, `increment_amount`, `increment_date`, `allow_check_in_time`, `check_out_time`, `is_on_leave`, `status`, `fcmtoken`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ezecrm - Sundari (Admin)', 'admin@gmail.com', 'uploads/profile/profile_1_1788586795_3daa76f7.png', NULL, NULL, NULL, NULL, NULL, NULL, 'Permanent', 0.00, 0.00, NULL, 0, 0.00, NULL, NULL, NULL, 0, 1, NULL, NULL, '$2y$12$63O0V7IM9dgak3pMZhpfoOL1qtD4PrYUrvR6wTzt5wJzU4igfGwea', NULL, NULL, '2026-09-05 11:10:32', NULL),
(2, 'Tharik', 'tharik@gmail.com', NULL, '9489042085', 'madurai,tamil nadu', NULL, '1993-05-09', '2026-01-19', 'manager', 'Temporary', 32000.00, 0.00, '09:00:00', 3, 0.00, NULL, '09:10:00', '18:30:00', 0, 1, 'cyEG1ItcSbeRfzQifeGLJ4:APA91bHE1YaDhuUfyWzK7kp0GRwGow9IY-D74PLTiqGTPcR0TTC0RTrUf6G9OlfqWgsXw2jr7SXr2lutVrBSnmuhO8A6hjcM_0s15L5Wsk2_2tAyv37OKh8', NULL, '$2y$12$aR.8L8L0O7fzNPGrbTPXnuDBPwPQxpJs2lZG7UtaH1dRV93VzzBQ6', NULL, '2026-08-07 02:06:43', '2026-09-22 16:58:58', NULL),
(3, 'riyaz', 'riyaz@gmail.com', NULL, '9485042596', 'madurai,tamil nadu', NULL, '1996-06-03', '2026-03-02', 'sale executive', 'Temporary', 20000.00, 0.00, '09:00:00', 3, 0.00, NULL, '09:10:00', '18:00:00', 0, 1, NULL, NULL, '$2y$12$wAOG0XmS9NXsMTXhNuz.EuraSyKW60ha6/y6flLrvx.3gOoHy6hLq', NULL, '2026-08-18 03:55:32', '2026-08-27 09:55:40', NULL),
(4, 'Sundari', 'accounts@erixon.in', NULL, '859298830', 'Thoothukudi', 'Female', '1999-01-28', '2021-09-06', 'Admin', 'Temporary', 14000.00, 1.50, '09:00:00', 0, 0.00, NULL, NULL, '18:00:00', 0, 1, NULL, NULL, '$2y$12$P4WJGT1.KW.sYRfVcvfzfebPiTeSdsxfcaUz/7GmHLE6gG9MS6e8i', NULL, '2026-08-21 05:26:22', '2026-09-05 11:11:14', '2026-09-05 11:11:14'),
(5, 'Maharajan', 'techsupport@erixon.in', NULL, '8590928830', 'Tiruneveli', 'Male', '1998-01-28', '2020-11-04', 'Product Manager', 'Permanent', 20000.00, 1.50, '09:00:00', 0, 0.00, NULL, '09:10:00', '18:00:00', 0, 1, NULL, NULL, '$2y$12$c4jorQW9dxji0W2p5Y42K.1DhNLl4L7/mAE2tqO6yI700oAIshKF.', NULL, '2026-08-21 05:32:54', '2026-09-23 14:29:39', NULL),
(6, 'Shanmuga Sundari', 'dataanalyst.erixon@gmail.com', NULL, '7306803864', 'tirunelveli', 'Female', '2003-09-27', '2026-06-17', 'Data Analyst', 'Temporary', 10000.00, 0.00, '09:00:00', 0, 0.00, NULL, '09:10:00', '18:00:00', 1, 1, 'dRW7fn7XSIypczZ9hwbTPw:APA91bFoy-Sa9WXFz1qdQkUsxYJLebyf9b9ZFVCS6X-AJmf4As5fda6fRl19ftRN1hgizZbkvg06A3OMi6mRNLQi1KHxH-lB3VojQjdj-u5ckJDDDh3B9Ic', NULL, '$2y$12$SLROuyWkMFkYR2k9DGBCLO4o2ErUR1ISSHcYkoXqJoGpxsv5PE.Ki', NULL, '2026-08-21 06:01:19', '2026-09-22 14:46:02', NULL),
(7, 'Baiju', 'info@erixon.in', NULL, '80890488830', NULL, 'Male', NULL, NULL, 'MD', 'Temporary', 0.00, 1.00, '09:00:00', 0, 0.00, NULL, NULL, '18:00:00', 0, 1, NULL, NULL, '$2y$12$e1gnJqRDE2VqGOghwzFGMerF9TLAuZOYQ2KN8fiAUAGUT3EQMBVki', NULL, '2026-08-21 06:05:23', '2026-08-21 06:05:23', NULL),
(8, 'Shanmuga Sundari', 'erixonindia@gmail.com', NULL, '7306803864', 'tirunelveli', NULL, NULL, NULL, NULL, 'Permanent', 0.00, 1.00, '09:00:00', 3, 0.00, NULL, '09:10:00', '18:00:00', 0, 1, NULL, NULL, '$2y$12$QQguCNoMIZ2Bry5fb3rbvOwqnbLAEcyMC0UPS1j6gJs9xH6aqpVRy', NULL, '2026-08-24 08:55:11', '2026-08-28 10:25:05', '2026-08-28 10:25:05'),
(9, 'tesst', 'test@gmail.com', NULL, NULL, NULL, 'Male', NULL, NULL, NULL, 'Temporary', 10000.00, 0.00, '09:00:00', 3, 0.00, NULL, '09:10:00', '18:00:00', 0, 1, NULL, NULL, '$2y$12$ascvM6ov8dIjd2Vc7WgdMu/DK.qnrnPFAiwDt/iZs2gPQvgonhRXC', NULL, '2026-08-26 05:58:38', '2026-08-26 06:00:34', '2026-08-26 06:00:34'),
(10, 'Baiju', 'baijuarun@gmail.com', NULL, '808 904 8830', NULL, 'Male', '1988-06-05', '2014-04-20', 'MD', 'Permanent', 0.00, 1.50, '09:00:00', 2, 0.00, NULL, '09:00:00', '18:00:00', 0, 1, NULL, NULL, '$2y$12$o7npdlVaxg2mp0RTPqNV3eiyWYT3wQvK5pqS2GxDiAXkieI2cfdX.', NULL, '2026-08-28 10:30:10', '2026-08-28 10:30:10', NULL),
(11, 'harini', 'harini@gmail.com', NULL, '859298830', NULL, NULL, NULL, NULL, NULL, 'Temporary', 0.00, 0.00, '09:00:00', 2, 0.00, NULL, '09:10:00', '18:00:00', 0, 1, NULL, NULL, '$2y$12$gkRDaddti0.cMRRt0oOp2uwN8ddmsgeatNimoJGoxVeAP4u30c/ky', NULL, '2026-08-28 10:59:13', '2026-09-05 11:11:02', '2026-09-05 11:11:02'),
(12, 'Manimala', 'erixonindiasupport@gmail.com', NULL, '7604 888 407', NULL, 'Female', NULL, NULL, NULL, 'Permanent', 0.00, 1.50, '09:00:00', 2, 0.00, NULL, '09:10:00', '18:00:00', 0, 1, NULL, NULL, '$2y$12$G53Twb4z0H9eZC8hvv9Akepbod15hLXuCirWEMnIUtkHrR5aQ5hIm', NULL, '2026-09-05 11:25:53', '2026-09-05 11:25:53', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `attendance_user_id_foreign` (`user_id`),
  ADD KEY `attendance_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  ADD KEY `audit_logs_created_at_event_index` (`created_at`,`event`),
  ADD KEY `audit_logs_module_created_at_index` (`module`,`created_at`),
  ADD KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `audit_logs_module_index` (`module`);

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
-- Indexes for table `coordinations`
--
ALTER TABLE `coordinations`
  ADD PRIMARY KEY (`coordination_id`),
  ADD KEY `coordinations_staff_id_foreign` (`staff_id`),
  ADD KEY `coordinations_created_by_foreign` (`created_by`);

--
-- Indexes for table `coordination_joining_staff`
--
ALTER TABLE `coordination_joining_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_coord_user` (`coordination_id`,`user_id`),
  ADD KEY `coordination_joining_staff_user_id_foreign` (`user_id`);

--
-- Indexes for table `credit_requests`
--
ALTER TABLE `credit_requests`
  ADD PRIMARY KEY (`credit_request_id`),
  ADD KEY `credit_requests_lead_id_foreign` (`lead_id`),
  ADD KEY `credit_requests_customer_id_foreign` (`customer_id`),
  ADD KEY `credit_requests_admin_approved_by_foreign` (`admin_approved_by`),
  ADD KEY `credit_requests_support_approved_by_foreign` (`support_approved_by`),
  ADD KEY `credit_requests_requested_by_foreign` (`requested_by`),
  ADD KEY `lead_source_id` (`lead_source_id`),
  ADD KEY `fk_credit_requests_lead_requirement` (`lead_requirement_id`);

--
-- Indexes for table `credit_request_custom_fields`
--
ALTER TABLE `credit_request_custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `credit_request_custom_fields_field_name_unique` (`field_name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customers_mobile_unique` (`mobile`),
  ADD KEY `customers_created_by_foreign` (`created_by`),
  ADD KEY `owner_by` (`owner_by`,`assign_by`),
  ADD KEY `customers_lead_requirement_id_foreign` (`lead_requirement_id`),
  ADD KEY `customers_lead_stage_id_foreign` (`lead_stage_id`);

--
-- Indexes for table `customer_custom_fields`
--
ALTER TABLE `customer_custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_custom_fields_field_name_unique` (`field_name`);

--
-- Indexes for table `demo_processes`
--
ALTER TABLE `demo_processes`
  ADD PRIMARY KEY (`demo_process_id`),
  ADD KEY `demo_processes_lead_source_id_foreign` (`lead_source_id`),
  ADD KEY `demo_processes_created_by_foreign` (`created_by`),
  ADD KEY `demo_processes_assigned_by_foreign` (`assigned_by`),
  ADD KEY `demo_processes_sub_assigned_by_foreign` (`sub_assigned_by`),
  ADD KEY `demo_processes_lead_requirement_id_foreign` (`lead_requirement_id`);

--
-- Indexes for table `demo_process_custom_fields`
--
ALTER TABLE `demo_process_custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `demo_process_custom_fields_field_name_unique` (`field_name`);

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
-- Indexes for table `followup_custom_fields`
--
ALTER TABLE `followup_custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `followup_custom_fields_field_name_unique` (`field_name`);

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
-- Indexes for table `incentives`
--
ALTER TABLE `incentives`
  ADD PRIMARY KEY (`incentive_id`),
  ADD KEY `incentives_staff_id_foreign` (`staff_id`),
  ADD KEY `incentives_created_by_foreign` (`created_by`);

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
-- Indexes for table `lead_custom_fields`
--
ALTER TABLE `lead_custom_fields`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payments_customer_id_foreign` (`customer_id`),
  ADD KEY `payments_lead_id_foreign` (`lead_id`),
  ADD KEY `payments_created_by_foreign` (`created_by`),
  ADD KEY `lead_source_id` (`lead_source_id`),
  ADD KEY `payments_lead_requirement_id_foreign` (`lead_requirement_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `permission_requests`
--
ALTER TABLE `permission_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission_requests_user_id_foreign` (`user_id`),
  ADD KEY `permission_requests_approved_by_foreign` (`approved_by`);

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
-- Indexes for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `salary_adjustments_user_id_month_unique` (`user_id`,`month`),
  ADD KEY `salary_adjustments_created_by_foreign` (`created_by`);

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
  MODIFY `attendance_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `call_logs`
--
ALTER TABLE `call_logs`
  MODIFY `call_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `call_recordings`
--
ALTER TABLE `call_recordings`
  MODIFY `call_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `coordinations`
--
ALTER TABLE `coordinations`
  MODIFY `coordination_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `coordination_joining_staff`
--
ALTER TABLE `coordination_joining_staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `credit_requests`
--
ALTER TABLE `credit_requests`
  MODIFY `credit_request_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `credit_request_custom_fields`
--
ALTER TABLE `credit_request_custom_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1143;

--
-- AUTO_INCREMENT for table `customer_custom_fields`
--
ALTER TABLE `customer_custom_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `demo_processes`
--
ALTER TABLE `demo_processes`
  MODIFY `demo_process_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `demo_process_custom_fields`
--
ALTER TABLE `demo_process_custom_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `followup_custom_fields`
--
ALTER TABLE `followup_custom_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followup_reassignments`
--
ALTER TABLE `followup_reassignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `incentives`
--
ALTER TABLE `incentives`
  MODIFY `incentive_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `lead_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lead_custom_fields`
--
ALTER TABLE `lead_custom_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_documents`
--
ALTER TABLE `lead_documents`
  MODIFY `lead_documents_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lead_requirements`
--
ALTER TABLE `lead_requirements`
  MODIFY `lead_requirements_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `lead_sources_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lead_stages`
--
ALTER TABLE `lead_stages`
  MODIFY `lead_stage_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lost_reasons`
--
ALTER TABLE `lost_reasons`
  MODIFY `lost_reason_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `permission_requests`
--
ALTER TABLE `permission_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `referral_settings`
--
ALTER TABLE `referral_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `template_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permission_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `coordinations`
--
ALTER TABLE `coordinations`
  ADD CONSTRAINT `coordinations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `coordinations_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `credit_requests`
--
ALTER TABLE `credit_requests`
  ADD CONSTRAINT `credit_requests_admin_approved_by_foreign` FOREIGN KEY (`admin_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `credit_requests_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `credit_requests_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`lead_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `credit_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `credit_requests_support_approved_by_foreign` FOREIGN KEY (`support_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_credit_requests_lead_requirement` FOREIGN KEY (`lead_requirement_id`) REFERENCES `lead_requirements` (`lead_requirements_id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customers_lead_requirement_id_foreign` FOREIGN KEY (`lead_requirement_id`) REFERENCES `lead_requirements` (`lead_requirements_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customers_lead_stage_id_foreign` FOREIGN KEY (`lead_stage_id`) REFERENCES `lead_stages` (`lead_stage_id`) ON DELETE SET NULL;

--
-- Constraints for table `demo_processes`
--
ALTER TABLE `demo_processes`
  ADD CONSTRAINT `demo_processes_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `demo_processes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `demo_processes_lead_requirement_id_foreign` FOREIGN KEY (`lead_requirement_id`) REFERENCES `lead_requirements` (`lead_requirements_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `demo_processes_lead_source_id_foreign` FOREIGN KEY (`lead_source_id`) REFERENCES `lead_sources` (`lead_sources_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `demo_processes_sub_assigned_by_foreign` FOREIGN KEY (`sub_assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `incentives`
--
ALTER TABLE `incentives`
  ADD CONSTRAINT `incentives_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incentives_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`lead_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_lead_requirement_id_foreign` FOREIGN KEY (`lead_requirement_id`) REFERENCES `lead_requirements` (`lead_requirements_id`) ON DELETE SET NULL;

--
-- Constraints for table `permission_requests`
--
ALTER TABLE `permission_requests`
  ADD CONSTRAINT `permission_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `permission_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  ADD CONSTRAINT `salary_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `salary_adjustments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
