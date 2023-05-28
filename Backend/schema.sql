-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2023 at 08:22 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `fk_cat_id`, `image`, `status`, `minimum_low`, `brand`, `created_at`, `updated_at`, `stock`, `normal_price`, `low_price`, `stock_price`) VALUES
(2, 'Example Product 3', 'This is an example product.', 2, 'https://example.com/product-image.jpg', 'active', '5.00', 'Example Brand', '2023-05-28 17:50:11', NULL, 10, '24.99', '14.99', '9.99'),
(4, 'Example Product 3', 'This is an example product.', 2, 'https://example.com/product-image.jpg', 'active', '5.00', 'Example Brand', '2023-05-28 17:51:23', NULL, 10, '24.99', '14.99', '9.99'),
(5, 'Example Product 4', 'This is an example product.', 2, 'https://example.com/product-image.jpg', 'active', '5.00', 'Example Brand', '2023-05-28 17:51:30', NULL, 10, '24.99', '14.99', '9.99'),
(6, 'Example Product 4', 'This is an example product.', 2, 'https://example.com/product-image.jpg', 'active', '5.00', 'Example Brand', '2023-05-28 17:51:55', NULL, 10, '24.99', '14.99', '9.99'),
(7, 'Example Product 4', 'This is an example product.', 2, 'https://example.com/product-image.jpg', 'active', '5.00', 'Example Brand', '2023-05-28 17:54:31', NULL, 10, '24.99', '14.99', '9.99');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_category` (`fk_cat_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
