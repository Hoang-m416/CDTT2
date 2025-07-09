-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 20, 2025 lúc 06:58 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `sportshop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$9QgA3A8NsgT9foOyVF2qieMlxPLjEqTpxZZB6niaeoCpS1yAc0i1u');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`) VALUES
(3, 'Kamto'),
(4, 'Adidas'),
(5, 'Nike');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `customer_id`, `product_id`, `size`, `quantity`, `price`, `created_at`) VALUES
(145, 10, 32, '36', 7, 215000.00, '2025-06-20 06:48:01'),
(146, 10, 32, '37', 13, 215000.00, '2025-06-20 06:48:16'),
(147, 10, 32, '38', 12, 215000.00, '2025-06-20 06:52:56'),
(148, 10, 32, '39', 12, 215000.00, '2025-06-20 06:54:13'),
(149, 4, 32, '36', 4, 215000.00, '2025-06-20 06:57:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(17, 'Áo Thun'),
(18, 'Áo Khoắc'),
(19, 'Quần Short'),
(20, 'Quần Dài'),
(21, 'Phụ Kiện'),
(22, 'Giày');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `province` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `username`, `full_name`, `email`, `password`, `phone`, `address`, `province`, `created_at`, `avatar`) VALUES
(1, 'min', 'Nguyễn Hồng Ân', 'min@gmail.com', '$2y$10$RHlPOMTssIaQJG6e/T03mOjqro3rCSLaqG.BpACgHgBLspbRVSE9y', '0926542262', 'cs3', 'Bà Rịa - Vũng Tàu', '2025-05-23 21:52:46', 'avatar_1_1748106919.png'),
(2, 'h', 'Nguyễn Khang Duy', 'admin@example.com', '$2y$10$5WHg5anWiS.Xr44Z2CO2OeZir2WsyvJm1W5eB.Y4fazMWF8fr8EGm', '0926542262', 'ggg', 'Đồng Nai', '2025-05-24 17:58:50', 'avatar_2_1748104399.png'),
(4, 'a1', 'Nguyễn Hữu Nhân', 'a1@gmail.com', '$2y$10$L.84PdnC4uAeX/pPSkmq8uRW/T/.IA4umd09fOdP7NBS8ZSdd3S9K', '0926542262', 'cs3', 'Gia Lai', '2025-05-27 17:01:02', NULL),
(5, 'a2', 'Lê Ngọc Tuấn', 'a2@gmail.com', '$2y$10$6SeEfETsKXKSFHZ.QT5nPe0PDVQkuU9nLX5vd65CsO73R2hVeMqEe', '654444444444', 'cs3', 'Gia Lai', '2025-05-27 17:03:20', NULL),
(6, 'min1', 'Nguyễn Văn v', 'a3@gmail.com', '$2y$10$3MztG1PCLUn8FXBoYMYQEOrsj/TJulbcDSEPLmq8DU.hUt5JDXrBG', '6667788', 'cs3', 'Đắk Nông', '2025-06-09 21:11:03', NULL),
(7, 'min1234', 'MIN CUTe44', 'a4@gmail.com', '$2y$10$3LDL9y3v6UCBTmOp2MFWLuDSkN8ptiNRKZQqd2vMHSWFzme0mrb8O', '66', 'cs3', 'Điện Biên', '2025-06-09 21:15:40', NULL),
(8, 'min555', 'Nguyễn Văn v11', 'a5@gmail.com', '$2y$10$Aj5yGpIy2jXhILtTwHia8.StRjlSbmFmzWhnuST6Un9p47Sijsxnm', '1234', 'cs1', 'Cao Bằng', '2025-06-09 21:17:59', NULL),
(9, 'min5554', 'Nguyễn Văn v11', 'a6@gmail.com', '$2y$10$Lwfe8P9fb76ar/7L4Ps1hOLLG/Uf3F1GvKqhnevcsvM9AxHAJqykq', '1234', 'cs1', 'Cao Bằng', '2025-06-09 21:18:56', NULL),
(10, 'mi11', 'minh', 'hoang11@gmail.com', '$2y$10$iL08l50EhtKQD2n31azjSOPtk.oT.dHOmgE0f6If3fgrn2RIfcxBC', '5657898099', 'cs3', 'Cà Mau', '2025-06-20 11:41:12', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `favorites`
--

INSERT INTO `favorites` (`id`, `customer_id`, `product_id`, `created_at`) VALUES
(26, 1, 30, '2025-05-25 10:55:58'),
(32, 2, 31, '2025-05-26 12:16:29'),
(36, 2, 30, '2025-06-02 10:18:26'),
(37, 2, 29, '2025-06-02 10:18:27'),
(38, 1, 26, '2025-06-07 11:56:43'),
(39, 1, 32, '2025-06-07 12:06:45'),
(42, 5, 32, '2025-06-12 03:52:20'),
(43, 5, 30, '2025-06-12 03:52:22'),
(44, 5, 29, '2025-06-12 03:52:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `sender` enum('admin','customer') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`, `sender`) VALUES
(20, 1, 1, 'hi', 0, '2025-05-26 22:42:58', 'customer'),
(21, 1, 1, 'hi', 0, '2025-05-26 22:43:04', 'admin'),
(22, 2, 1, 'hi', 0, '2025-05-26 23:52:50', 'customer'),
(23, 1, 1, 'cảm ơn bạn đã mua hàng bên shop\r\n', 0, '2025-06-02 22:21:59', 'admin'),
(24, 1, 1, 'kcj', 0, '2025-06-02 22:22:05', 'customer'),
(25, 1, 1, 'cảm ơn bạn đã mua hàng bên shop\r\n', 0, '2025-06-02 22:22:09', 'admin'),
(26, 1, 1, 'ủa', 0, '2025-06-02 22:22:29', 'customer'),
(27, 1, 1, 'hả', 0, '2025-06-02 22:22:38', 'admin'),
(28, 1, 1, 'kcj', 0, '2025-06-02 22:22:44', 'admin'),
(29, 1, 1, 'oke bạn', 0, '2025-06-02 22:22:53', 'customer'),
(30, 1, 1, '1000000 triệu', 0, '2025-06-07 23:24:15', 'admin'),
(31, 1, 1, 'oke chôys', 0, '2025-06-07 23:24:25', 'customer'),
(32, 5, 1, 'hi', 0, '2025-06-11 16:25:07', 'customer');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `delivery_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_fee` int(11) DEFAULT 0,
  `total_amount` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Chờ xử lý',
  `inventory_deducted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `delivery_address`, `payment_method`, `shipping_fee`, `total_amount`, `status`, `inventory_deducted`) VALUES
(30, 1, '2025-05-25 15:49:02', 'cs3', 'COD', 20000, 887000, 'Đã giao thành công', 0),
(31, 1, '2025-05-25 16:00:37', 'cs3', 'MoMo', 0, 460000, 'Đã giao thành công', 0),
(35, 1, '2025-05-25 22:45:07', 'cs3', 'COD', 20000, 598000, 'Đã hủy', 0),
(36, 1, '2025-05-25 22:46:49', 'cs3', 'MoMo', 0, 689800, 'Đã giao thành công', 0),
(37, 1, '2025-05-25 23:02:55', 'cs3', 'COD', 20000, 460000, 'Đã hủy', 0),
(38, 1, '2025-05-25 23:05:26', 'cs3', 'COD', 20000, 770000, 'Đã hủy', 0),
(39, 1, '2025-05-25 23:09:35', 'cs3', 'COD', 20000, 460000, 'Đã hủy', 0),
(40, 1, '2025-05-25 23:10:29', 'cs3', 'COD', 20000, 460000, 'Đã giao thành công', 0),
(41, 1, '2025-05-26 01:45:31', 'cs3', 'COD', 20000, 1020000, 'Đã hủy', 0),
(43, 1, '2025-05-25 21:02:52', '', 'COD', 0, 191100, 'Đã giao thành công', 0),
(47, 1, '2025-05-25 21:09:44', '', 'COD', 0, 191100, 'Đã giao thành công', 0),
(48, 1, '2025-05-26 15:35:38', 'cs3', 'COD', 0, 1225000, 'Đã giao thành công', 0),
(49, 1, '2025-05-26 23:09:54', 'cs3', 'COD', 0, 2695000, 'Đã giao thành công', 0),
(50, 2, '2025-05-27 00:08:42', 'cs3', 'COD', 20000, 451200, 'Đã giao thành công', 0),
(51, 2, '2025-05-27 00:18:26', 'cs3', 'MoMo', 0, 223742, 'Đã giao thành công', 0),
(52, 4, '2025-05-27 17:01:27', 'cs3', 'COD', 20000, 211100, 'Đã giao thành công', 0),
(53, 5, '2025-05-27 17:03:37', 'cs3', 'COD', 20000, 451200, 'Đã giao thành công', 0),
(54, 1, '2025-05-28 22:00:42', 'cs3', 'COD', 0, 9800000, 'Đã giao thành công', 0),
(67, 1, '2025-05-30 20:18:34', 'cs3', 'MoMo', 0, 5434000, 'Đang xử lý', 0),
(68, 1, '2025-05-30 20:19:55', 'cs3', 'COD', 0, 20900000, 'Đã hủy', 0),
(69, 1, '2025-05-30 20:21:22', 'cs3', 'COD', 0, 20900000, 'Đã hủy', 0),
(70, 1, '2025-05-30 20:24:28', 'cs3', 'COD', 0, 1254000, 'Đã hủy', 0),
(71, 1, '2025-05-30 20:28:40', 'cs3', 'COD', 0, 7410000, 'Đã hủy', 0),
(77, 1, '2025-05-31 00:10:54', 'cs3', 'COD', 0, 5434000, 'Đã giao thành công', 0),
(81, 1, '2025-06-01 01:42:55', 'cs3', 'COD', 0, 13376000, 'Đã giao thành công', 0),
(82, 1, '2025-06-02 00:55:21', 'cs3', 'COD', 20000, 968610, 'Đã hủy', 0),
(83, 1, '2025-06-02 01:08:27', 'cs3', 'MoMo', 0, 7965750, 'Đã giao thành công', 0),
(84, 4, '2025-06-03 10:08:23', 'cs3', 'COD', 20000, 215000, 'Chờ xác nhận', 0),
(87, 1, '2025-06-08 00:01:12', 'cs3', 'COD', 0, 7039500, 'Đã hủy', 0),
(88, 1, '2025-06-08 00:08:25', 'cs3 uth sâs', 'COD', 20000, 575750, 'Đã gửi đơn vận chuyển', 0),
(89, 5, '2025-06-09 21:22:54', 'cs3', 'COD', 0, 1170000, 'Đã giao thành công', 0),
(90, 5, '2025-06-10 17:37:56', 'cs3', 'COD', 20000, 211100, 'Đã hủy', 0),
(91, 5, '2025-06-12 19:24:36', 'cs3', 'COD', 0, 9310000, 'Đã hủy', 0),
(92, 5, '2025-06-12 19:25:31', 'cs3', 'COD', 20000, 211100, 'Đã hủy', 0),
(93, 10, '2025-06-20 11:53:53', 'cs3', 'COD', 0, 7740000, 'Đã hủy', 0),
(94, 4, '2025-06-20 11:57:20', 'cs3', 'COD', 20000, 880000, 'Chờ xác nhận', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `size`, `quantity`, `price`) VALUES
(36, 30, 23, 'S', 1, 289000),
(37, 30, 23, 'M', 1, 289000),
(38, 30, 23, 'L', 1, 289000),
(42, 35, 23, 'S', 1, 289000),
(43, 35, 23, 'M', 1, 289000),
(44, 36, 24, 'S', 1, 213300),
(45, 36, 24, 'XL', 1, 213300),
(46, 36, 26, 'S', 1, 243200),
(49, 38, 30, 'S', 1, 250000),
(54, 48, 30, 'S', 1, 250000),
(56, 49, 30, 'S', 1, 250000),
(58, 51, 25, 'S', 1, 207900),
(59, 52, 30, 'S', 1, 195000),
(78, 71, 29, 'S', 40, 195000),
(90, 82, 32, '36', 10, 10511),
(91, 82, 32, '37', 12, 10511),
(92, 82, 32, '38', 50, 10511),
(93, 82, 32, '39', 23, 10511),
(94, 83, 30, 'S', 43, 195000),
(95, 84, 30, 'M', 1, 195000),
(100, 87, 30, 'L', 38, 195000),
(101, 88, 30, 'M', 3, 195000),
(102, 89, 30, 'M', 3, 195000),
(103, 89, 30, 'L', 3, 195000),
(104, 90, 30, 'M', 1, 195000),
(105, 91, 30, 'M', 36, 250000),
(106, 91, 30, 'XL', 2, 250000),
(107, 92, 30, 'L', 1, 195000),
(108, 93, 32, '36', 11, 215000),
(109, 93, 32, '37', 13, 215000),
(110, 93, 32, '38', 12, 215000),
(111, 94, 32, '36', 4, 215000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 5.0,
  `rating_count` int(11) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category_id`, `brand_id`, `price`, `discount_percentage`, `quantity`, `color`, `image`, `rating`, `rating_count`, `is_deleted`) VALUES
(21, 'Áo Thun Thể Thao Đ001', 'Sản phẩm được thiết kế nhằm mang đến sự thoải mái và thời trang tối ưu cho người mặc trong mọi hoàn cảnh. Với sự kết hợp giữa chất liệu cao cấp và thiết kế hiện đại, sản phẩm phù hợp cho cả đi học, đi làm, dạo phố hay các hoạt động thể thao nhẹ nhàng.\r\n\r\n✔ Chất liệu vải mềm mại, thoáng khí, thấm hút mồ hôi tốt – mang lại cảm giác dễ chịu suốt cả ngày\r\n✔ Đường may tỉ mỉ, chắc chắn – đảm bảo độ bền và tính thẩm mỹ cao\r\n✔ Kiểu dáng trẻ trung, dễ phối đồ – phù hợp với nhiều phong cách khác nhau\r\n✔ Form dáng vừa vặn, tôn lên vóc dáng người mặc\r\n✔ Phù hợp cho nhiều dịp sử dụng: đi chơi, đi làm, vận động ngoài trời hay mặc hàng ngày\r\n✔ Nhiều màu sắc và kích cỡ để lựa chọn – đáp ứng nhu cầu đa dạng của khách hàng', 17, 4, 300000.00, 19.00, 400, 'Đỏ', 'img_6832d617c0dd60.28542742.png', 5.0, 0, 0),
(22, 'Áo Thun Thể Thao Đe001', 'Sản phẩm được thiết kế nhằm mang đến sự thoải mái và thời trang tối ưu cho người mặc trong mọi hoàn cảnh. Với sự kết hợp giữa chất liệu cao cấp và thiết kế hiện đại, sản phẩm phù hợp cho cả đi học, đi làm, dạo phố hay các hoạt động thể thao nhẹ nhàng.\r\n\r\n✔ Chất liệu vải mềm mại, thoáng khí, thấm hút mồ hôi tốt – mang lại cảm giác dễ chịu suốt cả ngày\r\n✔ Đường may tỉ mỉ, chắc chắn – đảm bảo độ bền và tính thẩm mỹ cao\r\n✔ Kiểu dáng trẻ trung, dễ phối đồ – phù hợp với nhiều phong cách khác nhau\r\n✔ Form dáng vừa vặn, tôn lên vóc dáng người mặc\r\n✔ Phù hợp cho nhiều dịp sử dụng: đi chơi, đi làm, vận động ngoài trời hay mặc hàng ngày\r\n✔ Nhiều màu sắc và kích cỡ để lựa chọn – đáp ứng nhu cầu đa dạng của khách hàng', 17, 5, 300000.00, 19.00, 400, 'Đen', 'img_6832d64783a242.20451505.png', 5.0, 0, 0),
(23, 'Áo Thun Thể Thao PL001', 'Sản phẩm được thiết kế nhằm mang đến sự thoải mái và thời trang tối ưu cho người mặc trong mọi hoàn cảnh. Với sự kết hợp giữa chất liệu cao cấp và thiết kế hiện đại, sản phẩm phù hợp cho cả đi học, đi làm, dạo phố hay các hoạt động thể thao nhẹ nhàng.\r\n\r\n✔ Chất liệu vải mềm mại, thoáng khí, thấm hút mồ hôi tốt – mang lại cảm giác dễ chịu suốt cả ngày\r\n✔ Đường may tỉ mỉ, chắc chắn – đảm bảo độ bền và tính thẩm mỹ cao\r\n✔ Kiểu dáng trẻ trung, dễ phối đồ – phù hợp với nhiều phong cách khác nhau\r\n✔ Form dáng vừa vặn, tôn lên vóc dáng người mặc\r\n✔ Phù hợp cho nhiều dịp sử dụng: đi chơi, đi làm, vận động ngoài trời hay mặc hàng ngày\r\n✔ Nhiều màu sắc và kích cỡ để lựa chọn – đáp ứng nhu cầu đa dạng của khách hàng', 17, 4, 340000.00, 15.00, 400, 'Xanh', 'img_6832d697e88797.60075768.png', 5.0, 1, 0),
(24, 'Áo Bóng Truyền BT001', 'Sản phẩm được thiết kế nhằm mang đến sự thoải mái và thời trang tối ưu cho người mặc trong mọi hoàn cảnh. Với sự kết hợp giữa chất liệu cao cấp và thiết kế hiện đại, sản phẩm phù hợp cho cả đi học, đi làm, dạo phố hay các hoạt động thể thao nhẹ nhàng.\r\n\r\n✔ Chất liệu vải mềm mại, thoáng khí, thấm hút mồ hôi tốt – mang lại cảm giác dễ chịu suốt cả ngày\r\n✔ Đường may tỉ mỉ, chắc chắn – đảm bảo độ bền và tính thẩm mỹ cao\r\n✔ Kiểu dáng trẻ trung, dễ phối đồ – phù hợp với nhiều phong cách khác nhau\r\n✔ Form dáng vừa vặn, tôn lên vóc dáng người mặc\r\n✔ Phù hợp cho nhiều dịp sử dụng: đi chơi, đi làm, vận động ngoài trời hay mặc hàng ngày\r\n✔ Nhiều màu sắc và kích cỡ để lựa chọn – đáp ứng nhu cầu đa dạng của khách hàng', 17, 3, 270000.00, 21.00, 200, 'Trắng', 'img_6832d6ed5d8509.24325501.png', 5.0, 1, 0),
(25, 'Áo Bóng Truyền BT002', 'Sản phẩm được thiết kế nhằm mang đến sự thoải mái và thời trang tối ưu cho người mặc trong mọi hoàn cảnh. Với sự kết hợp giữa chất liệu cao cấp và thiết kế hiện đại, sản phẩm phù hợp cho cả đi học, đi làm, dạo phố hay các hoạt động thể thao nhẹ nhàng.\r\n\r\n✔ Chất liệu vải mềm mại, thoáng khí, thấm hút mồ hôi tốt – mang lại cảm giác dễ chịu suốt cả ngày\r\n✔ Đường may tỉ mỉ, chắc chắn – đảm bảo độ bền và tính thẩm mỹ cao\r\n✔ Kiểu dáng trẻ trung, dễ phối đồ – phù hợp với nhiều phong cách khác nhau\r\n✔ Form dáng vừa vặn, tôn lên vóc dáng người mặc\r\n✔ Phù hợp cho nhiều dịp sử dụng: đi chơi, đi làm, vận động ngoài trời hay mặc hàng ngày\r\n✔ Nhiều màu sắc và kích cỡ để lựa chọn – đáp ứng nhu cầu đa dạng của khách hàng', 17, 4, 270000.00, 23.00, 250, 'Trắng', 'img_6832d739ed62a4.00435119.png', 5.0, 0, 0),
(26, 'Áo Thun Thể Thao Họa Tiết', 'Sản phẩm được thiết kế nhằm mang đến sự thoải mái và thời trang tối ưu cho người mặc trong mọi hoàn cảnh. Với sự kết hợp giữa chất liệu cao cấp và thiết kế hiện đại, sản phẩm phù hợp cho cả đi học, đi làm, dạo phố hay các hoạt động thể thao nhẹ nhàng.\r\n\r\n✔ Chất liệu vải mềm mại, thoáng khí, thấm hút mồ hôi tốt – mang lại cảm giác dễ chịu suốt cả ngày\r\n✔ Đường may tỉ mỉ, chắc chắn – đảm bảo độ bền và tính thẩm mỹ cao\r\n✔ Kiểu dáng trẻ trung, dễ phối đồ – phù hợp với nhiều phong cách khác nhau\r\n✔ Form dáng vừa vặn, tôn lên vóc dáng người mặc\r\n✔ Phù hợp cho nhiều dịp sử dụng: đi chơi, đi làm, vận động ngoài trời hay mặc hàng ngày\r\n✔ Nhiều màu sắc và kích cỡ để lựa chọn – đáp ứng nhu cầu đa dạng của khách hàng', 17, 5, 320000.00, 24.00, 200, 'xanh', 'img_6832d77bed6978.39084139.png', 5.0, 1, 0),
(29, 'Quần Short 002', 'Sản phẩm được thiết kế nhằm mang đến sự thoải mái và thời trang tối ưu cho người mặc trong mọi hoàn cảnh. Với sự kết hợp giữa chất liệu cao cấp và thiết kế hiện đại, sản phẩm phù hợp cho cả đi học, đi làm, dạo phố hay các hoạt động thể thao nhẹ nhàng.\r\n\r\n✔ Chất liệu vải mềm mại, thoáng khí, thấm hút mồ hôi tốt – mang lại cảm giác dễ chịu suốt cả ngày\r\n✔ Đường may tỉ mỉ, chắc chắn – đảm bảo độ bền và tính thẩm mỹ cao\r\n✔ Kiểu dáng trẻ trung, dễ phối đồ – phù hợp với nhiều phong cách khác nhau\r\n✔ Form dáng vừa vặn, tôn lên vóc dáng người mặc\r\n✔ Phù hợp cho nhiều dịp sử dụng: đi chơi, đi làm, vận động ngoài trời hay mặc hàng ngày\r\n✔ Nhiều màu sắc và kích cỡ để lựa chọn – đáp ứng nhu cầu đa dạng của khách hàng', 19, 4, 250000.00, 22.00, 160, 'Đen', 'img_6832d81e0a43c7.63603686.png', 5.0, 0, 0),
(30, 'Quần Short 003', 'Sản phẩm được thiết kế nhằm mang đến sự thoải mái và thời trang tối ưu cho người mặc trong mọi hoàn cảnh. Với sự kết hợp giữa chất liệu cao cấp và thiết kế hiện đại, sản phẩm phù hợp cho cả đi học, đi làm, dạo phố hay các hoạt động thể thao nhẹ nhàng.\r\n\r\n✔ Chất liệu vải mềm mại, thoáng khí, thấm hút mồ hôi tốt – mang lại cảm giác dễ chịu suốt cả ngày\r\n✔ Đường may tỉ mỉ, chắc chắn – đảm bảo độ bền và tính thẩm mỹ cao\r\n✔ Kiểu dáng trẻ trung, dễ phối đồ – phù hợp với nhiều phong cách khác nhau\r\n✔ Form dáng vừa vặn, tôn lên vóc dáng người mặc\r\n✔ Phù hợp cho nhiều dịp sử dụng: đi chơi, đi làm, vận động ngoài trời hay mặc hàng ngày\r\n✔ Nhiều màu sắc và kích cỡ để lựa chọn – đáp ứng nhu cầu đa dạng của khách hàng', 19, 3, 250000.00, 22.00, 200, 'xanh', 'img_6832d855b55567.94831730.png', 4.6, 5, 0),
(32, 'Giày', 'gg', 22, 4, 250000.00, 14.00, 95, 'Trắng', 'img_683be859010e12.57000715.png', 5.0, 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`) VALUES
(9, 21, 'extra_6832d617c45540.86880143.png'),
(10, 21, 'extra_6832d617c52586.49110217.png'),
(11, 21, 'extra_6832d617c603b5.95133208.png'),
(12, 22, 'extra_6832d6478789c2.68474266.png'),
(13, 22, 'extra_6832d64788a848.35618945.png'),
(14, 22, 'extra_6832d647899b99.54776829.png'),
(15, 23, 'extra_6832d697eb2e38.35116647.png'),
(16, 23, 'extra_6832d697ebe752.29537865.png'),
(17, 23, 'extra_6832d697eca310.03575131.png'),
(18, 24, 'extra_6832d6ed6329b1.33649266.png'),
(19, 24, 'extra_6832d6ed63fb80.38714351.png'),
(20, 24, 'extra_6832d6ed64c133.09778871.png'),
(21, 25, 'extra_6832d739eff516.98220746.png'),
(22, 25, 'extra_6832d739f093c5.71696392.png'),
(23, 25, 'extra_6832d739f17eb0.02572629.png'),
(24, 26, 'extra_6832d77bf332b6.57237110.png'),
(25, 26, 'extra_6832d77bf3f206.04026921.png'),
(26, 26, 'extra_6832d77c00ac25.26540864.png'),
(33, 29, 'extra_6832d81e0dde98.26449203.png'),
(34, 29, 'extra_6832d81e0eba77.22206672.png'),
(35, 29, 'extra_6832d81e0f9154.32788662.png'),
(36, 30, 'extra_6832d855b78119.72375024.png'),
(37, 30, 'extra_6832d855b80280.61433803.png'),
(38, 30, 'extra_6832d855b889b9.15344713.png'),
(39, 31, 'extra_6832d8d86597f6.07914068.png'),
(40, 31, 'extra_6832d8d8666592.84143748.png'),
(41, 31, 'extra_6832d8d8671fa0.74592398.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_ratings`
--

CREATE TABLE `product_ratings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_ratings`
--

INSERT INTO `product_ratings` (`id`, `customer_id`, `product_id`, `order_id`, `rating`, `review`, `created_at`, `updated`) VALUES
(1, 1, 31, 40, 5, 'Giày chắc chắn, đi êm chân', '2025-05-26 22:51:45', 1),
(2, 1, 31, 48, 5, 'Mua lại vẫn thấy oke nhó', '2025-05-26 22:52:04', 0),
(3, 1, 23, 30, 5, 'Thoải mái', '2025-05-26 22:54:44', 0),
(4, 1, 30, 48, 5, 'okla', '2025-05-26 23:13:37', 0),
(5, 1, 30, 49, 4, 'mua lại thất vọng', '2025-05-26 23:13:50', 0),
(7, 1, 24, 36, 5, 'oki', '2025-05-26 23:18:14', 0),
(8, 1, 31, 31, 5, 'oker', '2025-05-26 23:18:23', 1),
(9, 2, 31, 50, 5, 'tuyệt', '2025-05-27 00:19:39', 0),
(10, 4, 30, 52, 5, 'Oke\r\nWEb xịn', '2025-05-27 17:02:36', 0),
(11, 5, 31, 53, 5, 'Tuyêth', '2025-05-27 17:03:59', 0),
(12, 1, 31, 49, 5, 'ko', '2025-05-28 21:55:02', 0),
(13, 1, 30, 83, 4, 'okkkkkkk', '2025-06-02 22:20:58', 0),
(14, 5, 30, 89, 5, 'uii', '2025-06-09 21:32:05', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `product_id`, `size`, `quantity`) VALUES
(122, 21, 'S', 100),
(123, 21, 'M', 100),
(124, 21, 'L', 100),
(125, 21, 'XL', 100),
(126, 22, 'S', 100),
(127, 22, 'M', 100),
(128, 22, 'L', 100),
(129, 22, 'XL', 100),
(130, 23, 'S', 96),
(131, 23, 'M', 96),
(132, 23, 'L', 98),
(133, 23, 'XL', 100),
(134, 24, 'S', 49),
(135, 24, 'M', 50),
(136, 24, 'L', 50),
(137, 24, 'XL', 49),
(138, 25, 'S', 99),
(139, 25, 'M', 50),
(140, 25, 'L', 50),
(141, 25, 'XL', 50),
(142, 26, 'S', 49),
(143, 26, 'M', 50),
(144, 26, 'L', 50),
(145, 26, 'XL', 50),
(154, 29, 'S', 40),
(155, 29, 'M', 40),
(156, 29, 'L', 40),
(157, 29, 'XL', 40),
(158, 30, 'S', 7),
(159, 30, 'M', 1),
(160, 30, 'L', 5),
(161, 30, 'XL', 48),
(162, 31, '36', 0),
(163, 31, '37', 0),
(164, 31, '38', 50),
(165, 31, '39', 0),
(166, 31, '40', 50),
(167, 31, '41', 48),
(168, 31, '42', 0),
(169, 31, '43', 0),
(170, 32, '36', 7),
(171, 32, '37', 13),
(172, 32, '38', 12),
(173, 32, '39', 15),
(174, 32, '43', 12);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping_fee`
--

CREATE TABLE `shipping_fee` (
  `province` varchar(100) NOT NULL,
  `fee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `shipping_fee`
--

INSERT INTO `shipping_fee` (`province`, `fee`) VALUES
('An Giang', 20000),
('Bà Rịa - Vũng Tàu', 20000),
('Bắc Giang', 20000),
('Bắc Kạn', 25000),
('Bạc Liêu', 25000),
('Bắc Ninh', 20000),
('Bến Tre', 20000),
('Bình Dương', 20000),
('Bình Định', 20000),
('Bình Phước', 20000),
('Bình Thuận', 20000),
('Cà Mau', 25000),
('Cần Thơ', 20000),
('Cao Bằng', 25000),
('Đà Nẵng', 15000),
('Đắk Lắk', 20000),
('Đắk Nông', 20000),
('Điện Biên', 25000),
('Đồng Nai', 20000),
('Đồng Tháp', 20000),
('Gia Lai', 20000),
('Hà Giang', 25000),
('Hà Nam', 20000),
('Hà Nội', 15000),
('Hà Tĩnh', 20000),
('Hải Dương', 20000),
('Hải Phòng', 15000),
('Hậu Giang', 20000),
('Hòa Bình', 20000),
('Hưng Yên', 20000),
('Khánh Hòa', 20000),
('Kiên Giang', 25000),
('Kon Tum', 20000),
('Lai Châu', 25000),
('Lâm Đồng', 20000),
('Lạng Sơn', 20000),
('Lào Cai', 25000),
('Long An', 20000),
('Nam Định', 20000),
('Nghệ An', 20000),
('Ninh Bình', 20000),
('Ninh Thuận', 20000),
('Phú Thọ', 20000),
('Phú Yên', 20000),
('Quảng Bình', 20000),
('Quảng Nam', 20000),
('Quảng Ngãi', 20000),
('Quảng Ninh', 20000),
('Quảng Trị', 20000),
('Sóc Trăng', 20000),
('Sơn La', 25000),
('Tây Ninh', 20000),
('Thái Bình', 20000),
('Thái Nguyên', 20000),
('Thanh Hóa', 20000),
('Thừa Thiên Huế', 20000),
('Tiền Giang', 20000),
('TP. Hồ Chí Minh', 15000),
('Trà Vinh', 20000),
('Tuyên Quang', 20000),
('Vĩnh Long', 20000),
('Vĩnh Phúc', 20000),
('Yên Bái', 20000);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `shipping_fee`
--
ALTER TABLE `shipping_fee`
  ADD PRIMARY KEY (`province`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Các ràng buộc cho bảng `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `customers` (`id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
