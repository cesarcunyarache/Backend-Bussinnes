<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Query;
use App\Database\Sql;

class ReservaModel extends Connection
{

    private static int    $idReserva;
    private static int    $idCliente;
    private static string $cantComensales;
    private static string $fecha;
    private static string $hora;
    private static int $idMesero;
    private static string $estado;
    private static string $cometario;


    public function __construct(array $data)
    {

        self::$idCliente = $data['idCliente'];
        self::$cantComensales = $data['cantComensales'];
        self::$fecha = $data['fecha'];
        self::$hora = $data['hora'];
        self::$idMesero = 1;
        self::$estado = 'pendiente';
    }

    final public static function getIdReservabyFecha($dia)
    {
    try {
        $con = self::getConnection()->prepare("SELECT mesasreserva.idMesa, mesas.codigo, mesas.capacidad, reservas.fecha, reservas.hora FROM mesasreserva INNER JOIN reservas ON mesasreserva.idReserva = reservas.idReserva INNER JOIN mesas ON mesas.idMesa = mesasreserva.idMesa WHERE reservas.fecha = :dia");

        $con->execute([
            ':dia' => $dia
        ]);

        if ($con->rowCount() === 0) {
          return [];
        } else {
            $data = $con->fetchAll();
            if (count($data) > 0){
                return $data;
            } else {
               return[];
            }
        }
    } catch (\PDOException $e) {
        error_log("ReservaModel::getIdReservabyFecha -> " . $e);
        die(ResponseHttp::status500());
    }
    exit;
    }


    final public static function create()
    {
        try {
            $con = self::getConnection();
            $sql = "INSERT INTO Reservas (idCliente, cantComensales, fecha,  idMesero, hora, estado) VALUES (:idCliente,:cantComensales, :fecha,:idMesero,:hora,:estado)";
            $query = $con->prepare($sql);
            $query->execute([
                ':idCliente' => (int) self::getIdCliente(),
                ':cantComensales' =>(int) self::getcantComensales(),
                ':fecha'  => self::getfecha(),
                ':hora' => self::gethora(),
                ':idMesero' => self::getidMesero(),
                ':estado' => self::getestado(),
            ]);
            if ($query->rowCount() > 0) {
                return $con->lastInsertId();
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            error_log('MeseroModel::post -> ' . $e);
            die(ResponseHttp::status500());
        }
    }

    final public static function createMesasReserva($idReserva, $idMesa)
    {
        try {
            $con = self::getConnection();
            $sql = "INSERT INTO MesasReserva (idReserva, idMesa) VALUES (:idReserva,:idMesa)";
            $query = $con->prepare($sql);
            $query->execute([
                ':idReserva' => (int) $idReserva,
                ':idMesa' =>(int) $idMesa,
             
            ]);
            if ($query->rowCount() > 0) {
                return $con->lastInsertId();
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            error_log('MeseroModel::post -> ' . $e);
            die(ResponseHttp::status500());
        }
    }


    final public static function getIdReserva()
    {
        return self::$idReserva;
    }

    final public static function setId($idReserva)
    {
        self::$idReserva = $idReserva;
    }


    final public static function getIdCliente()
    {
        return self::$idCliente;
    }

    final public static function setIdCliente($idCliente)
    {
        self::$idCliente = $idCliente;
    }


    final public static function getcantComensales()
    {
        return self::$cantComensales;
    }

    final public static function setcantComensales($cantComensales)
    {
        self::$cantComensales = $cantComensales;
    }


    final public static function getfecha()
    {
        return self::$fecha;
    }

    final public static function setfecha($fecha)
    {
        self::$fecha = $fecha;
    }


    final public static function gethora()
    {
        return self::$hora;
    }

    final public static function sethora($hora)
    {
        self::$hora = $hora;
    }


    final public static function getidMesero()
    {
        return self::$idMesero;
    }

    final public static function setidMesero($idMesero)
    {
        self::$idMesero = $idMesero;
    }


    final public static function getestado()
    {
        return self::$estado;
    }

    final public static function setestado($estado)
    {
        self::$estado = $estado;
    }
}
