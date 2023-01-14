-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 14-01-2023 a las 15:22:06
-- Versión del servidor: 10.4.21-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `legaltech`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comparendos`
--

CREATE TABLE `comparendos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `transito` varchar(50) NOT NULL,
  `numCompa` varchar(50) NOT NULL,
  `fecha_comp` date NOT NULL,
  `lugar` varchar(60) NOT NULL,
  `vehiculo` varchar(100) NOT NULL,
  `servicio` varchar(20) NOT NULL,
  `marca` varchar(30) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `updated_by_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `comparendos`
--

INSERT INTO `comparendos` (`id`, `id_usuario`, `transito`, `numCompa`, `fecha_comp`, `lugar`, `vehiculo`, `servicio`, `marca`, `placa`, `created_at`, `updated_at`, `created_by_id`, `updated_by_id`) VALUES
(1, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:06:18', '2023-01-12 09:06:18', 2, 2),
(2, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:16:54', '2023-01-12 09:16:54', 2, 2),
(3, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:17:45', '2023-01-12 09:17:45', 2, 2),
(4, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:18:30', '2023-01-12 09:18:30', 2, 2),
(5, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:20:34', '2023-01-12 09:20:34', 2, 2),
(6, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:21:54', '2023-01-12 09:21:54', 2, 2),
(7, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:28:19', '2023-01-12 09:28:19', 2, 2),
(8, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:28:35', '2023-01-12 09:28:35', 2, 2),
(9, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:34:47', '2023-01-12 09:34:47', 2, 2),
(10, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:38:46', '2023-01-12 09:38:46', 2, 2),
(11, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:39:31', '2023-01-12 09:39:31', 2, 2),
(12, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:40:57', '2023-01-12 09:40:57', 2, 2),
(13, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:42:12', '2023-01-12 09:42:12', 2, 2),
(14, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-12 09:47:46', '2023-01-12 09:47:46', 2, 2),
(15, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-04', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-14 08:22:23', '2023-01-14 08:22:23', 2, 2),
(16, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-04', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-14 08:24:45', '2023-01-14 08:24:45', 2, 2),
(17, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-04', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-14 08:28:56', '2023-01-14 08:28:56', 2, 2),
(18, 2, 'Secretaría de Transito de Bogotá', '23094850475984375430', '2023-01-04', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'AUTOMOVIL', 'PARTICULAR', 'alfa-romeo', 'JXO357', '2023-01-14 08:32:27', '2023-01-14 08:32:27', 2, 2),
(19, 2, 'Secretaría de Transito del Quindio', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'CAMIONETA', 'PÚBLICO', 'audi', 'SZY356', '2023-01-14 08:37:34', '2023-01-14 08:37:34', 2, 2),
(20, 2, 'Secretaría de Transito del Quindio', '23094850475984375430', '2023-01-01', 'SAN ALBERTO-LA MATA R 4514-KM 1+499 SSN', 'CAMIONETA', 'PÚBLICO', 'audi', 'SZY356', '2023-01-14 08:43:06', '2023-01-14 08:43:06', 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `modules`
--

INSERT INTO `modules` (`id`, `title`, `description`, `status`) VALUES
(1, 'Roles', 'Permite ver el sistema de roles', 1),
(2, 'Reclamaciones', 'Permite ver el modulo de reclamaciones', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `r` int(11) NOT NULL,
  `w` int(11) NOT NULL,
  `u` int(11) NOT NULL,
  `d` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `role_id`, `module_id`, `r`, `w`, `u`, `d`) VALUES
(1, 1, 1, 1, 1, 1, 1),
(2, 3, 2, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `remembered_logins`
--

CREATE TABLE `remembered_logins` (
  `token_hash` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `status`) VALUES
(1, 'SuperAdmin', 'Tiene todo el control del sistema', 1),
(2, 'Administrador', 'Configuración y administración de toda la información del sistema', 1),
(3, 'Usuario', 'Le permite realizar operaciones en la plataforma.', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `api_key` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_hash` varchar(64) DEFAULT NULL,
  `password_reset_expires_at` datetime DEFAULT NULL,
  `userimg` varchar(200) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activation_hash` varchar(64) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `user_name`, `first_name`, `last_name`, `email`, `role_id`, `email_verified_at`, `api_key`, `password_hash`, `password_reset_hash`, `password_reset_expires_at`, `userimg`, `created_at`, `updated_at`, `activation_hash`, `is_active`) VALUES
(1, '79855135', 'FERNANDO', 'MARTINEZ', 'feramos60@gmail.com', 3, NULL, 'e29e44259c5fdb4c3b975c66ca730a8ae7d5a549bacff1798cff965392752407', '$2y$10$PdZFU1pYsSObWHkly6il.exy3vunmgzGgd7KTtj.2QZaVQF72eFLy', NULL, NULL, NULL, '2023-01-11 21:58:39', '2023-01-11 21:58:39', NULL, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comparendos`
--
ALTER TABLE `comparendos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `created_by_id` (`created_by_id`),
  ADD KEY `updated_by_id` (`updated_by_id`);

--
-- Indices de la tabla `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indices de la tabla `remembered_logins`
--
ALTER TABLE `remembered_logins`
  ADD PRIMARY KEY (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user` (`user_name`),
  ADD UNIQUE KEY `password_reset_hash` (`password_reset_hash`),
  ADD UNIQUE KEY `activation_hash` (`activation_hash`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comparendos`
--
ALTER TABLE `comparendos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permissions_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
