-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2026 at 01:18 PM
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
-- Database: `project_nonghainoy`
--

-- --------------------------------------------------------

--
-- Table structure for table `abbot`
--

CREATE TABLE `abbot` (
  `Abbot_id` int(11) NOT NULL,
  `Full_name` varchar(250) NOT NULL,
  `password` varchar(255) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation`
--

CREATE TABLE `donation` (
  `Material_offerings_id` int(11) NOT NULL,
  `Material_offerings_Name` varchar(250) NOT NULL,
  `Category` varchar(100) DEFAULT NULL,
  `Quantity` int(11) DEFAULT 0,
  `Unit` varchar(50) DEFAULT NULL,
  `Received_Date` varchar(250) DEFAULT NULL,
  `Income_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `Income_id` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Total_amount` decimal(15,2) NOT NULL,
  `Detail` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `abbot_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `Income_id` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Total_amount` decimal(15,2) NOT NULL,
  `Detail` text DEFAULT NULL,
  `User_id` int(11) DEFAULT NULL,
  `Abbot_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `table_user`
--

CREATE TABLE `table_user` (
  `user_id` int(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(30) NOT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'ລະຫັດອັດຕະໂນມັດ',
  `type` enum('income','expense') NOT NULL COMMENT 'ປະເພດ: income=ລາຍຮັບ, expense=ລາຍຈ່າຍ',
  `details` varchar(255) NOT NULL COMMENT 'ລາຍລະອຽດຂອງລາຍການ',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'ຈຳນວນເງິນ (ກີບ)',
  `date_added` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາທີ່ບັນທຶກ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ຕາຕະລາງເກັບລາຍຮັບ-ລາຍຈ່າຍຂອງວັດ';

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `type`, `details`, `amount`, `date_added`) VALUES
(2, 'income', 'ບໍລິຈາກສ້ອມແປງສິມ', 2500000.00, '2026-05-27 22:50:05'),
(3, 'expense', 'ຄ່າໄຟຟ້າປະຈຳເດືອນ', 350000.00, '2026-05-27 22:50:05'),
(4, 'expense', 'ຄ່າອາຫານພຣະ ແລະ ສາມະເນນ', 1200000.00, '2026-05-27 22:50:05'),
(10, 'expense', 'ຈ່າຍຄ່າໄຟ ເດືອນ06 07', 5000000.00, '2026-07-01 06:45:29'),
(11, 'income', 'ທ ກ ໂມທະນາ', 10000000.00, '2026-07-01 06:46:14'),
(13, 'income', 'ແມ່ອອກ ດ ໂມທະນາປູນ 1 ໂຕນ', 20000000.00, '2026-07-01 07:58:01'),
(14, 'expense', 'ຈ່າຍຄ່າແປງລົດ', 200000.00, '2026-07-01 07:58:49'),
(15, 'expense', 'ຈ່າຍຄ່າໄຟ ນ້ຳເດືອນ 03', 400000.00, '2026-07-01 07:59:38'),
(23, 'income', 'ທ ກ ໂມທະນາ', 2000000.00, '2026-08-13 01:03:08'),
(24, 'income', 'ເງິນກອງບຸນ', 1000000.00, '2026-08-13 01:04:07'),
(25, 'income', 'ເງິນບຸນຜ້າປ່າ', 10000000.00, '2026-08-13 07:55:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `phone`, `avatar`, `role`, `created_at`, `updated_at`) VALUES
(1, 'ພຣະ ປີ່ເຕີ້ ພົມມະລິນ', '$2y$10$H8ucx0/C21eGnIiA9EE2DezpiycQw04cJUxdhi3PI0VcuatbjeL6O', '2078911894', 'avatar_1_1786553617.png', 'user', '2026-08-12 16:54:44', NULL),
(2, 'ສາມະເນນ ຫລໍ່ ຈຸນຈະຄອນ', '$2y$10$QMZsPtkTXBqdmGgxPaU8PuzYdOJhZsgRG0qq8kJwFMV5970BRLQ1C', '02078083340', NULL, 'user', '2026-08-05 03:41:03', NULL),
(3, 'ກກກກ', '$2y$10$nOZSwAIPRGs4IbzQlAlHD.QVXL3LK17ohWQzLjhpso3nxcfdAZ0am', '2078911894', NULL, 'viewer', '2026-08-05 03:41:03', NULL),
(4, 'ແນວໂຮມບ້ານ', '$2y$10$BHXRjfzRZ6JSVj4zI9YOlOIb16Xp1h6GVOD9Nvfx5bbRxYkKrDMHC', '2096938116', NULL, 'viewer', '2026-08-05 03:41:03', NULL),
(5, 'ນາຍບ້ານ', '$2y$10$82xIDhiZJFulNdhPy/iu7OR2LxMGNyIs51InGpl/dW9SnOy3k8FL.', '02096938116', NULL, 'viewer', '2026-08-05 09:39:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `village_front_committee`
--

CREATE TABLE `village_front_committee` (
  `village_front_committee_id` int(11) NOT NULL,
  `Full_name` varchar(250) NOT NULL,
  `password` varchar(255) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Abbot_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abbot`
--
ALTER TABLE `abbot`
  ADD PRIMARY KEY (`Abbot_id`);

--
-- Indexes for table `donation`
--
ALTER TABLE `donation`
  ADD PRIMARY KEY (`Material_offerings_id`),
  ADD KEY `fk_donation_income` (`Income_id`);

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`Income_id`),
  ADD KEY `fk_expense_user` (`user_id`),
  ADD KEY `fk_expense_abbot` (`abbot_id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`Income_id`),
  ADD KEY `fk_income_user` (`User_id`),
  ADD KEY `fk_income_abbot` (`Abbot_id`);

--
-- Indexes for table `table_user`
--
ALTER TABLE `table_user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_date_added` (`date_added`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `village_front_committee`
--
ALTER TABLE `village_front_committee`
  ADD PRIMARY KEY (`village_front_committee_id`),
  ADD KEY `fk_vfc_abbot` (`Abbot_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abbot`
--
ALTER TABLE `abbot`
  MODIFY `Abbot_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation`
--
ALTER TABLE `donation`
  MODIFY `Material_offerings_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `Income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `Income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດອັດຕະໂນມັດ', AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `village_front_committee`
--
ALTER TABLE `village_front_committee`
  MODIFY `village_front_committee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donation`
--
ALTER TABLE `donation`
  ADD CONSTRAINT `fk_donation_income` FOREIGN KEY (`Income_id`) REFERENCES `expense` (`Income_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `expense`
--
ALTER TABLE `expense`
  ADD CONSTRAINT `fk_expense_abbot` FOREIGN KEY (`abbot_id`) REFERENCES `abbot` (`Abbot_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_expense_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `fk_income_abbot` FOREIGN KEY (`Abbot_id`) REFERENCES `abbot` (`Abbot_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_income_user` FOREIGN KEY (`User_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `village_front_committee`
--
ALTER TABLE `village_front_committee`
  ADD CONSTRAINT `fk_vfc_abbot` FOREIGN KEY (`Abbot_id`) REFERENCES `abbot` (`Abbot_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
