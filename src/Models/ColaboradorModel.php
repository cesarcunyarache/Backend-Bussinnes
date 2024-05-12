<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Query;
use App\Database\Sql;

class ColaboradorModel extends  Connection
{

    private static int    $id;
    private static int    $idTipoDoc;
    private static string $numeroDoc;
    private static string $nombres;
    private static string $apellidos;
    private static string $telefono;
    private static string $fechaNacimiento;
    private static string $genero;
    private static string $direccion;
    private static int $idUsuario;

    public function __construct(array $data)
    {
        self::$idTipoDoc = $data['idTipoDoc'];
        self::$numeroDoc = $data['numeroDoc'];
        self::$nombres = $data['nombres'];
        self::$apellidos = $data['apellidos'];
        self::$fechaNacimiento = $data['fechaNacimiento'];
        self::$telefono = $data['telefono'];
        self::$genero = $data['genero'];
        self::$direccion = $data['direccion'];
    }



    final public static function create()
    {
        try {
            $con = self::getConnection();
            $sql = "INSERT INTO Empleados (idTipoDoc, numeroDoc, nombres, apellidos, fechaNacimiento,telefono, genero,direccion) VALUES (:idTipoDoc,:numeroDoc, :nombres,:apellidos,:fechaNacimiento, :telefono,:genero, :direccion)";
            $query = $con->prepare($sql);
            $query->execute([
                ':idTipoDoc' => (int) self::getIdTipoDoc(),
                ':numeroDoc' => self::getNumeroDoc(),
                ':nombres'  => self::getNombres(),
                ':apellidos' => self::getApellidos(),
                ':fechaNacimiento' => self::getFechaNacimiento(),
                ':telefono' => self::getTelefono(),
                ':genero' => self::getGenero(),
                ':direccion' =>  self::getDireccion(),
            ]);
            if ($query->rowCount() > 0) {
                return $con->lastInsertId();
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            error_log('ColaboradorModel::post -> ' . $e);
            die(ResponseHttp::status500());
        }
    }

    final public static function update()
    {
        try {
            $con = self::getConnection();
            $sql = "UPDATE Colaboradores SET idTipoDoc=:idTipoDoc, numeroDoc=:numeroDoc, nombres=:nombres, apellidos=:apellidos, fechaNacimiento=:fechaNacimiento,telefono=:telefono, genero=:genero,direccion=:direccion WHERE id=:id";
            $query = $con->prepare($sql);
            $query->execute([
                ':idTipoDoc' => (int) self::getIdTipoDoc(),
                ':numeroDoc' => self::getNumeroDoc(),
                ':nombres'  => self::getNombres(),
                ':apellidos' => self::getApellidos(),
                ':fechaNacimiento' => self::getFechaNacimiento(),
                ':telefono' => self::getTelefono(),
                ':genero' => self::getGenero(),
                ':direccion' =>  self::getDireccion(),
                ':id' => self::getId(),
            ]);
            if ($query->rowCount() > 0) {
                return true;
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            error_log('ColaboradorModel::post -> ' . $e);
            die(ResponseHttp::status500());
        }
    }

    final public static function updateIdUser($idColaborador, $idUsuario)
    {
        try {
            $con = self::getConnection();
            $sql = "UPDATE Colaboradores SET idUsuario=:idUsuario WHERE id=:id";

            $query = $con->prepare($sql);
            $query->execute([
                ':idUsuario' => (int) $idUsuario,
                ':id' => (int) $idColaborador,
            ]);
            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log('ColaboradorModel::post -> ' . $e);
            die(ResponseHttp::status500());
        }
    }

    final public static function getClientByIdUser($idUser)
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM Colaboradores WHERE idUsuario = :idUsuario");
            $con->execute([
                ':idUsuario' => (int) $idUser
            ]);

            if ($con->rowCount() === 0) {
                echo ResponseHttp::status400('Usuario no encontrado');
            } else {
                $data = $con->fetch();
                if (count($data) > 0) {
                    return $data;
                } else {
                    echo ResponseHttp::status400('Usuario no encontrado');
                }
            }
        } catch (\PDOException $e) {
            error_log("ColaboradorModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }

    final public static function read()
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM Empleados");
            $con->execute();

            if ($con->rowCount() === 0) {
                return [];
            } else {
                $data = $con->fetchAll();
                if (count($data) > 0) {
                    return $data;
                } else {
                    return [];
                }
            }
        } catch (\PDOException $e) {
            error_log("UserModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }

    final public static function readNoMesero()
    {
        try {
            $con = self::getConnection()->prepare(
                "SELECT c.id, c.idTipoDoc, c.numeroDoc, c.nombres, c.apellidos, c.genero, c.direccion, c.idUsuario
                 FROM Colaboradores c
                LEFT JOIN Meseros m ON c.id = m.idColaborador
                WHERE m.idColaborador IS NULL;");
            $con->execute();

            if ($con->rowCount() === 0) {
                return [];
            } else {
                $data = $con->fetchAll();
                if (count($data) > 0) {
                    return $data;
                } else {
                    return [];
                }
            }
        } catch (\PDOException $e) {
            error_log("UserModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }

    final public static function getColaborador($id)
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM Colaboradores WHERE id=:id");
            $con->execute([':id' => $id]);

            if ($con->rowCount() === 0) {
                return [];
            } else {
                $data = $con->fetch();
                if (count($data) > 0) {
                    return $data;
                } else {
                    return [];
                }
            }
        } catch (\PDOException $e) {
            error_log("UserModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }

    /*

    final public static function getClientById($idClient)
    {
        try {
            $con = self::getConnection()->prepare("SELECT *
            FROM Clientes c
            INNER JOIN UsuariosClientes u ON c.idUsuario = u.id
            WHERE c.id = :id");
            $con->execute([
                ':id' => (int) $idClient
            ]);

            if ($con->rowCount() === 0) {
                echo ResponseHttp::status400('Cliente no encontrado');
            } else {
                $data = $con->fetch();
                if (count($data) > 0) {
                    return $data;
                } else {
                    echo ResponseHttp::status400('Cliente no encontrado');
                }
            }
        } catch (\PDOException $e) {
            error_log("UserModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    } 

    final public static function Update()
    {
        try {
            $con = self::getConnection();
            $sql = "UPDATE Clientes SET idTipoDoc=:idTipoDoc, numeroDoc=:numeroDoc, nombres=:nombres,apellidos=:apellidos, telefono=:telefono, fechaNacimiento=:fechaNacimiento, genero=:genero WHERE id=:id";

            $query = $con->prepare($sql);
            $query->execute([
                ':idTipoDoc' => (int) self::getIdTipoDoc(),
                ':numeroDoc' => self::getNumeroDoc(),
                ':nombres'  => self::getNombres(),
                ':apellidos' => self::getApellidos(),
                ':telefono' => self::getTelefono(),
                ':fechaNacimiento' => self::getFechaNacimiento(),
                ':genero' => self::getGenero(),
                ':id' => (int) self::getId(),
            ]);
            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log('UserModel::post -> ' . $e);
            die(ResponseHttp::status500());
        }
    }

    */


    final public static function getId()
    {
        return self::$id;
    }

    final public static function setId($id)
    {
        self::$id = $id;
    }

    final public static function getIdTipoDoc()
    {
        return self::$idTipoDoc;
    }

    final public static function setIdTipoDoc($idTipoDoc)
    {
        self::$idTipoDoc = $idTipoDoc;
    }

    final public static function getNumeroDoc()
    {
        return self::$numeroDoc;
    }

    final public static function setNumeroDoc($numeroDoc)
    {
        self::$numeroDoc = $numeroDoc;
    }

    final public static function getNombres()
    {
        return self::$nombres;
    }

    final public static function setNombres($nombres)
    {
        self::$nombres = $nombres;
    }

    final public static function getApellidos()
    {
        return self::$apellidos;
    }

    final public static function setApellidos($apellidos)
    {
        self::$apellidos = $apellidos;
    }

    final public static function getTelefono()
    {
        return self::$telefono;
    }

    final public static function setTelefono($telefono)
    {
        self::$telefono = $telefono;
    }

    final public static function getFechaNacimiento()
    {
        return self::$fechaNacimiento;
    }

    final public static function setFechaNacimiento($fechaNacimiento)
    {
        self::$fechaNacimiento = $fechaNacimiento;
    }

    final public static function getGenero()
    {
        return self::$genero;
    }

    final public static function setGenero($genero)
    {
        self::$genero = $genero;
    }

    final public static function getIdUsuario()
    {
        return self::$idUsuario;
    }

    final public static function setIdUsuario($idUsuario)
    {
        self::$idUsuario = $idUsuario;
    }

    final public static function setDireccion($direccion)
    {
        self::$direccion = $direccion;
    }

    final public static function getDireccion()
    {
        return self::$direccion;
    }
}
