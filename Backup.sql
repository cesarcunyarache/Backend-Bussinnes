-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 08-11-2023 a las 22:15:25
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
(1, 2, '12345678111', 'Cesar Efrain', 'Cunyarache', '123456789', '2004-10-10', 'Masculino', 9),
(17, 1, '12345678', 'juan', 'juan', '998989999', '2004-02-04', 'Masculino', 29),
(18, NULL, NULL, 'Efrain', 'Castillo', NULL, NULL, NULL, 30),
(19, NULL, NULL, 'cc', 'ccc', NULL, NULL, NULL, 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Colaboradores`
--

CREATE TABLE `Colaboradores` (
  `id` int(10) NOT NULL,
  `idTipoDoc` int(10) NOT NULL,
  `numeroDoc` varchar(25) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `genero` varchar(25) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `idUsuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Mesas`
--

CREATE TABLE `Mesas` (
  `idMesa` int(10) NOT NULL,
  `codigo` varchar(25) NOT NULL,
  `nivel` varchar(10) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `estado` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MesasReserva`
--

CREATE TABLE `MesasReserva` (
  `idMesaReserva` int(10) UNSIGNED NOT NULL,
  `idReserva` int(11) NOT NULL,
  `idMesa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Meseros`
--

CREATE TABLE `Meseros` (
  `idMesero` int(11) NOT NULL,
  `idColaborador` int(11) NOT NULL,
  `cantMesas` int(11) NOT NULL,
  `estado` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Reservas`
--

CREATE TABLE `Reservas` (
  `idReserva` int(10) NOT NULL,
  `idCliente` int(11) NOT NULL,
  `cantComensales` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `idMesero` int(11) DEFAULT NULL,
  `estado` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Roles`
--

CREATE TABLE `Roles` (
  `id` int(10) NOT NULL,
  `rol` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TipoDocumento`
--

CREATE TABLE `TipoDocumento` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `TipoDocumento`
--

INSERT INTO `TipoDocumento` (`id`, `tipo`) VALUES
(1, 'DNI'),
(2, 'Carnet de Extranjería'),
(3, 'Pasaporte'),
(4, 'RUC');

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
(29, 'manuel12072705@gmail.com', '$2y$10$AyoeYxOz9PhrDTL2aqcG6OHo.wizIwVR0.7zez2Oo7fTv7I8tTDdW'),
(30, 'jairomonterrey123@gmail.com', '$2y$10$VOCPKg6Fd9.QgpnyUHf3EOFEsfUUCe0Y8ZObNbJb2nIlQr1fWglXG'),
(31, 'cesarcunyarache@gmail.com', '$2y$10$c12oMcLp0jY.3MPt/.gDwejVcivTte/0OCo3jUX2NdBp8Tq5XBziy');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `UsuariosColaboradores`
--

CREATE TABLE `UsuariosColaboradores` (
  `id` int(10) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `idRol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indices de la tabla `Colaboradores`
--
ALTER TABLE `Colaboradores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTipoDoc` (`idTipoDoc`);

--
-- Indices de la tabla `Mesas`
--
ALTER TABLE `Mesas`
  ADD PRIMARY KEY (`idMesa`);

--
-- Indices de la tabla `MesasReserva`
--
ALTER TABLE `MesasReserva`
  ADD PRIMARY KEY (`idMesaReserva`),
  ADD KEY `idMesa` (`idMesa`),
  ADD KEY `idReserva` (`idReserva`);

--
-- Indices de la tabla `Meseros`
--
ALTER TABLE `Meseros`
  ADD PRIMARY KEY (`idMesero`),
  ADD KEY `idColaborador` (`idColaborador`);

--
-- Indices de la tabla `Reservas`
--
ALTER TABLE `Reservas`
  ADD PRIMARY KEY (`idReserva`),
  ADD KEY `idCliente` (`idCliente`),
  ADD KEY `idMesero` (`idMesero`);

--
-- Indices de la tabla `Roles`
--
ALTER TABLE `Roles`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `UsuariosColaboradores`
--
ALTER TABLE `UsuariosColaboradores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idRol` (`idRol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Clientes`
--
ALTER TABLE `Clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `Colaboradores`
--
ALTER TABLE `Colaboradores`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Mesas`
--
ALTER TABLE `Mesas`
  MODIFY `idMesa` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `MesasReserva`
--
ALTER TABLE `MesasReserva`
  MODIFY `idMesaReserva` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Meseros`
--
ALTER TABLE `Meseros`
  MODIFY `idMesero` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Reservas`
--
ALTER TABLE `Reservas`
  MODIFY `idReserva` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Roles`
--
ALTER TABLE `Roles`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `TipoDocumento`
--
ALTER TABLE `TipoDocumento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `UsuariosClientes`
--
ALTER TABLE `UsuariosClientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `UsuariosColaboradores`
--
ALTER TABLE `UsuariosColaboradores`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Clientes`
--
ALTER TABLE `Clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`idTipoDoc`) REFERENCES `TipoDocumento` (`id`),
  ADD CONSTRAINT `clientes_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `UsuariosClientes` (`id`);

--
-- Filtros para la tabla `Colaboradores`
--
ALTER TABLE `Colaboradores`
  ADD CONSTRAINT `colaboradores_ibfk_1` FOREIGN KEY (`idTipoDoc`) REFERENCES `tipoDocumento` (`id`);

--
-- Filtros para la tabla `MesasReserva`
--
ALTER TABLE `MesasReserva`
  ADD CONSTRAINT `mesasreserva_ibfk_1` FOREIGN KEY (`idMesa`) REFERENCES `Mesas` (`idMesa`),
  ADD CONSTRAINT `mesasreserva_ibfk_2` FOREIGN KEY (`idReserva`) REFERENCES `Reservas` (`idReserva`);

--
-- Filtros para la tabla `Meseros`
--
ALTER TABLE `Meseros`
  ADD CONSTRAINT `meseros_ibfk_1` FOREIGN KEY (`idColaborador`) REFERENCES `Colaboradores` (`id`);

--
-- Filtros para la tabla `Reservas`
--
ALTER TABLE `Reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `Clientes` (`id`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`idMesero`) REFERENCES `Meseros` (`idMesero`);

--
-- Filtros para la tabla `UsuariosColaboradores`
--
ALTER TABLE `UsuariosColaboradores`
  ADD CONSTRAINT `usuarioscolaboradores_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `Roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
