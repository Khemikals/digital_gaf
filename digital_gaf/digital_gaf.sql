-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2026 at 08:08 PM
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
-- Database: `digital_gaf`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_logs`
--

CREATE TABLE `access_logs` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `login_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `access_logs`
--

INSERT INTO `access_logs` (`id`, `username`, `ip_address`, `login_time`) VALUES
(2, 'admin', '::1', '2025-12-20 12:26:18'),
(3, 'admin', '::1', '2025-12-20 12:53:46'),
(4, 'admin', '::1', '2025-12-20 13:02:06'),
(5, 'admin', '::1', '2025-12-20 19:17:04'),
(6, 'admin', '::1', '2025-12-20 19:18:56'),
(7, 'admin', '::1', '2025-12-20 20:45:37'),
(8, 'admin', '::1', '2025-12-20 21:26:19'),
(9, 'admin', '::1', '2025-12-21 01:48:23'),
(10, 'admin', '::1', '2025-12-21 01:51:30'),
(11, 'admin', '::1', '2025-12-21 07:47:29'),
(12, 'admin', '::1', '2025-12-21 07:47:47'),
(13, 'admin', '::1', '2025-12-21 09:43:01'),
(14, 'admin', '::1', '2025-12-21 10:19:44'),
(15, 'admin', '::1', '2025-12-21 12:23:03'),
(16, 'admin', '::1', '2025-12-21 15:34:32'),
(17, 'admin', '::1', '2025-12-21 17:58:52'),
(18, 'admin', '::1', '2025-12-21 21:32:12'),
(19, 'admin', '::1', '2025-12-22 10:32:24'),
(20, 'admin', '::1', '2025-12-22 11:29:05'),
(21, 'admin', '192.168.1.184', '2025-12-22 12:07:42'),
(22, 'admin', '192.168.1.184', '2025-12-22 14:51:01'),
(23, 'admin', '192.168.1.184', '2025-12-22 19:17:12'),
(24, 'admin', '::1', '2025-12-22 19:51:51'),
(25, 'admin', '::1', '2026-01-05 12:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'admin', 'gaf_command_2025', '2025-12-19 19:50:52');

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `applicant_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `branch_choice` enum('Army','Navy','Air Force') NOT NULL,
  `height` decimal(3,2) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `region_of_residence` varchar(50) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ghana_card_number` varchar(20) DEFAULT 'GHA-0000',
  `digital_address` varchar(20) DEFAULT 'GA-000-00',
  `place_of_birth` varchar(100) DEFAULT NULL,
  `constituency` varchar(100) DEFAULT NULL,
  `place_of_residence` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `birth_region` varchar(50) DEFAULT NULL,
  `birth_constituency` varchar(100) DEFAULT NULL,
  `res_place` varchar(100) DEFAULT NULL,
  `res_region` varchar(50) DEFAULT NULL,
  `res_constituency` varchar(100) DEFAULT NULL,
  `birth_place` varchar(100) DEFAULT NULL,
  `application_status` enum('Pending','Shortlisted','Scheduled','Qualified','Rejected') DEFAULT 'Pending',
  `screening_center` varchar(255) DEFAULT NULL,
  `screening_date` date DEFAULT NULL,
  `vetting_notes` text DEFAULT NULL,
  `authorized_by` varchar(255) DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ghana_card` varchar(20) DEFAULT NULL,
  `passport_path` varchar(255) DEFAULT NULL,
  `ghana_card_front_path` varchar(255) DEFAULT NULL,
  `ghana_card_back_path` varchar(255) DEFAULT NULL,
  `birth_cert_path` varchar(255) DEFAULT NULL,
  `wassce_cert_path` varchar(255) DEFAULT NULL,
  `bece_cert_path` varchar(255) DEFAULT NULL,
  `jhs_name` varchar(255) DEFAULT NULL,
  `jhs_index` varchar(50) DEFAULT NULL,
  `jhs_math` varchar(5) DEFAULT NULL,
  `jhs_eng` varchar(5) DEFAULT NULL,
  `shs_name` varchar(255) DEFAULT NULL,
  `shs_index` varchar(50) DEFAULT NULL,
  `shs_program` varchar(100) DEFAULT NULL,
  `math_grade` varchar(5) DEFAULT NULL,
  `eng_grade` varchar(5) DEFAULT NULL,
  `vetting_remarks` text DEFAULT NULL,
  `vetted_by` varchar(255) DEFAULT NULL,
  `medical_status` enum('Pending','Fit','Unfit') DEFAULT 'Pending',
  `medical_report_path` varchar(255) DEFAULT NULL,
  `medical_remarks` text DEFAULT NULL,
  `medical_officer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`applicant_id`, `first_name`, `last_name`, `branch_choice`, `height`, `gender`, `dob`, `date_of_birth`, `height_cm`, `region_of_residence`, `phone_number`, `email`, `ghana_card_number`, `digital_address`, `place_of_birth`, `constituency`, `place_of_residence`, `age`, `weight`, `birth_region`, `birth_constituency`, `res_place`, `res_region`, `res_constituency`, `birth_place`, `application_status`, `screening_center`, `screening_date`, `vetting_notes`, `authorized_by`, `processed_at`, `ghana_card`, `passport_path`, `ghana_card_front_path`, `ghana_card_back_path`, `birth_cert_path`, `wassce_cert_path`, `bece_cert_path`, `jhs_name`, `jhs_index`, `jhs_math`, `jhs_eng`, `shs_name`, `shs_index`, `shs_program`, `math_grade`, `eng_grade`, `vetting_remarks`, `vetted_by`, `medical_status`, `medical_report_path`, `medical_remarks`, `medical_officer`) VALUES
