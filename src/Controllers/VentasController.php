<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;

use App\Config\Message;
use App\Models\VentasModel;
use Resend\Collection;

class VentasController extends Controller
{
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
    
    final public function postCreate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'idVenta'       => 'required|numeric',
                'idCliente'     => 'required|numeric',
                'idEmpleado'    => 'required|numeric',
                'fecha'         => 'required|date:Y-m-d',
                'total'         => 'required|numeric',
                'igv'           => 'required|numeric',
            ]);
    
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                try {
    
                    $params = $this->getParam();
    
                    if (!is_int($params['idVenta']) || !is_int($params['idCliente']) || !is_int($params['idEmpleado'])) {
                        throw new \Exception("ID Venta, ID Cliente, y ID Empleado deben ser enteros.");
                    }
                    if (!is_float((float)$params['total']) || !is_float((float)$params['igv'])) {
                        throw new \Exception("Total e IGV deben ser números decimales.");
                    }
    
                    $venta = new VentasModel($params);
                    $res = $venta::create();
                    
                    if ($res > 0) {
                        echo ResponseHttp::status200('Creado satisfactoriamente');
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                    }
                } catch (\Exception $e) {
                    echo ResponseHttp::status500($e->getMessage());
                }
            }
    
            exit;
        }
    }
    

}