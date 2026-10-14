-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 14, 2026 at 12:11 AM
-- Server version: 10.4.32-MariaDB-log
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `siap`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_institution` varchar(100) DEFAULT NULL,
  `check_in_time` datetime NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Hadir',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `purpose` text NOT NULL,
  `consumption_qty` int(10) UNSIGNED DEFAULT NULL COMMENT 'Jumlah peserta yang butuh konsumsi, null jika tidak dicentang',
  `other_notes` text DEFAULT NULL COMMENT 'Catatan kebutuhan lain-lain, null jika tidak dicentang',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Approved, 2=Rejected, 3=Cancelled',
  `booking_type` varchar(20) NOT NULL DEFAULT 'Regular' COMMENT 'Regular / Priority (Direksi)',
  `displaced_by_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID booking Priority yang menyebabkan booking ini bentrok, null jika tidak ada konflik',
  `relocation_note` text DEFAULT NULL COMMENT 'Catatan admin saat mengalihkan booking ini ke ruangan/jam lain',
  `awaiting_user_confirmation` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'True = menunggu peminjam konfirmasi (setuju/tolak) hasil pengalihan admin',
  `relocated_from_room_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Ruangan asal sebelum dialihkan (snapshot pengalihan terakhir)',
  `relocated_from_start_time` datetime DEFAULT NULL COMMENT 'Waktu mulai asal sebelum dialihkan',
  `relocated_from_end_time` datetime DEFAULT NULL COMMENT 'Waktu selesai asal sebelum dialihkan',
  `relocated_at` datetime DEFAULT NULL COMMENT 'Kapan admin mengusulkan pengalihan ini (untuk timeline)',
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL COMMENT 'Waktu admin melakukan aksi',
  `rejection_reason` text DEFAULT NULL COMMENT 'Alasan penolakan atau pembatalan',
  `approval_note` text DEFAULT NULL COMMENT 'Catatan admin untuk peminjam saat menyetujui booking, opsional',
  `absent_code` varchar(100) DEFAULT NULL,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `room_id`, `start_time`, `end_time`, `purpose`, `consumption_qty`, `other_notes`, `status`, `booking_type`, `displaced_by_id`, `relocation_note`, `awaiting_user_confirmation`, `relocated_from_room_id`, `relocated_from_start_time`, `relocated_from_end_time`, `relocated_at`, `processed_by`, `processed_at`, `rejection_reason`, `approval_note`, `absent_code`, `qr_code_path`, `created_at`, `updated_at`) VALUES
(24, 1, 8, '2026-08-28 07:00:00', '2026-08-28 08:00:00', 'grewafcacasc', 12, NULL, 1, 'Regular', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, '2026-08-27 10:05:43', NULL, 'fefasad', 'MTG-6A8FA9873AC59', NULL, '2026-08-27 03:05:21', '2026-08-27 03:05:43');

-- --------------------------------------------------------

--
-- Table structure for table `booking_facilities`
--

CREATE TABLE `booking_facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `facility_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Divisi Alat Berat & Angkutan', NULL, '2026-08-10 08:10:08'),
(3, 'Divisi SDM, Umum & K3', NULL, '2026-08-10 08:09:39');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `name`, `created_at`, `updated_at`) VALUES
(6, 'Videotron', '2026-08-07 01:00:43', '2026-08-07 01:00:43'),
(7, 'Microphones', '2026-08-07 01:01:34', '2026-08-19 07:05:57');

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
(1, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_08_13_052001_create_divisions_table', 1),
(6, '2023_08_13_052002_create_positions_table', 1),
(7, '2023_08_13_052003_create_users_table', 1),
(8, '2023_08_13_052004_create_rooms_table', 1),
(9, '2023_08_13_052005_create_facilities_table', 1),
(10, '2023_08_13_052006_create_bookings_table', 1),
(11, '2023_08_13_052007_create_booking_facilities_table', 1),
(12, '2023_08_13_052008_create_attendances_table', 1),
(13, '2023_08_13_052009_create_complaints_table', 1),
(14, '2023_08_13_052010_create_notifications_table', 1),
(15, '2023_08_13_052011_create_room_photos_table', 1),
(16, '2026_08_03_000000_create_room_facilities_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `booking_id`, `created_at`, `updated_at`) VALUES
(58, 1, 'Peminjaman ruangan baru dari Administrator Sistem untuk ruangan Ruang Rapat Rinjani pada 28 Aug 2026, 07:00 - 08:00. Keperluan: grewafcacasc', 0, 24, '2026-08-27 03:05:21', '2026-08-27 03:05:21'),
(59, 1, 'Status peminjaman ruangan Ruang Rapat Rinjani Anda telah diubah dari Pending menjadi Disetujui. Anda dapat menggunakan ruangan pada 28 Aug 2026, 07:00 - 08:00. Catatan admin: fefasad', 0, 24, '2026-08-27 03:05:43', '2026-08-27 03:05:43');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `division_id`, `name`, `created_at`, `updated_at`) VALUES
(3, 3, 'Umumd', NULL, '2026-08-19 09:10:16'),
(4, 1, 'Alat Berat Wilayah 1', '2026-08-10 08:12:19', '2026-08-10 08:12:19');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 10,
  `location` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `capacity`, `location`, `status`, `created_at`, `updated_at`) VALUES
(8, 'Ruang Rapat Rinjani', 40, 'Lantai 2 Gedung C', 1, '2026-08-07 03:19:37', '2026-08-07 08:12:52'),
(9, 'Ruang Rapat Semeru', 10, 'Lantai 3 Gedung A', 1, '2026-08-10 15:08:23', '2026-08-12 01:07:27');

-- --------------------------------------------------------

--
-- Table structure for table `room_displays`
--

CREATE TABLE `room_displays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'room' COMMENT 'room = layar khusus 1 ruangan, all = papan semua ruangan',
  `display_token` varchar(64) NOT NULL COMMENT 'Token rahasia di URL, pengganti login untuk perangkat TV',
  `orientation` varchar(20) NOT NULL DEFAULT 'landscape' COMMENT 'landscape atau portrait',
  `theme` varchar(10) NOT NULL DEFAULT 'light' COMMENT 'light atau dark - tema warna tampilan TV/digital signage',
  `show_qr` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Tampilkan QR code booking ruangan di layar TV',
  `show_upcoming` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Tampilkan daftar jadwal berikutnya hari ini',
  `upcoming_count` tinyint(3) UNSIGNED NOT NULL DEFAULT 3 COMMENT 'Berapa jadwal berikutnya yang ditampilkan',
  `refresh_seconds` smallint(5) UNSIGNED NOT NULL DEFAULT 60 COMMENT 'Interval auto-refresh halaman TV, dalam detik',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Non-aktifkan sementara tanpa menghapus konfigurasi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_displays`
--

INSERT INTO `room_displays` (`id`, `room_id`, `type`, `display_token`, `orientation`, `theme`, `show_qr`, `show_upcoming`, `upcoming_count`, `refresh_seconds`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'all', 'pUwK6b22BfW4LYeIdcJNe6KlFTN5JBNMRRTeR07OffPWoqFG', 'landscape', 'light', 1, 1, 3, 60, 1, '2026-08-11 07:53:40', '2026-08-13 09:07:55');

-- --------------------------------------------------------

--
-- Table structure for table `room_facilities`
--

CREATE TABLE `room_facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `facility_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_facilities`
--

