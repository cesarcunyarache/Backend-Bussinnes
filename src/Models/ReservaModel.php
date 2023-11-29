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
    private static        $idMesero;
    private static string $estado;
    private static string $comentario;


    public function __construct(array $data)
    {
        self::$cantComensales = $data['cantComensales'];
        self::$fecha = $data['fecha'];
        self::$hora = $data['hora'];
        self::$estado = 1;
        self::$idMesero = null;
        self::$comentario = $data['comentario'];
    }

    final public static function getIdReservabyFecha($dia)
    {
        try {
            $con = self::getConnection()->prepare("SELECT mesasreserva.idMesa, mesas.codigo, mesas.capacidad, reservas.fecha, reservas.hora FROM mesasreserva INNER JOIN reservas ON mesasreserva.idReserva = reservas.idReserva INNER JOIN mesas ON mesas.idMesa = mesasreserva.idMesa WHERE reservas.fecha = :dia AND (reservas.estado = 1 OR reservas.estado = 2 ) ");

            $con->execute([
                ':dia' => $dia
            ]);

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
            error_log("ReservaModel::getIdReservabyFecha -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }


    final public static function create()
    {
        try {
            $con = self::getConnection();
            $sql = "INSERT INTO Reservas (idCliente, cantComensales, fecha,  idMesero, hora, comentario, estado) VALUES (:idCliente,:cantComensales, :fecha,:idMesero,:hora,:comentario, :estado)";
            $query = $con->prepare($sql);
            $query->execute([
                ':idCliente' => (int) self::getIdCliente(),
                ':cantComensales' => (int) self::getcantComensales(),
                ':fecha'  => self::getfecha(),
                ':hora' => self::gethora(),
                ':idMesero' => self::getidMesero(),
                ':comentario' => self::getcomentario(),
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
                ':idMesa' => (int) $idMesa,

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

    final public static function read()
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM Reservas r INNER JOIN Clientes c  ON c.id = r.idCliente;");
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
            error_log("UserColaboradorModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }

    final public static function readMesas()
    {
        try {
            $con = self::getConnection()->prepare("SELECT * FROM Mesas");
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
            error_log("UserColaboradorModel::Login -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }

    final public static function getReadById($id)
    {
        try {
            $con = self::getConnection()->prepare(
                "SELECT c.numeroDoc, c.nombres, c.apellidos,  r.idReserva,
            r.fecha, r.hora , r.cantComensales, r.comentario, r.idMesero, r.estado
            FROM Reservas r 
            INNER JOIN Clientes c ON c.id = r.idCliente
            WHERE idReserva =:id"
            );

            $con->execute([':id' => $id]);

            if ($con->rowCount() === 0) {
                return [];
            } else {
                $data = $con->fetch();
                if (count($data) > 0) {
                    return $data;
                } else {
                    return [];
                }
            }
        } catch (\PDOException $e) {
            error_log("ReservaModel -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }

    final public static function getReadMesasReserva($id)
    {
        try {
            $con = self::getConnection()->prepare(
                "SELECT m.idMesa, m.codigo, m.nivel, m.capacidad FROM Reservas r
            INNER JOIN mesasreserva mr ON mr.idReserva = r.idReserva
            INNER JOIN mesas m on m.idMesa = mr.idMesa
            WHERE r.idReserva =:id"
            );

            $con->execute([':id' => $id]);

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
            error_log("ReservaModel -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }


    final public static function updateEstadoReserva($idReserva, $estado)
    {
        try {
            $con = self::getConnection();
            $sql = "UPDATE Reservas SET estado=:estado WHERE idReserva=:idReserva";

            $query = $con->prepare($sql);
            $query->execute([
                ':estado' => $estado,
                ':idReserva' => (int) $idReserva,
                
            ]);

            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log('ColaboradorModel::post -> ' . $e);
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

    final public static function setComentario($comentario)
    {
        self::$comentario = $comentario;
    }

    final public static function getComentario()
    {
        return self::$comentario;
    }
}
