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
    private static int $idProducto;
    private static string $nombre;
    private static string $descripcion;
    private static float $precio;
    private static $file;
    private static string $url;
    private static string $imagen;
    private static string $estado;
    private static int $idCategoria;

    public function __construct(array $data, $file)
    {
        self::$nombre = $data['nombre'];
        self::$descripcion = $data['descripcion'];
        self::$precio = $data['precio'];
        self::$file = $file;
        self::$estado = $data['estado'];
        self::$idCategoria = $data['idCategoria'];
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
    }

    final public static function create()
    {
        try {

            $con = self::getConnection();
            $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen, estado, idCategoria) VALUES (:nombre,:descripcion, :precio, :imagen, :estado, :idCategoria)";
            $query = $con->prepare($sql);
            $query->execute([
                ':nombre' => self::getNombre(),
                ':descripcion' => self::getDescripcion(),
                ':precio'    => (float) self::getPrecio(),
                ':imagen'  => self::getFile(),
                ':estado' => self::getEstado(),
                ':idCategoria' => self::getIdCategoria(),
            ]);

            if ($query->rowCount() > 0) {
                return $con->lastInsertId();
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            error_log('ProductoModel::postSave-> ' . $e);
            die((ResponseHttp::status500('No se puede registrar el producto')));
        }
    }

    final public static function putUpdate($id)
    {
        try {
          /*   $resImg = Security::uploadImage(self::$file, 'imagen', '');
            self::$url = $resImg['path'];
            self::$imagen = $resImg['name']; */

            $con = self::getConnection();
            $query = $con->prepare('UPDATE Productos SET nombre=:nombre, descripcion=:descripcion, precio=:precio, estado=:estado WHERE idProducto=:id');

            $query->execute([
                ':nombre'           => self::$nombre,
                ':descripcion'      => self::$descripcion,
                ':precio'           => self::$precio,
                ':estado'           => self::$estado,
                ':id'               => $id
            ]);

            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log('ProductoModel::postSave-> ' . $e);
            die((ResponseHttp::status500('No se puede Actualizar el producto')));
        }
    }


    final public static function putUpdateImage($id)
    {
        try {
            $resImg = Security::uploadImage(self::$file, 'imagen', 'public/Images/productos/');
            self::$url = $resImg['path'];
            self::$imagen = $resImg['name'];

            $con = self::getConnection();
            $query = $con->prepare('UPDATE Productos SET imagen=:imagen WHERE idProducto=:id');

            $query->execute([
                ':imagen'           => self::$url,
                ':id'               => $id
            ]);

            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log('ProductoModel::putUpdate-> ' . $e);
            die((ResponseHttp::status500('No se puede Actualizar el producto')));
        }
    }


    final public static function putUpdateProductos($id)
    {
        try {
            $resImg = Security::uploadImage(self::getFile(), 'imagen', '');
            self::setUrl($resImg['path']);
            self::setImagen($resImg['name']);

            $con = self::getConnection();
            $query = $con->prepare('UPDATE productos SET nombre=:nombre, descripcion=:descripcion, precio=:precio, imagen=:imagen, estado=:estado WHERE idProducto=:id');


            $query->execute([
                ':nombre'           => self::getNombre(),
                ':descripcion'      => self::getDescripcion(),
                ':imagen'           => self::getUrl(),
                ':precio'           => (float) self::getPrecio(),
                ':estado'           => self::getEstado(),
                ':idCategoria'      => (int) self::getIdCategoria(),
                ':id'               => (int) $id
            ]);

            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log('ProductoModel::postSave-> ' . $e);
            die((ResponseHttp::status500('No se puede Actualizar el producto')));
        }
    }


    final public static function getProducto($id)
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM productos  WHERE idProducto=:id;");
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

    final public static function getCategoryIdProd($idCa)
    {
        try {
            $con = self::getConnection()->prepare("
                SELECT p.idProducto, p.nombre AS nombre_producto, p.descripcion, p.precio, p.imagen, p.estado, c.idCategoria, c.categoria AS nombre_categoria
                FROM productos AS p
                INNER JOIN categoria AS c ON p.idCategoria = c.idCategoria
                WHERE c.idCategoria = :id
            ");
            $con->execute([
                ':id' => (int) $idCa
            ]);

            $data = $con->fetchAll(\PDO::FETCH_ASSOC);
            return $data;
        } catch (\PDOException $e) {
            error_log("UserModel::Login -> " . $e);
            throw new \Exception("Error al obtener productos", 500);
        }
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
    final public static function getDescripcion()
    {
        return self::$descripcion;
    }

    final public static function getPrecio()
    {
        return self::$precio;
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
    final public static function getIdCategoria()
    {
        return self::$idCategoria;
    }
    final public static function getIdColaborador()
    {
        return self::$idProducto;
    }


    final public static function setImagen($imagen)
    {
        self::$imagen = $imagen;
    }

    final public static function setIdCategoria($IDCategoria)
    {
        self::$idCategoria = $IDCategoria;
    }
    
}
