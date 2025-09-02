-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-08-2025 a las 02:49:07
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
(1, '$2y$10$U9mnZPZvYMYa1FsdKEQ7AOXElOCZ9Bbyxge5rH2kmVaqA3QRPL5M6', 'Clave oficial para vendedores que es: 123', '2025-08-13 00:13:46');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Emanuel Risso Patron', 'emarissopatron@gmail.com', '$2y$10$gv0geuLtNP7pyj2/p8McpO87XOSjH2aW1J/UPDiKQxrY4H7y8WfCm', 'administrador', '2025-08-19 21:19:43'),
(2, 'Sebastián Emanuel Risso Patron', 'emanuelrissopatron@alumnos.itr3.edu.ar', '$2y$10$4tbXIcQpDjuJJUK9qIfBU.Ao6H3qCgRLggP9dNxj9yTTeOGiPoZCi', 'comprador', '2025-08-19 21:24:16'),
(3, 'fran', 'rissonefran@gmail.com', '$2y$10$FGoU1y18heJuSl8wE2y5uenRK3RXWi9167CfR.2fY9XVdAlGvdMa2', 'administrador', '2025-08-19 22:17:07'),
(4, 'fran2', 'rissonefran2@gmail.com', '$2y$10$nbddOe8Aw16ggsBHJBNEQeccoRc/0ybI4VbqW6vxO2EMfn6OWBTh6', 'comprador', '2025-08-19 22:18:18'),
(5, 'caca', 'caca@gmail.com', '$2y$10$18c80DlHCv12iXTxRq0omONNGNTxrE4S0n78eWAAkURdWlDCaTPIO', 'comprador', '2025-08-27 00:35:58');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_keys`
--
ALTER TABLE `admin_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_keys`
--
ALTER TABLE `admin_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
