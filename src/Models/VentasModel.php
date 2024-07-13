<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Query;
use App\Database\Sql;

class VentasModel extends  Connection
{
    private static int    $idVenta;
    private static ?int    $idCliente;
    private static int    $idEmpleado;
    private static string $fecha;
    private static float $total;
    private static float $igv;
    private static $detalleVenta;

    final public function __construct(array $data)
    {
        self::$idCliente = $data['idCliente'] ?? null;
        self::$total = $data['total'];
        self::$igv = $data['igv'];
        self::$detalleVenta = $data['detalleVenta'];
    }


    final public static function create()
    {
        try {
            $con = self::getConnection();
            $con->beginTransaction();


            $sql = "INSERT INTO Ventas (idCliente, idEmpleado, fecha, hora, total, igv) VALUES (:idCliente, :idEmpleado, CURDATE(), CURTIME(), :total, :igv)";
            $query = $con->prepare($sql);
            $query->execute([
                ':idCliente' => self::getIdCliente() ?? null,
                ':idEmpleado' => (int) self::getIdEmpleado(),
                ':total' => (float) self::getTotal(),
                ':igv' => (float) self::getIgv(),
            ]);

            if ($query->rowCount() > 0) {
                $ventaId = $con->lastInsertId();
                $detalles = self::getDetalleVenta();

                $sqlDetalle = "INSERT INTO DetalleVenta (idVenta, idProducto, cantidad, subTotal) VALUES (:idVenta, :idProducto, :cantidad, :subTotal)";
                $queryDetalle = $con->prepare($sqlDetalle);

                foreach ($detalles as $detalle) {
                    $resDetalle = $queryDetalle->execute([
                        ':idVenta' =>  (int) $ventaId,
                        ':idProducto' => (int) $detalle['idProducto'],
                        ':cantidad' => (int) $detalle['cantidad'],
                        ':subTotal' => (float) $detalle['subTotal']
                    ]);

                    if (!$resDetalle) {
                        $con->rollBack();
                        echo (ResponseHttp::status500('Algo salió mal, por favor intenta más tarde'));
                    }
                }
                $con->commit();
                return true;
            } else {
                $con->rollBack();
                return false;
            }
        } catch (\PDOException $e) {
            $con->rollBack();
            error_log('VentasModel::create -> ' . $e->getMessage());
            echo (ResponseHttp::status500('Algo salió mal, por favor intenta más tarde'));
            exit;
        } catch (\Exception $e) {
            $con->rollBack();
            error_log('VentasModel::create -> ' . $e->getMessage());
            echo (ResponseHttp::status500('Algo salió mal, por favor intenta más tarde'));
            exit;
        }
    }


    final public static function read()
    {
        try {
            $con = self::getConnection()->prepare("SELECT v.*,
                                                    c.nombres as nombresCliente,
                                                    c.apellidos as apellidosCliente,
                                                    c.numeroDoc as numeroDocCliente,
                                                    e.nombres as nombresEmpleado,
                                                    e.apellidos as apellidosEmpleado,
                                                    e.numeroDoc as numeroDocEmpleado
                                                FROM Ventas v
                                                    LEFT JOIN Clientes c ON v.idCliente = c.idCliente
                                                    INNER JOIN Empleados e ON v.idEmpleado = e.idEmpleado ORDER BY v.fecha DESC;
                                                ");
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

    final public static function getDetalleVenta()
    {
        return self::$detalleVenta;
    }
}
