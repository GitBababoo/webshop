-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2026 at 04:52 PM
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
-- Database: `shopee_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `module`, `target_type`, `target_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 11, 'login', 'auth', NULL, NULL, 'เข้าสู่ระบบ | roles: superadmin,admin,content_mod,finance,support', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-07 17:28:19'),
(2, 11, 'login', 'auth', NULL, NULL, 'เข้าสู่ระบบ | roles: superadmin,admin,content_mod,finance,support', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-07 18:58:15');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(10) UNSIGNED DEFAULT NULL,
  `detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detail`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `perm_id` int(10) UNSIGNED NOT NULL,
  `granted` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `ann_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('info','warning','success','danger') NOT NULL DEFAULT 'info',
  `target` enum('all','buyer','seller','admin') NOT NULL DEFAULT 'all',
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_dismissible` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`ann_id`, `title`, `content`, `type`, `target`, `start_at`, `end_at`, `is_active`, `is_dismissible`, `created_by`, `created_at`) VALUES
(1, 'โปรโมชั่น 11.11 ลดสูงสุด 90% ฟรีค่าส่งทุกออเดอร์!', 'ช้อปสินค้าลดราคาทุกหมวดหมู่ในวัน 11.11 เริ่มเที่ยงคืนตรง รับส่วนลดสูงสุด 90% พร้อมฟรีค่าส่งทุกออเดอร์ไม่มีขั้นต่ำ อย่าพลาด!', 'warning', 'all', '2024-04-14 00:00:00', '2024-04-15 08:00:00', 0, 1, 11, '2026-04-07 17:17:47'),
(2, 'สมาชิกใหม่รับส่วนลด 100 บาท พร้อมคูปองฟรีค่าส่ง', 'สมาชิกใหม่ที่สมัครวันนี้รับทันที! คูปองส่วนลด 100 บาท และคูปองฟรีค่าส่ง พร้อมใช้งานได้เลย สมัครฟรีไม่มีค่าใช้จ่าย', 'success', 'buyer', '2024-06-01 00:00:00', '2024-06-30 23:59:59', 1, 1, 11, '2026-04-07 17:17:47'),
(3, 'แคมเปญสินค้าไอทีลดราคา โทรศัพท์ แท็บเล็ต ลดพิเศษ', 'แคมเปญลดราคาสินค้าไอทีครั้งใหญ่! โทรศัพท์มือถือ แท็บเล็ต และคอมพิวเตอร์ลดสูงสุด 50% มีสินค้าให้เลือกมากกว่า 1,000 รายการ', 'info', 'seller', '2024-06-20 00:00:00', '2024-07-31 23:59:59', 1, 1, 11, '2026-04-07 17:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `banner_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `position` enum('homepage_main','homepage_sub','category','flash_sale','popup') NOT NULL DEFAULT 'homepage_main',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`banner_id`, `title`, `image_url`, `link_url`, `position`, `sort_order`, `start_at`, `end_at`, `is_active`, `created_at`) VALUES
(1, 'Flash Sale ลดสูงสุด 90%', '/webshop/uploads/banners/banner_1.jpg', '/webshop/search.php?flash=1', 'homepage_main', 1, NULL, NULL, 1, '2026-04-07 16:36:13'),
(2, 'ส่งฟรีทุกออเดอร์', '/webshop/uploads/banners/banner_2.jpg', '/flash-sale', 'homepage_sub', 1, NULL, NULL, 1, '2026-04-07 16:36:13'),
(3, 'สมาชิกใหม่ลด 100 บาท', '/webshop/uploads/banners/banner_3.jpg', '/category/electronics', 'homepage_sub', 2, NULL, NULL, 1, '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 9, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(2, 10, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(3, 15, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(4, 16, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(5, 17, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(6, 18, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(7, 19, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(8, 20, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(9, 21, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(10, 22, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(11, 23, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(12, 24, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(13, 25, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(14, 26, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(15, 27, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(16, 28, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(17, 29, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(18, 30, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(19, 31, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(20, 32, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(21, 33, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(22, 34, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(34, 61, '2026-04-07 20:01:06', '2026-04-07 20:01:06'),
(35, 63, '2026-04-07 20:47:07', '2026-04-07 20:47:07'),
(36, 64, '2026-04-07 21:11:58', '2026-04-07 21:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(10) UNSIGNED NOT NULL,
  `cart_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `sku_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `is_checked` tinyint(1) NOT NULL DEFAULT 1,
  `added_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `product_id`, `sku_id`, `quantity`, `is_checked`, `added_at`, `updated_at`) VALUES
(1, 1, 2, 4, 1, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(2, 1, 7, 13, 1, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(3, 2, 5, 11, 1, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(4, 2, 4, 8, 2, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(5, 3, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(6, 4, 1, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(7, 5, 1, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(8, 6, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(9, 7, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(10, 8, 1, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(11, 9, 1, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(12, 10, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(13, 11, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(14, 12, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(15, 13, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(16, 14, 1, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(17, 15, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(18, 16, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(19, 17, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(20, 18, 1, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(21, 19, 1, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(22, 20, 1, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(23, 21, 1, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(24, 22, 1, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(25, 3, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(26, 4, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(27, 5, 2, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(28, 6, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(29, 7, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(30, 8, 2, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(31, 9, 2, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(32, 10, 2, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(33, 11, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(34, 12, 2, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(35, 13, 2, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(36, 14, 2, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(37, 15, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(38, 16, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(39, 17, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(40, 18, 2, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(41, 19, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(42, 20, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(43, 21, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(44, 22, 2, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(45, 3, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(46, 4, 3, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(47, 5, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(48, 6, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(49, 7, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(50, 8, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(51, 9, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(52, 10, 3, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(53, 11, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(54, 12, 3, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(55, 13, 3, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(56, 14, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(57, 15, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(58, 16, 3, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(59, 17, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(60, 18, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(61, 19, 3, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(62, 20, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(63, 21, 3, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(64, 22, 3, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(65, 3, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(66, 4, 4, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(67, 5, 4, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(68, 6, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(69, 7, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(70, 8, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(71, 9, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(72, 10, 4, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(73, 11, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(74, 12, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(75, 13, 4, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(76, 14, 4, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(77, 15, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(78, 16, 4, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(79, 17, 4, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(80, 18, 4, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(81, 19, 4, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(82, 20, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(83, 21, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(84, 22, 4, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(85, 3, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(86, 4, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(87, 5, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(88, 6, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(89, 7, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(90, 8, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(91, 9, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(92, 10, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(93, 11, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(94, 12, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(95, 13, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(96, 14, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(97, 15, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(98, 16, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(99, 17, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(100, 18, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(101, 19, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(102, 20, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(103, 21, 5, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(104, 22, 5, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(105, 3, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(106, 4, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(107, 5, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(108, 6, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(109, 7, 6, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(110, 8, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(111, 9, 6, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(112, 10, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(113, 11, 6, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(114, 12, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(115, 13, 6, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(116, 14, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(117, 15, 6, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(118, 16, 6, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(119, 17, 6, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(120, 18, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(121, 19, 6, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(122, 20, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(123, 21, 6, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(124, 22, 6, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(125, 3, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(126, 4, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(127, 5, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(128, 6, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(129, 7, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(130, 8, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(131, 9, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(132, 10, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(133, 11, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(134, 12, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(135, 13, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(136, 14, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(137, 15, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(138, 16, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(139, 17, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(140, 18, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(141, 19, 7, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(142, 20, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(143, 21, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(144, 22, 7, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(145, 3, 8, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(146, 4, 8, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(147, 5, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(148, 6, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(149, 7, 8, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(150, 8, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(151, 9, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(152, 10, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(153, 11, 8, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(154, 12, 8, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(155, 13, 8, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(156, 14, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(157, 15, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(158, 16, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(159, 17, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(160, 18, 8, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(161, 19, 8, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(162, 20, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(163, 21, 8, NULL, 2, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(164, 22, 8, NULL, 1, 1, '2026-04-07 17:21:46', '2026-04-07 17:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon_url` varchar(500) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `parent_id`, `name`, `slug`, `icon_url`, `image_url`, `sort_order`, `is_active`, `created_at`) VALUES
(1, NULL, 'อิเล็กทรอนิกส์', 'electronics', NULL, '/webshop/uploads/categories/category_1.jpg', 1, 1, '2026-04-07 16:36:13'),
(2, NULL, 'แฟชั่น', 'fashion', NULL, '/webshop/uploads/categories/category_2.jpg', 2, 1, '2026-04-07 16:36:13'),
(3, NULL, 'บ้านและเฟอร์นิเจอร์', 'home-living', NULL, '/webshop/uploads/categories/category_3.jpg', 3, 1, '2026-04-07 16:36:13'),
(4, NULL, 'กีฬาและกิจกรรมกลางแจ้ง', 'sports-outdoors', NULL, '/webshop/uploads/categories/category_4.jpg', 4, 1, '2026-04-07 16:36:13'),
(5, NULL, 'สุขภาพและความงาม', 'health-beauty', NULL, '/webshop/uploads/categories/category_5.jpg', 5, 1, '2026-04-07 16:36:13'),
(6, 1, 'โทรศัพท์และอุปกรณ์เสริม', 'mobile-accessories', NULL, '/webshop/uploads/categories/category_6.jpg', 1, 1, '2026-04-07 16:36:13'),
(7, 1, 'แล็ปท็อปและคอมพิวเตอร์', 'laptops-computers', NULL, '/webshop/uploads/categories/category_7.jpg', 2, 1, '2026-04-07 16:36:13'),
(8, 1, 'เครื่องเสียงและหูฟัง', 'audio-headphones', NULL, '/webshop/uploads/categories/category_8.jpg', 3, 1, '2026-04-07 16:36:13'),
(9, 2, 'เสื้อผ้าผู้ชาย', 'mens-clothing', NULL, '/webshop/uploads/categories/category_9.jpg', 1, 1, '2026-04-07 16:36:13'),
(10, 2, 'เสื้อผ้าผู้หญิง', 'womens-clothing', NULL, '/webshop/uploads/categories/category_10.jpg', 2, 1, '2026-04-07 16:36:13'),
(11, 2, 'รองเท้า', 'shoes', NULL, '/webshop/uploads/categories/category_11.jpg', 3, 1, '2026-04-07 16:36:13'),
(12, 3, 'เฟอร์นิเจอร์', 'furniture', NULL, '/webshop/uploads/categories/category_12.jpg', 1, 1, '2026-04-07 16:36:13'),
(13, 3, 'เครื่องครัว', 'kitchen', NULL, '/webshop/uploads/categories/category_13.jpg', 2, 1, '2026-04-07 16:36:13'),
(14, 3, 'ผ้าปูที่นอนและเครื่องนอน', 'bedding', NULL, '/webshop/uploads/categories/category_14.jpg', 3, 1, '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `cms_menus`
--

CREATE TABLE `cms_menus` (
  `menu_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cms_menus`
--

INSERT INTO `cms_menus` (`menu_id`, `name`, `location`, `created_at`) VALUES
(1, 'เมนูหลัก', 'header_main', '2026-04-07 17:05:34'),
(2, 'Footer ซ้าย', 'footer_left', '2026-04-07 17:05:34'),
(3, 'Footer ขวา', 'footer_right', '2026-04-07 17:05:34');

-- --------------------------------------------------------

--
-- Table structure for table `cms_menu_items`
--

CREATE TABLE `cms_menu_items` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `label` varchar(150) NOT NULL,
  `url` varchar(500) NOT NULL,
  `target` enum('_self','_blank') NOT NULL DEFAULT '_self',
  `icon` varchar(100) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

CREATE TABLE `cms_pages` (
  `page_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `template` enum('default','fullwidth','sidebar','landing') NOT NULL DEFAULT 'default',
  `status` enum('published','draft','private') NOT NULL DEFAULT 'draft',
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cms_pages`
--

INSERT INTO `cms_pages` (`page_id`, `title`, `slug`, `content`, `meta_title`, `meta_desc`, `meta_keywords`, `og_image`, `template`, `status`, `is_system`, `sort_order`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'เกี่ยวกับเรา', 'about-us', 'Shopee-style Shop คือแพลตฟอร์มอีคอมเมิร์ซที่ใหญ่ที่สุดในไทย เรามีสินค้าหลากหลายราคาถูก พร้อมบริการส่งฟรีและโปรโมชั่นพิเศษมากมาย', NULL, NULL, NULL, NULL, 'default', 'published', 1, 0, NULL, NULL, '2026-04-07 17:05:34', '2026-04-07 19:18:44'),
(2, 'นโยบายความเป็นส่วนตัว', 'privacy-policy', 'เราให้ความสำคัญกับข้อมูลส่วนตัวของท่าน ข้อมูลทั้งหมดจะถูกเก็บเป็นความลับและไม่เปิดเผยแก่บุคคลภายนอก', NULL, NULL, NULL, NULL, 'default', 'published', 1, 0, NULL, NULL, '2026-04-07 17:05:34', '2026-04-07 19:18:44'),
(3, 'การจัดส่ง', 'terms-of-service', 'เราจัดส่งทั่วประเทศไทย ใช้เวลา 1-3 วันทำการสำหรับกรุงเทพ และ 3-7 วันสำหรับต่างจังหวัด', NULL, NULL, NULL, NULL, 'default', 'published', 1, 0, NULL, NULL, '2026-04-07 17:05:34', '2026-04-07 19:18:44'),
(4, 'วิธีการสั่งซื้อ', 'return-policy', '1. เลือกสินค้า 2. เพิ่มลงตะกร้า 3. กรอกที่อยู่ 4. เลือกวิธีชำระเงิน 5. รอรับสินค้า', NULL, NULL, NULL, NULL, 'default', 'published', 1, 0, NULL, NULL, '2026-04-07 17:05:34', '2026-04-07 19:18:44'),
(5, 'ข้อตกลงและเงื่อนไข', 'contact-us', 'การใช้บริการเว็บไซต์นี้ ถือว่าท่านยอมรับข้อตกลงและเงื่อนไขการใช้งานทั้งหมด กรุณาอ่านอย่างละเอียดก่อนใช้งาน', NULL, NULL, NULL, NULL, 'default', 'published', 1, 0, NULL, NULL, '2026-04-07 17:05:34', '2026-04-07 19:18:44');

-- --------------------------------------------------------

--
-- Table structure for table `cms_widgets`
--

CREATE TABLE `cms_widgets` (
  `widget_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `widget_type` enum('html','image_banner','product_grid','category_list','text','video','countdown','announcement') NOT NULL DEFAULT 'html',
  `position` varchar(100) NOT NULL,
  `content` longtext DEFAULT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `buyer_user_id` int(10) UNSIGNED NOT NULL,
  `shop_id` int(10) UNSIGNED NOT NULL,
  `last_message_at` datetime DEFAULT NULL,
  `buyer_unread` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `seller_unread` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`conversation_id`, `buyer_user_id`, `shop_id`, `last_message_at`, `buyer_unread`, `seller_unread`, `created_at`) VALUES
(1, 8, 1, '2024-04-09 08:30:00', 0, 1, '2026-04-07 16:36:13'),
(2, 9, 3, '2024-05-01 09:00:00', 0, 0, '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `template_id` int(10) UNSIGNED NOT NULL,
  `template_key` varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` longtext NOT NULL,
  `body_text` text DEFAULT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`template_id`, `template_key`, `name`, `subject`, `body_html`, `body_text`, `variables`, `is_active`, `updated_at`) VALUES
(1, 'order_confirm', 'ยืนยันการสมัครสมาชิก', 'ยืนยันการสมัครสมาชิก', 'ขอบคุณที่สมัครสมาชิกกับเรา กรุณายืนยันอีเมลเพื่อเปิดใช้งานบัญชีของท่าน', NULL, '[\"order_number\",\"total_amount\",\"customer_name\",\"items\"]', 1, '2026-04-07 20:30:06'),
(2, 'order_shipped', 'ยืนยันออเดอร์', 'ออเดอร์ของคุณได้รับการยืนยัน', 'ออเดอร์ #{order_number} ได้รับการยืนยันแล้ว เรากำลังจัดเตรียมสินค้าเพื่อจัดส่งให้คุณ', NULL, '[\"order_number\",\"tracking_number\",\"provider_name\"]', 1, '2026-04-07 20:30:06'),
(3, 'welcome', 'จัดส่งสำเร็จ', 'สินค้าของคุณจัดส่งแล้ว', 'ออเดอร์ของคุณจัดส่งแล้ว หมายเลขติดตาม: {tracking_number} คลิกเพื่อติดตามสถานะ', NULL, '[\"username\",\"email\"]', 1, '2026-04-07 20:30:06');

-- --------------------------------------------------------

--
-- Table structure for table `flash_sales`
--

CREATE TABLE `flash_sales` (
  `flash_sale_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `banner_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `flash_sales`
--

INSERT INTO `flash_sales` (`flash_sale_id`, `title`, `start_at`, `end_at`, `banner_url`, `is_active`, `created_at`) VALUES
(1, 'Flash Sale 11.11', '2024-05-10 12:00:00', '2024-05-10 14:00:00', 'https://picsum.photos/seed/20000/800/200', 0, '2026-04-07 16:36:13'),
(2, 'เที่ยงวันนี้ลดราคา', '2024-05-10 22:00:00', '2024-05-11 00:00:00', 'https://picsum.photos/seed/40000/800/200', 0, '2026-04-07 16:36:13'),
(3, 'Super Brand Day', '2024-05-11 10:00:00', '2024-05-11 16:00:00', 'https://picsum.photos/seed/60000/800/200', 1, '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `flash_sale_items`
--

CREATE TABLE `flash_sale_items` (
  `flash_item_id` int(10) UNSIGNED NOT NULL,
  `flash_sale_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `sku_id` int(10) UNSIGNED DEFAULT NULL,
  `flash_price` decimal(12,2) NOT NULL,
  `original_price` decimal(12,2) NOT NULL,
  `qty_available` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `qty_sold` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `per_user_limit` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `flash_sale_items`
--

INSERT INTO `flash_sale_items` (`flash_item_id`, `flash_sale_id`, `product_id`, `sku_id`, `flash_price`, `original_price`, `qty_available`, `qty_sold`, `per_user_limit`) VALUES
(1, 1, 1, 1, 39900.00, 45900.00, 10, 10, 1),
(2, 1, 3, 5, 8990.00, 12990.00, 20, 18, 1),
(3, 3, 4, 8, 390.00, 590.00, 50, 5, 2),
(4, 3, 6, 12, 499.00, 699.00, 30, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fraud_reports`
--

CREATE TABLE `fraud_reports` (
  `report_id` int(10) UNSIGNED NOT NULL,
  `reporter_id` int(10) UNSIGNED NOT NULL,
  `target_type` enum('user','shop','product','order','review') NOT NULL,
  `target_id` int(10) UNSIGNED NOT NULL,
  `fraud_type` enum('counterfeit','scam','harassment','spam','fake_review','other') NOT NULL,
  `description` text NOT NULL,
  `evidence_url` varchar(500) DEFAULT NULL,
  `status` enum('pending','investigating','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `resolution` text DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fraud_reports`
--

INSERT INTO `fraud_reports` (`report_id`, `reporter_id`, `target_type`, `target_id`, `fraud_type`, `description`, `evidence_url`, `status`, `reviewed_by`, `resolution`, `reviewed_at`, `created_at`) VALUES
(1, 15, 'product', 5, 'counterfeit', 'สินค้าเป็นของปลอม ไม่ใช่ของแท้', NULL, 'investigating', NULL, NULL, NULL, '2026-04-07 17:21:46'),
(2, 17, 'shop', 3, 'spam', 'ร้านค้าส่ง spam message หลอกลวง', NULL, 'resolved', NULL, NULL, NULL, '2026-04-07 17:21:46'),
(3, 19, 'user', 18, 'fake_review', 'รีวิวปลอม ดูเหมือนสร้างขึ้น', NULL, 'pending', NULL, NULL, NULL, '2026-04-07 17:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `ip_blacklist`
--

CREATE TABLE `ip_blacklist` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `blocked_by` int(10) UNSIGNED DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_points`
--

CREATE TABLE `loyalty_points` (
  `point_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `total_points` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `used_points` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expired_points` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tier` enum('bronze','silver','gold','platinum','diamond') NOT NULL DEFAULT 'bronze',
  `tier_updated_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loyalty_points`
--

INSERT INTO `loyalty_points` (`point_id`, `user_id`, `total_points`, `used_points`, `expired_points`, `tier`, `tier_updated_at`, `updated_at`) VALUES
(1, 6, 1250, 200, 0, 'silver', NULL, '2026-04-07 17:17:47'),
(2, 7, 450, 50, 0, 'bronze', NULL, '2026-04-07 17:17:47'),
(3, 8, 3200, 500, 0, 'gold', NULL, '2026-04-07 17:17:47'),
(4, 9, 180, 0, 0, 'bronze', NULL, '2026-04-07 17:17:47'),
(5, 10, 720, 100, 0, 'bronze', NULL, '2026-04-07 17:17:47'),
(6, 15, 1204, 165, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(7, 16, 1155, 108, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(8, 17, 1305, 197, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(9, 18, 653, 70, 0, 'gold', NULL, '2026-04-07 17:21:46'),
(10, 19, 441, 85, 0, 'silver', NULL, '2026-04-07 17:21:46'),
(11, 20, 1059, 169, 0, 'silver', NULL, '2026-04-07 17:21:46'),
(12, 21, 232, 54, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(13, 22, 1398, 14, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(14, 23, 998, 71, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(15, 24, 1492, 106, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(16, 25, 1823, 189, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(17, 26, 1026, 49, 0, 'gold', NULL, '2026-04-07 17:21:46'),
(18, 27, 301, 61, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(19, 28, 1794, 177, 0, 'gold', NULL, '2026-04-07 17:21:46'),
(20, 29, 852, 114, 0, 'silver', NULL, '2026-04-07 17:21:46'),
(21, 30, 1103, 138, 0, 'gold', NULL, '2026-04-07 17:21:46'),
(22, 31, 711, 1, 0, 'bronze', NULL, '2026-04-07 17:21:46'),
(23, 32, 474, 184, 0, 'gold', NULL, '2026-04-07 17:21:46'),
(24, 33, 183, 87, 0, 'gold', NULL, '2026-04-07 17:21:46'),
(25, 34, 1267, 18, 0, 'silver', NULL, '2026-04-07 17:21:46'),
(37, 61, 0, 0, 0, 'bronze', NULL, '2026-04-07 20:01:06'),
(38, 63, 0, 0, 0, 'bronze', NULL, '2026-04-07 20:47:07'),
(39, 64, 0, 0, 0, 'bronze', NULL, '2026-04-07 21:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_transactions`
--

CREATE TABLE `loyalty_transactions` (
  `txn_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `points` int(11) NOT NULL,
  `type` enum('earn','spend','expire','adjust','bonus') NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(10) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loyalty_transactions`
--

INSERT INTO `loyalty_transactions` (`txn_id`, `user_id`, `points`, `type`, `reference_type`, `reference_id`, `description`, `expires_at`, `created_at`) VALUES
(1, 6, 500, 'earn', 'order', 1, 'ซื้อ Samsung Galaxy S24 Ultra', NULL, '2026-04-07 17:17:47'),
(2, 6, 750, 'earn', 'order', 3, 'ซื้อ Sony WH-1000XM5', NULL, '2026-04-07 17:17:47'),
(3, 6, -200, 'spend', NULL, NULL, 'แลกแต้มส่วนลด 200 บาท', NULL, '2026-04-07 17:17:47'),
(4, 8, 1500, 'earn', 'order', 3, ' 3 ', NULL, '2026-04-07 17:17:47'),
(5, 8, 1000, 'bonus', NULL, NULL, ' VIP ', NULL, '2026-04-07 17:17:47'),
(6, 8, 700, 'earn', 'order', 4, '', NULL, '2026-04-07 17:17:47'),
(7, 8, -500, 'spend', NULL, NULL, ' Gift Voucher', NULL, '2026-04-07 17:17:47'),
(8, 7, 490, 'earn', 'order', 2, ' Hoodie Oversized', NULL, '2026-04-07 17:17:47'),
(9, 7, -40, 'expire', NULL, NULL, 'Points ', NULL, '2026-04-07 17:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(10) UNSIGNED NOT NULL,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `message_type` enum('text','image','product','order','sticker') NOT NULL DEFAULT 'text',
  `content` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `order_id` int(10) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `conversation_id`, `sender_id`, `message_type`, `content`, `image_url`, `product_id`, `order_id`, `is_read`, `created_at`) VALUES
(1, 1, 8, 'text', 'สวัสดีครับ สอบถาม Sony XM5 มีสีอะไรบ้างครับ?', NULL, NULL, NULL, 1, '2026-04-07 16:36:13'),
(2, 1, 3, 'text', 'สวัสดีครับ ขณะนี้มีสีดำและสีขาวตามลำดับ', NULL, NULL, NULL, 1, '2026-04-07 16:36:13'),
(3, 1, 8, 'text', 'ขอบคุณครับ ขอสั่งซื้อสีดำพร้อมส่งเลยครับ', NULL, NULL, NULL, 1, '2026-04-07 16:36:13'),
(4, 1, 3, 'text', 'Ó╣äÓ©öÓ╣ëÓ╣ÇÓ©ÑÓ©óÓ©äÓ©úÓ©▒Ó©Ü! Ó©üÓ©öÓ©¬Ó©▒Ó╣êÓ©çÓ╣äÓ©öÓ╣ëÓ╣ÇÓ©ÑÓ©óÓ©ÖÓ©░Ó©äÓ©úÓ©▒Ó©Ü Ó©¬Ó╣êÓ©çÓ╣ÇÓ©úÓ╣çÓ©ºÓ╣üÓ©ÖÓ╣êÓ©ÖÓ©¡Ó©Ö', NULL, NULL, NULL, 0, '2026-04-07 16:36:13'),
(5, 2, 9, 'text', 'Ó©üÓ©úÓ©░Ó©ùÓ©░Ó©éÓ©ÖÓ©▓Ó©öÓ©ÖÓ©ÁÓ╣ëÓ╣ÇÓ©½Ó©íÓ©▓Ó©░Ó©üÓ©▒Ó©ÜÓ╣ÇÓ©òÓ©▓Ó╣üÓ©üÓ╣èÓ©¬Ó©ùÓ©▒Ó╣êÓ©ºÓ╣äÓ©øÓ╣äÓ©½Ó©íÓ©äÓ©úÓ©▒Ó©Ü?', NULL, NULL, NULL, 1, '2026-04-07 16:36:13'),
(6, 2, 5, 'text', 'Ó╣ÇÓ©½Ó©íÓ©▓Ó©░Ó©íÓ©▓Ó©üÓ╣ÇÓ©ÑÓ©óÓ©äÓ©úÓ©▒Ó©Ü Ó╣âÓ©èÓ╣ëÓ╣äÓ©öÓ╣ëÓ©üÓ©▒Ó©ÜÓ╣ÇÓ©òÓ©▓Ó©ùÓ©©Ó©üÓ©øÓ©úÓ©░Ó╣ÇÓ©áÓ©ùÓ©äÓ©úÓ©▒Ó©Ü', NULL, NULL, NULL, 1, '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(10) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `type`, `title`, `body`, `reference_type`, `reference_id`, `is_read`, `created_at`) VALUES
(1, 6, 'order_update', 'สินค้าของคุณได้รับการจัดส่งแล้ว!', 'ออเดอร์ ORD-20240401-000001 กำลังจัดส่ง', 'order', 1, 1, '2026-04-07 16:36:13'),
(2, 6, 'promotion', 'Flash Sale เริ่มแล้ว!', 'อย่าพลาด! สินค้าลดสูงสุด 70% เฉพาะวันนี้เท่านั้น', NULL, NULL, 0, '2026-04-07 16:36:13'),
(3, 7, 'order_update', 'คำสั่งซื้อของคุณสำเร็จแล้ว', 'ออเดอร์ ORD-20240402-000002 สำเร็จแล้ว ขอบคุณ', 'order', 2, 0, '2026-04-07 16:36:13'),
(4, 8, 'promotion', 'รับส่วนลดพิเศษสำหรับสมาชิก', 'ใช้โค้ด TECH200 ลดทันที หมดเขต 31 ม.ค.', NULL, NULL, 0, '2026-04-07 16:36:13'),
(5, 3, 'new_order', 'มีคำสั่งซื้อใหม่!', 'ออเดอร์ ORD-20240501-000004 รอดำเนินการ', 'order', 4, 1, '2026-04-07 16:36:13'),
(6, 15, 'flash_sale', 'Flash Sale Ó╣ÇÓ©úÓ©┤Ó╣êÓ©íÓ╣üÓ©ÑÓ╣ëÓ©º!', 'Ó╣âÓ©èÓ╣ëÓ╣éÓ©äÓ╣ëÓ©ö SHOPEE50 Ó©ÑÓ©öÓ©ùÓ©▒Ó©ÖÓ©ùÓ©Á', NULL, NULL, 0, '2026-04-07 17:21:46'),
(7, 16, 'flash_sale', 'Ó©¡Ó©¡Ó╣ÇÓ©öÓ©¡Ó©úÓ╣îÓ©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©üÓ©│Ó©ÑÓ©▒Ó©çÓ╣ÇÓ©öÓ©┤Ó©ÖÓ©ùÓ©▓Ó©ç!', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©ûÓ©╣Ó©üÓ©êÓ©▒Ó©öÓ©¬Ó╣êÓ©çÓ╣üÓ©ÑÓ╣ëÓ©º', NULL, NULL, 1, '2026-04-07 17:21:46'),
(8, 17, 'review_remind', 'Ó©¡Ó©¡Ó╣ÇÓ©öÓ©¡Ó©úÓ╣îÓ©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©üÓ©│Ó©ÑÓ©▒Ó©çÓ╣ÇÓ©öÓ©┤Ó©ÖÓ©ùÓ©▓Ó©ç!', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©ùÓ©ÁÓ╣êÓ©¬Ó©▒Ó╣êÓ©çÓ©úÓ©¡Ó©üÓ©▓Ó©úÓ©úÓ©ÁÓ©ºÓ©┤Ó©º', NULL, NULL, 0, '2026-04-07 17:21:46'),
(9, 18, 'order_update', 'อัพเดทสถานะออเดอร์', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©ùÓ©ÁÓ╣êÓ©¬Ó©▒Ó╣êÓ©çÓ©úÓ©¡Ó©üÓ©▓Ó©úÓ©úÓ©ÁÓ©ºÓ©┤Ó©º', NULL, NULL, 0, '2026-04-07 17:21:46'),
(10, 19, 'flash_sale', 'Ó©¡Ó©óÓ╣êÓ©▓Ó©ÑÓ©ÀÓ©íÓ©úÓ©ÁÓ©ºÓ©┤Ó©ºÓ©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©ûÓ©╣Ó©üÓ©êÓ©▒Ó©öÓ©¬Ó╣êÓ©çÓ╣üÓ©ÑÓ╣ëÓ©º', NULL, NULL, 1, '2026-04-07 17:21:46'),
(11, 20, 'flash_sale', 'Ó©¡Ó©¡Ó╣ÇÓ©öÓ©¡Ó©úÓ╣îÓ©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©üÓ©│Ó©ÑÓ©▒Ó©çÓ╣ÇÓ©öÓ©┤Ó©ÖÓ©ùÓ©▓Ó©ç!', 'Ó©¡Ó©óÓ╣êÓ©▓Ó©×Ó©ÑÓ©▓Ó©ö Ó©úÓ©▓Ó©äÓ©▓Ó©öÓ©ÁÓ╣üÓ©äÓ╣êÓ©ºÓ©▒Ó©ÖÓ©ÖÓ©ÁÓ╣ë', NULL, NULL, 1, '2026-04-07 17:21:46'),
(12, 21, 'order_update', 'อัพเดทสถานะออเดอร์', 'Ó╣âÓ©èÓ╣ëÓ╣éÓ©äÓ╣ëÓ©ö SHOPEE50 Ó©ÑÓ©öÓ©ùÓ©▒Ó©ÖÓ©ùÓ©Á', NULL, NULL, 1, '2026-04-07 17:21:46'),
(13, 22, 'review_remind', 'Ó©¡Ó©¡Ó╣ÇÓ©öÓ©¡Ó©úÓ╣îÓ©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©üÓ©│Ó©ÑÓ©▒Ó©çÓ╣ÇÓ©öÓ©┤Ó©ÖÓ©ùÓ©▓Ó©ç!', 'Ó╣âÓ©èÓ╣ëÓ╣éÓ©äÓ╣ëÓ©ö SHOPEE50 Ó©ÑÓ©öÓ©ùÓ©▒Ó©ÖÓ©ùÓ©Á', NULL, NULL, 1, '2026-04-07 17:21:46'),
(14, 23, 'promotion', 'โปรโมชั่นใหม่', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©ûÓ©╣Ó©üÓ©êÓ©▒Ó©öÓ©¬Ó╣êÓ©çÓ╣üÓ©ÑÓ╣ëÓ©º', NULL, NULL, 0, '2026-04-07 17:21:46'),
(15, 24, 'order_update', 'อัพเดทสถานะออเดอร์', 'Ó╣âÓ©èÓ╣ëÓ╣éÓ©äÓ╣ëÓ©ö SHOPEE50 Ó©ÑÓ©öÓ©ùÓ©▒Ó©ÖÓ©ùÓ©Á', NULL, NULL, 1, '2026-04-07 17:21:46'),
(16, 25, 'review_remind', 'Ó©¡Ó©¡Ó╣ÇÓ©öÓ©¡Ó©úÓ╣îÓ©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©üÓ©│Ó©ÑÓ©▒Ó©çÓ╣ÇÓ©öÓ©┤Ó©ÖÓ©ùÓ©▓Ó©ç!', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©ûÓ©╣Ó©üÓ©êÓ©▒Ó©öÓ©¬Ó╣êÓ©çÓ╣üÓ©ÑÓ╣ëÓ©º', NULL, NULL, 1, '2026-04-07 17:21:46'),
(17, 26, 'flash_sale', 'Ó©¡Ó©¡Ó╣ÇÓ©öÓ©¡Ó©úÓ╣îÓ©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©üÓ©│Ó©ÑÓ©▒Ó©çÓ╣ÇÓ©öÓ©┤Ó©ÖÓ©ùÓ©▓Ó©ç!', 'Ó©¡Ó©óÓ╣êÓ©▓Ó©×Ó©ÑÓ©▓Ó©ö Ó©úÓ©▓Ó©äÓ©▓Ó©öÓ©ÁÓ╣üÓ©äÓ╣êÓ©ºÓ©▒Ó©ÖÓ©ÖÓ©ÁÓ╣ë', NULL, NULL, 0, '2026-04-07 17:21:46'),
(18, 27, 'flash_sale', 'Ó©¡Ó©¡Ó╣ÇÓ©öÓ©¡Ó©úÓ╣îÓ©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©üÓ©│Ó©ÑÓ©▒Ó©çÓ╣ÇÓ©öÓ©┤Ó©ÖÓ©ùÓ©▓Ó©ç!', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©ùÓ©ÁÓ╣êÓ©¬Ó©▒Ó╣êÓ©çÓ©úÓ©¡Ó©üÓ©▓Ó©úÓ©úÓ©ÁÓ©ºÓ©┤Ó©º', NULL, NULL, 0, '2026-04-07 17:21:46'),
(19, 28, 'flash_sale', 'Flash Sale Ó╣ÇÓ©úÓ©┤Ó╣êÓ©íÓ╣üÓ©ÑÓ╣ëÓ©º!', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©ùÓ©ÁÓ╣êÓ©¬Ó©▒Ó╣êÓ©çÓ©úÓ©¡Ó©üÓ©▓Ó©úÓ©úÓ©ÁÓ©ºÓ©┤Ó©º', NULL, NULL, 1, '2026-04-07 17:21:46'),
(20, 29, 'order_update', 'อัพเดทสถานะออเดอร์', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©ûÓ©╣Ó©üÓ©êÓ©▒Ó©öÓ©¬Ó╣êÓ©çÓ╣üÓ©ÑÓ╣ëÓ©º', NULL, NULL, 1, '2026-04-07 17:21:46'),
(21, 30, 'flash_sale', 'Flash Sale Ó╣ÇÓ©úÓ©┤Ó╣êÓ©íÓ╣üÓ©ÑÓ╣ëÓ©º!', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©éÓ©¡Ó©çÓ©äÓ©©Ó©ôÓ©ûÓ©╣Ó©üÓ©êÓ©▒Ó©öÓ©¬Ó╣êÓ©çÓ╣üÓ©ÑÓ╣ëÓ©º', NULL, NULL, 1, '2026-04-07 17:21:46'),
(22, 31, 'promotion', 'โปรโมชั่นใหม่', 'Ó╣âÓ©èÓ╣ëÓ╣éÓ©äÓ╣ëÓ©ö SHOPEE50 Ó©ÑÓ©öÓ©ùÓ©▒Ó©ÖÓ©ùÓ©Á', NULL, NULL, 0, '2026-04-07 17:21:46'),
(23, 32, 'promotion', 'โปรโมชั่นใหม่', 'Ó©¡Ó©óÓ╣êÓ©▓Ó©×Ó©ÑÓ©▓Ó©ö Ó©úÓ©▓Ó©äÓ©▓Ó©öÓ©ÁÓ╣üÓ©äÓ╣êÓ©ºÓ©▒Ó©ÖÓ©ÖÓ©ÁÓ╣ë', NULL, NULL, 0, '2026-04-07 17:21:46'),
(24, 33, 'order_update', 'อัพเดทสถานะออเดอร์', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©ùÓ©ÁÓ╣êÓ©¬Ó©▒Ó╣êÓ©çÓ©úÓ©¡Ó©üÓ©▓Ó©úÓ©úÓ©ÁÓ©ºÓ©┤Ó©º', NULL, NULL, 0, '2026-04-07 17:21:46'),
(25, 34, 'promotion', 'โปรโมชั่นใหม่', 'Ó╣âÓ©èÓ╣ëÓ╣éÓ©äÓ╣ëÓ©ö SHOPEE50 Ó©ÑÓ©öÓ©ùÓ©▒Ó©ÖÓ©ùÓ©Á', NULL, NULL, 0, '2026-04-07 17:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `buyer_user_id` int(10) UNSIGNED NOT NULL,
  `shop_id` int(10) UNSIGNED NOT NULL,
  `address_id` int(10) UNSIGNED NOT NULL,
  `provider_id` int(10) UNSIGNED DEFAULT NULL,
  `voucher_id` int(10) UNSIGNED DEFAULT NULL,
  `platform_voucher_id` int(10) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shop_discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `voucher_discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `coins_used` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cod','credit_card','debit_card','bank_transfer','e_wallet','shopee_pay','coins') NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `order_status` enum('pending','confirmed','processing','shipped','delivered','completed','cancelled','return_requested','returned') NOT NULL DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancel_reason` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_number`, `buyer_user_id`, `shop_id`, `address_id`, `provider_id`, `voucher_id`, `platform_voucher_id`, `subtotal`, `shipping_fee`, `shop_discount`, `voucher_discount`, `coins_used`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `note`, `tracking_number`, `shipped_at`, `delivered_at`, `completed_at`, `cancelled_at`, `cancel_reason`, `created_at`, `updated_at`) VALUES
(1, 'ORD-20240401-000001', 6, 1, 1, 5, NULL, NULL, 42900.00, 0.00, 0.00, 0.00, 0.00, 42900.00, 'shopee_pay', 'paid', 'completed', NULL, 'SPX1234567890', '2024-04-01 14:00:00', '2024-04-04 10:30:00', '2024-04-05 08:00:00', NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(2, 'ORD-20240402-000002', 7, 2, 3, 1, NULL, NULL, 490.00, 40.00, 0.00, 0.00, 0.00, 530.00, 'cod', 'paid', 'completed', NULL, 'JNT9876543210', '2024-04-02 09:00:00', '2024-04-05 13:00:00', '2024-04-06 09:00:00', NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(3, 'ORD-20240410-000003', 8, 1, 4, 5, NULL, NULL, 10990.00, 0.00, 500.00, 0.00, 0.00, 10490.00, 'credit_card', 'paid', 'completed', NULL, 'SPX1111111111', '2024-04-10 11:00:00', '2024-04-13 15:00:00', '2024-04-14 10:00:00', NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(4, 'ORD-20240501-000004', 9, 3, 5, 3, NULL, NULL, 599.00, 50.00, 0.00, 0.00, 0.00, 649.00, 'bank_transfer', 'paid', 'shipped', NULL, 'FLH0000000001', '2024-05-01 16:00:00', NULL, NULL, NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(5, 'ORD-20240502-000005', 10, 2, 6, 1, NULL, NULL, 890.00, 40.00, 0.00, 0.00, 0.00, 930.00, 'cod', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 17:23:31'),
(31, 'ORD-20240301-000006', 15, 1, 7, 1, NULL, NULL, 8490.00, 0.00, 0.00, 0.00, 0.00, 8490.00, 'shopee_pay', 'paid', 'completed', NULL, 'JNT1000000006', '2024-03-01 00:00:00', '2024-03-04 00:00:00', '2024-03-05 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(32, 'ORD-20240302-000007', 16, 2, 8, 3, NULL, NULL, 690.00, 40.00, 0.00, 0.00, 0.00, 730.00, 'cod', 'paid', 'completed', NULL, 'FLH1000000007', '2024-03-02 00:00:00', '2024-03-06 00:00:00', NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(33, 'ORD-20240310-000008', 17, 3, 9, 5, NULL, NULL, 3990.00, 0.00, 200.00, 0.00, 0.00, 3790.00, 'bank_transfer', 'paid', 'completed', NULL, 'SPX1000000008', '2024-03-10 00:00:00', '2024-03-14 00:00:00', '2024-03-15 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(34, 'ORD-20240315-000009', 18, 1, 10, 5, NULL, NULL, 46900.00, 0.00, 0.00, 500.00, 0.00, 46400.00, 'credit_card', 'paid', 'completed', NULL, 'SPX1000000009', '2024-03-15 00:00:00', '2024-03-18 00:00:00', '2024-03-20 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(35, 'ORD-20240320-000010', 19, 4, 11, 1, NULL, NULL, 750.00, 40.00, 0.00, 0.00, 0.00, 790.00, 'cod', 'paid', 'completed', NULL, 'JNT1000000010', '2024-03-20 00:00:00', '2024-03-23 00:00:00', '2024-03-25 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(36, 'ORD-20240325-000011', 20, 5, 12, 3, NULL, NULL, 3490.00, 40.00, 0.00, 0.00, 0.00, 3530.00, 'shopee_pay', 'paid', 'completed', NULL, 'FLH1000000011', '2024-03-25 00:00:00', '2024-03-28 00:00:00', '2024-03-30 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(37, 'ORD-20240401-000012', 21, 2, 13, 1, NULL, NULL, 990.00, 40.00, 0.00, 0.00, 0.00, 1030.00, 'cod', 'paid', 'completed', NULL, 'JNT1000000012', '2024-04-01 00:00:00', '2024-04-04 00:00:00', '2024-04-05 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(38, 'ORD-20240405-000013', 22, 1, 14, 5, NULL, NULL, 2990.00, 0.00, 0.00, 0.00, 0.00, 2990.00, 'shopee_pay', 'paid', 'completed', NULL, 'SPX1000000013', '2024-04-05 00:00:00', '2024-04-08 00:00:00', '2024-04-10 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(39, 'ORD-20240410-000014', 23, 3, 15, 3, NULL, NULL, 1590.00, 40.00, 0.00, 0.00, 0.00, 1630.00, 'bank_transfer', 'paid', 'completed', NULL, 'FLH1000000014', '2024-04-10 00:00:00', '2024-04-14 00:00:00', '2024-04-15 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(40, 'ORD-20240415-000015', 24, 4, 16, 1, NULL, NULL, 280.00, 40.00, 0.00, 0.00, 0.00, 320.00, 'cod', 'paid', 'completed', NULL, 'JNT1000000015', '2024-04-15 00:00:00', '2024-04-18 00:00:00', '2024-04-20 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(41, 'ORD-20240420-000016', 25, 5, 17, 5, NULL, NULL, 590.00, 40.00, 0.00, 0.00, 0.00, 630.00, 'shopee_pay', 'paid', 'shipped', NULL, 'SPX1000000016', '2024-04-20 00:00:00', NULL, NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(42, 'ORD-20240425-000017', 26, 1, 18, 1, NULL, NULL, 84900.00, 0.00, 0.00, 0.00, 0.00, 84900.00, 'credit_card', 'paid', 'delivered', NULL, 'JNT1000000017', '2024-04-25 00:00:00', '2024-04-28 00:00:00', NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(43, 'ORD-20240501-000018', 27, 2, 19, 3, NULL, NULL, 1290.00, 40.00, 0.00, 0.00, 0.00, 1330.00, 'cod', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(44, 'ORD-20240502-000019', 28, 3, 20, 5, NULL, NULL, 4200.00, 0.00, 0.00, 0.00, 0.00, 4200.00, 'shopee_pay', 'paid', 'processing', NULL, 'SPX1000000019', '2024-05-02 00:00:00', NULL, NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(45, 'ORD-20240503-000020', 29, 4, 21, 1, NULL, NULL, 990.00, 40.00, 0.00, 0.00, 0.00, 1030.00, 'cod', 'pending', 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(46, 'ORD-20240504-000021', 15, 5, 7, 3, NULL, NULL, 13500.00, 0.00, 0.00, 0.00, 0.00, 13500.00, 'bank_transfer', 'paid', 'shipped', NULL, 'FLH1000000021', '2024-05-04 00:00:00', NULL, NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(47, 'ORD-20240505-000022', 16, 1, 8, 5, NULL, NULL, 9490.00, 0.00, 0.00, 0.00, 0.00, 9490.00, 'shopee_pay', 'paid', 'delivered', NULL, 'SPX1000000022', '2024-05-05 00:00:00', '2024-05-08 00:00:00', NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(48, 'ORD-20240506-000023', 17, 2, 9, 1, NULL, NULL, 690.00, 40.00, 0.00, 0.00, 0.00, 730.00, 'cod', 'paid', 'completed', NULL, 'JNT1000000023', '2024-05-06 00:00:00', '2024-05-09 00:00:00', '2024-05-10 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(49, 'ORD-20240507-000024', 18, 3, 10, 3, NULL, NULL, 3990.00, 0.00, 0.00, 0.00, 0.00, 3990.00, 'shopee_pay', 'paid', 'completed', NULL, 'FLH1000000024', '2024-05-07 00:00:00', '2024-05-10 00:00:00', '2024-05-12 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(50, 'ORD-20240508-000025', 19, 4, 11, 5, NULL, NULL, 750.00, 40.00, 0.00, 50.00, 0.00, 740.00, 'shopee_pay', 'paid', 'completed', NULL, 'SPX1000000025', '2024-05-08 00:00:00', '2024-05-11 00:00:00', '2024-05-13 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(51, 'ORD-20240509-000026', 20, 5, 12, 1, NULL, NULL, 13900.00, 0.00, 0.00, 0.00, 0.00, 13900.00, 'bank_transfer', 'paid', 'return_requested', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(52, 'ORD-20240510-000027', 21, 1, 13, 5, NULL, NULL, 2990.00, 0.00, 0.00, 0.00, 0.00, 2990.00, 'shopee_pay', 'paid', 'completed', NULL, 'SPX1000000027', '2024-05-10 00:00:00', '2024-05-13 00:00:00', '2024-05-14 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(53, 'ORD-20240511-000028', 22, 2, 14, 3, NULL, NULL, 490.00, 40.00, 0.00, 0.00, 0.00, 530.00, 'cod', 'paid', 'completed', NULL, 'FLH1000000028', '2024-05-11 00:00:00', '2024-05-14 00:00:00', '2024-05-15 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(54, 'ORD-20240512-000029', 23, 3, 15, 1, NULL, NULL, 1590.00, 40.00, 0.00, 0.00, 0.00, 1630.00, 'cod', 'paid', 'confirmed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(55, 'ORD-20240513-000030', 24, 4, 16, 5, NULL, NULL, 280.00, 40.00, 0.00, 0.00, 0.00, 320.00, 'shopee_pay', 'paid', 'completed', NULL, 'SPX1000000030', '2024-05-13 00:00:00', '2024-05-16 00:00:00', '2024-05-17 00:00:00', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(56, 'ORD-20260407-030447', 61, 3, 38, 1, NULL, NULL, 599.00, 0.00, 0.00, 0.00, 0.00, 599.00, 'cod', 'pending', 'cancelled', '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 20:12:53', '2026-04-07 20:33:11'),
(57, 'ORD-20260407-056969', 61, 1, 38, 4, NULL, NULL, 29990.00, 0.00, 0.00, 0.00, 0.00, 29990.00, 'shopee_pay', 'pending', 'pending', '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 20:35:01', '2026-04-07 20:35:01'),
(58, 'ORD-20260407-088560', 63, 1, 39, 1, NULL, NULL, 209930.00, 0.00, 0.00, 0.00, 0.00, 209930.00, 'cod', 'pending', 'pending', '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 20:59:49', '2026-04-07 20:59:49'),
(59, 'ORD-20260407-089194', 63, 4, 39, 1, NULL, NULL, 750.00, 0.00, 0.00, 0.00, 0.00, 750.00, 'cod', 'pending', 'cancelled', '', NULL, NULL, NULL, NULL, '2026-04-07 20:59:57', NULL, '2026-04-07 20:59:49', '2026-04-07 20:59:57');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `sku_id` int(10) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `sku_snapshot` varchar(500) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `subtotal` decimal(12,2) NOT NULL,
  `review_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `sku_id`, `product_name`, `sku_snapshot`, `image_url`, `unit_price`, `quantity`, `subtotal`, `review_id`) VALUES
(1, 1, 1, 1, 'Samsung Galaxy S24 Ultra 256GB', '256GB / Titanium Black', 'https://cdn.shopee.th/img/s24u-1.jpg', 42900.00, 1, 42900.00, NULL),
(2, 2, 4, 7, 'Oversized Pastel Hoodie', 'S / Pink', 'https://cdn.shopee.th/img/hoodie-1.jpg', 490.00, 1, 490.00, NULL),
(3, 3, 3, 5, 'Sony WH-1000XM5 Headphones', 'Black', 'https://cdn.shopee.th/img/xm5-1.jpg', 10990.00, 1, 10990.00, NULL),
(4, 4, 6, 12, 'Ceramic Non-Stick Frying Pan', '28cm', 'https://cdn.shopee.th/img/pan-1.jpg', 599.00, 1, 599.00, NULL),
(5, 5, 5, 11, 'White Chunky Platform Sneakers', 'Size 39', 'https://cdn.shopee.th/img/sneaker-1.jpg', 890.00, 1, 890.00, NULL),
(6, 31, 1, NULL, 'Samsung Galaxy S24 Ultra 256GB', NULL, 'https://cdn.shopee.th/img/samsung-galaxy-s24-ultra-256.jpg', 8490.00, 1, 8490.00, NULL),
(7, 32, 4, NULL, 'Oversized Pastel Hoodie', NULL, 'https://cdn.shopee.th/img/oversized-pastel-hoodie.jpg', 690.00, 1, 690.00, NULL),
(8, 33, 6, NULL, 'Ceramic Non-Stick Frying Pan 28cm', NULL, 'https://cdn.shopee.th/img/ceramic-nonstick-frypan-28.jpg', 3990.00, 1, 3990.00, NULL),
(9, 34, 1, NULL, 'Samsung Galaxy S24 Ultra 256GB', NULL, 'https://cdn.shopee.th/img/samsung-galaxy-s24-ultra-256.jpg', 46900.00, 1, 46900.00, NULL),
(10, 35, 24, NULL, 'Ó╣ÇÓ©ïÓ©úÓ©▒Ó╣êÓ©íÓ©ºÓ©┤Ó©òÓ©▓Ó©íÓ©┤Ó©ÖÓ©ïÓ©Á Skinsation', NULL, 'https://cdn.shopee.th/img/serum-vitc-skinsation.jpg', 750.00, 1, 750.00, NULL),
(11, 36, 28, NULL, 'Ó©öÓ©▒Ó©íÓ╣ÇÓ©ÜÓ©ÑÓ©ÖÓ╣ëÓ©│Ó©½Ó©ÖÓ©▒Ó©üÓ©øÓ©úÓ©▒Ó©ÜÓ╣äÓ©öÓ╣ë 2-24kg', NULL, 'https://cdn.shopee.th/img/adjustable-dumbbell-24.jpg', 3490.00, 1, 3490.00, NULL),
(12, 37, 4, NULL, 'Oversized Pastel Hoodie', NULL, 'https://cdn.shopee.th/img/oversized-pastel-hoodie.jpg', 990.00, 1, 990.00, NULL),
(13, 38, 1, NULL, 'Samsung Galaxy S24 Ultra 256GB', NULL, 'https://cdn.shopee.th/img/samsung-galaxy-s24-ultra-256.jpg', 2990.00, 1, 2990.00, NULL),
(14, 39, 6, NULL, 'Ceramic Non-Stick Frying Pan 28cm', NULL, 'https://cdn.shopee.th/img/ceramic-nonstick-frypan-28.jpg', 1590.00, 1, 1590.00, NULL),
(15, 40, 24, NULL, 'Ó╣ÇÓ©ïÓ©úÓ©▒Ó╣êÓ©íÓ©ºÓ©┤Ó©òÓ©▓Ó©íÓ©┤Ó©ÖÓ©ïÓ©Á Skinsation', NULL, 'https://cdn.shopee.th/img/serum-vitc-skinsation.jpg', 280.00, 1, 280.00, NULL),
(16, 41, 28, NULL, 'Ó©öÓ©▒Ó©íÓ╣ÇÓ©ÜÓ©ÑÓ©ÖÓ╣ëÓ©│Ó©½Ó©ÖÓ©▒Ó©üÓ©øÓ©úÓ©▒Ó©ÜÓ╣äÓ©öÓ╣ë 2-24kg', NULL, 'https://cdn.shopee.th/img/adjustable-dumbbell-24.jpg', 590.00, 1, 590.00, NULL),
(17, 42, 1, NULL, 'Samsung Galaxy S24 Ultra 256GB', NULL, 'https://cdn.shopee.th/img/samsung-galaxy-s24-ultra-256.jpg', 84900.00, 1, 84900.00, NULL),
(18, 43, 4, NULL, 'Oversized Pastel Hoodie', NULL, 'https://cdn.shopee.th/img/oversized-pastel-hoodie.jpg', 1290.00, 1, 1290.00, NULL),
(19, 44, 6, NULL, 'Ceramic Non-Stick Frying Pan 28cm', NULL, 'https://cdn.shopee.th/img/ceramic-nonstick-frypan-28.jpg', 4200.00, 1, 4200.00, NULL),
(20, 45, 24, NULL, 'Ó╣ÇÓ©ïÓ©úÓ©▒Ó╣êÓ©íÓ©ºÓ©┤Ó©òÓ©▓Ó©íÓ©┤Ó©ÖÓ©ïÓ©Á Skinsation', NULL, 'https://cdn.shopee.th/img/serum-vitc-skinsation.jpg', 990.00, 1, 990.00, NULL),
(21, 46, 28, NULL, 'Ó©öÓ©▒Ó©íÓ╣ÇÓ©ÜÓ©ÑÓ©ÖÓ╣ëÓ©│Ó©½Ó©ÖÓ©▒Ó©üÓ©øÓ©úÓ©▒Ó©ÜÓ╣äÓ©öÓ╣ë 2-24kg', NULL, 'https://cdn.shopee.th/img/adjustable-dumbbell-24.jpg', 13500.00, 1, 13500.00, NULL),
(22, 47, 1, NULL, 'Samsung Galaxy S24 Ultra 256GB', NULL, 'https://cdn.shopee.th/img/samsung-galaxy-s24-ultra-256.jpg', 9490.00, 1, 9490.00, NULL),
(23, 48, 4, NULL, 'Oversized Pastel Hoodie', NULL, 'https://cdn.shopee.th/img/oversized-pastel-hoodie.jpg', 690.00, 1, 690.00, NULL),
(24, 49, 6, NULL, 'Ceramic Non-Stick Frying Pan 28cm', NULL, 'https://cdn.shopee.th/img/ceramic-nonstick-frypan-28.jpg', 3990.00, 1, 3990.00, NULL),
(25, 50, 24, NULL, 'Ó╣ÇÓ©ïÓ©úÓ©▒Ó╣êÓ©íÓ©ºÓ©┤Ó©òÓ©▓Ó©íÓ©┤Ó©ÖÓ©ïÓ©Á Skinsation', NULL, 'https://cdn.shopee.th/img/serum-vitc-skinsation.jpg', 750.00, 1, 750.00, NULL),
(26, 51, 28, NULL, 'Ó©öÓ©▒Ó©íÓ╣ÇÓ©ÜÓ©ÑÓ©ÖÓ╣ëÓ©│Ó©½Ó©ÖÓ©▒Ó©üÓ©øÓ©úÓ©▒Ó©ÜÓ╣äÓ©öÓ╣ë 2-24kg', NULL, 'https://cdn.shopee.th/img/adjustable-dumbbell-24.jpg', 13900.00, 1, 13900.00, NULL),
(27, 52, 1, NULL, 'Samsung Galaxy S24 Ultra 256GB', NULL, 'https://cdn.shopee.th/img/samsung-galaxy-s24-ultra-256.jpg', 2990.00, 1, 2990.00, NULL),
(28, 53, 4, NULL, 'Oversized Pastel Hoodie', NULL, 'https://cdn.shopee.th/img/oversized-pastel-hoodie.jpg', 490.00, 1, 490.00, NULL),
(29, 54, 6, NULL, 'Ceramic Non-Stick Frying Pan 28cm', NULL, 'https://cdn.shopee.th/img/ceramic-nonstick-frypan-28.jpg', 1590.00, 1, 1590.00, NULL),
(30, 55, 24, NULL, 'Ó╣ÇÓ©ïÓ©úÓ©▒Ó╣êÓ©íÓ©ºÓ©┤Ó©òÓ©▓Ó©íÓ©┤Ó©ÖÓ©ïÓ©Á Skinsation', NULL, 'https://cdn.shopee.th/img/serum-vitc-skinsation.jpg', 280.00, 1, 280.00, NULL),
(37, 56, 6, NULL, 'หูฟังไร้สาย Bluetooth 5.3', NULL, '/webshop/uploads/products/product_6.jpg', 599.00, 1, 599.00, NULL),
(38, 57, 9, NULL, 'ผ้าปูที่นอนเซ็ต 6 ฟุต 6 ชิ้น', NULL, '/webshop/uploads/products/product_9.jpg', 29990.00, 1, 29990.00, NULL),
(39, 58, 9, NULL, 'ผ้าปูที่นอนเซ็ต 6 ฟุต 6 ชิ้น', NULL, '/webshop/uploads/products/product_9.jpg', 29990.00, 7, 209930.00, NULL),
(40, 59, 24, NULL, 'เสื้อยืดคอกลมแพ็ค 3 ตัว', NULL, '/webshop/uploads/products/product_24.jpg', 750.00, 1, 750.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `history_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','completed','cancelled','return_requested','returned') NOT NULL,
  `note` varchar(500) DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`history_id`, `order_id`, `status`, `note`, `created_by`, `created_at`) VALUES
(1, 1, 'pending', 'Order placed', 6, '2026-04-07 16:36:13'),
(2, 1, 'confirmed', 'Seller confirmed', 3, '2026-04-07 16:36:13'),
(3, 1, 'shipped', 'Handed to SPX', 3, '2026-04-07 16:36:13'),
(4, 1, 'delivered', 'Delivered successfully', NULL, '2026-04-07 16:36:13'),
(5, 1, 'completed', 'Auto-completed after 7d', NULL, '2026-04-07 16:36:13'),
(6, 2, 'pending', 'Order placed', 7, '2026-04-07 16:36:13'),
(7, 2, 'confirmed', 'Seller confirmed', 4, '2026-04-07 16:36:13'),
(8, 2, 'shipped', 'Shipped via JNT', 4, '2026-04-07 16:36:13'),
(9, 2, 'delivered', 'Delivered', NULL, '2026-04-07 16:36:13'),
(10, 2, 'completed', 'Buyer confirmed receipt', 7, '2026-04-07 16:36:13'),
(11, 4, 'pending', 'Order placed', 9, '2026-04-07 16:36:13'),
(12, 4, 'confirmed', 'Seller confirmed', 5, '2026-04-07 16:36:13'),
(13, 4, 'shipped', 'Shipped via Flash', 5, '2026-04-07 16:36:13'),
(14, 31, 'pending', 'Order placed', 15, '2026-04-07 17:21:46'),
(15, 46, 'pending', 'Order placed', 15, '2026-04-07 17:21:46'),
(16, 32, 'pending', 'Order placed', 16, '2026-04-07 17:21:46'),
(17, 47, 'pending', 'Order placed', 16, '2026-04-07 17:21:46'),
(18, 33, 'pending', 'Order placed', 17, '2026-04-07 17:21:46'),
(19, 48, 'pending', 'Order placed', 17, '2026-04-07 17:21:46'),
(20, 34, 'pending', 'Order placed', 18, '2026-04-07 17:21:46'),
(21, 49, 'pending', 'Order placed', 18, '2026-04-07 17:21:46'),
(22, 35, 'pending', 'Order placed', 19, '2026-04-07 17:21:46'),
(23, 50, 'pending', 'Order placed', 19, '2026-04-07 17:21:46'),
(24, 36, 'pending', 'Order placed', 20, '2026-04-07 17:21:46'),
(25, 51, 'pending', 'Order placed', 20, '2026-04-07 17:21:46'),
(26, 37, 'pending', 'Order placed', 21, '2026-04-07 17:21:46'),
(27, 52, 'pending', 'Order placed', 21, '2026-04-07 17:21:46'),
(28, 38, 'pending', 'Order placed', 22, '2026-04-07 17:21:46'),
(29, 53, 'pending', 'Order placed', 22, '2026-04-07 17:21:46'),
(30, 39, 'pending', 'Order placed', 23, '2026-04-07 17:21:46'),
(31, 54, 'pending', 'Order placed', 23, '2026-04-07 17:21:46'),
(32, 40, 'pending', 'Order placed', 24, '2026-04-07 17:21:46'),
(33, 55, 'pending', 'Order placed', 24, '2026-04-07 17:21:46'),
(34, 41, 'pending', 'Order placed', 25, '2026-04-07 17:21:46'),
(35, 42, 'pending', 'Order placed', 26, '2026-04-07 17:21:46'),
(36, 43, 'pending', 'Order placed', 27, '2026-04-07 17:21:46'),
(37, 44, 'pending', 'Order placed', 28, '2026-04-07 17:21:46'),
(38, 45, 'pending', 'Order placed', 29, '2026-04-07 17:21:46'),
(45, 54, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(46, 44, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(47, 41, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(48, 46, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(49, 42, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(50, 47, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(51, 31, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(52, 32, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(53, 33, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(54, 34, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(55, 35, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(56, 36, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(57, 37, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(58, 38, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(59, 39, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(60, 40, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(61, 48, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(62, 49, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(63, 50, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(64, 52, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(65, 53, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(66, 55, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(67, 51, 'confirmed', 'Seller confirmed', 11, '2026-04-07 17:21:46'),
(76, 31, 'shipped', 'Shipped ÔÇô JNT1000000006', 11, '2026-04-07 17:21:46'),
(77, 32, 'shipped', 'Shipped ÔÇô FLH1000000007', 11, '2026-04-07 17:21:46'),
(78, 33, 'shipped', 'Shipped ÔÇô SPX1000000008', 11, '2026-04-07 17:21:46'),
(79, 34, 'shipped', 'Shipped ÔÇô SPX1000000009', 11, '2026-04-07 17:21:46'),
(80, 35, 'shipped', 'Shipped ÔÇô JNT1000000010', 11, '2026-04-07 17:21:46'),
(81, 36, 'shipped', 'Shipped ÔÇô FLH1000000011', 11, '2026-04-07 17:21:46'),
(82, 37, 'shipped', 'Shipped ÔÇô JNT1000000012', 11, '2026-04-07 17:21:46'),
(83, 38, 'shipped', 'Shipped ÔÇô SPX1000000013', 11, '2026-04-07 17:21:46'),
(84, 39, 'shipped', 'Shipped ÔÇô FLH1000000014', 11, '2026-04-07 17:21:46'),
(85, 40, 'shipped', 'Shipped ÔÇô JNT1000000015', 11, '2026-04-07 17:21:46'),
(86, 41, 'shipped', 'Shipped ÔÇô SPX1000000016', 11, '2026-04-07 17:21:46'),
(87, 42, 'shipped', 'Shipped ÔÇô JNT1000000017', 11, '2026-04-07 17:21:46'),
(88, 44, 'shipped', 'Shipped ÔÇô SPX1000000019', 11, '2026-04-07 17:21:46'),
(89, 46, 'shipped', 'Shipped ÔÇô FLH1000000021', 11, '2026-04-07 17:21:46'),
(90, 47, 'shipped', 'Shipped ÔÇô SPX1000000022', 11, '2026-04-07 17:21:46'),
(91, 48, 'shipped', 'Shipped ÔÇô JNT1000000023', 11, '2026-04-07 17:21:46'),
(92, 49, 'shipped', 'Shipped ÔÇô FLH1000000024', 11, '2026-04-07 17:21:46'),
(93, 50, 'shipped', 'Shipped ÔÇô SPX1000000025', 11, '2026-04-07 17:21:46'),
(94, 52, 'shipped', 'Shipped ÔÇô SPX1000000027', 11, '2026-04-07 17:21:46'),
(95, 53, 'shipped', 'Shipped ÔÇô FLH1000000028', 11, '2026-04-07 17:21:46'),
(96, 55, 'shipped', 'Shipped ÔÇô SPX1000000030', 11, '2026-04-07 17:21:46'),
(107, 31, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(108, 32, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(109, 33, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(110, 34, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(111, 35, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(112, 36, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(113, 37, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(114, 38, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(115, 39, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(116, 40, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(117, 42, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(118, 47, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(119, 48, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(120, 49, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(121, 50, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(122, 52, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(123, 53, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(124, 55, 'delivered', 'Delivered', NULL, '2026-04-07 17:21:46'),
(138, 31, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(139, 32, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(140, 33, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(141, 34, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(142, 35, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(143, 36, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(144, 37, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(145, 38, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(146, 39, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(147, 40, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(148, 48, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(149, 49, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(150, 50, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(151, 52, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(152, 53, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(153, 55, 'completed', 'Auto-completed', NULL, '2026-04-07 17:21:46'),
(169, 56, 'pending', 'Order placed', 61, '2026-04-07 20:12:53'),
(170, 56, 'cancelled', 'User cancelled', 61, '2026-04-07 20:33:11'),
(171, 57, 'pending', 'Order placed', 61, '2026-04-07 20:35:01'),
(172, 58, 'pending', 'Order placed', 63, '2026-04-07 20:59:49'),
(173, 59, 'pending', 'Order placed', 63, '2026-04-07 20:59:49'),
(174, 59, 'cancelled', 'ลูกค้ายกเลิกคำสั่งซื้อ', 63, '2026-04-07 20:59:57');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `otp_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `contact` varchar(150) NOT NULL,
  `contact_type` enum('email','phone') NOT NULL DEFAULT 'email',
  `otp_code` varchar(10) NOT NULL,
  `purpose` enum('register','login','reset_password','change_email','change_phone','verify') NOT NULL DEFAULT 'verify',
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `max_attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `payment_method` enum('cod','credit_card','debit_card','bank_transfer','e_wallet','shopee_pay','coins') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'THB',
  `status` enum('pending','success','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `transaction_ref` varchar(255) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `paid_at` datetime DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `refund_amount` decimal(12,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `amount`, `currency`, `status`, `transaction_ref`, `gateway_response`, `paid_at`, `refunded_at`, `refund_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 'shopee_pay', 42900.00, 'THB', 'success', 'TXN-SP-00001', NULL, '2024-04-01 10:05:00', NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(2, 2, 'cod', 530.00, 'THB', 'success', 'TXN-COD-00002', NULL, '2024-04-05 13:00:00', NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(3, 3, 'credit_card', 10490.00, 'THB', 'success', 'TXN-CC-00003', NULL, '2024-04-10 09:30:00', NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(4, 4, 'bank_transfer', 649.00, 'THB', 'success', 'TXN-BT-00004', NULL, '2024-05-01 12:00:00', NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(5, 5, 'cod', 930.00, 'THB', 'pending', NULL, NULL, NULL, NULL, NULL, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(6, 31, 'shopee_pay', 8490.00, 'THB', 'success', 'TXN-SHO-000031', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(7, 32, 'cod', 730.00, 'THB', 'success', 'TXN-COD-000032', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(8, 33, 'bank_transfer', 3790.00, 'THB', 'success', 'TXN-BAN-000033', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(9, 34, 'credit_card', 46400.00, 'THB', 'success', 'TXN-CRE-000034', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(10, 35, 'cod', 790.00, 'THB', 'success', 'TXN-COD-000035', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(11, 36, 'shopee_pay', 3530.00, 'THB', 'success', 'TXN-SHO-000036', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(12, 37, 'cod', 1030.00, 'THB', 'success', 'TXN-COD-000037', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(13, 38, 'shopee_pay', 2990.00, 'THB', 'success', 'TXN-SHO-000038', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(14, 39, 'bank_transfer', 1630.00, 'THB', 'success', 'TXN-BAN-000039', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(15, 40, 'cod', 320.00, 'THB', 'success', 'TXN-COD-000040', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(16, 41, 'shopee_pay', 630.00, 'THB', 'success', 'TXN-SHO-000041', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(17, 42, 'credit_card', 84900.00, 'THB', 'success', 'TXN-CRE-000042', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(18, 43, 'cod', 1330.00, 'THB', 'pending', 'TXN-COD-000043', NULL, NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(19, 44, 'shopee_pay', 4200.00, 'THB', 'success', 'TXN-SHO-000044', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(20, 45, 'cod', 1030.00, 'THB', 'pending', 'TXN-COD-000045', NULL, NULL, NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(21, 46, 'bank_transfer', 13500.00, 'THB', 'success', 'TXN-BAN-000046', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(22, 47, 'shopee_pay', 9490.00, 'THB', 'success', 'TXN-SHO-000047', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(23, 48, 'cod', 730.00, 'THB', 'success', 'TXN-COD-000048', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(24, 49, 'shopee_pay', 3990.00, 'THB', 'success', 'TXN-SHO-000049', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(25, 50, 'shopee_pay', 740.00, 'THB', 'success', 'TXN-SHO-000050', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(26, 51, 'bank_transfer', 13900.00, 'THB', 'success', 'TXN-BAN-000051', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(27, 52, 'shopee_pay', 2990.00, 'THB', 'success', 'TXN-SHO-000052', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(28, 53, 'cod', 530.00, 'THB', 'success', 'TXN-COD-000053', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(29, 54, 'cod', 1630.00, 'THB', 'success', 'TXN-COD-000054', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(30, 55, 'shopee_pay', 320.00, 'THB', 'success', 'TXN-SHO-000055', NULL, '2026-04-07 17:21:46', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(37, 56, 'cod', 599.00, 'THB', 'pending', NULL, NULL, NULL, NULL, NULL, '2026-04-07 20:12:53', '2026-04-07 20:12:53'),
(38, 57, 'shopee_pay', 29990.00, 'THB', 'pending', NULL, NULL, NULL, NULL, NULL, '2026-04-07 20:35:01', '2026-04-07 20:35:01'),
(39, 58, 'cod', 209930.00, 'THB', 'pending', NULL, NULL, NULL, NULL, NULL, '2026-04-07 20:59:49', '2026-04-07 20:59:49'),
(40, 59, 'cod', 750.00, 'THB', 'pending', NULL, NULL, NULL, NULL, NULL, '2026-04-07 20:59:49', '2026-04-07 20:59:49');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `perm_id` int(10) UNSIGNED NOT NULL,
  `perm_key` varchar(100) NOT NULL,
  `label` varchar(150) NOT NULL,
  `perm_group` varchar(100) NOT NULL DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`perm_id`, `perm_key`, `label`, `perm_group`, `description`) VALUES
(1, 'users.view', 'ดูรายการผู้ใช้', 'users', NULL),
(2, 'users.create', 'สร้างผู้ใช้', 'users', NULL),
(3, 'users.edit', 'แก้ไขผู้ใช้', 'users', NULL),
(4, 'users.delete', 'ลบผู้ใช้', 'users', NULL),
(5, 'shops.view', 'ดูร้านค้า', 'shops', NULL),
(6, 'shops.edit', 'แก้ไขร้านค้า', 'shops', NULL),
(7, 'shops.ban', 'Ó©úÓ©░Ó©çÓ©▒Ó©ÜÓ©úÓ╣ëÓ©▓Ó©ÖÓ©äÓ╣ëÓ©▓', 'shops', NULL),
(8, 'products.view', 'ดูสินค้า', 'products', NULL),
(9, 'products.edit', 'แก้ไขสินค้า', 'products', NULL),
(10, 'products.delete', 'ลบสินค้า', 'products', NULL),
(11, 'orders.view', 'ดูคำสั่งซื้อ', 'orders', NULL),
(12, 'orders.edit', 'แก้ไขคำสั่งซื้อ', 'orders', NULL),
(13, 'reviews.view', 'Ó©öÓ©╣Ó©úÓ©ÁÓ©ºÓ©┤Ó©º', 'reviews', NULL),
(14, 'reviews.hide', 'Ó©ïÓ╣êÓ©¡Ó©Ö/Ó╣üÓ©¬Ó©öÓ©çÓ©úÓ©ÁÓ©ºÓ©┤Ó©º', 'reviews', NULL),
(15, 'vouchers.manage', 'Ó©êÓ©▒Ó©öÓ©üÓ©▓Ó©úÓ╣éÓ©äÓ╣ëÓ©öÓ©¬Ó╣êÓ©ºÓ©ÖÓ©ÑÓ©ö', 'vouchers', NULL),
(16, 'flash_sales.manage', 'Ó©êÓ©▒Ó©öÓ©üÓ©▓Ó©ú Flash Sale', 'promotions', NULL),
(17, 'banners.manage', 'Ó©êÓ©▒Ó©öÓ©üÓ©▓Ó©úÓ╣üÓ©ÜÓ©ÖÓ╣ÇÓ©ÖÓ©¡Ó©úÓ╣î', 'promotions', NULL),
(18, 'reports.view', 'ดูรายงาน', 'reports', NULL),
(19, 'cms.pages', 'Ó©êÓ©▒Ó©öÓ©üÓ©▓Ó©úÓ©½Ó©ÖÓ╣ëÓ©▓Ó╣ÇÓ©ºÓ╣çÓ©Ü', 'cms', NULL),
(20, 'cms.menus', 'Ó©êÓ©▒Ó©öÓ©üÓ©▓Ó©úÓ╣ÇÓ©íÓ©ÖÓ©╣', 'cms', NULL),
(21, 'cms.widgets', 'Ó©êÓ©▒Ó©öÓ©üÓ©▓Ó©ú Widget', 'cms', NULL),
(22, 'settings.general', 'Ó©òÓ©▒Ó╣ëÓ©çÓ©äÓ╣êÓ©▓Ó©ùÓ©▒Ó╣êÓ©ºÓ╣äÓ©ø', 'settings', NULL),
(23, 'settings.payment', 'Ó©òÓ©▒Ó╣ëÓ©çÓ©äÓ╣êÓ©▓Ó©üÓ©▓Ó©úÓ©èÓ©│Ó©úÓ©░Ó╣ÇÓ©çÓ©┤Ó©Ö', 'settings', NULL),
(24, 'settings.shipping', 'Ó©òÓ©▒Ó╣ëÓ©çÓ©äÓ╣êÓ©▓Ó©üÓ©▓Ó©úÓ©êÓ©▒Ó©öÓ©¬Ó╣êÓ©ç', 'settings', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `platform_vouchers`
--

CREATE TABLE `platform_vouchers` (
  `voucher_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed','free_shipping') NOT NULL,
  `discount_value` decimal(12,2) NOT NULL,
  `min_order_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `max_discount_cap` decimal(12,2) DEFAULT NULL,
  `total_qty` int(10) UNSIGNED DEFAULT NULL,
  `used_qty` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `per_user_limit` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `applicable_to` enum('all','category','product') NOT NULL DEFAULT 'all',
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `start_at` datetime NOT NULL,
  `expire_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `platform_vouchers`
--

INSERT INTO `platform_vouchers` (`voucher_id`, `code`, `name`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_cap`, `total_qty`, `used_qty`, `per_user_limit`, `applicable_to`, `category_id`, `start_at`, `expire_at`, `is_active`, `created_at`) VALUES
(1, 'SHOPEE50', 'Shopee ลด 50 บาท', 'ส่วนลด 50 บาท สำหรับออเดอร์ 200 บาทขึ้นไป', 'fixed', 50.00, 200.00, NULL, 1000, 0, 1, 'all', NULL, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 1, '2026-04-07 16:36:13'),
(2, 'FREESHIP', 'ฟรีค่าจัดส่ง', 'ฟรีค่าจัดส่ง ไม่มีขั้นต่ำ', 'free_shipping', 0.00, 0.00, 40.00, 5000, 0, 2, 'all', NULL, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 1, '2026-04-07 16:36:13'),
(3, 'NEW30', 'สมาชิกใหม่ ลด 30%', 'ลด 30% สำหรับสมาชิกใหม่', 'percentage', 30.00, 100.00, 200.00, 500, 0, 1, 'all', NULL, '2024-01-01 00:00:00', '2024-06-30 23:59:59', 1, '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `shop_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `base_price` decimal(12,2) NOT NULL,
  `discount_price` decimal(12,2) DEFAULT NULL,
  `condition_type` enum('new','used','refurbished') NOT NULL DEFAULT 'new',
  `brand` varchar(100) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `weight_grams` int(10) UNSIGNED DEFAULT NULL,
  `length_cm` decimal(8,2) DEFAULT NULL,
  `width_cm` decimal(8,2) DEFAULT NULL,
  `height_cm` decimal(8,2) DEFAULT NULL,
  `total_stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_sold` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_likes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('draft','active','inactive','banned') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `shop_id`, `category_id`, `name`, `slug`, `description`, `base_price`, `discount_price`, `condition_type`, `brand`, `sku`, `weight_grams`, `length_cm`, `width_cm`, `height_cm`, `total_stock`, `total_sold`, `rating`, `total_reviews`, `total_views`, `total_likes`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 'เซรั่มวิตามินซี Skinsation 4 ชิ้น', 'samsung-galaxy-s24-ultra-256', 'เซรั่มวิตามินซีเข้มข้น ช่วยให้ผิวกระจ่างใส ลดจุดด่างดำ 4 ขวดในเซ็ตเดียว', 45900.00, 42900.00, 'new', 'Skinsation', 'SAM-S24U-256', 232, NULL, NULL, NULL, 50, 7, 4.60, 5, 4638, 0, 'active', 1, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(2, 1, 7, 'ดัมเบลน้ำหนักปรับได้ 2-24kg', 'macbook-air-m3-13', 'ดัมเบลปรับน้ำหนักได้ 12 ระดับ เหมาะสำหรับออกกำลังกายที่บ้าน', 45990.00, NULL, 'new', 'FitnessPro', 'MBP-M3-13', 1240, NULL, NULL, NULL, 30, 0, 0.00, 0, 3233, 0, 'active', 1, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(3, 1, 8, 'หม้อทอดไร้น้ำมัน 5 ลิตร', 'sony-wh1000xm5', 'หม้อทอดไร้น้ำมันดิจิตอล ควบคุมอุณหภูมิได้ 80-200°C จอสัมผัส LCD', 12990.00, 10990.00, 'new', 'AirFryer', 'SNY-XM5-BLK', 250, NULL, NULL, NULL, 80, 1, 5.00, 1, 2152, 0, 'active', 0, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(4, 2, 10, 'เสื้อยืดคอกลมแพ็ค 3 ตัว', 'oversized-pastel-hoodie', 'เสื้อยืดคอกลมคุณภาพดี ผ้านุ่มใส่สบาย ระบายอากาศได้ดี', 590.00, 490.00, 'new', 'BasicWear', 'HSW-OVS-001', 400, NULL, NULL, NULL, 200, 5, 4.20, 5, 962, 0, 'active', 1, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(5, 2, 11, 'ครีมกันแดด SPF50+ PA++++', 'white-chunky-sneakers', 'ครีมกันแดดเนื้อบางเบา ไม่เหนียวเหนอะหนะ กันน้ำกันเหงื่อ', 890.00, NULL, 'new', 'SunShield', 'SNK-CHP-WHT', 600, NULL, NULL, NULL, 150, 0, 0.00, 0, 3254, 0, 'active', 0, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(6, 3, 13, 'หูฟังไร้สาย Bluetooth 5.3', 'ceramic-nonstick-frypan-28', 'หูฟังบลูทูธเสียงคมชัด แบตอึด 30 ชั่วโมง กันน้ำ IPX5', 699.00, 599.00, 'new', 'SoundMax', 'TFL-PAN-28', 850, NULL, NULL, NULL, 120, 6, 4.67, 3, 3285, 0, 'active', 0, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(7, 3, 14, 'น้ำหอมกลิ่นกลางคืน 100ml', 'cotton-bed-sheet-set', 'น้ำหอมหรูหรา กลิ่นติดทนนาน 8 ชั่วโมง สำหรับงานกลางคืน', 890.00, 750.00, 'new', 'NightScent', 'BED-CTN-KG', 1200, NULL, NULL, NULL, 60, 0, 0.00, 0, 1564, 0, 'active', 0, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(8, 1, 6, 'แปรงสีฟันไฟฟ้า โหมด Whitening', 'iphone-15-pro-max-256', 'แปรงสีฟันไฟฟ้า 5 โหมด หัวแปรงเปลี่ยนได้ ตั้งเวลา 2 นาที', 49900.00, 46900.00, 'new', 'CleanTeeth', 'AAPL-IP15PM-256', 221, NULL, NULL, NULL, 25, 0, 0.00, 0, 2865, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(9, 1, 6, 'ผ้าปูที่นอนเซ็ต 6 ฟุต 6 ชิ้น', 'oppo-find-x7-ultra', 'ผ้าปูที่นอนผ้าฝ้าย 100% นุ่มสบาย ระบายอากาศ ไม่ร้อน', 32990.00, 29990.00, 'new', 'SoftSleep', 'OPP-FX7U', 226, NULL, NULL, NULL, 40, 0, 0.00, 0, 4532, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(10, 1, 7, 'กระเป๋าเป้สะพายหลังกันน้ำ', 'asus-rog-zephyrus-g16', 'กระเป๋าเป้กันน้ำ มีช่องใส่โน๊ตบุ๊ค 15.6 นิ้ว หลายช่องซิป', 89900.00, 84900.00, 'new', 'TravelBag', 'ASU-ROG-G16', 2200, NULL, NULL, NULL, 15, 0, 0.00, 0, 3968, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(11, 1, 7, 'โทรศัพท์ Samsung Galaxy S24 Ultra', 'lenovo-thinkpad-x1', 'สมาร์ทโฟนเรือธง จอ 6.8 นิ้ว กล้อง 200MP แบต 5000mAh', 52900.00, NULL, 'new', 'Samsung', 'LNV-X1C-11', 1120, NULL, NULL, NULL, 20, 0, 0.00, 0, 1143, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(12, 1, 8, 'โน๊ตบุ๊ค MacBook Air M3 13 นิ้ว', 'airpods-pro-2nd', 'โน๊ตบุ๊คบางเบา ชิป M3 แบตอึด 18 ชั่วโมง จอ Retina', 9490.00, 8490.00, 'new', 'Apple', 'AAPL-ADP2', 56, NULL, NULL, NULL, 60, 0, 0.00, 0, 3711, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(13, 1, 8, 'หูฟัง Sony WH-1000XM5', 'jbl-flip-6', 'หูฟังตัดเสียงรบกวนระดับพรีเมียม แบต 30 ชั่วโมง สบายหู', 3490.00, 2990.00, 'new', 'Sony', 'JBL-FLIP6', 550, NULL, NULL, NULL, 90, 0, 0.00, 0, 5027, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(14, 1, 6, 'เสื้อฮู้ดโอเวอร์ไซส์', 'samsung-tab-s9', 'เสื้อฮู้ดทรงหลวม ผ้าหนานุ่ม มีหลายสีให้เลือก', 25900.00, 23900.00, 'new', 'StreetWear', 'SAM-TS9-256', 498, NULL, NULL, NULL, 35, 0, 0.00, 0, 3902, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(15, 2, 9, 'รองเท้าผ้าใบหนาพื้นสูง', 'cargo-baggy-cotton', 'รองเท้าผ้าใบแฟชั่น ใส่สบาย สูง 5 ซม. มีสีขาวดำ', 790.00, 690.00, 'new', 'SneakerMax', 'CGO-BGY-001', 450, NULL, NULL, NULL, 150, 0, 0.00, 0, 4332, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(16, 2, 10, 'กระทะเซรามิก 28 ซม.', 'set-2pcs-summer', 'กระทะเคลือบเซรามิก ไม่ติด ทำความสะอาดง่าย ก้นแบน', 890.00, 750.00, 'new', 'KitchenPro', 'SET-2P-LIN', 350, NULL, NULL, NULL, 120, 0, 0.00, 0, 4853, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(17, 2, 11, 'ชุดเครื่องนอนฝ้าย', 'chelsea-boot-leather', 'ชุดผ้าปูที่นอนคุณภาพดี 6 ชิ้น ลายพื้นเรียบ', 1590.00, NULL, 'new', 'SleepWell', 'CLB-LTH-BLK', 900, NULL, NULL, NULL, 80, 0, 0.00, 0, 1170, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(18, 2, 10, 'iPhone 15 Pro Max 256GB', 'dress-lace-vintage', 'ไอโฟนรุ่นท็อป จอไทเทเนียม กล้อง 48MP USB-C', 1290.00, 990.00, 'new', 'Apple', 'DRS-LCE-VTG', 380, NULL, NULL, NULL, 60, 0, 0.00, 0, 1190, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(19, 2, 11, 'OPPO Find X7 Ultra', 'slip-on-canvas-shoes', 'มือถือกล้องเทพ ซูม 100x ชาร์จไว 100W', 490.00, NULL, 'new', 'OPPO', 'SLP-CVS-001', 450, NULL, NULL, NULL, 200, 0, 0.00, 0, 2340, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(20, 3, 12, 'โน๊ตบุ๊คเกมมิ่ง ASUS ROG', 'desk-rubberwood-120', 'โน๊ตบุ๊คเล่นเกม RTX 4060 จอ 165Hz RGB', 4500.00, 3990.00, 'new', 'ASUS', 'DSK-RBW-120', 15000, NULL, NULL, NULL, 20, 0, 0.00, 0, 3033, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(21, 3, 13, 'เซรั่มวิตามินซี Skinsation 4 ชิ้น', 'philips-blender-hr2221', 'เซรั่มวิตามินซีเข้มข้น ช่วยให้ผิวกระจ่างใส ลดจุดด่างดำ 4 ขวดในเซ็ตเดียว', 1290.00, 990.00, 'new', 'Skinsation', 'PHL-BLD-7W', 1200, NULL, NULL, NULL, 60, 0, 0.00, 0, 3046, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(22, 3, 14, 'ดัมเบลน้ำหนักปรับได้ 2-24kg', 'tencel-bedset-6ft', 'ดัมเบลปรับน้ำหนักได้ 12 ระดับ เหมาะสำหรับออกกำลังกายที่บ้าน', 1890.00, 1590.00, 'new', 'FitnessPro', 'BED-TNL-6F', 1500, NULL, NULL, NULL, 40, 0, 0.00, 0, 1029, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(23, 3, 12, 'หม้อทอดไร้น้ำมัน 5 ลิตร', 'ergonomic-chair-mesh', 'หม้อทอดไร้น้ำมันดิจิตอล ควบคุมอุณหภูมิได้ 80-200°C จอสัมผัส LCD', 4900.00, 4200.00, 'new', 'AirFryer', 'CHR-ERG-MSH', 12000, NULL, NULL, NULL, 25, 0, 0.00, 0, 908, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(24, 4, 5, 'เสื้อยืดคอกลมแพ็ค 3 ตัว', 'serum-vitc-skinsation', 'เสื้อยืดคอกลมคุณภาพดี ผ้านุ่มใส่สบาย ระบายอากาศได้ดี', 890.00, 750.00, 'new', 'BasicWear', 'SKS-VTC-30', 80, NULL, NULL, NULL, 200, 4, 4.50, 4, 1354, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(25, 4, 5, 'ครีมกันแดด SPF50+ PA++++', 'sunscreen-spf50-pa4', 'ครีมกันแดดเนื้อบางเบา ไม่เหนียวเหนอะหนะ กันน้ำกันเหงื่อ', 490.00, NULL, 'new', 'SunShield', 'SPY-SPF50', 100, NULL, NULL, NULL, 300, 0, 0.00, 0, 3947, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(26, 4, 5, 'หูฟังไร้สาย Bluetooth 5.3', '3ce-lipstick-mini-set', 'หูฟังบลูทูธเสียงคมชัด แบตอึด 30 ชั่วโมง กันน้ำ IPX5', 1290.00, 990.00, 'new', 'SoundMax', '3CE-LPS-SET', 200, NULL, NULL, NULL, 150, 0, 0.00, 0, 574, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(27, 4, 5, 'น้ำหอมกลิ่นกลางคืน 100ml', 'collagen-mask-10pcs', 'น้ำหอมหรูหรา กลิ่นติดทนนาน 8 ชั่วโมง สำหรับงานกลางคืน', 350.00, 280.00, 'new', 'NightScent', 'MSK-COL-10', 300, NULL, NULL, NULL, 500, 0, 0.00, 0, 928, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(28, 5, 4, 'แปรงสีฟันไฟฟ้า โหมด Whitening', 'adjustable-dumbbell-24', 'แปรงสีฟันไฟฟ้า 5 โหมด หัวแปรงเปลี่ยนได้ ตั้งเวลา 2 นาที', 3900.00, 3490.00, 'new', 'CleanTeeth', 'DBL-ADJ-24', 24000, NULL, NULL, NULL, 30, 4, 5.00, 1, 2820, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(29, 5, 4, 'ผ้าปูที่นอนเซ็ต 6 ฟุต 6 ชิ้น', 'dryfit-sport-shirt', 'ผ้าปูที่นอนผ้าฝ้าย 100% นุ่มสบาย ระบายอากาศ ไม่ร้อน', 390.00, NULL, 'new', 'SoftSleep', 'SPT-DRY-001', 200, NULL, NULL, NULL, 250, 0, 0.00, 0, 1217, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(30, 5, 4, 'กระเป๋าเป้สะพายหลังกันน้ำ', 'yoga-mat-8mm-tpe', 'กระเป๋าเป้กันน้ำ มีช่องใส่โน๊ตบุ๊ค 15.6 นิ้ว หลายช่องซิป', 690.00, 590.00, 'new', 'TravelBag', 'YGA-MAT-8M', 1800, NULL, NULL, NULL, 120, 0, 0.00, 0, 2525, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(31, 5, 4, 'โทรศัพท์ Samsung Galaxy S24 Ultra', 'garmin-forerunner-265', 'สมาร์ทโฟนเรือธง จอ 6.8 นิ้ว กล้อง 200MP แบต 5000mAh', 14900.00, 13500.00, 'new', 'Samsung', 'GRM-FR265-BLK', 47, NULL, NULL, NULL, 40, 0, 0.00, 0, 3876, 0, 'active', 1, '2026-04-07 17:17:58', '2026-04-07 19:18:44'),
(32, 5, 4, 'โน๊ตบุ๊ค MacBook Air M3 13 นิ้ว', 'treadmill-foldable-3hp', 'โน๊ตบุ๊คบางเบา ชิป M3 แบตอึด 18 ชั่วโมง จอ Retina', 15900.00, 13900.00, 'new', 'Apple', 'TRD-FLD-3HP', 45000, NULL, NULL, NULL, 8, 0, 0.00, 0, 1702, 0, 'active', 0, '2026-04-07 17:17:58', '2026-04-07 19:18:44');

-- --------------------------------------------------------

--
-- Table structure for table `product_answers`
--

CREATE TABLE `product_answers` (
  `answer_id` int(10) UNSIGNED NOT NULL,
  `question_id` int(10) UNSIGNED NOT NULL,
  `answerer_id` int(10) UNSIGNED NOT NULL,
  `answerer_type` enum('seller','admin','user') NOT NULL DEFAULT 'seller',
  `answer` text NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_answers`
--

INSERT INTO `product_answers` (`answer_id`, `question_id`, `answerer_id`, `answerer_type`, `answer`, `is_verified`, `created_at`) VALUES
(1, 1, 3, 'seller', 'รองรับ 5G ครบทุก Band ที่ใช้ในไทย รวมทั้ง Sub-6GHz และ mmWave ด้วย', 1, '2026-04-07 17:17:47'),
(2, 2, 3, 'seller', 'หน้าจอ 6.8 นิ้ว ไม่พับได้ แต่มาพร้อมกระจกกันรอย Gorilla Glass น้ำหนัก 232 กรัม', 1, '2026-04-07 17:17:47'),
(3, 3, 3, 'seller', 'เหมาะมากสำหรับนักศึกษา มี RAM มาให้พร้อม รองรับ Software นักศึกษาได้สบาย แบตอยู่ได้ทั้งวัน', 1, '2026-04-07 17:17:47'),
(4, 4, 3, 'seller', 'Ó©ÖÓ╣ëÓ©│Ó©½Ó©ÖÓ©▒Ó©üÓ╣ÇÓ©ÜÓ©▓Ó©íÓ©▓Ó©üÓ©äÓ╣êÓ©░ Ó╣üÓ©äÓ╣ê 250 Ó©üÓ©úÓ©▒Ó©í Ó╣âÓ©¬Ó╣êÓ©ÖÓ©▓Ó©ÖÓ╣å Ó╣äÓ©íÓ╣êÓ╣ÇÓ©íÓ©ÀÓ╣êÓ©¡Ó©ó', 1, '2026-04-07 17:17:47'),
(5, 5, 4, 'seller', 'Ó╣äÓ©ïÓ©¬Ó╣î M Ó©äÓ©ºÓ©▓Ó©íÓ©¬Ó©╣Ó©ç 165-175 Ó╣ÇÓ©½Ó©íÓ©▓Ó©░Ó©íÓ©▓Ó©üÓ╣ÇÓ©ÑÓ©óÓ©äÓ©úÓ©▒Ó©Ü Ó╣üÓ©òÓ╣êÓ©ûÓ╣ëÓ©▓Ó©èÓ©¡Ó©Ü Oversized Ó©íÓ©▓Ó©üÓ╣å Ó©éÓ©ÂÓ╣ëÓ©Ö L Ó╣äÓ©öÓ╣ëÓ╣ÇÓ©ÑÓ©ó', 1, '2026-04-07 17:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `product_bans`
--

CREATE TABLE `product_bans` (
  `ban_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `banned_by` int(10) UNSIGNED NOT NULL,
  `reason` text NOT NULL,
  `ban_category` enum('counterfeit','prohibited','spam','fraud','copyright','other') NOT NULL DEFAULT 'other',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `unbanned_by` int(10) UNSIGNED DEFAULT NULL,
  `unban_reason` varchar(255) DEFAULT NULL,
  `unbanned_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_bans`
--

INSERT INTO `product_bans` (`ban_id`, `product_id`, `banned_by`, `reason`, `ban_category`, `is_active`, `unbanned_by`, `unban_reason`, `unbanned_at`, `created_at`) VALUES
(1, 5, 11, 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©ÑÓ©¡Ó©üÓ╣ÇÓ©ÑÓ©ÁÓ©óÓ©ÖÓ╣üÓ©ÜÓ©ÜÓ©òÓ©úÓ©▓Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓', 'counterfeit', 0, NULL, NULL, NULL, '2026-04-07 17:17:46');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_url`, `alt_text`, `sort_order`, `is_primary`, `created_at`) VALUES
(104, 1, '/webshop/uploads/products/product_1.jpg', NULL, 0, 1, '2026-04-07 19:33:31'),
(105, 1, '/webshop/uploads/products/product_1.jpg', NULL, 1, 0, '2026-04-07 19:33:31'),
(106, 1, '/webshop/uploads/products/product_1.jpg', NULL, 2, 0, '2026-04-07 19:33:31'),
(107, 2, '/webshop/uploads/products/product_2.jpg', NULL, 0, 1, '2026-04-07 19:33:32'),
(108, 2, '/webshop/uploads/products/product_2.jpg', NULL, 1, 0, '2026-04-07 19:33:32'),
(109, 2, '/webshop/uploads/products/product_2.jpg', NULL, 2, 0, '2026-04-07 19:33:32'),
(110, 3, '/webshop/uploads/products/product_3.jpg', NULL, 0, 1, '2026-04-07 19:33:34'),
(111, 3, '/webshop/uploads/products/product_3.jpg', NULL, 1, 0, '2026-04-07 19:33:34'),
(112, 3, '/webshop/uploads/products/product_3.jpg', NULL, 2, 0, '2026-04-07 19:33:34'),
(113, 4, '/webshop/uploads/products/product_4.jpg', NULL, 0, 1, '2026-04-07 19:33:36'),
(114, 4, '/webshop/uploads/products/product_4.jpg', NULL, 1, 0, '2026-04-07 19:33:36'),
(115, 4, '/webshop/uploads/products/product_4.jpg', NULL, 2, 0, '2026-04-07 19:33:36'),
(116, 5, '/webshop/uploads/products/product_5.jpg', NULL, 0, 1, '2026-04-07 19:33:43'),
(117, 5, '/webshop/uploads/products/product_5.jpg', NULL, 1, 0, '2026-04-07 19:33:43'),
(118, 5, '/webshop/uploads/products/product_5.jpg', NULL, 2, 0, '2026-04-07 19:33:43'),
(119, 6, '/webshop/uploads/products/product_6.jpg', NULL, 0, 1, '2026-04-07 19:33:45'),
(120, 6, '/webshop/uploads/products/product_6.jpg', NULL, 1, 0, '2026-04-07 19:33:45'),
(121, 6, '/webshop/uploads/products/product_6.jpg', NULL, 2, 0, '2026-04-07 19:33:45'),
(122, 7, '/webshop/uploads/products/product_7.jpg', NULL, 0, 1, '2026-04-07 19:33:47'),
(123, 7, '/webshop/uploads/products/product_7.jpg', NULL, 1, 0, '2026-04-07 19:33:47'),
(124, 7, '/webshop/uploads/products/product_7.jpg', NULL, 2, 0, '2026-04-07 19:33:47'),
(125, 8, '/webshop/uploads/products/product_8.jpg', NULL, 0, 1, '2026-04-07 19:33:48'),
(126, 8, '/webshop/uploads/products/product_8.jpg', NULL, 1, 0, '2026-04-07 19:33:48'),
(127, 8, '/webshop/uploads/products/product_8.jpg', NULL, 2, 0, '2026-04-07 19:33:48'),
(128, 9, '/webshop/uploads/products/product_9.jpg', NULL, 0, 1, '2026-04-07 19:33:50'),
(129, 9, '/webshop/uploads/products/product_9.jpg', NULL, 1, 0, '2026-04-07 19:33:50'),
(130, 9, '/webshop/uploads/products/product_9.jpg', NULL, 2, 0, '2026-04-07 19:33:50'),
(131, 10, '/webshop/uploads/products/product_10.jpg', NULL, 0, 1, '2026-04-07 19:33:52'),
(132, 10, '/webshop/uploads/products/product_10.jpg', NULL, 1, 0, '2026-04-07 19:33:52'),
(133, 10, '/webshop/uploads/products/product_10.jpg', NULL, 2, 0, '2026-04-07 19:33:52'),
(134, 11, '/webshop/uploads/products/product_11.jpg', NULL, 0, 1, '2026-04-07 19:33:53'),
(135, 11, '/webshop/uploads/products/product_11.jpg', NULL, 1, 0, '2026-04-07 19:33:53'),
(136, 11, '/webshop/uploads/products/product_11.jpg', NULL, 2, 0, '2026-04-07 19:33:53'),
(137, 12, '/webshop/uploads/products/product_12.jpg', NULL, 0, 1, '2026-04-07 19:33:55'),
(138, 12, '/webshop/uploads/products/product_12.jpg', NULL, 1, 0, '2026-04-07 19:33:55'),
(139, 12, '/webshop/uploads/products/product_12.jpg', NULL, 2, 0, '2026-04-07 19:33:55'),
(140, 13, '/webshop/uploads/products/product_13.jpg', NULL, 0, 1, '2026-04-07 19:33:57'),
(141, 13, '/webshop/uploads/products/product_13.jpg', NULL, 1, 0, '2026-04-07 19:33:57'),
(142, 13, '/webshop/uploads/products/product_13.jpg', NULL, 2, 0, '2026-04-07 19:33:57'),
(143, 14, '/webshop/uploads/products/product_14.jpg', NULL, 0, 1, '2026-04-07 19:33:58'),
(144, 14, '/webshop/uploads/products/product_14.jpg', NULL, 1, 0, '2026-04-07 19:33:58'),
(145, 14, '/webshop/uploads/products/product_14.jpg', NULL, 2, 0, '2026-04-07 19:33:58'),
(146, 15, '/webshop/uploads/products/product_15.jpg', NULL, 0, 1, '2026-04-07 19:34:00'),
(147, 15, '/webshop/uploads/products/product_15.jpg', NULL, 1, 0, '2026-04-07 19:34:00'),
(148, 15, '/webshop/uploads/products/product_15.jpg', NULL, 2, 0, '2026-04-07 19:34:00'),
(149, 16, '/webshop/uploads/products/product_16.jpg', NULL, 0, 1, '2026-04-07 19:34:01'),
(150, 16, '/webshop/uploads/products/product_16.jpg', NULL, 1, 0, '2026-04-07 19:34:01'),
(151, 16, '/webshop/uploads/products/product_16.jpg', NULL, 2, 0, '2026-04-07 19:34:01'),
(152, 17, '/webshop/uploads/products/product_17.jpg', NULL, 0, 1, '2026-04-07 19:34:03'),
(153, 17, '/webshop/uploads/products/product_17.jpg', NULL, 1, 0, '2026-04-07 19:34:03'),
(154, 17, '/webshop/uploads/products/product_17.jpg', NULL, 2, 0, '2026-04-07 19:34:03'),
(155, 18, '/webshop/uploads/products/product_18.jpg', NULL, 0, 1, '2026-04-07 19:34:05'),
(156, 18, '/webshop/uploads/products/product_18.jpg', NULL, 1, 0, '2026-04-07 19:34:05'),
(157, 18, '/webshop/uploads/products/product_18.jpg', NULL, 2, 0, '2026-04-07 19:34:05'),
(158, 19, '/webshop/uploads/products/product_19.jpg', NULL, 0, 1, '2026-04-07 19:34:06'),
(159, 19, '/webshop/uploads/products/product_19.jpg', NULL, 1, 0, '2026-04-07 19:34:06'),
(160, 19, '/webshop/uploads/products/product_19.jpg', NULL, 2, 0, '2026-04-07 19:34:06'),
(161, 20, '/webshop/uploads/products/product_20.jpg', NULL, 0, 1, '2026-04-07 19:34:08'),
(162, 20, '/webshop/uploads/products/product_20.jpg', NULL, 1, 0, '2026-04-07 19:34:08'),
(163, 20, '/webshop/uploads/products/product_20.jpg', NULL, 2, 0, '2026-04-07 19:34:08'),
(164, 21, '/webshop/uploads/products/product_21.jpg', NULL, 0, 1, '2026-04-07 19:34:10'),
(165, 21, '/webshop/uploads/products/product_21.jpg', NULL, 1, 0, '2026-04-07 19:34:10'),
(166, 21, '/webshop/uploads/products/product_21.jpg', NULL, 2, 0, '2026-04-07 19:34:10'),
(167, 22, '/webshop/uploads/products/product_22.jpg', NULL, 0, 1, '2026-04-07 19:34:11'),
(168, 22, '/webshop/uploads/products/product_22.jpg', NULL, 1, 0, '2026-04-07 19:34:11'),
(169, 22, '/webshop/uploads/products/product_22.jpg', NULL, 2, 0, '2026-04-07 19:34:11'),
(170, 23, '/webshop/uploads/products/product_23.jpg', NULL, 0, 1, '2026-04-07 19:34:13'),
(171, 23, '/webshop/uploads/products/product_23.jpg', NULL, 1, 0, '2026-04-07 19:34:13'),
(172, 23, '/webshop/uploads/products/product_23.jpg', NULL, 2, 0, '2026-04-07 19:34:13'),
(173, 24, '/webshop/uploads/products/product_24.jpg', NULL, 0, 1, '2026-04-07 19:34:15'),
(174, 24, '/webshop/uploads/products/product_24.jpg', NULL, 1, 0, '2026-04-07 19:34:15'),
(175, 24, '/webshop/uploads/products/product_24.jpg', NULL, 2, 0, '2026-04-07 19:34:15'),
(176, 25, '/webshop/uploads/products/product_25.jpg', NULL, 0, 1, '2026-04-07 19:34:16'),
(177, 25, '/webshop/uploads/products/product_25.jpg', NULL, 1, 0, '2026-04-07 19:34:16'),
(178, 25, '/webshop/uploads/products/product_25.jpg', NULL, 2, 0, '2026-04-07 19:34:16'),
(179, 26, '/webshop/uploads/products/product_26.jpg', NULL, 0, 1, '2026-04-07 19:34:18'),
(180, 26, '/webshop/uploads/products/product_26.jpg', NULL, 1, 0, '2026-04-07 19:34:18'),
(181, 26, '/webshop/uploads/products/product_26.jpg', NULL, 2, 0, '2026-04-07 19:34:18'),
(182, 27, '/webshop/uploads/products/product_27.jpg', NULL, 0, 1, '2026-04-07 19:34:20'),
(183, 27, '/webshop/uploads/products/product_27.jpg', NULL, 1, 0, '2026-04-07 19:34:20'),
(184, 27, '/webshop/uploads/products/product_27.jpg', NULL, 2, 0, '2026-04-07 19:34:20'),
(185, 28, '/webshop/uploads/products/product_28.jpg', NULL, 0, 1, '2026-04-07 19:34:21'),
(186, 28, '/webshop/uploads/products/product_28.jpg', NULL, 1, 0, '2026-04-07 19:34:21'),
(187, 28, '/webshop/uploads/products/product_28.jpg', NULL, 2, 0, '2026-04-07 19:34:21'),
(188, 29, '/webshop/uploads/products/product_29.jpg', NULL, 0, 1, '2026-04-07 19:34:23'),
(189, 29, '/webshop/uploads/products/product_29.jpg', NULL, 1, 0, '2026-04-07 19:34:23'),
(190, 29, '/webshop/uploads/products/product_29.jpg', NULL, 2, 0, '2026-04-07 19:34:23'),
(191, 30, '/webshop/uploads/products/product_30.jpg', NULL, 0, 1, '2026-04-07 19:34:25'),
(192, 30, '/webshop/uploads/products/product_30.jpg', NULL, 1, 0, '2026-04-07 19:34:25'),
(193, 30, '/webshop/uploads/products/product_30.jpg', NULL, 2, 0, '2026-04-07 19:34:25'),
(194, 31, '/webshop/uploads/products/product_31.jpg', NULL, 0, 1, '2026-04-07 19:34:26'),
(195, 31, '/webshop/uploads/products/product_31.jpg', NULL, 1, 0, '2026-04-07 19:34:26'),
(196, 31, '/webshop/uploads/products/product_31.jpg', NULL, 2, 0, '2026-04-07 19:34:26'),
(197, 32, '/webshop/uploads/products/product_32.jpg', NULL, 0, 1, '2026-04-07 19:34:28'),
(198, 32, '/webshop/uploads/products/product_32.jpg', NULL, 1, 0, '2026-04-07 19:34:28'),
(199, 32, '/webshop/uploads/products/product_32.jpg', NULL, 2, 0, '2026-04-07 19:34:28');

-- --------------------------------------------------------

--
-- Table structure for table `product_questions`
--

CREATE TABLE `product_questions` (
  `question_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','answered','hidden') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_questions`
--

INSERT INTO `product_questions` (`question_id`, `product_id`, `user_id`, `question`, `is_anonymous`, `status`, `created_at`) VALUES
(1, 1, 7, 'Samsung S24 Ultra รองรับเครือข่าย 5G หรือไม่?', 0, 'answered', '2026-04-07 17:17:47'),
(2, 1, 8, 'หน้าจอ 6.8 นิ้ว พับได้หรือมีกระจกกันรอยไหม?', 0, 'answered', '2026-04-07 17:17:47'),
(3, 2, 9, 'MacBook Air M3 ใช้งานนักศึกษาได้ไหม?', 0, 'answered', '2026-04-07 17:17:47'),
(4, 3, 6, 'Sony XM5 Ó╣âÓ©¬Ó╣êÓ╣üÓ©ÑÓ╣ëÓ©ºÓ©½Ó©ÖÓ©▒Ó©üÓ©íÓ©▒Ó╣ëÓ©óÓ©äÓ©░?', 0, 'answered', '2026-04-07 17:17:47'),
(5, 4, 9, 'Hoodie Ó╣äÓ©ïÓ©¬Ó╣î M Ó©¬Ó©╣Ó©ç 175 Ó╣âÓ©¬Ó╣êÓ╣äÓ©öÓ╣ëÓ©íÓ©▒Ó╣ëÓ©óÓ©äÓ©úÓ©▒Ó©Ü?', 0, 'answered', '2026-04-07 17:17:47'),
(6, 6, 7, 'Ó©üÓ©úÓ©░Ó©ùÓ©░Ó╣âÓ©èÓ╣ëÓ©üÓ©▒Ó©ÜÓ╣ÇÓ©òÓ©▓Ó╣üÓ©íÓ╣êÓ╣ÇÓ©½Ó©ÑÓ╣çÓ©üÓ╣äÓ©ƒÓ©ƒÓ╣ëÓ©▓Ó╣äÓ©öÓ╣ëÓ©íÓ©▒Ó╣ëÓ©óÓ©äÓ©úÓ©▒Ó©Ü?', 0, 'pending', '2026-04-07 17:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `product_reports`
--

CREATE TABLE `product_reports` (
  `report_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `reporter_id` int(10) UNSIGNED NOT NULL,
  `reason` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','reviewed','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_skus`
--

CREATE TABLE `product_skus` (
  `sku_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `sku_code` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `discount_price` decimal(12,2) DEFAULT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sold` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `weight_grams` int(10) UNSIGNED DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_skus`
--

INSERT INTO `product_skus` (`sku_id`, `product_id`, `sku_code`, `price`, `discount_price`, `stock`, `sold`, `weight_grams`, `image_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'SAM-S24U-256-BLK', 45900.00, 42900.00, 20, 5, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(2, 1, 'SAM-S24U-256-GRY', 45900.00, 42900.00, 15, 3, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(3, 1, 'SAM-S24U-512-BLK', 50900.00, 47900.00, 10, 2, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(4, 2, 'MBP-M3-13-SLV', 45990.00, NULL, 30, 8, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(5, 3, 'SNY-XM5-BLK', 12990.00, 10990.00, 50, 12, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(6, 3, 'SNY-XM5-SLV', 12990.00, 10990.00, 30, 5, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(7, 4, 'HSW-OVS-S-PNK', 590.00, 490.00, 30, 10, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(8, 4, 'HSW-OVS-M-PNK', 590.00, 490.00, 40, 15, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(9, 4, 'HSW-OVS-L-BGE', 590.00, 490.00, 30, 8, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(10, 5, 'SNK-CHP-38', 890.00, NULL, 25, 4, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(11, 5, 'SNK-CHP-39', 890.00, NULL, 30, 6, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(12, 6, 'TFL-PAN-28', 699.00, 599.00, 120, 30, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(13, 7, 'BED-CTN-KG', 890.00, 750.00, 60, 18, NULL, NULL, 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `product_specifications`
--

CREATE TABLE `product_specifications` (
  `spec_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `spec_key` varchar(150) NOT NULL,
  `spec_value` varchar(500) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_specifications`
--

INSERT INTO `product_specifications` (`spec_id`, `product_id`, `spec_key`, `spec_value`, `sort_order`) VALUES
(1, 8, 'แบรนด์', 'Apple', 1),
(2, 9, 'แบรนด์', 'OPPO', 1),
(3, 10, 'แบรนด์', 'ASUS', 1),
(4, 11, 'แบรนด์', 'Lenovo', 1),
(5, 12, 'แบรนด์', 'Apple', 1),
(6, 13, 'แบรนด์', 'JBL', 1),
(7, 14, 'แบรนด์', 'Samsung', 1),
(8, 15, 'แบรนด์', 'No Brand', 1),
(9, 16, 'แบรนด์', 'No Brand', 1),
(10, 17, 'แบรนด์', 'No Brand', 1),
(11, 18, 'แบรนด์', 'No Brand', 1),
(12, 19, 'แบรนด์', 'No Brand', 1),
(13, 20, 'แบรนด์', 'No Brand', 1),
(14, 21, 'แบรนด์', 'Philips', 1),
(15, 22, 'แบรนด์', 'No Brand', 1),
(16, 23, 'แบรนด์', 'No Brand', 1),
(17, 24, 'แบรนด์', 'Skinsation', 1),
(18, 25, 'แบรนด์', 'Sunplay', 1),
(19, 26, 'แบรนด์', '3CE', 1),
(20, 27, 'แบรนด์', 'No Brand', 1),
(21, 28, 'แบรนด์', 'No Brand', 1),
(22, 29, 'แบรนด์', 'No Brand', 1),
(23, 30, 'แบรนด์', 'No Brand', 1),
(24, 31, 'แบรนด์', 'Garmin', 1),
(25, 32, 'แบรนด์', 'No Brand', 1),
(26, 8, 'ประเภท', '', 2),
(27, 9, 'ประเภท', '', 2),
(28, 10, 'ประเภท', '', 2),
(29, 11, 'ประเภท', '', 2),
(30, 12, 'ประเภท', '', 2),
(31, 13, 'ประเภท', '', 2),
(32, 14, 'ประเภท', '', 2),
(33, 15, 'ประเภท', '', 2),
(34, 16, 'ประเภท', '', 2),
(35, 17, 'ประเภท', '', 2),
(36, 18, 'ประเภท', '', 2),
(37, 19, 'ประเภท', '', 2),
(38, 20, 'ประเภท', '', 2),
(39, 21, 'ประเภท', '', 2),
(40, 22, 'ประเภท', '', 2),
(41, 23, 'ประเภท', '', 2),
(42, 24, 'ประเภท', '', 2),
(43, 25, 'ประเภท', '', 2),
(44, 26, 'ประเภท', '', 2),
(45, 27, 'ประเภท', '', 2),
(46, 28, 'ประเภท', '', 2),
(47, 29, 'ประเภท', '', 2),
(48, 30, 'ประเภท', '', 2),
(49, 31, 'ประเภท', '', 2),
(50, 32, 'ประเภท', '', 2),
(51, 8, 'น้ำหนัก', '221 ', 3),
(52, 9, 'น้ำหนัก', '226 ', 3),
(53, 10, 'น้ำหนัก', '2200 ', 3),
(54, 11, 'น้ำหนัก', '1120 ', 3),
(55, 12, 'น้ำหนัก', '56 ', 3),
(56, 13, 'น้ำหนัก', '550 ', 3),
(57, 14, 'น้ำหนัก', '498 ', 3),
(58, 15, 'น้ำหนัก', '450 ', 3),
(59, 16, 'น้ำหนัก', '350 ', 3),
(60, 17, 'น้ำหนัก', '900 ', 3),
(61, 18, 'น้ำหนัก', '380 ', 3),
(62, 19, 'น้ำหนัก', '450 ', 3),
(63, 20, 'น้ำหนัก', '15000 ', 3),
(64, 21, 'น้ำหนัก', '1200 ', 3),
(65, 22, 'น้ำหนัก', '1500 ', 3),
(66, 23, 'น้ำหนัก', '12000 ', 3),
(67, 24, 'น้ำหนัก', '80 ', 3),
(68, 25, 'น้ำหนัก', '100 ', 3),
(69, 26, 'น้ำหนัก', '200 ', 3),
(70, 27, 'น้ำหนัก', '300 ', 3),
(71, 28, 'น้ำหนัก', '24000 ', 3),
(72, 29, 'น้ำหนัก', '200 ', 3),
(73, 30, 'น้ำหนัก', '1800 ', 3),
(74, 31, 'น้ำหนัก', '47 ', 3),
(75, 32, 'น้ำหนัก', '45000 ', 3);

-- --------------------------------------------------------

--
-- Table structure for table `product_views`
--

CREATE TABLE `product_views` (
  `view_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `viewed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `referral_id` int(10) UNSIGNED NOT NULL,
  `referrer_id` int(10) UNSIGNED NOT NULL,
  `referred_id` int(10) UNSIGNED NOT NULL,
  `referral_code` varchar(20) NOT NULL,
  `reward_amount` decimal(12,2) DEFAULT NULL,
  `reward_given` tinyint(1) NOT NULL DEFAULT 0,
  `rewarded_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_codes`
--

CREATE TABLE `referral_codes` (
  `code_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `total_referred` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_earned` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_requests`
--

CREATE TABLE `return_requests` (
  `return_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `buyer_user_id` int(10) UNSIGNED NOT NULL,
  `reason` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `return_type` enum('return_refund','refund_only') NOT NULL DEFAULT 'return_refund',
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `refund_amount` decimal(12,2) DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `return_requests`
--

INSERT INTO `return_requests` (`return_id`, `order_id`, `buyer_user_id`, `reason`, `description`, `return_type`, `status`, `refund_amount`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 51, 20, 'สินค้าไม่ตรงตามที่สั่ง ผิดสี', 'ลูกค้าได้รับสินค้าผิดสีจากที่สั่ง และต้องการคืนเงิน', 'return_refund', 'pending', NULL, NULL, '2026-04-07 17:21:46', '2026-04-07 20:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `return_request_images`
--

CREATE TABLE `return_request_images` (
  `image_id` int(10) UNSIGNED NOT NULL,
  `return_id` int(10) UNSIGNED NOT NULL,
  `image_url` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `sku_id` int(10) UNSIGNED DEFAULT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `reviewer_id` int(10) UNSIGNED NOT NULL,
  `shop_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `seller_reply` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `sku_id`, `order_id`, `reviewer_id`, `shop_id`, `rating`, `comment`, `is_anonymous`, `seller_reply`, `replied_at`, `is_hidden`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 6, 1, 5, 'ชอบมากค่ะ จะกลับมาซื้ออีกแน่นอน', 0, NULL, NULL, 0, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(2, 4, 7, 2, 7, 2, 4, 'สินค้าดีมากค่ะ ส่งไว แพคของมาดีมาก', 0, NULL, NULL, 0, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(3, 3, 5, 3, 8, 1, 5, 'สินค้าตรงรูป คุณภาพตามที่คาดไว้', 0, NULL, NULL, 0, '2026-04-07 16:36:13', '2026-04-07 19:18:44'),
(4, 1, NULL, 31, 15, 1, 5, 'ร้านนี้ดีจริง ส่งไว ของแท้ แนะนำ', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(5, 4, NULL, 32, 16, 2, 4, 'คุณภาพดีเกินราคา จะสั่งอีกแน่นอนค่ะ', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(6, 6, NULL, 33, 17, 3, 4, 'ได้รับของแล้ว ตรงปก สภาพดี ไม่มีตำหนิ', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(7, 1, NULL, 34, 18, 1, 5, 'ประทับใจมาก สินค้าดี ราคาไม่แพง', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(8, 24, NULL, 35, 19, 4, 4, 'ร้านนี้บริการดี ตอบไว ส่งไว ประทับใจมาก', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(9, 28, NULL, 36, 20, 5, 5, 'ดีมากค่ะ ไม่ผิดหวัง จะสั่งอีก', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(10, 4, NULL, 37, 21, 2, 5, 'ของดีมีคุณภาพ แพคมาอย่างดี ส่งไว', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(11, 1, NULL, 38, 22, 1, 4, 'สินค้าคุณภาพดีมาก แนะนำเลยค่ะ ซื้อซ้ำแน่', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(12, 6, NULL, 39, 23, 3, 5, 'สินค้าดีมากค่ะ ส่งไว แพคของมาดีมาก', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(13, 24, NULL, 40, 24, 4, 5, 'คุณภาพดีเกินราคา จะสั่งอีกแน่นอนค่ะ', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(14, 4, NULL, 48, 17, 2, 4, 'ซื้อมาครั้งที่ 3 แล้ว ชอบมาก ของดีจริง', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(15, 6, NULL, 49, 18, 3, 5, 'ได้รับของแล้ว ตรงปก สภาพดี ไม่มีตำหนิ', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(16, 24, NULL, 50, 19, 4, 5, 'ร้านนี้บริการดี ตอบไว ส่งไว ประทับใจมาก', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(17, 1, NULL, 52, 21, 1, 4, 'ส่งเร็วมาก ของดีจริง ไม่ผิดหวังที่สั่ง', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(18, 4, NULL, 53, 22, 2, 4, 'ราคาถูก คุณภาพดี คุ้มค่าเกินราคา', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44'),
(19, 24, NULL, 55, 24, 4, 4, 'แพคมาดีมาก สินค้าไม่มีตำหนิ ครบถ้วน', 0, NULL, NULL, 0, '2026-04-07 17:21:46', '2026-04-07 19:18:44');

-- --------------------------------------------------------

--
-- Table structure for table `review_images`
--

CREATE TABLE `review_images` (
  `image_id` int(10) UNSIGNED NOT NULL,
  `review_id` int(10) UNSIGNED NOT NULL,
  `image_url` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_likes`
--

CREATE TABLE `review_likes` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `review_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_key` varchar(50) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#6c757d',
  `icon` varchar(50) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_system` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_key`, `role_name`, `description`, `color`, `icon`, `is_default`, `is_system`, `sort_order`, `created_at`) VALUES
(1, 'superadmin', 'Super Administrator', 'ผู้ดูแลระบบสูงสุด มีสิทธิ์เข้าถึงทุกส่วนของระบบ', '#dc3545', 'bi-shield-fill', 0, 1, 10, '2026-04-07 17:17:46'),
(2, 'admin', 'Administrator', 'ผู้ดูแลระบบทั่วไป', '#fd7e14', 'bi-shield-half', 0, 1, 20, '2026-04-07 17:17:46'),
(3, 'content_mod', 'Content Moderator', 'ดูแลเนื้อหา รีวิว และรายงาน', '#6610f2', 'bi-eye-fill', 0, 1, 30, '2026-04-07 17:17:46'),
(4, 'finance', 'Finance Manager', 'ดูแลการเงิน รายงาน และการชำระเงิน', '#198754', 'bi-cash-coin', 0, 1, 40, '2026-04-07 17:17:46'),
(5, 'support', 'Customer Support', 'ดูแล Ticket และช่วยเหลือลูกค้า', '#0d6efd', 'bi-headset', 0, 1, 50, '2026-04-07 17:17:46'),
(6, 'seller', 'Seller', 'ผู้ขายสินค้าในระบบ', '#ff7337', 'bi-shop', 0, 1, 60, '2026-04-07 17:17:46'),
(7, 'buyer', 'Buyer', 'ผู้ซื้อสินค้าทั่วไป', '#6c757d', 'bi-person', 1, 1, 70, '2026-04-07 17:17:46'),
(8, 'vip_buyer', 'VIP Buyer', 'Ó©ÑÓ©╣Ó©üÓ©äÓ╣ëÓ©▓ VIP Ó©óÓ©¡Ó©öÓ©ïÓ©ÀÓ╣ëÓ©¡Ó©¬Ó©╣Ó©ç', '#ffc107', 'bi-star-fill', 0, 0, 80, '2026-04-07 17:17:46'),
(9, 'seller_premium', 'Premium Seller', 'Ó©úÓ╣ëÓ©▓Ó©ÖÓ©äÓ╣ëÓ©▓Ó©×Ó©úÓ©ÁÓ╣ÇÓ©íÓ©ÁÓ©óÓ©í', '#20c997', 'bi-shop-window', 0, 0, 90, '2026-04-07 17:17:46');

-- --------------------------------------------------------

--
-- Table structure for table `search_history`
--

CREATE TABLE `search_history` (
  `search_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `searched_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `search_history`
--

INSERT INTO `search_history` (`search_id`, `user_id`, `keyword`, `searched_at`) VALUES
(1, 6, 'samsung s24', '2026-04-07 16:36:13'),
(2, 6, 'macbook m3', '2026-04-07 16:36:13'),
(3, 7, 'hoodie oversized', '2026-04-07 16:36:13'),
(4, 8, 'headphone', '2026-04-07 16:36:13'),
(5, 8, 'sony xm5', '2026-04-07 16:36:13'),
(6, 9, 'Ó©üÓ©úÓ©░Ó©ùÓ©░ non-stick', '2026-04-07 16:36:13'),
(7, 10, 'Ó©úÓ©¡Ó©çÓ╣ÇÓ©ùÓ╣ëÓ©▓Ó©£Ó╣ëÓ©▓Ó╣âÓ©Ü', '2026-04-07 16:36:13'),
(8, 15, 'Ó©èÓ©©Ó©öÓ©£Ó╣ëÓ©▓Ó©øÓ©╣Ó©ùÓ©ÁÓ╣êÓ©ÖÓ©¡Ó©Ö', '2026-04-07 17:21:46'),
(9, 16, 'Ó©üÓ©úÓ©░Ó©ùÓ©░', '2026-04-07 17:21:46'),
(10, 17, 'Ó©èÓ©©Ó©öÓ©£Ó╣ëÓ©▓Ó©øÓ©╣Ó©ùÓ©ÁÓ╣êÓ©ÖÓ©¡Ó©Ö', '2026-04-07 17:21:46'),
(11, 18, 'cargo pants', '2026-04-07 17:21:46'),
(12, 19, 'Ó©½Ó©╣Ó©ƒÓ©▒Ó©ç', '2026-04-07 17:21:46'),
(13, 20, 'Ó╣ÇÓ©¬Ó©ÀÓ╣ëÓ©¡Ó╣üÓ©ƒÓ©èÓ©▒Ó╣êÓ©Ö', '2026-04-07 17:21:46'),
(14, 21, 'Ó╣ÇÓ©ïÓ©úÓ©▒Ó╣êÓ©íÓ©ºÓ©┤Ó©òÓ©▓Ó©íÓ©┤Ó©ÖÓ©ïÓ©Á', '2026-04-07 17:21:46'),
(15, 22, 'Ó©üÓ©úÓ©░Ó©ùÓ©░', '2026-04-07 17:21:46'),
(16, 23, 'Ó©ÑÓ©┤Ó©øÓ©¬Ó©òÓ©┤Ó©ü', '2026-04-07 17:21:46'),
(17, 24, 'Ó©ÑÓ©┤Ó©øÓ©¬Ó©òÓ©┤Ó©ü', '2026-04-07 17:21:46'),
(18, 25, 'Ó©½Ó©╣Ó©ƒÓ©▒Ó©ç', '2026-04-07 17:21:46'),
(19, 26, 'Ó©½Ó©╣Ó©ƒÓ©▒Ó©ç', '2026-04-07 17:21:46'),
(20, 27, 'Ó©ÑÓ©┤Ó©øÓ©¬Ó©òÓ©┤Ó©ü', '2026-04-07 17:21:46'),
(21, 28, 'Ó©½Ó©╣Ó©ƒÓ©▒Ó©ç', '2026-04-07 17:21:46'),
(22, 29, 'Ó©äÓ©úÓ©ÁÓ©íÓ©üÓ©▒Ó©ÖÓ╣üÓ©öÓ©ö', '2026-04-07 17:21:46'),
(23, 30, 'Ó╣ÇÓ©¬Ó©ÀÓ╣êÓ©¡Ó╣éÓ©óÓ©äÓ©░', '2026-04-07 17:21:46'),
(24, 31, 'cargo pants', '2026-04-07 17:21:46'),
(25, 32, 'Ó©èÓ©©Ó©öÓ©£Ó╣ëÓ©▓Ó©øÓ©╣Ó©ùÓ©ÁÓ╣êÓ©ÖÓ©¡Ó©Ö', '2026-04-07 17:21:46'),
(26, 33, 'Ó╣ÇÓ©¬Ó©ÀÓ╣ëÓ©¡Ó╣üÓ©ƒÓ©èÓ©▒Ó╣êÓ©Ö', '2026-04-07 17:21:46'),
(27, 34, 'Ó©èÓ©©Ó©öÓ©£Ó╣ëÓ©▓Ó©øÓ©╣Ó©ùÓ©ÁÓ╣êÓ©ÖÓ©¡Ó©Ö', '2026-04-07 17:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_providers`
--

CREATE TABLE `shipping_providers` (
  `provider_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `tracking_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_providers`
--

INSERT INTO `shipping_providers` (`provider_id`, `name`, `code`, `logo_url`, `tracking_url`, `is_active`) VALUES
(1, 'Flash Home', 'JNT', NULL, 'https://www.jtexpress.th/track?no={tracking_no}', 1),
(2, 'J&T Express', 'KERRY', NULL, 'https://th.kerryexpress.com/track?no={tracking_no}', 1),
(3, 'Kerry Express', 'FLASH', NULL, 'https://www.flashexpress.co.th/tracking/{tracking_no}', 1),
(4, 'DHL Express', 'THPOST', NULL, 'https://track.thailandpost.co.th/?tracknumber={tracking_no}', 1),
(5, 'Thai Post', 'SPX', NULL, 'https://spx.co.th/track?no={tracking_no}', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_rates`
--

CREATE TABLE `shipping_rates` (
  `rate_id` int(10) UNSIGNED NOT NULL,
  `provider_id` int(10) UNSIGNED NOT NULL,
  `zone_id` int(10) UNSIGNED NOT NULL,
  `min_weight_g` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `max_weight_g` int(10) UNSIGNED DEFAULT NULL,
  `base_rate` decimal(8,2) NOT NULL,
  `rate_per_kg` decimal(8,2) NOT NULL DEFAULT 0.00,
  `min_days` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `max_days` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_rates`
--

INSERT INTO `shipping_rates` (`rate_id`, `provider_id`, `zone_id`, `min_weight_g`, `max_weight_g`, `base_rate`, `rate_per_kg`, `min_days`, `max_days`, `is_active`) VALUES
(1, 1, 1, 0, 500, 30.00, 0.00, 1, 2, 1),
(2, 1, 1, 501, 1000, 35.00, 0.00, 1, 2, 1),
(3, 1, 1, 1001, NULL, 35.00, 10.00, 1, 2, 1),
(4, 1, 2, 0, 500, 35.00, 0.00, 1, 3, 1),
(5, 1, 2, 501, NULL, 35.00, 12.00, 1, 3, 1),
(6, 1, 3, 0, NULL, 40.00, 15.00, 2, 4, 1),
(7, 1, 4, 0, NULL, 40.00, 15.00, 2, 4, 1),
(8, 1, 5, 0, NULL, 45.00, 18.00, 2, 5, 1),
(9, 1, 6, 0, NULL, 40.00, 12.00, 1, 3, 1),
(10, 5, 1, 0, 2000, 25.00, 0.00, 1, 2, 1),
(11, 5, 1, 2001, NULL, 25.00, 8.00, 1, 2, 1),
(12, 5, 2, 0, NULL, 30.00, 10.00, 1, 3, 1),
(13, 5, 3, 0, NULL, 35.00, 12.00, 2, 4, 1),
(14, 5, 4, 0, NULL, 35.00, 12.00, 2, 4, 1),
(15, 5, 5, 0, NULL, 40.00, 15.00, 2, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_zones`
--

CREATE TABLE `shipping_zones` (
  `zone_id` int(10) UNSIGNED NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `provinces` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`provinces`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_zones`
--

INSERT INTO `shipping_zones` (`zone_id`, `zone_name`, `provinces`, `is_active`, `created_at`) VALUES
(1, 'กรุงเทพฯ และปริมณฑล', '[\"กรุงเทพมหานคร\",\"นนทบุรี\",\"ปทุมธานี\",\"สมุทรปราการ\",\"นครปฐม\"]', 1, '2026-04-07 17:17:46'),
(2, 'ภาคเหนือ', '[\"เชียงใหม่\",\"เชียงราย\",\"ลำปาง\",\"ลำพูน\",\"แม่ฮ่องสอน\",\"พะเยา\",\"แพร่\",\"น่าน\"]', 1, '2026-04-07 17:17:46'),
(3, 'ภาคกลาง', '[\"อยุธยา\",\"อ่างทอง\",\"ลพบุรี\",\"สิงห์บุรี\",\"สระบุรี\",\"นครสวรรค์\",\"อุทัยธานี\",\"ชัยนาท\"]', 1, '2026-04-07 17:17:46'),
(4, 'ภาคตะวันออกเฉียงเหนือ', '[\"นครราชสีมา\",\"ขอนแก่น\",\"อุดรธานี\",\"อุบลราชธานี\",\"บึงกาฬ\",\"หนองคาย\",\"เลย\",\"สกลนคร\"]', 1, '2026-04-07 17:17:46'),
(5, 'ภาคตะวันออก', '[\"ชลบุรี\",\"ระยอง\",\"จันทบุรี\",\"ตราด\",\"ฉะเชิงเทรา\",\"สมุทรปราการ\"]', 1, '2026-04-07 17:17:46'),
(6, 'ภาคตะวันตก', '[\"กาญจนบุรี\",\"ราชบุรี\",\"สุพรรณบุรี\",\"เพชรบุรี\",\"ประจวบคีรีขันธ์\"]', 1, '2026-04-07 17:17:46');

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `shop_id` int(10) UNSIGNED NOT NULL,
  `owner_user_id` int(10) UNSIGNED NOT NULL,
  `shop_name` varchar(150) NOT NULL,
  `shop_slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `banner_url` varchar(500) DEFAULT NULL,
  `shop_type` enum('individual','mall','official') NOT NULL DEFAULT 'individual',
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_products` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_sales` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `response_rate` decimal(5,2) DEFAULT NULL,
  `response_time` varchar(50) DEFAULT NULL,
  `joined_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`shop_id`, `owner_user_id`, `shop_name`, `shop_slug`, `description`, `logo_url`, `banner_url`, `shop_type`, `rating`, `total_reviews`, `total_products`, `total_sales`, `response_rate`, `response_time`, `joined_at`, `is_verified`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'ร้านสกินแคร์ถูกและดี', 'techgadget-th', 'จำหน่ายเครื่องสำอางและสกินแคร์ราคาถูก ของแท้ 100%', '/webshop/uploads/shops/logos/shop_001_logo.jpg', NULL, 'mall', 4.67, 6, 10, 8, NULL, NULL, '2026-04-07 16:36:13', 1, 1, '2026-04-07 16:36:13', '2026-04-07 19:59:07'),
(2, 4, 'ฟิตเนสพลัส', 'fashion-nipa', 'อุปกรณ์ฟิตเนสและอาหารเสริมครบวงจร คุณภาพดี', '/webshop/uploads/shops/logos/shop_002_logo.jpg', NULL, 'individual', 4.20, 5, 7, 5, NULL, NULL, '2026-04-07 16:36:13', 1, 1, '2026-04-07 16:36:13', '2026-04-07 19:59:07'),
(3, 5, 'บ้านน่าอยู่', 'homestuff-pro', 'ของใช้ในบ้านและเฟอร์นิเจอร์คุณภาพดี ราคาเป็นมิตร', '/webshop/uploads/shops/logos/shop_003_logo.jpg', NULL, 'individual', 4.67, 3, 6, 6, NULL, NULL, '2026-04-07 16:36:13', 0, 1, '2026-04-07 16:36:13', '2026-04-07 19:59:07'),
(4, 13, 'แฟชั่นไทย', 'beautyglow-th', 'เสื้อผ้าแฟชั่นไทย อัพเดททุกซีซั่น ส่งไวทุกออเดอร์', '/webshop/uploads/shops/logos/shop_004_logo.jpg', NULL, 'individual', 4.50, 4, 4, 4, NULL, NULL, '2026-04-07 17:17:58', 1, 1, '2026-04-07 17:17:58', '2026-04-07 19:59:07'),
(5, 14, 'ไอทีมอลล์', 'sportzone-th', 'สินค้าไอที โทรศัพท์ คอมพิวเตอร์ ราคาถูก ประกันศูนย์', '/webshop/uploads/shops/logos/shop_005_logo.jpg', NULL, 'individual', 5.00, 1, 5, 4, NULL, NULL, '2026-04-07 17:17:58', 0, 1, '2026-04-07 17:17:58', '2026-04-07 19:59:07');

-- --------------------------------------------------------

--
-- Table structure for table `shop_bans`
--

CREATE TABLE `shop_bans` (
  `ban_id` int(10) UNSIGNED NOT NULL,
  `shop_id` int(10) UNSIGNED NOT NULL,
  `banned_by` int(10) UNSIGNED NOT NULL,
  `ban_type` enum('warning','temporary','permanent') NOT NULL DEFAULT 'temporary',
  `reason` text NOT NULL,
  `detail` text DEFAULT NULL,
  `duration_days` int(10) UNSIGNED DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `unbanned_by` int(10) UNSIGNED DEFAULT NULL,
  `unban_reason` varchar(255) DEFAULT NULL,
  `unbanned_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_bans`
--

INSERT INTO `shop_bans` (`ban_id`, `shop_id`, `banned_by`, `ban_type`, `reason`, `detail`, `duration_days`, `expires_at`, `is_active`, `unbanned_by`, `unban_reason`, `unbanned_at`, `created_at`) VALUES
(1, 3, 11, 'warning', 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó╣äÓ©íÓ╣êÓ©òÓ©úÓ©çÓ©òÓ©▓Ó©íÓ©äÓ©│Ó╣éÓ©åÓ©®Ó©ôÓ©▓', 'Ó©ÑÓ©╣Ó©üÓ©äÓ╣ëÓ©▓Ó©úÓ╣ëÓ©¡Ó©çÓ╣ÇÓ©úÓ©ÁÓ©óÓ©Ö 3 Ó©úÓ©▓Ó©ó', NULL, NULL, 0, NULL, NULL, NULL, '2026-04-07 17:17:46');

-- --------------------------------------------------------

--
-- Table structure for table `shop_followers`
--

CREATE TABLE `shop_followers` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `shop_id` int(10) UNSIGNED NOT NULL,
  `followed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_followers`
--

INSERT INTO `shop_followers` (`user_id`, `shop_id`, `followed_at`) VALUES
(6, 1, '2026-04-07 16:36:13'),
(7, 1, '2026-04-07 16:36:13'),
(8, 1, '2026-04-07 16:36:13'),
(8, 2, '2026-04-07 16:36:13'),
(9, 2, '2026-04-07 16:36:13'),
(9, 3, '2026-04-07 16:36:13'),
(10, 1, '2026-04-07 16:36:13'),
(10, 3, '2026-04-07 16:36:13'),
(15, 3, '2026-04-07 17:21:46'),
(16, 2, '2026-04-07 17:21:46'),
(16, 5, '2026-04-07 17:21:46'),
(17, 1, '2026-04-07 17:21:46'),
(17, 4, '2026-04-07 17:21:46'),
(18, 3, '2026-04-07 17:21:46'),
(19, 2, '2026-04-07 17:21:46'),
(19, 5, '2026-04-07 17:21:46'),
(20, 1, '2026-04-07 17:21:46'),
(20, 4, '2026-04-07 17:21:46'),
(21, 3, '2026-04-07 17:21:46'),
(22, 2, '2026-04-07 17:21:46'),
(22, 5, '2026-04-07 17:21:46'),
(23, 1, '2026-04-07 17:21:46'),
(23, 4, '2026-04-07 17:21:46'),
(24, 3, '2026-04-07 17:21:46'),
(25, 2, '2026-04-07 17:21:46'),
(25, 5, '2026-04-07 17:21:46'),
(26, 1, '2026-04-07 17:21:46'),
(26, 4, '2026-04-07 17:21:46'),
(27, 3, '2026-04-07 17:21:46'),
(28, 2, '2026-04-07 17:21:46'),
(28, 5, '2026-04-07 17:21:46'),
(29, 1, '2026-04-07 17:21:46'),
(29, 4, '2026-04-07 17:21:46'),
(30, 3, '2026-04-07 17:21:46'),
(31, 2, '2026-04-07 17:21:46'),
(31, 5, '2026-04-07 17:21:46'),
(32, 1, '2026-04-07 17:21:46'),
(32, 4, '2026-04-07 17:21:46'),
(33, 3, '2026-04-07 17:21:46'),
(34, 2, '2026-04-07 17:21:46'),
(34, 5, '2026-04-07 17:21:46'),
(61, 1, '2026-04-07 20:37:20'),
(63, 1, '2026-04-07 20:48:10');

-- --------------------------------------------------------

--
-- Table structure for table `shop_rating_summary`
--

CREATE TABLE `shop_rating_summary` (
  `shop_id` int(10) UNSIGNED NOT NULL,
  `rating_5` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rating_4` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rating_3` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rating_2` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rating_1` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `avg_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_rating_summary`
--

INSERT INTO `shop_rating_summary` (`shop_id`, `rating_5`, `rating_4`, `rating_3`, `rating_2`, `rating_1`, `avg_rating`, `total_reviews`, `updated_at`) VALUES
(1, 25, 8, 2, 1, 0, 4.58, 36, '2026-04-07 16:36:13'),
(2, 15, 6, 1, 0, 0, 4.64, 22, '2026-04-07 16:36:13'),
(3, 8, 3, 1, 0, 0, 4.58, 12, '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `shop_vouchers`
--

CREATE TABLE `shop_vouchers` (
  `voucher_id` int(10) UNSIGNED NOT NULL,
  `shop_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed','free_shipping') NOT NULL,
  `discount_value` decimal(12,2) NOT NULL,
  `min_order_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `max_discount_cap` decimal(12,2) DEFAULT NULL,
  `total_qty` int(10) UNSIGNED DEFAULT NULL,
  `used_qty` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `per_user_limit` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `start_at` datetime NOT NULL,
  `expire_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_vouchers`
--

INSERT INTO `shop_vouchers` (`voucher_id`, `shop_id`, `code`, `name`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_cap`, `total_qty`, `used_qty`, `per_user_limit`, `start_at`, `expire_at`, `is_active`, `created_at`) VALUES
(1, 1, 'TECH200', 'TechGadget Ó©ÑÓ©ö 200 Ó©ÜÓ©▓Ó©ù', NULL, 'fixed', 200.00, 3000.00, NULL, 200, 0, 1, '2024-04-01 00:00:00', '2024-07-31 23:59:59', 1, '2026-04-07 16:36:13'),
(2, 2, 'FASHION10', 'Fashion Ó©ÑÓ©ö 10%', NULL, 'percentage', 10.00, 300.00, 100.00, 300, 0, 1, '2024-04-01 00:00:00', '2024-07-31 23:59:59', 1, '2026-04-07 16:36:13'),
(3, 3, 'HOME50', 'HomeStuff Ó©ÑÓ©ö 50 Ó©ÜÓ©▓Ó©ù', NULL, 'fixed', 50.00, 400.00, NULL, 150, 0, 1, '2024-04-01 00:00:00', '2024-07-31 23:59:59', 1, '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_id` int(10) UNSIGNED NOT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json','html','image','color') NOT NULL DEFAULT 'text',
  `label` varchar(150) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`setting_id`, `setting_group`, `setting_key`, `setting_value`, `setting_type`, `label`, `description`, `sort_order`, `updated_at`) VALUES
(1, 'general', 'site_name', 'Shopee Thailand', 'text', 'ชื่อเว็บไซต์', NULL, 1, '2026-04-07 20:24:45'),
(2, 'general', 'site_tagline', 'ช้อปทุกอย่าง ง่ายทุกที่', 'text', 'คำขวัญเว็บไซต์', NULL, 2, '2026-04-07 20:24:47'),
(3, 'general', 'site_email', 'contact@shopee.th', 'text', 'อีเมลติดต่อ', NULL, 3, '2026-04-07 20:24:45'),
(4, 'general', 'site_phone', '02-000-0000', 'text', 'เบอร์โทรติดต่อ', NULL, 4, '2026-04-07 20:24:45'),
(5, 'general', 'site_address', '123 ถนนสาทร กรุงเทพมหานคร 10120', 'text', 'ที่อยู่', NULL, 5, '2026-04-07 20:24:47'),
(6, 'general', 'site_logo', '', 'image', 'โลโก้เว็บไซต์', NULL, 6, '2026-04-07 20:24:45'),
(7, 'general', 'site_favicon', '', 'image', 'Favicon', NULL, 7, '2026-04-07 17:05:34'),
(8, 'general', 'maintenance_mode', '0', 'boolean', 'โหมดปิดปรับปรุง', NULL, 8, '2026-04-07 20:24:45'),
(9, 'general', 'currency', 'THB', 'text', 'สกุลเงิน', NULL, 9, '2026-04-07 20:24:45'),
(10, 'general', 'currency_symbol', '฿', 'text', 'สัญลักษณ์สกุลเงิน', NULL, 10, '2026-04-07 20:24:47'),
(11, 'seo', 'meta_title', 'Shopee Thailand - ช้อปทุกอย่าง ง่ายทุกที่', 'text', 'Meta Title หลัก', NULL, 1, '2026-04-07 20:24:47'),
(12, 'seo', 'meta_description', 'ช้อปสินค้าราคาถูก คุณภาพดี ส่งไว ปลอดภัย บน Shopee Thailand', 'text', 'Meta Description', NULL, 2, '2026-04-07 20:24:47'),
(13, 'seo', 'google_analytics', '', 'text', 'Google Analytics ID', NULL, 3, '2026-04-07 17:05:34'),
(14, 'seo', 'facebook_pixel', '', 'text', 'Facebook Pixel ID', NULL, 4, '2026-04-07 17:05:34'),
(15, 'social', 'facebook_url', '', 'text', 'Facebook URL', NULL, 1, '2026-04-07 17:05:34'),
(16, 'social', 'instagram_url', '', 'text', 'Instagram URL', NULL, 2, '2026-04-07 17:05:34'),
(17, 'social', 'line_url', '', 'text', 'LINE URL', NULL, 3, '2026-04-07 17:05:34'),
(18, 'social', 'youtube_url', '', 'text', 'YouTube URL', NULL, 4, '2026-04-07 17:05:34'),
(19, 'payment', 'cod_enabled', '1', 'boolean', 'เปิด COD', NULL, 1, '2026-04-07 20:24:45'),
(20, 'payment', 'bank_transfer_enabled', '1', 'boolean', 'เปิดโอนเงิน', NULL, 2, '2026-04-07 20:24:45'),
(21, 'payment', 'credit_card_enabled', '0', 'boolean', 'เปิดบัตรเครดิต', NULL, 3, '2026-04-07 20:24:45'),
(22, 'payment', 'bank_name', 'ธนาคารไทยพาณิชย์', 'text', 'ชื่อธนาคาร', NULL, 4, '2026-04-07 20:24:47'),
(23, 'payment', 'bank_account', '000-0-00000-0', 'text', 'เลขบัญชี', NULL, 5, '2026-04-07 20:24:45'),
(24, 'payment', 'bank_account_name', 'Shopee TH Co.,Ltd.', 'text', 'ชื่อบัญชี', NULL, 6, '2026-04-07 20:24:45'),
(25, 'shipping', 'free_shipping_min', '500', 'number', 'ขั้นต่ำส่งฟรี (฿)', NULL, 1, '2026-04-07 20:24:45'),
(26, 'shipping', 'default_shipping_fee', '40', 'number', 'ค่าส่งมาตรฐาน (฿)', NULL, 2, '2026-04-07 20:24:45'),
(27, 'email', 'smtp_host', 'smtp.gmail.com', 'text', 'SMTP Host', NULL, 1, '2026-04-07 17:05:34'),
(28, 'email', 'smtp_port', '587', 'number', 'SMTP Port', NULL, 2, '2026-04-07 17:05:34'),
(29, 'email', 'smtp_user', '', 'text', 'SMTP Username', NULL, 3, '2026-04-07 17:05:34'),
(30, 'email', 'smtp_pass', '', 'text', 'SMTP Password', NULL, 4, '2026-04-07 17:05:34'),
(31, 'email', 'from_name', 'Shopee TH', 'text', 'ชื่อผู้ส่ง', NULL, 5, '2026-04-07 20:24:45'),
(32, 'appearance', 'primary_color', '#EE4D2D', 'color', 'สีหลัก', NULL, 1, '2026-04-07 20:24:45'),
(33, 'appearance', 'secondary_color', '#FF7337', 'color', 'สีรอง', NULL, 2, '2026-04-07 20:24:45'),
(34, 'appearance', 'header_bg', '#EE4D2D', 'color', 'สี Header', NULL, 3, '2026-04-07 20:24:45'),
(35, 'appearance', 'footer_bg', '#222222', 'color', 'สี Footer', NULL, 4, '2026-04-07 20:24:45');

-- --------------------------------------------------------

--
-- Table structure for table `sku_option_map`
--

CREATE TABLE `sku_option_map` (
  `sku_id` int(10) UNSIGNED NOT NULL,
  `option_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `ticket_number` varchar(20) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `category` enum('payment','shipping','product','account','refund','fraud','other') NOT NULL DEFAULT 'other',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','waiting_user','resolved','closed') NOT NULL DEFAULT 'open',
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `satisfaction_rating` tinyint(3) UNSIGNED DEFAULT NULL CHECK (`satisfaction_rating` between 1 and 5),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`ticket_id`, `ticket_number`, `user_id`, `order_id`, `subject`, `category`, `priority`, `status`, `assigned_to`, `resolved_at`, `closed_at`, `satisfaction_rating`, `created_at`, `updated_at`) VALUES
(1, 'TKT-20240401-0001', 6, 1, 'Ó©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó╣äÓ©íÓ╣êÓ©òÓ©úÓ©çÓ©üÓ©▒Ó©ÜÓ©áÓ©▓Ó©×', 'product', 'medium', 'resolved', 11, '2024-04-03 14:00:00', NULL, NULL, '2026-04-07 17:17:47', '2026-04-07 17:17:47'),
(2, 'TKT-20240402-0002', 7, 2, 'Ó©óÓ©▒Ó©çÓ╣äÓ©íÓ╣êÓ╣äÓ©öÓ╣ëÓ©úÓ©▒Ó©ÜÓ©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓', 'shipping', 'high', 'resolved', 12, '2024-04-06 10:00:00', NULL, NULL, '2026-04-07 17:17:47', '2026-04-07 17:17:47'),
(3, 'TKT-20240501-0003', 9, 4, 'Ó©òÓ╣ëÓ©¡Ó©çÓ©üÓ©▓Ó©úÓ©éÓ©¡Ó©äÓ©ÀÓ©ÖÓ╣ÇÓ©çÓ©┤Ó©Ö', 'refund', 'high', 'open', 12, NULL, NULL, NULL, '2026-04-07 17:17:47', '2026-04-07 17:17:47'),
(4, 'TKT-20240502-0004', 10, 5, 'Ó©èÓ╣êÓ©ºÓ©óÓ©öÓ╣ëÓ©ºÓ©ó Ó╣äÓ©íÓ╣êÓ©¬Ó©▓Ó©íÓ©▓Ó©úÓ©ûÓ©èÓ©│Ó©úÓ©░Ó╣ÇÓ©çÓ©┤Ó©ÖÓ╣äÓ©öÓ╣ë', 'payment', 'urgent', 'in_progress', 11, NULL, NULL, NULL, '2026-04-07 17:17:47', '2026-04-07 17:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_messages`
--

CREATE TABLE `support_ticket_messages` (
  `message_id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `sender_type` enum('user','admin','system') NOT NULL DEFAULT 'user',
  `message` text NOT NULL,
  `attachment_url` varchar(500) DEFAULT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_ticket_messages`
--

INSERT INTO `support_ticket_messages` (`message_id`, `ticket_id`, `sender_id`, `sender_type`, `message`, `attachment_url`, `is_internal`, `created_at`) VALUES
(1, 1, 6, 'user', 'Ó©¬Ó©▒Ó╣êÓ©çÓ©ïÓ©ÀÓ╣ëÓ©¡ Samsung S24 Ultra Ó©íÓ©▓Ó╣üÓ©òÓ╣êÓ©¬Ó©ÁÓ╣äÓ©íÓ╣êÓ©òÓ©úÓ©ç Ó╣âÓ©ÖÓ©úÓ©╣Ó©øÓ╣ÇÓ©øÓ╣çÓ©ÖÓ©¬Ó©ÁÓ©íÓ╣êÓ©ºÓ©çÓ╣üÓ©òÓ╣êÓ╣äÓ©öÓ╣ëÓ©úÓ©▒Ó©ÜÓ©¬Ó©ÁÓ╣ÇÓ©ùÓ©▓', NULL, 0, '2026-04-07 17:17:47'),
(2, 1, 11, 'admin', 'Ó©éÓ©¡Ó╣éÓ©ùÓ©®Ó©öÓ╣ëÓ©ºÓ©óÓ©ÖÓ©░Ó©äÓ©░ Ó©ùÓ©▓Ó©çÓ╣ÇÓ©úÓ©▓Ó©êÓ©░Ó©òÓ©┤Ó©öÓ©òÓ╣êÓ©¡Ó©úÓ╣ëÓ©▓Ó©ÖÓ©äÓ╣ëÓ©▓Ó╣üÓ©ÑÓ©░Ó©¬Ó╣êÓ©çÓ©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó©¬Ó©ÁÓ©ûÓ©╣Ó©üÓ©òÓ╣ëÓ©¡Ó©çÓ╣âÓ©½Ó╣ëÓ©äÓ©©Ó©ôÓ©áÓ©▓Ó©óÓ╣âÓ©Ö 3 Ó©ºÓ©▒Ó©ÖÓ©ùÓ©│Ó©üÓ©▓Ó©úÓ©äÓ╣êÓ©░', NULL, 0, '2026-04-07 17:17:47'),
(3, 1, 6, 'user', 'Ó©éÓ©¡Ó©ÜÓ©äÓ©©Ó©ôÓ©íÓ©▓Ó©üÓ©äÓ╣êÓ©░', NULL, 0, '2026-04-07 17:17:47'),
(4, 2, 7, 'user', 'Ó©¬Ó©▒Ó╣êÓ©çÓ╣äÓ©ø 5 Ó©ºÓ©▒Ó©ÖÓ╣üÓ©ÑÓ╣ëÓ©ºÓ©óÓ©▒Ó©çÓ╣äÓ©íÓ╣êÓ╣äÓ©öÓ╣ëÓ©úÓ©▒Ó©ÜÓ©¬Ó©┤Ó©ÖÓ©äÓ╣ëÓ©▓Ó╣ÇÓ©ÑÓ©óÓ©äÓ©úÓ©▒Ó©Ü', NULL, 0, '2026-04-07 17:17:47'),
(5, 2, 12, 'admin', 'Ó©òÓ©úÓ©ºÓ©êÓ©¬Ó©¡Ó©ÜÓ╣üÓ©ÑÓ╣ëÓ©ºÓ©×Ó©ÜÓ©ºÓ╣êÓ©▓Ó©×Ó©▒Ó©¬Ó©öÓ©©Ó©òÓ©┤Ó©öÓ©¡Ó©óÓ©╣Ó╣êÓ©ùÓ©ÁÓ╣êÓ©¬Ó©▓Ó©éÓ©▓Ó©äÓ©úÓ©▒Ó©Ü Ó©êÓ©░Ó©øÓ©úÓ©░Ó©¬Ó©▓Ó©Ö J&T Ó╣âÓ©½Ó╣ëÓ©ÖÓ©│Ó©¬Ó╣êÓ©çÓ©ºÓ©▒Ó©ÖÓ©ÖÓ©ÁÓ╣ëÓ╣ÇÓ©ÑÓ©óÓ©äÓ©úÓ©▒Ó©Ü', NULL, 0, '2026-04-07 17:17:47'),
(6, 3, 9, 'user', 'Ó©üÓ©úÓ©░Ó©ùÓ©░Ó©ùÓ©ÁÓ╣êÓ©¬Ó©▒Ó╣êÓ©çÓ©íÓ©▓Ó╣üÓ©òÓ©üÓ©òÓ©▒Ó╣ëÓ©çÓ╣üÓ©òÓ╣êÓ╣üÓ©üÓ©░Ó©üÓ©ÑÓ╣êÓ©¡Ó©ç Ó©éÓ©¡Ó©äÓ©ÀÓ©ÖÓ╣ÇÓ©çÓ©┤Ó©ÖÓ©öÓ╣ëÓ©ºÓ©óÓ©äÓ©úÓ©▒Ó©Ü', NULL, 0, '2026-04-07 17:17:47'),
(7, 4, 10, 'user', 'Ó©üÓ©öÓ©èÓ©│Ó©úÓ©░Ó╣ÇÓ©çÓ©┤Ó©ÖÓ╣üÓ©ÑÓ╣ëÓ©ºÓ©éÓ©ÂÓ╣ëÓ©Ö error Ó©òÓ©ÑÓ©¡Ó©ö Ó©ùÓ©│Ó©óÓ©▒Ó©çÓ╣äÓ©çÓ©öÓ©ÁÓ©äÓ©░', NULL, 0, '2026-04-07 17:17:47'),
(8, 4, 11, 'admin', 'Ó©ÑÓ©¡Ó©çÓ©ÑÓ╣ëÓ©▓Ó©ç cache Ó╣üÓ©ÑÓ╣ëÓ©ºÓ©ÑÓ©¡Ó©çÓ╣âÓ©½Ó©íÓ╣êÓ©äÓ©úÓ©▒Ó©Ü Ó©½Ó©úÓ©ÀÓ©¡Ó╣ÇÓ©øÓ©ÑÓ©ÁÓ╣êÓ©óÓ©Ö browser Ó©öÓ©╣Ó©äÓ©úÓ©▒Ó©Ü', NULL, 0, '2026-04-07 17:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `tax_settings`
--

CREATE TABLE `tax_settings` (
  `tax_id` int(10) UNSIGNED NOT NULL,
  `tax_name` varchar(100) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `applies_to` enum('all','category','product') NOT NULL DEFAULT 'all',
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `is_inclusive` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_settings`
--

INSERT INTO `tax_settings` (`tax_id`, `tax_name`, `tax_rate`, `applies_to`, `category_id`, `is_inclusive`, `is_active`, `created_at`) VALUES
(1, 'VAT 7%', 7.00, 'all', NULL, 1, 1, '2026-04-07 17:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `role` enum('buyer','seller','admin','superadmin') NOT NULL DEFAULT 'buyer',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `phone`, `password_hash`, `full_name`, `avatar_url`, `gender`, `birth_date`, `role`, `is_verified`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'admin1', 'admin1@shopee.th', '0800000001', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Admin One', '/webshop/uploads/users/avatar_1_1775566415.jpg', 'male', '1990-01-01', 'admin', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(2, 'admin2', 'admin2@shopee.th', '0800000002', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Admin Two', '/webshop/uploads/users/avatar_2_1775566415.jpg', 'female', '1992-05-10', 'admin', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(3, 'seller_a', 'seller_a@mail.com', '0811111111', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Somchai Thong', '/webshop/uploads/users/avatar_3_1775566415.jpg', 'male', '1988-03-15', 'seller', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(4, 'seller_b', 'seller_b@mail.com', '0822222222', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Nipa Kaew', '/webshop/uploads/users/avatar_4_1775566416.jpg', 'female', '1993-07-22', 'seller', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(5, 'seller_c', 'seller_c@mail.com', '0833333333', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Pisan Dee', '/webshop/uploads/users/avatar_5_1775566416.jpg', 'male', '1985-11-30', 'seller', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(6, 'buyer1', 'buyer1@mail.com', '0841111111', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Manee Suk', '/webshop/uploads/users/avatar_6_1775566416.jpg', 'female', '1995-04-18', 'buyer', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(7, 'buyer2', 'buyer2@mail.com', '0842222222', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Wanchai Porn', '/webshop/uploads/users/avatar_7_1775566416.jpg', 'male', '1998-09-05', 'buyer', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(8, 'buyer3', 'buyer3@mail.com', '0843333333', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Lalita Chan', '/webshop/uploads/users/avatar_8_1775566416.jpg', 'female', '2000-12-25', 'buyer', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(9, 'buyer4', 'buyer4@mail.com', '0844444444', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Natthapong K', '/webshop/uploads/users/avatar_9_1775566416.jpg', 'male', '1997-06-14', 'buyer', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(10, 'buyer5', 'buyer5@mail.com', '0845555555', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Porntip W', '/webshop/uploads/users/avatar_10_1775566416.jpg', 'female', '2001-02-28', 'buyer', 1, 1, NULL, '2026-04-07 16:36:13', '2026-04-07 20:43:53'),
(11, 'superadmin', 'superadmin@shopee.th', '0800000000', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Super Administrator', '/webshop/uploads/users/avatar_11_1775566416.jpg', NULL, NULL, 'superadmin', 1, 1, '2026-04-07 18:58:15', '2026-04-07 17:05:34', '2026-04-07 20:43:53'),
(12, 'admin', 'admin@shopee.th', '0800000003', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Administrator', '/webshop/uploads/users/avatar_12_1775566417.jpg', NULL, NULL, 'admin', 1, 1, NULL, '2026-04-07 17:05:34', '2026-04-07 20:43:53'),
(13, 'seller_d', 'seller_d@mail.com', '0844444441', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Wanchai Jaidee', '/webshop/uploads/users/avatar_13_1775566417.jpg', 'male', '1990-04-20', 'seller', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(14, 'seller_e', 'seller_e@mail.com', '0855555551', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Siriporn Kaew', '/webshop/uploads/users/avatar_14_1775566417.jpg', 'female', '1992-08-15', 'seller', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(15, 'buyer006', 'b006@mail.com', '0861111116', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Ariya Sombat', '/webshop/uploads/users/avatar_15_1775566417.jpg', 'female', '1996-02-12', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(16, 'buyer007', 'b007@mail.com', '0861111117', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Krit Panit', '/webshop/uploads/users/avatar_16_1775566417.jpg', 'male', '1988-07-30', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(17, 'buyer008', 'b008@mail.com', '0861111118', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Malee Srisuk', '/webshop/uploads/users/avatar_17_1775566417.jpg', 'female', '1993-11-05', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(18, 'buyer009', 'b009@mail.com', '0861111119', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Tawan Chai', '/webshop/uploads/users/avatar_18_1775566417.jpg', 'male', '2000-03-18', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(19, 'buyer010', 'b010@mail.com', '0861111110', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Nattida Park', '/webshop/uploads/users/avatar_19_1775566417.jpg', 'female', '1997-09-25', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(20, 'buyer011', 'b011@mail.com', '0871111111', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Somsak Dee', '/webshop/uploads/users/avatar_20_1775566417.jpg', 'male', '1985-06-10', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(21, 'buyer012', 'b012@mail.com', '0871111112', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Chanida Wan', '/webshop/uploads/users/avatar_21_1775566418.jpg', 'female', '1999-01-22', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(22, 'buyer013', 'b013@mail.com', '0871111113', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Pakorn Lek', '/webshop/uploads/users/avatar_22_1775566418.jpg', 'male', '1994-12-08', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(23, 'buyer014', 'b014@mail.com', '0871111114', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Sirada Na', '/webshop/uploads/users/avatar_23_1775566418.jpg', 'female', '2001-05-16', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(24, 'buyer015', 'b015@mail.com', '0871111115', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Mongkol Rod', '/webshop/uploads/users/avatar_24_1775566418.jpg', 'male', '1990-08-28', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(25, 'buyer016', 'b016@mail.com', '0881111116', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Pimchanok W', '/webshop/uploads/users/avatar_25_1775566418.jpg', 'female', '1995-04-03', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(26, 'buyer017', 'b017@mail.com', '0881111117', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Chatchai Boon', '/webshop/uploads/users/avatar_26_1775566418.jpg', 'male', '1987-11-19', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(27, 'buyer018', 'b018@mail.com', '0881111118', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Jirawan Porn', '/webshop/uploads/users/avatar_27_1775566418.jpg', 'female', '2002-07-07', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(28, 'buyer019', 'b019@mail.com', '0881111119', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Nantapong S', '/webshop/uploads/users/avatar_28_1775566418.jpg', 'male', '1998-02-14', 'buyer', 0, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(29, 'buyer020', 'b020@mail.com', '0881111110', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Ratana Kul', '/webshop/uploads/users/avatar_29_1775566418.jpg', 'female', '1993-09-09', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(30, 'buyer021', 'b021@mail.com', '0891111121', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Supawit K', '/webshop/uploads/users/avatar_30_1775566419.jpg', 'male', '1996-06-06', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(31, 'buyer022', 'b022@mail.com', '0891111122', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Lalita Ang', '/webshop/uploads/users/avatar_31_1775566419.jpg', 'female', '1999-12-25', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(32, 'buyer023', 'b023@mail.com', '0891111123', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Thatchai M', '/webshop/uploads/users/avatar_32_1775566419.jpg', 'male', '2003-03-03', 'buyer', 0, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(33, 'buyer024', 'b024@mail.com', '0891111124', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Vareeya C', '/webshop/uploads/users/avatar_33_1775566419.jpg', 'female', '1991-10-10', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(34, 'buyer025', 'b025@mail.com', '0891111125', '$2y$10$VyIMozF9OwKb0I0AwDOEve5OFktFugU5zoWSpyyOnIJYNY.4mjBsO', 'Jirasak P', '/webshop/uploads/users/avatar_34_1775566419.jpg', 'male', '1984-01-01', 'buyer', 1, 1, NULL, '2026-04-07 17:17:58', '2026-04-07 20:43:53'),
(61, 'dang2551', 'pushilkun@gmail.com', '0958462520', '$2y$10$RhxdaKeYo4H0WTY5U/tShuGyjxkHsCSNKKdvHEAcGwXa5bEGnYPBi', 'วงศธร ฉาบสีทอง', NULL, NULL, NULL, 'buyer', 0, 1, '2026-04-07 20:01:19', '2026-04-07 20:01:06', '2026-04-07 20:01:19'),
(63, 'dang2552', 'pushilkun1@gmail.com', '0958462521', '$2y$10$X8ef1CnvcFFYmEddU0jq9OACstnBAN17Lqs2QC0oajiPH2GPIC6Ym', 'วงศธร ฉาบสีทอง', NULL, NULL, NULL, 'buyer', 0, 1, '2026-04-07 20:47:13', '2026-04-07 20:47:07', '2026-04-07 20:47:13'),
(64, 'testuser', 'test@example.com', '0812345678', '$2y$10$8GKpzdt0PQft263Nx9faIuX638ruixEcHvDOV.6g7QP3DNCzePxDC', 'Test User', NULL, NULL, NULL, 'buyer', 0, 1, '2026-04-07 21:13:07', '2026-04-07 21:11:58', '2026-04-07 21:13:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `address_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `label` varchar(50) DEFAULT 'Home',
  `recipient_name` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `district` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Thailand',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`address_id`, `user_id`, `label`, `recipient_name`, `phone`, `address_line1`, `address_line2`, `district`, `province`, `postal_code`, `country`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 6, 'Home', 'Manee Suk', '0841111111', '123 Sukhumvit Rd.', NULL, 'Watthana', 'Bangkok', '10110', 'Thailand', 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(2, 6, 'Office', 'Manee Suk', '0841111111', '456 Silom Rd.', NULL, 'Bang Rak', 'Bangkok', '10500', 'Thailand', 0, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(3, 7, 'Home', 'Wanchai Porn', '0842222222', '78 Nimman Rd.', NULL, 'Suthep', 'Chiang Mai', '50200', 'Thailand', 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(4, 8, 'Home', 'Lalita Chan', '0843333333', '9 Ratchadamnoen Ave.', NULL, 'Phra Nakhon', 'Bangkok', '10200', 'Thailand', 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(5, 9, 'Home', 'Natthapong K', '0844444444', '55 Mueang District', NULL, 'Mueang', 'Khon Kaen', '40000', 'Thailand', 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(6, 10, 'Home', 'Porntip W', '0845555555', '12 Beach Rd.', NULL, 'Mueang', 'Phuket', '83000', 'Thailand', 1, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(7, 15, 'Home', 'Ariya Sombat', '0861111116', '345 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(8, 16, 'Home', 'Krit Panit', '0861111117', '100 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(9, 17, 'Home', 'Malee Srisuk', '0861111118', '464 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(10, 18, 'Home', 'Tawan Chai', '0861111119', '20 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(11, 19, 'Home', 'Nattida Park', '0861111110', '706 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(12, 20, 'Home', 'Somsak Dee', '0871111111', '475 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(13, 21, 'Home', 'Chanida Wan', '0871111112', '253 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(14, 22, 'Home', 'Pakorn Lek', '0871111113', '842 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(15, 23, 'Home', 'Sirada Na', '0871111114', '451 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(16, 24, 'Home', 'Mongkol Rod', '0871111115', '729 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(17, 25, 'Home', 'Pimchanok W', '0881111116', '294 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(18, 26, 'Home', 'Chatchai Boon', '0881111117', '280 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(19, 27, 'Home', 'Jirawan Porn', '0881111118', '518 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(20, 28, 'Home', 'Nantapong S', '0881111119', '749 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(21, 29, 'Home', 'Ratana Kul', '0881111110', '193 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(22, 30, 'Home', 'Supawit K', '0891111121', '717 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(23, 31, 'Home', 'Lalita Ang', '0891111122', '6 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(24, 32, 'Home', 'Thatchai M', '0891111123', '878 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(25, 33, 'Home', 'Vareeya C', '0891111124', '375 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(26, 34, 'Home', 'Jirasak P', '0891111125', '241 Ó©ûÓ©ÖÓ©ÖÓ©¬Ó©©Ó©éÓ©©Ó©íÓ©ºÓ©┤Ó©ù', NULL, 'Ó©ºÓ©▒Ó©ÆÓ©ÖÓ©▓', 'Ó©üÓ©úÓ©©Ó©çÓ╣ÇÓ©ùÓ©×Ó©íÓ©½Ó©▓Ó©ÖÓ©äÓ©ú', '10110', 'Thailand', 1, '2026-04-07 17:17:58', '2026-04-07 17:17:58'),
(38, 61, 'Home', '11', '0958462520', '83/51 ถนนพระราชดำริ', 'ตำบล หนองแก อำเภท หัวหิน', 'หัวหิน', 'ประจวบคีรีขันธ์', '77110', 'Thailand', 1, '2026-04-07 20:12:52', '2026-04-07 20:12:52'),
(39, 63, 'Home', '11', '0958462520', '83/51 ถนนพระราชดำริ', 'ตำบล หนองแก อำเภท หัวหิน', 'หนองแก', 'ประจวบคีรีขันธ์', '77110', 'Thailand', 1, '2026-04-07 20:59:42', '2026-04-07 20:59:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_bans`
--

CREATE TABLE `user_bans` (
  `ban_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `banned_by` int(10) UNSIGNED NOT NULL,
  `ban_type` enum('warning','temporary','permanent') NOT NULL DEFAULT 'temporary',
  `reason` text NOT NULL,
  `detail` text DEFAULT NULL,
  `evidence_url` varchar(500) DEFAULT NULL,
  `duration_days` int(10) UNSIGNED DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `unbanned_by` int(10) UNSIGNED DEFAULT NULL,
  `unban_reason` varchar(255) DEFAULT NULL,
  `unbanned_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_bans`
--

INSERT INTO `user_bans` (`ban_id`, `user_id`, `banned_by`, `ban_type`, `reason`, `detail`, `evidence_url`, `duration_days`, `expires_at`, `is_active`, `unbanned_by`, `unban_reason`, `unbanned_at`, `created_at`) VALUES
(1, 7, 11, 'warning', 'Ó©×Ó©ñÓ©òÓ©┤Ó©üÓ©úÓ©úÓ©íÓ╣äÓ©íÓ╣êÓ╣ÇÓ©½Ó©íÓ©▓Ó©░Ó©¬Ó©í', 'Ó©¬Ó╣êÓ©çÓ©éÓ╣ëÓ©¡Ó©äÓ©ºÓ©▓Ó©íÓ©úÓ©ÜÓ©üÓ©ºÓ©ÖÓ©£Ó©╣Ó╣ëÓ©éÓ©▓Ó©ó', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-04-07 17:17:46'),
(2, 9, 11, 'temporary', 'Ó©ïÓ©ÀÓ╣ëÓ©¡Ó╣üÓ©ÑÓ╣ëÓ©ºÓ©óÓ©üÓ╣ÇÓ©ÑÓ©┤Ó©üÓ©ïÓ╣ëÓ©│Ó©ïÓ©▓Ó©ü', 'Ó©óÓ©üÓ╣ÇÓ©ÑÓ©┤Ó©ü 5 Ó©äÓ©úÓ©▒Ó╣ëÓ©çÓ╣âÓ©Ö 1 Ó©¬Ó©▒Ó©øÓ©öÓ©▓Ó©½Ó╣îÓ╣éÓ©öÓ©óÓ╣äÓ©íÓ╣êÓ©íÓ©ÁÓ╣ÇÓ©½Ó©òÓ©©Ó©£Ó©Ñ', NULL, 7, '2026-04-14 17:17:46', 0, NULL, NULL, NULL, '2026-04-07 17:17:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `assigned_by` int(10) UNSIGNED DEFAULT NULL,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `assigned_by`, `assigned_at`, `expires_at`, `is_active`, `reason`) VALUES
(1, 1, 2, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(2, 2, 2, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(3, 3, 6, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(4, 4, 6, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(5, 5, 6, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(6, 6, 7, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(7, 7, 7, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(8, 8, 7, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(9, 9, 7, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(10, 10, 7, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(11, 11, 1, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(12, 12, 2, 11, '2026-04-07 17:17:46', NULL, 1, 'Initial setup'),
(16, 11, 2, 11, '2026-04-07 17:17:46', NULL, 1, 'SuperAdmin gets all roles'),
(17, 11, 3, 11, '2026-04-07 17:17:46', NULL, 1, 'SuperAdmin gets all roles'),
(18, 11, 4, 11, '2026-04-07 17:17:46', NULL, 1, 'SuperAdmin gets all roles'),
(19, 11, 5, 11, '2026-04-07 17:17:46', NULL, 1, 'SuperAdmin gets all roles'),
(23, 12, 3, 11, '2026-04-07 17:17:46', NULL, 1, 'Admin extra roles'),
(24, 12, 5, 11, '2026-04-07 17:17:46', NULL, 1, 'Admin extra roles'),
(26, 6, 8, 11, '2026-04-07 17:17:46', NULL, 1, 'High purchase volume'),
(27, 3, 9, 11, '2026-04-07 17:17:46', NULL, 1, 'Verified premium seller'),
(28, 13, 6, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(29, 14, 6, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(30, 15, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(31, 16, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(32, 17, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(33, 18, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(34, 19, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(35, 20, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(36, 21, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(37, 22, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(38, 23, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(39, 24, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(40, 25, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(41, 26, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(42, 27, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(43, 28, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(44, 29, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(45, 30, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(46, 31, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(47, 32, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(48, 33, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role'),
(49, 34, 7, 11, '2026-04-07 17:22:53', NULL, 1, 'Auto-assigned from primary role');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','unknown') NOT NULL DEFAULT 'unknown',
  `os` varchar(100) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `last_active` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_vouchers`
--

CREATE TABLE `user_vouchers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `voucher_type` enum('platform','shop') NOT NULL,
  `platform_voucher_id` int(10) UNSIGNED DEFAULT NULL,
  `shop_voucher_id` int(10) UNSIGNED DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `used_order_id` int(10) UNSIGNED DEFAULT NULL,
  `collected_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variant_options`
--

CREATE TABLE `variant_options` (
  `option_id` int(10) UNSIGNED NOT NULL,
  `variant_type_id` int(10) UNSIGNED NOT NULL,
  `value` varchar(100) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant_options`
--

INSERT INTO `variant_options` (`option_id`, `variant_type_id`, `value`, `image_url`, `sort_order`) VALUES
(1, 1, '256GB', NULL, 0),
(2, 1, '512GB', NULL, 1),
(3, 2, 'Titanium Black', NULL, 0),
(4, 2, 'Titanium Gray', NULL, 1),
(5, 2, 'Titanium Violet', NULL, 2),
(6, 3, 'S', NULL, 0),
(7, 3, 'M', NULL, 1),
(8, 3, 'L', NULL, 2),
(9, 3, 'XL', NULL, 3),
(10, 4, 'Pink', NULL, 0),
(11, 4, 'Beige', NULL, 1),
(12, 4, 'Navy', NULL, 2),
(13, 5, '36', NULL, 0),
(14, 5, '37', NULL, 1),
(15, 5, '38', NULL, 2),
(16, 5, '39', NULL, 3),
(17, 5, '40', NULL, 4);

-- --------------------------------------------------------

--
-- Table structure for table `variant_types`
--

CREATE TABLE `variant_types` (
  `variant_type_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant_types`
--

INSERT INTO `variant_types` (`variant_type_id`, `product_id`, `type_name`, `sort_order`) VALUES
(1, 1, 'Storage', 0),
(2, 1, 'Color', 1),
(3, 4, 'Size', 0),
(4, 4, 'Color', 1),
(5, 5, 'Size', 0);

-- --------------------------------------------------------

--
-- Table structure for table `voucher_usage_log`
--

CREATE TABLE `voucher_usage_log` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `voucher_type` enum('platform','shop') NOT NULL,
  `voucher_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `discount_applied` decimal(12,2) NOT NULL,
  `used_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `wallet_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `coins` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`wallet_id`, `user_id`, `balance`, `coins`, `created_at`, `updated_at`) VALUES
(1, 6, 250.00, 120.00, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(2, 7, 0.00, 50.00, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(3, 8, 100.00, 200.00, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(4, 9, 0.00, 30.00, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(5, 10, 75.00, 80.00, '2026-04-07 16:36:13', '2026-04-07 16:36:13'),
(6, 15, 236.61, 233.00, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(7, 16, 231.79, 296.38, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(8, 17, 274.49, 234.33, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(9, 18, 129.27, 284.83, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(10, 19, 485.77, 2.80, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(11, 20, 66.07, 189.79, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(12, 21, 383.42, 280.87, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(13, 22, 190.32, 28.34, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(14, 23, 165.25, 110.71, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(15, 24, 426.84, 48.40, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(16, 25, 122.80, 223.19, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(17, 26, 491.56, 205.10, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(18, 27, 234.46, 88.09, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(19, 28, 30.71, 127.86, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(20, 29, 473.38, 136.56, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(21, 30, 217.86, 243.91, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(22, 31, 378.96, 105.16, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(23, 32, 239.42, 102.81, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(24, 33, 138.44, 106.90, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(25, 34, 475.50, 205.81, '2026-04-07 17:21:46', '2026-04-07 17:21:46'),
(37, 61, 0.00, 0.00, '2026-04-07 20:01:06', '2026-04-07 20:01:06'),
(38, 63, 0.00, 0.00, '2026-04-07 20:47:07', '2026-04-07 20:47:07'),
(39, 64, 0.00, 0.00, '2026-04-07 21:11:58', '2026-04-07 21:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `wallet_id` int(10) UNSIGNED NOT NULL,
  `type` enum('topup','withdrawal','payment','refund','cashback','coins_earn','coins_spend') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `balance_before` decimal(12,2) NOT NULL,
  `balance_after` decimal(12,2) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(10) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'success',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`transaction_id`, `wallet_id`, `type`, `amount`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `description`, `status`, `created_at`) VALUES
(1, 1, 'cashback', 50.00, 200.00, 250.00, 'order', 1, 'Cashback 1% Ó©êÓ©▓Ó©üÓ©¡Ó©¡Ó╣ÇÓ©öÓ©¡Ó©úÓ╣î ORD-20240401-000001', 'success', '2026-04-07 16:36:13'),
(2, 3, 'coins_earn', 100.00, 100.00, 200.00, 'order', 3, 'Coins earned from order ORD-20240410-000003', 'success', '2026-04-07 16:36:13'),
(3, 1, 'topup', 200.00, 0.00, 200.00, NULL, NULL, 'Ó╣ÇÓ©òÓ©┤Ó©íÓ╣ÇÓ©çÓ©┤Ó©Ö Shopee Pay', 'success', '2026-04-07 16:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `added_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`user_id`, `product_id`, `added_at`) VALUES
(6, 2, '2026-04-07 16:36:13'),
(6, 3, '2026-04-07 16:36:13'),
(7, 1, '2026-04-07 16:36:13'),
(8, 4, '2026-04-07 16:36:13'),
(9, 1, '2026-04-07 16:36:13'),
(10, 3, '2026-04-07 16:36:13'),
(15, 1, '2026-04-07 17:21:46'),
(15, 2, '2026-04-07 17:21:46'),
(15, 3, '2026-04-07 17:21:46'),
(15, 4, '2026-04-07 17:21:46'),
(15, 5, '2026-04-07 17:21:46'),
(15, 6, '2026-04-07 17:21:46'),
(16, 1, '2026-04-07 17:21:46'),
(16, 2, '2026-04-07 17:21:46'),
(16, 3, '2026-04-07 17:21:46'),
(16, 4, '2026-04-07 17:21:46'),
(16, 5, '2026-04-07 17:21:46'),
(16, 6, '2026-04-07 17:21:46'),
(17, 1, '2026-04-07 17:21:46'),
(17, 2, '2026-04-07 17:21:46'),
(17, 3, '2026-04-07 17:21:46'),
(17, 4, '2026-04-07 17:21:46'),
(17, 5, '2026-04-07 17:21:46'),
(17, 6, '2026-04-07 17:21:46'),
(18, 1, '2026-04-07 17:21:46'),
(18, 2, '2026-04-07 17:21:46'),
(18, 3, '2026-04-07 17:21:46'),
(18, 4, '2026-04-07 17:21:46'),
(18, 5, '2026-04-07 17:21:46'),
(18, 6, '2026-04-07 17:21:46'),
(19, 1, '2026-04-07 17:21:46'),
(19, 2, '2026-04-07 17:21:46'),
(19, 3, '2026-04-07 17:21:46'),
(19, 4, '2026-04-07 17:21:46'),
(19, 5, '2026-04-07 17:21:46'),
(19, 6, '2026-04-07 17:21:46'),
(20, 1, '2026-04-07 17:21:46'),
(20, 2, '2026-04-07 17:21:46'),
(20, 3, '2026-04-07 17:21:46'),
(20, 4, '2026-04-07 17:21:46'),
(20, 5, '2026-04-07 17:21:46'),
(20, 6, '2026-04-07 17:21:46'),
(21, 1, '2026-04-07 17:21:46'),
(21, 2, '2026-04-07 17:21:46'),
(21, 3, '2026-04-07 17:21:46'),
(21, 4, '2026-04-07 17:21:46'),
(21, 5, '2026-04-07 17:21:46'),
(21, 6, '2026-04-07 17:21:46'),
(22, 1, '2026-04-07 17:21:46'),
(22, 2, '2026-04-07 17:21:46'),
(22, 3, '2026-04-07 17:21:46'),
(22, 4, '2026-04-07 17:21:46'),
(22, 5, '2026-04-07 17:21:46'),
(22, 6, '2026-04-07 17:21:46'),
(23, 1, '2026-04-07 17:21:46'),
(23, 2, '2026-04-07 17:21:46'),
(23, 3, '2026-04-07 17:21:46'),
(23, 4, '2026-04-07 17:21:46'),
(23, 5, '2026-04-07 17:21:46'),
(23, 6, '2026-04-07 17:21:46'),
(24, 1, '2026-04-07 17:21:46'),
(24, 2, '2026-04-07 17:21:46'),
(24, 3, '2026-04-07 17:21:46'),
(24, 4, '2026-04-07 17:21:46'),
(24, 5, '2026-04-07 17:21:46'),
(24, 6, '2026-04-07 17:21:46'),
(25, 1, '2026-04-07 17:21:46'),
(25, 2, '2026-04-07 17:21:46'),
(25, 3, '2026-04-07 17:21:46'),
(25, 4, '2026-04-07 17:21:46'),
(25, 5, '2026-04-07 17:21:46'),
(25, 6, '2026-04-07 17:21:46'),
(26, 1, '2026-04-07 17:21:46'),
(26, 2, '2026-04-07 17:21:46'),
(26, 3, '2026-04-07 17:21:46'),
(26, 4, '2026-04-07 17:21:46'),
(26, 5, '2026-04-07 17:21:46'),
(26, 6, '2026-04-07 17:21:46'),
(27, 1, '2026-04-07 17:21:46'),
(27, 2, '2026-04-07 17:21:46'),
(27, 3, '2026-04-07 17:21:46'),
(27, 4, '2026-04-07 17:21:46'),
(27, 5, '2026-04-07 17:21:46'),
(27, 6, '2026-04-07 17:21:46'),
(28, 1, '2026-04-07 17:21:46'),
(28, 2, '2026-04-07 17:21:46'),
(28, 3, '2026-04-07 17:21:46'),
(28, 4, '2026-04-07 17:21:46'),
(28, 5, '2026-04-07 17:21:46'),
(28, 6, '2026-04-07 17:21:46'),
(29, 1, '2026-04-07 17:21:46'),
(29, 2, '2026-04-07 17:21:46'),
(29, 3, '2026-04-07 17:21:46'),
(29, 4, '2026-04-07 17:21:46'),
(29, 5, '2026-04-07 17:21:46'),
(29, 6, '2026-04-07 17:21:46'),
(30, 1, '2026-04-07 17:21:46'),
(30, 2, '2026-04-07 17:21:46'),
(30, 3, '2026-04-07 17:21:46'),
(30, 4, '2026-04-07 17:21:46'),
(30, 5, '2026-04-07 17:21:46'),
(30, 6, '2026-04-07 17:21:46'),
(31, 1, '2026-04-07 17:21:46'),
(31, 2, '2026-04-07 17:21:46'),
(31, 3, '2026-04-07 17:21:46'),
(31, 4, '2026-04-07 17:21:46'),
(31, 5, '2026-04-07 17:21:46'),
(31, 6, '2026-04-07 17:21:46'),
(32, 1, '2026-04-07 17:21:46'),
(32, 2, '2026-04-07 17:21:46'),
(32, 3, '2026-04-07 17:21:46'),
(32, 4, '2026-04-07 17:21:46'),
(32, 5, '2026-04-07 17:21:46'),
(32, 6, '2026-04-07 17:21:46'),
(33, 1, '2026-04-07 17:21:46'),
(33, 2, '2026-04-07 17:21:46'),
(33, 3, '2026-04-07 17:21:46'),
(33, 4, '2026-04-07 17:21:46'),
(33, 5, '2026-04-07 17:21:46'),
(33, 6, '2026-04-07 17:21:46'),
(34, 1, '2026-04-07 17:21:46'),
(34, 2, '2026-04-07 17:21:46'),
(34, 3, '2026-04-07 17:21:46'),
(34, 4, '2026-04-07 17:21:46'),
(34, 5, '2026-04-07 17:21:46'),
(34, 6, '2026-04-07 17:21:46'),
(61, 13, '2026-04-07 20:36:35'),
(63, 9, '2026-04-07 20:48:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_al_user` (`user_id`),
  ADD KEY `idx_al_module` (`module`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_al_admin` (`admin_id`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`user_id`,`perm_id`),
  ADD KEY `fk_ap_perm` (`perm_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`ann_id`),
  ADD KEY `fk_ann_creator` (`created_by`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `uq_cart_sku` (`cart_id`,`product_id`,`sku_id`),
  ADD KEY `fk_ci_product` (`product_id`),
  ADD KEY `fk_ci_sku` (`sku_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_cat_parent` (`parent_id`);

--
-- Indexes for table `cms_menus`
--
ALTER TABLE `cms_menus`
  ADD PRIMARY KEY (`menu_id`),
  ADD UNIQUE KEY `location` (`location`);

--
-- Indexes for table `cms_menu_items`
--
ALTER TABLE `cms_menu_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `fk_cmi_menu` (`menu_id`),
  ADD KEY `fk_cmi_parent` (`parent_id`);

--
-- Indexes for table `cms_pages`
--
ALTER TABLE `cms_pages`
  ADD PRIMARY KEY (`page_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `cms_widgets`
--
ALTER TABLE `cms_widgets`
  ADD PRIMARY KEY (`widget_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD UNIQUE KEY `uq_conv` (`buyer_user_id`,`shop_id`),
  ADD KEY `fk_conv_shop` (`shop_id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`template_id`),
  ADD UNIQUE KEY `template_key` (`template_key`);

--
-- Indexes for table `flash_sales`
--
ALTER TABLE `flash_sales`
  ADD PRIMARY KEY (`flash_sale_id`);

--
-- Indexes for table `flash_sale_items`
--
ALTER TABLE `flash_sale_items`
  ADD PRIMARY KEY (`flash_item_id`),
  ADD KEY `fk_fsi_sale` (`flash_sale_id`),
  ADD KEY `fk_fsi_product` (`product_id`);

--
-- Indexes for table `fraud_reports`
--
ALTER TABLE `fraud_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_fr_reporter` (`reporter_id`),
  ADD KEY `fk_fr_reviewer` (`reviewed_by`);

--
-- Indexes for table `ip_blacklist`
--
ALTER TABLE `ip_blacklist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `fk_ipbl_user` (`blocked_by`);

--
-- Indexes for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD PRIMARY KEY (`point_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  ADD PRIMARY KEY (`txn_id`),
  ADD KEY `fk_lt_user` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `fk_msg_sender` (`sender_id`),
  ADD KEY `idx_msg_conv` (`conversation_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_notif_user` (`user_id`),
  ADD KEY `idx_notif_unread` (`user_id`,`is_read`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `fk_order_address` (`address_id`),
  ADD KEY `fk_order_provider` (`provider_id`),
  ADD KEY `idx_order_buyer` (`buyer_user_id`),
  ADD KEY `idx_order_shop` (`shop_id`),
  ADD KEY `idx_order_status` (`order_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `fk_oi_order` (`order_id`),
  ADD KEY `fk_oi_product` (`product_id`),
  ADD KEY `fk_oi_sku` (`sku_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `fk_osh_order` (`order_id`),
  ADD KEY `fk_osh_user` (`created_by`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`otp_id`),
  ADD KEY `idx_otp_contact` (`contact`,`contact_type`),
  ADD KEY `fk_otp_user` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_pay_order` (`order_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`perm_id`),
  ADD UNIQUE KEY `perm_key` (`perm_key`);

--
-- Indexes for table `platform_vouchers`
--
ALTER TABLE `platform_vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `uq_product_slug` (`shop_id`,`slug`),
  ADD KEY `idx_prod_status` (`status`),
  ADD KEY `idx_prod_category` (`category_id`),
  ADD KEY `idx_prod_shop` (`shop_id`);

--
-- Indexes for table `product_answers`
--
ALTER TABLE `product_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `fk_pa_question` (`question_id`),
  ADD KEY `fk_pa_answerer` (`answerer_id`);

--
-- Indexes for table `product_bans`
--
ALTER TABLE `product_bans`
  ADD PRIMARY KEY (`ban_id`),
  ADD KEY `fk_pb_product` (`product_id`),
  ADD KEY `fk_pb_banner` (`banned_by`),
  ADD KEY `fk_pb_unbanner` (`unbanned_by`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_pimg_product` (`product_id`);

--
-- Indexes for table `product_questions`
--
ALTER TABLE `product_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `fk_pq_product` (`product_id`),
  ADD KEY `fk_pq_user` (`user_id`);

--
-- Indexes for table `product_reports`
--
ALTER TABLE `product_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_pr_product` (`product_id`),
  ADD KEY `fk_pr_reporter` (`reporter_id`);

--
-- Indexes for table `product_skus`
--
ALTER TABLE `product_skus`
  ADD PRIMARY KEY (`sku_id`),
  ADD KEY `fk_psku_product` (`product_id`);

--
-- Indexes for table `product_specifications`
--
ALTER TABLE `product_specifications`
  ADD PRIMARY KEY (`spec_id`),
  ADD KEY `fk_spec_product` (`product_id`);

--
-- Indexes for table `product_views`
--
ALTER TABLE `product_views`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `idx_pv_product` (`product_id`),
  ADD KEY `idx_pv_user` (`user_id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`referral_id`),
  ADD UNIQUE KEY `referred_id` (`referred_id`),
  ADD KEY `fk_ref_referrer` (`referrer_id`);

--
-- Indexes for table `referral_codes`
--
ALTER TABLE `referral_codes`
  ADD PRIMARY KEY (`code_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `return_requests`
--
ALTER TABLE `return_requests`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `fk_ret_order` (`order_id`),
  ADD KEY `fk_ret_buyer` (`buyer_user_id`);

--
-- Indexes for table `return_request_images`
--
ALTER TABLE `return_request_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_rimg_return` (`return_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_rev_order` (`order_id`),
  ADD KEY `fk_rev_reviewer` (`reviewer_id`),
  ADD KEY `fk_rev_shop` (`shop_id`),
  ADD KEY `idx_rev_product` (`product_id`),
  ADD KEY `idx_rev_rating` (`rating`);

--
-- Indexes for table `review_images`
--
ALTER TABLE `review_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_revi_review` (`review_id`);

--
-- Indexes for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD PRIMARY KEY (`user_id`,`review_id`),
  ADD KEY `fk_rl_review` (`review_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_key` (`role_key`);

--
-- Indexes for table `search_history`
--
ALTER TABLE `search_history`
  ADD PRIMARY KEY (`search_id`),
  ADD KEY `idx_sh_user` (`user_id`);

--
-- Indexes for table `shipping_providers`
--
ALTER TABLE `shipping_providers`
  ADD PRIMARY KEY (`provider_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `shipping_rates`
--
ALTER TABLE `shipping_rates`
  ADD PRIMARY KEY (`rate_id`),
  ADD KEY `fk_sr_provider` (`provider_id`),
  ADD KEY `fk_sr_zone` (`zone_id`);

--
-- Indexes for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  ADD PRIMARY KEY (`zone_id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`shop_id`),
  ADD UNIQUE KEY `shop_name` (`shop_name`),
  ADD UNIQUE KEY `shop_slug` (`shop_slug`),
  ADD KEY `fk_shop_owner` (`owner_user_id`);

--
-- Indexes for table `shop_bans`
--
ALTER TABLE `shop_bans`
  ADD PRIMARY KEY (`ban_id`),
  ADD KEY `fk_sb_shop` (`shop_id`),
  ADD KEY `fk_sb_banner` (`banned_by`),
  ADD KEY `fk_sb_unbanner` (`unbanned_by`);

--
-- Indexes for table `shop_followers`
--
ALTER TABLE `shop_followers`
  ADD PRIMARY KEY (`user_id`,`shop_id`),
  ADD KEY `fk_sf_shop` (`shop_id`);

--
-- Indexes for table `shop_rating_summary`
--
ALTER TABLE `shop_rating_summary`
  ADD PRIMARY KEY (`shop_id`);

--
-- Indexes for table `shop_vouchers`
--
ALTER TABLE `shop_vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `uq_shop_voucher_code` (`shop_id`,`code`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `sku_option_map`
--
ALTER TABLE `sku_option_map`
  ADD PRIMARY KEY (`sku_id`,`option_id`),
  ADD KEY `fk_som_option` (`option_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `fk_st_order` (`order_id`),
  ADD KEY `fk_st_assignee` (`assigned_to`),
  ADD KEY `idx_ticket_status` (`status`),
  ADD KEY `idx_ticket_user` (`user_id`);

--
-- Indexes for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `fk_stm_ticket` (`ticket_id`),
  ADD KEY `fk_stm_sender` (`sender_id`);

--
-- Indexes for table `tax_settings`
--
ALTER TABLE `tax_settings`
  ADD PRIMARY KEY (`tax_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `fk_addr_user` (`user_id`);

--
-- Indexes for table `user_bans`
--
ALTER TABLE `user_bans`
  ADD PRIMARY KEY (`ban_id`),
  ADD KEY `idx_ub_user` (`user_id`),
  ADD KEY `idx_ub_active` (`is_active`),
  ADD KEY `fk_ub_banner` (`banned_by`),
  ADD KEY `fk_ub_unbanner` (`unbanned_by`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_role` (`user_id`,`role_id`),
  ADD KEY `fk_ur_role` (`role_id`),
  ADD KEY `fk_ur_assigner` (`assigned_by`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_sess_user` (`user_id`);

--
-- Indexes for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_uv_user` (`user_id`);

--
-- Indexes for table `variant_options`
--
ALTER TABLE `variant_options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `fk_vopt_type` (`variant_type_id`);

--
-- Indexes for table `variant_types`
--
ALTER TABLE `variant_types`
  ADD PRIMARY KEY (`variant_type_id`),
  ADD KEY `fk_vtype_product` (`product_id`);

--
-- Indexes for table `voucher_usage_log`
--
ALTER TABLE `voucher_usage_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_vul_user` (`user_id`),
  ADD KEY `fk_vul_order` (`order_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`wallet_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `fk_wt_wallet` (`wallet_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `fk_wl_product` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `ann_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `banner_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `cms_menus`
--
ALTER TABLE `cms_menus`
  MODIFY `menu_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cms_menu_items`
--
ALTER TABLE `cms_menu_items`
  MODIFY `item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms_pages`
--
ALTER TABLE `cms_pages`
  MODIFY `page_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cms_widgets`
--
ALTER TABLE `cms_widgets`
  MODIFY `widget_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `template_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `flash_sales`
--
ALTER TABLE `flash_sales`
  MODIFY `flash_sale_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `flash_sale_items`
--
ALTER TABLE `flash_sale_items`
  MODIFY `flash_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `fraud_reports`
--
ALTER TABLE `fraud_reports`
  MODIFY `report_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ip_blacklist`
--
ALTER TABLE `ip_blacklist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  MODIFY `point_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  MODIFY `txn_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `history_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `otp_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `perm_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `platform_vouchers`
--
ALTER TABLE `platform_vouchers`
  MODIFY `voucher_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `product_answers`
--
ALTER TABLE `product_answers`
  MODIFY `answer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_bans`
--
ALTER TABLE `product_bans`
  MODIFY `ban_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `product_questions`
--
ALTER TABLE `product_questions`
  MODIFY `question_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_reports`
--
ALTER TABLE `product_reports`
  MODIFY `report_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_skus`
--
ALTER TABLE `product_skus`
  MODIFY `sku_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_specifications`
--
ALTER TABLE `product_specifications`
  MODIFY `spec_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `product_views`
--
ALTER TABLE `product_views`
  MODIFY `view_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `referral_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_codes`
--
ALTER TABLE `referral_codes`
  MODIFY `code_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_requests`
--
ALTER TABLE `return_requests`
  MODIFY `return_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `return_request_images`
--
ALTER TABLE `return_request_images`
  MODIFY `image_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `review_images`
--
ALTER TABLE `review_images`
  MODIFY `image_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `search_history`
--
ALTER TABLE `search_history`
  MODIFY `search_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `shipping_providers`
--
ALTER TABLE `shipping_providers`
  MODIFY `provider_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shipping_rates`
--
ALTER TABLE `shipping_rates`
  MODIFY `rate_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  MODIFY `zone_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `shop_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shop_bans`
--
ALTER TABLE `shop_bans`
  MODIFY `ban_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shop_vouchers`
--
ALTER TABLE `shop_vouchers`
  MODIFY `voucher_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `ticket_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  MODIFY `message_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tax_settings`
--
ALTER TABLE `tax_settings`
  MODIFY `tax_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `address_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `user_bans`
--
ALTER TABLE `user_bans`
  MODIFY `ban_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variant_options`
--
ALTER TABLE `variant_options`
  MODIFY `option_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `variant_types`
--
ALTER TABLE `variant_types`
  MODIFY `variant_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `voucher_usage_log`
--
ALTER TABLE `voucher_usage_log`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `wallet_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_acl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `fk_al_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD CONSTRAINT `fk_ap_perm` FOREIGN KEY (`perm_id`) REFERENCES `permissions` (`perm_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ap_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_ann_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_ci_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ci_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ci_sku` FOREIGN KEY (`sku_id`) REFERENCES `product_skus` (`sku_id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `cms_menu_items`
--
ALTER TABLE `cms_menu_items`
  ADD CONSTRAINT `fk_cmi_menu` FOREIGN KEY (`menu_id`) REFERENCES `cms_menus` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cmi_parent` FOREIGN KEY (`parent_id`) REFERENCES `cms_menu_items` (`item_id`) ON DELETE SET NULL;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `fk_conv_buyer` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conv_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `flash_sale_items`
--
ALTER TABLE `flash_sale_items`
  ADD CONSTRAINT `fk_fsi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fsi_sale` FOREIGN KEY (`flash_sale_id`) REFERENCES `flash_sales` (`flash_sale_id`) ON DELETE CASCADE;

--
-- Constraints for table `fraud_reports`
--
ALTER TABLE `fraud_reports`
  ADD CONSTRAINT `fk_fr_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_fr_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `ip_blacklist`
--
ALTER TABLE `ip_blacklist`
  ADD CONSTRAINT `fk_ipbl_user` FOREIGN KEY (`blocked_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD CONSTRAINT `fk_lp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  ADD CONSTRAINT `fk_lt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_conv` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_address` FOREIGN KEY (`address_id`) REFERENCES `user_addresses` (`address_id`),
  ADD CONSTRAINT `fk_order_buyer` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_order_provider` FOREIGN KEY (`provider_id`) REFERENCES `shipping_providers` (`provider_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_order_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `fk_oi_sku` FOREIGN KEY (`sku_id`) REFERENCES `product_skus` (`sku_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `fk_osh_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_osh_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_pay_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_prod_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `fk_prod_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_answers`
--
ALTER TABLE `product_answers`
  ADD CONSTRAINT `fk_pa_answerer` FOREIGN KEY (`answerer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_pa_question` FOREIGN KEY (`question_id`) REFERENCES `product_questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_bans`
--
ALTER TABLE `product_bans`
  ADD CONSTRAINT `fk_pb_banner` FOREIGN KEY (`banned_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_pb_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pb_unbanner` FOREIGN KEY (`unbanned_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_pimg_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_questions`
--
ALTER TABLE `product_questions`
  ADD CONSTRAINT `fk_pq_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pq_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `product_reports`
--
ALTER TABLE `product_reports`
  ADD CONSTRAINT `fk_pr_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pr_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `product_skus`
--
ALTER TABLE `product_skus`
  ADD CONSTRAINT `fk_psku_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_specifications`
--
ALTER TABLE `product_specifications`
  ADD CONSTRAINT `fk_spec_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_views`
--
ALTER TABLE `product_views`
  ADD CONSTRAINT `fk_pv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pv_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `fk_ref_referred` FOREIGN KEY (`referred_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ref_referrer` FOREIGN KEY (`referrer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `referral_codes`
--
ALTER TABLE `referral_codes`
  ADD CONSTRAINT `fk_rc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `return_requests`
--
ALTER TABLE `return_requests`
  ADD CONSTRAINT `fk_ret_buyer` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_ret_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `return_request_images`
--
ALTER TABLE `return_request_images`
  ADD CONSTRAINT `fk_rimg_return` FOREIGN KEY (`return_id`) REFERENCES `return_requests` (`return_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_rev_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `fk_rev_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rev_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_rev_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `review_images`
--
ALTER TABLE `review_images`
  ADD CONSTRAINT `fk_revi_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`review_id`) ON DELETE CASCADE;

--
-- Constraints for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD CONSTRAINT `fk_rl_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`review_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `search_history`
--
ALTER TABLE `search_history`
  ADD CONSTRAINT `fk_sh_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_rates`
--
ALTER TABLE `shipping_rates`
  ADD CONSTRAINT `fk_sr_provider` FOREIGN KEY (`provider_id`) REFERENCES `shipping_providers` (`provider_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sr_zone` FOREIGN KEY (`zone_id`) REFERENCES `shipping_zones` (`zone_id`) ON DELETE CASCADE;

--
-- Constraints for table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `fk_shop_owner` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `shop_bans`
--
ALTER TABLE `shop_bans`
  ADD CONSTRAINT `fk_sb_banner` FOREIGN KEY (`banned_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_sb_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sb_unbanner` FOREIGN KEY (`unbanned_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `shop_followers`
--
ALTER TABLE `shop_followers`
  ADD CONSTRAINT `fk_sf_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sf_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `shop_rating_summary`
--
ALTER TABLE `shop_rating_summary`
  ADD CONSTRAINT `fk_srs_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `shop_vouchers`
--
ALTER TABLE `shop_vouchers`
  ADD CONSTRAINT `fk_sv_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `sku_option_map`
--
ALTER TABLE `sku_option_map`
  ADD CONSTRAINT `fk_som_option` FOREIGN KEY (`option_id`) REFERENCES `variant_options` (`option_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_som_sku` FOREIGN KEY (`sku_id`) REFERENCES `product_skus` (`sku_id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `fk_st_assignee` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_st_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_st_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD CONSTRAINT `fk_stm_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_stm_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `fk_addr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_bans`
--
ALTER TABLE `user_bans`
  ADD CONSTRAINT `fk_ub_banner` FOREIGN KEY (`banned_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_ub_unbanner` FOREIGN KEY (`unbanned_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ub_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_ur_assigner` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `fk_sess_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD CONSTRAINT `fk_uv_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `variant_options`
--
ALTER TABLE `variant_options`
  ADD CONSTRAINT `fk_vopt_type` FOREIGN KEY (`variant_type_id`) REFERENCES `variant_types` (`variant_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `variant_types`
--
ALTER TABLE `variant_types`
  ADD CONSTRAINT `fk_vtype_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `voucher_usage_log`
--
ALTER TABLE `voucher_usage_log`
  ADD CONSTRAINT `fk_vul_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vul_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `fk_wallet_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `fk_wt_wallet` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`wallet_id`);

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `fk_wl_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
