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


    final public static function createTokenJwt(string $key, array $data)
    {
        $payload = array(
            "iat" => time(),
            "exp" => time() + (60 * 60 * 6),
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
}
