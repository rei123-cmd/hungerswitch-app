-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 05:00 AM
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
-- Database: `hungerswitch_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `region` varchar(100) NOT NULL,
  `coverage_area` text DEFAULT NULL,
  `total_distributions` int(11) DEFAULT 0,
  `active_communities` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL COMMENT 'founder user_id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `user_id`, `region`, `coverage_area`, `total_distributions`, `active_communities`, `created_at`, `updated_at`, `is_active`, `verification_status`, `verified_at`, `verified_by`) VALUES
(1, 4, 'Tangerang Selatan', 'BSD, Serpong, Alam Sutera', 0, 0, '2025-11-08 03:45:32', '2025-11-08 03:45:32', 1, 'pending', NULL, NULL),
(3, 8, 'tangerang selatan', NULL, 0, 0, '2025-11-09 14:47:07', '2025-11-09 14:47:07', 1, 'pending', NULL, NULL),
(4, 12, 'tangerang selatan', NULL, 0, 0, '2025-11-18 18:55:48', '2025-11-18 18:55:48', 1, 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` int(11) NOT NULL,
  `registered_by_agent_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `id_number` varchar(50) DEFAULT NULL COMMENT 'NIK/KTP',
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text NOT NULL,
  `family_size` int(11) DEFAULT 1,
  `income_level` enum('very_low','low','medium') DEFAULT 'low',
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `verification_notes` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beneficiaries`
--

INSERT INTO `beneficiaries` (`id`, `registered_by_agent_id`, `name`, `id_number`, `phone_number`, `address`, `family_size`, `income_level`, `verification_status`, `verification_notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ibu Siti Aminah', '3174012345678901', '081234567890', 'Jl. Raya Serpong No. 123, Tangerang Selatan', 4, 'low', 'verified', NULL, 1, '2025-11-27 03:04:27', '2025-11-27 03:04:27'),
(2, 1, 'Bapak Ahmad Yani', '3174012345678902', '081234567891', 'Jl. BSD Boulevard No. 45, BSD City', 5, 'very_low', 'verified', NULL, 1, '2025-11-27 03:04:27', '2025-11-27 03:04:27'),
(3, 1, 'Ibu Fatimah', '3174012345678903', '081234567892', 'Jl. Alam Sutera No. 78, Serpong', 3, 'low', 'pending', NULL, 1, '2025-11-27 03:04:27', '2025-11-27 03:04:27'),
(4, 1, 'Bapak Joko Widodo', '3174012345678904', '081234567893', 'Jl. Gading Serpong No. 234, Tangerang', 6, 'very_low', 'verified', NULL, 1, '2025-11-27 03:04:27', '2025-11-27 03:04:27'),
(5, 1, 'Ibu Nurlaila', '3174012345678905', '081234567894', 'Jl. Pahlawan No. 56, Pamulang', 4, 'low', 'verified', NULL, 1, '2025-11-27 03:04:27', '2025-11-27 03:04:27');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`) VALUES
(1, 'Bakery', 'bakery', 'Roti, kue, dan produk bakery', 'bi-cup-hot', '2025-11-08 03:45:32'),
(2, 'Ready to Eat', 'ready-to-eat', 'Makanan siap saji', 'bi-box-seam', '2025-11-08 03:45:32'),
(3, 'Healthy Food', 'healthy', 'Makanan sehat dan organik', 'bi-heart', '2025-11-08 03:45:32'),
(4, 'Beverages', 'beverages', 'Minuman segar', 'bi-cup-straw', '2025-11-08 03:45:32'),
(5, 'Snacks', 'snacks', 'Camilan dan makanan ringan', 'bi-bag', '2025-11-08 03:45:32');

-- --------------------------------------------------------

--
-- Table structure for table `distribution_reports`
--

CREATE TABLE `distribution_reports` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL COMMENT 'ID dari food_programs',
  `agent_id` int(11) NOT NULL COMMENT 'ID dari agents',
  `report_type` enum('daily','weekly','monthly','program_completion') NOT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `portions_distributed` int(11) DEFAULT 0,
  `beneficiaries_reached` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `photos` longtext DEFAULT NULL COMMENT 'JSON array of photo URLs',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `distribution_reports`
--

