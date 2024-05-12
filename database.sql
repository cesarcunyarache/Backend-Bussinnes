CREATE TABLE `DetalleVenta` (
  `idDetalleVenta` INT NOT NULL,
  `idVenta` INT NOT NULL,
  `idProducto` INT NOT NULL,
  `cantidad` BIGINT NOT NULL,
  `subtotal` DECIMAL(8,2) NOT NULL,
  PRIMARY KEY (`idDetalleVenta`)
);

CREATE TABLE `TipoDocumento` (
  `idTipoDoc` INT NOT NULL,
  `tipo` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`idTipoDoc`)
);

CREATE TABLE `Productos` (
  `idProducto` INT NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `precio` DECIMAL(8,2) NOT NULL,
  `imagen` VARCHAR(255) NOT NULL,
  `idCategoria` INT NOT NULL,
  PRIMARY KEY (`idProducto`)
);

CREATE TABLE `Precios` (
  `idPrecio` INT NOT NULL,
  `idProducto` INT NOT NULL,
  `precio` DECIMAL(8,2) NOT NULL,
  PRIMARY KEY (`idPrecio`)
);

CREATE TABLE `Roles` (
  `idRol` BIGINT NOT NULL,
  `rol` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`idRol`)
);

CREATE TABLE `Ventas` (
  `idVenta` INT NOT NULL,
  `idCliente` INT NOT NULL,
  `idEmpleado` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `total` DECIMAL(8,2) NOT NULL,
  `igv` DECIMAL(8,2) NOT NULL,
  PRIMARY KEY (`idVenta`)
);

CREATE TABLE `Empleado` (
  `idEmpleado` INT NOT NULL,
  `idTipoDoc` INT NOT NULL,
  `numeroDoc` VARCHAR(10) NOT NULL,
  `nombres` VARCHAR(255) NOT NULL,
  `apellidos` VARCHAR(255) NOT NULL,
  `fechaNacimiento` DATE NOT NULL,
  `telefono` VARCHAR(10) NOT NULL,
  `genero` VARCHAR(25) NOT NULL,
  `direccion` VARCHAR(255) NOT NULL,
  `idUsuario` INT NOT NULL,
  PRIMARY KEY (`idEmpleado`)
);

CREATE TABLE `Usuarios` (
  `idUsuario` INT NOT NULL,
  `correo` VARCHAR(255) NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL,
  `idRol` BIGINT NOT NULL,
  PRIMARY KEY (`idUsuario`)
);

CREATE TABLE `Clientes` (
  `idCliente` INT NOT NULL,
  `idTipoDoc` INT NOT NULL,
  `numeroDoc` VARCHAR(255) NOT NULL,
  `nombres` VARCHAR(255) NOT NULL,
  `apellidos` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(10) NOT NULL,
  `fechaNacimiento` DATE NOT NULL,
  `genero` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`idCliente`)
);

CREATE TABLE `Categoria` (
  `idCategoria` INT NOT NULL,
  `categoria` VARCHAR(255) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `imagen` TEXT NOT NULL,
  PRIMARY KEY (`idCategoria`)
);

CREATE TABLE `Promociones` (
  `idPromocion` INT NOT NULL,
  `idProducto` INT NOT NULL,
  `porcentajeDescuento` DECIMAL(8,2) NOT NULL,
  `precio` DECIMAL(8,2) NOT NULL,
  `imagen` VARCHAR(255) NOT NULL,
  `fechaInicio` DATE NOT NULL,
  `fechaFin` DATE NOT NULL,
  PRIMARY KEY (`idPromocion`)
);

ALTER TABLE `DetalleVenta` ADD CONSTRAINT `detalleventa_idproducto_foreign` FOREIGN KEY (`idProducto`) REFERENCES `Productos` (`idProducto`);

ALTER TABLE `Clientes` ADD CONSTRAINT `clientes_idtipodoc_foreign` FOREIGN KEY (`idTipoDoc`) REFERENCES `TipoDocumento` (`idTipoDoc`);

ALTER TABLE `Productos` ADD CONSTRAINT `productos_idcategoria_foreign` FOREIGN KEY (`idCategoria`) REFERENCES `Categoria` (`idCategoria`);

ALTER TABLE `DetalleVenta` ADD CONSTRAINT `detalleventa_idventa_foreign` FOREIGN KEY (`idVenta`) REFERENCES `Ventas` (`idVenta`);

ALTER TABLE `Empleado` ADD CONSTRAINT `empleado_idusuario_foreign` FOREIGN KEY (`idUsuario`) REFERENCES `Usuarios` (`idUsuario`);

ALTER TABLE `Empleado` ADD CONSTRAINT `empleado_idtipodoc_foreign` FOREIGN KEY (`idTipoDoc`) REFERENCES `TipoDocumento` (`idTipoDoc`);

ALTER TABLE `Ventas` ADD CONSTRAINT `ventas_idcliente_foreign` FOREIGN KEY (`idCliente`) REFERENCES `Clientes` (`idCliente`);

ALTER TABLE `Ventas` ADD CONSTRAINT `ventas_idempleado_foreign` FOREIGN KEY (`idEmpleado`) REFERENCES `Empleado` (`idEmpleado`);

ALTER TABLE `Usuarios` ADD CONSTRAINT `usuarios_idrol_foreign` FOREIGN KEY (`idRol`) REFERENCES `Roles` (`idRol`);
