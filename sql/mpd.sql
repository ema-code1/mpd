-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 06:22 PM
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
-- Database: `mpd`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_keys`
--

CREATE TABLE `admin_keys` (
  `id` int(11) NOT NULL,
  `admin_key_hash` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_keys`
--

INSERT INTO `admin_keys` (`id`, `admin_key_hash`, `description`, `created_at`) VALUES
(1, '$2y$10$U9mnZPZvYMYa1FsdKEQ7AOXElOCZ9Bbyxge5rH2kmVaqA3QRPL5M6', 'Clave oficial para vendedores que es: 123', '2025-08-13 03:13:46');

-- --------------------------------------------------------

--
-- Table structure for table `carrito`
--

CREATE TABLE `carrito` (
  `user_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `seleccionado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=seleccionado para comprar, 0=no seleccionado',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `libros`
--

CREATE TABLE `libros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `autor` varchar(255) NOT NULL,
  `edicion` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `foto1` varchar(255) DEFAULT NULL,
  `foto2` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `descripcion`, `autor`, `edicion`, `precio`, `categoria`, `foto1`, `foto2`, `created_at`, `updated_at`) VALUES
(1, 'El quijote de la mancha', 'ohola coomo te va abuelos de los todos como te va como te va', 'Jose Mangold', 'De bolsillo', 123.00, 'Fixion', 'imgs/1757697604_adf71dcd8ef402ce55c1.webp', NULL, '2025-09-01 22:06:13', '2025-10-14 20:26:56'),
(2, 'Blue label', 'Hola don pepe hola don jose', 'Jose Mangold', 'tapa blanda', 123123.00, 'Histioria', 'imgs/1759877814_88fb913e3a4d256f3072.png', NULL, '2025-09-05 22:39:34', '2025-10-17 15:26:11'),
(3, 'Black Label', 'En algun lugar de la mancha', 'Juan Jose Paso', 'Tapa de madera', 4000.00, 'cp', 'imgs/1757697583_a30de170b3c1ccfec668.jpg', 'imgs/1757697583_275ad732509d2300e1ce.jpg', '2025-09-05 22:47:50', '2025-10-17 15:58:18'),
(5, 'asdas', 'asdasdada', 'asdad', 'sad', 23423.00, '2342356etzegzfdgzgfzdg', 'imgs/1757369912_aa4d20de5e518e9db20e.jpg', NULL, '2025-09-08 22:18:32', '2025-10-17 15:50:14'),
(7, 'juancho', 'juancho', 'juancho', 'juancho', 123.00, 'juancho', 'imgs/1757698850_fa8014dc015cd3b02086.png', NULL, '2025-09-12 17:40:50', '2025-10-14 20:27:09'),
(8, 'LIibro', 'HOla descricopj cono te va', 'Lionel MESSI CUCCITINI', 'de boldisioo', 123345.00, 'videojuegos', 'imgs/1757714194_9a39a53fb0ab1b4dbc3b.jpg', 'imgs/1757714194_e08337e6baf7b039a114.jpg', '2025-09-12 21:56:34', '2025-10-14 20:27:12');

-- --------------------------------------------------------

--
-- Table structure for table `montos`
--

CREATE TABLE `montos` (
  `id` int(11) NOT NULL,
  `monto_enpesos` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `montos`
--

INSERT INTO `montos` (`id`, `monto_enpesos`) VALUES
(1, 17123.00),
(2, 15200.00),
(3, 23700.50),
(4, 19800.75),
(5, 34100.25),
(6, 28950.00),
(7, 41200.00),
(8, 37850.00),
(9, 25100.00),
(10, 29500.00),
(11, 54246.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_columns`
--

CREATE TABLE `stock_columns` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL DEFAULT 'ingreso',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_columns`
--

INSERT INTO `stock_columns` (`id`, `name`, `tipo`, `created_at`) VALUES
(12, 'Ingreso 10/17/2025', 'ingreso', '2025-10-17 16:16:31'),
(13, 'Ingreso 10/17/2025', 'ingreso', '2025-10-17 16:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `stock_values`
--

CREATE TABLE `stock_values` (
  `id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_values`
--

INSERT INTO `stock_values` (`id`, `column_id`, `libro_id`, `cantidad`, `created_at`, `updated_at`) VALUES
(59, 13, 1, 0, '2025-10-17 16:19:52', '2025-10-17 16:19:52'),
(60, 13, 2, 0, '2025-10-17 16:19:52', '2025-10-17 16:19:52'),
(61, 13, 3, 0, '2025-10-17 16:19:52', '2025-10-17 16:19:52'),
(62, 13, 5, 0, '2025-10-17 16:19:52', '2025-10-17 16:19:52'),
(63, 13, 7, 0, '2025-10-17 16:19:52', '2025-10-17 16:19:52'),
(64, 13, 8, 0, '2025-10-17 16:19:52', '2025-10-17 16:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('administrador','comprador') NOT NULL DEFAULT 'comprador',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(6, 'pepe honguito', 'pepechotito@gmail.hotmail.hot', '$2y$10$PI9Alg6wn87cM2ZVpymnCu6XDj3Pfnb1Bl07bRE6UB7TwEeXX5l32', 'administrador', '2025-08-29 21:17:40'),
(12, 'holacomote', 'chau@gmail.com', '$2y$10$55EbjeGPMtKrglYAIUQD3eoIgSWRkxX3XZpef87qGZch./SKujuou', 'comprador', '2025-09-17 03:23:50'),
(13, 'Sebastián', 'emanuelrissopatron@alumnos.itr3.edu.ar', '$2y$10$HE2DAetZgqmUs3G03Fs8JOWRdRtawngliR4fOH0mSJKSjgVBGsQ86', 'comprador', '2025-10-15 00:15:09'),
(14, 'Ema', 'emarissopatron@gmail.com', '$2y$10$niAIS6PqdZI5E2yZ0CF28.kaf1OF5zRS2RGMnrgRzD7bffW6H/zIi', 'administrador', '2025-10-15 00:21:22');

-- --------------------------------------------------------

--
-- Table structure for table `ventas`
--

CREATE TABLE `ventas` (
  `venta_id` int(11) NOT NULL,
  `comprador_id` int(11) NOT NULL,
  `nombre_comprador` varchar(100) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `monto_id` int(11) NOT NULL,
  `fecha_de_pago` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ventas`
--

INSERT INTO `ventas` (`venta_id`, `comprador_id`, `nombre_comprador`, `libro_id`, `monto_id`, `fecha_de_pago`) VALUES
(1, 6, 'pepe honguito', 1, 1, '2025-10-09'),
(2, 12, 'holacomote', 2, 2, '2025-01-10'),
(3, 12, 'holacomote', 5, 3, '2025-02-14'),
(4, 12, 'holacomote', 7, 4, '2025-03-07'),
(5, 12, 'holacomote', 8, 5, '2025-05-21'),
(6, 12, 'holacomote', 1, 6, '2025-06-11'),
(7, 12, 'holacomote', 2, 7, '2025-07-19'),
(8, 12, 'holacomote', 3, 8, '2025-08-05'),
(9, 12, 'holacomote', 5, 9, '2025-09-23'),
(10, 12, 'holacomote', 7, 10, '2025-10-02'),
(11, 12, 'holacomote', 8, 11, '2025-11-15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_keys`
--
ALTER TABLE `admin_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`user_id`,`libro_id`),
  ADD KEY `carrito_ibfk_2` (`libro_id`);

--
-- Indexes for table `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `montos`
--
ALTER TABLE `montos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_columns`
--
ALTER TABLE `stock_columns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_values`
--
ALTER TABLE `stock_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_col_lib` (`column_id`,`libro_id`),
  ADD KEY `fk_sv_lib` (`libro_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`venta_id`),
  ADD KEY `comprador_id` (`comprador_id`),
  ADD KEY `libro_id` (`libro_id`),
  ADD KEY `monto_id` (`monto_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_keys`
--
ALTER TABLE `admin_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `montos`
--
ALTER TABLE `montos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `stock_columns`
--
ALTER TABLE `stock_columns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `stock_values`
--
ALTER TABLE `stock_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ventas`
--
ALTER TABLE `ventas`
  MODIFY `venta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_values`
--
ALTER TABLE `stock_values`
  ADD CONSTRAINT `fk_sv_col` FOREIGN KEY (`column_id`) REFERENCES `stock_columns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sv_lib` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`comprador_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`),
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`monto_id`) REFERENCES `montos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
