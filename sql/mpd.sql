-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-11-2025 a las 19:04:25
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mpd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_keys`
--

CREATE TABLE `admin_keys` (
  `id` int(11) NOT NULL,
  `admin_key_hash` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_keys`
--

INSERT INTO `admin_keys` (`id`, `admin_key_hash`, `description`, `created_at`) VALUES
(1, '$2y$10$U9mnZPZvYMYa1FsdKEQ7AOXElOCZ9Bbyxge5rH2kmVaqA3QRPL5M6', 'Clave oficial para vendedores que es: 123', '2025-08-13 03:13:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
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
-- Estructura de tabla para la tabla `libros`
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
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `descripcion`, `autor`, `edicion`, `precio`, `categoria`, `foto1`, `foto2`, `created_at`, `updated_at`) VALUES
(12, 'Harry Potter', 'El Mejor', 'JKRowling', 'Tapa Blanda', 45000.00, 'Niños', 'imgs/1763142445_d21b464c93350c6257ee.png', 'imgs/1763142445_35e187207fccd1dc6431.jpg', '2025-11-14 17:46:31', '2025-11-14 17:47:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `descripcion` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_columns`
--

CREATE TABLE `stock_columns` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL DEFAULT 'ingreso',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stock_columns`
--

INSERT INTO `stock_columns` (`id`, `name`, `tipo`, `created_at`) VALUES
(14, 'Ingreso 17/10/2025', 'ingreso', '2025-10-17 17:17:19'),
(17, 'Ingreso 17/10/2025', 'ingreso', '2025-10-17 17:38:33'),
(18, 'Ingreso 17/10/2025', 'ingreso', '2025-10-17 17:38:40'),
(21, 'Ingreso 17/10/2025', 'ingreso', '2025-10-17 17:38:45'),
(23, 'Ingreso 17/10/2025', 'ingreso', '2025-10-17 17:38:49'),
(25, 'Ingreso 17/10/2025', 'ingreso', '2025-10-17 17:39:17'),
(26, 'Egreso 28/10/2025', 'egreso', '2025-10-28 22:47:14'),
(27, 'Egreso 29/10/2025', 'egreso', '2025-10-29 18:59:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_values`
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
-- Volcado de datos para la tabla `stock_values`
--

INSERT INTO `stock_values` (`id`, `column_id`, `libro_id`, `cantidad`, `created_at`, `updated_at`) VALUES
(150, 25, 12, 2, '2025-11-14 17:48:46', '2025-11-14 17:48:47'),
(151, 26, 12, 4, '2025-11-14 17:48:49', '2025-11-14 17:48:55'),
(152, 14, 12, 0, '2025-11-14 17:53:25', '2025-11-14 17:54:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('administrador','comprador') NOT NULL DEFAULT 'comprador',
  `foto_perfil` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `foto_perfil`, `created_at`) VALUES
(6, 'Pepe higienica', 'pepepepito@gmail.coma', '$2y$10$c3LxObTjaEQWa3xQ94E.K.IuFwzkw/xjNDSq.u7jgRLzmoArb2lTu', 'administrador', '6_f8971b8a2716fba8.webp', '2025-08-29 21:17:40'),
(12, 'holacomote', 'chau@gmail.com', '$2y$10$55EbjeGPMtKrglYAIUQD3eoIgSWRkxX3XZpef87qGZch./SKujuou', 'comprador', NULL, '2025-09-17 03:23:50'),
(13, 'Sebastián', 'emanuelrissopatron@alumnos.itr3.edu.ar', '$2y$10$HE2DAetZgqmUs3G03Fs8JOWRdRtawngliR4fOH0mSJKSjgVBGsQ86', 'comprador', NULL, '2025-10-15 00:15:09'),
(14, 'Emyyy', 'emarissopatron@gmail.com', '$2y$10$niAIS6PqdZI5E2yZ0CF28.kaf1OF5zRS2RGMnrgRzD7bffW6H/zIi', 'administrador', NULL, '2025-10-15 00:21:22'),
(15, 'JuanJosePaso', 'paso@gmail.com', '$2y$10$UdMxCij8db.pbazKLTLDW.UFi8hshwDbg.0fg8SUxudTGK6lJu0bO', 'comprador', NULL, '2025-10-22 19:26:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `venta_id` int(11) NOT NULL,
  `comprador_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `monto_venta` decimal(10,2) DEFAULT NULL,
  `fecha_de_pago` date NOT NULL,
  `met_pago` enum('efectivo','transferencia') NOT NULL DEFAULT 'efectivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`venta_id`, `comprador_id`, `libro_id`, `cantidad`, `monto_venta`, `fecha_de_pago`, `met_pago`) VALUES
(74, 12, 1, 2, 246.00, '2024-11-17', 'efectivo'),
(75, 12, 3, 3, 12000.00, '2024-11-17', 'efectivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_detalle`
--

CREATE TABLE `ventas_detalle` (
  `detalle_id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_maestro`
--

CREATE TABLE `ventas_maestro` (
  `venta_id` int(11) NOT NULL,
  `comprador_id` int(11) NOT NULL,
  `fecha_de_pago` date NOT NULL,
  `total_venta` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_keys`
--
ALTER TABLE `admin_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`user_id`,`libro_id`),
  ADD KEY `carrito_ibfk_2` (`libro_id`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_libro` (`user_id`,`libro_id`),
  ADD KEY `libro_id` (`libro_id`);

--
-- Indices de la tabla `stock_columns`
--
ALTER TABLE `stock_columns`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `stock_values`
--
ALTER TABLE `stock_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_col_lib` (`column_id`,`libro_id`),
  ADD KEY `fk_sv_lib` (`libro_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`venta_id`),
  ADD KEY `comprador_id` (`comprador_id`),
  ADD KEY `libro_id` (`libro_id`);

--
-- Indices de la tabla `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  ADD PRIMARY KEY (`detalle_id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `libro_id` (`libro_id`);

--
-- Indices de la tabla `ventas_maestro`
--
ALTER TABLE `ventas_maestro`
  ADD PRIMARY KEY (`venta_id`),
  ADD KEY `comprador_id` (`comprador_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_keys`
--
ALTER TABLE `admin_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `stock_columns`
--
ALTER TABLE `stock_columns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `stock_values`
--
ALTER TABLE `stock_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `venta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  MODIFY `detalle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas_maestro`
--
ALTER TABLE `ventas_maestro`
  MODIFY `venta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD CONSTRAINT `resenas_ibfk_1` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resenas_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `stock_values`
--
ALTER TABLE `stock_values`
  ADD CONSTRAINT `fk_sv_col` FOREIGN KEY (`column_id`) REFERENCES `stock_columns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sv_lib` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  ADD CONSTRAINT `ventas_detalle_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas_maestro` (`venta_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_detalle_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`);

--
-- Filtros para la tabla `ventas_maestro`
--
ALTER TABLE `ventas_maestro`
  ADD CONSTRAINT `ventas_maestro_ibfk_1` FOREIGN KEY (`comprador_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
