<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Query;
use App\Database\Sql;
use Rakit\Validation\Rules\Date;

class VentasModel extends  Connection
{
    private static int    $idVenta;
    private static int    $idCliente;
    private static int    $idEmpleado;
    private static string $fecha;
    private static float $total;
    private static float $igv;

    final public function __construct(array $data)
    {
        self::$idVenta = $data['idVenta'];
        self::$idCliente = $data['idCliente'];
        self::$idEmpleado = $data['idEmpleado'];
        self::$fecha = $data['fecha'];
        self::$total = $data['total'];
        self::$igv = $data['igv'];
    }

    final public static function setIdVenta($idVenta)
    {
        self::$idVenta = $idVenta;
    }
    final public static function setIdCliente($idCliente)
    {
        self::$idCliente = $idCliente;
    }

    final public static function setIdEmpleado($idEmpleado)
    {
        self::$idEmpleado = $idEmpleado;
    }

    final public static function setFecha($fecha)
    {
        self::$fecha = $fecha;
    }

    final public static function setTotal($total)
    {
        self::$total = $total;
    }
    final public static function setIgv($igv)
    {
        self::$igv = $igv;
    }
    final public static function getIdVenta()
    {
        return self::$idVenta;
    }
    final public static function getIdCliente()
    {
        return self::$idCliente;
    }
    final public static function getIdEmpleado()
    {
        return self::$idEmpleado;
    }
    final public static function getFecha()
    {
        return self::$fecha;
    }
    final public static function getTotal()
    {
        return self::$total;
    }
    final public static function getIgv()
    {
        return self::$igv;
    }

    final public static function read()
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM ventas");
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
            error_log("VentasModel::read -> " . $e);
            throw new \Exception("Error al leer las ventas", 500);
        }
    }
    final public static function create()
{
    try {
        $con = self::getConnection();
        $sql = "INSERT INTO ventas (idVenta, idCliente, idEmpleado, fecha, total, igv) VALUES (:idVenta, :idCliente, :idEmpleado, :fecha, :total, :igv)";
        $query = $con->prepare($sql);
        $success = $query->execute([
            ':idVenta' => (int) self::getIdVenta(),
            ':idCliente' => (int) self::getIdCliente(),
            ':idEmpleado' => (int) self::getIdEmpleado(),
            ':fecha'  => self::getFecha(),
            ':total' => (float) self::getTotal(),
            ':igv' => (float) self::getIgv(),
        ]);

        if ($success) {
            return true; // Inserción exitosa
        } else {
            return false; // Inserción fallida
        }
    } catch (\PDOException $e) {
        error_log('VentasModel::create -> ' . $e->getMessage());
        throw new \Exception("Error al registrar la venta: " . $e->getMessage(), 500);
    }
}

}