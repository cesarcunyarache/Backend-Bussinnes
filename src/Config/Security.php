<?php

namespace App\Config;

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Bulletproof\Image;


class Security
{

    private static $jwt_data;

    final public static function secretKey()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
        return $_ENV['SECRET_KEY'];
    }

    final public static function createPassword(string $pw)
    {
        $pass = password_hash($pw, PASSWORD_DEFAULT);
        return $pass;
    }


    final public static function validatePassword(string $pw, string $pwh)
    {
        if (password_verify($pw, $pwh)) {
            return true;
        } else {
            error_log('La contraseña es incorrecta');
            return false;
        }
    }


    final public static function createTokenJwt(string $key, array $data, int $exp = 21600)
    {
        $payload = array(
            "iat" => time(),
            "exp" => time() + $exp,
            /* "exp" => time() + (60 * 60 * 6), */
            "data" => $data
        );
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    final public static function validateTokenJwt(string $key)
    {
        if (!isset($_COOKIE['token'])) {
            die(ResponseHttp::status400('El token de acceso en requerido'));
            exit;
        }
        try {
            $jwt =  $_COOKIE['token'];
            $data = JWT::decode($jwt, new Key($key, 'HS256'));
            self::$jwt_data = $data;
            return $data;
        } catch (\Exception $e) {
            error_log('Token invalido o expiro' . $e);
            echo ResponseHttp::status401('Token invalido o expirado');
            exit();
        }
    }

    final public static function validateToken(string $jwt, string $key)
    {
        try {
            $data = JWT::decode($jwt, new Key($key, 'HS256'));
            self::$jwt_data = $data;
            return $data;
        } catch (\Exception $e) {
            error_log('Token invalido o expiro' . $e);
            echo ResponseHttp::status401('Token invalido o expirado');
        }
    }


    final public static function getDataJwt()
    {
        $jwt_decoded_array = json_decode(json_encode(self::$jwt_data), true);
        return $jwt_decoded_array['data'];
        exit;
    }


    final public static function createOTP($email)
    {
        try {
            session_destroy();
            session_start();
            $otp = rand(1000, 9999);
            $_SESSION['otp'] = $otp;
            $_SESSION['mail'] = $email;
            return $otp;
        } catch (\Exception $e) {
            echo ResponseHttp::status401('Algo salio mal');
        }
    }

    final public static function validateOTP($otp, $email)
    {
        try {
            session_start();
            $otpValidate = $_SESSION['otp'];
            $emailValidate = $_SESSION['mail'];
            if ($email == $emailValidate && $otp == $otpValidate) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            echo ResponseHttp::status401('Token invalido o expirado');
        }
    }

    final public static function SchemaValidationPrueba()
    {
        return  [
            1 => [
                'length' => 8,
                'pattern' => '/^[0-9]*$/',
                'message' => 'El Número de Documento debe contener solo números',
            ],
            2 => [
                'length' => 12,
                'pattern' => '/^[A-Za-z0-9]*$/',
                'message' => 'El Número de Documento debe contener solo caracteres alfanuméricos',
            ],
            3 => [
                'length' => 12,
                'pattern' => '/^[A-Za-z0-9]*$/',
                'message' => 'El Número de Documento debe contener solo caracteres alfanuméricos',
            ],
            4 => [
                'length' => 11,
                'pattern' => '/^[0-9]*$/',
                'message' => 'El Número de Documento debe contener solo números',
            ],
        ];
    }

    final public static function SchemaValidation(int $idTipoDoc, string $numeroDoc)
    {
        $isValidate = false;
        $message = '';

        switch ($idTipoDoc) {
            case 1:
                if (strlen($numeroDoc) === 8) {
                    if (preg_match('/^[0-9]*$/', $numeroDoc)) {
                        $isValidate = true;
                    } else {
                        $message = 'El Número de Documento debe contener solo números';
                    }
                } else {
                    $message = 'El Número de documento debe tener una longitud de 8 caracteres';
                }
                break;
            case 2:
                if (strlen($numeroDoc) <= 12) {
                    if (preg_match('/^[A-Za-z0-9]*$/', $numeroDoc)) {
                        $isValidate = true;
                    } else {
                        $message = 'El Número de Documento debe contener solo caracteres alfanuméricos';
                    }
                } else {
                    $message = 'El Número de documento debe tener una longitud maxima de 12 caracteres';
                }
                break;

            case 3:
                if (strlen($numeroDoc) <= 12) {
                    if (preg_match('/^[A-Za-z0-9]*$/', $numeroDoc)) {
                        $isValidate = true;
                    } else {
                        $message = 'El Número de Documento debe contener solo caracteres alfanuméricos';
                    }
                } else {
                    $message = 'El Número de documento debe tener una longitud maxima de 12 caracteres';
                }
                break;

            case 4:
                if (strlen($numeroDoc) === 11) {
                    if (preg_match('/^[0-9]*$/', $numeroDoc)) {
                        $isValidate = true;
                    } else {
                        $message = 'El Número de Documento debe contener solo números';
                    }
                } else {
                    $message = 'El Número de documento debe tener una longitud de 11 caracteres';
                }
                break;
        }

        return ["message" => $message, "isValidate"  => $isValidate];
    }


    final public static function uploadImage($file, $name)
    {
        try {

            $file = new Image($file);


            $file->setMime(array('png', 'jpg', 'jpeg')); //formatos admitidos
            $file->setSize(100000000, 500000000000); //Tamaño admitidos es Bytes
            $file->setDimension(2000, 2000); //Dimensiones admitidas en Pixeles
            $file->setLocation('public/Images'); //Ubicación de la carpeta



            if ($file[$name]) {
                $upload = $file->upload();
                if ($upload) {
                    $imgUrl = UrlBase::urlBase . '/public/Images/' . $upload->getName() . '.' . $upload->getMime();
                    $data = [
                        'path' => $imgUrl,
                        'name' => $upload->getName() . '.' . $upload->getMime()
                    ];
                    return $data;
                } else {

                    die(json_encode(ResponseHttp::status400($file->getError())));
                }
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    /***********************Subir fotos en base64***************************/
    final public static function uploadImageBase64(array $data, string $name)
    {
        $token = bin2hex(random_bytes(32) . time());
        $name_img = $token . '.png';
        $route = dirname(__DIR__, 2) . "/public/Images/{$name_img}";

        //Decodificamos la imagen
        $img_decoded = base64_decode(
            preg_replace('/^[^,]*,/', '', $data[$name])
        );

        $v = file_put_contents($route, $img_decoded);

        //Validamos si se subio la imagen
        if ($v) {
            return UrlBase::urlBase . "/public/Images/{$name_img}";
        } else {
            unlink($route);
            die(json_encode(ResponseHttp::status500('No se puede subir la imagen')));
        }
    }
}
