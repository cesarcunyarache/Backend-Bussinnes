<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Query;
use App\Database\Sql;

class MeseroModel extends  Connection
{

    private static int    $idMesero;
    private static int    $idColaborador;
    private static string $cantMesas;
    private static string $estado;
    private static $file;
    private static string $url;
    private static string $imagen;
    private static string $IDtoken; 

    public function __construct(array $data, $file)
    {
        echo 1;
        self::$idColaborador = $data['idColaborador'];
        self::$estado = $data['estado'];
        self::$file = $file;
    }



    final public static function postSave() 
    {
   
        if ( false/* Sql::exists("SELECT name FROM productos WHERE name = :name",":name",self::getName()) */) {  
            return ResponseHttp::status400('El Producto ya esta registrado');
        } else {
            try {
           
                $resImg = Security::uploadImage(self::getFile(),'product'); 
              
              
                self::setUrl($resImg['path']);
                self::setImagen($resImg['name']);
                self::setIDtoken(hash('md5', 'upload' . self::getUrl()));
                
                
             /*    $con = self::getConnection();
                $query = $con->prepare('INSERT INTO productos(name,description,stock,url,imageName,IDtoken) VALUES (:name,:description,:stock,:url,:imageName,:IDtoken)');
                $query->execute([
                    ':name'        => self::getName(),
                    ':description' => self::getDescription(),
                    ':stock'       => self::getStock(),
                    ':url'         => self::getUrl(),
                    ':imageName'   => self::getImageName(),
                    ':IDtoken'     => self::getIDtoken()
                ]); 
                
                if ($query->rowCount() > 0) {
                    return ResponseHttp::status200('Producto registrado');
                } else {
                    return ResponseHttp::status500('No se puede registrar el producto');
                } */
            } catch (\PDOException $e) {
                error_log('ProductModel::postSave-> ' . $e);
                die(json_encode(ResponseHttp::status500('No se puede registrar el producto')));
            }  
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

    final public static function getFile()
    {
        return self::$file;
    }


    final public static function setUrl($url){
        self::$url = $url;
    }

    final public static function getUrl(){
        return self::$url;
    }


    final public static function setImagen($imagen){
        self::$imagen = $imagen;
    }

    final public static function setIDToken($idToken){
        self::$IDtoken = $idToken;
    }



  
}