INSERT INTO `room_facilities` (`id`, `room_id`, `facility_id`, `quantity`, `created_at`, `updated_at`) VALUES
(8, 8, 7, 20, '2026-08-07 03:19:37', '2026-08-12 01:58:53'),
(10, 8, 6, 1, '2026-08-13 02:09:27', '2026-08-13 02:09:27');

-- --------------------------------------------------------

--
-- Table structure for table `room_photos`
--

CREATE TABLE `room_photos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_photos`
--

INSERT INTO `room_photos` (`id`, `room_id`, `photo_path`, `order`, `created_at`, `updated_at`) VALUES
(18, 8, 'rooms/mCwvRRmTVOujz8NcxDPcpkLK0h6QvjyK3XAEGK7W.jpg', 0, '2026-08-07 08:22:02', '2026-08-07 08:22:02'),
(19, 9, 'rooms/MIp9wr3bnb2o1KdAw3XInAqsjO0uuG3ZTRrahGTC.jpg', 0, '2026-08-10 15:08:24', '2026-08-10 15:08:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nip` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `position_id` bigint(20) UNSIGNED NOT NULL,
  `gender` enum('L','P') NOT NULL DEFAULT 'L',
  `employment_status` varchar(50) NOT NULL DEFAULT 'Kontrak',
  `password` varchar(255) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 2 COMMENT '1=Admin, 2=User, 3=Sesdir',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nip`, `full_name`, `division_id`, `position_id`, `gender`, `employment_status`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'ADM001', 'Administrator Sistem', 3, 3, 'L', 'Tetap', '$2y$10$vYedsbsFQ336zdHxeg3cFOtMaBiMBylhItPc./HisSEOWZiaOnCFi', 1, 1, 'rpMl2FAhTIcapH2jRnZg2njVhfZAcdfrjgkZPKOCTsybWCakmcNCCtRRbNWm', '2026-08-05 01:37:46', '2026-08-10 08:11:51'),
(5, 'AB001', 'Alber Wilayah 1', 1, 4, 'L', 'Kontrak', '$2y$10$erz/r/ZJSVDnfMVPITAE/ehKFPzARkUx8T6cKM8uYq4sFceHAJRRG', 2, 1, NULL, '2026-08-10 08:32:44', '2026-08-10 08:32:44'),
(6, 'SES001', 'Sesdir 01', 3, 3, 'L', 'Kontrak', '$2y$10$YQeckJ0JoGpk8eKDxykXROr.joA6vhZx8fKWNZJuyuqRzYHfkmP66', 3, 1, NULL, '2026-08-10 08:33:06', '2026-08-10 08:33:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_booking_id_foreign` (`booking_id`),
  ADD KEY `attendances_user_id_foreign` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookings_absent_code_unique` (`absent_code`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_room_id_foreign` (`room_id`),
  ADD KEY `bookings_processed_by_foreign` (`processed_by`),
  ADD KEY `bookings_displaced_by_id_foreign` (`displaced_by_id`),
  ADD KEY `bookings_relocated_from_room_id_foreign` (`relocated_from_room_id`);

--
-- Indexes for table `booking_facilities`
--
ALTER TABLE `booking_facilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_facilities_booking_id_foreign` (`booking_id`),
  ADD KEY `booking_facilities_facility_id_foreign` (`facility_id`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `divisions_name_unique` (`name`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `facilities_name_unique` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_booking_id_foreign` (`booking_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `positions_name_unique` (`name`),
  ADD KEY `positions_division_id_foreign` (`division_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_displays`
--
ALTER TABLE `room_displays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_displays_room_id_unique` (`room_id`),
  ADD UNIQUE KEY `room_displays_display_token_unique` (`display_token`);

--
-- Indexes for table `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_facilities_room_id_facility_id_unique` (`room_id`,`facility_id`),
  ADD KEY `room_facilities_facility_id_foreign` (`facility_id`);

--
-- Indexes for table `room_photos`
--
ALTER TABLE `room_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_photos_room_id_foreign` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_nip_unique` (`nip`),
  ADD KEY `users_division_id_foreign` (`division_id`),
  ADD KEY `users_position_id_foreign` (`position_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `booking_facilities`
--
ALTER TABLE `booking_facilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `room_displays`
--
ALTER TABLE `room_displays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `room_facilities`
--
ALTER TABLE `room_facilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `room_photos`
--
ALTER TABLE `room_photos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_displaced_by_id_foreign` FOREIGN KEY (`displaced_by_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_relocated_from_room_id_foreign` FOREIGN KEY (`relocated_from_room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_facilities`
--
ALTER TABLE `booking_facilities`
  ADD CONSTRAINT `booking_facilities_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_facilities_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`);

--
-- Constraints for table `room_displays`
--
ALTER TABLE `room_displays`
  ADD CONSTRAINT `room_displays_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD CONSTRAINT `room_facilities_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_facilities_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_photos`
--
ALTER TABLE `room_photos`
  ADD CONSTRAINT `room_photos_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
