-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 25-10-2023 a las 23:41:22
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `Restobar`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Clientes`
--

CREATE TABLE `Clientes` (
  `id` int(11) NOT NULL,
  `idTipoDoc` int(11) DEFAULT NULL,
  `numeroDoc` varchar(25) DEFAULT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(10) DEFAULT NULL,
  `fechaNacimiento` date DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  `idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Clientes`
--

INSERT INTO `Clientes` (`id`, `idTipoDoc`, `numeroDoc`, `nombres`, `apellidos`, `telefono`, `fechaNacimiento`, `genero`, `idUsuario`) VALUES
(1, NULL, NULL, 'Cesar', 'Cunyarache', NULL, NULL, NULL, 9),
(2, NULL, NULL, 'cc', 'cc', NULL, NULL, NULL, 10),
(3, NULL, NULL, 'cc', 'cc', NULL, NULL, NULL, 11),
(4, NULL, NULL, 'cc', 'cc', NULL, NULL, NULL, 12),
(5, NULL, NULL, 'cc', 'ccc', NULL, NULL, NULL, 13);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TipoDocumento`
--

CREATE TABLE `TipoDocumento` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `UsuariosClientes`
--

CREATE TABLE `UsuariosClientes` (
  `id` int(11) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `UsuariosClientes`
--

INSERT INTO `UsuariosClientes` (`id`, `correo`, `contrasena`) VALUES
(9, 'cesar@gmail.com', '$2y$10$ezyXaeyxsyPBErroowI66OVixXgkjiyabgMD/DizNpJLyjyVMSBZq'),
(10, 'cesar2@gmail.com', '$2y$10$MU66GirAylJfEKYz.ghT7.6P7uE7XFQerpmIsDUBCt7BI/ybMzZPm'),
(11, 'ces@gmail.com', '$2y$10$jenJAp.uAxy3VnscBvgByeNVKcvVGwnllyuum8AScXqiPShyuNcBW'),
(12, 'ces@33gmail.com', '$2y$10$0Bw3QL9Kal64DpRFDc9fjO94L20kXp5mZvCklndnFWVJzpV0SGaVe'),
(13, 'cesar22@gmail.com', '$2y$10$vXatGQv61rjbPCF1PuyV7.QWoQKBbIcwNNnVgy2mZwrcZs/luSyNW');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Clientes`
--
ALTER TABLE `Clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTipoDoc` (`idTipoDoc`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `TipoDocumento`
--
ALTER TABLE `TipoDocumento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `UsuariosClientes`
--
ALTER TABLE `UsuariosClientes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Clientes`
--
ALTER TABLE `Clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `TipoDocumento`
--
ALTER TABLE `TipoDocumento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `UsuariosClientes`
--
ALTER TABLE `UsuariosClientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Clientes`
--
ALTER TABLE `Clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`idTipoDoc`) REFERENCES `TipoDocumento` (`id`),
  ADD CONSTRAINT `clientes_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `UsuariosClientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
