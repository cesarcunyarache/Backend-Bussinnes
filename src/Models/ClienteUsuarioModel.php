<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Query;
use App\Database\Sql;

class ClienteUsuarioModel extends  Connection
{

    private static int    $id;
    private static string $correo;
    private static string $contrasena;

    public function __construct(array $data)
    {
        self::$correo   = $data['correo'];
        self::$contrasena = $data['contrasena'];
    }

    final public static function login()
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM UsuariosClientes WHERE correo = :correo");
            $con->execute([
                ':correo' => self::getCorreo()
            ]);

            if ($con->rowCount() === 0) {
                echo ResponseHttp::status400('El correo o la contraseña son incorrectos');
            } else {
                $data = $con->fetch();

                if (count($data) > 0) {
                    if (Security::validatePassword(self::getContrasena(), $data['contrasena'])) {
                        return $data;
                    } else {
                        echo ResponseHttp::status400('El correo o la contraseña son incorrectos');
                    }
                } else {
                    echo ResponseHttp::status400('El correo o la contraseña son incorrectos');
                }
            }
        } catch (\PDOException $e) {
            error_log("UserModel::Login -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
        exit;
    }



    final public static function createUser()
    {
        if (Sql::exists("SELECT correo FROM UsuariosClientes WHERE correo = :correo", ":correo", self::getCorreo())) {
            echo (ResponseHttp::status400("El Correo ya esta registrado"));
        } else {

            try {

                $con = self::getConnection();
                $sql = "INSERT INTO UsuariosClientes (correo, contrasena) VALUES (:correo,:contrasena)";
                $query = $con->prepare($sql);
                $query->execute([
                    ':correo'  => self::getCorreo(),
                    ':contrasena' => Security::createPassword(self::getContrasena()),
                ]);

                if ($query->rowCount() > 0) {
                    return $con->lastInsertId();
                } else {
                    return 0;
                }
            } catch (\PDOException $e) {
                error_log('UserModel::post -> ' . $e);
                die(json_encode(ResponseHttp::status500()));
            }
        }
        exit();
    }




    final public static function getCorreo()
    {
        return self::$correo;
    }

    final public static function getContrasena()
    {
        return self::$contrasena;
    }

    final public static function set(string $correo)
    {
        self::$correo = $correo;
    }

    final public static function setContrasena(string $contrasena)
    {
        self::$contrasena = $contrasena;
    }
}
