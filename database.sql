
CREATE TABLE tipoDocumento(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `tipo` VARCHAR(50) NOT NULL
);
CREATE TABLE Clientes (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `idTipoDoc` INT NOT NULL,
    `numeroDoc` VARCHAR(10) NULL,
    `nombres` VARCHAR(50) NOT NULL,
    `apellidos` VARCHAR(50) NOT NULL,
    `telefono` VARCHAR(10) NULL,
    `fechaNacimiento` DATE NULL,
    `genero` VARCHAR(10) NULL,
    `idUsuario` INT NOT NULL
);

CREATE TABLE UsuariosClientes(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `correo` VARCHAR(255) NOT NULL,
    `contrasena` VARCHAR(255) NOT NULL
);


ALTER TABLE
    Clientes ADD FOREIGN KEY(`idTipoDoc`) REFERENCES TipoDocumento(`id`);

ALTER TABLE Clientes ADD  FOREIGN KEY(`idUsuario`) REFERENCES UsuariosClientes(`id`);