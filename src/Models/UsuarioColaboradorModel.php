<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Sql;

class UsuarioColaboradorModel extends Connection
{

    private static int    $id;
    private static string $correo;
    private static string $contrasena;
    private static int    $idRol;

    public function __construct(array $data)
    {
        self::$correo   = $data['correo'];
        self::$contrasena = $data['contrasena'];
        self::$idRol = $data['idRol'];
    }

    

    final public static function login($correo, $contrasena)
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM UsuariosColaboradores WHERE correo = :correo");
            $con->execute([
                ':correo' => $correo
            ]);

            if ($con->rowCount() === 0) {
                echo ResponseHttp::status400('El correo o la contraseña son incorrectos');
            } else {
                $data = $con->fetch();

                if (count($data) > 0) {
                    if (Security::validatePassword($contrasena, $data['contrasena'])) {
                        return $data;
                    } else {
                        echo ResponseHttp::status400('El correo o la contraseña son incorrectos');
                    }
                } else {
                    echo ResponseHttp::status400('El correo o la contraseña son incorrectos');
                }
            }
        } catch (\PDOException $e) {
            error_log("UserColaboradorModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }



    final public static function createUser()
    {
        if (Sql::exists("SELECT correo FROM  UsuariosColaboradores WHERE correo = :correo", ":correo", self::getCorreo())) {
            echo (ResponseHttp::status400("El Correo ya esta registrado"));
        } else {

            try {

                $con = self::getConnection();
                $sql = "INSERT INTO  UsuariosColaboradores (correo, contrasena, idRol) VALUES (:correo,:contrasena, :idRol)";
                $query = $con->prepare($sql);
                $query->execute([
                    ':correo'  => self::getCorreo(),
                    ':contrasena' => Security::createPassword(self::getContrasena()),
                    ':idRol' => self::getIdRol(),
                ]);

                if ($query->rowCount() > 0) {
                    return $con->lastInsertId();
                } else {
                    return 0;
                }
            } catch (\PDOException $e) {
                error_log('UserColaboradorModel::post -> ' . $e);
                die(ResponseHttp::status500());
            }
        }
        exit();
    }

    final public static function validateCorreo($correo)
    {

        try {

            if (Sql::exists("SELECT correo FROM UsuariosColaboradores WHERE correo = :correo", ":correo", $correo)) {
                echo (ResponseHttp::status400("El Correo ya esta registrado"));
                exit();
            } else {
                return true;
            }
        } catch (\PDOException $e) {
            error_log('UserColaboradorModell::post -> ' . $e);
            die(ResponseHttp::status500());
        }
    }



    final public static function getUserByCorreo($correo)
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM UsuariosColaboradores WHERE correo = :correo");
            $con->execute([
                ':correo' => $correo
            ]);

            if ($con->rowCount() === 0) {
                echo ResponseHttp::status400('El correo no existe');
            } else {
                $data = $con->fetch();
                if (count($data) > 0) {
                    return $data;
                } else {
                    return [];
                }
            }
        } catch (\PDOException $e) {
            error_log("UserColaboradorModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }


    final public static function UpdatePassword($id, $password)
    {
        try {
            $con = self::getConnection();
            $sql = "UPDATE UsuariosColaboradores SET contrasena=:contrasena WHERE id=:id";

            $query = $con->prepare($sql);
            $query->execute([
                ':contrasena' => Security::createPassword($password),
                ':id' => (int) $id,
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

    /*

    final public static function UpdateEmail($id, $email)
    {
        try {
            $con = self::getConnection();
            $sql = "UPDATE UsuariosClientes SET correo=:correo WHERE id=:id";

            $query = $con->prepare($sql);
            $query->execute([
                ':correo' => $email,
                ':id' => (int) $id,
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




    final public static function getCorreo()
    {
        return self::$correo;
    }

    final public static function getContrasena()
    {
        return self::$contrasena;
    }

    final public static function getIdRol()
    {
        return self::$idRol;
    }

    final public static function set(string $correo)
    {
        self::$correo = $correo;
    }

    final public static function setContrasena(string $contrasena)
    {
        self::$contrasena = $contrasena;
    }

    final public static function setIdRol(string $idRol)
    {
        self::$idRol = $idRol;
    }
}
