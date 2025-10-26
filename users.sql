-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 20, 2025 at 07:06 PM
-- Server version: 8.0.43-0ubuntu0.22.04.2
-- PHP Version: 8.1.2-1ubuntu2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `users`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_name` varchar(128) DEFAULT NULL,
  `shipping_address` text,
  `shipping_phone` varchar(32) DEFAULT NULL,
  `payment_method` varchar(32) DEFAULT NULL,
  `payment_status` varchar(32) DEFAULT 'unpaid',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(128) DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`) VALUES
(18, 'omdalvi4205@gmail.com', '8d4ee8a91a619e0ef3310470615aa63c', '2025-07-04 08:45:59');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(200) NOT NULL,
  `stock` int NOT NULL,
  `description` varchar(1000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `category`, `stock`, `description`) VALUES
(1, 'Ballpoint Pen', '25.00', 'https://via.placeholder.com/150?text=Ballpoint+Pen', '', 3, ''),
(2, 'Notebook', '80.00', 'https://via.placeholder.com/150?text=Notebook', '', 0, ''),
(3, 'Highlighter', '30.00', 'https://via.placeholder.com/150?text=Highlighter', '', 0, ''),
(4, 'Sticky Notes', '20.00', 'https://via.placeholder.com/150?text=Sticky+Notes', '', 0, ''),
(5, 'Mechanical Pencil', '45.00', 'https://via.placeholder.com/150?text=Mechanical+Pencil', '', 0, ''),
(6, 'Eraser', '10.00', 'https://via.placeholder.com/150?text=Eraser', '', 0, ''),
(7, 'Stapler', '60.00', 'https://via.placeholder.com/150?text=Stapler', '', 0, ''),
(8, 'Paper Clips', '15.00', 'https://via.placeholder.com/150?text=Paper+Clips', '', 0, ''),
(9, 'Ruler', '18.00', 'https://via.placeholder.com/150?text=Ruler', '', 0, ''),
(10, 'Glue Stick', '22.00', 'https://via.placeholder.com/150?text=Glue+Stick', '', 0, ''),
(11, 'Scissors', '70.00', 'https://via.placeholder.com/150?text=Scissors', '', 0, ''),
(12, 'Marker', '35.00', 'https://via.placeholder.com/150?text=Marker', '', 0, ''),
(13, 'Binder Clips', '25.00', 'https://via.placeholder.com/150?text=Binder+Clips', '', 0, ''),
(14, 'Correction Tape', '40.00', 'https://via.placeholder.com/150?text=Correction+Tape', '', 0, ''),
(15, 'Desk Organizer', '120.00', 'https://via.placeholder.com/150?text=Desk+Organizer', '', 2, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `profile_photo`, `first_name`, `last_name`, `address`, `phone`, `email`, `password`, `is_admin`) VALUES
(1, 'noobom', 'Om Dalvi', 'uploads/user_1_1745554785.png', 'Om', 'Dalvi', 'Room no. 103 aadishakti apt sabe gaon diva east', '8356979194', 'omdalvi4205@gmail.com', '$2y$12$5qKgYtzBI.sn97R75ZUQqOsJ4bBYAvO4mQZEczIh.hmyGZRjAED9e', 0),
(7, 'ohyeahom', 'om', NULL, NULL, NULL, NULL, '8356979194', 'noob4205om@gmail.com', '$2y$10$mv402Chg6XenDfR2HKr4Ue3m91KgHA.bcvqWaUzvns22W8xi35dTW', 1),
(3, 'raj', 'patade', NULL, '', '', NULL, NULL, 'pataderaj6@gmail.com', '$2y$12$r8hCU.yHcxeitgLeRNGrS.REPZR8.SU/acWUBxapwqRlcI5felfUW', 0),
(5, 'prachi', 'prachi bhandare', NULL, 'prachi', 'Bhandare', 'prachibhandare001@gmail.com', '', 'prachibhandare001@gmail.com', '$2y$10$eFGHEen.M7ttr5d6VWhlVub/oagQeKA2lx4UjD83qiDqkbp8M8SE.', 0),
(6, 'admin', 'Dalvi Om Shrikant Sharmila', NULL, NULL, NULL, NULL, '8356979194', 'omomm@gmail.com', '$2y$10$1Stk529qKjsZhmttnOe/J.T4SnfjMpd15/gOdBoeAsD0/ETBcSafe', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
