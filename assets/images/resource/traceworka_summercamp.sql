-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 29, 2026 at 04:58 PM
-- Server version: 8.0.46-cll-lve
-- PHP Version: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `traceworka_summercamp`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General Enquiry',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'Tosin Ajibade', 'ajibade_tosin@yahoo.com', '08152519433', 'Registration Enquiry', 'Jdjdndbdhsvsbsnncndbrbbdbdbdbbd', 1, '2026-06-26 20:32:11');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int UNSIGNED NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('Male','Female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `age` tinyint UNSIGNED NOT NULL,
  `school` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_grade` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `learning_track` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `courses` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `medical_condition` text COLLATE utf8mb4_unicode_ci,
  `allergies` text COLLATE utf8mb4_unicode_ci,
  `emergency_contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emergency_phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emergency_relationship` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_children` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `amount_to_pay` int UNSIGNED DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `first_name`, `last_name`, `other_name`, `gender`, `date_of_birth`, `age`, `school`, `class_grade`, `address`, `parent_name`, `relationship`, `phone`, `alt_phone`, `email`, `parent_address`, `learning_track`, `courses`, `medical_condition`, `allergies`, `emergency_contact`, `emergency_phone`, `emergency_relationship`, `package`, `number_of_children`, `amount_to_pay`, `status`, `admin_notes`, `created_at`) VALUES
(1, 'Farouk', 'Bello', 'Favour', 'Male', '2017-02-08', 9, 'Traceworka Montessori School', 'Basic 2', 'Ajibode', 'Madam B', 'Mother', '+2349071543344', '', 'ajibade_tosin@yahoo.com', 'Bodija', 'Vocational Skills', 'Baking & Pastry', 'Stooling and Purging', '', 'Madam B', '+2349071543344', 'Mother', 'Early Bird', 1, 45000, 'confirmed', 'Paid(Test)', '2026-06-27 18:16:34'),
(2, 'John', 'Bull', '', 'Male', '2008-11-13', 17, 'OAFP', 'SS3', 'Parakin', 'Thomas Bull', 'Father', '+2348152519433', '+2348168967327', 'ajibade_tosin@yahoo.com', 'Parakin', 'Technology', 'Coding & Programming', '', '', 'Thomas Bull', '+2348152519433', 'Father', 'Premium', 2, 133000, 'cancelled', NULL, '2026-06-27 18:27:12'),
(3, 'Deanna', 'Bull', '', 'Female', '2010-06-09', 16, 'HighmarkS Academy', 'SS2', 'Parakin', 'Thomas Bull', 'Father', '+2348152519433', '+2348168967327', 'ajibade_tosin@yahoo.com', 'Parakin', 'Entrepreneurship', 'Financial Planning', 'Savant Syndrome', 'Lactose Intolerance', 'Thomas Bull', '+2348152519433', 'Father', 'Premium', 2, 133000, 'confirmed', '', '2026-06-27 18:27:12'),
(4, 'Perfect', 'Godwill', 'Ifedayo', 'Male', '2011-09-11', 14, 'TETREM Group of Schools', 'Grade 2', '167, Adetokunbo Ademola Crescent, Wuse 2, FCT, Abuja', 'Oluwabusayo Mosaku', 'Mother', '+2347067386984', '+2347067386984', 'oluwabusayojuliana22@gmail.com', '167, Adetokunbo Ademola Crescent, Wuse 2, FCT, Abuja', 'Technology', 'UI/UX Design', 'None', 'None', 'Oluwabusayo Godwill', '+2347067386984', 'Mother', 'Early Bird', 1, 45000, 'confirmed', NULL, '2026-06-29 14:28:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_package` (`package`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_track` (`learning_track`),
  ADD KEY `idx_created` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