(1, 'Leon Akwasi Sylvester', 'Owusu', 'Army', 1.80, 'Male', '2005-08-11', NULL, NULL, NULL, '594264517', NULL, 'GHA-0000', 'GA-000-00', NULL, NULL, NULL, NULL, 68.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Qualified', 'Burma Camp, Liberation Barracks (Accra)', '2026-01-01', NULL, NULL, '2025-12-24 21:49:59', 'GHA-715464606-9', 'uploads/passports/GHA-715464606-9_photo.JPG', 'uploads/documents/GHA-715464606-9_ghana_card_front.jpeg', 'uploads/documents/GHA-715464606-9_ghana_card_back.jpeg', 'uploads/documents/GHA-715464606-9_birth_cert.jpeg', 'uploads/documents/GHA-715464606-9_wassce_cert.jpeg', 'uploads/documents/GHA-715464606-9_bece_cert.jpeg', 'SANKORE ISLAMIC JHS', '0610724103', '1', '1', 'EJISUMAN SENIOR HIGH SCHOOL ', '0051062742', 'General Science', 'A1', 'A1', NULL, NULL, 'Pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `applicant_documents`
--

CREATE TABLE `applicant_documents` (
  `doc_id` int(11) NOT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `passport_picture_path` varchar(255) DEFAULT NULL,
  `wassce_cert_path` varchar(255) DEFAULT NULL,
  `birth_cert_path` varchar(255) DEFAULT NULL,
  `ghana_card_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applicant_education`
--

CREATE TABLE `applicant_education` (
  `edu_id` int(11) NOT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `jhs_name` varchar(255) DEFAULT NULL,
  `bece_index` varchar(20) DEFAULT NULL,
  `jhs_year` varchar(4) DEFAULT NULL,
  `shs_name` varchar(255) DEFAULT NULL,
  `shs_program` varchar(100) DEFAULT NULL,
  `wassce_index` varchar(20) DEFAULT NULL,
  `shs_year` varchar(4) DEFAULT NULL,
  `math_grade` varchar(2) DEFAULT NULL,
  `eng_grade` varchar(2) DEFAULT NULL,
  `science_grade` varchar(2) DEFAULT NULL,
  `sub_3_name` varchar(50) DEFAULT NULL,
  `sub_3_grade` varchar(2) DEFAULT NULL,
  `sub_4_name` varchar(50) DEFAULT NULL,
  `sub_4_grade` varchar(2) DEFAULT NULL,
  `sub_5_name` varchar(50) DEFAULT NULL,
  `sub_5_grade` varchar(2) DEFAULT NULL,
  `sub_6_name` varchar(50) DEFAULT NULL,
  `sub_6_grade` varchar(2) DEFAULT NULL,
  `elective_subject` varchar(100) DEFAULT NULL,
  `elective_grade` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `application_status` varchar(20) DEFAULT 'Submitted',
  `assigned_venue` varchar(100) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `screening_date` date DEFAULT NULL,
  `screening_location` varchar(255) DEFAULT NULL,
  `screening_id` varchar(50) DEFAULT NULL,
  `disqualification_reason` text DEFAULT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `application_category` enum('Civilian Recruit','Tradesman','Officer Cadet') DEFAULT 'Civilian Recruit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `civilian_access_logs`
--

CREATE TABLE `civilian_access_logs` (
  `access_id` int(11) NOT NULL,
  `voucher_serial` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `login_status` enum('Succeeded','Denied') NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `civilian_access_logs`
--

INSERT INTO `civilian_access_logs` (`access_id`, `voucher_serial`, `ip_address`, `login_status`, `attempt_time`) VALUES
(1, 'ghdhhdhhd', '192.168.1.184', 'Denied', '2025-12-22 13:41:46'),
(2, 'GAF-TEST-001', '192.168.1.184', 'Succeeded', '2025-12-22 15:02:47'),
(3, 'GAF-TEST-001', '::1', 'Denied', '2025-12-22 19:38:12'),
(4, 'GAF-TEST-001', '::1', 'Succeeded', '2025-12-22 19:42:07'),
(5, 'GAF-TEST-001', '::1', 'Denied', '2025-12-22 19:51:11'),
(6, 'GAF-TEST-001', '::1', 'Denied', '2025-12-24 06:36:35'),
(7, 'GAF-TEST-001', '::1', 'Denied', '2025-12-24 20:44:49'),
(8, 'GAF-TEST-001', '::1', 'Succeeded', '2025-12-24 20:48:24'),
(9, 'GAF-TEST-001', '::1', 'Succeeded', '2025-12-24 20:50:30'),
(10, 'GAF-TEST-001', '192.168.1.184', 'Succeeded', '2025-12-24 21:12:10'),
(11, 'GAF-TEST-001', '::1', 'Denied', '2026-01-05 11:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `doc_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gate_logs`
--

CREATE TABLE `gate_logs` (
  `log_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `officer_username` varchar(50) NOT NULL,
  `check_in_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `medical_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `medical_report_path` varchar(255) NOT NULL,
  `medical_status` enum('Fit','Unfit','Pending') DEFAULT 'Pending',
  `medical_remarks` text DEFAULT NULL,
  `medical_officer` varchar(100) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_results`
--

CREATE TABLE `medical_results` (
  `medical_id` int(11) NOT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `test_outcome` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`) VALUES
(2, 'commander', '$2y$10$/ii8uH1aLtGKILejV5CIn.5Qau19oVHaeN3eCXpZu9qgBznl6zW5u'),
(3, 'admin', '$2y$10$pLidrW58YhYisWfW/I8YF.uS0/YpWf2Y1l7l6vX5f/mP.R8j.YxW.'),
(5, 'GAF_ADMIN_01', '$2y$10$geW33I7EDAvyPIgxB6NrRub.uZWezt4nTxiI6tbKTbvlhecbLLw/G');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `voucher_id` int(11) NOT NULL,
  `serial_number` varchar(20) DEFAULT NULL,
  `pin` varchar(20) NOT NULL,
  `pin_code` varchar(10) DEFAULT NULL,
  `status` varchar(10) DEFAULT 'Active',
  `used_by_applicant_id` int(11) DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `category` varchar(50) DEFAULT 'Civilian Recruit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`voucher_id`, `serial_number`, `pin`, `pin_code`, `status`, `used_by_applicant_id`, `is_used`, `category`) VALUES
(1, 'GAF-2025-001', '', '12345', 'active', 106, 0, 'Civilian Recruit'),
(2, 'GAF-2025-002', '', '67890', 'active', NULL, 0, 'Civilian Recruit'),
(3, 'GAF-2025-003', '', '11223', 'active', NULL, 0, 'Civilian Recruit'),
(4, 'GAF-2025-1001', '', '998877', 'active', NULL, 0, 'Civilian Recruit'),
(11, 'GAF-2025-TEST', '123456', NULL, 'active', NULL, 0, 'Civilian Recruit'),
(12, 'GAF2025NEW', '', '123456', 'active', NULL, 0, 'Civilian Recruit'),
(13, 'GAF-TEST-001', '', '111222', 'used', NULL, 0, 'Civilian Recruit'),
(14, 'GAF-TEST-002', '', '222333', 'active', NULL, 0, 'Civilian Recruit'),
(15, 'GAF-TEST-003', '', '333444', 'active', NULL, 0, 'Civilian Recruit'),
(16, 'GAF-TEST-004', '', '444555', 'active', NULL, 0, 'Civilian Recruit'),
(17, 'GAF-TEST-005', '', '555666', 'active', NULL, 0, 'Civilian Recruit'),
(18, 'GAF-TEST-006', '', '666777', 'active', NULL, 0, 'Civilian Recruit'),
(19, 'GAF-TEST-007', '', '777888', 'active', NULL, 0, 'Civilian Recruit'),
(20, 'GAF-TEST-008', '', '888999', 'active', NULL, 0, 'Civilian Recruit'),
(21, 'GAF-TEST-009', '', '999000', 'active', NULL, 0, 'Civilian Recruit'),
(22, 'GAF-TEST-010', '', '101010', 'active', NULL, 0, 'Civilian Recruit');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`applicant_id`);

--
-- Indexes for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `applicant_education`
--
ALTER TABLE `applicant_education`
  ADD PRIMARY KEY (`edu_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `civilian_access_logs`
--
ALTER TABLE `civilian_access_logs`
  ADD PRIMARY KEY (`access_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `gate_logs`
--
ALTER TABLE `gate_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`medical_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `medical_results`
--
ALTER TABLE `medical_results`
  ADD PRIMARY KEY (`medical_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `applicant_education`
--
ALTER TABLE `applicant_education`
  MODIFY `edu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `civilian_access_logs`
--
ALTER TABLE `civilian_access_logs`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gate_logs`
--
ALTER TABLE `gate_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `medical_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_results`
--
ALTER TABLE `medical_results`
  MODIFY `medical_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  ADD CONSTRAINT `applicant_documents_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`);

--
-- Constraints for table `applicant_education`
--
ALTER TABLE `applicant_education`
  ADD CONSTRAINT `applicant_education_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`);

--
-- Constraints for table `gate_logs`
--
ALTER TABLE `gate_logs`
  ADD CONSTRAINT `gate_logs_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`);

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`);

--
-- Constraints for table `medical_results`
--
ALTER TABLE `medical_results`
  ADD CONSTRAINT `medical_results_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
