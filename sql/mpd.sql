-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-11-2025 a las 20:33:14
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

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`user_id`, `libro_id`, `cantidad`, `seleccionado`, `created_at`) VALUES
(12, 1, 3, 1, '2025-10-22 16:22:19'),
(12, 5, 1, 1, '2025-10-22 16:22:15'),
(13, 3, 2, 1, '2025-11-05 16:26:20'),
(13, 10, 2, 1, '2025-10-28 19:51:17'),
(15, 2, 1, 1, '2025-10-22 16:26:38'),
(15, 7, 2, 1, '2025-10-22 16:26:34'),
(15, 8, 2, 1, '2025-10-22 16:26:30');

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
(1, 'El quijote de la mancha', 'ohola coomo te va abuelos de los todos como te va como te va', 'Jose Mangold', 'De bolsillo', 123.00, 'Fixion', 'imgs/1757697604_adf71dcd8ef402ce55c1.webp', NULL, '2025-09-01 22:06:13', '2025-10-14 20:26:56'),
(2, 'Blue label', 'Hola don pepe hola don jose', 'Jose Mangold', 'tapa blanda', 123123.00, 'Histioria', 'imgs/1759877814_88fb913e3a4d256f3072.png', NULL, '2025-09-05 22:39:34', '2025-10-17 15:26:11'),
(3, 'Black Label', 'En algun lugar de la mancha', 'Juan Jose Paso', 'Tapa de madera', 4000.00, 'cp', 'imgs/1757697583_a30de170b3c1ccfec668.jpg', 'imgs/1757697583_275ad732509d2300e1ce.jpg', '2025-09-05 22:47:50', '2025-10-17 15:58:18'),
(5, 'asdas', 'asdasdada', 'asdad', 'sad', 23423.00, '2342356etzegzfdgzgfzdg', 'imgs/1757369912_aa4d20de5e518e9db20e.jpg', NULL, '2025-09-08 22:18:32', '2025-10-17 15:50:14'),
(7, 'juancho', 'juancho', 'juancho', 'juancho', 123.00, 'juancho', 'imgs/1757698850_fa8014dc015cd3b02086.png', NULL, '2025-09-12 17:40:50', '2025-10-14 20:27:09'),
(8, 'LIibro', 'HOla descricopj cono te va', 'Lionel MESSI CUCCITINI', 'de boldisioo', 123345.00, 'videojuegos', 'imgs/1757714194_9a39a53fb0ab1b4dbc3b.jpg', 'imgs/1757714194_e08337e6baf7b039a114.jpg', '2025-09-12 21:56:34', '2025-10-14 20:27:12'),
(10, 'Harry Potterrrrrr', 'El mejoprrr', 'JKRowlin', '', 40000.00, 'Niños', 'imgs/1761691780_47f3092329fb3fb02f31.png', 'imgs/1761691780_53f71907c25957285efd.png', '2025-10-28 22:49:40', '2025-10-28 22:50:52');

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

--
-- Volcado de datos para la tabla `resenas`
--

