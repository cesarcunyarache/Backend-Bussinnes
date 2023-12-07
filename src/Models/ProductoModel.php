<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Query;
use App\Database\Sql;
use Random\Engine\Secure;

class ProductoModel extends  Connection
{

    private static int    $idProducto;
    private static string    $nombre;
    private static int    $estado;
    private static float  $precio;
    private static int  $costoPuntos;
    private static $file;
    private static string $url;
    private static string $imagen;
    private static string $IDtoken;

    public function __construct(array $data, $file)
    {
        self::$nombre = $data['nombre'];
        self::$precio = $data['precio'];
        self::$costoPuntos = $data['costoPuntos'];
        self::$estado = $data['estado'];
        self::$file = $file;
    }



    final public static function postSave()
    {
        try {

            $resImg = Security::uploadImage(self::getFile(), 'imagen');
            self::setUrl($resImg['path']);
            self::setImagen($resImg['name']);
            self::setIDtoken(hash('md5', 'upload' . self::getUrl()));

            $con = self::getConnection();
            $query = $con->prepare('INSERT INTO Productos (nombre,imagen, precio, costoPuntos, estado) VALUES (:nombre,:imagen, :precio, :costoPuntos, :estado)');

            $query->execute([
                ':nombre'    => self::getNombre(),
                ':imagen'    => self::getUrl(),
                ':precio'    => (float) self::getPrecio(),
                ':costoPuntos' => (int) self::getCostoPuntos(),
                ':estado' => (int) self::getEstado(),
            ]);

            if ($query->rowCount() > 0) {
                return $con->lastInsertId();
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            error_log('ProductosModel::postSave-> ' . $e);
            die((ResponseHttp::status500('No se puede registrar el mesero')));
        }
    }

    final public static function putUpdate($id)
    {
        try {
            $resImg = Security::uploadImage(self::getFile(), 'imagen');
            self::setUrl($resImg['path']);
            self::setImagen($resImg['name']);
            self::setIDtoken(hash('md5', 'upload' . self::getUrl()));

            $con = self::getConnection();
            $query = $con->prepare('UPDATE Meseros SET imagen=:imagen, estado=:estado WHERE idMesero=:id');


            $query->execute([
                ':imagen'           => self::getUrl(),
                ':estado'           => (int) self::getEstado(),
                ':id'               => (int) $id
            ]);

            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log('MeseroModel::postSave-> ' . $e);
            die((ResponseHttp::status500('No se puede Actualizar el mesero')));
        }
    }

    final public static function read()
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM productos");
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
            error_log("ProductoModel::read -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }
    //

    final public static function putUpdateProductos($id)
    {
        try {
            $resImg = Security::uploadImage(self::getFile(), 'imagen');
            self::setUrl($resImg['path']);
            self::setImagen($resImg['name']);
            self::setIDtoken(hash('md5', 'upload' . self::getUrl()));

            $con = self::getConnection();
            $query = $con->prepare('UPDATE productos SET nombre=:nombre, imagen=:imagen, precio=:precio, costoPuntos=:costoPuntos WHERE idProducto=:id');


            $query->execute([
                ':nombre'           => (int) self::getNombre(),
                ':imagen'           => self::getUrl(),
                ':precio'           => (float) self::getPrecio(),
                ':costoPuntos'      => (int) self::getCostoPuntos(),
                ':idProducto'               => (int) $id
            ]);

            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log('MeseroModel::postSave-> ' . $e);
            die((ResponseHttp::status500('No se puede Actualizar el producto')));
        }
    }

    final public static function getReadMeseroForReserva($fecha, $hora)
    {
        try {
            $con = self::getConnection()->prepare("SELECT *, COUNT(r.idReserva) as cantidadReservas
            FROM Meseros m
            INNER JOIN Reservas r ON r.idMesero = m.idMesero
            WHERE 
                r.fecha = :fecha AND
                r.hora  = :hora
            GROUP BY m.idMesero;");

            $con->execute([
                ':fecha' => $fecha,
                ':hora' => $hora
            ]);

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
            error_log("ReservaModel::getIdReservabyFecha -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }

    final public static function getColaborador($id)
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM Colaboradores c INNER JOIN Meseros m ON c.id = m.idColaborador WHERE idColaborador=:id;");
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


    final public static function getMeseroById($id)
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM Colaboradores c INNER JOIN Meseros m ON c.id = m.idColaborador WHERE idMesero=:id;");
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



    final public static function getClientByIdUser($idUser)
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM Clientes WHERE idUsuario = :idUsuario");
            $con->execute([
                ':idUsuario' => (int) $idUser
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

    /*  final public static function Update()

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
    } */



    final public static function search($fecha)
    {
        try {
            $con = self::getConnection()->prepare("SELECT idReserva
            FROM Reservas WHERE fecha=:fecha");
            $con->execute([
                ':fecha' => $fecha
            ]);

            if ($con->rowCount() === 0) {
                echo ResponseHttp::status400('No hay niguna reserva');
            } else {
                $data = $con->fetch();
                if (count($data) > 0) {
                    return $data;
                } else {
                    echo ResponseHttp::status400('No hay niguna reserva');
                }
            }
        } catch (\PDOException $e) {
            error_log("UserModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }

    final public static function getNombre()
    {
        return self::$nombre;
    }

    final public static function getPrecio()
    {
        return self::$precio;
    }

    final public static function getCostoPuntos()
    {
        return self::$costoPuntos;
    }



    final public static function getFile()
    {
        return self::$file;
    }


    final public static function setUrl($url)
    {
        self::$url = $url;
    }

    final public static function setIdProducto($idProducto)
    {
        self::$idProducto = $idProducto;
    }

    final public static function getUrl()
    {
        return self::$url;
    }

    final public static function getImagen()
    {
        return self::$imagen;
    }

    final public static function getEstado()
    {
        return self::$estado;
    }


    final public static function getIdColaborador()
    {
        return self::$idProducto;
    }


    final public static function setImagen($imagen)
    {
        self::$imagen = $imagen;
    }

    final public static function setIDToken($idToken)
    {
        self::$IDtoken = $idToken;
    }
}
