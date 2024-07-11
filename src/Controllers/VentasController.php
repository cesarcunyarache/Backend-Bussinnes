<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;
use App\Config\Message;
use App\Models\VentasModel;

class VentasController extends Controller
{


    final public function postCreate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'idCliente'     => '',
                'total'         => 'required',
                'igv'           => 'required',
                'detalleVenta'  => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                try {
                    $data = Security::validateTokenJwt(Security::secretKey());
                    $idEmpleado = $data->data->idEmpleado;

                    $venta = new VentasModel($this->getParam());
                    $venta::setIdEmpleado($idEmpleado);
                    $res = $venta::create();

                    if ($res) {
                        echo ResponseHttp::status200('Creado satisfactoriamente');
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente o intente más tarde. $idEmpleado");
                    }
                } catch (\Exception $e) {
                    echo ResponseHttp::status500($e->getMessage());
                }
            }

            exit;
        }
    }


    final public function getRead(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            try {
                $data = VentasModel::read();
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function getReadById(string $endPoint)
    {
        if ($this->getMethod() == 'get'  && $endPoint == $this->getRoute()) {

            try {
                Security::validateTokenJwt(Security::secretKey());
                $id = $this->getAttribute()[1];
                $data = VentasModel::getIdVenta($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }
}
