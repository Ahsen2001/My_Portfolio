-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2026 at 09:30 PM
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
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'image/Profile.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `name`, `title`, `bio`, `profile_image`) VALUES
(1, 'Umer Ahsen', 'Full Stack Web Developer', 'A motivated and detail-oriented web development graduate with strong hands-on experience in HTML, CSS, JavaScript, PHP, and MySQL. Passionate about building interactive, scalable, and secure applications. I am currently pursuing my BA (Hons) in ICT at the South Eastern University of Sri Lanka.', 'image/Profile.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `name`) VALUES
(1, 'HTML / CSS / Javascript'),
(2, 'PHP & MySQL'),
(3, 'ReactJS'),
(4, 'Bootstrap & Responsive Design'),
(5, 'Git & GitHub Workflow');

-- --------------------------------------------------------

--
-- Table structure for table `timeline`
--

CREATE TABLE `timeline` (
  `id` int(11) NOT NULL,
  `type` enum('experience','education') NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `duration_dates` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timeline`
--

INSERT INTO `timeline` (`id`, `type`, `title`, `subtitle`, `duration_dates`, `description`) VALUES
(1, 'experience', 'Tutor', 'BRIGHT MINDS COLLEGE', 'Nov 2024 - 2025', 'Instructor leading the Diploma in Computer Basics program, preparing students with foundational computer literacy and systems training.'),
(2, 'experience', 'Teacher', 'AN NOOR ACADEMY', 'Feb 2022 - 2024', 'Delivered student-centered lessons and curriculum guidance over two academic years of school instruction.'),
(3, 'education', 'BA (Hons) ICT', 'South Eastern University of Sri Lanka', 'Jul 2022 - Present', 'Currently pursuing professional qualifications, with specialization in modern computing methods and information technology systems.'),
(4, 'education', 'HNDIT', 'ATI Batticaloa', 'Apr 2022 - 2025', 'Completed Advanced National Diploma in Information Technology, focusing on software engineering, database design, and systems architecture.');

-- --------------------------------------------------------

--
-- Table structure for table `certifications`
--

CREATE TABLE `certifications` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certifications`
--

INSERT INTO `certifications` (`id`, `name`) VALUES
(1, 'SLASSCOM Fundamentals'),
(2, 'Mobile Phone Repair Technician'),
(3, 'Security & Surveillance Technician'),
(4, 'IT Fundamentals');

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
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `timeline`
--
ALTER TABLE `timeline`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `timeline`
--
ALTER TABLE `timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