INSERT INTO `resenas` (`id`, `libro_id`, `user_id`, `rating`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 3, 6, 3, 'asdadadad{<sdkfj<ñoidfjh<iosfdujh<spoifhj<spoifj<spofij<sadf', '2025-10-21 17:34:09', NULL),
(5, 2, 6, 2, '<ñkjflñ<kjh.<kjfh.zskdjfhnzskljdfnskdjfnsdfjnskfjnslfknslfsfsfsfsfsfsfs', '2025-10-21 18:53:57', NULL),
(6, 2, 13, 4, 'Hellooooo. Es la mejorr.', '2025-10-28 19:15:36', NULL),
(7, 10, 14, 5, 'jota Ka Rowlin', '2025-10-28 19:50:23', NULL);

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
(65, 14, 1, 0, '2025-10-17 17:17:19', '2025-10-17 17:17:19'),
(66, 14, 2, 0, '2025-10-17 17:17:19', '2025-10-17 17:17:19'),
(67, 14, 3, 3, '2025-10-17 17:17:19', '2025-10-17 17:29:21'),
(68, 14, 5, 0, '2025-10-17 17:17:19', '2025-10-17 17:37:27'),
(69, 14, 7, 0, '2025-10-17 17:17:19', '2025-10-17 17:17:19'),
(70, 14, 8, 2, '2025-10-17 17:17:19', '2025-10-17 18:12:57'),
(83, 17, 1, 0, '2025-10-17 17:38:33', '2025-10-17 17:38:33'),
(84, 17, 2, 0, '2025-10-17 17:38:33', '2025-10-17 17:38:33'),
(85, 17, 3, 0, '2025-10-17 17:38:33', '2025-10-17 17:38:33'),
(86, 17, 5, 0, '2025-10-17 17:38:33', '2025-10-17 17:38:33'),
(87, 17, 7, 0, '2025-10-17 17:38:33', '2025-10-17 17:38:33'),
(88, 17, 8, 0, '2025-10-17 17:38:33', '2025-10-17 17:38:33'),
(89, 18, 1, 0, '2025-10-17 17:38:40', '2025-10-17 17:38:40'),
(90, 18, 2, 0, '2025-10-17 17:38:40', '2025-10-17 17:38:40'),
(91, 18, 3, 0, '2025-10-17 17:38:40', '2025-10-17 17:38:40'),
(92, 18, 5, 0, '2025-10-17 17:38:40', '2025-10-17 17:38:40'),
(93, 18, 7, 0, '2025-10-17 17:38:40', '2025-10-17 17:38:40'),
(94, 18, 8, 0, '2025-10-17 17:38:40', '2025-10-17 17:38:40'),
(107, 21, 1, 0, '2025-10-17 17:38:45', '2025-10-17 17:38:45'),
(108, 21, 2, 0, '2025-10-17 17:38:45', '2025-10-17 17:38:45'),
(109, 21, 3, 0, '2025-10-17 17:38:45', '2025-10-17 17:38:45'),
(110, 21, 5, 0, '2025-10-17 17:38:45', '2025-10-17 17:40:02'),
(111, 21, 7, 0, '2025-10-17 17:38:45', '2025-10-17 17:38:45'),
(112, 21, 8, 0, '2025-10-17 17:38:45', '2025-10-17 17:38:45'),
(119, 23, 1, 0, '2025-10-17 17:38:49', '2025-10-17 17:38:49'),
(120, 23, 2, 0, '2025-10-17 17:38:49', '2025-10-17 17:38:49'),
(121, 23, 3, 1, '2025-10-17 17:38:49', '2025-10-28 22:46:01'),
(122, 23, 5, 2, '2025-10-17 17:38:49', '2025-10-29 18:57:25'),
(123, 23, 7, 0, '2025-10-17 17:38:49', '2025-10-17 17:38:49'),
(124, 23, 8, 0, '2025-10-17 17:38:49', '2025-10-17 17:38:49'),
(131, 25, 1, 0, '2025-10-17 17:39:17', '2025-10-17 17:39:17'),
(132, 25, 2, 0, '2025-10-17 17:39:17', '2025-10-17 17:39:17'),
(133, 25, 3, 6, '2025-10-17 17:39:17', '2025-10-17 17:39:34'),
(134, 25, 5, 5, '2025-10-17 17:39:17', '2025-10-17 17:39:30'),
(135, 25, 7, 0, '2025-10-17 17:39:17', '2025-10-17 17:39:17'),
(136, 25, 8, 0, '2025-10-17 17:39:17', '2025-10-17 17:39:17'),
(137, 26, 1, 0, '2025-10-28 22:47:14', '2025-10-28 22:47:14'),
(138, 26, 2, 0, '2025-10-28 22:47:14', '2025-10-28 22:47:14'),
(139, 26, 3, 0, '2025-10-28 22:47:14', '2025-10-28 22:47:14'),
(140, 26, 5, 3, '2025-10-28 22:47:14', '2025-10-28 22:47:29'),
(141, 26, 7, 0, '2025-10-28 22:47:14', '2025-10-28 22:47:14'),
(142, 26, 8, 0, '2025-10-28 22:47:14', '2025-10-28 22:47:14'),
(143, 27, 1, 0, '2025-10-29 18:59:02', '2025-10-29 18:59:02'),
(144, 27, 2, 0, '2025-10-29 18:59:02', '2025-10-29 18:59:02'),
(145, 27, 3, 0, '2025-10-29 18:59:02', '2025-10-29 18:59:02'),
(146, 27, 5, 0, '2025-10-29 18:59:02', '2025-10-29 18:59:02'),
(147, 27, 7, 0, '2025-10-29 18:59:02', '2025-10-29 18:59:02'),
(148, 27, 8, 0, '2025-10-29 18:59:02', '2025-10-29 18:59:02'),
(149, 27, 10, 0, '2025-10-29 18:59:02', '2025-10-29 18:59:02');

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
  `fecha_de_pago` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`venta_id`, `comprador_id`, `libro_id`, `cantidad`, `monto_venta`, `fecha_de_pago`) VALUES
(1, 6, 1, 1, 17123.00, '2025-10-09'),
(2, 12, 2, 1, 15200.00, '2025-01-10'),
(3, 12, 5, 1, 23700.50, '2025-02-14'),
(4, 12, 7, 1, 19800.75, '2025-03-07'),
(5, 12, 8, 1, 34100.25, '2025-05-21'),
(6, 12, 1, 1, 28950.00, '2025-06-11'),
(7, 12, 2, 1, 41200.00, '2025-07-19'),
(8, 12, 3, 1, 37850.00, '2025-08-05'),
(9, 12, 5, 1, 25100.00, '2025-09-23'),
(10, 12, 7, 1, 29500.00, '2025-10-02'),
(11, 12, 8, 1, 54246.00, '2025-11-15');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `venta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`comprador_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