INSERT INTO `distribution_reports` (`id`, `program_id`, `agent_id`, `report_type`, `report_date`, `portions_distributed`, `beneficiaries_reached`, `notes`, `photos`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'program_completion', '2025-12-01 07:30:00', 180, 160, 'Distribusi berjalan lancar. Semua penerima sangat antusias dan berterima kasih. Beberapa porsi tersisa dibagikan kepada warga sekitar masjid.', NULL, '2025-11-27 03:04:49', '2025-11-27 03:04:49'),
(2, 2, 1, 'program_completion', '2025-12-05 04:45:00', 150, 150, 'Program distribusi makanan di sekolah sangat sukses. Anak-anak senang dengan menu yang diberikan. Guru dan orang tua memberikan apresiasi positif.', NULL, '2025-11-27 03:04:49', '2025-11-27 03:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('one_time','monthly') DEFAULT 'one_time',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation_fund`
--

CREATE TABLE `donation_fund` (
  `id` int(11) NOT NULL,
  `current_balance` decimal(15,2) DEFAULT 0.00,
  `total_received` decimal(15,2) DEFAULT 0.00,
  `total_distributed` decimal(15,2) DEFAULT 0.00,
  `last_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donation_fund`
--

INSERT INTO `donation_fund` (`id`, `current_balance`, `total_received`, `total_distributed`, `last_updated_at`) VALUES
(1, 3500000.00, 3500000.00, 0.00, '2025-11-27 03:59:01'),
(2, 0.00, 0.00, 0.00, '2025-11-26 16:28:47');

-- --------------------------------------------------------

--
-- Table structure for table `donation_transactions`
--

CREATE TABLE `donation_transactions` (
  `id` int(11) NOT NULL,
  `transaction_type` enum('income','distribution','operational') NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL COMMENT 'donations, food_programs, etc',
  `reference_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `balance_before` decimal(12,2) NOT NULL,
  `balance_after` decimal(12,2) NOT NULL,
  `description` text DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL COMMENT 'founder user_id',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donation_transactions`
--

INSERT INTO `donation_transactions` (`id`, `transaction_type`, `reference_type`, `reference_id`, `amount`, `balance_before`, `balance_after`, `description`, `processed_by`, `created_at`) VALUES
(1, 'income', 'donations', 1, 100000.00, 0.00, 100000.00, 'Donasi dari Hamba Allah', NULL, '2025-11-26 16:42:07'),
(2, 'income', 'donations', 2, 500000.00, 100000.00, 600000.00, 'Donasi dari filbert', NULL, '2025-11-27 03:55:34'),
(3, 'income', 'donations', 3, 250000.00, 600000.00, 850000.00, 'Donasi dari filbert', NULL, '2025-11-27 03:55:55'),
(4, 'income', 'donations', 4, 100000.00, 850000.00, 950000.00, 'Donasi dari filbert', NULL, '2025-11-27 03:56:00'),
(5, 'income', 'donations', 5, 25000.00, 950000.00, 975000.00, 'Donasi dari filbert', NULL, '2025-11-27 03:56:04'),
(6, 'income', 'donations', 6, 25000.00, 975000.00, 1000000.00, 'Donasi dari filbert', NULL, '2025-11-27 03:56:09'),
(7, 'income', 'donations', 7, 2500000.00, 1000000.00, 3500000.00, 'Donasi dari Hamba Allah', NULL, '2025-11-27 03:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `food_programs`
--

CREATE TABLE `food_programs` (
  `id` int(11) NOT NULL,
  `program_number` varchar(50) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `assigned_agent_id` int(11) NOT NULL COMMENT 'ID dari table agents',
  `budget_allocated` decimal(12,2) NOT NULL,
  `quantity_planned` int(11) NOT NULL COMMENT 'Target porsi',
  `quantity_distributed` int(11) DEFAULT 0,
  `menu_description` text DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `distribution_time` time DEFAULT NULL,
  `distribution_location` varchar(255) DEFAULT NULL,
  `status` enum('planned','in_progress','completed','cancelled') DEFAULT 'planned',
  `proof_photos` longtext DEFAULT NULL COMMENT 'JSON array of photo URLs',
  `recipient_data` longtext DEFAULT NULL COMMENT 'JSON data penerima offline',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL COMMENT 'founder user_id',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_programs`
--

INSERT INTO `food_programs` (`id`, `program_number`, `program_name`, `assigned_agent_id`, `budget_allocated`, `quantity_planned`, `quantity_distributed`, `menu_description`, `scheduled_date`, `distribution_time`, `distribution_location`, `status`, `proof_photos`, `recipient_data`, `notes`, `created_by`, `created_at`, `completed_at`) VALUES
(1, 'FP-2025-001', 'Berbagi Berkah Ramadan', 1, 5000000.00, 200, 180, 'Nasi box dengan lauk ayam, sayur, dan buah', '2025-12-01', '12:00:00', 'Masjid Al-Ikhlas BSD', 'completed', NULL, NULL, NULL, 1, '2025-11-27 03:04:37', NULL),
(2, 'FP-2025-002', 'Peduli Gizi Anak', 1, 3000000.00, 150, 150, 'Paket makanan bergizi untuk anak sekolah', '2025-12-05', '10:00:00', 'SDN Serpong 1', 'completed', NULL, NULL, NULL, 1, '2025-11-27 03:04:37', NULL),
(3, 'FP-2025-003', 'Sarapan Sehat Komunitas', 1, 2500000.00, 100, 0, 'Bubur ayam dan roti gandum dengan susu', '2025-12-15', '07:00:00', 'Balai RW 05 BSD', 'planned', NULL, NULL, NULL, 1, '2025-11-27 03:04:37', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `founder_dashboard_stats`
-- (See below for the actual view)
--
CREATE TABLE `founder_dashboard_stats` (
`total_customers` bigint(21)
,`total_partners` bigint(21)
,`total_agents` bigint(21)
,`total_marketplace_orders` bigint(21)
,`total_programs` bigint(21)
,`total_portions_distributed` decimal(32,0)
,`donation_balance` decimal(15,2)
,`total_donations_received` decimal(15,2)
,`total_active_beneficiaries` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `impact_stats`
--

CREATE TABLE `impact_stats` (
  `id` int(11) NOT NULL,
  `stat_date` date NOT NULL,
  `portions_saved` int(11) DEFAULT 0,
  `people_helped` int(11) DEFAULT 0,
  `donations_received` decimal(12,2) DEFAULT 0.00,
  `co2_reduced` decimal(10,2) DEFAULT 0.00,
  `active_partners` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `impact_stats`
--

INSERT INTO `impact_stats` (`id`, `stat_date`, `portions_saved`, `people_helped`, `donations_received`, `co2_reduced`, `active_partners`, `created_at`) VALUES
(1, '2025-11-08', 50000, 10000, 2500000.00, 25.00, 2000, '2025-11-08 03:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `marketplace_transactions`
--

CREATE TABLE `marketplace_transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `gross_amount` decimal(12,2) NOT NULL,
  `platform_fee` decimal(12,2) DEFAULT 0.00 COMMENT '5-10% komisi',
  `partner_amount` decimal(12,2) NOT NULL COMMENT 'Yang diterima mitra',
  `payment_gateway` varchar(50) DEFAULT NULL,
  `settlement_status` enum('pending','settled','failed') DEFAULT 'pending',
  `settled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text DEFAULT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `order_source` enum('marketplace','donation_program') DEFAULT 'marketplace',
  `program_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `order_data` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `savings_amount` decimal(10,2) DEFAULT 0.00,
  `pickup_location` varchar(255) DEFAULT NULL,
  `pickup_time` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_gateway` varchar(50) DEFAULT NULL COMMENT 'midtrans, xendit, etc',
  `payment_status` enum('unpaid','paid','failed') DEFAULT 'unpaid',
  `payment_proof` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_gateway_ref` varchar(100) DEFAULT NULL COMMENT 'Referensi dari Midtrans/Xendit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `order_source`, `program_id`, `user_id`, `order_data`, `total_amount`, `savings_amount`, `pickup_location`, `pickup_time`, `payment_method`, `payment_gateway`, `payment_status`, `payment_proof`, `paid_at`, `status`, `notes`, `created_at`, `updated_at`, `payment_gateway_ref`) VALUES
(1, 'HS-251124190222806', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzIiLCJuYW1lIjoiQ3JvaXNzYW50IEJ1dHRlciBQcmVtaXVtIiwicHJpY2UiOiIxMjAwMC4wMCIsIm9yaWdpbmFsUHJpY2UiOiIyNTAwMC4wMCIsInJlc3RhdXJhbnQiOiJCYWtlcnkgQXJ0aXNhbiIsImltYWdlIjoiaHR0cHM6XC9cL2ltYWdlcy51bnNwbGFzaC5jb21cL3Bob3RvLTE1NTU1MDcwMzYtYWIxZjQwMzg4MDhhP3c9NDAwIiwiY2F0ZWdvcnkiOiJCYWtlcnkiLCJxdWFudGl0eSI6MX1dLCJwcmljaW5nIjp7InN1YnRvdGFsIjoxMjAwMCwic2VydmljZSI6MjAwMCwidG90YWwiOjE0MDAwfSwiZGVsaXZlcnkiOnsidHlwZSI6InBpY2t1cCIsImFkZHJlc3MiOiJ0YW5nZXJhbmcgc2VsYXRhbiIsIm5vdGVzIjoiIn19', 14000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-24 18:02:27', '', NULL, '2025-11-24 18:02:22', '2025-11-24 18:02:27', NULL),
(2, 'HS-251124190700296', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzIiLCJuYW1lIjoiQ3JvaXNzYW50IEJ1dHRlciBQcmVtaXVtIiwicHJpY2UiOiIxMjAwMC4wMCIsIm9yaWdpbmFsUHJpY2UiOiIyNTAwMC4wMCIsInJlc3RhdXJhbnQiOiJCYWtlcnkgQXJ0aXNhbiIsImltYWdlIjoiaHR0cHM6XC9cL2ltYWdlcy51bnNwbGFzaC5jb21cL3Bob3RvLTE1NTU1MDcwMzYtYWIxZjQwMzg4MDhhP3c9NDAwIiwiY2F0ZWdvcnkiOiJCYWtlcnkiLCJxdWFudGl0eSI6Mn1dLCJwcmljaW5nIjp7InN1YnRvdGFsIjoyNDAwMCwic2VydmljZSI6MjAwMCwidG90YWwiOjI2MDAwfSwiZGVsaXZlcnkiOnsidHlwZSI6InBpY2t1cCIsImFkZHJlc3MiOiJ1bW4iLCJub3RlcyI6IiJ9fQ==', 26000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-24 18:07:03', '', NULL, '2025-11-24 18:07:00', '2025-11-24 18:07:03', NULL),
(3, 'HS-251124195020775', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzMiLCJuYW1lIjoiRG9uYXQgQ29rbGF0IElzaSBQcmVtaXVtIiwicHJpY2UiOiI4MDAwLjAwIiwib3JpZ2luYWxQcmljZSI6IjIwMDAwLjAwIiwicmVzdGF1cmFudCI6IkJha2VyeSBBcnRpc2FuIiwiaW1hZ2UiOiJodHRwczpcL1wvaW1hZ2VzLnVuc3BsYXNoLmNvbVwvcGhvdG8tMTU1MTAyNDYwMS1iZWM3OGFlYTcwNGI/dz00MDAiLCJjYXRlZ29yeSI6IlNuYWNrcyIsInF1YW50aXR5IjoxfSx7ImlkIjoicHJvZHVjdF8xIiwibmFtZSI6IlJvdGkgVGF3YXIgR2FuZHVtIFByZW1pdW0iLCJwcmljZSI6IjE1MDAwLjAwIiwib3JpZ2luYWxQcmljZSI6IjM1MDAwLjAwIiwicmVzdGF1cmFudCI6IkJha2VyeSBBcnRpc2FuIiwiaW1hZ2UiOiJodHRwczpcL1wvaW1hZ2VzLnVuc3BsYXNoLmNvbVwvcGhvdG8tMTUwOTQ0MDE1OTU5Ni0wMjQ5MDg4NzcyZmY/dz00MDAiLCJjYXRlZ29yeSI6IkJha2VyeSIsInF1YW50aXR5IjoxfV0sInByaWNpbmciOnsic3VidG90YWwiOjIzMDAwLCJzZXJ2aWNlIjoyMDAwLCJ0b3RhbCI6MjUwMDB9LCJ0eXBlIjoicGlja3VwIn0=', 25000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-24 18:50:38', '', NULL, '2025-11-24 18:50:20', '2025-11-24 18:50:38', NULL),
(4, 'HS-251124204429602', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzIiLCJuYW1lIjoiQ3JvaXNzYW50IEJ1dHRlciBQcmVtaXVtIiwicHJpY2UiOiIxMjAwMC4wMCIsIm9yaWdpbmFsUHJpY2UiOiIyNTAwMC4wMCIsInJlc3RhdXJhbnQiOiJCYWtlcnkgQXJ0aXNhbiIsImltYWdlIjoiaHR0cHM6XC9cL2ltYWdlcy51bnNwbGFzaC5jb21cL3Bob3RvLTE1NTU1MDcwMzYtYWIxZjQwMzg4MDhhP3c9NDAwIiwiY2F0ZWdvcnkiOiJCYWtlcnkiLCJxdWFudGl0eSI6MX1dLCJwcmljaW5nIjp7InN1YnRvdGFsIjoxMjAwMCwic2VydmljZSI6MjAwMCwidG90YWwiOjE0MDAwfSwidHlwZSI6InBpY2t1cCJ9', 14000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-24 19:44:43', '', NULL, '2025-11-24 19:44:29', '2025-11-24 19:44:43', NULL),
(5, 'HS-251125054057936', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzQiLCJuYW1lIjoiU2FuZHdpY2ggQXlhbSBCQlEiLCJwcmljZSI6IjIwMDAwLjAwIiwib3JpZ2luYWxQcmljZSI6IjQ1MDAwLjAwIiwicmVzdGF1cmFudCI6IkJha2VyeSBBcnRpc2FuIiwiaW1hZ2UiOiJodHRwczpcL1wvaW1hZ2VzLnVuc3BsYXNoLmNvbVwvcGhvdG8tMTUyODczNTYwMjc4MC0yNTUyZmQ0NmM3YWY/dz00MDAiLCJjYXRlZ29yeSI6IlJlYWR5IHRvIEVhdCIsInF1YW50aXR5IjoxfV0sInByaWNpbmciOnsic3VidG90YWwiOjIwMDAwLCJzZXJ2aWNlIjoyMDAwLCJ0b3RhbCI6MjIwMDB9LCJ0eXBlIjoicGlja3VwIn0=', 22000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-25 04:41:03', '', NULL, '2025-11-25 04:40:57', '2025-11-25 04:41:03', NULL),
(6, 'HS-251125065744413', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzIiLCJuYW1lIjoiQ3JvaXNzYW50IEJ1dHRlciBQcmVtaXVtIiwicHJpY2UiOiIxMjAwMC4wMCIsIm9yaWdpbmFsUHJpY2UiOiIyNTAwMC4wMCIsInJlc3RhdXJhbnQiOiJCYWtlcnkgQXJ0aXNhbiIsImltYWdlIjoiaHR0cHM6XC9cL2ltYWdlcy51bnNwbGFzaC5jb21cL3Bob3RvLTE1NTU1MDcwMzYtYWIxZjQwMzg4MDhhP3c9NDAwIiwiY2F0ZWdvcnkiOiJCYWtlcnkiLCJxdWFudGl0eSI6MX0seyJpZCI6InByb2R1Y3RfMyIsIm5hbWUiOiJEb25hdCBDb2tsYXQgSXNpIFByZW1pdW0iLCJwcmljZSI6IjgwMDAuMDAiLCJvcmlnaW5hbFByaWNlIjoiMjAwMDAuMDAiLCJyZXN0YXVyYW50IjoiQmFrZXJ5IEFydGlzYW4iLCJpbWFnZSI6Imh0dHBzOlwvXC9pbWFnZXMudW5zcGxhc2guY29tXC9waG90by0xNTUxMDI0NjAxLWJlYzc4YWVhNzA0Yj93PTQwMCIsImNhdGVnb3J5IjoiU25hY2tzIiwicXVhbnRpdHkiOjF9XSwicHJpY2luZyI6eyJzdWJ0b3RhbCI6MjAwMDAsInNlcnZpY2UiOjIwMDAsInRvdGFsIjoyMjAwMH0sInR5cGUiOiJwaWNrdXAifQ==', 22000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-25 05:57:51', '', NULL, '2025-11-25 05:57:44', '2025-11-25 05:57:51', NULL),
(7, 'HS-251125072033953', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzIiLCJuYW1lIjoiQ3JvaXNzYW50IEJ1dHRlciBQcmVtaXVtIiwicHJpY2UiOiIxMjAwMC4wMCIsIm9yaWdpbmFsUHJpY2UiOiIyNTAwMC4wMCIsInJlc3RhdXJhbnQiOiJCYWtlcnkgQXJ0aXNhbiIsImltYWdlIjoiaHR0cHM6XC9cL2ltYWdlcy51bnNwbGFzaC5jb21cL3Bob3RvLTE1NTU1MDcwMzYtYWIxZjQwMzg4MDhhP3c9NDAwIiwiY2F0ZWdvcnkiOiJCYWtlcnkiLCJxdWFudGl0eSI6MX1dLCJwcmljaW5nIjp7InN1YnRvdGFsIjoxMjAwMCwic2VydmljZSI6MjAwMCwidG90YWwiOjE0MDAwfSwidHlwZSI6InBpY2t1cCJ9', 14000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-25 06:20:38', '', NULL, '2025-11-25 06:20:33', '2025-11-25 06:20:38', NULL),
(8, 'HS-251125090534348', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzMiLCJuYW1lIjoiRG9uYXQgQ29rbGF0IElzaSBQcmVtaXVtIiwicHJpY2UiOiI4MDAwLjAwIiwib3JpZ2luYWxQcmljZSI6IjIwMDAwLjAwIiwicmVzdGF1cmFudCI6IkJha2VyeSBBcnRpc2FuIiwiaW1hZ2UiOiJodHRwczpcL1wvaW1hZ2VzLnVuc3BsYXNoLmNvbVwvcGhvdG8tMTU1MTAyNDYwMS1iZWM3OGFlYTcwNGI/dz00MDAiLCJjYXRlZ29yeSI6IlNuYWNrcyIsInF1YW50aXR5IjoxfV0sInByaWNpbmciOnsic3VidG90YWwiOjgwMDAsInNlcnZpY2UiOjIwMDAsInRvdGFsIjoxMDAwMH0sInR5cGUiOiJwaWNrdXAifQ==', 10000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-25 08:05:39', '', NULL, '2025-11-25 08:05:34', '2025-11-25 08:05:39', NULL),
(9, 'HS-251126160946753', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzIiLCJuYW1lIjoiQ3JvaXNzYW50IEJ1dHRlciBQcmVtaXVtIiwicHJpY2UiOiIxMjAwMC4wMCIsIm9yaWdpbmFsUHJpY2UiOiIyNTAwMC4wMCIsInJlc3RhdXJhbnQiOiJCYWtlcnkgQXJ0aXNhbiIsImltYWdlIjoiaHR0cHM6XC9cL2ltYWdlcy51bnNwbGFzaC5jb21cL3Bob3RvLTE1NTU1MDcwMzYtYWIxZjQwMzg4MDhhP3c9NDAwIiwiY2F0ZWdvcnkiOiJCYWtlcnkiLCJxdWFudGl0eSI6MX1dLCJwcmljaW5nIjp7InN1YnRvdGFsIjoxMjAwMCwic2VydmljZSI6MjAwMCwidG90YWwiOjE0MDAwfSwidHlwZSI6InBpY2t1cCJ9', 14000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-26 15:09:50', '', NULL, '2025-11-26 15:09:46', '2025-11-26 15:09:50', NULL),
(10, 'HS-251127044651630', 'marketplace', NULL, 9, 'eyJpdGVtcyI6W3siaWQiOiJwcm9kdWN0XzEiLCJuYW1lIjoiUm90aSBUYXdhciBHYW5kdW0gUHJlbWl1bSIsInByaWNlIjoiMTUwMDAuMDAiLCJvcmlnaW5hbFByaWNlIjoiMzUwMDAuMDAiLCJyZXN0YXVyYW50IjoiQmFrZXJ5IEFydGlzYW4iLCJpbWFnZSI6Imh0dHBzOlwvXC9pbWFnZXMudW5zcGxhc2guY29tXC9waG90by0xNTA5NDQwMTU5NTk2LTAyNDkwODg3NzJmZj93PTQwMCIsImNhdGVnb3J5IjoiQmFrZXJ5IiwicXVhbnRpdHkiOjF9LHsiaWQiOiJwcm9kdWN0XzMiLCJuYW1lIjoiRG9uYXQgQ29rbGF0IElzaSBQcmVtaXVtIiwicHJpY2UiOiI4MDAwLjAwIiwib3JpZ2luYWxQcmljZSI6IjIwMDAwLjAwIiwicmVzdGF1cmFudCI6IkJha2VyeSBBcnRpc2FuIiwiaW1hZ2UiOiJodHRwczpcL1wvaW1hZ2VzLnVuc3BsYXNoLmNvbVwvcGhvdG8tMTU1MTAyNDYwMS1iZWM3OGFlYTcwNGI/dz00MDAiLCJjYXRlZ29yeSI6IlNuYWNrcyIsInF1YW50aXR5IjoxfSx7ImlkIjoicHJvZHVjdF80IiwibmFtZSI6IlNhbmR3aWNoIEF5YW0gQkJRIiwicHJpY2UiOiIyMDAwMC4wMCIsIm9yaWdpbmFsUHJpY2UiOiI0NTAwMC4wMCIsInJlc3RhdXJhbnQiOiJCYWtlcnkgQXJ0aXNhbiIsImltYWdlIjoiaHR0cHM6XC9cL2ltYWdlcy51bnNwbGFzaC5jb21cL3Bob3RvLTE1Mjg3MzU2MDI3ODAtMjU1MmZkNDZjN2FmP3c9NDAwIiwiY2F0ZWdvcnkiOiJSZWFkeSB0byBFYXQiLCJxdWFudGl0eSI6MX1dLCJwcmljaW5nIjp7InN1YnRvdGFsIjo0MzAwMCwic2VydmljZSI6MjAwMCwidG90YWwiOjQ1MDAwfSwidHlwZSI6InBpY2t1cCJ9', 45000.00, 0.00, NULL, NULL, NULL, NULL, 'paid', NULL, '2025-11-27 03:46:55', '', NULL, '2025-11-27 03:46:51', '2025-11-27 03:46:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(50) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_type` enum('umkm','restaurant','cafe','bakery','other') NOT NULL,
  `business_address` text NOT NULL,
  `business_phone` varchar(20) DEFAULT NULL,
  `business_email` varchar(255) DEFAULT NULL,
  `business_description` text DEFAULT NULL,
  `business_logo` varchar(500) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bank_account_name` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `verification_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`verification_documents`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `user_id`, `business_name`, `business_type`, `business_address`, `business_phone`, `business_email`, `business_description`, `business_logo`, `bank_name`, `bank_account_number`, `bank_account_name`, `verification_status`, `verification_documents`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 'Bakery Artisan', 'bakery', 'Jl. Raya BSD No. 123, Tangerang Selatan', '081234567890', NULL, NULL, NULL, NULL, NULL, NULL, 'verified', NULL, 1, '2025-11-18 18:18:14', '2025-11-18 18:18:14'),
(2, 3, 'Healthy Bowl Cafe', 'cafe', 'Jl. Serpong Raya No. 45, BSD City', '081234567891', NULL, NULL, NULL, NULL, NULL, NULL, 'verified', NULL, 1, '2025-11-18 18:18:14', '2025-11-18 18:18:14'),
(3, 11, 'Ayam Bu Yudi', 'restaurant', 'allogio', '082222222222', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, 1, '2025-11-18 18:53:48', '2025-11-18 18:53:48');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `preparation_time` int(11) DEFAULT 15 COMMENT 'Waktu persiapan dalam menit',
  `pickup_instructions` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `expiry_time` datetime DEFAULT NULL,
  `pickup_location` varchar(255) DEFAULT NULL,
  `status` enum('available','sold_out','expired') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `partner_id`, `category_id`, `name`, `description`, `original_price`, `discounted_price`, `stock`, `preparation_time`, `pickup_instructions`, `image_url`, `expiry_time`, `pickup_location`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Roti Tawar Gandum Premium', 'Roti tawar gandum segar, cocok untuk sarapan sehat. Tanpa pengawet, 100% whole wheat.', 35000.00, 15000.00, 20, 15, NULL, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400', '2025-11-08 16:45:33', 'Bakery Artisan - Jl. Raya BSD No. 123', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33'),
(2, 2, 1, 'Croissant Butter Premium', 'Croissant klasik dengan butter premium imported. Crispy di luar, lembut di dalam.', 25000.00, 12000.00, 15, 15, NULL, 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400', '2025-11-08 14:45:33', 'Bakery Artisan - Jl. Raya BSD No. 123', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33'),
(3, 2, 5, 'Donat Coklat Isi Premium', 'Donat lembut dengan isian coklat manis dan topping coklat chips. Fresh from oven!', 20000.00, 8000.00, 30, 15, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400', '2025-11-08 15:45:33', 'Bakery Artisan - Jl. Raya BSD No. 123', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33'),
(4, 2, 2, 'Sandwich Ayam BBQ', 'Sandwich dengan ayam BBQ dan sayuran segar. Dengan roti whole wheat dan saus signature.', 45000.00, 20000.00, 10, 15, NULL, 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=400', '2025-11-08 13:45:33', 'Bakery Artisan - Jl. Raya BSD No. 123', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33'),
(5, 3, 3, 'Salad Bowl Sehat Premium', 'Salad segar dengan dressing pilihan: Caesar, Honey Mustard, atau Balsamic. Porsi besar!', 40000.00, 18000.00, 12, 15, NULL, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400', '2025-11-08 14:45:33', 'Healthy Bowl Cafe - Jl. Serpong Raya No. 45', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33'),
(6, 3, 2, 'Nasi Kotak Ayam Geprek', 'Nasi dengan ayam geprek pedas level 1-5. Dengan lalapan segar dan sambal matah.', 35000.00, 15000.00, 25, 15, NULL, 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=400', '2025-11-08 12:45:33', 'Healthy Bowl Cafe - Jl. Serpong Raya No. 45', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33'),
(7, 2, 1, 'Baguette Prancis', 'Baguette autentik Prancis dengan crust renyah. Perfect untuk breakfast atau dijadikan sandwich.', 30000.00, 13000.00, 8, 15, NULL, 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400', '2025-11-08 15:45:33', 'Bakery Artisan - Jl. Raya BSD No. 123', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33'),
(8, 3, 3, 'Buddha Bowl Quinoa', 'Buddha bowl dengan quinoa, roasted vegetables, chickpeas, dan tahini dressing. Vegan friendly!', 48000.00, 22000.00, 10, 15, NULL, 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400', '2025-11-08 13:45:33', 'Healthy Bowl Cafe - Jl. Serpong Raya No. 45', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33'),
(9, 2, 5, 'Cookies Chocolate Chips', 'Cookies premium dengan chocolate chips melimpah. Crispy outside, chewy inside. Pack isi 6pcs.', 28000.00, 12000.00, 18, 15, NULL, 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=400', '2025-11-08 17:45:33', 'Bakery Artisan - Jl. Raya BSD No. 123', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33'),
(10, 3, 4, 'Smoothie Bowl Tropical', 'Smoothie bowl dengan buah tropis segar: mangga, nanas, pisang. Topping granola dan chia seeds.', 38000.00, 17000.00, 15, 15, NULL, 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=400', '2025-11-08 14:45:33', 'Healthy Bowl Cafe - Jl. Serpong Raya No. 45', 'available', '2025-11-08 03:45:33', '2025-11-08 03:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','partner','agent','founder') DEFAULT 'customer',
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `address` text DEFAULT NULL,
  `profile_image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of roles: customer, partner, agent' CHECK (json_valid(`roles`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `status`, `address`, `profile_image`, `created_at`, `updated_at`, `roles`) VALUES
(1, 'Test Customer', 'user@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'founder', 'active', NULL, NULL, '2025-11-08 03:45:32', '2025-11-26 17:48:16', '[\"founder\"]'),
(2, 'Bakery Artisan', 'bakery@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', 'partner', 'active', NULL, NULL, '2025-11-08 03:45:32', '2025-11-18 18:34:16', '[\"partner\"]'),
(3, 'Healthy Bowl Cafe', 'cafe@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892', 'partner', 'active', NULL, NULL, '2025-11-08 03:45:32', '2025-11-18 18:16:50', '[\"partner\"]'),
(4, 'Agent Demo', 'agent@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567893', 'agent', 'active', NULL, NULL, '2025-11-08 03:45:32', '2025-11-18 18:34:16', '[\"agent\"]'),
(5, 'Admin Hungerswitch', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567894', '', 'active', NULL, NULL, '2025-11-08 03:45:32', '2025-11-18 18:16:50', '[\"admin\"]'),
(8, 'filbert', 'rei@gmail.com', '$2y$10$ymeBgazKBSJxd9jsAViwUesmD0V8VEKO0H3swn/2vIJhHG3IQyNy6', '123123', 'agent', 'active', NULL, NULL, '2025-11-09 14:47:07', '2025-11-18 18:16:50', '[\"agent\"]'),
(9, 'filbert', 'filbert@gmail.com', '$2y$10$jXhwVxRyD4ysIDHY2MTKIegqdwxThPlb1hEmZ0LLv.bOSKO4WSYsu', '08123123123', 'customer', 'active', '', NULL, '2025-11-18 17:41:31', '2025-11-18 18:16:50', '[\"customer\"]'),
(10, 'bren', 'bren@gmail.com', '$2y$10$n4gaGumE6Af0Gq/zpebCLeb1KLv1i10wqj3qzlkvPT.2jB//4wvUC', '081111111111', 'customer', 'active', NULL, NULL, '2025-11-18 18:46:47', '2025-11-18 18:46:47', '[\"customer\"]'),
(11, 'sondek', 'sondek@gmail.com', '$2y$10$MAEHvA3ogumYlzWZP0K47OAkkNCM1zlzRfcJl.j6LgLovCcS4bsI.', '082222222222', 'partner', 'active', NULL, NULL, '2025-11-18 18:53:48', '2025-11-18 18:53:48', '[\"partner\"]'),
(12, 'sonhoreg', 'sonhoreg@gmail.com', '$2y$10$.pK9IvL5uq7azWiQQyFFrOWU4BLV2u4lc/e.WdbSYKimfY5rBS8A6', '083333333333', 'agent', 'active', NULL, NULL, '2025-11-18 18:55:48', '2025-11-18 18:55:48', '[\"agent\"]'),
(13, 'brenda', 'brenda@gmail.com', '$2y$10$7N8dsDLsg6AvxGPYRyE5UOOYIP7r0a4c4iO9YQ1ojfNIZaq.btSby', '081111112376', 'customer', 'active', NULL, NULL, '2025-11-26 15:12:36', '2025-11-26 15:12:36', '[\"customer\"]'),
(14, 'Test Multi', 'multi@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456789', 'customer', 'active', NULL, NULL, '2025-11-26 17:05:04', '2025-11-26 17:05:04', '[\"customer\", \"partner\"]'),
(15, 'Admin Founder', 'founder@hungerswitch.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'founder', 'active', NULL, NULL, '2025-11-26 17:48:55', '2025-11-26 17:48:55', '[\"founder\"]');

-- --------------------------------------------------------

--
-- Table structure for table `user_donations`
--

CREATE TABLE `user_donations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `donation_number` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_proof` text DEFAULT NULL,
  `status` enum('pending','paid','failed') DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_donations`
--

INSERT INTO `user_donations` (`id`, `user_id`, `donation_number`, `amount`, `payment_method`, `payment_proof`, `status`, `paid_at`, `notes`, `created_at`) VALUES
(1, 9, 'DON-20251126-69272DDF89D5D', 100000.00, 'QRIS / Transfer', NULL, 'paid', '2025-11-26 16:42:07', NULL, '2025-11-26 16:42:07'),
(2, 9, 'DON-20251127-6927CBB6EAB64', 500000.00, 'QRIS / Transfer', NULL, 'paid', '2025-11-27 03:55:34', NULL, '2025-11-27 03:55:34'),
(3, 9, 'DON-20251127-6927CBCBB3457', 250000.00, 'QRIS / Transfer', NULL, 'paid', '2025-11-27 03:55:55', NULL, '2025-11-27 03:55:55'),
(4, 9, 'DON-20251127-6927CBD0C3CB6', 100000.00, 'QRIS / Transfer', NULL, 'paid', '2025-11-27 03:56:00', NULL, '2025-11-27 03:56:00'),
(5, 9, 'DON-20251127-6927CBD49DB12', 25000.00, 'QRIS / Transfer', NULL, 'paid', '2025-11-27 03:56:04', NULL, '2025-11-27 03:56:04'),
(6, 9, 'DON-20251127-6927CBD922939', 25000.00, 'QRIS / Transfer', NULL, 'paid', '2025-11-27 03:56:09', NULL, '2025-11-27 03:56:09'),
(7, 10, 'DON-20251127-6927CC85CBEAA', 2500000.00, 'QRIS / Transfer', NULL, 'paid', '2025-11-27 03:59:01', NULL, '2025-11-27 03:59:01');

-- --------------------------------------------------------

--
-- Structure for view `founder_dashboard_stats`
--
DROP TABLE IF EXISTS `founder_dashboard_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `founder_dashboard_stats`  AS SELECT (select count(0) from `users` where `users`.`role` = 'customer') AS `total_customers`, (select count(0) from `users` where `users`.`role` = 'partner') AS `total_partners`, (select count(0) from `users` where `users`.`role` = 'agent') AS `total_agents`, (select count(0) from `orders` where `orders`.`order_source` = 'marketplace') AS `total_marketplace_orders`, (select count(0) from `food_programs`) AS `total_programs`, (select sum(`food_programs`.`quantity_distributed`) from `food_programs`) AS `total_portions_distributed`, (select `donation_fund`.`current_balance` from `donation_fund` where `donation_fund`.`id` = 1) AS `donation_balance`, (select `donation_fund`.`total_received` from `donation_fund` where `donation_fund`.`id` = 1) AS `total_donations_received`, (select count(0) from `beneficiaries` where `beneficiaries`.`is_active` = 1) AS `total_active_beneficiaries` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_region` (`region`);

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_agent` (`registered_by_agent_id`),
  ADD KEY `idx_status` (`verification_status`),
  ADD KEY `idx_beneficiaries_agent_status` (`registered_by_agent_id`,`is_active`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `distribution_reports`
--
ALTER TABLE `distribution_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_program` (`program_id`),
  ADD KEY `idx_agent` (`agent_id`),
  ADD KEY `idx_date` (`report_date`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`payment_status`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `donation_fund`
--
ALTER TABLE `donation_fund`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donation_transactions`
--
ALTER TABLE `donation_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`transaction_type`),
  ADD KEY `idx_reference` (`reference_type`,`reference_id`);

--
-- Indexes for table `food_programs`
--
ALTER TABLE `food_programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_number` (`program_number`),
  ADD KEY `idx_agent` (`assigned_agent_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date` (`scheduled_date`),
  ADD KEY `idx_programs_status_date` (`status`,`scheduled_date`);

--
-- Indexes for table `impact_stats`
--
ALTER TABLE `impact_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_date` (`stat_date`),
  ADD KEY `idx_date` (`stat_date`);

--
-- Indexes for table `marketplace_transactions`
--
ALTER TABLE `marketplace_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_settlement` (`settlement_status`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_orders_source` (`order_source`,`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_partner` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_partner` (`partner_id`),
  ADD KEY `idx_expiry` (`expiry_time`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `user_donations`
--
ALTER TABLE `user_donations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `donation_number` (`donation_number`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `distribution_reports`
--
ALTER TABLE `distribution_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation_fund`
--
ALTER TABLE `donation_fund`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donation_transactions`
--
ALTER TABLE `donation_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `food_programs`
--
ALTER TABLE `food_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `impact_stats`
--
ALTER TABLE `impact_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `marketplace_transactions`
--
ALTER TABLE `marketplace_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_donations`
--
ALTER TABLE `user_donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agents`
--
ALTER TABLE `agents`
  ADD CONSTRAINT `agents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `distribution_reports`
--
ALTER TABLE `distribution_reports`
  ADD CONSTRAINT `distribution_reports_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `food_programs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `distribution_reports_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `partners`
--
ALTER TABLE `partners`
  ADD CONSTRAINT `partners_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
