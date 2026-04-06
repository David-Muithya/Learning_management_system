-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 11:28 AM
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
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `created_at`) VALUES
(1, 6, 'login', 'user', 6, '2026-04-03 04:46:40'),
(2, 6, 'view_course', 'course', 1, '2026-04-03 04:46:40'),
(3, 7, 'login', 'user', 7, '2026-04-03 04:46:40'),
(4, 7, 'submit_assignment', 'submission', 3, '2026-04-03 04:46:40'),
(5, 2, 'grade_assignment', 'submission', 2, '2026-04-03 04:46:40'),
(6, 8, 'login', 'user', 8, '2026-04-03 04:46:40'),
(7, 9, 'login', 'user', 9, '2026-04-03 04:46:40'),
(8, 10, 'login', 'user', 10, '2026-04-03 04:46:40'),
(9, 1, 'verify_payment', 'mock_payments', 1, '2026-04-03 04:46:40'),
(12, NULL, 'logout', 'user', 13, '2026-04-03 05:21:23'),
(13, 12, 'failed_login', 'user', 12, '2026-04-03 12:04:37'),
(14, 12, 'failed_login', 'user', 12, '2026-04-03 12:05:23'),
(15, 13, 'register', 'user', 13, '2026-04-03 12:24:47'),
(16, 13, 'login', 'user', 13, '2026-04-03 12:24:53'),
(17, 12, 'failed_login', 'user', 12, '2026-04-03 12:28:10'),
(18, 12, 'failed_login', 'user', 12, '2026-04-03 12:32:32'),
(19, 13, 'logout', 'user', 13, '2026-04-03 12:47:45'),
(20, 14, 'register', 'user', 14, '2026-04-04 11:19:03'),
(21, 14, 'login', 'user', 14, '2026-04-04 11:19:16'),
(22, 1, 'login', 'user', 1, '2026-04-04 13:13:36'),
(23, 1, 'logout', 'user', 1, '2026-04-04 14:52:17'),
(24, 1, 'login', 'user', 1, '2026-04-04 15:24:12'),
(25, 1, 'logout', 'user', 1, '2026-04-04 15:46:05'),
(26, 17, 'login', 'user', 17, '2026-04-04 15:52:16'),
(27, 17, 'logout', 'user', 17, '2026-04-04 15:57:21'),
(28, 17, 'login', 'user', 17, '2026-04-04 16:03:40'),
(29, 14, 'login', 'user', 14, '2026-04-04 16:04:21'),
(30, 1, 'login', 'user', 1, '2026-04-04 19:00:41'),
(31, 14, 'login', 'user', 14, '2026-04-04 19:02:46'),
(32, 17, 'login', 'user', 17, '2026-04-04 19:03:30'),
(33, 1, 'login', 'user', 1, '2026-04-04 21:11:01'),
(34, 14, 'login', 'user', 14, '2026-04-04 21:33:42'),
(35, 14, 'login', 'user', 14, '2026-04-04 21:34:03'),
(36, 14, 'login', 'user', 14, '2026-04-04 21:34:07'),
(37, 14, 'login', 'user', 14, '2026-04-04 21:34:16'),
(38, 14, 'login', 'user', 14, '2026-04-04 21:46:28'),
(39, 14, 'login', 'user', 14, '2026-04-04 21:48:11'),
(40, 14, 'login', 'user', 14, '2026-04-04 21:48:58'),
(41, 14, 'login', 'user', 14, '2026-04-04 21:51:19'),
(42, 14, 'login', 'user', 14, '2026-04-04 21:54:10'),
(43, 14, 'login', 'user', 14, '2026-04-04 21:54:29'),
(44, 14, 'login', 'user', 14, '2026-04-04 21:55:21'),
(45, 6, 'failed_login', 'user', 6, '2026-04-04 21:57:47'),
(46, 14, 'login', 'user', 14, '2026-04-04 21:57:50'),
(47, 6, 'failed_login', 'user', 6, '2026-04-04 21:58:06'),
(48, 6, 'login', 'user', 6, '2026-04-04 21:58:07'),
(49, 6, 'failed_login', 'user', 6, '2026-04-04 21:58:23'),
(50, 6, 'login', 'user', 6, '2026-04-04 21:58:23'),
(51, 2, 'failed_login', 'user', 2, '2026-04-04 21:58:24'),
(52, 14, 'login', 'user', 14, '2026-04-04 21:59:50'),
(53, 6, 'failed_login', 'user', 6, '2026-04-04 21:59:56'),
(54, 6, 'login', 'user', 6, '2026-04-04 21:59:56'),
(55, 2, 'failed_login', 'user', 2, '2026-04-04 21:59:56'),
(56, 14, 'login', 'user', 14, '2026-04-04 22:01:32'),
(57, 14, 'login', 'user', 14, '2026-04-04 22:01:42'),
(58, 6, 'login', 'user', 6, '2026-04-04 22:05:35'),
(59, 14, 'login', 'user', 14, '2026-04-04 22:06:20'),
(60, 1, 'login', 'user', 1, '2026-04-04 22:09:50'),
(61, 14, 'login', 'user', 14, '2026-04-04 22:10:22'),
(62, 14, 'login', 'user', 14, '2026-04-04 22:17:28'),
(63, 14, 'login', 'user', 14, '2026-04-04 22:17:39'),
(64, 14, 'login', 'user', 14, '2026-04-04 22:19:09'),
(65, 14, 'login', 'user', 14, '2026-04-04 22:24:12'),
(66, 17, 'login', 'user', 17, '2026-04-04 22:24:31'),
(67, 1, 'login', 'user', 1, '2026-04-04 22:26:12'),
(68, 14, 'login', 'user', 14, '2026-04-05 18:00:49'),
(69, 1, 'login', 'user', 1, '2026-04-05 18:02:32'),
(70, 17, 'login', 'user', 17, '2026-04-05 18:02:54'),
(71, 17, 'logout', 'user', 17, '2026-04-05 18:04:10'),
(72, 17, 'login', 'user', 17, '2026-04-05 18:40:59'),
(73, 17, 'logout', 'user', 17, '2026-04-05 18:59:22'),
(74, 17, 'failed_login', 'user', 17, '2026-04-05 18:59:30'),
(75, 17, 'login', 'user', 17, '2026-04-05 18:59:59'),
(76, 17, 'logout', 'user', 17, '2026-04-05 19:00:22'),
(77, 2, 'login', 'user', 2, '2026-04-05 19:01:05'),
(78, 2, 'logout', 'user', 2, '2026-04-05 19:04:24'),
(79, 3, 'login', 'user', 3, '2026-04-05 19:04:52'),
(80, 3, 'logout', 'user', 3, '2026-04-05 19:11:20'),
(81, 16, 'login', 'user', 16, '2026-04-05 19:13:03'),
(82, 16, 'logout', 'user', 16, '2026-04-05 20:28:40'),
(83, 15, 'login', 'user', 15, '2026-04-05 20:29:39'),
(84, 14, 'login', 'user', 14, '2026-04-05 20:29:59'),
(85, 14, 'logout', 'user', 14, '2026-04-05 20:30:41'),
(86, 15, 'login', 'user', 15, '2026-04-05 20:38:04'),
(87, 15, 'logout', 'user', 15, '2026-04-05 20:41:04'),
(88, 16, 'login', 'user', 16, '2026-04-05 20:41:23'),
(89, 16, 'logout', 'user', 16, '2026-04-05 20:50:51'),
(90, 15, 'login', 'user', 15, '2026-04-05 20:51:06'),
(91, 15, 'logout', 'user', 15, '2026-04-05 20:53:31'),
(92, 5, 'login', 'user', 5, '2026-04-05 20:54:08'),
(93, 5, 'logout', 'user', 5, '2026-04-05 20:58:24'),
(94, 4, 'login', 'user', 4, '2026-04-05 20:58:54'),
(95, 4, 'logout', 'user', 4, '2026-04-05 21:28:32'),
(96, 15, 'login', 'user', 15, '2026-04-05 21:28:41'),
(97, 15, 'logout', 'user', 15, '2026-04-05 21:28:48'),
(98, 15, 'login', 'user', 15, '2026-04-05 21:28:55'),
(99, 15, 'logout', 'user', 15, '2026-04-05 21:28:59'),
(100, 2, 'login', 'user', 2, '2026-04-05 21:29:26'),
(101, 2, 'logout', 'user', 2, '2026-04-05 21:42:31'),
(102, 15, 'login', 'user', 15, '2026-04-05 21:43:40'),
(103, 15, 'logout', 'user', 15, '2026-04-05 21:48:59'),
(104, 15, 'login', 'user', 15, '2026-04-05 21:51:21'),
(105, 15, 'logout', 'user', 15, '2026-04-05 21:57:21'),
(106, 4, 'login', 'user', 4, '2026-04-05 21:57:59'),
(107, 14, 'login', 'user', 14, '2026-04-06 07:29:01'),
(108, 14, 'logout', 'user', 14, '2026-04-06 07:34:22'),
(109, 14, 'failed_login', 'user', 14, '2026-04-06 07:34:30'),
(110, 14, 'login', 'user', 14, '2026-04-06 07:34:54'),
(111, 14, 'logout', 'user', 14, '2026-04-06 07:34:58'),
(112, 7, 'login', 'user', 7, '2026-04-06 07:35:57'),
(113, 1, 'login', 'user', 1, '2026-04-06 07:48:51'),
(114, 7, 'logout', 'user', 7, '2026-04-06 08:00:44'),
(115, 5, 'login', 'user', 5, '2026-04-06 08:35:14'),
(116, 5, 'logout', 'user', 5, '2026-04-06 08:36:47'),
(117, 2, 'login', 'user', 2, '2026-04-06 09:24:52'),
(118, 2, 'logout', 'user', 2, '2026-04-06 09:27:05'),
(119, 3, 'login', 'user', 3, '2026-04-06 09:27:32');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) NOT NULL,
  `course_id` bigint(20) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `posted_by` bigint(20) DEFAULT NULL,
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `course_id`, `title`, `content`, `posted_by`, `priority`, `created_at`, `updated_at`) VALUES
(1, 1, 'Welcome to Python Programming', 'Welcome everyone! Please ensure you have Python 3.8+ installed before the next session.', 3, 'high', '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(2, 2, 'MERN Stack Kickoff', 'We\'ll start with JavaScript fundamentals. Complete the pre-reading materials.', 2, 'high', '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(3, 2, 'First Project Deadline Extended', 'The first project deadline has been extended by one week.', 2, 'normal', '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(4, 3, 'Data Science Resources', 'Check the resources section for recommended books and datasets.', 3, 'normal', '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(5, 4, 'Security Tools Installation', 'Install Wireshark and Kali Linux for the upcoming modules.', 4, 'high', '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(6, 5, 'React Native Setup', 'Follow the setup guide to install React Native CLI or Expo.', 5, 'normal', '2026-04-03 04:46:40', '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint(20) NOT NULL,
  `course_id` bigint(20) NOT NULL,
  `instructor_id` bigint(20) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `max_points` decimal(5,2) DEFAULT 100.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `course_id`, `instructor_id`, `title`, `description`, `due_date`, `max_points`, `created_at`, `deleted_at`) VALUES
(1, 1, 3, 'Python Functions and Modules Assignment', 'Create a Python program with custom functions and modules', '2026-05-03 04:46:40', 100.00, '2026-04-03 04:46:40', NULL),
(2, 1, 3, 'Object-Oriented Programming Project', 'Build a class-based system for a library management system', '2026-05-18 04:46:40', 100.00, '2026-04-03 04:46:40', NULL),
(3, 2, 2, 'React Component Development', 'Build reusable React components for an e-commerce site', '2026-05-03 04:46:40', 100.00, '2026-04-03 04:46:40', NULL),
(4, 2, 2, 'Full Stack CRUD Application', 'Create a full stack MERN application with CRUD operations', '2026-06-02 04:46:40', 150.00, '2026-04-03 04:46:40', NULL),
(5, 3, 3, 'Data Analysis with Pandas', 'Analyze a dataset and create visualizations using pandas and matplotlib', '2026-05-08 04:46:40', 100.00, '2026-04-03 04:46:40', NULL),
(6, 3, 3, 'Machine Learning Model Implementation', 'Build and train a classification model on real-world data', '2026-05-23 04:46:40', 150.00, '2026-04-03 04:46:40', NULL),
(7, 4, 4, 'Network Security Assessment', 'Perform a network security assessment and write a report', '2026-05-13 04:46:40', 100.00, '2026-04-03 04:46:40', NULL),
(8, 5, 5, 'React Native UI Components', 'Build a set of reusable UI components for mobile app', '2026-05-03 04:46:40', 100.00, '2026-04-03 04:46:40', NULL),
(9, 7, 17, 'create the steps of software engineering', 'this assignment should be submitted as cat 1', '2026-04-15 12:00:00', 100.00, '2026-04-04 21:36:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` bigint(20) NOT NULL,
  `course_id` bigint(20) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') DEFAULT 'absent',
  `marked_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `course_id`, `student_id`, `date`, `status`, `marked_by`, `created_at`) VALUES
(1, 1, 6, '2026-04-03', 'present', 3, '2026-04-03 04:46:40'),
(2, 2, 6, '2026-04-03', 'present', 2, '2026-04-03 04:46:40'),
(3, 2, 7, '2026-04-03', 'present', 2, '2026-04-03 04:46:40'),
(4, 2, 11, '2026-04-03', 'absent', 2, '2026-04-03 04:46:40'),
(5, 3, 8, '2026-04-03', 'present', 3, '2026-04-03 04:46:40'),
(6, 4, 9, '2026-04-03', 'late', 4, '2026-04-03 04:46:40'),
(7, 5, 10, '2026-04-03', 'present', 5, '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key_name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Alice Muthoni', 'alice.muthoni@gmail.com', 'Course Inquiry', 'I would like to know more about the Python course schedule', '2026-04-03 04:46:40'),
(2, 'John Kamau', 'john.kamau@gmail.com', 'Payment Issue', 'My M-PESA payment was successful but course not activated', '2026-04-03 04:46:40'),
(3, 'Mary Wanjiku', 'mary.wanjiku@gmail.com', 'Technical Support', 'Having trouble accessing course materials', '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `instructor_id` bigint(20) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `thumbnail` varchar(255) DEFAULT NULL,
  `credits` int(11) DEFAULT 3,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('draft','pending_approval','published','archived','rejected') NOT NULL DEFAULT 'draft',
  `max_students` int(11) DEFAULT 50,
  `enrollment_count` int(11) DEFAULT 0,
  `syllabus` text DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `slug`, `code`, `description`, `short_description`, `category_id`, `instructor_id`, `price`, `thumbnail`, `credits`, `start_date`, `end_date`, `status`, `max_students`, `enrollment_count`, `syllabus`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Python Programming Masterclass', 'python-programming-masterclass-69d37c952fb5d', 'PRG101', 'Learn Python from basics to advanced concepts including OOP, file handling, and modules', 'Complete Python programming course for beginners to advanced', 1, 3, 15000.00, 'course_1775467669.jpg', 4, '2026-04-15', '2026-07-15', 'published', 50, 3, 'Week 1-2: Python Basics\r\nWeek 3-4: Functions and Modules\r\nWeek 5-6: OOP in Python\r\nWeek 7-8: File Handling and Exceptions\r\nWeek 9-10: Final Project', 1, '2026-04-03 04:46:40', '2026-04-06 09:27:49', NULL),
(2, 'Full Stack Web Development with MERN', 'full-stack-web-development-with-mern-69d37c08e502b', 'WD201', 'Master MongoDB, Express.js, React, and Node.js to build full-stack web applications', 'Become a full stack developer with MERN stack', 2, 2, 25000.00, 'course_1775467528.png', 5, '2026-05-01', '2026-08-31', 'published', 40, 3, 'Week 1-2: HTML/CSS/JavaScript Review\r\nWeek 3-4: React Fundamentals\r\nWeek 5-6: Node.js and Express\r\nWeek 7-8: MongoDB Database\r\nWeek 9-10: Full Stack Integration Project', 1, '2026-04-03 04:46:40', '2026-04-06 09:25:28', NULL),
(3, 'Data Science and Machine Learning', 'data-science-machine-learning', 'DS301', 'Learn data analysis, visualization, and machine learning using Python libraries', 'Complete data science bootcamp with real-world projects', 3, 3, 30000.00, NULL, 5, '2026-06-01', '2026-09-30', 'published', 35, 1, 'Week 1-2: NumPy and Pandas\nWeek 3-4: Data Visualization with Matplotlib/Seaborn\nWeek 5-6: Statistics for Data Science\nWeek 7-8: Machine Learning Algorithms\nWeek 9-10: Capstone Project', 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40', NULL),
(4, 'Cyber Security Fundamentals', 'cyber-security-fundamentals-69d2d200b9250', 'CS401', 'Learn network security, cryptography, ethical hacking, and security best practices', 'Introduction to cyber security for beginners', 4, 4, 20000.00, 'course_1775424000.png', 4, '2026-07-01', '2026-10-31', 'published', 30, 1, 'Week 1-2: Security Concepts and Principles\r\nWeek 3-4: Network Security\r\nWeek 5-6: Cryptography Basics\r\nWeek 7-8: Ethical Hacking and Penetration Testing\r\nWeek 9-10: Security Audit Project', 1, '2026-04-03 04:46:40', '2026-04-05 21:20:00', NULL),
(5, 'React Native Mobile App Development', 'react-native-mobile-app-development-69d3706aad248', 'MB501', 'Build cross-platform mobile apps for iOS and Android using React Native', 'Master mobile app development with React Native and Expo', 5, 5, 22000.00, 'course_1775464554.png', 4, '2026-08-01', '2026-11-30', 'published', 35, 1, 'Week 1-2: React Native Basics\r\nWeek 3-4: Navigation and Routing\r\nWeek 5-6: State Management (Redux)\r\nWeek 7-8: API Integration\r\nWeek 9-10: App Deployment Project', 1, '2026-04-03 04:46:40', '2026-04-06 08:35:54', NULL),
(6, 'Software Development', 'software-development-69d185b25280a', 'SOF-017-594', 'The Software Development course provides a thorough foundation in the principles, methodologies, and practices of modern software development. It begins with an introduction to the role of software engineering in society and the importance of structured approaches to building reliable systems. Students will study requirements engineering, system modeling, and architectural design, learning how to translate user needs into technical specifications.The course covers programming paradigms, coding standards, and version control, ensuring students develop strong technical skills. Testing strategies, including unit, integration, and system testing, are emphasized to promote quality assurance. Project management techniques such as Agile and Scrum are introduced to prepare learners for collaborative, iterative development environments.Advanced topics include software maintenance, scalability, security considerations, and emerging trends such as cloud-based development and AI-driven tools. Practical assignments and group projects allow students to apply theoretical knowledge to real-world scenarios, fostering problem-solving, teamwork, and communication skills.By the end of the course, graduates will be able to design, implement, and manage software systems that meet industry standards. They will be equipped to pursue careers as software engineers, system analysts, project managers, or continue into advanced research and innovation in computing.', 'Software Development is the systematic application of engineering principles to the design, development, testing, and maintenance of software systems. This course equips students with the knowledge and skills to build scalable, reliable, and efficient software solutions. Learners will explore core topics such as software development life cycles, requirements analysis, system design, coding practices, testing methodologies, and project management. Emphasis is placed on both technical proficiency', 5, 17, 30000.00, 'course_1775338930.png', 3, '2026-04-12', '2026-08-31', 'published', 50, 0, 'Week 1: Introduction to Software Engineering\r\nWeek 2: Requirements Engineering\r\nWeek 3: Software Process Models\r\nWeek 4: System Modeling &amp;amp; Design\r\nWeek 5: Programming Practices\r\nWeek 6: Software Testing Fundamentals\r\nWeek 7: Quality Assurance &amp;amp; Metrics\r\nWeek 8: Project Management in Software Engineering\r\nWeek 9: Software Maintenance &amp;amp; Evolution\r\nWeek 10: Security &amp;amp; Ethics in Software Engineering\r\nWeek 11: Emerging Trends\r\nWeek 12: Capstone Project &amp;amp; Review', 17, '2026-04-04 21:00:47', '2026-04-04 21:43:12', NULL),
(7, 'Software Engineering', 'software-engineering-69d17e4f157d7', 'SOF-017-823', 'The Software Engineering course provides a thorough foundation in the principles, methodologies, and practices of modern software development. It begins with an introduction to the role of software engineering in society and the importance of structured approaches to building reliable systems. Students will study requirements engineering, system modeling, and architectural design, learning how to translate user needs into technical specifications.The course covers programming paradigms, coding standards, and version control, ensuring students develop strong technical skills. Testing strategies, including unit, integration, and system testing, are emphasized to promote quality assurance. Project management techniques such as Agile and Scrum are introduced to prepare learners for collaborative, iterative development environments.Advanced topics include software maintenance, scalability, security considerations, and emerging trends such as cloud-based development and AI-driven tools. Practical assignments and group projects allow students to apply theoretical knowledge to real-world scenarios, fostering problem-solving, teamwork, and communication skills.By the end of the course, graduates will be able to design, implement, and manage software systems that meet industry standards. They will be equipped to pursue careers as software engineers, system analysts, project managers, or continue into advanced research and innovation in computing.', 'Software Engineering is the systematic application of engineering principles to the design, development, testing, and maintenance of software systems. This course equips students with the knowledge and skills to build scalable, reliable, and efficient software solutions. Learners will explore core topics such as software development life cycles, requirements analysis, system design, coding practices, testing methodologies, and project management. Emphasis is placed on both technical proficiency', 1, 17, 25000.00, 'course_1775336624.png', 3, '2026-04-10', '2026-08-25', 'published', 50, 1, 'Week 1: Introduction to Software Engineering\r\nWeek 2: Requirements Engineering\r\nWeek 3: Software Process Models\r\nWeek 4: System Modeling &amp;amp; Design\r\nWeek 5: Programming Practices\r\nWeek 6: Software Testing Fundamentals\r\nWeek 7: Quality Assurance &amp;amp; Metrics\r\nWeek 8: Project Management in Software Engineering\r\nWeek 9: Software Maintenance &amp;amp; Evolution\r\nWeek 10: Security &amp;amp; Ethics in Software Engineering\r\nWeek 11: Emerging Trends\r\nWeek 12: Capstone Project &amp;amp; Review', 17, '2026-04-04 21:03:44', '2026-04-04 22:10:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_categories`
--

CREATE TABLE `course_categories` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_categories`
--

INSERT INTO `course_categories` (`id`, `name`, `slug`, `description`, `image`, `parent_id`, `created_at`) VALUES
(1, 'Programming', 'programming', 'Master programming languages and software development fundamentals', NULL, NULL, '2026-04-03 04:46:40'),
(2, 'Web Development', 'web-development', 'Build modern responsive websites and web applications', NULL, NULL, '2026-04-03 04:46:40'),
(3, 'Data Science', 'data-science', 'Analyze data, build machine learning models, and derive insights', NULL, NULL, '2026-04-03 04:46:40'),
(4, 'Cyber Security', 'cyber-security', 'Protect systems, networks, and data from digital attacks', NULL, NULL, '2026-04-03 04:46:40'),
(5, 'Mobile Development', 'mobile-development', 'Create mobile applications for iOS and Android platforms', NULL, NULL, '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `course_progress`
--

CREATE TABLE `course_progress` (
  `id` bigint(20) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `course_id` bigint(20) NOT NULL,
  `material_id` bigint(20) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_progress`
--

INSERT INTO `course_progress` (`id`, `student_id`, `course_id`, `material_id`, `is_completed`, `completed_at`) VALUES
(1, 6, 1, 1, 1, '2026-04-03 04:46:40'),
(2, 6, 1, 2, 0, '2026-04-03 04:46:40'),
(3, 6, 2, 3, 1, '2026-04-03 04:46:40'),
(4, 7, 2, 3, 1, '2026-04-03 04:46:40'),
(5, 8, 3, 5, 0, '2026-04-03 04:46:40'),
(6, 9, 4, 6, 0, '2026-04-03 04:46:40'),
(7, 10, 5, 7, 0, '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Stand-in structure for view `course_statistics`
-- (See below for the actual view)
--
CREATE TABLE `course_statistics` (
`id` bigint(20)
,`title` varchar(200)
,`code` varchar(20)
,`enrollment_count` int(11)
,`max_students` int(11)
,`status` enum('draft','pending_approval','published','archived','rejected')
);

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

CREATE TABLE `discussions` (
  `id` bigint(20) NOT NULL,
  `course_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussions`
--

INSERT INTO `discussions` (`id`, `course_id`, `user_id`, `title`, `content`, `created_at`) VALUES
(1, 1, 6, 'Help with Python OOP concepts', 'I\'m struggling with inheritance and polymorphism. Can someone explain?', '2026-04-03 04:46:40'),
(2, 2, 7, 'React hooks vs class components', 'Which one should we focus on for modern development?', '2026-04-03 04:46:40'),
(3, 2, 2, 'Resource: Best React tutorials', 'Here are some great resources for learning React effectively.', '2026-04-03 04:46:40'),
(4, 3, 8, 'Question about pandas dataframes', 'How do I handle missing values in a large dataset?', '2026-04-03 04:46:40'),
(5, 4, 9, 'Best practices for password security', 'What are the current best practices for storing passwords?', '2026-04-03 04:46:40'),
(6, 5, 10, 'React Native navigation issue', 'Having trouble with stack navigation setup. Any advice?', '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `discussion_replies`
--

CREATE TABLE `discussion_replies` (
  `id` bigint(20) NOT NULL,
  `discussion_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussion_replies`
--

INSERT INTO `discussion_replies` (`id`, `discussion_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 3, 'Inheritance allows a class to inherit attributes from a parent class. Polymorphism allows methods to have different implementations.', '2026-04-03 04:46:40'),
(2, 2, 2, 'Focus on functional components with hooks - that\'s the modern standard.', '2026-04-03 04:46:40'),
(3, 4, 3, 'You can use dropna() to remove or fillna() to fill missing values with mean/median.', '2026-04-03 04:46:40'),
(4, 5, 4, 'Use bcrypt or Argon2 for password hashing, never store plain text passwords.', '2026-04-03 04:46:40'),
(5, 6, 5, 'Check that you have installed @react-navigation/native and its dependencies correctly.', '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `course_id` bigint(20) NOT NULL,
  `enrolled_by` bigint(20) DEFAULT NULL,
  `enrollment_date` date NOT NULL,
  `status` enum('pending_payment','pending_verification','active','completed','dropped','rejected') NOT NULL DEFAULT 'pending_payment',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `final_grade` varchar(2) DEFAULT NULL,
  `mock_payment_id` bigint(20) DEFAULT NULL,
  `mock_payment_status` enum('pending','completed','verified') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `enrolled_by`, `enrollment_date`, `status`, `enrolled_at`, `completed_at`, `completion_date`, `final_grade`, `mock_payment_id`, `mock_payment_status`) VALUES
(1, 6, 1, 6, '2026-04-03', 'active', '2026-04-03 04:46:40', NULL, NULL, NULL, 1, 'verified'),
(2, 6, 2, 6, '2026-04-03', 'active', '2026-04-03 04:46:40', NULL, NULL, NULL, 2, 'verified'),
(3, 7, 2, 7, '2026-04-03', 'active', '2026-04-03 04:46:40', NULL, NULL, NULL, 3, 'verified'),
(4, 8, 3, 8, '2026-04-03', 'active', '2026-04-03 04:46:40', NULL, NULL, NULL, 4, 'verified'),
(5, 9, 4, 9, '2026-04-03', 'active', '2026-04-03 04:46:40', NULL, NULL, NULL, 5, 'verified'),
(6, 10, 5, 10, '2026-04-03', 'active', '2026-04-03 04:46:40', NULL, NULL, NULL, 6, 'verified'),
(7, 11, 2, 11, '2026-04-03', 'active', '2026-04-03 04:46:40', NULL, NULL, NULL, 7, 'verified'),
(8, 12, 1, 12, '2026-04-03', 'pending_payment', '2026-04-03 04:46:40', NULL, NULL, NULL, 8, 'pending'),
(9, 7, 1, 7, '2026-04-03', 'active', '2026-04-03 04:46:40', NULL, NULL, NULL, 9, 'verified'),
(10, 13, 1, 13, '2026-04-03', 'active', '2026-04-03 12:25:27', NULL, NULL, NULL, 11, 'verified'),
(11, 14, 7, 14, '2026-04-05', 'active', '2026-04-04 22:06:43', NULL, NULL, NULL, 12, 'verified');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) NOT NULL,
  `enrollment_id` bigint(20) NOT NULL,
  `assignment_id` bigint(20) NOT NULL,
  `grade_value` double DEFAULT NULL,
  `letter_grade` varchar(2) DEFAULT NULL,
  `graded_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `enrollment_id`, `assignment_id`, `grade_value`, `letter_grade`, `graded_by`, `created_at`) VALUES
(1, 1, 1, 88, 'B+', 3, '2026-04-03 04:46:40'),
(2, 2, 3, 92, 'A-', 2, '2026-04-03 04:46:40'),
(3, 4, 5, 85, 'B', 3, '2026-04-03 04:46:40'),
(4, 11, 9, 78, 'B', 17, '2026-04-04 22:34:24'),
(5, 3, 3, 78, 'B', 2, '2026-04-05 21:40:35');

-- --------------------------------------------------------

--
-- Table structure for table `instructor_applications`
--

CREATE TABLE `instructor_applications` (
  `id` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `highest_qualification` varchar(100) DEFAULT NULL,
  `institution` varchar(200) DEFAULT NULL,
  `graduation_year` int(4) DEFAULT NULL,
  `years_experience` int(2) DEFAULT NULL,
  `current_role` varchar(100) DEFAULT NULL,
  `organization` varchar(200) DEFAULT NULL,
  `expertise_areas` text DEFAULT NULL,
  `teaching_philosophy` text DEFAULT NULL,
  `sample_course_idea` text DEFAULT NULL,
  `portfolio_link` varchar(500) DEFAULT NULL,
  `why_teach` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instructor_applications`
--

INSERT INTO `instructor_applications` (`id`, `email`, `first_name`, `last_name`, `phone`, `highest_qualification`, `institution`, `graduation_year`, `years_experience`, `current_role`, `organization`, `expertise_areas`, `teaching_philosophy`, `sample_course_idea`, `portfolio_link`, `why_teach`, `status`, `reviewed_by`, `review_notes`, `reviewed_at`, `created_at`) VALUES
(1, 'samuel.kariuki@example.com', 'Samuel', 'Kariuki', '+712345678', 'Master\'s in Computer Science', 'University of Nairobi', 2019, 6, 'Lead Developer', 'Safaricom PLC', 'Web Development, Cloud Computing', 'Project-based learning with real-world applications', 'Building Scalable Web Applications with AWS', 'https://github.com/samuelkariuki', 'Passionate about mentoring the next generation of developers', 'approved', 1, '', '2026-04-04 13:23:26', '2026-04-03 04:46:40'),
(2, 'esther.mwangi@example.com', 'Esther', 'Mwangi', '+723456789', 'PhD in Data Science', 'Strathmore University', 2020, 4, 'Data Scientist', 'IBM Kenya', 'Machine Learning, AI, Data Analytics', 'Hands-on approach with real datasets', 'Practical Machine Learning for Business', 'https://github.com/esthermwangi', 'Want to bridge the gap between academia and industry', 'approved', 1, '', '2026-04-04 13:23:49', '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` bigint(20) NOT NULL,
  `course_id` bigint(20) NOT NULL,
  `module_id` bigint(20) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('document','video','audio','image','link','other') DEFAULT 'document',
  `file_path` varchar(500) DEFAULT NULL,
  `content_url` varchar(500) DEFAULT NULL,
  `order_index` int(11) DEFAULT 0,
  `uploaded_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `course_id`, `module_id`, `title`, `description`, `type`, `file_path`, `content_url`, `order_index`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Python Installation Guide', 'Step-by-step guide to install Python', 'document', NULL, 'https://docs.python.org/3/installing/index.html', 1, 3, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(2, 1, 2, 'Functions in Python Video', 'Video tutorial on Python functions', 'video', NULL, 'https://www.youtube.com/watch?v=example1', 1, 3, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(3, 2, 3, 'HTML/CSS Crash Course', 'Complete guide to HTML5 and CSS3', 'document', NULL, 'https://developer.mozilla.org/en-US/docs/Web/HTML', 1, 2, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(4, 2, 4, 'React Hooks Documentation', 'Official React hooks documentation', 'link', NULL, 'https://react.dev/reference/react/hooks', 1, 2, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(5, 3, 5, 'Pandas Tutorial', 'Complete pandas tutorial for beginners', 'document', NULL, 'https://pandas.pydata.org/docs/getting_started/', 1, 3, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(6, 4, 7, 'Network Security Basics', 'Introduction to network security concepts', 'document', NULL, NULL, 1, 4, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(7, 5, 8, 'React Native Setup Guide', 'How to set up React Native development environment', 'document', NULL, 'https://reactnative.dev/docs/environment-setup', 1, 5, '2026-04-03 04:46:40', '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `mock_payments`
--

CREATE TABLE `mock_payments` (
  `id` bigint(20) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `course_id` bigint(20) NOT NULL,
  `enrollment_id` bigint(20) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `transaction_id` varchar(100) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','verified','rejected') NOT NULL DEFAULT 'pending',
  `verified_by` bigint(20) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mock_payments`
--

INSERT INTO `mock_payments` (`id`, `student_id`, `course_id`, `enrollment_id`, `amount`, `transaction_id`, `payment_date`, `status`, `verified_by`, `verified_at`, `notes`, `created_at`) VALUES
(1, 6, 1, 1, 15000.00, 'TXN-MPESA-001', '2026-04-03 04:46:40', 'verified', 1, '2026-04-03 04:46:40', 'Payment via M-PESA verified', '2026-04-03 04:46:40'),
(2, 6, 2, 2, 25000.00, 'TXN-MPESA-002', '2026-04-03 04:46:40', 'verified', 1, '2026-04-03 04:46:40', 'Payment via M-PESA verified', '2026-04-03 04:46:40'),
(3, 7, 2, 3, 25000.00, 'TXN-MPESA-003', '2026-04-03 04:46:40', 'verified', 1, '2026-04-03 04:46:40', 'Payment via M-PESA verified', '2026-04-03 04:46:40'),
(4, 8, 3, 4, 30000.00, 'TXN-MPESA-004', '2026-04-03 04:46:40', 'verified', 1, '2026-04-03 04:46:40', 'Payment via M-PESA verified', '2026-04-03 04:46:40'),
(5, 9, 4, 5, 20000.00, 'TXN-MPESA-005', '2026-04-03 04:46:40', 'verified', 1, '2026-04-03 04:46:40', 'Payment via M-PESA verified', '2026-04-03 04:46:40'),
(6, 10, 5, 6, 22000.00, 'TXN-MPESA-006', '2026-04-03 04:46:40', 'verified', 1, '2026-04-03 04:46:40', 'Payment via M-PESA verified', '2026-04-03 04:46:40'),
(7, 11, 2, 7, 25000.00, 'TXN-MPESA-007', '2026-04-03 04:46:40', 'verified', 1, '2026-04-04 13:26:00', 'was  succesfully made , the payment and can now start the course', '2026-04-03 04:46:40'),
(8, 12, 1, 8, 15000.00, 'TXN-MPESA-008', '2026-04-03 04:46:40', 'pending', NULL, NULL, 'Payment pending verification', '2026-04-03 04:46:40'),
(9, 7, 1, 9, 15000.00, 'TXN-MPESA-009', '2026-04-03 04:46:40', 'verified', 1, '2026-04-03 04:46:40', 'Payment via M-PESA verified', '2026-04-03 04:46:40'),
(11, 13, 1, 10, 15000.00, 'MOCK-69CFB1B73DF87-1945', '2026-04-03 12:25:27', 'verified', 1, '2026-04-04 13:26:05', 'was  succesfully made , the payment and can now start the course', '2026-04-03 12:25:27'),
(12, 14, 7, 11, 25000.00, 'MOCK-69D18B73E59EC-9845', '2026-04-04 22:06:43', 'verified', 1, '2026-04-04 22:10:09', 'this is the payment to cover the first module', '2026-04-04 22:06:43');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) NOT NULL,
  `course_id` bigint(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `order_index` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `course_id`, `title`, `description`, `order_index`, `created_at`, `updated_at`) VALUES
(1, 1, 'Python Basics', 'Variables, data types, and control structures', 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(2, 1, 'Functions and Modules', 'Creating and using functions and modules', 2, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(3, 2, 'HTML/CSS/JavaScript Review', 'Frontend fundamentals', 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(4, 2, 'React Fundamentals', 'Components, props, state, and hooks', 2, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(5, 3, 'NumPy and Pandas', 'Data manipulation libraries', 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(6, 3, 'Data Visualization', 'Creating charts and graphs', 2, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(7, 4, 'Network Security', 'Network protocols and security measures', 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(8, 5, 'React Native Basics', 'Core components and styling', 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40'),
(9, 7, 'Module 1', 'The first of the trimester of the three modules', 0, '2026-04-04 21:21:42', '2026-04-04 21:21:42'),
(10, 6, 'Module 1', 'first module for this course', 0, '2026-04-04 21:42:55', '2026-04-04 21:42:55');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 6, 'Assignment Graded', 'Your Python assignment has been graded. Score: 88%', 0, '2026-04-03 04:46:40'),
(2, 6, 'New Announcement', 'New announcement in Python Programming course', 0, '2026-04-03 04:46:40'),
(3, 7, 'Assignment Submitted', 'Your React assignment has been submitted successfully', 0, '2026-04-03 04:46:40'),
(4, 8, 'Course Started', 'Data Science course has started. Check the materials.', 0, '2026-04-03 04:46:40'),
(5, 9, 'Payment Verified', 'Your course payment has been verified successfully', 1, '2026-04-03 04:46:40'),
(6, 1, 'New Enrollment Payment', 'David Muithya has completed payment for course \'Python Programming Masterclass\'. Awaiting verification.', 0, '2026-04-03 12:25:27'),
(7, 11, 'Enrollment Approved!', 'Your enrollment for \'Full Stack Web Development with MERN\' has been verified. You can now access the course materials.', 0, '2026-04-04 13:26:00'),
(8, 13, 'Enrollment Approved!', 'Your enrollment for \'Python Programming Masterclass\' has been verified. You can now access the course materials.', 0, '2026-04-04 13:26:05'),
(9, 17, 'Course Approved', 'Your course \'Software Engineering\' has been approved and is now published.', 0, '2026-04-04 21:23:49'),
(10, 17, 'Course Approved', 'Your course \'Software Engineering\' has been approved and is now published.', 0, '2026-04-04 21:42:22'),
(11, 17, 'Course Approved', 'Your course \'Software Development\' has been approved and is now published.', 0, '2026-04-04 21:43:12'),
(12, 1, 'New Enrollment Payment', 'mutua james has completed payment for course \'Software Engineering\'. Awaiting verification.', 0, '2026-04-04 22:06:43'),
(13, 14, 'Enrollment Approved!', 'Your enrollment for \'Software Engineering\' has been verified. You can now access the course materials.', 0, '2026-04-04 22:10:09'),
(14, 17, 'New Assignment Submission', 'mutua james has submitted the assignment \'create the steps of software engineering\'.', 0, '2026-04-04 22:12:51'),
(15, 14, 'Assignment Graded', 'Your assignment \'create the steps of software engineering\' has been graded. Score: 78/100', 0, '2026-04-04 22:34:24'),
(16, 7, 'Assignment Graded', 'Your assignment \'React Component Development\' has been graded. Score: 78/100', 0, '2026-04-05 21:40:35'),
(17, 3, 'New Assignment Submission', 'John Kamau has submitted the assignment \'Python Functions and Modules Assignment\'.', 0, '2026-04-06 07:50:38');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `user_id`, `token`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 6, 'token_abc123_def456', '2026-04-04 04:46:40', NULL, '2026-04-03 04:46:40'),
(2, 7, 'token_ghi789_jkl012', '2026-04-04 04:46:40', NULL, '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` bigint(20) NOT NULL,
  `course_id` bigint(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `passing_score` int(11) DEFAULT 60,
  `attempts_allowed` int(11) DEFAULT 1,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `course_id`, `title`, `description`, `duration_minutes`, `passing_score`, `attempts_allowed`, `start_date`, `end_date`, `created_by`, `created_at`) VALUES
(1, 1, 'Python Basics Quiz', 'Test your knowledge on Python fundamentals', 30, 60, 2, '2026-04-03 04:46:40', '2026-05-03 04:46:40', 3, '2026-04-03 04:46:40'),
(2, 2, 'React Fundamentals Quiz', 'Assessment on React components and hooks', 45, 70, 2, '2026-04-03 04:46:40', '2026-05-03 04:46:40', 2, '2026-04-03 04:46:40'),
(3, 3, 'Pandas Data Analysis Quiz', 'Test your pandas and data manipulation skills', 40, 65, 2, '2026-04-03 04:46:40', '2026-05-03 04:46:40', 3, '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` bigint(20) NOT NULL,
  `quiz_id` bigint(20) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `status` enum('in_progress','submitted','graded') DEFAULT 'in_progress'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `quiz_id`, `student_id`, `started_at`, `completed_at`, `score`, `status`) VALUES
(1, 1, 6, '2026-04-01 04:46:40', '2026-04-01 04:46:40', 80, 'graded'),
(2, 2, 6, '2026-04-02 04:46:40', '2026-04-02 04:46:40', 90, 'graded'),
(3, 2, 7, '2026-04-03 04:46:40', NULL, NULL, 'in_progress');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_options`
--

CREATE TABLE `quiz_options` (
  `id` bigint(20) NOT NULL,
  `question_id` bigint(20) NOT NULL,
  `option_text` varchar(500) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_options`
--

INSERT INTO `quiz_options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, 'function myFunction()', 0),
(2, 1, 'def myFunction():', 1),
(3, 1, 'create myFunction():', 0),
(4, 1, 'new myFunction():', 0),
(5, 2, 'tuple', 0),
(6, 2, 'string', 0),
(7, 2, 'list', 1),
(8, 2, 'integer', 0),
(9, 3, 'useEffect', 1),
(10, 3, 'useState', 0),
(11, 3, 'useContext', 0),
(12, 3, 'useReducer', 0),
(13, 4, 'props', 1),
(14, 4, 'state', 0),
(15, 4, 'context', 0),
(16, 4, 'redux', 0),
(17, 5, 'read.csv()', 0),
(18, 5, 'load_csv()', 0),
(19, 5, 'pd.read_csv()', 1),
(20, 5, 'csv.import()', 0);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` bigint(20) NOT NULL,
  `quiz_id` bigint(20) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','short_answer','essay') DEFAULT 'multiple_choice',
  `points` int(11) DEFAULT 1,
  `order_index` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `question_type`, `points`, `order_index`) VALUES
(1, 1, 'What is the correct way to create a function in Python?', 'multiple_choice', 10, 1),
(2, 1, 'Which of the following is a mutable data type in Python?', 'multiple_choice', 10, 2),
(3, 2, 'What hook is used for side effects in React?', 'multiple_choice', 10, 1),
(4, 2, 'What is the correct way to pass data from parent to child component?', 'multiple_choice', 10, 2),
(5, 3, 'Which pandas function is used to read a CSV file?', 'multiple_choice', 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `session_token`, `ip_address`, `user_agent`, `last_activity`, `expires_at`, `created_at`) VALUES
(1, 6, 'sess_abc123_xyz456', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-04-03 04:46:40', '2026-04-10 04:46:40', '2026-04-03 04:46:40'),
(2, 2, 'sess_def456_uvw789', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2026-04-03 04:46:40', '2026-04-10 04:46:40', '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` bigint(20) NOT NULL,
  `assignment_id` bigint(20) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `submission_text` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_late` tinyint(1) DEFAULT 0,
  `status` enum('submitted','graded','late','resubmitted') DEFAULT 'submitted',
  `grade` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` bigint(20) DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `assignment_id`, `student_id`, `submission_text`, `submitted_at`, `is_late`, `status`, `grade`, `feedback`, `graded_by`, `graded_at`) VALUES
(1, 1, 6, 'Submitted Python functions and modules assignment with 5 custom functions', '2026-03-29 04:46:40', 0, 'graded', 88.00, 'Great work! Functions are well-structured. Consider adding docstrings.', 3, '2026-04-01 04:46:40'),
(2, 3, 6, 'Built product card component with props and state management', '2026-03-31 04:46:40', 0, 'graded', 92.00, 'Excellent component design! Very clean code.', 2, '2026-04-02 04:46:40'),
(3, 3, 7, 'E-commerce product listing component with filtering', '2026-04-01 04:46:40', 0, 'graded', 78.00, 'good work, the research was worthy working on', 2, '2026-04-06 00:40:35'),
(4, 5, 8, 'Data analysis on Kenyan housing dataset with visualizations', '2026-03-27 04:46:40', 0, 'graded', 85.00, 'Good analysis. Could include more insights.', 3, '2026-03-31 04:46:40'),
(5, 7, 9, 'Network vulnerability assessment report', '2026-03-30 04:46:40', 0, 'submitted', NULL, NULL, NULL, NULL),
(6, 8, 10, 'React Native button and card components with styling', '2026-04-01 04:46:40', 0, 'submitted', NULL, NULL, NULL, NULL),
(7, 9, 14, '| Step | Name | Description |\r\n| --- | --- | --- |\r\n| **1** | **Requirement Analysis** | This is the foundation stage where stakeholders, users, and developers identify what the software should do. Requirements are gathered through interviews, surveys, and documentation. The goal is to define clear functional and non‑functional requirements. |\r\n| **2** | **Feasibility Study** | The team evaluates whether the project is technically, economically, and operationally feasible. It helps determine if the software can be built within budget, time, and resource constraints. |\r\n| **3** | **System Design** | Based on the requirements, architects and designers create the system’s blueprint — including data structures, architecture, user interfaces, and algorithms. Design documents guide developers during implementation. |\r\n| **4** | **Implementation (Coding)** | Developers translate the design into actual code using suitable programming languages and frameworks. This is the most active phase, where the software takes shape. |\r\n| **5** | **Testing** | The developed software is rigorously tested to identify and fix bugs. Testing ensures the product meets all requirements and performs reliably under different conditions. |\r\n| **6** | **Deployment** | Once testing is complete, the software is released to users or moved into production. Deployment can be done in stages (pilot, beta, full release) depending on project strategy. |\r\n| **7** | **Maintenance** | After deployment, the software is monitored and updated to fix issues, improve performance, or add new features. Maintenance ensures long‑term reliability and user satisfaction. |', '2026-04-04 22:12:51', 0, 'graded', 78.00, 'good work james', 17, '2026-04-05 01:34:24'),
(8, 1, 7, '\"\"\"\r\nmain.py — Library Management System\r\n====================================\r\nEntry point that demonstrates all modules and custom functions.\r\n\r\nProject layout:\r\n    library_system/\r\n    ├── main.py              ← you are here\r\n    └── modules/\r\n        ├── __init__.py      ← package exports\r\n        ├── book.py          ← Book class + factory function\r\n        ├── member.py        ← Member class\r\n        ├── library.py       ← Library orchestrator class\r\n        └── utils.py         ← helper / utility functions\r\n\"\"\"\r\n\r\nfrom modules import Book, Member, Library, generate_report, validate_isbn, validate_email\r\nfrom modules.utils import print_header\r\n\r\n\r\n# ── 1. Seed the collection ────────────────────────────────────────────────────\r\n\r\ndef build_sample_library() -> Library:\r\n    \"\"\"\r\n    Custom function: creates a Library and populates it with\r\n    sample books and members.\r\n    \"\"\"\r\n    lib = Library(\"City Central Library\")\r\n\r\n    books = [\r\n        Book(\"The Pragmatic Programmer\", \"David Thomas\",      \"9780135957059\", \"Technology\", 2019),\r\n        Book(\"Clean Code\",               \"Robert C. Martin\",  \"9780132350884\", \"Technology\", 2008),\r\n        Book(\"Dune\",                     \"Frank Herbert\",     \"9780441013593\", \"Sci-Fi\",     1965),\r\n        Book(\"1984\",                     \"George Orwell\",     \"9780451524935\", \"Dystopia\",   1949),\r\n        Book(\"Sapiens\",                  \"Yuval Noah Harari\", \"9780062316097\", \"History\",    2011),\r\n    ]\r\n\r\n    members = [\r\n        Member(\"Alice Kamau\",  \"M001\", \"alice@example.com\"),\r\n        Member(\"Brian Omondi\", \"M002\", \"brian@example.com\"),\r\n        Member(\"Cynthia Njeri\", \"M003\", \"cynthia@example.com\"),\r\n    ]\r\n\r\n    for book in books:\r\n        lib.add_book(book)\r\n\r\n    for member in members:\r\n        lib.register_member(member)\r\n\r\n    return lib\r\n\r\n\r\n# ── 2. Validation demo ────────────────────────────────────────────────────────\r\n\r\ndef demo_validation():\r\n    print_header(\"Validation Utilities\")\r\n\r\n    test_isbns = [\"9780135957059\", \"123-456\", \"0-306-40615-2\"]\r\n    for isbn in test_isbns:\r\n        ok = validate_isbn(isbn)\r\n        print(f\"  ISBN \'{isbn}\' → {\'✅ valid\' if ok else \'❌ invalid\'}\")\r\n\r\n    test_emails = [\"alice@example.com\", \"not-an-email\", \"b@x.io\"]\r\n    for email in test_emails:\r\n        ok = validate_email(email)\r\n        print(f\"  Email \'{email}\' → {\'✅ valid\' if ok else \'❌ invalid\'}\")\r\n\r\n\r\n# ── 3. Circulation demo ───────────────────────────────────────────────────────\r\n\r\ndef demo_circulation(lib: Library):\r\n    print_header(\"Checkout & Return\")\r\n\r\n    # Checkout two books to Alice\r\n    lib.checkout(\"9780135957059\", \"M001\")\r\n    lib.checkout(\"9780132350884\", \"M001\")\r\n\r\n    # Brian borrows Dune\r\n    lib.checkout(\"9780441013593\", \"M002\")\r\n\r\n    # Try to borrow a book already checked out\r\n    lib.checkout(\"9780441013593\", \"M003\")\r\n\r\n    # Alice returns one\r\n    lib.return_book(\"9780135957059\", \"M001\")\r\n\r\n    # Brian returns Dune\r\n    lib.return_book(\"9780441013593\", \"M002\")\r\n\r\n\r\n# ── 4. Search demo ────────────────────────────────────────────────────────────\r\n\r\ndef demo_search(lib: Library):\r\n    print_header(\"Search\")\r\n    lib.search(\"tech\")      # matches genre\r\n    lib.search(\"Orwell\")    # matches author\r\n    lib.search(\"xyz\")       # no results\r\n\r\n\r\n# ── 5. Inventory snapshot ─────────────────────────────────────────────────────\r\n\r\ndef demo_inventory(lib: Library):\r\n    print_header(\"Full Inventory\")\r\n    for book in lib.books:\r\n        print(book)\r\n        print()\r\n\r\n\r\n# ── 6. Member profiles ────────────────────────────────────────────────────────\r\n\r\ndef demo_members(lib: Library):\r\n    print_header(\"Member Profiles\")\r\n    for member in lib.members:\r\n        print(member)\r\n        print()\r\n\r\n\r\n# ── 7. Report ─────────────────────────────────────────────────────────────────\r\n\r\ndef demo_report(lib: Library):\r\n    print_header(\"System Report\")\r\n    print(generate_report(lib.books, lib.members))\r\n\r\n\r\n# ── Main ──────────────────────────────────────────────────────────────────────\r\n\r\ndef main():\r\n    print(\"\\n🏛️  Welcome to the Library Management System\\n\")\r\n\r\n    lib = build_sample_library()\r\n\r\n    demo_validation()\r\n    demo_circulation(lib)\r\n    demo_search(lib)\r\n    demo_inventory(lib)\r\n    demo_members(lib)\r\n    demo_report(lib)\r\n\r\n    print(\"\\n✅ All demos complete.\\n\")\r\n\r\n\r\nif __name__ == \"__main__\":\r\n    main()', '2026-04-06 07:50:38', 0, 'submitted', NULL, NULL, NULL, NULL);

--
-- Triggers `submissions`
--
DELIMITER $$
CREATE TRIGGER `check_late_submission` BEFORE INSERT ON `submissions` FOR EACH ROW BEGIN
    DECLARE due DATETIME;

    SELECT due_date INTO due FROM assignments WHERE id = NEW.assignment_id;

    IF NOW() > due THEN
        SET NEW.is_late = TRUE;
        SET NEW.status = 'late';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES
(1, 'site_name', 'SkillMaster Kenya LMS', 'text', 'Name of the LMS platform', '2026-04-03 04:46:40'),
(2, 'site_logo', '/assets/images/logo.png', 'text', 'Path to site logo', '2026-04-03 04:46:40'),
(3, 'timezone', 'Africa/Nairobi', 'text', 'System timezone (Kenyan time)', '2026-04-03 04:46:40'),
(4, 'max_file_size', '20', 'number', 'Maximum file upload size in MB', '2026-04-03 04:46:40'),
(5, 'allowed_file_types', 'pdf,doc,docx,jpg,png,mp4', 'text', 'Allowed file types for uploads', '2026-04-03 04:46:40'),
(6, 'maintenance_mode', 'false', 'boolean', 'Put system in maintenance mode', '2026-04-03 04:46:40'),
(7, 'mpesa_paybill', '123456', 'text', 'M-PESA Paybill number for payments', '2026-04-03 04:46:40'),
(8, 'currency', 'KES', 'text', 'System currency', '2026-04-03 04:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','instructor','student') NOT NULL DEFAULT 'student',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `must_change_password` tinyint(1) DEFAULT 0,
  `created_by` bigint(20) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `linkedin_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `first_name`, `last_name`, `profile_pic`, `bio`, `phone_number`, `address`, `is_active`, `must_change_password`, `created_by`, `last_login`, `created_at`, `updated_at`, `deleted_at`, `facebook_link`, `twitter_link`, `linkedin_link`) VALUES
(1, 'admin_kenya', 'admin@skillmaster.co.ke', '$2y$12$REvg7zx18Pafi8Mu0ZnzB.h0g8fbcsGFXT2NZlG072z3aW29kmqui', 'admin', 'James', 'Otieno', 'admin_1.png', 'System Administrator at SkillMaster Kenya', '+254711223344', 'Nairobi, Kenya', 1, 0, NULL, '2026-04-06 07:48:51', '2026-04-03 04:46:40', '2026-04-06 08:47:55', NULL, NULL, NULL, NULL),
(2, 'mercy_wambui', 'mercy.wambui@skillmaster.co.ke', '$2y$12$QfgYHex82L.VTxMdOR.pb.HfE0t/CVmrBiRfueYD0l/Jxt6TXOcLq', 'instructor', 'Mercy', 'Wambui', 'instructor_2.png', 'Senior Web Developer with 8+ years experience in MERN stack', '+254722334455', 'Westlands, Nairobi', 1, 0, 1, '2026-04-06 09:24:52', '2026-04-03 04:46:40', '2026-04-06 09:24:52', NULL, NULL, NULL, NULL),
(3, 'peter_ndungu', 'peter.ndungu@skillmaster.co.ke', '$2y$12$QfgYHex82L.VTxMdOR.pb.HfE0t/CVmrBiRfueYD0l/Jxt6TXOcLq', 'instructor', 'Peter', 'Ndungu', 'instructor_3.png', 'Data Scientist specializing in Python, Machine Learning, and AI', '+712345678', 'Kilimani, Nairobi', 1, 0, 1, '2026-04-06 09:27:32', '2026-04-03 04:46:40', '2026-04-06 09:27:32', NULL, '', '', ''),
(4, 'faith_chepkoech', 'faith.chepkoech@skillmaster.co.ke', '$2y$12$QfgYHex82L.VTxMdOR.pb.HfE0t/CVmrBiRfueYD0l/Jxt6TXOcLq', 'instructor', 'Faith', 'Chepkoech', 'instructor_4.png', 'Cyber Security Expert with certification in CEH and CISSP', '+723456789', 'Eldoret, Kenya', 1, 0, 1, '2026-04-05 21:57:59', '2026-04-03 04:46:40', '2026-04-05 22:02:11', NULL, NULL, NULL, NULL),
(5, 'brian_odhiambo', 'brian.odhiambo@skillmaster.co.ke', '$2y$12$QfgYHex82L.VTxMdOR.pb.HfE0t/CVmrBiRfueYD0l/Jxt6TXOcLq', 'instructor', 'Brian', 'Odhiambo', 'instructor_5.png', 'Mobile App Developer specializing in React Native and Flutter', '+734567890', 'Kisumu, Kenya', 1, 0, 1, '2026-04-06 08:35:14', '2026-04-03 04:46:40', '2026-04-06 08:35:14', NULL, NULL, NULL, NULL),
(6, 'alice_muthoni', 'alice.muthoni@gmail.com', '$2y$12$yVnUn9k4Zh.IsTiG6ibpVegmDq77Z2D8xzVjLYLg0MiyG//SB7dPW', 'student', 'Alice', 'Muthoni', NULL, 'Computer Science student at University of Nairobi', '+745678901', 'Nairobi, Kenya', 1, 0, 1, '2026-04-04 22:05:35', '2026-04-03 04:46:40', '2026-04-06 07:34:16', NULL, NULL, NULL, NULL),
(7, 'john_kamau', 'john.kamau@gmail.com', '$2y$12$yVnUn9k4Zh.IsTiG6ibpVegmDq77Z2D8xzVjLYLg0MiyG//SB7dPW', 'student', 'John', 'Kamau', NULL, 'IT student passionate about web development', '+756789012', 'Nakuru, Kenya', 1, 0, 1, '2026-04-06 07:35:57', '2026-04-03 04:46:40', '2026-04-06 07:35:57', NULL, NULL, NULL, NULL),
(8, 'mary_wanjiku', 'mary.wanjiku@gmail.com', '$2y$12$yVnUn9k4Zh.IsTiG6ibpVegmDq77Z2D8xzVjLYLg0MiyG//SB7dPW', 'student', 'Mary', 'Wanjiku', NULL, 'Data science enthusiast', '+767890123', 'Thika, Kenya', 1, 0, 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40', '2026-04-06 07:34:16', NULL, NULL, NULL, NULL),
(9, 'david_okoth', 'david.okoth@gmail.com', '$2y$12$yVnUn9k4Zh.IsTiG6ibpVegmDq77Z2D8xzVjLYLg0MiyG//SB7dPW', 'student', 'David', 'Okoth', NULL, 'Cyber security student', '+778901234', 'Kisumu, Kenya', 1, 0, 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40', '2026-04-06 07:34:16', NULL, NULL, NULL, NULL),
(10, 'sarah_chebet', 'sarah.chebet@gmail.com', '$2y$12$yVnUn9k4Zh.IsTiG6ibpVegmDq77Z2D8xzVjLYLg0MiyG//SB7dPW', 'student', 'Sarah', 'Chebet', NULL, 'Mobile development learner', '+789012345', 'Eldoret, Kenya', 1, 0, 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40', '2026-04-06 07:34:16', NULL, NULL, NULL, NULL),
(11, 'james_mwangi', 'james.mwangi@gmail.com', '$2y$12$yVnUn9k4Zh.IsTiG6ibpVegmDq77Z2D8xzVjLYLg0MiyG//SB7dPW', 'student', 'James', 'Mwangi', NULL, 'Full stack developer in training', '+790123456', 'Mombasa, Kenya', 1, 0, 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40', '2026-04-06 07:34:16', NULL, NULL, NULL, NULL),
(12, 'grace_atieno', 'grace.atieno@gmail.com', '$2y$12$yVnUn9k4Zh.IsTiG6ibpVegmDq77Z2D8xzVjLYLg0MiyG//SB7dPW', 'student', 'Grace', 'Atieno', NULL, 'Programming student', '+701234567', 'Nairobi, Kenya', 1, 0, 1, '2026-04-03 04:46:40', '2026-04-03 04:46:40', '2026-04-06 07:34:16', NULL, NULL, NULL, NULL),
(13, 'david.muithya', 'muithya321@gmail.com', '$2y$12$yVnUn9k4Zh.IsTiG6ibpVegmDq77Z2D8xzVjLYLg0MiyG//SB7dPW', 'student', 'David', 'Muithya', NULL, NULL, '+254712759544', NULL, 1, 0, NULL, '2026-04-03 12:24:53', '2026-04-03 12:24:47', '2026-04-06 07:34:16', NULL, NULL, NULL, NULL),
(14, 'mutua.james', 'james.mutua@gmail.com', '$2y$12$yVnUn9k4Zh.IsTiG6ibpVegmDq77Z2D8xzVjLYLg0MiyG//SB7dPW', 'student', 'mutua', 'james', 'student_14.jpeg', 'Software engineer student', '0785492212', '123, chuka', 1, 0, NULL, '2026-04-06 07:34:54', '2026-04-04 11:19:03', '2026-04-06 07:34:54', NULL, '', '', ''),
(15, 'samuel.kariuki', 'samuel.kariuki@skillmaster.co.ke', '$2y$12$QfgYHex82L.VTxMdOR.pb.HfE0t/CVmrBiRfueYD0l/Jxt6TXOcLq', 'instructor', 'Samuel', 'Kariuki', 'instructor_15.png', 'Web Development, Cloud Computing', '+712345678', '', 1, 0, 1, '2026-04-05 21:51:21', '2026-04-04 13:23:27', '2026-04-06 07:56:37', NULL, '', '', ''),
(16, 'esther.mwangi', 'esther.mwangi@skillmaster.co.ke', '$2y$12$QfgYHex82L.VTxMdOR.pb.HfE0t/CVmrBiRfueYD0l/Jxt6TXOcLq', 'instructor', 'Esther', 'Mwangi', 'instructor_16.png', 'Machine Learning, AI, Data Analytics', '+723456789', 'Nairobi, 472', 1, 0, 1, '2026-04-05 20:41:23', '2026-04-04 13:23:49', '2026-04-06 07:56:37', NULL, '', '', ''),
(17, 'sarah.akinyi', 'sarah.akinyi@skillmaster.co.ke', '$2y$12$QfgYHex82L.VTxMdOR.pb.HfE0t/CVmrBiRfueYD0l/Jxt6TXOcLq', 'instructor', 'Sarah', 'Akinyi', 'instructor_17.png', 'Senior Data Scientist &amp; ML Educator', '+254723456789', 'Nairobi, Kenya', 1, 0, 1, '2026-04-05 18:59:59', '2026-04-04 15:44:40', '2026-04-05 18:59:59', NULL, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users_instructor_backup`
--

CREATE TABLE `users_instructor_backup` (
  `id` bigint(20) NOT NULL DEFAULT 0,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','instructor','student') NOT NULL DEFAULT 'student',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `must_change_password` tinyint(1) DEFAULT 0,
  `created_by` bigint(20) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `linkedin_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_instructor_backup`
--

INSERT INTO `users_instructor_backup` (`id`, `username`, `email`, `password`, `role`, `first_name`, `last_name`, `profile_pic`, `bio`, `phone_number`, `address`, `is_active`, `must_change_password`, `created_by`, `last_login`, `created_at`, `updated_at`, `deleted_at`, `facebook_link`, `twitter_link`, `linkedin_link`) VALUES
(15, 'samuel.kariuki', 'samuel.kariuki@example.com', '$2y$12$QfgYHex82L.VTxMdOR.pb.HfE0t/CVmrBiRfueYD0l/Jxt6TXOcLq', 'instructor', 'Samuel', 'Kariuki', 'instructor_15.png', 'Web Development, Cloud Computing', '+712345678', '', 1, 0, 1, '2026-04-05 21:51:21', '2026-04-04 13:23:27', '2026-04-05 21:56:51', NULL, '', '', ''),
(16, 'esther.mwangi', 'esther.mwangi@example.com', '$2y$12$QfgYHex82L.VTxMdOR.pb.HfE0t/CVmrBiRfueYD0l/Jxt6TXOcLq', 'instructor', 'Esther', 'Mwangi', 'instructor_16.png', 'Machine Learning, AI, Data Analytics', '+723456789', 'Nairobi, 472', 1, 0, 1, '2026-04-05 20:41:23', '2026-04-04 13:23:49', '2026-04-05 20:46:36', NULL, '', '', '');

-- --------------------------------------------------------

--
-- Structure for view `course_statistics`
--
DROP TABLE IF EXISTS `course_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `course_statistics`  AS SELECT `c`.`id` AS `id`, `c`.`title` AS `title`, `c`.`code` AS `code`, `c`.`enrollment_count` AS `enrollment_count`, `c`.`max_students` AS `max_students`, `c`.`status` AS `status` FROM `courses` AS `c` GROUP BY `c`.`id`, `c`.`title`, `c`.`code`, `c`.`enrollment_count`, `c`.`max_students`, `c`.`status` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor_id` (`instructor_id`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_course_due` (`course_id`,`due_date`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`course_id`,`student_id`,`date`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `marked_by` (`marked_by`),
  ADD KEY `idx_date` (`date`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key_name`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_start_date` (`start_date`),
  ADD KEY `idx_instructor_status` (`instructor_id`,`status`),
  ADD KEY `idx_status_created` (`status`,`created_at`);

--
-- Indexes for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_parent_id` (`parent_id`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `course_progress`
--
ALTER TABLE `course_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_progress` (`student_id`,`course_id`,`material_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `idx_is_completed` (`is_completed`);

--
-- Indexes for table `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_id` (`discussion_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_course_id` (`course_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_enrollment_date` (`enrollment_date`),
  ADD KEY `idx_student_status` (`student_id`,`status`),
  ADD KEY `idx_course_status` (`course_id`,`status`),
  ADD KEY `fk_enrollment_mock_payment` (`mock_payment_id`),
  ADD KEY `idx_student_status_updated` (`student_id`,`status`),
  ADD KEY `idx_course_status_updated` (`course_id`,`status`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `graded_by` (`graded_by`),
  ADD KEY `idx_enrollment_assignment` (`enrollment_id`,`assignment_id`);

--
-- Indexes for table `instructor_applications`
--
ALTER TABLE `instructor_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_reviewed_by` (`reviewed_by`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `mock_payments`
--
ALTER TABLE `mock_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_transaction_id` (`transaction_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_course_id` (`course_id`),
  ADD KEY `idx_enrollment_id` (`enrollment_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_date` (`payment_date`),
  ADD KEY `idx_verified_by` (`verified_by`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_order` (`course_id`,`order_index`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attempt` (`quiz_id`,`student_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `idx_session_token` (`session_token`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_submission_student` (`student_id`),
  ADD KEY `fk_submission_grader` (`graded_by`),
  ADD KEY `idx_submitted_at` (`submitted_at`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_assignment_student` (`assignment_id`,`student_id`),
  ADD KEY `idx_assignment_student_status` (`assignment_id`,`student_id`,`status`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_role_status` (`role`,`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_categories`
--
ALTER TABLE `course_categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_progress`
--
ALTER TABLE `course_progress`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `instructor_applications`
--
ALTER TABLE `instructor_applications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `mock_payments`
--
ALTER TABLE `mock_payments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quiz_options`
--
ALTER TABLE `quiz_options`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `course_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courses_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD CONSTRAINT `course_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `course_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `course_categories_ibfk_parent` FOREIGN KEY (`parent_id`) REFERENCES `course_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_progress`
--
ALTER TABLE `course_progress`
  ADD CONSTRAINT `course_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_progress_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_progress_ibfk_3` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `discussions_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  ADD CONSTRAINT `discussion_replies_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `fk_enrollment_mock_payment` FOREIGN KEY (`mock_payment_id`) REFERENCES `mock_payments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_3` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `instructor_applications`
--
ALTER TABLE `instructor_applications`
  ADD CONSTRAINT `fk_application_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `materials_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `materials_ibfk_3` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mock_payments`
--
ALTER TABLE `mock_payments`
  ADD CONSTRAINT `fk_mock_payment_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mock_payment_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_mock_payment_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mock_payment_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD CONSTRAINT `quiz_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_submission_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_submission_grader` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_submission_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
