-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 05:06 PM
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
-- Database: `portfolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$RIvLH/yIDUC70AapBMA7Ue.4SJrloJlwehf02VTBxC0ljvl.8yfAi'),
(2, 'Ahsen', '$2y$10$iejJKBE88jzE/pGeOc3iturSaU61zopHVlSHYJLZex.bjF02Q6/Jm');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `github_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `github_link`, `created_at`) VALUES
(1, 'IPHS Campus Management System', 'PHP system with role-based access', 'https://github.com/Ahsen2001', '2026-04-05 00:32:04'),
(2, 'HireHub Job Board Platform', 'A full-stack job board web application built with TypeScript, featuring user profiles, job listings, and application tracking.', 'https://github.com/Ahsen2001/hirehub-job-platform', '2026-06-28 14:15:00'),
(3, 'Onyxa E-Commerce Portal', 'A modern and feature-rich e-commerce and business solution developed using Laravel and Blade templates.', 'https://github.com/Ahsen2001/onyxa', '2026-06-28 14:15:00'),
(4, 'Daily Cart Mobile Client', 'A mobile e-commerce client application designed and developed with Dart and Flutter for a seamless user shopping experience.', 'https://github.com/Ahsen2001/daily_cart', '2026-06-28 14:15:00'),
(5, 'Ibnu Abbas Management System', 'An educational institution administration portal built with TypeScript, managing student records and operations.', 'https://github.com/Ahsen2001/Ibnu_Abbas', '2026-06-28 14:15:00'),
(6, 'JDik Masjid Management Portal', 'A management and administration web app for masjid activities, built using Laravel and Blade.', 'https://github.com/Ahsen2001/jdik-masjid-management', '2026-06-28 14:15:00'),
(7, 'Green Wealth Farm Application', 'A custom web application built with PHP for agricultural farm inventory, sales, and employee tracking.', 'https://github.com/Ahsen2001/greenwealthfarm', '2026-06-28 14:15:00'),
(8, 'Fake News Detection Model', 'A machine learning data mining system developed in Jupyter Notebooks to classify and identify fake news stories.', 'https://github.com/Ahsen2001/Fake-News-Detection', '2026-06-28 14:15:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
