-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 21, 2025 lúc 04:49 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `vnvc`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `address` text NOT NULL,
  `notes` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `fullname`, `phone`, `email`, `booking_date`, `address`, `notes`, `total_amount`, `status`, `created_at`) VALUES
(1, 12, 'lê anh kiệt', '0389429503', 'voanh15052@gmail.com', '2025-04-19', 'khu ohoos 9, tt hà lam', 'gần nhà văn hoá', 1900000.00, 'pending', '2025-04-18 12:33:45'),
(2, 14, 'anhkim', '03338641911', '123@gmail.com', '2025-04-21', 'kp2', 'gần 22', 3330000.00, 'cancelled', '2025-04-20 12:44:42'),
(3, 14, 'anhkim', '03338641911', '123@gmail.com', '2025-04-21', 'khu ohoos 9, tt hà lam', 'hần hotel\r\n', 1000000.00, 'cancelled', '2025-04-20 18:24:55'),
(4, 14, 'anhkim', '03338641911', '123@gmail.com', '2025-04-21', 'khu ohoos 9, tt hà lam', '12', 1000000.00, 'cancelled', '2025-04-20 18:26:24'),
(5, 14, 'anhkim', '03338641911', '123@gmail.com', '2025-04-22', 'khu ohoos 9, tt hà lam', '1', 980000.00, 'pending', '2025-04-21 02:36:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_items`
--

CREATE TABLE `booking_items` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `vaccine_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_items`
--

INSERT INTO `booking_items` (`id`, `booking_id`, `vaccine_id`, `vaccine_name`, `price`, `quantity`) VALUES
(1, 1, 2, 'Vắc xin 5 trong 1 Pentaxim', 1100000.00, 1),
(2, 1, 12, 'Vắc xin rota Rotarix', 800000.00, 1),
(3, 2, 1, 'Vắc xin 6 trong 1 Infanrix Hexa', 1350000.00, 1),
(4, 2, 2, 'Vắc xin 5 trong 1 Pentaxim', 1100000.00, 1),
(5, 2, 8, 'Vắc xin thủy đậu Varivax', 700000.00, 1),
(6, 2, 5, 'Vắc xin viêm gan B Engerix-B', 180000.00, 1),
(7, 3, 13, 'Vắc xin viêm màng não mô cầu Menactra', 1000000.00, 1),
(8, 4, 13, 'Vắc xin viêm màng não mô cầu Menactra', 1000000.00, 1),
(9, 5, 6, 'Vắc xin sởi - quai bị - rubella MMR II', 280000.00, 1),
(10, 5, 8, 'Vắc xin thủy đậu Varivax', 700000.00, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `login_time`, `ip_address`, `user_agent`) VALUES
(1, 11, '2025-04-18 09:03:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(2, 12, '2025-04-18 12:32:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(3, 13, '2025-04-18 12:40:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(4, 14, '2025-04-19 03:37:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(5, 14, '2025-04-19 03:41:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(6, 14, '2025-04-19 04:22:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(7, 14, '2025-04-19 08:34:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(8, 14, '2025-04-20 11:32:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(9, 14, '2025-04-20 11:33:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(10, 14, '2025-04-20 17:32:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(12, 14, '2025-04-20 18:59:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(13, 14, '2025-04-21 02:35:22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(14, 14, '2025-04-21 02:52:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(15, 14, '2025-04-21 03:24:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(16, 16, '2025-04-21 03:26:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(17, 16, '2025-04-21 03:34:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(18, 16, '2025-04-21 03:41:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'),
(19, 16, '2025-04-21 04:11:53', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vaccine_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `vaccine_id`, `quantity`, `total_price`, `order_date`) VALUES
(1, 1, 1, 2, 2700000.00, '2025-04-18 05:52:00'),
(2, 2, 3, 1, 2200000.00, '2025-04-18 05:52:00'),
(3, 3, 5, 3, 540000.00, '2025-04-18 05:52:00'),
(4, 4, 7, 1, 950000.00, '2025-04-18 05:52:00'),
(5, 5, 9, 2, 900000.00, '2025-04-18 05:52:00'),
(6, 6, 2, 1, 1100000.00, '2025-04-18 05:52:00'),
(7, 7, 4, 5, 1750000.00, '2025-04-18 05:52:00'),
(8, 8, 6, 2, 560000.00, '2025-04-18 05:52:00'),
(9, 9, 8, 1, 700000.00, '2025-04-18 05:52:00'),
(10, 1, 10, 1, 600000.00, '2025-04-18 05:52:00'),
(11, 2, 12, 2, 1600000.00, '2025-04-18 05:52:00'),
(12, 3, 14, 1, 300000.00, '2025-04-18 05:52:00'),
(13, 4, 16, 3, 750000.00, '2025-04-18 05:52:00'),
(14, 5, 18, 1, 900000.00, '2025-04-18 05:52:00'),
(15, 6, 20, 2, 2400000.00, '2025-04-18 05:52:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `created_at`, `role`) VALUES
(1, 'admin', '$2y$10$7rLSfS32c1Iu/9a7oN3Giu7aK3QzF3nA3eF4g5h6i7j8k9l0m1n2o', 'admin@vnvc.vn', 'Quản Trị Viên', '2025-04-18 05:52:00', 'user'),
(2, 'user1', '$2y$10$8sMTfT43d2J3k4l5m6n7ou8bK4RzG5nB6eF7g8h9i0j1k2l3m4n5o', 'user1@vnvc.vn', 'Nguyễn Văn An', '2025-04-18 05:52:00', 'user'),
(3, 'user2', '$2y$10$9tNUgU54e3K4l5m6n7o8pu9cK5RzH6nC7eF8g9i0j1k2l3m4n5o6p', 'user2@vnvc.vn', 'Trần Thị Bình', '2025-04-18 05:52:00', 'user'),
(4, 'user3', '$2y$10$0uOVhV65f4L5m6n7o8q9rv0dK6RzI7nD8eF9g0i1j2k3l4m5n6o7q', 'user3@vnvc.vn', 'Lê Minh Châu', '2025-04-18 05:52:00', 'user'),
(5, 'user4', '$2y$10$1vPWiW76g5M6n7o8p9s0tw1eK7RzJ8nE9eF0g1i2j3k4l5m6n7o8r', 'user4@vnvc.vn', 'Phạm Quốc Dũng', '2025-04-18 05:52:00', 'user'),
(6, 'user5', '$2y$10$2wQXjX87h6N7o8p9q0u1vx2fK8RzK9nF0eF1g2i3j4k5l6m7n8o9s', 'user5@vnvc.vn', 'Hoàng Thị Em', '2025-04-18 05:52:00', 'user'),
(7, 'user6', '$2y$10$3xRYkY98i7O8p9q0r1v2wy3gK9RzL0nG1eF2g3i4j5k6l7m8n9o0t', 'user6@vnvc.vn', 'Đặng Văn Phong', '2025-04-18 05:52:00', 'user'),
(8, 'user7', '$2y$10$4ySZlZ09j8P9q0r1s2w3xz4hK0RzM1nH2eF3g4i5j6k7l8m9n0o1u', 'user7@vnvc.vn', 'Vũ Thị Giang', '2025-04-18 05:52:00', 'user'),
(9, 'user8', '$2y$10$5zTAmA10k9Q0r1s2t3x4yz5iK1RzN2nI3eF4g5i6j7k8l9m0n1o2v', 'user8@vnvc.vn', 'Bùi Minh Hiếu', '2025-04-18 05:52:00', 'user'),
(10, 'user9', '$2y$10$6aUBnB21l0R1s2t3u4y5z06jK2RzO3nJ4eF5g6i7j8k9l0m1n2o3w', 'user9@vnvc.vn', 'Ngô Thị In', '2025-04-18 05:52:00', 'user'),
(11, 'Cuong', '$2y$10$EErU8HgVM.p6DXOfPIE8ieMAJg050bc0Z33ZBS9I7DXD6c3hMcRHq', 'voanhkim228@gamil.com', 'Võ Thị Kim Cương', '2025-04-18 02:03:05', 'user'),
(12, 'anhkiet', '$2y$10$Tf3FqduJ37dVBd6R3YAjLuTf9dgLgn5oHXuHch2eIGSWXDAp..12C', 'voanh15052@gmail.com', 'lê anh kiệt', '2025-04-18 05:31:34', 'user'),
(13, 'duyen', '$2y$10$4rQkVR7LCl8Fg0mwrf7tJ.fbDfRsWlAmqJ7NtuvkQCHJc3x9k5lx.', 'cuongvtk2506@ut.edu.vn', 'huynh thi', '2025-04-18 05:40:18', 'user'),
(14, 'kim', '$2y$10$W4EAiIVuI8dHvedf.vRK0ObmIR3wJgWZgIbvZ/B7V.826gMsuNV1W', '123@gmail.com', 'anhkim', '2025-04-18 20:37:18', 'user'),
(16, 'minhhuy', '$2y$10$CPiB.b3IZ0OSwitupDa45.Vjx00ECZcEaUHQFSyXNA6ZRXAoD6Zkq', '123456@gmail.com', 'minhhuy', '2025-04-20 20:26:22', 'admin');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaccines`
--

CREATE TABLE `vaccines` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `disease_prevented` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `vaccines`
--

INSERT INTO `vaccines` (`id`, `name`, `origin`, `price`, `disease_prevented`, `category`) VALUES
(1, 'Vắc xin 6 trong 1 Infanrix Hexa', 'Bỉ', 1350000.00, 'Bạch hầu, Ho gà, Uốn ván, Bại liệt, Hib, Viêm gan B', 'Vắc xin cho trẻ em'),
(2, 'Vắc xin 5 trong 1 Pentaxim', 'Pháp', 1100000.00, 'Bạch hầu, Ho gà, Uốn ván, Bại liệt, Hib', 'Vắc xin cho trẻ em'),
(3, 'Vắc xin HPV Gardasil', 'Mỹ', 2200000.00, 'Ung thư cổ tử cung, Sùi mào gà', 'Vắc xin cho tuổi vị thành niên và thanh niên'),
(4, 'Vắc xin cúm Vaxigrip Tetra', 'Pháp', 350000.00, 'Cúm mùa', 'Vắc xin cho người trưởng thành'),
(5, 'Vắc xin viêm gan B Engerix-B', 'Bỉ', 180000.00, 'Viêm gan B', 'Vắc xin cho trẻ em'),
(6, 'Vắc xin sởi - quai bị - rubella MMR II', 'Mỹ', 280000.00, 'Sởi, Quai bị, Rubella', 'Vắc xin cho trẻ em tiền học đường'),
(7, 'Vắc xin phế cầu Synflorix', 'Bỉ', 950000.00, 'Viêm phổi, Viêm màng não do phế cầu', 'Vắc xin cho trẻ em'),
(8, 'Vắc xin thủy đậu Varivax', 'Mỹ', 700000.00, 'Thủy đậu', 'Vắc xin cho trẻ em tiền học đường'),
(9, 'Vắc xin viêm não Nhật Bản Imojev', 'Thái Lan', 450000.00, 'Viêm não Nhật Bản', 'Vắc xin cho trẻ em'),
(10, 'Vắc xin uốn ván - bạch hầu - ho gà Boostrix', 'Bỉ', 600000.00, 'Uốn ván, Bạch hầu, Ho gà', 'Vắc xin cho người trưởng thành'),
(11, 'Vắc xin viêm gan A Havrix', 'Bỉ', 500000.00, 'Viêm gan A', 'Vắc xin cho trẻ em tiền học đường'),
(12, 'Vắc xin rota Rotarix', 'Bỉ', 800000.00, 'Tiêu chảy do Rotavirus', 'Vắc xin cho trẻ em'),
(13, 'Vắc xin viêm màng não mô cầu Menactra', 'Mỹ', 1000000.00, 'Viêm màng não mô cầu', 'Vắc xin cho tuổi vị thành niên và thanh niên'),
(14, 'Vắc xin dại Verorab', 'Pháp', 300000.00, 'Bệnh dại', 'Vắc xin cho người trưởng thành'),
(15, 'Vắc xin thương hàn Typhim Vi', 'Pháp', 400000.00, 'Thương hàn', 'Vắc xin cho người trưởng thành'),
(16, 'Vắc xin viêm não mô cầu BC Va-Mengoc-BC', 'Cuba', 250000.00, 'Viêm màng não mô cầu B+C', 'Vắc xin cho trẻ em'),
(17, 'Vắc xin lao BCG', 'Việt Nam', 100000.00, 'Lao', 'Vắc xin cho trẻ em'),
(18, 'Vắc xin 4 trong 1 Tetraxim', 'Pháp', 900000.00, 'Bạch hầu, Ho gà, Uốn ván, Bại liệt', 'Vắc xin cho trẻ em'),
(19, 'Vắc xin viêm gan A+B Twinrix', 'Bỉ', 850000.00, 'Viêm gan A, Viêm gan B', 'Vắc xin cho người trưởng thành'),
(20, 'Vắc xin phế cầu Prevenar 13', 'Anh', 1200000.00, 'Viêm phổi, Viêm màng não do phế cầu', 'Vắc xin cho trẻ em'),
(22, 'VẮC XIN PHÒNG BỆNH HO GÀ - BẠCH HẦU - UỐN VÁN ADACEL', 'Sanofi (Pháp)', 650000.00, 'Ho gà, Bạch hầu, Uốn ván', 'Vắc xin cho phụ nữ chuẩn bị trước mang thai');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `booking_items`
--
ALTER TABLE `booking_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Chỉ mục cho bảng `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vaccine_id` (`vaccine_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `booking_items`
--
ALTER TABLE `booking_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `booking_items`
--
ALTER TABLE `booking_items`
  ADD CONSTRAINT `booking_items_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Các ràng buộc cho bảng `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
