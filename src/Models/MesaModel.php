<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Database\Connection;
use App\Database\Query;
use App\Database\Sql;

class MesaModel extends Connection
{
    


    public function __construct(array $data)
    {
    }

    final public static function read()
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
            error_log("MesaModel:: -> " . $e);
            die(ResponseHttp::status500());
        }
        exit;
    }
}
