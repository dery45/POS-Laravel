-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2023 at 08:59 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos_suge`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  `fk_product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `fk_user_id`, `fk_product_id`, `created_at`) VALUES
(1, 1, 1, '2023-05-23 17:00:00'),
(2, 1, 2, '2023-05-23 17:00:00'),
(3, 2, 3, '2023-05-23 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Electronics', '2023-05-23 17:00:00', NULL),
(2, 'Clothing', '2023-05-23 17:00:00', NULL),
(3, 'Home & Kitchen', '2023-05-23 17:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `daily_capital`
--

CREATE TABLE `daily_capital` (
  `id` int(11) NOT NULL,
  `capital` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_capital`
--

INSERT INTO `daily_capital` (`id`, `capital`, `created_at`) VALUES
(1, 5000.00, '2023-05-24 01:00:00'),
(2, 7000.00, '2023-05-25 01:00:00'),
(3, 6000.00, '2023-05-26 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `fk_user_id`, `created_at`) VALUES
(1, 1, '2023-05-23 17:00:00'),
(2, 2, '2023-05-23 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `fk_order_id` int(11) NOT NULL,
  `fk_product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`fk_order_id`, `fk_product_id`, `price`, `quantity`) VALUES
(1, 1, 1500.00, 2),
(1, 2, 20.00, 3),
(2, 3, 70.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  `fk_order_id` int(11) NOT NULL,
  `method` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `evidence` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `fk_user_id`, `fk_order_id`, `method`, `amount`, `evidence`, `created_at`) VALUES
(1, 1, 1, 'Credit Card', 3040.00, 'payment_proof.jpg', '2023-05-23 17:00:00'),
(2, 2, 2, 'PayPal', 70.00, 'payment_proof.jpg', '2023-05-23 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `price_history`
--

CREATE TABLE `price_history` (
  `price_id` int(11) NOT NULL,
  `normal_price` decimal(10,2) DEFAULT NULL,
  `low_price` decimal(10,2) DEFAULT NULL,
  `stock_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fk_product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `price_history`
--

INSERT INTO `price_history` (`price_id`, `normal_price`, `low_price`, `stock_price`, `created_at`, `fk_product_id`) VALUES
(1, 1000.00, 900.00, 950.00, '2023-05-24 03:00:00', 1),
(2, 1050.00, 950.00, 1000.00, '2023-05-25 03:00:00', 1),
(3, 1100.00, 1000.00, 1050.00, '2023-05-26 03:00:00', 1),
(4, 25.00, 20.00, 22.50, '2023-05-24 03:00:00', 2),
(5, 28.00, 22.50, 25.00, '2023-05-25 03:00:00', 2),
(6, 30.00, 25.00, 28.00, '2023-05-26 03:00:00', 2),
(7, 1500.00, 1400.00, 1450.00, '2023-05-24 03:00:00', 3),
(8, 1550.00, 1450.00, 1500.00, '2023-05-25 03:00:00', 3),
(9, 1600.00, 1500.00, 1550.00, '2023-05-26 03:00:00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `fk_cat_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `minimum_low` decimal(10,2) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `stock` int(11) DEFAULT NULL,
  `normal_price` decimal(10,2) DEFAULT NULL,
  `low_price` decimal(10,2) DEFAULT NULL,
  `stock_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `fk_cat_id`, `image`, `status`, `minimum_low`, `brand`, `created_at`, `updated_at`, `stock`, `normal_price`, `low_price`, `stock_price`) VALUES
(1, 'Laptop', 'Powerful laptop for work and gaming', 1, 'laptop.jpg', 'active', 10.00, 'ABC', '2023-05-23 17:00:00', NULL, 50, 1500.00, 1300.00, 1400.00),
(2, 'T-Shirt', 'Casual cotton t-shirt', 2, 'tshirt.jpg', 'active', 20.00, 'XYZ', '2023-05-23 17:00:00', NULL, 100, 25.00, 20.00, 22.50),
(3, 'Blender', 'High-performance blender for smoothies', 3, 'blender.jpg', 'active', 5.00, 'XYZ', '2023-05-23 17:00:00', NULL, 30, 80.00, 70.00, 75.00);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `access` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `access`) VALUES
(1, 'Admin', 'full'),
(2, 'User', 'limited'),
(3, 'Guest', 'none');

-- --------------------------------------------------------

--
-- Table structure for table `stock_history`
--

CREATE TABLE `stock_history` (
  `id` int(11) NOT NULL,
  `stock` int(11) DEFAULT NULL,
  `fk_product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_history`
--

INSERT INTO `stock_history` (`id`, `stock`, `fk_product_id`, `created_at`) VALUES
(1, 15, 1, '2023-05-24 01:00:00'),
(2, 20, 1, '2023-05-25 01:00:00'),
(3, 25, 1, '2023-05-26 01:00:00'),
(4, 60, 2, '2023-05-24 01:00:00'),
(5, 55, 2, '2023-05-25 01:00:00'),
(6, 50, 2, '2023-05-26 01:00:00'),
(7, 8, 3, '2023-05-24 01:00:00'),
(8, 10, 3, '2023-05-25 01:00:00'),
(9, 12, 3, '2023-05-26 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `fk_role_id` tinyint(4) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `edited_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `address`, `phone_number`, `fk_role_id`, `password`, `email`, `token`, `created_at`, `edited_at`) VALUES
(1, 'John Doe', '123 Main St, City', '1234567890', 1, 'password123', 'john.doe@example.com', 'token123', '2023-05-24 00:00:00', NULL),
(2, 'Jane Smith', '456 Elm St, City', '9876543210', 2, 'password456', 'jane.smith@example.com', 'token456', '2023-05-24 00:00:00', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_user_id` (`fk_user_id`),
  ADD KEY `fk_product_id` (`fk_product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `daily_capital`
--
ALTER TABLE `daily_capital`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_user_id` (`fk_user_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`fk_order_id`,`fk_product_id`),
  ADD KEY `fk_product_id` (`fk_product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_user_id` (`fk_user_id`),
  ADD KEY `fk_order_id` (`fk_order_id`);

--
-- Indexes for table `price_history`
--
ALTER TABLE `price_history`
  ADD PRIMARY KEY (`price_id`),
  ADD KEY `fk_product_id` (`fk_product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_category` (`fk_cat_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_id` (`fk_product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user-roles` (`fk_role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `daily_capital`
--
ALTER TABLE `daily_capital`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `price_history`
--
ALTER TABLE `price_history`
  MODIFY `price_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`fk_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`fk_product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`fk_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`fk_order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`fk_product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`fk_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`fk_order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `price_history`
--
ALTER TABLE `price_history`
  ADD CONSTRAINT `fk_prices_product` FOREIGN KEY (`fk_product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `price_history_ibfk_1` FOREIGN KEY (`fk_product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`fk_cat_id`) REFERENCES `categories` (`cat_id`);

--
-- Constraints for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD CONSTRAINT `stock_history_ibfk_1` FOREIGN KEY (`fk_product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `user-roles` FOREIGN KEY (`fk_role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
