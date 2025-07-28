-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 21 Jul 2025 pada 01.16
-- Versi server: 8.0.30
-- Versi PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laporaja`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `feedback`
--

CREATE TABLE `feedback` (
  `id` bigint UNSIGNED NOT NULL,
  `report_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `impression` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `satisfaction` tinyint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_04_01_165009_create_residents_table', 1),
(5, '2025_04_01_165117_create_report_categories_table', 1),
(6, '2025_04_01_165215_create_reports_table', 1),
(7, '2025_04_01_165404_create_report_statuses_table', 1),
(8, '2025_04_01_165732_create_permission_tables', 1),
(9, '2025_06_27_000000_add_phone_number_to_residents_table', 1),
(10, '2025_06_28_132308_create_feedback_table', 1),
(11, '2025_06_29_102438_add_urgency_level_to_reports_table', 1),
(12, '2025_07_13_000001_add_initial_urgency_to_reports_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard-view', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(2, 'user-view', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(3, 'user-create', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(4, 'user-edit', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(5, 'user-delete', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(6, 'resident-view', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(7, 'resident-create', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(8, 'resident-edit', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(9, 'resident-delete', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(10, 'report-category-view', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(11, 'report-category-create', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(12, 'report-category-edit', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(13, 'report-category-delete', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(14, 'report-view', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(15, 'report-create', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(16, 'report-edit', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(17, 'report-delete', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(18, 'report-status-view', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(19, 'report-status-create', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(20, 'report-status-edit', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(21, 'report-status-delete', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

CREATE TABLE `reports` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resident_id` bigint UNSIGNED NOT NULL,
  `report_category_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urgency_level` int NOT NULL DEFAULT '0',
  `initial_urgency` int NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `reports`
--

INSERT INTO `reports` (`id`, `code`, `resident_id`, `report_category_id`, `title`, `description`, `image`, `latitude`, `longitude`, `address`, `urgency_level`, `initial_urgency`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'LAPORAJA25387', 1, 1, 'ZZZ', 'Halo', 'assets/report/image/m3BlZTSwriYS8wsZMu53eBvJw0WiAYJMueG62Nfe.png', '-6.9181652', '106.93152', 'Jalan R. Syamsudin, S.H, Cikole, Sukabumi, Jawa Barat, Jawa, 43113, Indonesia', 3, 1, NULL, '2025-07-13 02:56:32', '2025-07-13 02:56:32'),
(2, 'LAPORAJA29507', 1, 2, 'AAA', 'AAAA', 'assets/report/image/nRHWc7Mw9iJjPP50Hj7Q51fxWzsTPmx9jTBzV1VJ.png', '-6.9181652', '106.93152', 'Jalan R. Syamsudin, S.H, Cikole, Sukabumi, Jawa Barat, Jawa, 43113, Indonesia', 2, 1, NULL, '2025-07-13 02:57:04', '2025-07-13 02:57:04'),
(3, 'LAPORAJA53187', 1, 1, 'AAAA', 'aaa', 'assets/report/image/a2V50j7WMNea58GFLdDuOt7eMZBDdBH7qE8QANvj.png', '-6.9181652', '106.93152', 'Jalan R. Syamsudin, S.H, Cikole, Sukabumi, Jawa Barat, Jawa, 43113, Indonesia', 3, 1, NULL, '2025-07-13 02:57:29', '2025-07-13 02:57:29'),
(4, 'LAPORAJA79340', 1, 3, 'ZZAAA', 'AAAA', 'assets/report/image/wpgN3GDYOIyIwvIwESeNqeIs2MDYjVPuIMMBVX4j.png', '-6.9181652', '106.93152', 'Jalan R. Syamsudin, S.H, Cikole, Sukabumi, Jawa Barat, Jawa, 43113, Indonesia', 1, 1, NULL, '2025-07-13 02:57:46', '2025-07-13 02:57:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `report_categories`
--

CREATE TABLE `report_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `report_categories`
--

INSERT INTO `report_categories` (`id`, `name`, `image`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Keamanan dan Keselamatan Publik', 'assets/report-category/image/Bsp8YqxNWNPziMWosbUWNNZyfZFCaMCG1ecbPZVh.png', NULL, '2025-07-13 02:54:17', '2025-07-13 02:54:17'),
(2, 'Infrastruktur dan Fasilitas Umum', 'assets/report-category/image/DmiDgk55yZwfev155NJS6Ul9ClxlPI8lJk44YW1i.png', NULL, '2025-07-13 02:54:43', '2025-07-13 02:54:43'),
(3, 'Administrasi dan Layanan Umum', 'assets/report-category/image/Ncc14CzdRLE2eSGKHGI6FxT5MFT0VXb6ErNCMJS0.png', NULL, '2025-07-13 02:55:14', '2025-07-13 02:55:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `report_statuses`
--

CREATE TABLE `report_statuses` (
  `id` bigint UNSIGNED NOT NULL,
  `report_id` bigint UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('delivered','in_process','completed','rejected') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `report_statuses`
--

INSERT INTO `report_statuses` (`id`, `report_id`, `image`, `status`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'delivered', 'Laporan Berhasil Diterima', NULL, '2025-07-13 02:56:32', '2025-07-13 02:56:32'),
(2, 2, NULL, 'delivered', 'Laporan Berhasil Diterima', NULL, '2025-07-13 02:57:04', '2025-07-13 02:57:04'),
(3, 3, NULL, 'delivered', 'Laporan Berhasil Diterima', NULL, '2025-07-13 02:57:29', '2025-07-13 02:57:29'),
(4, 4, NULL, 'delivered', 'Laporan Berhasil Diterima', NULL, '2025-07-13 02:57:46', '2025-07-13 02:57:46'),
(5, 1, 'assets/report-status/image/DJyTsSTodxXCmCoOxv3oqr74mm067qOFT6vy9M1f.png', 'in_process', 'Laporan Telah Diproses', NULL, '2025-07-14 02:50:16', '2025-07-14 02:50:16'),
(6, 1, 'assets/report-status/image/EvRACxUTpG60gU1sHS8PblUDE5W8KWg9cGxDTAgd.png', 'completed', 'Laporan Sudah Ditangani', NULL, '2025-07-15 03:43:04', '2025-07-15 03:43:04'),
(7, 3, 'assets/report-status/image/nMcfdDiXKaGVcfEVVkxgkqAesDRr5AH833GIbqn7.png', 'in_process', 'Test Proses', NULL, '2025-07-15 05:43:57', '2025-07-15 05:43:57'),
(8, 3, 'assets/report-status/image/Osll4zYX02ouFHaYjpevj24hnA0DAgUJTOw5kyLh.png', 'completed', 'Selesai', NULL, '2025-07-15 05:44:31', '2025-07-15 05:44:31'),
(9, 4, NULL, 'in_process', 'Proses', NULL, '2025-07-19 17:35:36', '2025-07-19 17:35:36'),
(10, 2, NULL, 'in_process', 'Diproses', NULL, '2025-07-19 17:36:22', '2025-07-19 17:36:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `residents`
--

CREATE TABLE `residents` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `residents`
--

INSERT INTO `residents` (`id`, `user_id`, `avatar`, `deleted_at`, `created_at`, `updated_at`, `phone_number`) VALUES
(1, 2, 'assets/avatar/uuCM7HccZtOGXQLta1jSuOluLMLpiXyhOItRZw9P.jpg', NULL, '2025-07-13 02:55:59', '2025-07-13 02:55:59', '081222255445');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03'),
(2, 'resident', 'web', '2025-07-13 02:53:03', '2025-07-13 02:53:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
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
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(10, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('CwMBOcJDKMBKFwX4TVskfYwPVVTqiW7gZMsq4X9t', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibFRHSHlrc2ljT0NMQ0Y5VnBnRVB0c2s0dEJsRnBPZUM0U1hYUzZkTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9yZXNpZGVudCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo1OiJhbGVydCI7YTowOnt9fQ==', 1752591119),
('EyAbxsTe98XY1am4FgOSsMZhxw2f8RYTKULRRjK2', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYlBlQnM1MEk4QWV3VldXcEsxbXpzWXJJRUlxZGh0MU9iODg1eVpMayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZXBvcnQvMSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1753058298),
('GYqfEPXEs18j6VJMsMbX9HzkcMw14MbNRg1Qq2b1', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWTU2NEoxY1NkVW10d1FZQmdFcFFudExiOE5RNFg5NlFLTkVyR3J4aCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNDoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2FkbWluL3JlcG9ydCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQzOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYWRtaW4vcmVwb3J0LWNhdGVnb3J5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1752589566),
('M6lXi9HeSoQ6OIgdBYVwreArfKCfqNHM4cIn2zJz', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNlVOTldyaFlWNVdJYXNLdWFRQTRubzZtWGgybG92N3NwWkNDSFJmYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9teS1yZXBvcnQ/c3RhdHVzPWRlbGl2ZXJlZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1752591658),
('VfhllcsbX0SJecHmDl46d65TvBBu1ol23RTVfnfh', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQ25PaVVVQTc3c1Z0cjJSWUYyTkl2eWVBd3BBbzRCSVg5Vkh6T0M0QiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NToiYWxlcnQiO2E6MDp7fX0=', 1752971924);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Admin Lapor Aja', 'admin@gmail.com', NULL, '$2y$12$r14fg.9QI/KzkayCXLgaM.nJQL9rYfvAPFd/.tK8ChX9f7KQdu98G', NULL, NULL, '2025-07-13 02:53:04', '2025-07-13 02:53:04'),
(2, 'Zaedan', 'zaedan@gmail.com', NULL, '$2y$12$lc8GVFSLbPTvlH.mak62UOt3qMwpQmRRm58h4cTLsbw8G6uMP3NES', NULL, NULL, '2025-07-13 02:55:59', '2025-07-13 02:55:59');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `feedback_report_id_unique` (`report_id`),
  ADD KEY `feedback_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reports_code_unique` (`code`);

--
-- Indeks untuk tabel `report_categories`
--
ALTER TABLE `report_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `report_statuses`
--
ALTER TABLE `report_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `report_categories`
--
ALTER TABLE `report_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `report_statuses`
--
ALTER TABLE `report_statuses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `residents`
--
ALTER TABLE `residents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
