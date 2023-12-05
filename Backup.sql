-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 05-12-2023 a las 22:26:07
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
(1, 1, '12345678', 'Cesar Efrain', 'Cunyarache', '123456789', '2004-10-10', 'Masculino', 9),
(17, 1, '12345678', 'juan', 'juan', '998989999', '2004-02-04', 'Masculino', 29),
(18, 1, '12345678', 'Efrain', 'Castillo', '12333', '1999-10-10', '1234', 30),
(19, 1, '12333455', 'cc', 'ccc', '123456777', '1999-10-10', 'Masculino', 31);

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

--
-- Volcado de datos para la tabla `Colaboradores`
--

INSERT INTO `Colaboradores` (`id`, `idTipoDoc`, `numeroDoc`, `nombres`, `apellidos`, `fechaNacimiento`, `telefono`, `genero`, `direccion`, `idUsuario`) VALUES
(1, 2, '12345678', 'Cesar Efrain', 'Cunyarache Castillo', '1999-06-09', '984564115', 'Masculino', '12023', 1),
(2, 1, '76848273', 'Efrain', 'Castillo', '2002-01-30', '983617388', 'Masculino', 'A.v Grau', 2),
(14, 1, '76672948', 'nuevo colab', 'nuevo colab', '1984-06-14', '983938383', 'Masculino', 'rrrrrrrrrr', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Mesas`
--

CREATE TABLE `Mesas` (
  `idMesa` int(10) NOT NULL,
  `codigo` varchar(25) NOT NULL,
  `nivel` varchar(10) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `estado` tinyint(4) NOT NULL,
  `pLeft` decimal(10,0) NOT NULL,
  `pTop` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Mesas`
--

INSERT INTO `Mesas` (`idMesa`, `codigo`, `nivel`, `capacidad`, `estado`, `pLeft`, `pTop`) VALUES
(1, 'S1', 'S', 4, 1, 87, 26),
(2, 'S2', 'S', 4, 1, 87, 48),
(3, 'S3', 'S', 4, 1, 77, 26),
(4, 'S4', 'S', 4, 1, 77, 48),
(5, 'S5', 'S', 4, 1, 67, 26),
(6, 'S6', 'S', 4, 1, 67, 48),
(7, 'S7', 'S', 4, 1, 57, 26),
(8, 'S8', 'S', 4, 1, 57, 48),
(9, 'S9', 'S', 4, 1, 47, 26),
(10, 'S10', 'S', 4, 1, 47, 48),
(11, 'S11', 'S', 4, 1, 37, 26),
(12, 'S12', 'S', 4, 1, 37, 48),
(13, 'S13', 'S', 4, 1, 27, 26),
(14, 'S14', 'S', 4, 1, 27, 48),
(15, 'S15', 'S', 4, 1, 17, 26),
(16, 'S16', 'S', 4, 1, 17, 48);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesasreserva`
--

CREATE TABLE `mesasreserva` (
  `idMesaReserva` int(10) NOT NULL,
  `idReserva` int(11) NOT NULL,
  `idMesa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesasreserva`
--

INSERT INTO `mesasreserva` (`idMesaReserva`, `idReserva`, `idMesa`) VALUES
(85, 66, 5),
(86, 67, 6),
(87, 68, 7),
(88, 69, 2),
(89, 70, 2),
(90, 71, 5),
(91, 72, 4),
(92, 73, 2),
(93, 73, 1),
(94, 74, 1),
(95, 74, 2),
(96, 75, 1),
(97, 76, 1),
(98, 76, 2),
(99, 77, 4),
(100, 77, 2),
(101, 78, 1),
(102, 79, 1),
(103, 80, 4),
(104, 80, 2),
(105, 81, 3),
(106, 81, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Meseros`
--

CREATE TABLE `Meseros` (
  `idMesero` int(11) NOT NULL,
  `idColaborador` int(11) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `estado` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Meseros`
--

INSERT INTO `Meseros` (`idMesero`, `idColaborador`, `imagen`, `estado`) VALUES
(1, 1, 'http://localhost/api/public/Images/656f946816ebe4.30765529_qjgknfliphmeo.jpeg', '1'),
(8, 2, 'http://localhost/api/public/Images/656f94956f6719.67311122_higplmkjqonfe.jpeg', '1'),
(17, 14, 'http://localhost/api/public/Images/656f949c83c267.46374347_ijmqngpkfohle.jpeg', '1');

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
  `comentario` text DEFAULT NULL,
  `estado` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Reservas`
--

INSERT INTO `Reservas` (`idReserva`, `idCliente`, `cantComensales`, `fecha`, `hora`, `idMesero`, `comentario`, `estado`) VALUES
(66, 1, 2, '2023-11-30', '03:00:00', NULL, '', '2'),
(67, 1, 2, '2023-11-29', '03:00:00', 1, '', '0'),
(68, 1, 2, '2023-11-29', '03:00:00', NULL, '', '0'),
(69, 1, 2, '2023-12-30', '01:30:00', NULL, '', '4'),
(70, 1, 3, '2023-12-03', '02:00:00', 8, '', '1'),
(71, 1, 2, '2023-12-01', '10:30:00', 17, '', '1'),
(72, 1, 2, '2023-11-28', '02:00:00', NULL, '', '0'),
(73, 1, 12, '2023-11-29', '02:30:00', 8, '', '1'),
(74, 19, 10, '2023-11-28', '10:30:00', 8, '', '0'),
(75, 19, 2, '2023-11-28', '10:30:00', 17, '', '1'),
(76, 1, 12, '2023-11-29', '08:00:00', 8, '', '1'),
(77, 19, 12, '2023-11-30', '02:00:00', 1, '', '1'),
(78, 1, 2, '2023-12-08', '01:30:00', 1, '', '1'),
(79, 1, 2, '2023-12-08', '01:30:00', 1, '', '1'),
(80, 1, 12, '2023-12-15', '12:00:00', 8, '123', '1'),
(81, 1, 12, '2023-12-06', '12:00:00', NULL, '12', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Roles`
--

CREATE TABLE `Roles` (
  `id` int(10) NOT NULL,
  `rol` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Roles`
--

INSERT INTO `Roles` (`id`, `rol`) VALUES
(1, 'Gerente'),
(2, 'Administrador'),
(3, 'Colaborador');

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
(9, 'cesar@gmail.com', '$2y$10$ekxLDkhgAuSkWRkGnxY2heR7Ri3lzRivpq8eRBghSVxYQAMARKeS2'),
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
-- Volcado de datos para la tabla `UsuariosColaboradores`
--

INSERT INTO `UsuariosColaboradores` (`id`, `correo`, `contrasena`, `idRol`) VALUES
(1, 'cesarcunyarache@gmail.com', '$2y$10$ohIYss/XsGxEFl5J8PwTAeQErjEN66RoSV.kQe45Cbmgpln7x1oGW', 1),
(2, 'cesarcunyarachecastilo@gmail.com', '$2y$10$YTmIFiVccqIVhRaIQToCNOLHIZoVz8YLM.kXXOJZL3o5GrZFoYQnO', 2),
(3, 'jairomonterrey123@gmail.com', '$2y$10$0jiqRPfsx/n.AsWVxMRHhOulA759fwJ5MLUT5bnsn/RHBKZ/uxAJO', 3);

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
-- Indices de la tabla `mesasreserva`
--
ALTER TABLE `mesasreserva`
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
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `Mesas`
--
ALTER TABLE `Mesas`
  MODIFY `idMesa` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `mesasreserva`
--
ALTER TABLE `mesasreserva`
  MODIFY `idMesaReserva` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT de la tabla `Meseros`
--
ALTER TABLE `Meseros`
  MODIFY `idMesero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `Reservas`
--
ALTER TABLE `Reservas`
  MODIFY `idReserva` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `Roles`
--
ALTER TABLE `Roles`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Filtros para la tabla `mesasreserva`
--
ALTER TABLE `mesasreserva`
  ADD CONSTRAINT `mesasreserva_ibfk_1` FOREIGN KEY (`idMesa`) REFERENCES `mesas` (`idMesa`),
  ADD CONSTRAINT `mesasreserva_ibfk_2` FOREIGN KEY (`idReserva`) REFERENCES `reservas` (`idReserva`);

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
