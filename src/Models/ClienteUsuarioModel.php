<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Sql;

class ClienteUsuarioModel extends Connection
{

    private static int    $idCliente;
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
            $con = self::getConnection()->prepare("SELECT * FROM usuario WHERE correo = :correo ");
            $con->execute([
                ':correo' => self::getEmail()
            ]);

            if ($con->rowCount() === 0) {
                return ResponseHttp::status400('El usuario o contraseña son incorrectos');
            } else {
                foreach ($con as $res) {
                    if (Security::validatePassword(self::getContrasena() , $res['password'])) {
                            $payload = ['IDToken' => $res['IDToken']];
                            $token = Security::createTokenJwt(Security::secretKey(),$payload);

                            $data = [
                                'name'  => $res['nombre'],
                                'rol'   => $res['rol'],
                                'token' => $token
                            ];
                            return ResponseHttp::status200($data);
                            exit;
                    } else {
                        return ResponseHttp::status400('El usuario o contraseña son incorrectos');
                    }
                }
            }
        } catch (\PDOException $e) {
            error_log("UserModel::Login -> " .$e);
            die(json_encode(ResponseHttp::status500()));           
        }
    }


    final public static function loginTest(){
        $data = [
            'nombre'  => "Cesar",
            'apellidos'   => "Cunyarache",
            'id' => 1
        ];
        return $data;
        exit;
    }

    final public static function getEmail()
    {
        return self::$correo;
    }

    final public static function getContrasena()
    {
        return self::$contrasena;
    }

    final public static function setEmail(string $correo)
    {
        self::$correo = $correo;
    }

    final public static function setContrasena(string $contrasena)
    {
        self::$contrasena = $contrasena;
    }
}
