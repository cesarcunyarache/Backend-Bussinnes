<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Query;
use App\Database\Sql;
class CategoriaModel extends  Connection
{
    private static int    $id;
    private static string $categoria;
    private static string    $descripcion;
    private static $file;
    private static string $url;
    private static string $imagen;
    private static string    $estado;
  
    final public function __construct(array $data, $file)
{
    self::$categoria = $data['categoria'];
    self::$descripcion = $data['descripcion'];
    self::$estado = $data['estado'];
    self::$file = $file;
}


    final public static function setCategoria($categoria)
    {
        self::$categoria = $categoria;
    }
    final public static function getCategoria()
    {
        return self::$categoria;
    }
    final public static function setDescripcion($descripcion)
    {
        self::$descripcion = $descripcion;
    }
    final public static function getDescripcion()
    {
        return self::$descripcion;
    }

    final public static function setUrl($url)
    {
        self::$url = $url;
    }
    final public static function getUrl()
    {
        return self::$url;
    }
    final public static function getFile()
    {
        return self::$file;
    }
    final public static function getEstado()
    {
        return self::$estado;
    }
    final public static function setImagen($imagen)
    {
        self::$imagen = $imagen;
    }

    final public static function read()
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM categoria");
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
            error_log("CategoriaModel::read -> " . $e);
            throw new \Exception("Error al leer las categorías", 500);
        }
    }

    final public static function create()
    {
        try {
                       
            $con = self::getConnection();
            $sql = "INSERT INTO categoria (categoria, descripcion, imagen, estado) VALUES (:categoria,:descripcion, :imagen,:estado)";
            $query = $con->prepare($sql);
            $query->execute([
                ':categoria' => self::getCategoria(),
                ':descripcion' => self::getDescripcion(),
                ':imagen'  => self::getFile(),
                ':estado' => self::getEstado(),
            ]);
            if ($query->rowCount() > 0) {
                return $con->lastInsertId();
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            error_log('CategoriaModel::postSave-> ' . $e);
            die(ResponseHttp::status500('No se puede registrar la categoria'));
        }
    }
}